<?php

namespace App\Http\Controllers\Ciudadano;

use App\Http\Controllers\Controller;
use App\Models\Empresa;
use App\Models\PerfilAmbiental;
use App\Models\DocumentoEmpresa; // Ajusta según tu modelo
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class EmpresaController extends Controller
{
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
                    'numero_racda'     => $perfilData['numero_racda'] ?? '',
                    'genera_efluentes' => $perfilData['genera_efluentes'] ?? false,
                    'genera_emisiones' => $perfilData['genera_emisiones'] ?? false,
                ]);
            }

            // 3. Subir y Registrar Documentos Sensibles
            if ($request->hasFile('documentos')) {
                foreach ($request->file('documentos') as $tipoDocumento => $file) {
                    if ($file->isValid()) {
                        // Guarda de forma segura en storage/app/private/expedientes/{empresa_id}
                        $path = $file->store("expedientes/{$empresa->id}", 'private');

                        // Si tienes relación o tabla de documentos
                        $empresa->documentos()->create([
                            'tipo'            => $tipoDocumento,
                            'ruta_archivo'    => $path,
                            'nombre_original' => $file->getClientOriginalName(),
                            'mime_type'       => $file->getClientMimeType(),
                            'peso_bytes'      => $file->getSize(),
                        ]);
                    }
                }
            }

            return response()->json(['success' => true, 'message' => 'Empresa registrada correctamente.']);
        });
    }
}