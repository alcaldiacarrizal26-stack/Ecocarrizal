<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class AlmacenamientoController extends Controller
{
    /**
     * Muestra las estadísticas de uso de almacenamiento del sistema.
     */
    public function index()
    {
        $disks = ['public', 'private'];
        $stats = [];

        // Calcular estadísticas globales por disco
        foreach ($disks as $diskName) {
            try {
                $disk = Storage::disk($diskName);
                $allFiles = $disk->allFiles();
                
                $totalSize = 0;
                foreach ($allFiles as $file) {
                    $totalSize += $disk->size($file);
                }

                $stats[$diskName] = [
                    'total_files'    => count($allFiles),
                    'total_size_mb'  => round($totalSize / 1024 / 1024, 2),
                ];
            } catch (\Throwable $e) {
                $stats[$diskName] = [
                    'total_files'    => 0,
                    'total_size_mb'  => 0,
                ];
            }
        }

        // Desglose detallado por directorios clave de la Alcaldía
        $directoriosObjetivo = [
            'Expedientes de Empresas'       => ['disk' => 'public', 'path' => 'expedientes'],
            'Declaraciones de Residuos'     => ['disk' => 'private', 'path' => 'declaraciones'],
            'Evidencias de Soporte Técnico' => ['disk' => 'public', 'path' => 'soporte/evidencias'],
            'Evidencias de Inspección'      => ['disk' => 'public', 'path' => 'inspecciones/evidencias'],
            'Compensaciones Ambientales'    => ['disk' => 'public', 'path' => 'compensaciones'],
        ];

        $folderStats = [];
        foreach ($directoriosObjetivo as $label => $info) {
            try {
                $disk = Storage::disk($info['disk']);
                $files = $disk->exists($info['path']) ? $disk->allFiles($info['path']) : [];
                
                $size = 0;
                foreach ($files as $file) {
                    $size += $disk->size($file);
                }

                $folderStats[] = [
                    'label'   => $label,
                    'disk'    => strtoupper($info['disk']),
                    'path'    => $info['path'],
                    'count'   => count($files),
                    'size_mb' => round($size / 1024 / 1024, 2),
                ];
            } catch (\Throwable $e) {
                $folderStats[] = [
                    'label'   => $label,
                    'disk'    => strtoupper($info['disk']),
                    'path'    => $info['path'],
                    'count'   => 0,
                    'size_mb' => 0,
                ];
            }
        }

        return view('admin.almacenamiento', compact('stats', 'folderStats'));
    }
}