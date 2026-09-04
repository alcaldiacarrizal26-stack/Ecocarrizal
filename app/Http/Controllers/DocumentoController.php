<?php

namespace App\Http\Controllers;

use App\Models\DocumentoEmpresa;
use Illuminate\Support\Facades\Storage;

class DocumentoController extends Controller
{
    public function show(DocumentoEmpresa $documento)
    {
        // 1. Verificar Autorización (El usuario debe ser el dueño o un fiscal/admin)
        $user = auth()->user();
        if ($user->id !== $documento->empresa->user_id && !$user->hasRole(['admin', 'fiscal'])) {
            abort(403, 'Acceso no autorizado a este documento.');
        }

        // 2. Verificar existencia en el disco privado
        if (!Storage::disk('private')->exists($documento->ruta_archivo)) {
            abort(404, 'El archivo no existe.');
        }

        // 3. Retornar el archivo directamente al navegador
        return Storage::disk('private')->response(
            $documento->ruta_archivo,
            $documento->nombre_original
        );
    }
}