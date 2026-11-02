<?php

namespace App\Http\Controllers\Ciudadano;

use App\Http\Controllers\Controller;
use App\Models\Solicitud;
use App\Models\SolicitudRecaudo;
use App\Services\TerritorioService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SolicitudController extends Controller
{
    private function getTiposTramitesConfig(): array
    {
        return [
            'inspeccion' => [
                'nombre' => 'Solicitud de Inspección TÉCNICA',
                'descripcion' => 'Evaluación e inspección en sitio por parte del personal técnico ambiental.',
                'plazoDias' => 0,
                'requisitosObligatorios' => [
                    ['key' => 'cedula', 'label' => 'Copia de la Cédula de Identidad', 'hint' => 'PDF o imagen legible']
                ],
                'requisitosCondicionales' => [],
                'requisitosOpcionales' => []
            ],
            'poda_deshierbe' => [
                'nombre' => 'Solicitud de Permiso para Poda o Deshierbe',
                'descripcion' => 'Autorización técnica para labores de mantenimiento, fitosanitarias o poda de formación de vegetación.',
                'plazoDias' => 31,
                'requisitosObligatorios' => [
                    ['key' => 'cedula', 'label' => 'Copia de la Cédula de Identidad', 'hint' => 'Requisito inicial obligatorio'],
                    ['key' => 'carta_explicativa', 'label' => 'Carta Explicativa del Trabajo a Realizar', 'hint' => 'Detallar motivos y ramas a intervenir']
                ],
                'requisitosCondicionales' => [
                    [
                        'key' => 'autorizacion_propietario', 
                        'label' => 'Autorización del Propietario', 
                        'hint' => 'Requerido si no eres el propietario directo del inmueble/terreno',
                        'flag_check' => 'aplica_autorizacion_propietario'
                    ],
                    [
                        'key' => 'variables_ambientales', 
                        'label' => 'Variables Ambientales (MINEC)', 
                        'hint' => 'Requerido si el terreno se encuentra en Zona Protectora o requiere afectación vegetal ante el MINEC',
                        'flag_check' => 'aplica_variables_ambientales'
                    ],
                    [
                        'key' => 'registro_mercantil', 
                        'label' => 'Copia del Registro Mercantil', 
                        'hint' => 'Requerido para Personas Jurídicas / Empresas',
                        'flag_check' => 'es_persona_juridica'
                    ],
                    [
                        'key' => 'rif_empresa', 
                        'label' => 'Copia del RIF Vigente de la Empresa', 
                        'hint' => 'Requerido para Personas Jurídicas / Empresas',
                        'flag_check' => 'es_persona_juridica'
                    ]
                ],
                'requisitosOpcionales' => [
                    ['key' => 'doc_propiedad', 'label' => 'Documento de Propiedad Registrado', 'hint' => 'Título o documento legal'],
                    ['key' => 'plano_ubicacion', 'label' => 'Plano de Ubicación del Terreno', 'hint' => 'Croquis o plano de mensura'],
                    ['key' => 'solvencia_municipal', 'label' => 'Solvencia Municipal Vigente', 'hint' => 'Emitida por la Alcaldía'],
                    ['key' => 'constancia_catastral', 'label' => 'Constancia Catastral Vigente', 'hint' => 'Catastro municipal']
                ]
            ],
            'tala' => [
                'nombre' => 'Solicitud de Permiso para Tala de Árboles',
                'descripcion' => 'Autorización técnica para el derribo/remoción de ejemplares arbóreos por riesgo, infraestructura o enfermedad.',
                'plazoDias' => 31,
                'requisitosObligatorios' => [
                    ['key' => 'cedula', 'label' => 'Copia de la Cédula de Identidad', 'hint' => 'Requisito inicial obligatorio'],
                    ['key' => 'carta_explicativa', 'label' => 'Carta Justificativa de Tala', 'hint' => 'Detallar motivos de riesgo, daño a infraestructura o estado fitosanitario del árbol'],
                    ['key' => 'carta_compromiso', 'label' => 'Carta de Compromiso de Compensación Ambiental', 'hint' => 'Compromiso firmado para la donación o reforestación de ejemplares']
                ],
                'requisitosCondicionales' => [
                    [
                        'key' => 'autorizacion_propietario', 
                        'label' => 'Autorización del Propietario', 
                        'hint' => 'Requerido si no eres el propietario directo del inmueble/terreno',
                        'flag_check' => 'aplica_autorizacion_propietario'
                    ],
                    [
                        'key' => 'variables_ambientales', 
                        'label' => 'Variables Ambientales (MINEC)', 
                        'hint' => 'Requerido si el terreno se encuentra en Zona Protectora o de resguardo hídrico',
                        'flag_check' => 'aplica_variables_ambientales'
                    ],
                    [
                        'key' => 'registro_mercantil', 
                        'label' => 'Copia del Registro Mercantil', 
                        'hint' => 'Requerido para Personas Jurídicas / Empresas',
                        'flag_check' => 'es_persona_juridica'
                    ],
                    [
                        'key' => 'rif_empresa', 
                        'label' => 'Copia del RIF Vigente de la Empresa', 
                        'hint' => 'Requerido para Personas Jurídicas / Empresas',
                        'flag_check' => 'es_persona_juridica'
                    ]
                ],
                'requisitosOpcionales' => [
                    ['key' => 'doc_propiedad', 'label' => 'Documento de Propiedad Registrado', 'hint' => 'Título o documento legal'],
                    ['key' => 'plano_ubicacion', 'label' => 'Plano de Ubicación del Terreno', 'hint' => 'Croquis o plano de mensura'],
                    ['key' => 'solvencia_municipal', 'label' => 'Solvencia Municipal Vigente', 'hint' => 'Emitida por la Alcaldía'],
                    ['key' => 'constancia_catastral', 'label' => 'Constancia Catastral Vigente', 'hint' => 'Catastro municipal']
                ]
            ],
            'desmalezamiento_conformacion' => [
                'nombre' => 'Permiso de Desmalezamiento y Conformación de Terreno',
                'descripcion' => 'Permisos para trabajos de limpieza profunda, acondicionamiento y movimiento menor de suelos.',
                'plazoDias' => 31,
                'requisitosObligatorios' => [
                    ['key' => 'cedula', 'label' => 'Copia de la Cédula de Identidad', 'hint' => 'Requisito inicial obligatorio'],
                    ['key' => 'carta_explicativa', 'label' => 'Carta Explicativa del Trabajo a Realizar', 'hint' => 'Detallar intervención de suelo']
                ],
                'requisitosCondicionales' => [
                    [
                        'key' => 'autorizacion_propietario', 
                        'label' => 'Autorización del Propietario', 
                        'hint' => 'Requerido si no eres el propietario directo del inmueble/terreno',
                        'flag_check' => 'aplica_autorizacion_propietario'
                    ],
                    [
                        'key' => 'variables_ambientales', 
                        'label' => 'Variables Ambientales (MINEC)', 
                        'hint' => 'Requerido si el terreno se encuentra en Zona Protectora o requiere afectación vegetal ante el MINEC',
                        'flag_check' => 'aplica_variables_ambientales'
                    ],
                    [
                        'key' => 'registro_mercantil', 
                        'label' => 'Copia del Registro Mercantil', 
                        'hint' => 'Requerido para Personas Jurídicas / Empresas',
                        'flag_check' => 'es_persona_juridica'
                    ],
                    [
                        'key' => 'rif_empresa', 
                        'label' => 'Copia del RIF Vigente de la Empresa', 
                        'hint' => 'Requerido para Personas Jurídicas / Empresas',
                        'flag_check' => 'es_persona_juridica'
                    ]
                ],
                'requisitosOpcionales' => [
                    ['key' => 'doc_propiedad', 'label' => 'Documento de Propiedad Registrado', 'hint' => 'Título o documento legal'],
                    ['key' => 'plano_ubicacion', 'label' => 'Plano de Ubicación del Terreno', 'hint' => 'Croquis o plano de mensura'],
                    ['key' => 'solvencia_municipal', 'label' => 'Solvencia Municipal Vigente', 'hint' => 'Emitida por la Alcaldía'],
                    ['key' => 'constancia_catastral', 'label' => 'Constancia Catastral Vigente', 'hint' => 'Catastro municipal'],
                    ['key' => 'carta_compromiso', 'label' => 'Carta de Compromiso de Reposición', 'hint' => 'En caso de afectación a bienes públicos']
                ]
            ]
        ];
    }

    private function getKeysEsperadasParaSolicitud(string $tipoTramite, array $datosEspecificos): array
    {
        $configTramites = $this->getTiposTramitesConfig();
        $configActual = $configTramites[$tipoTramite] ?? null;
        if (!$configActual) {
            return [];
        }

        $keys = [];

        // 1. Obligatorios
        foreach ($configActual['requisitosObligatorios'] as $req) {
            $keys[] = $req['key'];
        }

        // 2. Condicionales activos
        if (!empty($datosEspecificos['aplica_autorizacion_propietario'])) {
            $keys[] = 'autorizacion_propietario';
        }
        if (!empty($datosEspecificos['aplica_variables_ambientales'])) {
            $keys[] = 'variables_ambientales';
        }
        if (!empty($datosEspecificos['es_persona_juridica'])) {
            $keys[] = 'registro_mercantil';
            $keys[] = 'rif_empresa';
        }

        // 3. Opcionales / Documentos Complementarios
        if (!empty($configActual['requisitosOpcionales'])) {
            foreach ($configActual['requisitosOpcionales'] as $req) {
                $keys[] = $req['key'];
            }
        }

        return array_unique($keys);
    }

    public function index()
    {
        $territorios = TerritorioService::getTerritorios();
        $tiposTramites = $this->getTiposTramitesConfig();

        $tramites = Solicitud::with(['inspector', 'recaudos'])
            ->where('user_id', auth()->id())
            ->latest()
            ->get();

        return view('ciudadano.solicitudes', compact('tramites', 'territorios', 'tiposTramites'));
    }

    public function store(Request $request)
    {
        $userId = auth()->id();

        $rateLimitKey = 'solicitud-submission:' . $userId;
        if (RateLimiter::tooManyAttempts($rateLimitKey, 3)) {
            $seconds = RateLimiter::availableIn($rateLimitKey);
            $minutes = ceil($seconds / 60);

            return redirect()->back()
                ->withInput()
                ->with('error', "Has superado el límite de solicitudes permitidas. Por favor, espera {$minutes} minuto(s) antes de intentar registrar un nuevo trámite.");
        }

        $duplicateExists = Solicitud::where('user_id', $userId)
            ->where('tipo_tramite', $request->input('tipo_tramite'))
            ->where('motivo_descripcion', $request->input('motivo_solicitud'))
            ->where('created_at', '>=', now()->subSeconds(60))
            ->exists();

        if ($duplicateExists) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Ya has enviado una solicitud con estas especificaciones recientemente. Revisa tu lista de trámites.');
        }

        $validated = $request->validate([
            'tipo_tramite'                     => 'required|string|in:inspeccion,poda_deshierbe,desmalezamiento_conformacion,tala',
            'comuna'                           => 'required|string',
            'sector'                           => 'required|string',
            'direccion_exacta'                 => 'required|string|max:500',
            'motivo_solicitud'                 => 'required|string|max:1000',
            'aplica_autorizacion_propietario'  => 'nullable|boolean',
            'aplica_variables_ambientales'     => 'nullable|boolean',
            'es_persona_juridica'              => 'nullable|boolean',
            'documentos'                       => 'nullable|array',
            'documentos.*'                     => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);

        DB::transaction(function () use ($request, $validated, $userId) {
            $yearActual = Carbon::now()->year;

            DB::statement('SELECT pg_advisory_xact_lock(?)', [crc32('solicitudes_codigo_' . $yearActual)]);

            $ultimoSecuencial = DB::table('solicitudes')
                ->whereYear('created_at', $yearActual)
                ->count() + 1;

            $codigoExpediente = sprintf('SOL-%d-%04d', $yearActual, $ultimoSecuencial);

            $datosEspecificos = [
                'aplica_autorizacion_propietario' => $request->boolean('aplica_autorizacion_propietario'),
                'aplica_variables_ambientales'    => $request->boolean('aplica_variables_ambientales'),
                'es_persona_juridica'             => $request->boolean('es_persona_juridica'),
            ];

            $configTramites = $this->getTiposTramitesConfig();
            $configActual = $configTramites[$validated['tipo_tramite']] ?? null;
            $plazoDias = $configActual['plazoDias'] ?? 0;

            $keysEsperadas = $this->getKeysEsperadasParaSolicitud($validated['tipo_tramite'], $datosEspecificos);

            $documentosSubidos = $request->file('documentos') ?? [];
            $keysSubidas = [];
            foreach ($documentosSubidos as $key => $file) {
                if ($file && $file->isValid() && $file->getSize() > 0) {
                    $keysSubidas[] = $key;
                }
            }

            $faltantes = array_diff($keysEsperadas, $keysSubidas);

            if (count($faltantes) > 0 && $plazoDias > 0) {
                $estadoFinal = 'incompleta';
                $fechaLimite = Carbon::now()->addDays($plazoDias);
            } else {
                $estadoFinal = 'pendiente';
                $fechaLimite = null;
            }

            $solicitud = Solicitud::create([
                'id'                      => (string) Str::uuid(),
                'codigo_expediente'       => $codigoExpediente,
                'user_id'                 => $userId,
                'inspector_id'            => null,
                'tipo_tramite'            => $validated['tipo_tramite'],
                'comuna'                  => $validated['comuna'],
                'sector'                  => $validated['sector'],
                'direccion_exacta'        => $validated['direccion_exacta'],
                'motivo_descripcion'      => $validated['motivo_solicitud'],
                'datos_especificos'       => $datosEspecificos,
                'estado'                  => $estadoFinal,
                'fecha_limite_documentos' => $fechaLimite,
            ]);

            foreach ($documentosSubidos as $key => $file) {
                if ($file && $file->isValid() && $file->getSize() > 0) {
                    $path = $file->store("solicitudes/{$solicitud->id}", 'public');
                    $extension = strtolower($file->getClientOriginalExtension());

                    SolicitudRecaudo::create([
                        'id'                       => (string) Str::uuid(),
                        'solicitud_id'             => $solicitud->id,
                        'tipo_recaudo'             => $key,
                        'nombre_archivo'           => $file->getClientOriginalName(),
                        'bucket_path'              => $path,
                        'archivo_url'              => Storage::url($path),
                        'es_evidencia_fotografica' => in_array($extension, ['jpg', 'jpeg', 'png']),
                        'created_at'               => now(),
                    ]);
                }
            }
        });

        RateLimiter::hit($rateLimitKey, 600);

        return redirect()
            ->route('ciudadano.solicitudes')
            ->with('success', 'Tu solicitud ha sido registrada exitosamente.');
    }

    public function subirPendientes(Request $request, $id)
    {
        $solicitud = Solicitud::with('recaudos')->where('user_id', auth()->id())->findOrFail($id);

        if ($solicitud->fecha_limite_documentos && Carbon::now()->gt(Carbon::parse($solicitud->fecha_limite_documentos))) {
            $solicitud->estado = 'cancelado';
            $solicitud->save();

            return redirect()->route('ciudadano.solicitudes')
                ->with('error', 'El plazo límite para consignar la documentación de este expediente ha expirado. La solicitud ha sido cancelada y debes crear una nueva.');
        }

        $request->validate([
            'documentos'   => 'required|array|min:1',
            'documentos.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);

        DB::transaction(function () use ($request, $solicitud) {
            if ($request->hasFile('documentos')) {
                foreach ($request->file('documentos') as $key => $file) {
                    if ($file && $file->isValid()) {
                        $path = $file->store("solicitudes/{$solicitud->id}", 'public');
                        $extension = strtolower($file->getClientOriginalExtension());

                        SolicitudRecaudo::updateOrCreate(
                            [
                                'solicitud_id' => $solicitud->id,
                                'tipo_recaudo' => $key,
                            ],
                            [
                                'id'                       => (string) Str::uuid(),
                                'nombre_archivo'           => $file->getClientOriginalName(),
                                'bucket_path'              => $path,
                                'archivo_url'              => Storage::url($path),
                                'es_evidencia_fotografica' => in_array($extension, ['jpg', 'jpeg', 'png']),
                                'created_at'               => now(),
                            ]
                        );
                    }
                }
            }

            $solicitud->load('recaudos');

            $datosEspecificos = is_array($solicitud->datos_especificos) 
                ? $solicitud->datos_especificos 
                : (json_decode($solicitud->datos_especificos, true) ?? []);

            $keysEsperadas = $this->getKeysEsperadasParaSolicitud($solicitud->tipo_tramite, $datosEspecificos);
            $consignados = $solicitud->recaudos->pluck('tipo_recaudo')->toArray();
            $faltantes = array_diff($keysEsperadas, $consignados);

            // SI YA NO QUEDAN FALTANTES, pasa a 'pendiente' y notifica al Director de Ambiente
            if (count($faltantes) === 0) {
                $solicitud->estado = 'pendiente';
                $solicitud->fecha_limite_documentos = null;
                $solicitud->save();

                // DISPARAR NOTIFICACIÓN GERENCIAL AL DIRECTOR DE AMBIENTE
                $directoresAmbiente = \App\Models\User::whereHas('role', function ($q) {
                    $q->whereIn('nombre', ['admin', 'director', 'director_ambiente']);
                })->get();

                foreach ($directoresAmbiente as $dir) {
                    \App\Models\Notificacion::create([
                        'user_id'  => $dir->id,
                        'titulo'   => 'Documentación Completa de Solicitud',
                        'mensaje'  => "El expediente {$solicitud->codigo_expediente} ha completado sus recaudos pendientes y está listo para revisión.",
                        'tipo'     => 'info',
                        'link_url' => route('director.ambiente.solicitudes'),
                    ]);
                }
            }
        });

        return redirect()
            ->route('ciudadano.solicitudes')
            ->with('success', 'Documentos adjuntados exitosamente. El expediente ha sido actualizado y enviado a revisión.');
    }
}