<?php

namespace App\Http\Controllers\Ciudadano;

use App\Http\Controllers\Controller;
use App\Models\DeclaracionResiduo;
use App\Models\Empresa;
use App\Models\Notificacion;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DeclaracionResiduoController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $empresa = Empresa::where('user_id', $user->id)->first();

        if (!$empresa) {
            return redirect()->route('ciudadano.empresas.create')
                ->with('warning', 'Debes registrar una empresa antes de realizar declaraciones de residuos.');
        }

        $declaraciones = DeclaracionResiduo::with(['detalles'])
            ->where('empresa_id', $empresa->id)
            ->latest()
            ->paginate(10);

        return view('ciudadano.empresa.declaraciones', compact('empresa', 'declaraciones'));
    }

    public function create()
    {
        $user = Auth::user();
        $empresa = Empresa::with('perfilAmbiental')->where('user_id', $user->id)->firstOrFail();

        return view('ciudadano.empresa.declarar', compact('empresa'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $empresa = Empresa::with('perfilAmbiental')->where('user_id', $user->id)->firstOrFail();

        $rolDeclarante = $request->input('rol_declarante', 'GENERADOR');
        $isNoAplica = $rolDeclarante === 'NO_APLICA';

        $rules = [
            'rol_declarante'          => 'required|in:GENERADOR,MANEJADOR,NO_APLICA',
            'periodo'                 => 'required|string|max:20',
            'fecha_inicio_periodo'    => [
                'required',
                'date',
                function ($attribute, $value, $fail) {
                    if ($value && \Carbon\Carbon::parse($value)->dayOfWeekIso !== 1) {
                        $fail('La fecha de inicio del período debe ser obligatoriamente un día Lunes.');
                    }
                },
            ],
            'fecha_fin_periodo'       => [
                $isNoAplica ? 'nullable' : 'required',
                'date',
                function ($attribute, $value, $fail) use ($request) {
                    if ($value) {
                        $carbonFin = \Carbon\Carbon::parse($value)->startOfDay();
                        if ($carbonFin->dayOfWeekIso !== 7) {
                            $fail('La fecha de fin del período debe ser obligatoriamente un día Domingo.');
                        }
                        if ($request->filled('fecha_inicio_periodo')) {
                            $carbonInicio = \Carbon\Carbon::parse($request->input('fecha_inicio_periodo'))->startOfDay();
                            $domingoEsperado = $carbonInicio->copy()->addDays(6);

                            if (!$carbonFin->equalTo($domingoEsperado)) {
                                $fail('El rango de la declaración semanal debe comprender exactamente desde el lunes hasta el domingo de la misma semana.');
                            }
                        }
                    }
                },
            ],
            'manejadora_rif'          => 'nullable|string|max:255',
            'manejadora_nombre'       => 'nullable|string|max:255',
            'generador_origen_nombre' => 'nullable|string|max:255',
            'generador_origen_rif'    => 'nullable|string|max:255',
            'hoja_seguimiento_numero' => 'nullable|string|max:50',
            'vehiculo_placa'          => 'nullable|string|max:20',
            'destino_final_nombre'    => 'nullable|string|max:255',
            'declaracion_jurada'      => $isNoAplica ? 'required|accepted' : 'nullable',
            'manifiestos'             => 'nullable|array',
            'manifiestos.*'           => 'file|mimes:pdf,jpg,jpeg,png|max:5120',
        ];

        if (!$isNoAplica) {
            $rules['detalles']                  = 'required|array|min:1';
            $rules['detalles.*.codigo_residuo'] = 'required|string';
            $rules['detalles.*.nombre_residuo'] = 'required|string';
            $rules['detalles.*.categoria']      = 'required|in:COMUN,RECICLABLE,PELIGROSO';
            $rules['detalles.*.tipo_operacion'] = 'nullable|string';
            $rules['detalles.*.cantidad']       = 'required|numeric|min:0';
            $rules['detalles.*.unidad_medida']  = 'required|string';
            $rules['detalles.*.manifiesto']     = 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120';
        }

        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return back()->withInput()->with('error', 'Error de validación: ' . collect($validator->errors()->all())->implode(' | '));
        }

        $validated = $validator->validated();
        $lockKey = 'declaracion_store_lock_' . $empresa->id . '_' . md5($validated['periodo'] . $validated['fecha_inicio_periodo']);

        return Cache::lock($lockKey, 5)->get(function () use ($request, $validated, $empresa, $user, $isNoAplica, $rolDeclarante) {
            $archivosProcesados = [];
            $declaracionId = null;

            try {
                DB::transaction(function () use ($request, $validated, $empresa, $user, $isNoAplica, $rolDeclarante, &$archivosProcesados, &$declaracionId) {
                    
                    $archivosGuardados = [];
                    if ($request->hasFile('manifiestos')) {
                        foreach ($request->file('manifiestos') as $file) {
                            $path = $file->store("declaraciones/{$empresa->id}/generales", 'private');
                            $archivosProcesados[] = $path;
                            $archivosGuardados[] = [
                                'nombre_original' => $file->getClientOriginalName(),
                                'path'            => $path,
                                'mime_type'       => $file->getClientMimeType(),
                            ];
                        }
                    }

                    $tonComunes = 0.000;
                    $tonReciclables = 0.000;
                    $tonPeligrosas = 0.000;
                    $detallesAInsertar = [];

                    if (!$isNoAplica && isset($validated['detalles'])) {
                        foreach ($validated['detalles'] as $index => $item) {
                            $factorTon = match (strtoupper($item['unidad_medida'])) {
                                'TON'    => 1.0,
                                'KG'     => 0.001,
                                'LITROS' => 0.001,
                                'M3'     => 1.0,
                                default  => 0.001,
                            };

                            $cantidadTon = round((float)$item['cantidad'] * $factorTon, 3);

                            match ($item['categoria']) {
                                'COMUN'      => $tonComunes += $cantidadTon,
                                'RECICLABLE' => $tonReciclables += $cantidadTon,
                                'PELIGROSO'  => $tonPeligrosas += $cantidadTon,
                            };

                            $manifiestoDetallePath = null;
                            if ($request->hasFile("detalles.{$index}.manifiesto")) {
                                $fileDetalle = $request->file("detalles.{$index}.manifiesto");
                                $manifiestoDetallePath = $fileDetalle->store("declaraciones/{$empresa->id}/detalles", 'private');
                                $archivosProcesados[] = $manifiestoDetallePath;
                            }

                            $detallesAInsertar[] = [
                                'codigo_residuo'     => $item['codigo_residuo'],
                                'nombre_residuo'     => $item['nombre_residuo'],
                                'categoria'          => $item['categoria'],
                                'tipo_operacion'     => $item['tipo_operacion'] ?? null,
                                'cantidad'           => $item['cantidad'],
                                'unidad_medida'      => $item['unidad_medida'],
                                'cantidad_toneladas' => $cantidadTon,
                                'manifiesto_path'    => $manifiestoDetallePath,
                            ];
                        }
                    }

                    $declaracion = DeclaracionResiduo::create([
                        'empresa_id'              => $empresa->id,
                        'user_id'                 => $user->id,
                        'rol_declarante'          => $rolDeclarante,
                        'periodo'                 => $validated['periodo'],
                        'fecha_inicio_periodo'    => $validated['fecha_inicio_periodo'],
                        'fecha_fin_periodo'       => $validated['fecha_fin_periodo'] ?? $validated['fecha_inicio_periodo'],
                        'toneladas_comunes'       => $tonComunes,
                        'toneladas_reciclables'   => $tonReciclables,
                        'toneladas_peligrosas'    => $tonPeligrosas,
                        'manejadora_rif'          => $validated['manejadora_rif'] ?? null,
                        'manejadora_nombre'       => $validated['manejadora_nombre'] ?? null,
                        'generador_origen_nombre' => $validated['generador_origen_nombre'] ?? null,
                        'generador_origen_rif'    => $validated['generador_origen_rif'] ?? null,
                        'hoja_seguimiento_numero' => $validated['hoja_seguimiento_numero'] ?? null,
                        'vehiculo_placa'          => $validated['vehiculo_placa'] ?? null,
                        'destino_final_nombre'    => $validated['destino_final_nombre'] ?? null,
                        'manifiestos_archivos'    => $archivosGuardados,
                        'estatus'                 => 'PRESENTADA',
                    ]);

                    $declaracionId = $declaracion->id;

                    if (!empty($detallesAInsertar)) {
                        $declaracion->detalles()->createMany($detallesAInsertar);
                    }
                });

                // NOTIFICAR AL DIRECTOR DE RESIDUOS O ADMINS
                $directoresResiduos = User::whereHas('role', function ($q) {
                    $q->whereIn('nombre', ['admin', 'director', 'director_residuos']);
                })->get();

                foreach ($directoresResiduos as $dir) {
                    Notificacion::create([
                        'user_id'  => $dir->id,
                        'titulo'   => 'Nueva Declaración de Residuos',
                        'mensaje'  => "La empresa {$empresa->razon_social} ha presentado una nueva declaración para el período {$validated['periodo']}.",
                        'tipo'     => 'info',
                        'link_url' => route('director.residuos.declaraciones.show', $declaracionId),
                    ]);
                }

            } catch (\Throwable $e) {
                foreach ($archivosProcesados as $path) {
                    Storage::disk('private')->delete($path);
                }

                return back()->withInput()->with('error', 'Ocurrió un error en base de datos: ' . $e->getMessage());
            }

            $mensajeExito = $isNoAplica 
                ? 'Notificación simplificada registrada exitosamente.' 
                : 'Declaración de residuos presentada exitosamente.';

            return redirect()->route('ciudadano.declaraciones.index')
                ->with('success', $mensajeExito);
        }) ?: back()->withInput()->with('error', 'Ya se está procesando una declaración idéntica para este período. Por favor espera un momento.');
    }
}