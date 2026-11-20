<?php

namespace App\Http\Controllers;

use App\Models\Inspeccion;
use App\Models\Solicitud;
use App\Models\Denuncia;
use App\Models\OrdenInspeccion;
use App\Models\Compensacion;
use App\Services\CompensacionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class InspeccionController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $rol = strtolower($user->role->nombre ?? '');

        if ($rol === 'inspector') {
            // MAYÚSCULAS EN LOS ESTADOS
            $solicitudes = Solicitud::where('inspector_id', $user->id)
                ->whereIn('estado', ['INSPECTOR_ASIGNADO', 'EN_INSPECCION', 'REQUIERE_REINSPECCION'])
                ->get();

            $denuncias = Denuncia::where('inspector_id', $user->id)
                ->whereIn('estado', ['INSPECTOR_ASIGNADO', 'EN_REVISION', 'EN_INSPECCION', 'REQUIERE_REINSPECCION'])
                ->get();

            $ordenesEmpresas = OrdenInspeccion::where('inspector_id', $user->id)
                ->whereIn('estatus', ['INSPECTOR_ASIGNADO', 'EN_INSPECCION', 'REQUIERE_REINSPECCION'])
                ->with('empresa')
                ->get();

            $inspeccionesRealizadas = Inspeccion::where('inspector_id', $user->id)
                ->with(['solicitud', 'denuncia', 'ordenInspeccion.empresa', 'empresa'])
                ->latest()
                ->paginate(10);
        } else {
            $solicitudes = Solicitud::whereIn('estado', ['INSPECTOR_ASIGNADO', 'EN_INSPECCION', 'REQUIERE_REINSPECCION'])
                ->with('inspector')
                ->get();

            $denuncias = Denuncia::whereIn('estado', ['INSPECTOR_ASIGNADO', 'EN_REVISION', 'EN_INSPECCION', 'REQUIERE_REINSPECCION'])
                ->with('inspector')
                ->get();

            $ordenesEmpresas = OrdenInspeccion::whereIn('estatus', ['INSPECTOR_ASIGNADO', 'EN_INSPECCION', 'REQUIERE_REINSPECCION'])
                ->with(['inspector', 'empresa'])
                ->get();

            $inspeccionesRealizadas = Inspeccion::with(['solicitud', 'denuncia', 'ordenInspeccion.empresa', 'empresa', 'inspector'])
                ->latest()
                ->paginate(10);
        }

        $asignacionesPendientes = $solicitudes->concat($denuncias)->concat($ordenesEmpresas);
        $currentInspectorId = $user->id;

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

        // Buscar en Orden, Solicitud o Denuncia (Estados en MAYÚSCULAS)
        $ordenEmpresa = OrdenInspeccion::find($id);
        if ($ordenEmpresa) {
            if ($rol === 'inspector' && $ordenEmpresa->inspector_id !== $user->id) {
                abort(403, 'No tienes permiso.');
            }
            
            // ELIMINADO fecha_inicio_inspeccion
            $ordenEmpresa->update(['estatus' => 'EN_INSPECCION']);

            return redirect()->back()->with('success', 'La orden ha pasado a estado "En Inspección".');
        }

        $solicitud = Solicitud::find($id);
        if ($solicitud) {
            $expediente = $solicitud;
            $campoEstatus = 'estado';
            $nuevoEstado = 'EN_INSPECCION';
        } else {
            $expediente = Denuncia::findOrFail($id);
            $campoEstatus = 'estado';
            $nuevoEstado = 'EN_REVISION';
        }

        if ($rol === 'inspector' && $expediente->inspector_id !== $user->id) {
            abort(403, 'No tienes permiso.');
        }

        // ELIMINADO fecha_inicio_inspeccion
        $expediente->update([$campoEstatus => $nuevoEstado]);

        return redirect()->back()->with('success', 'El expediente ha pasado a estado de inspección.');
    }

    public function evaluar($id)
    {
        $orden = OrdenInspeccion::with(['empresa', 'inspector'])->find($id);
        if ($orden) {
            $tipoOrigen = 'orden';
            $expediente = $orden;
            return view('inspecciones.evaluar', compact('expediente', 'tipoOrigen'));
        }

        $solicitud = Solicitud::with(['solicitante', 'inspector'])->find($id);
        if ($solicitud) {
            $tipoOrigen = 'solicitud';
            $expediente = $solicitud;
            return view('inspecciones.evaluar', compact('expediente', 'tipoOrigen'));
        }

        $denuncia = Denuncia::with(['user', 'inspector'])->findOrFail($id);
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
            // CAMBIADO: Evaluar el ID del tipo de trámite, no el string
            if ($solicitudCheck && $solicitudCheck->tipoTramite->slug === 'tala') {
                $esSolicitudTala = true;
            }
        }

        $validated = $request->validate([
            'tipo_origen'           => 'required|in:solicitud,denuncia,orden',
            'origen_id'             => 'required',
            'observaciones_campo'   => 'required|string|min:10',
            'dictamen'              => ['required', Rule::in(['APROBADO', 'CON_OBSERVACIONES', 'RECHAZADO', 'REINSPECCION_REQUERIDA'])],
            'hallazgos'             => 'nullable|array',
            'recomendaciones'       => 'nullable|string',
            'evidencias'            => 'nullable|array',
            'evidencias.*'          => 'image|mimes:jpeg,png,jpg,webp|max:5120',

            'cantidad_arboles_talar' => $esSolicitudTala ? 'nullable|required_if:dictamen,APROBADO|integer|min:1' : 'nullable',
            'especie_afectada'       => 'nullable|string|max:100',
            'especie_compensacion'   => $esSolicitudTala ? 'nullable|required_if:dictamen,APROBADO|string|max:100' : 'nullable',
        ]);

        $user = auth()->user();
        
        $origen = match($validated['tipo_origen']) {
            'solicitud' => Solicitud::findOrFail($validated['origen_id']),
            'denuncia'  => Denuncia::findOrFail($validated['origen_id']),
            'orden'     => OrdenInspeccion::findOrFail($validated['origen_id']),
        };

        $inspectorId = $user->id;
        $lockKey = 'inspeccion_store_lock_' . $validated['tipo_origen'] . '_' . $validated['origen_id'];

        return Cache::lock($lockKey, 5)->get(function () use ($validated, $request, $inspectorId, $origen, $esSolicitudTala) {
            DB::transaction(function () use ($validated, $request, $inspectorId, $origen, $esSolicitudTala) {
                $evidenciasPaths = [];

                if ($request->hasFile('evidencias')) {
                    foreach ($request->file('evidencias') as $foto) {
                        $evidenciasPaths[] = $foto->store('inspecciones/evidencias', 'public');
                    }
                }

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
                    
                    // CAMPOS NUEVOS DEL CICLO DE VIDA
                    'estado'              => 'FINALIZADA',
                    'fecha_inicio'        => now(), 
                    'fecha_cierre'        => now(),
                ];

                if ($validated['tipo_origen'] === 'solicitud') {
                    $inspeccionData['solicitud_id'] = $origen->id;

                    $nuevoEstado = match($validated['dictamen']) {
                        'APROBADO'               => 'APROBADO',
                        'RECHAZADO'              => 'RECHAZADO',
                        'REINSPECCION_REQUERIDA' => 'REQUIERE_REINSPECCION',
                        default                  => 'EN_INSPECCION'
                    };
                    
                    $updateData = ['estado' => $nuevoEstado];
                    if (in_array($nuevoEstado, ['APROBADO', 'RECHAZADO'])) {
                        $updateData['fecha_cierre'] = now();
                    }
                    $origen->update($updateData);

                } elseif ($validated['tipo_origen'] === 'denuncia') {
                    $inspeccionData['denuncia_id'] = $origen->id;

                    $nuevoEstado = match($validated['dictamen']) {
                        'APROBADO', 'CON_OBSERVACIONES' => 'ATENDIDO',
                        'RECHAZADO'                      => 'DESESTIMADO',
                        'REINSPECCION_REQUERIDA'        => 'REQUIERE_REINSPECCION',
                        default                          => 'EN_REVISION'
                    };
                    
                    $updateData = ['estado' => $nuevoEstado];
                    if (in_array($nuevoEstado, ['ATENDIDO', 'DESESTIMADO'])) {
                        $updateData['fecha_cierre'] = now();
                    }
                    $origen->update($updateData);

                } else { // 'orden' (Empresa)
                    $inspeccionData['orden_inspeccion_id'] = $origen->id;
                    $inspeccionData['empresa_id']          = $origen->empresa_id;

                    $nuevoEstado = match($validated['dictamen']) {
                        'APROBADO', 'CON_OBSERVACIONES' => 'ATENDIDO',
                        'RECHAZADO'                      => 'DESESTIMADO',
                        'REINSPECCION_REQUERIDA'        => 'REQUIERE_REINSPECCION',
                        default                          => 'EN_INSPECCION'
                    };

                    $updateData = ['estatus' => $nuevoEstado];
                    if (in_array($nuevoEstado, ['ATENDIDO', 'DESESTIMADO'])) {
                        $updateData['fecha_cierre'] = now();
                    }
                    $origen->update($updateData);
                }

                $inspeccion = Inspeccion::create($inspeccionData);

                if ($esSolicitudTala && $validated['dictamen'] === 'APROBADO') {
                    $cantidadTalar = (int) ($request->cantidad_arboles_talar ?? 1);
                    $cantidadAsignada = CompensacionService::calcularEjemplares($cantidadTalar, 8);

                    Compensacion::create([
                        'codigo_compensacion' => 'COMP-' . date('Y') . '-' . strtoupper(Str::random(5)),
                        'solicitud_id'        => $origen->id,
                        'user_id'             => $origen->user_id,
                        // 'inspeccion_id' YA NO EXISTE
                        'especie_requerida'   => $request->especie_compensacion ?? 'Araguaney',
                        'cantidad_asignada'   => $cantidadAsignada,
                        'cantidad_entregada'  => 0,
                        'estatus'             => 'PENDIENTE',
                        'observaciones'       => "Compensación por tala de {$cantidadTalar} árbol(es).",
                    ]);
                }
            });

            return redirect()->route('inspecciones.index')
                ->with('success', 'Informe y dictamen registrados exitosamente.');
        });
    }

    public function show($id)
    {
        // CORREGIDO: relación inspector.credencialInspector
        $inspeccion = Inspeccion::with(['inspector.credencialInspector', 'ordenInspeccion.empresa', 'empresa'])
            ->findOrFail($id);

        return view('inspecciones.show', compact('inspeccion'));
    }

    public function descargarPdf($id)
    {
        $inspeccion = Inspeccion::with(['inspector.credencialInspector', 'ordenInspeccion.empresa', 'empresa'])
            ->findOrFail($id);

        $urlVerificacion = route('validar.acta', ['codigo' => $inspeccion->codigo_acta]);

        $qrCode = base64_encode(\SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')
            ->size(110)
            ->errorCorrection('H')
            ->generate($urlVerificacion));

        return view('inspecciones.pdf', compact('inspeccion', 'qrCode', 'urlVerificacion'));
    }
}