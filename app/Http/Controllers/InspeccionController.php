<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Inspeccion;
use App\Models\Solicitud;
use App\Models\Denuncia;
use App\Models\Inspector;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

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
     * Muestra el formulario para evaluar por tipo y ID explícitos.
     */
    public function evaluar($tipo, $id)
    {
        if ($tipo === 'solicitud') {
            // Se carga la relación 'solicitante'
            $expediente = Solicitud::with(['solicitante', 'inspector.user'])->findOrFail($id);
        } else {
            // Se carga la relación 'user'
            $expediente = Denuncia::with(['user', 'inspector.user'])->findOrFail($id);
        }

        $tipoOrigen = $tipo;

        return view('inspecciones.evaluar', compact('expediente', 'tipoOrigen'));
    }

    public function edit($id)
    {
        $solicitud = Solicitud::with(['solicitante', 'inspector.user'])->find($id);
        $denuncia = null;
        $tipoOrigen = 'solicitud';

        if (!$solicitud) {
            $denuncia = Denuncia::with(['user', 'inspector.user'])->findOrFail($id);
            $tipoOrigen = 'denuncia';
        }

        $expediente = $solicitud ?? $denuncia;

        return view('inspecciones.evaluar', compact('expediente', 'tipoOrigen'));
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'tipo_origen' => 'required|in:solicitud,denuncia',
            'origen_id' => 'required|uuid',
            'fecha_visita' => 'required|date',
            'observaciones_campo' => 'required|string|min:10',
            'dictamen' => 'required|in:APROBADO,CON_OBSERVACIONES,RECHAZADO,REINSPECCION_REQUERIDA',
            'hallazgos' => 'nullable|array',
            'recomendaciones' => 'nullable|string',
            'evidencias.*' => 'nullable|image|max:5120',
        ]);

        $user = auth()->user();
        $inspectorId = null;

        if ($user->inspector) {
            $inspectorId = $user->inspector->id;
        } else {
            // Si el Director/Admin realiza la evaluación directamente:
            if ($validated['tipo_origen'] === 'solicitud') {
                $origen = Solicitud::find($validated['origen_id']);
            } else {
                $origen = Denuncia::find($validated['origen_id']);
            }

            $inspectorId = $origen?->inspector_id ?? Inspector::first()?->id;
        }

        DB::transaction(function () use ($validated, $request, $inspectorId) {
            $evidenciasPaths = [];

            if ($request->hasFile('evidencias')) {
                foreach ($request->file('evidencias') as $foto) {
                    $evidenciasPaths[] = $foto->store('inspecciones/evidencias', 'public');
                }
            }

            $codigoActa = 'ACTA-' . strtoupper(Str::random(8));

            $inspeccionData = [
                'codigo_acta' => $codigoActa,
                'inspector_id' => $inspectorId,
                'fecha_visita' => $validated['fecha_visita'],
                'observaciones_campo' => $validated['observaciones_campo'],
                'dictamen' => $validated['dictamen'],
                'hallazgos' => $validated['hallazgos'] ?? [],
                'recomendaciones' => $validated['recomendaciones'] ?? null,
                'fotos_evidencia' => $evidenciasPaths,
            ];

            if ($validated['tipo_origen'] === 'solicitud') {
                $inspeccionData['solicitud_id'] = $validated['origen_id'];

                $solicitud = Solicitud::findOrFail($validated['origen_id']);
                $nuevoEstado = match($validated['dictamen']) {
                    'APROBADO' => 'aprobada',
                    'RECHAZADO' => 'rechazada',
                    default => 'en_inspeccion'
                };
                $solicitud->update(['estado' => $nuevoEstado]);

            } else {
                $inspeccionData['denuncia_id'] = $validated['origen_id'];

                $denuncia = Denuncia::findOrFail($validated['origen_id']);
                $nuevoEstatus = match($validated['dictamen']) {
                    'APROBADO', 'CON_OBSERVACIONES' => 'atendido',
                    'RECHAZADO' => 'desestimado',
                    default => 'en_revision'
                };
                $denuncia->update(['estatus' => $nuevoEstatus]);
            }

            Inspeccion::create($inspeccionData);
        });

        return redirect()->route('inspecciones.index')
            ->with('success', 'Informe y acta de inspección registrados exitosamente.');
    }
}