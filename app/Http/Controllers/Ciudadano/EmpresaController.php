<?php

namespace App\Http\Controllers\Ciudadano;

use App\Http\Controllers\Controller;
use App\Models\Empresa;
use App\Models\PerfilAmbiental;
use App\Models\DocumentoExpediente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class EmpresaController extends Controller
{
    /**
     * Muestra el estado actual o expediente de la empresa del ciudadano.
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        // Cargar la empresa vinculada al usuario
        $empresa = $user->empresa()->with([
            'perfilAmbiental',
            'documentosExpediente',
        ])->first();

        // Si no tiene empresa registrada, lo enviamos al formulario de registro
        if (!$empresa) {
            return redirect()->route('ciudadano.empresas.create');
        }

        return view('ciudadano.empresa.index', compact('empresa'));
    }

    /**
     * Alias de index() por si alguna ruta apunta a show().
     */
    public function show(Request $request)
    {
        return $this->index($request);
    }

    /**
     * Retorna la vista del formulario para registrar una nueva empresa.
     */
    public function create()
    {
        return view('ciudadano.empresa.crear');
    }

    /**
     * Almacena una nueva empresa vinculada al ciudadano autenticado.
     */
    public function store(Request $request)
    {
        // Validar que el ciudadano no posea ya una empresa registrada
        if (auth()->user()->empresa) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Ya posees una empresa registrada.'], 422);
            }
            return redirect()->route('ciudadano.empresas')->with('error', 'Ya tienes una empresa vinculada a tu cuenta.');
        }

        // Validación de datos de entrada
        $request->validate([
            'rif'                   => 'required|string|unique:empresas,rif',
            'razon_social'          => 'required|string',
            'numero_cuenta_patente' => 'required|string',
            'correo_comercial'      => 'required|email',
            'telefono_comercial'    => 'required|string',
            'comuna'                => 'required|string',
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

        return DB::transaction(function () use ($request) {
            $empresa = Empresa::create([
                'user_id'               => auth()->id(),
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

            if ($request->has('perfil')) {
                $perfilData = is_string($request->perfil) ? json_decode($request->perfil, true) : $request->perfil;

                $rolAmbiental = $perfilData['rol_ambiental'] ?? 'GENERADOR';

                // Normalización de RACDA
                $numRacda = trim($perfilData['numero_racda'] ?? '');
                if (empty($numRacda) || Str::contains(strtolower($numRacda), 'no aplica')) {
                    $numRacda = null;
                }

                // Helper para garantizar formateo de arrays JSON
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

                // Datos exclusivos de Generador
                $modalidadManejadora  = $esGenerador ? ($perfilData['modalidad_manejadora'] ?? 'PROPIA') : null;
                $manejadoraContratada = $esGenerador ? ($perfilData['manejadora_contratada'] ?? $perfilData['manejadora_nombre'] ?? null) : null;
                $manejadoraRif        = $esGenerador ? ($perfilData['manejadora_rif'] ?? null) : null;
                $manejadoraRacda      = $esGenerador ? ($perfilData['manejadora_racda'] ?? null) : null;

                // Datos exclusivos de Manejador
                $modalidadAtencion        = $esManejador ? ($perfilData['modalidad_atencion_manejador'] ?? 'MULTICLIENTE') : null;
                $generadorExclusivoNombre = $esManejador ? ($perfilData['generador_exclusivo_nombre'] ?? null) : null;
                $generadorExclusivoRif    = $esManejador ? ($perfilData['generador_exclusivo_rif'] ?? null) : null;
                $generadorExclusivoRacda  = $esManejador ? ($perfilData['generador_exclusivo_racda'] ?? null) : null;
                $actividadesManejador     = $esManejador ? $cleanArray('actividades_manejador') : null;

                $empresa->perfilAmbiental()->create([
                    'rol_ambiental'                => $rolAmbiental,
                    'subtipo_generador'            => $esGenerador ? ($perfilData['subtipo_generador'] ?? 'PEQUENO') : null,
                    'numero_racda'                 => $numRacda,

                    'genera_efluentes'             => filter_var($perfilData['genera_efluentes'] ?? false, FILTER_VALIDATE_BOOLEAN),
                    'genera_emisiones'             => filter_var($perfilData['genera_emisiones'] ?? false, FILTER_VALIDATE_BOOLEAN),
                    'genera_peligrosos'            => filter_var($perfilData['genera_peligrosos'] ?? false, FILTER_VALIDATE_BOOLEAN),
                    'genera_ruidos'                => filter_var($perfilData['genera_ruidos'] ?? false, FILTER_VALIDATE_BOOLEAN),
                    'usa_pozo_agua'                => filter_var($perfilData['usa_pozo_agua'] ?? false, FILTER_VALIDATE_BOOLEAN),

                    // Manejadora externa (si es GENERADOR)
                    'modalidad_manejadora'         => $modalidadManejadora,
                    'manejadora_contratada'        => $manejadoraContratada,
                    'manejadora_rif'               => $manejadoraRif,
                    'manejadora_racda'             => $manejadoraRacda,

                    // Cobertura / clientes (si es MANEJADOR)
                    'modalidad_atencion_manejador' => $modalidadAtencion,
                    'generador_exclusivo_nombre'   => $generadorExclusivoNombre,
                    'generador_exclusivo_rif'      => $generadorExclusivoRif,
                    'generador_exclusivo_racda'    => $generadorExclusivoRacda,

                    // Actividades y Listados de Residuos
                    'actividades_manejador'        => $actividadesManejador,
                    'sustancias_manejadas'         => $perfilData['sustancias_manejadas'] ?? null,
                    'residuos_tipos'               => $cleanArray('residuos_tipos'),
                    'residuos_comunes'             => $cleanArray('residuos_comunes'),
                    'residuos_reciclables'         => $cleanArray('residuos_reciclables'),
                    'residuos_peligrosos'          => $cleanArray('residuos_peligrosos'),
                ]);
            }

            if ($request->hasFile('documentos')) {
                $nombresRequisitos = [
                    'doc_renma'           => 'Registro RENMA',
                    'doc_no_objecion'     => 'Constancia de No Objeción Ambiental',
                    'doc_racda'           => 'Registro RACDA',
                    'doc_poliza'          => 'Póliza de Seguro de Responsabilidad Ambiental',
                    'doc_efluentes'       => 'Caracterización de Efluentes Líquidos',
                    'doc_emisiones'       => 'Caracterización de Emisiones Atmosféricas',
                    'doc_plan_desechos'   => 'Plan de Manejo de Desechos y Residuos Sólidos',
                    'doc_plan_emergencia' => 'Plan de Emergencia y Contingencia Ambiental',
                ];

                $fechasEmision     = $request->input('fechas_emision', []);
                $fechasVencimiento = $request->input('fechas_vencimiento', []);
                $esVitalicio       = $request->input('es_vitalicio', []);

                foreach ($request->file('documentos') as $codigoRequisito => $file) {
                    if ($file && $file->isValid()) {
                        $path = $file->store("expedientes/{$empresa->id}", 'public');

                        $vitalicio = filter_var($esVitalicio[$codigoRequisito] ?? false, FILTER_VALIDATE_BOOLEAN);

                        $empresa->documentosExpediente()->create([
                            'codigo_requisito'  => strtoupper($codigoRequisito),
                            'nombre_requisito'  => $nombresRequisitos[$codigoRequisito] ?? 'Documento Adjunto',
                            'archivo_path'      => $path,
                            'fecha_emision'     => $fechasEmision[$codigoRequisito] ?? null,
                            'fecha_vencimiento' => $vitalicio ? null : ($fechasVencimiento[$codigoRequisito] ?? null),
                            'es_vitalicio'      => $vitalicio,
                            'estado_revision'   => 'VALIDO',
                        ]);
                    }
                }
            }

            if ($request->wantsJson()) {
                return response()->json(['success' => true, 'message' => 'Empresa registrada correctamente.']);
            }

            return redirect()->route('ciudadano.empresas')->with('success', 'Empresa registrada en proceso de revisión.');
        });
    }

    /**
     * Sirve archivos del expediente validando la propiedad de la empresa o roles administrativos.
     */
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
}