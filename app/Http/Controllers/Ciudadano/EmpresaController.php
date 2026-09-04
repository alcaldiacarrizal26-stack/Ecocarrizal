<?php

namespace App\Http\Controllers\Ciudadano;

use App\Http\Controllers\Controller;
use App\Models\Empresa;
use App\Models\PerfilAmbiental;
use App\Models\DocumentoExpediente; // Ajusta según tu modelo
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class EmpresaController extends Controller
{

    public function index()
    {
        $empresas = Empresa::with([
            'perfilAmbiental',
            'documentosExpediente'
        ])
        ->where('user_id', auth()->id())
        ->latest()
        ->get();

        return view('ciudadano.empresa', compact('empresas'));
    }

    public function show(Empresa $empresa)
    {
        $user = auth()->user();
        $rolesAutorizados = ['admin', 'director', 'fiscal', 'analista'];

        // Validar si es el propietario de la empresa o si tiene un rol con permisos
        if ($empresa->user_id !== $user->id && !$user->hasAnyRole($rolesAutorizados)) {
            abort(403, 'No tienes autorización para consultar este expediente.');
        }

        // Cargar las relaciones necesarias para desplegar la información completa
        $empresa->load(['perfilAmbiental', 'documentosExpediente']);

        // Se apunta a 'ciudadano.empresa' según tu estructura
        return view('ciudadano.empresa', compact('empresa'));
    }

    /**
     * Servir el archivo privado del expediente.
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

    public function store(Request $request)
    {
        // Validación de datos
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
            'documentos.*'          => 'file|mimes:pdf,jpg,jpeg,png|max:5120', // Máx 5MB por archivo
        ]);

        return DB::transaction(function () use ($request) {
            // 1. Crear Empresa
            $empresa = Empresa::create([
                'user_id'              => auth()->id(),
                'rif'                  => $request->rif,
                'razon_social'         => $request->razon_social,
                'nombre_comercial'     => $request->nombre_comercial,
                'numero_cuenta_patente'=> $request->numero_cuenta_patente,
                'correo_comercial'     => $request->correo_comercial,
                'telefono_comercial'   => $request->telefono_comercial,
                'comuna'               => $request->comuna,
                'sector'               => $request->sector,
                'calle_avenida'        => $request->calle_avenida,
                'edificio_galpon_local'=> $request->edificio_galpon_local,
                'estatus'              => 'PENDIENTE_APROBACION',
            ]);

            // 2. Crear Perfil Ambiental
            if ($request->has('perfil')) {
                $perfilData = is_string($request->perfil) ? json_decode($request->perfil, true) : $request->perfil;

                $empresa->perfilAmbiental()->create([
                    'rol_ambiental'        => $perfilData['rol_ambiental'] ?? 'GENERADOR',
                    'subtipo_generador'    => $perfilData['subtipo_generador'] ?? 'PEQUENO',
                    'numero_racda'         => $perfilData['numero_racda'] ?? 'RACDA No aplica',
                    'genera_efluentes'     => filter_var($perfilData['genera_efluentes'] ?? false, FILTER_VALIDATE_BOOLEAN),
                    'genera_emisiones'     => filter_var($perfilData['genera_emisiones'] ?? false, FILTER_VALIDATE_BOOLEAN),
                    'genera_peligrosos'    => filter_var($perfilData['genera_peligrosos'] ?? false, FILTER_VALIDATE_BOOLEAN),
                    'genera_ruidos'        => filter_var($perfilData['genera_ruidos'] ?? false, FILTER_VALIDATE_BOOLEAN),
                    'usa_pozo_agua'        => filter_var($perfilData['usa_pozo_agua'] ?? false, FILTER_VALIDATE_BOOLEAN),
                    'manejadora_contratada' => $perfilData['manejadora_contratada'] ?? null,
                    'actividades_manejador' => isset($perfilData['actividades_manejador']) ? json_encode($perfilData['actividades_manejador']) : null,
                    'sustancias_manejadas'  => $perfilData['sustancias_manejadas'] ?? null,
                    'residuos_tipos'       => isset($perfilData['residuos_tipos']) && !empty($perfilData['residuos_tipos']) 
                                                ? json_encode($perfilData['residuos_tipos']) 
                                                : null,
                ]);
            }

            // 3. Subir y Registrar Documentos Sensibles en la tabla documentos_expediente
            if ($request->hasFile('documentos')) {
                $nombresRequisitos = [
                    'doc_renma'          => 'Registro RENMA',
                    'doc_no_objecion'    => 'Constancia de No Objeción Ambiental',
                    'doc_racda'          => 'Registro RACDA',
                    'doc_poliza'          => 'Póliza de Seguro de Responsabilidad Ambiental',
                    'doc_efluentes'      => 'Caracterización de Efluentes Líquidos',
                    'doc_emisiones'      => 'Caracterización de Emisiones Atmosféricas',
                    'doc_plan_desechos'  => 'Plan de Manejo de Desechos y Residuos Sólidos',
                    'doc_plan_emergencia'=> 'Plan de Emergencia y Contingencia Ambiental',
                ];

                foreach ($request->file('documentos') as $codigoRequisito => $file) {
                    if ($file && $file->isValid()) {
                        // Guarda en storage/app/private/expedientes/{empresa_id}
                        $path = $file->store("expedientes/{$empresa->id}", 'private');

                        $empresa->documentosExpediente()->create([
                            'codigo_requisito' => strtoupper($codigoRequisito),
                            'nombre_requisito' => $nombresRequisitos[$codigoRequisito] ?? 'Documento Adjunto',
                            'archivo_path'     => $path,
                        ]);
                    }
                }
            }

            return response()->json(['success' => true, 'message' => 'Empresa registrada correctamente.']);
        });
    }
}