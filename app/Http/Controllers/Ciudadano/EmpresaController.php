<?php

namespace App\Http\Controllers\Ciudadano;

use App\Http\Controllers\Controller;
use App\Models\Empresa;
use App\Models\PerfilAmbiental;
use App\Models\DocumentoExpediente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class EmpresaController extends Controller
{
    /**
     * Muestra el estado actual o expediente de la empresa del ciudadano.
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        // Cargar la empresa vinculada al usuario excluyendo declaracionesResiduos por ahora
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
        ]);

        return DB::transaction(function () use ($request) {
            $empresa = Empresa::create([
                'user_id'               => auth()->id(),
                'rif'                   => $request->rif,
                'razon_social'          => $request->razon_social,
                'nombre_comercial'      => $request->nombre_comercial,
                'numero_cuenta_patente' => $request->numero_cuenta_patente,
                'correo_comercial'      => $request->correo_comercial,
                'telefono_comercial'    => $request->telefono_comercial,
                'comuna'                => $request->comuna,
                'sector'                => $request->sector,
                'calle_avenida'         => $request->calle_avenida,
                'edificio_galpon_local' => $request->edificio_galpon_local,
                'estatus'               => 'PENDIENTE_APROBACION',
            ]);

            if ($request->has('perfil')) {
                $perfilData = is_string($request->perfil) ? json_decode($request->perfil, true) : $request->perfil;

                $empresa->perfilAmbiental()->create([
                    'rol_ambiental'         => $perfilData['rol_ambiental'] ?? 'GENERADOR',
                    'subtipo_generador'     => $perfilData['subtipo_generador'] ?? 'PEQUENO',
                    'numero_racda'          => $perfilData['numero_racda'] ?? 'RACDA No aplica',
                    'genera_efluentes'      => filter_var($perfilData['genera_efluentes'] ?? false, FILTER_VALIDATE_BOOLEAN),
                    'genera_emisiones'      => filter_var($perfilData['genera_emisiones'] ?? false, FILTER_VALIDATE_BOOLEAN),
                    'genera_peligrosos'     => filter_var($perfilData['genera_peligrosos'] ?? false, FILTER_VALIDATE_BOOLEAN),
                    'genera_ruidos'         => filter_var($perfilData['genera_ruidos'] ?? false, FILTER_VALIDATE_BOOLEAN),
                    'usa_pozo_agua'         => filter_var($perfilData['usa_pozo_agua'] ?? false, FILTER_VALIDATE_BOOLEAN),
                    'manejadora_contratada' => $perfilData['manejadora_contratada'] ?? null,
                    'actividades_manejador'  => isset($perfilData['actividades_manejador']) ? json_encode($perfilData['actividades_manejador']) : null,
                    'sustancias_manejadas'   => $perfilData['sustancias_manejadas'] ?? null,
                    'residuos_tipos'        => isset($perfilData['residuos_tipos']) && !empty($perfilData['residuos_tipos']) 
                                                ? json_encode($perfilData['residuos_tipos']) 
                                                : null,
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

                foreach ($request->file('documentos') as $codigoRequisito => $file) {
                    if ($file && $file->isValid()) {
                        $path = $file->store("expedientes/{$empresa->id}", 'private');

                        $empresa->documentosExpediente()->create([
                            'codigo_requisito' => strtoupper($codigoRequisito),
                            'nombre_requisito' => $nombresRequisitos[$codigoRequisito] ?? 'Documento Adjunto',
                            'archivo_path'     => $path,
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
     * Sirve archivos privados del expediente validando la propiedad de la empresa.
     */
    public function verDocumento(DocumentoExpediente $documento)
    {
        $user = auth()->user();
        $rolesAutorizados = ['admin', 'director', 'fiscal', 'analista'];

        if ($documento->empresa->user_id !== $user->id && !$user->hasAnyRole($rolesAutorizados)) {
            abort(403, 'No tienes permisos para visualizar este documento.');
        }

        if (!Storage::disk('private')->exists($documento->archivo_path)) {
            abort(404, 'El archivo solicitado no existe en el servidor.');
        }

        return Storage::disk('private')->response($documento->archivo_path);
    }
}