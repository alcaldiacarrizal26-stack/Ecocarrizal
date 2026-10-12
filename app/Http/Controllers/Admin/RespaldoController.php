<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BitacoraEvento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;
use ZipArchive;

class RespaldoController extends Controller
{
    public function index()
    {
        // Listar respaldos almacenados en el storage (carpeta backups)
        $archivos = collect(Storage::files('backups'))
            ->filter(fn($file) => str_ends_with($file, '.sql') || str_ends_with($file, '.zip'))
            ->map(function ($file) {
                return [
                    'nombre' => basename($file),
                    'path' => $file,
                    'tamano' => round(Storage::size($file) / 1024 / 1024, 2) . ' MB',
                    'fecha' => Carbon::createFromTimestamp(Storage::lastModified($file))->format('d/m/Y H:i:s'),
                ];
            })
            ->sortByDesc('fecha')
            ->values();

        return view('admin.respaldos.index', compact('archivos'));
    }

    public function store(Request $request)
    {
        try {
            $timestamp = date('Y-m-d_H-i-s');

            if (!file_exists(storage_path('app/private/backups'))) {
                mkdir(storage_path('app/private/backups'), 0755, true);
            }

            // 1. Respaldo de Base de Datos PostgreSQL (.sql)
            $database = config('database.connections.pgsql.database');
            $username = config('database.connections.pgsql.username');
            $password = config('database.connections.pgsql.password');
            $host = config('database.connections.pgsql.host');
            $port = config('database.connections.pgsql.port');

            $sqlFileName = 'backup_db_' . $timestamp . '.sql';
            $sqlFilePath = storage_path('app/private/backups/' . $sqlFileName);

            $command = sprintf(
                'PGPASSWORD=%s pg_dump -h %s -p %s -U %s %s > %s',
                escapeshellarg($password),
                escapeshellarg($host),
                escapeshellarg($port),
                escapeshellarg($username),
                escapeshellarg($database),
                escapeshellarg($sqlFilePath)
            );

            exec($command, $output, $returnVar);

            // 2. Respaldo de Archivos Físicos y Documentos Adjuntos (.zip)
            $zipFileName = 'backup_archivos_' . $timestamp . '.zip';
            $zipFilePath = storage_path('app/private/backups/' . $zipFileName);

            $zip = new ZipArchive;
            if ($zip->open($zipFilePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
                $publicPath = storage_path('app/public');
                if (File::exists($publicPath)) {
                    $files = File::allFiles($publicPath);
                    foreach ($files as $file) {
                        $filePathReal = $file->getRealPath();
                        $relativePath = 'public/' . $file->getRelativePathname();
                        $zip->addFile($filePathReal, $relativePath);
                    }
                }
                $zip->close();
            }

            // Registrar en Bitácora
            BitacoraEvento::registrar([
                'modulo' => 'respaldos',
                'tipo_evento' => 'GENERAR_BACKUP',
                'nivel' => 'info',
                'descripcion' => "Se generó el paquete completo de respaldo (Base de datos y archivos adjuntos) con fecha {$timestamp}.",
            ]);

            return back()->with('success', '¡Copia de seguridad de base de datos y documentos generada con éxito!');

        } catch (\Throwable $th) {
            return back()->withErrors(['error' => 'Error al generar el respaldo: ' . $th->getMessage()]);
        }
    }

    public function download(Request $request)
    {
        $file = $request->input('file');
        $path = 'backups/' . basename($file);

        if (Storage::exists($path)) {
            BitacoraEvento::registrar([
                'modulo' => 'respaldos',
                'tipo_evento' => 'DESCARGAR_BACKUP',
                'nivel' => 'warning',
                'descripcion' => "Se descargó el archivo de respaldo: " . basename($file),
            ]);

            return Storage::download($path);
        }

        return back()->withErrors(['error' => 'El archivo de respaldo solicitado no existe.']);
    }

    public function destroy(Request $request)
    {
        $file = $request->input('file');
        $path = 'backups/' . basename($file);

        if (Storage::exists($path)) {
            Storage::delete($path);

            BitacoraEvento::registrar([
                'modulo' => 'respaldos',
                'tipo_evento' => 'ELIMINAR_BACKUP',
                'nivel' => 'danger',
                'descripcion' => "Se eliminó el archivo de respaldo: " . basename($file),
            ]);

            return back()->with('success', 'Respaldo eliminado correctamente.');
        }

        return back()->withErrors(['error' => 'No se pudo encontrar el archivo para eliminar.']);
    }
}