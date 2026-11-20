<?php

namespace App\Http\Controllers\Ciudadano;

use App\Http\Controllers\Controller;
use App\Models\Denuncia;
use App\Models\Notificacion;
use App\Models\User;
use App\Services\TerritorioService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class DenunciaController extends Controller
{
    public function index()
    {
        $denuncias = Denuncia::where('user_id', auth()->id())
            ->latest()
            ->get()
            ->map(function ($denuncia) {
                if (is_array($denuncia->evidencias)) {
                    $denuncia->evidencias = array_map(function ($path) {
                        // Usamos Storage::url directamente. 
                        // Esto generará una URL relativa (/storage/...) que el navegador resolverá correctamente.
                        return Storage::url($path);
                    }, $denuncia->evidencias);
                }
                return $denuncia;
            });

        $territorios = TerritorioService::getTerritorios();

        return view('ciudadano.denuncias', compact('denuncias', 'territorios'));
    }

    public function store(Request $request)
    {
        $userId = auth()->id();

        $rateLimitKey = 'denuncia-submission:' . $userId;
        if (RateLimiter::tooManyAttempts($rateLimitKey, 3)) {
            $seconds = RateLimiter::availableIn($rateLimitKey);
            $minutes = ceil($seconds / 60);
            return redirect()->back()->withInput()->with('error', "Has superado el límite de reportes. Espera {$minutes} minuto(s).");
        }

        $duplicateExists = Denuncia::where('user_id', $userId)
            ->where('descripcion', $request->input('descripcion'))
            ->where('created_at', '>=', now()->subSeconds(60))
            ->exists();

        if ($duplicateExists) {
            return redirect()->back()->withInput()->with('error', 'Ya has enviado una denuncia idéntica recientemente.');
        }

        $validated = $request->validate([
            'tipo_contaminacion'      => 'required|string',
            'otro_tipo_contaminacion' => 'nullable|required_if:tipo_contaminacion,Otro|string|max:100',
            'comuna'                  => 'required|string|exists:territorios,name',
            'sector'                  => 'required|string',
            'punto_referencia'        => 'required|string|max:255',
            'nivel_gravedad'          => ['required', Rule::in(['BAJA', 'MEDIA', 'ALTA', 'CRITICA'])], // <-- MAYÚSCULAS
            'justificacion_gravedad'  => 'nullable|required_if:nivel_gravedad,ALTA|string|max:1000',
            'descripcion'             => 'required|string|max:3000',
            'evidencias'              => 'required|array|min:1|max:5',
            'evidencias.*'            => 'image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        if (!TerritorioService::verificarExistencia($validated['comuna'], $validated['sector'])) {
            return redirect()->back()->withInput()->with('error', 'El sector no pertenece a la comuna indicada.');
        }

        $rutasEvidencias = [];
        if ($request->hasFile('evidencias')) {
            foreach ($request->file('evidencias') as $file) {
                $rutasEvidencias[] = $file->store('evidencias_denuncias', 'public');
            }
        }

        $denuncia = Denuncia::create([
            'user_id'                  => $userId,
            'tipo_contaminacion'       => $validated['tipo_contaminacion'],
            'otro_tipo_contaminacion'  => $validated['tipo_contaminacion'] === 'Otro' ? $validated['otro_tipo_contaminacion'] : null,
            'comuna'                   => $validated['comuna'],
            'sector'                   => $validated['sector'],
            'punto_referencia'         => $validated['punto_referencia'],
            'nivel_gravedad'           => $validated['nivel_gravedad'],
            'justificacion_gravedad'   => $validated['nivel_gravedad'] === 'ALTA' ? $validated['justificacion_gravedad'] : null, // <-- MAYÚSCULAS
            'descripcion'              => $validated['descripcion'],
            'evidencias'               => $rutasEvidencias,
            'estado'                   => 'PENDIENTE', // <-- CAMBIADO estatus -> estado y en MAYÚSCULAS
        ]);

        $directoresAmbiente = User::whereHas('role', function ($q) {
            $q->whereIn('nombre', ['admin', 'director', 'director_ambiente']);
        })->get();

        foreach ($directoresAmbiente as $dir) {
            Notificacion::create([
                'user_id'  => $dir->id,
                'titulo'   => 'Nueva Denuncia Ambiental',
                'mensaje'  => "Se ha registrado un reporte en la Comuna: {$validated['comuna']} (Gravedad: {$validated['nivel_gravedad']}).",
                'tipo'     => $validated['nivel_gravedad'] === 'ALTA' ? 'ERROR' : 'ADVERTENCIA', // <-- ENUM MAYÚSCULAS
                'link_url' => route('director.ambiente.denuncias'),
            ]);
        }

        RateLimiter::hit($rateLimitKey, 300);

        return redirect()->route('ciudadano.denuncias')->with('success', 'Denuncia ambiental registrada exitosamente.');
    }
}