<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Inspeccion;
use App\Models\Solicitud;
use App\Models\Denuncia;
use App\Models\OrdenInspeccion;
use App\Models\Empresa;
use App\Models\Inspector;
use App\Models\Compensacion;
use App\Services\CompensacionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
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
                ->whereIn('estado', ['inspector_asignado', 'en_inspeccion', 'requiere_reinspeccion'])
                ->get();

            $denuncias = Denuncia::where('inspector_id', $inspector->id)
                ->whereIn('estatus', ['inspector_asignado', 'en_revision', 'en_inspeccion', 'requiere_reinspeccion'])
                ->get();

            $ordenesEmpresas = OrdenInspeccion::where('inspector_id', $inspector->id)
                ->whereIn('estatus', ['inspector_asignado', 'en_inspeccion', 'requiere_reinspeccion'])
                ->with('empresa')
                ->get();

            $inspeccionesRealizadas = Inspeccion::where('inspector_id', $inspector->id)
                ->with(['solicitud', 'denuncia', 'ordenInspeccion.empresa', 'empresa'])
                ->latest()
                ->paginate(10);
        } else {
            // Directores y Admins ven todas las asignaciones pendientes a nivel general
            $solicitudes = Solicitud::whereIn('estado', ['inspector_asignado', 'en_inspeccion', 'requiere_reinspeccion'])
                ->with('inspector.user')
                ->get();

            $denuncias = Denuncia::whereIn('estatus', ['inspector_asignado', 'en_revision', 'en_inspeccion', 'requiere_reinspeccion'])
                ->with('inspector.user')
                ->get();

            $ordenesEmpresas = OrdenInspeccion::whereIn('estatus', ['inspector_asignado', 'en_inspeccion', 'requiere_reinspeccion'])
                ->with(['inspector.user', 'empresa'])
                ->get();

            $inspeccionesRealizadas = Inspeccion::with(['solicitud', 'denuncia', 'ordenInspeccion.empresa', 'empresa', 'inspector.user'])
                ->latest()
                ->paginate(10);
        }

        $asignacionesPendientes = $solicitudes->concat($denuncias)->concat($ordenesEmpresas);
        
        $currentInspectorId = $inspector?->id ?? null;

        return view('inspecciones.index', compact(
            'asignacionesPendientes',
            'inspeccionesRealizadas',
            'rol',
            'currentInspectorId'
        ));
    }

    public function iniciarRevision(Request $request, $id)
    {
        $user = auth()->user();
        $rol = strtolower($user->role->nombre ?? '');

        // 1. Revisar si es una Orden de Inspección de Empresa
        $ordenEmpresa = OrdenInspeccion::find($id);
        if ($ordenEmpresa) {
            if ($rol === 'inspector' && $ordenEmpresa->inspector_id !== $user->inspector?->id) {
                abort(403, 'No tienes permiso para actualizar esta orden.');
            }

            $payload = ['estatus' => 'en_inspeccion'];
            if (!$ordenEmpresa->fecha_inicio_inspeccion) {
                $payload['fecha_inicio_inspeccion'] = now();
            }
            $ordenEmpresa->update($payload);

            return redirect()->back()->with('success', 'La orden de inspección ha pasado a estado "En Inspección".');
        }

        // 2. Solicitud o Denuncia
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

        if ($rol === 'inspector' && $expediente->inspector_id !== $user->inspector?->id) {
            abort(403, 'No tienes permiso para actualizar este expediente.');
        }

        $payload = [$campoEstatus => $nuevoEstado];
        if (!$expediente->fecha_inicio_inspeccion) {
            $payload['fecha_inicio_inspeccion'] = now();
        }

        $expediente->update($payload);

        return redirect()->back()->with('success', 'El expediente ha pasado a estado de inspección.');
    }

    public function evaluar($id)
    {
        // 1. Buscar si es Orden de Empresa
        $orden = OrdenInspeccion::with(['empresa', 'inspector.user'])->find($id);
        if ($orden) {
            $tipoOrigen = 'orden';
            $expediente = $orden;
            return view('inspecciones.evaluar', compact('expediente', 'tipoOrigen'));
        }

        // 2. Buscar en Solicitud
        $solicitud = Solicitud::with(['solicitante', 'inspector.user'])->find($id);
        if ($solicitud) {
            $tipoOrigen = 'solicitud';
            $expediente = $solicitud;
            return view('inspecciones.evaluar', compact('expediente', 'tipoOrigen'));
        }

        // 3. Buscar en Denuncia
        $denuncia = Denuncia::with(['user', 'inspector.user'])->findOrFail($id);
        $tipoOrigen = 'denuncia';
        $expediente = $denuncia;

        return view('inspecciones.evaluar', compact('expediente', 'tipoOrigen'));
    }

    public function edit($id)
    {
        return $this->evaluar($id);
    }
    
    public function store(Request $request)
    {
        $tipoOrigen = $request->input('tipo_origen');
        $origenId = $request->input('origen_id');
        
        $esSolicitudTala = false;
        if ($tipoOrigen === 'solicitud' && $origenId) {
            $solicitudCheck = Solicitud::find($origenId);
            if ($solicitudCheck && $solicitudCheck->tipo_tramite === 'tala') {
                $esSolicitudTala = true;
            }
        }

        $validated = $request->validate([
            'tipo_origen'           => 'required|in:solicitud,denuncia,orden',
            'origen_id'             => 'required', // Flexibilizado para soportar tipos de ID mixtos
            'observaciones_campo'   => 'required|string|min:10',
            'dictamen'              => ['required', Rule::in(['APROBADO', 'CON_OBSERVACIONES', 'RECHAZADO', 'REINSPECCION_REQUERIDA'])],
            'hallazgos'             => 'nullable|array',
            'recomendaciones'       => 'nullable|string',
            'evidencias'            => 'nullable|array',
            'evidencias.*'          => 'image|mimes:jpeg,png,jpg,webp|max:5120',

            'cantidad_arboles_talar' => $esSolicitudTala ? 'nullable|required_if:dictamen,APROBADO|integer|min:1' : 'nullable',
            'especie_afectada'       => 'nullable|string|max:100',
            'es_especie_protegida'   => 'nullable|boolean',
            'especie_compensacion'   => $esSolicitudTala ? 'nullable|required_if:dictamen,APROBADO|string|max:100' : 'nullable',
        ]);

        $user = auth()->user();
        
        $origen = match($validated['tipo_origen']) {
            'solicitud' => Solicitud::findOrFail($validated['origen_id']),
            'denuncia'  => Denuncia::findOrFail($validated['origen_id']),
            'orden'     => OrdenInspeccion::findOrFail($validated['origen_id']),
        };

        $inspectorId = $user->inspector?->id ?? $origen->inspector_id;

        if (!$inspectorId && in_array(strtolower($user->role->nombre ?? ''), ['director', 'admin'])) {
            $inspectorFallback = Inspector::first();
            $inspectorId = $inspectorFallback?->id;
        }

        if (!$inspectorId) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['origen_id' => 'No se puede procesar la inspección sin un inspector válido registrado en el sistema.']);
        }

        // BLINDAJE CONTRA RACE CONDITION: Candado atómico por 5 segundos basado en el origen de la inspección
        $lockKey = 'inspeccion_store_lock_' . $validated['tipo_origen'] . '_' . $validated['origen_id'];

        return Cache::lock($lockKey, 5)->get(function () use ($validated, $request, $inspectorId, $origen, $esSolicitudTala) {
            DB::transaction(function () use ($validated, $request, $inspectorId, $origen, $esSolicitudTala) {
                $evidenciasPaths = [];

                if ($request->hasFile('evidencias')) {
                    foreach ($request->file('evidencias') as $foto) {
                        $evidenciasPaths[] = $foto->store('inspecciones/evidencias', 'public');
                    }
                }

                // Generación segura de código de acta único verificado en base de datos
                do {
                    $codigoActa = 'ACTA-' . strtoupper(Str::random(8));
                } while (Inspeccion::where('codigo_acta', $codigoActa)->exists());

                $inspeccionData = [
                    'codigo_acta'         => $codigoActa,
                    'inspector_id'        => $inspectorId,
                    'fecha_visita'        => $request->fecha_visita ?? now(),
                    'observaciones_campo' => $validated['observaciones_campo'],
                    'dictamen'            => $validated['dictamen'],
                    'hallazgos'           => $validated['hallazgos'] ?? [],
                    'recomendaciones'     => $validated['recomendaciones'] ?? null,
                    'fotos_evidencia'     => $evidenciasPaths,
                ];

                // 1. Actualización según origen
                if ($validated['tipo_origen'] === 'solicitud') {
                    $inspeccionData['solicitud_id'] = $origen->id;

                    $nuevoEstado = match($validated['dictamen']) {
                        'APROBADO'               => 'aprobado',
                        'RECHAZADO'              => 'rechazado',
                        'REINSPECCION_REQUERIDA' => 'requiere_reinspeccion',
                        default                  => 'en_inspeccion'
                    };
                    
                    $updateData = ['estado' => $nuevoEstado];
                    if (in_array($nuevoEstado, ['aprobado', 'rechazado'])) {
                        $updateData['fecha_cierre'] = now();
                    }
                    $origen->update($updateData);

                } elseif ($validated['tipo_origen'] === 'denuncia') {
                    $inspeccionData['denuncia_id'] = $origen->id;

                    $nuevoEstatus = match($validated['dictamen']) {
                        'APROBADO', 'CON_OBSERVACIONES' => 'atendido',
                        'RECHAZADO'                      => 'desestimado',
                        'REINSPECCION_REQUERIDA'        => 'requiere_reinspeccion',
                        default                          => 'en_revision'
                    };
                    
                    $updateData = ['estatus' => $nuevoEstatus];
                    if (in_array($nuevoEstatus, ['atendido', 'desestimado'])) {
                        $updateData['fecha_cierre'] = now();
                    }
                    $origen->update($updateData);

                } else { // 'orden' (Empresa)
                    $inspeccionData['orden_inspeccion_id'] = $origen->id;
                    $inspeccionData['empresa_id']          = $origen->empresa_id;

                    $nuevoEstatus = match($validated['dictamen']) {
                        'APROBADO', 'CON_OBSERVACIONES' => 'atendido',
                        'RECHAZADO'                      => 'desestimado',
                        'REINSPECCION_REQUERIDA'        => 'requiere_reinspeccion',
                        default                          => 'en_inspeccion'
                    };

                    $updateData = ['estatus' => $nuevoEstatus];
                    if (in_array($nuevoEstatus, ['atendido', 'desestimado'])) {
                        $updateData['fecha_cierre'] = now();
                    }
                    $origen->update($updateData);
                }

                // 2. Creación del Acta de Inspección
                $inspeccion = Inspeccion::create($inspeccionData);

                // 3. Generación de Compensación Ambiental (Solo si es solicitud de tala aprobada)
                if (
                    $esSolicitudTala &&
                    $validated['dictamen'] === 'APROBADO'
                ) {
                    $cantidadTalar = (int) ($request->cantidad_arboles_talar ?? 1);
                    $esProtegida = (bool) ($request->es_especie_protegida ?? false);

                    $cantidadAsignada = CompensacionService::calcularEjemplares($cantidadTalar, $esProtegida, 8);

                    Compensacion::create([
                        'codigo_resolucion'  => 'RES-COMP-' . date('Y') . '-' . strtoupper(Str::random(5)),
                        'solicitud_id'       => $origen->id,
                        'user_id'            => $origen->user_id,
                        'inspeccion_id'      => $inspeccion->id,
                        'especie_requerida'  => $request->especie_compensacion ?? 'Araguaney / Apamate',
                        'cantidad_asignada'  => $cantidadAsignada,
                        'cantidad_entregada' => 0,
                        'estatus'            => 'PENDIENTE',
                        'observaciones'      => "Compensación por tala de {$cantidadTalar} árbol(es).",
                    ]);
                }
            });

            return redirect()->route('inspecciones.index')
                ->with('success', 'Informe y dictamen de inspección registrados exitosamente.');
        }) ?: back()->withErrors(['origen_id' => 'Ya se está procesando un dictamen para esta inspección. Por favor espera un momento.'])->withInput();
    }

    public function show($id)
    {
        $inspeccion = Inspeccion::with(['solicitud', 'denuncia', 'ordenInspeccion.empresa', 'empresa', 'inspector.user'])->findOrFail($id);

        return view('inspecciones.show', compact('inspeccion'));
    }
}