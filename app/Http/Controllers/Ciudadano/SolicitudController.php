<?php

namespace App\Http\Controllers\Ciudadano;

use App\Http\Controllers\Controller;
use App\Models\Solicitud;
use App\Models\SolicitudRecaudo;
use App\Models\RecaudoCatalogo;
use App\Models\TipoTramite;
use App\Services\TerritorioService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class SolicitudController extends Controller
{
    private function getTiposTramitesConfig(): array
    {
        return \App\Models\TipoTramite::with('recaudos')->get()->mapWithKeys(function ($tramite) {
            
            // Helper para inyectar el "flag" a los requisitos condicionales
            $mapCondicionales = function ($recaudos) {
                return $recaudos->map(function ($r) {
                    $flag = match($r->key) {
                        'autorizacion_propietario' => 'aplicaAutorizacionPropietario',
                        'variables_ambientales' => 'aplicaVariablesAmbientales',
                        'registro_mercantil', 'rif_empresa' => 'esPersonaJuridica',
                        default => null
                    };
                    $arr = $r->toArray();
                    $arr['flag'] = $flag;
                    return $arr;
                })->values()->toArray();
            };

            return [
                $tramite->slug => [
                    'nombre' => $tramite->nombre,
                    'descripcion' => $tramite->descripcion,
                    'plazoDias' => $tramite->plazo_dias,
                    'requisitosObligatorios' => $tramite->recaudos->where('tipo_requisito', 'OBLIGATORIO')->values()->toArray(),
                    'requisitosCondicionales' => $mapCondicionales($tramite->recaudos->where('tipo_requisito', 'CONDICIONAL')),
                    'requisitosOpcionales' => $tramite->recaudos->where('tipo_requisito', 'OPCIONAL')->values()->toArray(),
                ]
            ];
        })->toArray();
    }

    private function getKeysEsperadasParaSolicitud(string $slugTramite, array $datosEspecificos): array
    {
        $tipoTramite = TipoTramite::where('slug', $slugTramite)->first();
        if (!$tipoTramite) return [];

        $keysObligatoriasYOpcionales = RecaudoCatalogo::where('tipo_tramite_id', $tipoTramite->id)
            ->whereIn('tipo_requisito', ['OBLIGATORIO', 'OPCIONAL'])
            ->pluck('key')
            ->toArray();

        $keysCondicionales = [];
        if (!empty($datosEspecificos['aplica_autorizacion_propietario'])) {
            $keysCondicionales[] = 'autorizacion_propietario';
        }
        if (!empty($datosEspecificos['aplica_variables_ambientales'])) {
            $keysCondicionales[] = 'variables_ambientales';
        }
        if (!empty($datosEspecificos['es_persona_juridica'])) {
            $keysCondicionales[] = 'registro_mercantil';
            $keysCondicionales[] = 'rif_empresa';
        }

        return array_unique(array_merge($keysObligatoriasYOpcionales, $keysCondicionales));
    }

    public function index()
    {
        $territorios = TerritorioService::getTerritorios();
        $tiposTramites = $this->getTiposTramitesConfig();

        $tramites = Solicitud::with(['inspector', 'recaudos.recaudoCatalogo', 'tipoTramite'])
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
            return redirect()->back()->withInput()->with('error', "Has superado el límite. Espera {$minutes} minuto(s).");
        }

        $validated = $request->validate([
            'tipo_tramite'                     => 'required|string|exists:tipos_tramites,slug', // Validamos contra el catálogo
            'comuna'                           => 'required|string|exists:territorios,name',
            'sector'                           => 'required|string',
            'direccion_exacta'                 => 'required|string|max:500',
            'motivo_solicitud'                 => 'required|string|max:1000',
            'aplica_autorizacion_propietario'  => 'nullable|boolean',
            'aplica_variables_ambientales'     => 'nullable|boolean',
            'es_persona_juridica'              => 'nullable|boolean',
            'documentos'                       => 'nullable|array',
            'documentos.*'                     => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);

        if (!TerritorioService::verificarExistencia($validated['comuna'], $validated['sector'])) {
            return redirect()->back()->withInput()->with('error', 'El sector no pertenece a la comuna indicada.');
        }

        DB::transaction(function () use ($request, $validated, $userId) {
            $yearActual = Carbon::now()->year;
            DB::statement('SELECT pg_advisory_xact_lock(?)', [crc32('solicitudes_codigo_' . $yearActual)]);

            $ultimoSecuencial = DB::table('solicitudes')->whereYear('created_at', $yearActual)->count() + 1;
            $codigoSolicitud = sprintf('SOL-%d-%04d', $yearActual, $ultimoSecuencial);

            $datosEspecificos = [
                'aplica_autorizacion_propietario' => $request->boolean('aplica_autorizacion_propietario'),
                'aplica_variables_ambientales'    => $request->boolean('aplica_variables_ambientales'),
                'es_persona_juridica'             => $request->boolean('es_persona_juridica'),
            ];

            $tipoTramite = TipoTramite::where('slug', $validated['tipo_tramite'])->firstOrFail();
            
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
            $estadoFinal = (count($faltantes) > 0 && $plazoDias > 0) ? 'INCOMPLETA' : 'PENDIENTE'; // <-- MAYÚSCULAS
            $fechaLimite = (count($faltantes) > 0 && $plazoDias > 0) ? Carbon::now()->addDays($plazoDias) : null;

            $solicitud = Solicitud::create([
                'id'                      => (string) Str::uuid(),
                'codigo_solicitud'        => $codigoSolicitud, // <-- CORREGIDO
                'user_id'                 => $userId,
                'inspector_id'            => null,
                'tipo_tramite_id'         => $tipoTramite->id, // <-- CORREGIDO (ID en vez de string)
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

                    // Buscar en el catálogo por el ID del trámite y la key
                    $recaudoCatalogo = RecaudoCatalogo::where('tipo_tramite_id', $tipoTramite->id)
                        ->where('key', $key)
                        ->first();

                    if ($recaudoCatalogo) {
                        SolicitudRecaudo::create([
                            'id'                  => (string) Str::uuid(),
                            'solicitud_id'        => $solicitud->id,
                            'recaudo_catalogo_id' => $recaudoCatalogo->id, // <-- CORREGIDO (ID)
                            'nombre_archivo'      => $file->getClientOriginalName(),
                            'bucket_path'         => $path,
                            'archivo_url'         => Storage::url($path),
                        ]);
                    }
                }
            }
        });

        RateLimiter::hit($rateLimitKey, 600);

        return redirect()->route('ciudadano.solicitudes')->with('success', 'Tu solicitud ha sido registrada exitosamente.');
    }

    public function subirPendientes(Request $request, $id)
    {
        $solicitud = Solicitud::with('recaudos')->where('user_id', auth()->id())->findOrFail($id);

        if ($solicitud->fecha_limite_documentos && Carbon::now()->gt(Carbon::parse($solicitud->fecha_limite_documentos))) {
            $solicitud->estado = 'RECHAZADO';
            $solicitud->observaciones = 'Plazo de consignación de documentos vencido.';
            $solicitud->save();
            return redirect()->route('ciudadano.solicitudes')->with('error', 'El plazo límite ha expirado. La solicitud ha sido rechazada.');
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

                        $recaudoCatalogo = RecaudoCatalogo::where('tipo_tramite_id', $solicitud->tipo_tramite_id)
                            ->where('key', $key)
                            ->first();

                        if ($recaudoCatalogo) {
                            SolicitudRecaudo::updateOrCreate(
                                [
                                    'solicitud_id' => $solicitud->id,
                                    'recaudo_catalogo_id' => $recaudoCatalogo->id,
                                ],
                                [
                                    'id'             => (string) Str::uuid(),
                                    'nombre_archivo' => $file->getClientOriginalName(),
                                    'bucket_path'    => $path,
                                    'archivo_url'    => Storage::url($path),
                                ]
                            );
                        }
                    }
                }
            }

            $solicitud->load('recaudos');

            $datosEspecificos = is_array($solicitud->datos_especificos) 
                ? $solicitud->datos_especificos 
                : (json_decode($solicitud->datos_especificos, true) ?? []);

            $keysEsperadas = $this->getKeysEsperadasParaSolicitud($solicitud->tipoTramite->slug, $datosEspecificos);
            
            $consignados = $solicitud->recaudos->map(function($rec) {
                return $rec->recaudoCatalogo->key;
            })->toArray();

            $faltantes = array_diff($keysEsperadas, $consignados);

            if (count($faltantes) === 0) {
                $solicitud->estado = 'PENDIENTE';
                $solicitud->fecha_limite_documentos = null;
                $solicitud->save();
            }
        });

        return redirect()->route('ciudadano.solicitudes')->with('success', 'Documentos adjuntados exitosamente.');
    }

    public function descargarComprobante($id)
    {
        $solicitud = Solicitud::with(['usuario', 'recaudos.recaudoCatalogo', 'inspector', 'tipoTramite'])
            ->where('user_id', auth()->id())
            ->findOrFail($id);

        if ($solicitud->estado === 'INCOMPLETA') {
            return redirect()->back()->with('error', 'No se puede generar el documento mientras tenga recaudos pendientes.');
        }

        $urlVerificacion = route('validar.expediente', ['codigo' => $solicitud->codigo_solicitud]);

        $qrCode = base64_encode(QrCode::format('svg')
            ->size(120)
            ->errorCorrection('H')
            ->generate($urlVerificacion));

        return view('ciudadano.pdf.comprobante-solicitud', compact('solicitud', 'qrCode', 'urlVerificacion'));
    }
}