<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class HomeController extends Controller
{
    /**
     * Muestra la vista de bienvenida institucional (Welcome).
     */
    public function index()
    {
        return view('welcome');
    }

    /**
     * Maneja la descarga individual de los documentos del marco legal en PDF.
     */
    public function descargarMarcoLegalPdf($documento)
    {
        $paths = [
            'constitucion' => 'pdf/marco_legal/constitucion_rbv.pdf',
            'ley_ambiente' => 'pdf/marco_legal/ley_organica_ambiente.pdf',
            'ley_penal_ambiente' => 'pdf/marco_legal/ley_penal_ambiente.pdf',
            'normativa_racda' => 'pdf/marco_legal/normativa_racda.pdf',
            'ley_gestion_basura' => 'pdf/marco_legal/ley_gestion_basura.pdf',
            'ordenanza_aseo' => 'pdf/marco_legal/ordenanza_aseo_carrizal.pdf',
            'ordenanza_convivencia' => 'pdf/marco_legal/ordenanza_convivencia.pdf',
            'decretos_fiscalizacion' => 'pdf/marco_legal/decretos_fiscalizacion.pdf',
        ];

        // Validar si el documento existe en el disco público
        if (array_key_exists($documento, $paths) && Storage::disk('public')->exists($paths[$documento])) {
            return Storage::disk('public')->download($paths[$documento]);
        }

        // Respuesta limpia si el archivo físico aún no ha sido cargado al servidor
        return back()->with('error', 'El documento PDF solicitado se encuentra en proceso de digitalización institucional.');
    }
}