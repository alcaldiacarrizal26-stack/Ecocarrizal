<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Inspeccion;
use App\Models\Solicitud;
use App\Models\Denuncia;
use App\Models\Inspector;
use App\Models\Compensacion;
use App\Services\CompensacionService;
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

        $asignacionesPendientes = $solicitudes->concat($denuncias);

        return view('inspecciones.index', compact(
            'asignacionesPendientes',
            'inspeccionesRealizadas',
            'rol'
        ));
    }

    /**
     * Pasa un expediente a estado de revisión/inspección y fija fecha_inicio_inspeccion.
     */
    public function iniciarRevision(Request $request, $id)
    {
        $user = auth()->user();
        $rol = strtolower($user->role->nombre ?? '');

        // Intentamos buscar primero en Solicitud; si no existe, buscamos en Denuncia
        $solicitud = Solicitud::find($id);

        if ($solicitud) {
            $expediente = $solicitud;
            $campoEstatus = 'estado';
            $nuevoEstado = 'en_inspeccion';
        } else {
            $expediente = Denuncia::findOrFail($id);
            $campoEstatus = 'estatus';
            $nuevoEstado = 'en_revision';
        }

        // Validación de autorización para inspectores
        if ($rol === 'inspector' && $expediente->inspector_id !== $user->inspector?->id) {
            abort(403, 'No tienes permiso para actualizar este expediente.');
        }

        $payload = [
            $campoEstatus => $nuevoEstado
        ];

        // Se registra la fecha de inicio de inspección solo la primera vez que inicia
        if (!$expediente->fecha_inicio_inspeccion) {
            $payload['fecha_inicio_inspeccion'] = now();
        }

        $expediente->update($payload);

        return redirect()->back()->with('success', 'El expediente ha pasado a estado "En Inspección".');
    }

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

            // Campos específicos para evaluación de Tala / Compensación
            'cantidad_arboles_talar' => 'nullable|required_if:dictamen,APROBADO|integer|min:1',
            'especie_afectada' => 'nullable|string|max:100',
            'es_especie_protegida' => 'nullable|boolean',
            'especie_compensacion' => 'nullable|required_if:dictamen,APROBADO|string|max:100',
        ]);

        $user = auth()->user();
        
        $origen = $validated['tipo_origen'] === 'solicitud' 
            ? Solicitud::findOrFail($validated['origen_id'])
            : Denuncia::findOrFail($validated['origen_id']);

        $inspectorId = $user->inspector?->id ?? $origen->inspector_id;

        if (!$inspectorId) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['origen_id' => 'No se puede procesar la inspección sin un inspector asignado al expediente.']);
        }

        DB::transaction(function () use ($validated, $request, $inspectorId, $origen) {
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
                'fecha_visita' => $request->fecha_visita ?? now(),
                'observaciones_campo' => $validated['observaciones_campo'],
                'dictamen' => $validated['dictamen'],
                'hallazgos' => $validated['hallazgos'] ?? [],
                'recomendaciones' => $validated['recomendaciones'] ?? null,
                'fotos_evidencia' => $evidenciasPaths,
            ];

            // 1. Actualización de Estados y Fechas
            if ($validated['tipo_origen'] === 'solicitud') {
                $inspeccionData['solicitud_id'] = $origen->id;

                $nuevoEstado = match($validated['dictamen']) {
                    'APROBADO' => 'aprobado',
                    'RECHAZADO' => 'rechazado',
                    default => 'en_inspeccion'
                };
                
                $updateData = ['estado' => $nuevoEstado];

                if (in_array($nuevoEstado, ['aprobado', 'rechazado'])) {
                    $updateData['fecha_cierre'] = now();
                }

                $origen->update($updateData);

            } else {
                $inspeccionData['denuncia_id'] = $origen->id;

                $nuevoEstatus = match($validated['dictamen']) {
                    'APROBADO', 'CON_OBSERVACIONES' => 'atendido',
                    'RECHAZADO' => 'desestimado',
                    default => 'en_revision'
                };
                
                $updateData = ['estatus' => $nuevoEstatus];

                if (in_array($nuevoEstatus, ['atendido', 'desestimado'])) {
                    $updateData['fecha_cierre'] = now();
                }

                $origen->update($updateData);
            }

            // 2. Creación del Registro de Inspección
            $inspeccion = Inspeccion::create($inspeccionData);

            // 3. Generación del Expediente de Compensación (Si es Solicitud de Tala Aprobada)
            if (
                $validated['tipo_origen'] === 'solicitud' &&
                $origen->tipo_tramite === 'tala' &&
                $validated['dictamen'] === 'APROBADO'
            ) {
                $cantidadTalar = (int) $validated['cantidad_arboles_talar'];
                $esProtegida = (bool) ($request->es_especie_protegida ?? false);

                // Cálculo 1:8 con multiplicador por especie protegida
                $cantidadAsignada = CompensacionService::calcularEjemplares(
                    $cantidadTalar,
                    $esProtegida,
                    8
                );

                Compensacion::create([
                    'codigo_resolucion' => 'RES-COMP-' . date('Y') . '-' . strtoupper(Str::random(5)),
                    'solicitud_id'      => $origen->id,
                    'user_id'           => $origen->user_id,
                    'inspeccion_id'     => $inspeccion->id,
                    'especie_requerida' => $validated['especie_compensacion'] ?? 'Araguaney / Apamate',
                    'cantidad_asignada' => $cantidadAsignada,
                    'cantidad_entregada' => 0,
                    'estatus'            => 'PENDIENTE',
                    'observaciones'     => "Compensación por tala de {$cantidadTalar} árbol(es). " . 
                                           ($esProtegida ? 'Aplica recargo por Especie Protegida/Vedada (Ratio 1:16).' : 'Ratio estándar (1:8).'),
                ]);
            }
        });

        return redirect()->route('inspecciones.index')
            ->with('success', 'Informe, dictamen y resolución de compensación registrados exitosamente.');
    }
}