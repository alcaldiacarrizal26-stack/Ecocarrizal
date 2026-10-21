<?php

namespace App\Http\Controllers\Ciudadano;

use App\Http\Controllers\Controller;
use App\Models\Denuncia;
use App\Services\TerritorioService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class DenunciaController extends Controller
{
    /**
     * Muestra la lista de denuncias del ciudadano y los datos del formulario.
     */
    public function index()
    {
        // Obtener las denuncias del usuario autenticado
        $denuncias = Denuncia::where('user_id', auth()->id())
            ->latest()
            ->get()
            ->map(function ($denuncia) {
                if (is_array($denuncia->evidencias)) {
                    $denuncia->evidencias = array_map(function ($path) {
                        return asset(Storage::url($path));
                    }, $denuncia->evidencias);
                }
                return $denuncia;
            });

        // Obtener la estructura oficial de comunas/circuitos y sectores desde el Service
        $territorios = TerritorioService::getTerritorios();

        return view('ciudadano.denuncias', compact('denuncias', 'territorios'));
    }

    /**
     * Almacena una nueva denuncia ambiental con protección Anti-Spam.
     */
    public function store(Request $request)
    {
        $userId = auth()->id();

        // 1. CAPA ANTI-SPAM: Throttle / Rate Limiting por Usuario (Máx 3 peticiones cada 5 minutos)
        $rateLimitKey = 'denuncia-submission:' . $userId;
        if (RateLimiter::tooManyAttempts($rateLimitKey, 3)) {
            $seconds = RateLimiter::availableIn($rateLimitKey);
            $minutes = ceil($seconds / 60);

            return redirect()->back()
                ->withInput()
                ->with('error', "Has superado el límite de reportes permitidos. Por favor, espera {$minutes} minuto(s) antes de intentar nuevamente.");
        }

        // 2. CAPA ANTI-SPAM: Prevenir duplicados idénticos en un ventana de 60 segundos (Doble Clic / Spam rápido)
        $duplicateExists = Denuncia::where('user_id', $userId)
            ->where('descripcion', $request->input('descripcion'))
            ->where('created_at', '>=', now()->subSeconds(60))
            ->exists();

        if ($duplicateExists) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Ya has enviado una denuncia idéntica recientemente. Por favor verifica tu historial.');
        }

        // 3. VALIDACIÓN DE ENTRADA (Incluyendo límite máximo de 5 imágenes)
        $validated = $request->validate([
            'tipo_contaminacion'      => 'required|string',
            'otro_tipo_contaminacion' => 'nullable|required_if:tipo_contaminacion,Otro|string|max:100',
            'comuna'                  => 'required|string',
            'sector'                  => 'required|string',
            'punto_referencia'        => 'required|string|max:255',
            'nivel_gravedad'          => ['required', Rule::in(['baja', 'media', 'alta'])],
            'justificacion_gravedad'  => 'nullable|required_if:nivel_gravedad,alta|string|max:1000',
            'descripcion'             => 'required|string|max:3000',
            'evidencias'              => 'required|array|min:1|max:5',
            'evidencias.*'            => 'image|mimes:jpeg,png,jpg,webp|max:5120', // Máximo 5MB por imagen
        ]);

        // 4. GUARDADO SEGURO DE ARCHIVOS CON RUTAS ÚNICAS
        $rutasEvidencias = [];
        if ($request->hasFile('evidencias')) {
            foreach ($request->file('evidencias') as $file) {
                $rutasEvidencias[] = $file->store('evidencias_denuncias', 'public');
            }
        }

        // 5. REGISTRO EN LA BASE DE DATOS
        Denuncia::create([
            'user_id'                  => $userId,
            'tipo_contaminacion'       => $validated['tipo_contaminacion'],
            'otro_tipo_contaminacion'  => $validated['tipo_contaminacion'] === 'Otro' ? $validated['otro_tipo_contaminacion'] : null,
            'comuna'                   => $validated['comuna'],
            'sector'                   => $validated['sector'],
            'punto_referencia'         => $validated['punto_referencia'],
            'nivel_gravedad'           => $validated['nivel_gravedad'],
            'justificacion_gravedad'   => $validated['nivel_gravedad'] === 'alta' ? $validated['justificacion_gravedad'] : null,
            'descripcion'              => $validated['descripcion'],
            'evidencias'               => $rutasEvidencias,
            'estatus'                  => 'pendiente',
        ]);

        // Registrar intento en el RateLimiter (expira en 300 segundos / 5 min)
        RateLimiter::hit($rateLimitKey, 300);

        return redirect()->route('ciudadano.denuncias')
            ->with('success', 'Denuncia ambiental registrada exitosamente.');
    }
}