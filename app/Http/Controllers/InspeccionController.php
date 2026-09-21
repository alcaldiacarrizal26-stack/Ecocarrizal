<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Inspeccion;
use App\Models\Solicitud;
use App\Models\Denuncia;
use App\Models\Inspector;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class InspeccionController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $rol = strtolower($user->role->nombre ?? '');
        $inspector = $user->inspector ?? null;

        if ($rol === 'inspector' && $inspector) {
            // Filtrar asignaciones exclusivas del inspector autenticado
            $solicitudes = Solicitud::where('inspector_id', $inspector->id)
                ->whereIn('estado', ['inspector_asignado', 'en_inspeccion'])
                ->get();

            $denuncias = Denuncia::where('inspector_id', $inspector->id)
                ->whereIn('estatus', ['inspector_asignado', 'en_revision'])
                ->get();

            $inspeccionesRealizadas = Inspeccion::where('inspector_id', $inspector->id)
                ->with(['solicitud', 'denuncia'])
                ->latest()
                ->paginate(10);
        } else {
            // Para Director y Admin: Ver todas las asignaciones y expedientes pendientes
            $solicitudes = Solicitud::whereIn('estado', ['inspector_asignado', 'en_inspeccion'])
                ->with('inspector.user')
                ->get();

            $denuncias = Denuncia::whereIn('estatus', ['inspector_asignado', 'en_revision'])
                ->with('inspector.user')
                ->get();

            $inspeccionesRealizadas = Inspeccion::with(['solicitud', 'denuncia', 'inspector.user'])
                ->latest()
                ->paginate(10);
        }

        // Unificamos Solicitudes y Denuncias
        $asignacionesPendientes = $solicitudes->concat($denuncias);

        return view('inspecciones.index', compact(
            'asignacionesPendientes',
            'inspeccionesRealizadas',
            'rol'
        ));
    }

    /**
     * Pasa un expediente a estado de revisión/inspección.
     */
    public function iniciarRevision(Request $request, $tipo, $id)
    {
        $user = auth()->user();
        $rol = strtolower($user->role->nombre ?? '');

        if ($tipo === 'solicitud') {
            $expediente = Solicitud::findOrFail($id);
            $campoEstatus = 'estado';
            $nuevoEstado = 'en_inspeccion';
        } elseif ($tipo === 'denuncia') {
            $expediente = Denuncia::findOrFail($id);
            $campoEstatus = 'estatus';
            $nuevoEstado = 'en_revision';
        } else {
            abort(404, 'Tipo de expediente no válido.');
        }

        // Validación de autorización para inspectores
        if ($rol === 'inspector' && $expediente->inspector_id !== $user->inspector?->id) {
            abort(403, 'No tienes permiso para actualizar este expediente.');
        }

        $expediente->update([
            $campoEstatus => $nuevoEstado
        ]);

        return redirect()->back()->with('success', 'El expediente ha pasado a estado "En Revisión".');
    }

    /**
     * Muestra el formulario para evaluar por tipo y ID explícitos.
     */
    public function evaluar($tipo, $id)
    {
        if (!in_array($tipo, ['solicitud', 'denuncia'])) {
            abort(404, 'Tipo de origen no válido.');
        }

        if ($tipo === 'solicitud') {
            $expediente = Solicitud::with(['solicitante', 'inspector.user'])->findOrFail($id);
        } else {
            $expediente = Denuncia::with(['user', 'inspector.user'])->findOrFail($id);
        }

        $tipoOrigen = $tipo;

        return view('inspecciones.evaluar', compact('expediente', 'tipoOrigen'));
    }

    /**
     * Redirección/Alias para mantener compatibilidad con rutas resource o legacy.
     */
    public function edit($id)
    {
        $solicitud = Solicitud::with(['solicitante', 'inspector.user'])->find($id);

        if ($solicitud) {
            return $this->evaluar('solicitud', $solicitud->id);
        }

        $denuncia = Denuncia::with(['user', 'inspector.user'])->findOrFail($id);
        return $this->evaluar('denuncia', $denuncia->id);
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'tipo_origen' => 'required|in:solicitud,denuncia',
            'origen_id' => 'required|uuid',
            'observaciones_campo' => 'required|string|min:10',
            'dictamen' => ['required', Rule::in(['APROBADO', 'CON_OBSERVACIONES', 'RECHAZADO', 'REINSPECCION_REQUERIDA'])],
            'hallazgos' => 'nullable|array',
            'recomendaciones' => 'nullable|string',
            'evidencias' => 'nullable|array',
            'evidencias.*' => 'image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        $user = auth()->user();
        
        // Obtención del origen
        $origen = $validated['tipo_origen'] === 'solicitud' 
            ? Solicitud::findOrFail($validated['origen_id'])
            : Denuncia::findOrFail($validated['origen_id']);

        // Definición del Inspector asignado a la evaluación
        $inspectorId = $user->inspector?->id ?? $origen->inspector_id;

        if (!$inspectorId) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['origen_id' => 'No se puede procesar la inspección sin un inspector asignado al expediente.']);
        }

        DB::transaction(function () use ($validated, $request, $inspectorId, $origen) {
            $evidenciasPaths = [];

            // Subida de evidencias
            if ($request->hasFile('evidencias')) {
                foreach ($request->file('evidencias') as $foto) {
                    $evidenciasPaths[] = $foto->store('inspecciones/evidencias', 'public');
                }
            }

            $codigoActa = 'ACTA-' . strtoupper(Str::random(8));

            $inspeccionData = [
                'codigo_acta' => $codigoActa,
                'inspector_id' => $inspectorId,
                'fecha_visita' => $request->fecha_visita ?? now(),
                'observaciones_campo' => $validated['observaciones_campo'],
                'dictamen' => $validated['dictamen'],
                'hallazgos' => $validated['hallazgos'] ?? [],
                'recomendaciones' => $validated['recomendaciones'] ?? null,
                'fotos_evidencia' => $evidenciasPaths,
            ];

            // Actualización de estados según origen y dictamen
            if ($validated['tipo_origen'] === 'solicitud') {
                $inspeccionData['solicitud_id'] = $origen->id;

                $nuevoEstado = match($validated['dictamen']) {
                    'APROBADO' => 'aprobada',
                    'RECHAZADO' => 'rechazada',
                    default => 'en_inspeccion'
                };
                
                $origen->update(['estado' => $nuevoEstado]);

            } else {
                $inspeccionData['denuncia_id'] = $origen->id;

                $nuevoEstatus = match($validated['dictamen']) {
                    'APROBADO', 'CON_OBSERVACIONES' => 'atendido',
                    'RECHAZADO' => 'desestimado',
                    default => 'en_revision'
                };
                
                $origen->update(['estatus' => $nuevoEstatus]);
            }

            Inspeccion::create($inspeccionData);
        });

        return redirect()->route('inspecciones.index')
            ->with('success', 'Informe y acta de inspección registrados exitosamente.');
    }
}