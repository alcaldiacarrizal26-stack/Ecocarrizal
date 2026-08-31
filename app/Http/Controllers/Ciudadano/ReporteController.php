<?php

namespace App\Http\Controllers\Ciudadano;

use App\Http\Controllers\Controller;
use App\Models\Denuncia;
use App\Services\TerritorioService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ReporteController extends Controller
{
    /**
     * Muestra la lista de reportes del ciudadano y los datos del formulario.
     */
    public function index()
    {
        // Obtener las denuncias del usuario autenticado
        $denuncias = Denuncia::where('user_id', auth()->id())
            ->latest()
            ->get()
            ->map(function ($denuncia) {
                // Mapear cada ruta para convertirla en URL pública utilizable por el frontend
                if (is_array($denuncia->evidencias)) {
                    $denuncia->evidencias = array_map(function ($path) {
                        return asset(Storage::url($path));
                    }, $denuncia->evidencias);
                }
                return $denuncia;
            });

        // Obtener la estructura oficial de comunas/circuitos y sectores desde el Service
        $territorios = TerritorioService::getTerritorios();

        return view('ciudadano.reportes', compact('denuncias', 'territorios'));
    }

    /**
     * Almacena una nueva denuncia ambiental.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'tipo_contaminacion' => 'required|string',
            'otro_tipo_contaminacion' => 'nullable|required_if:tipo_contaminacion,Otro|string|max:100',
            'comuna' => 'required|string',
            'sector' => 'required|string',
            'punto_referencia' => 'required|string',
            'nivel_gravedad' => ['required', Rule::in(['baja', 'media', 'alta'])],
            'justificacion_gravedad' => 'nullable|required_if:nivel_gravedad,alta|string',
            'descripcion' => 'required|string',
            'evidencias' => 'required|array|min:1',
            'evidencias.*' => 'image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        $rutasEvidencias = [];
        if ($request->hasFile('evidencias')) {
            foreach ($request->file('evidencias') as $file) {
                $rutasEvidencias[] = $file->store('evidencias_denuncias', 'public');
            }
        }

        Denuncia::create([
            'user_id' => auth()->id(),
            'tipo_contaminacion' => $validated['tipo_contaminacion'],
            'otro_tipo_contaminacion' => $validated['tipo_contaminacion'] === 'Otro' ? $validated['otro_tipo_contaminacion'] : null,
            'comuna' => $validated['comuna'],
            'sector' => $validated['sector'],
            'punto_referencia' => $validated['punto_referencia'],
            'nivel_gravedad' => $validated['nivel_gravedad'],
            'justificacion_gravedad' => $validated['nivel_gravedad'] === 'alta' ? $validated['justificacion_gravedad'] : null,
            'descripcion' => $validated['descripcion'],
            'evidencias' => $rutasEvidencias,
            'estatus' => 'pendiente',
        ]);

        return redirect()->route('ciudadano.reportes')->with('success', 'Denuncia ambiental registrada exitosamente.');
    }
}