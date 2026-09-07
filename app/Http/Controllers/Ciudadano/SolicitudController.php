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
    public function index()
    {
        $territorios = TerritorioService::getTerritorios();

        $tiposTramites = [
            'inspeccion' => [
                'nombre' => 'Solicitud de Inspección TÉCNICA',
                'descripcion' => 'Evaluación e inspección en sitio por parte del personal técnico ambiental.',
                'plazoDias' => 0,
                'requisitosObligatorios' => [
                    ['key' => 'cedula', 'label' => 'Copia de la Cédula de Identidad', 'hint' => 'PDF o imagen legible']
                ],
                'requisitosCondicionales' => []
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
                    ]
                ],
                'requisitosOpcionales' => [
                    ['key' => 'registro_rif', 'label' => 'Registro Mercantil y RIF', 'hint' => 'Solo si es Persona Jurídica'],
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
                    ]
                ],
                'requisitosOpcionales' => [
                    ['key' => 'registro_rif', 'label' => 'Registro Mercantil y RIF', 'hint' => 'Solo si es Persona Jurídica'],
                    ['key' => 'doc_propiedad', 'label' => 'Documento de Propiedad Registrado', 'hint' => 'Título o documento legal'],
                    ['key' => 'plano_ubicacion', 'label' => 'Plano de Ubicación del Terreno', 'hint' => 'Croquis o plano de mensura'],
                    ['key' => 'solvencia_municipal', 'label' => 'Solvencia Municipal Vigente', 'hint' => 'Emitida por la Alcaldía'],
                    ['key' => 'constancia_catastral', 'label' => 'Constancia Catastral Vigente', 'hint' => 'Catastro municipal'],
                    ['key' => 'carta_compromiso', 'label' => 'Carta de Compromiso de Reposición', 'hint' => 'En caso de afectación a bienes públicos']
                ]
            ]
        ];

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

            // Realizamos el conteo directo sin asociarlo al lockForUpdate
            $conteoAno = DB::table('solicitudes')
                ->whereYear('created_at', $yearActual)
                ->count();

            $ultimoSecuencial = $conteoAno + 1;
            $codigoExpediente = sprintf('SOL-%d-%04d', $yearActual, $ultimoSecuencial);

            // Guardamos las condicionales en el JSONB
            $datosEspecificos = [
                'aplica_autorizacion_propietario' => $request->boolean('aplica_autorizacion_propietario'),
                'aplica_variables_ambientales'    => $request->boolean('aplica_variables_ambientales'),
            ];

            $solicitud = Solicitud::create([
                'id'                 => (string) Str::uuid(),
                'codigo_expediente'  => $codigoExpediente,
                'user_id'            => auth()->id(),
                'inspector_id'       => null,
                'tipo_tramite'       => $validated['tipo_tramite'],
                'comuna'             => $validated['comuna'],
                'sector'             => $validated['sector'],
                'direccion_exacta'   => $validated['direccion_exacta'],
                'motivo_descripcion' => $validated['motivo_solicitud'],
                'datos_especificos'  => $datosEspecificos,
                'estado'             => 'pendiente',
                'proximo_paso'       => 'Asignación de inspector para revisión técnica',
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