<?php

namespace App\Http\Controllers\Ciudadano;

use App\Http\Controllers\Controller;
use App\Models\Solicitud;
use App\Models\SolicitudRecaudo;
use App\Services\TerritorioService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
                'descripcion' => 'Autorización técnica para labores de mantenimiento, acondicionamiento de vegetación o poda de árboles.',
                'plazoDias' => 90,
                'requisitosObligatorios' => [
                    ['key' => 'cedula', 'label' => 'Copia de la Cédula de Identidad', 'hint' => 'Requisito inicial obligatorio'],
                    ['key' => 'carta_explicativa', 'label' => 'Carta Explicativa del Trabajo a Realizar', 'hint' => 'Detallar motivos e intervención']
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
            ],
            'desmalezamiento_conformacion' => [
                'nombre' => 'Permiso de Desmalezamiento y Conformación de Terreno',
                'descripcion' => 'Permisos para trabajos de limpieza profunda, acondicionamiento y movimiento menor de suelos.',
                'plazoDias' => 90,
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
        $validated = $request->validate([
            'tipo_tramite'                    => 'required|string|in:inspeccion,poda_deshierbe,desmalezamiento_conformacion',
            'comuna'                          => 'required|string',
            'sector'                          => 'required|string',
            'direccion_exacta'                => 'required|string|max:500',
            'motivo_solicitud'                => 'required|string|max:1000',
            'aplica_autorizacion_propietario' => 'nullable|boolean',
            'aplica_variables_ambientales'    => 'nullable|boolean',
            'es_persona_juridica'             => 'nullable|boolean',
            'documentos'                      => 'required|array|min:1',
            'documentos.*'                    => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);

        DB::transaction(function () use ($request, $validated) {
            $yearActual = Carbon::now()->year;

            // Bloqueamos la fila más reciente del año en curso para sincronizar la concurrencia
            $ultimaSolicitud = DB::table('solicitudes')
                ->whereYear('created_at', $yearActual)
                ->orderBy('created_at', 'desc')
                ->lockForUpdate()
                ->first();

            // Conteo secuencial del año
            $conteoAno = DB::table('solicitudes')
                ->whereYear('created_at', $yearActual)
                ->count();

            $ultimoSecuencial = $conteoAno + 1;
            $codigoExpediente = sprintf('SOL-%d-%04d', $yearActual, $ultimoSecuencial);

            // Guardamos las condicionales en el JSONB/JSON
            $datosEspecificos = [
                'aplica_autorizacion_propietario' => $request->boolean('aplica_autorizacion_propietario'),
                'aplica_variables_ambientales'    => $request->boolean('aplica_variables_ambientales'),
                'es_persona_juridica'             => $request->boolean('es_persona_juridica'),
            ];

            // Determinación de Requisitos Esperados vs Consignados
            $configTramites = $this->getTiposTramitesConfig();
            $configActual = $configTramites[$validated['tipo_tramite']] ?? null;

            $keysEsperadas = [];
            if ($configActual) {
                // Requisitos Obligatorios
                foreach ($configActual['requisitosObligatorios'] as $req) {
                    $keysEsperadas[] = $req['key'];
                }
                // Requisitos Condicionales según los flags marcados
                foreach ($configActual['requisitosCondicionales'] as $req) {
                    if (!empty($req['flag_check']) && $request->boolean($req['flag_check'])) {
                        $keysEsperadas[] = $req['key'];
                    }
                }
                // Requisitos Opcionales/Complementarios
                foreach ($configActual['requisitosOpcionales'] as $req) {
                    $keysEsperadas[] = $req['key'];
                }
            }

            // Claves de archivos realmente adjuntados en la petición
            $documentosSubidos = $request->file('documentos') ?? [];
            $keysSubidas = array_keys(array_filter($documentosSubidos, fn($file) => $file !== null));

            // Comparar si faltan archivos esperados
            $faltantes = array_diff($keysEsperadas, $keysSubidas);
            $plazoDias = $configActual['plazoDias'] ?? 0;

            if (count($faltantes) > 0 && $plazoDias > 0) {
                $estadoFinal = 'incompleta';
                $fechaLimite = Carbon::now()->addDays($plazoDias);
                $proximoPaso = 'Consignación de documentación pendiente por parte del ciudadano';
            } else {
                $estadoFinal = 'pendiente';
                $fechaLimite = null;
                $proximoPaso = 'Asignación de inspector para revisión técnica';
            }

            $solicitud = Solicitud::create([
                'id'                      => (string) Str::uuid(),
                'codigo_expediente'       => $codigoExpediente,
                'user_id'                 => auth()->id(),
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

            if ($request->hasFile('documentos')) {
                foreach ($request->file('documentos') as $key => $file) {
                    if ($file && $file->isValid()) {
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
            }
        });

        return redirect()
            ->route('ciudadano.solicitudes')
            ->with('success', 'Tu solicitud ha sido registrada exitosamente.');
    }
}