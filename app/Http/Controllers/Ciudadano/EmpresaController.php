<?php

namespace App\Http\Controllers\Ciudadano;

use App\Http\Controllers\Controller;
use App\Models\Empresa;
use App\Models\Sancion;
use App\Models\RequisitoEmpresaCatalogo;
use App\Models\DocumentoExpediente;
use App\Services\TerritorioService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class EmpresaController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $empresa = $user->empresa()->with(['perfilAmbiental', 'documentosExpediente.requisito', 'sanciones'])->first();

        if (!$empresa) {
            return redirect()->route('ciudadano.empresas.create');
        }

        return view('ciudadano.empresa.index', compact('empresa'));
    }

    public function show(Request $request)
    {
        return $this->index($request);
    }

    public function create()
    {
        $user = auth()->user();
        $empresa = $user->empresa()->with(['perfilAmbiental', 'documentosExpediente'])->first();

        if ($empresa && !in_array($empresa->estatus, ['OBSERVADO', 'RECHAZADO'])) {
            return redirect()->route('ciudadano.empresas');
        }

        // Requisitos activos, con toda la metadata nueva
        $requisitosCatalogo = RequisitoEmpresaCatalogo::where('activo', true)
            ->orderBy('orden')
            ->orderBy('nombre')
            ->get();

        return view('ciudadano.empresa.crear', compact('empresa', 'requisitosCatalogo'));
    }

    public function sancionesIndex(Request $request)
    {
        $user = auth()->user();
        $empresa = $user->empresa;

        if (!$empresa) {
            return redirect()->route('ciudadano.empresas.create');
        }

        $sanciones = Sancion::where('empresa_id', $empresa->id)->latest()->get();

        return view('ciudadano.empresa.sanciones', compact('empresa', 'sanciones'));
    }

    public function store(Request $request)
    {
        $user = auth()->id();
        $empresaExistente = Empresa::where('user_id', $user)->first();

        if ($empresaExistente && !in_array($empresaExistente->estatus, ['OBSERVADO', 'RECHAZADO'])) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Ya posees una empresa registrada y activa.'], 422);
            }
            return redirect()->route('ciudadano.empresas')->with('error', 'Ya tienes una empresa vinculada a tu cuenta.');
        }

        $rifRule = $empresaExistente 
            ? 'required|string|unique:empresas,rif,' . $empresaExistente->id 
            : 'required|string|unique:empresas,rif';

        $request->validate([
            'rif'                   => $rifRule,
            'razon_social'          => 'required|string',
            'numero_cuenta_patente' => 'required|string',
            'correo_comercial'      => 'required|email',
            'telefono_comercial'    => 'required|string',
            'comuna'                => 'required|string|exists:territorios,name',
            'sector'                => 'required|string',
            'calle_avenida'         => 'required|string',
            'edificio_galpon_local' => 'required|string',
            'documentos'            => 'nullable|array',
            'documentos.*'          => 'file|mimes:pdf,jpg,jpeg,png|max:5120',
            'fechas_emision'        => 'nullable|array',
            'fechas_emision.*'      => 'nullable|date',
            'fechas_vencimiento'    => 'nullable|array',
            'fechas_vencimiento.*'  => 'nullable|date',
            'es_vitalicio'          => 'nullable|array',
        ]);

        if (!TerritorioService::verificarExistencia($request->input('comuna'), $request->input('sector'))) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'El sector no pertenece a la comuna indicada.'], 422);
            }
            return redirect()->back()->withInput()->with('error', 'El sector no pertenece a la comuna indicada.');
        }

        $lockKey = 'empresa_store_lock_' . $user;

        return Cache::lock($lockKey, 5)->get(function () use ($request, $empresaExistente, $user) {
            return DB::transaction(function () use ($request, $empresaExistente, $user) {
                if ($empresaExistente) {
                    $empresaExistente->update([
                        'rif'                    => strtoupper(trim($request->rif)),
                        'razon_social'           => trim($request->razon_social),
                        'nombre_comercial'       => $request->nombre_comercial ? trim($request->nombre_comercial) : null,
                        'numero_cuenta_patente'  => trim($request->numero_cuenta_patente),
                        'correo_comercial'       => strtolower(trim($request->correo_comercial)),
                        'telefono_comercial'     => trim($request->telefono_comercial),
                        'comuna'                 => trim($request->comuna),
                        'sector'                 => trim($request->sector),
                        'calle_avenida'          => trim($request->calle_avenida),
                        'edificio_galpon_local'  => trim($request->edificio_galpon_local),
                        'estatus'                => 'PENDIENTE_APROBACION',
                        'observaciones_administrativas' => null, // <-- CORREGIDO
                    ]);
                    $empresa = $empresaExistente;
                    $empresa->perfilAmbiental()->delete();
                } else {
                    $empresa = Empresa::create([
                        'user_id'               => $user,
                        'rif'                   => strtoupper(trim($request->rif)),
                        'razon_social'          => trim($request->razon_social),
                        'nombre_comercial'      => $request->nombre_comercial ? trim($request->nombre_comercial) : null,
                        'numero_cuenta_patente' => trim($request->numero_cuenta_patente),
                        'correo_comercial'      => strtolower(trim($request->correo_comercial)),
                        'telefono_comercial'    => trim($request->telefono_comercial),
                        'comuna'                => trim($request->comuna),
                        'sector'                => trim($request->sector),
                        'calle_avenida'         => trim($request->calle_avenida),
                        'edificio_galpon_local' => trim($request->edificio_galpon_local),
                        'estatus'               => 'PENDIENTE_APROBACION',
                    ]);
                }

                if ($request->has('perfil')) {
                    $perfilData = is_string($request->perfil) ? json_decode($request->perfil, true) : $request->perfil;

                    $rolAmbiental = $perfilData['rol_ambiental'] ?? 'GENERADOR';

                    $numRacda = trim($perfilData['numero_racda'] ?? '');
                    if (empty($numRacda) || Str::contains(strtolower($numRacda), 'no aplica')) {
                        $numRacda = null;
                    }

                    $cleanArray = function ($key) use ($perfilData) {
                        if (!isset($perfilData[$key])) return null;
                        $val = $perfilData[$key];
                        if (is_array($val)) return $val;
                        if (is_string($val)) {
                            $decoded = json_decode($val, true);
                            return is_array($decoded) ? $decoded : [$val];
                        }
                        return null;
                    };

                    $esGenerador = ($rolAmbiental === 'GENERADOR');
                    $esManejador = ($rolAmbiental === 'MANEJADOR');

                    // SALVAGUARDA: Si el rol es NO_APLICA, forzar todos los flags a false
                    if ($rolAmbiental === 'NO_APLICA') {
                        $perfilData['genera_efluentes']  = false;
                        $perfilData['genera_emisiones']  = false;
                        $perfilData['genera_peligrosos'] = false;
                        $perfilData['genera_ruidos']     = false;
                        $perfilData['usa_pozo_agua']     = false;
                        $perfilData['modalidad_manejadora'] = null;
                        $perfilData['modalidad_atencion_manejador'] = null;
                        $perfilData['manejadora_contratada'] = null;
                        $perfilData['manejadora_rif'] = null;
                        $perfilData['manejadora_racda'] = null;
                        $perfilData['generador_exclusivo_nombre'] = null;
                        $perfilData['generador_exclusivo_rif'] = null;
                        $perfilData['generador_exclusivo_racda'] = null;
                        $perfilData['subtipo_generador'] = null;
                    }

                    $empresa->perfilAmbiental()->create([
                        'rol_ambiental'                => $rolAmbiental,
                        'subtipo_generador'            => $esGenerador ? ($perfilData['subtipo_generador'] ?? 'PEQUENO') : null,
                        'numero_racda'                 => $numRacda,

                        'genera_efluentes'             => filter_var($perfilData['genera_efluentes'] ?? false, FILTER_VALIDATE_BOOLEAN),
                        'genera_emisiones'             => filter_var($perfilData['genera_emisiones'] ?? false, FILTER_VALIDATE_BOOLEAN),
                        'genera_peligrosos'            => filter_var($perfilData['genera_peligrosos'] ?? false, FILTER_VALIDATE_BOOLEAN),
                        'genera_ruidos'                => filter_var($perfilData['genera_ruidos'] ?? false, FILTER_VALIDATE_BOOLEAN),
                        'usa_pozo_agua'                => filter_var($perfilData['usa_pozo_agua'] ?? false, FILTER_VALIDATE_BOOLEAN),

                        'modalidad_manejadora'         => $esGenerador ? ($perfilData['modalidad_manejadora'] ?? 'PROPIA') : null,
                        'manejadora_contratada'        => $esGenerador ? ($perfilData['manejadora_contratada'] ?? $perfilData['manejadora_nombre'] ?? null) : null,
                        'manejadora_rif'               => $esGenerador ? ($perfilData['manejadora_rif'] ?? null) : null,
                        'manejadora_racda'             => $esGenerador ? ($perfilData['manejadora_racda'] ?? null) : null,

                        'modalidad_atencion_manejador' => $esManejador ? ($perfilData['modalidad_atencion_manejador'] ?? 'MULTICLIENTE') : null,
                        'generador_exclusivo_nombre'   => $esManejador ? ($perfilData['generador_exclusivo_nombre'] ?? null) : null,
                        'generador_exclusivo_rif'      => $esManejador ? ($perfilData['generador_exclusivo_rif'] ?? null) : null,
                        'generador_exclusivo_racda'    => $esManejador ? ($perfilData['generador_exclusivo_racda'] ?? null) : null,

                        'actividades_manejador'        => $esManejador ? $cleanArray('actividades_manejador') : null,
                        'sustancias_manejadas'         => $perfilData['sustancias_manejadas'] ?? null,
                        'residuos_tipos'               => $cleanArray('residuos_tipos'),
                        'residuos_comunes'             => $cleanArray('residuos_comunes'),
                        'residuos_reciclables'         => $cleanArray('residuos_reciclables'),
                        'residuos_peligrosos'          => $cleanArray('residuos_peligrosos'),
                    ]);
                }

                // VALIDACIÓN SERVER-SIDE DE REQUISITOS OBLIGATORIOS
                if (!isset($perfilData)) {
                    $perfilData = $request->input('perfil', []);
                    if (is_string($perfilData)) {
                        $perfilData = json_decode($perfilData, true) ?: [];
                    }
                }

                $requisitosInfo = $this->getRequisitosEsperados($perfilData, $request->rif);
                $documentosSubidosIds = array_map('intval', array_keys($request->file('documentos', []) ?: []));

                $faltantes = $requisitosInfo['obligatorios']
                    ->filter(fn($req) => !in_array($req->id, $documentosSubidosIds))
                    ->pluck('nombre')
                    ->values()
                    ->toArray();

                if (!empty($faltantes)) {
                    $mensaje = 'Faltan los siguientes recaudos obligatorios: ' . implode(', ', $faltantes);

                    if ($request->wantsJson()) {
                        return response()->json([
                            'success' => false,
                            'message' => $mensaje,
                            'errors'  => ['documentos' => [$mensaje]],
                        ], 422);
                    }

                    return redirect()->back()->withInput()->withErrors(['documentos' => $mensaje]);
                }

                if ($request->hasFile('documentos')) {
                    $fechasEmision     = $request->input('fechas_emision', []);
                    $fechasVencimiento = $request->input('fechas_vencimiento', []);
                    $esVitalicio       = $request->input('es_vitalicio', []);

                    foreach ($request->file('documentos') as $requisitoId => $file) {
                        if ($file && $file->isValid()) {
                            $path = $file->store("expedientes/{$empresa->id}", 'public');

                            // CORREGIDO: buscar por ID (único) en lugar de por código
                            $catalogoRequisito = RequisitoEmpresaCatalogo::find((int) $requisitoId);
                            
                            if (!$catalogoRequisito) {
                                continue; // Requisito inexistente, saltar
                            }

                            $vitalicio = filter_var($esVitalicio[$requisitoId] ?? false, FILTER_VALIDATE_BOOLEAN);

                            $empresa->documentosExpediente()->updateOrCreate(
                                ['requisito_id' => $catalogoRequisito->id],
                                [
                                    'archivo_path'      => $path,
                                    'fecha_emision'     => $fechasEmision[$requisitoId] ?? null,
                                    'fecha_vencimiento' => $vitalicio ? null : ($fechasVencimiento[$requisitoId] ?? null),
                                    'es_vitalicio'      => $vitalicio,
                                    'estado_revision'   => 'OBSERVADO',
                                ]
                            );
                        }
                    }
                }

                $directoresResiduos = \App\Models\User::whereHas('role', function ($q) {
                    $q->whereIn('nombre', ['admin', 'director', 'director_residuos']);
                })->get();

                foreach ($directoresResiduos as $dir) {
                    \App\Models\Notificacion::create([
                        'user_id'  => $dir->id,
                        'titulo'   => 'Nueva Empresa / Expediente',
                        'mensaje'  => "Se ha registrado la empresa {$empresa->razon_social} (RIF: {$empresa->rif}) y espera validación.",
                        'tipo'     => 'INFORMACION', // <-- CORREGIDO
                        'link_url' => route('director.residuos.empresas.show', $empresa->id),
                    ]);
                }

                if ($request->wantsJson()) {
                    return response()->json(['success' => true, 'message' => 'Expediente procesado y enviado a revisión correctamente.']);
                }

                return redirect()->route('ciudadano.empresas')->with('success', 'Expediente actualizado y reenviado a revisión.');
            });
        }) ?: back()->withErrors(['rif' => 'Se está procesando una solicitud idéntica de registro de empresa. Por favor espera un momento.'])->withInput();
    }

    public function verDocumento(DocumentoExpediente $documento)
    {
        $user = auth()->user();
        $rolesAutorizados = ['admin', 'director', 'fiscal', 'analista'];

        if ($documento->empresa->user_id !== $user->id && !$user->hasAnyRole($rolesAutorizados)) {
            abort(403, 'No tienes permisos para visualizar este documento.');
        }

        if (Storage::disk('public')->exists($documento->archivo_path)) {
            return Storage::disk('public')->response($documento->archivo_path);
        }

        if (Storage::disk('private')->exists($documento->archivo_path)) {
            return Storage::disk('private')->response($documento->archivo_path);
        }

        abort(404, 'El archivo solicitado no existe en el servidor.');
    }

    public function renovarDocumento(Request $request, DocumentoExpediente $documento)
    {
        $user = auth()->user();

        if ($documento->empresa->user_id !== $user->id) {
            abort(403, 'No tienes autorización para realizar esta acción.');
        }

        $request->validate([
            'archivo'           => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'fecha_emision'     => 'required|date',
            'fecha_vencimiento' => 'nullable|date',
            'es_vitalicio'      => 'nullable|boolean',
        ]);

        $vitalicio = filter_var($request->input('es_vitalicio', false), FILTER_VALIDATE_BOOLEAN);

        if ($documento->archivo_path && Storage::disk('public')->exists($documento->archivo_path)) {
            Storage::disk('public')->delete($documento->archivo_path);
        }

        $path = $request->file('archivo')->store("expedientes/{$documento->empresa_id}", 'public');

        $documento->update([
            'archivo_path'      => $path,
            'fecha_emision'     => $request->fecha_emision,
            'fecha_vencimiento' => $vitalicio ? null : $request->fecha_vencimiento,
            'es_vitalicio'      => $vitalicio,
            'estado_revision'   => 'OBSERVADO', // <-- CORREGIDO
        ]);

        return redirect()->route('ciudadano.empresas')->with('success', 'Documento actualizado y enviado a revisión correctamente.');
    }

    /**
     * Determina qué requisitos aplican según el perfil ambiental y el RIF de la empresa.
     * Devuelve los IDs de todos los aplicables y la colección de los obligatorios.
     */
    private function getRequisitosEsperados(array $perfilData, string $rif): array
    {
        $rol = $perfilData['rol_ambiental'] ?? 'GENERADOR';
        $rifPrefix = strtoupper(substr(trim($rif), 0, 1));
        $esPersonaJuridica = in_array($rifPrefix, ['J', 'G']);

        $todos = RequisitoEmpresaCatalogo::where('activo', true)->get();

        $obligatorios = collect();

        foreach ($todos as $req) {
            // 1. Filtrar por rol
            $roles = array_map('trim', explode(',', $req->aplica_a_rol ?? 'TODOS'));
            if (!in_array('TODOS', $roles) && !in_array($rol, $roles)) {
                continue;
            }

            // 2. Filtrar por condicion_flag
            if ($req->condicion_flag) {
                $aplica = match ($req->condicion_flag) {
                    'genera_peligrosos'      => filter_var($perfilData['genera_peligrosos'] ?? false, FILTER_VALIDATE_BOOLEAN),
                    'genera_efluentes'       => filter_var($perfilData['genera_efluentes'] ?? false, FILTER_VALIDATE_BOOLEAN),
                    'genera_emisiones'       => filter_var($perfilData['genera_emisiones'] ?? false, FILTER_VALIDATE_BOOLEAN),
                    'usa_pozo_agua'          => filter_var($perfilData['usa_pozo_agua'] ?? false, FILTER_VALIDATE_BOOLEAN),
                    'genera_riesgo_ambiental' => (
                        filter_var($perfilData['genera_peligrosos'] ?? false, FILTER_VALIDATE_BOOLEAN)
                        || filter_var($perfilData['genera_efluentes'] ?? false, FILTER_VALIDATE_BOOLEAN)
                        || filter_var($perfilData['genera_emisiones'] ?? false, FILTER_VALIDATE_BOOLEAN)
                    ),
                    'modalidad_externa'      => ($perfilData['modalidad_manejadora'] ?? '') === 'EXTERNA',
                    'es_persona_juridica'    => $esPersonaJuridica,
                    default                  => true,
                };

                if (!$aplica) continue;
            }

            if ($req->tipo_requisito === 'OBLIGATORIO') {
                $obligatorios->push($req);
            }
        }

        return ['obligatorios' => $obligatorios];
    }
}