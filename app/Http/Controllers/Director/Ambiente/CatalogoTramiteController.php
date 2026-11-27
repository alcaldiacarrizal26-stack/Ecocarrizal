<?php

namespace App\Http\Controllers\Director\Ambiente;

use App\Http\Controllers\Controller;
use App\Models\BitacoraEvento;
use App\Models\RecaudoCatalogo;
use App\Models\TipoTramite;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CatalogoTramiteController extends Controller
{
    public function index()
    {
        $tramites = TipoTramite::with('recaudos')
            ->orderBy('id')
            ->get();

        return view('director.ambiente.catalogo', compact('tramites'));
    }

    public function storeTramite(Request $request)
    {
        $data = $request->validate([
            'nombre'      => 'required|string|max:255',
            'slug'        => 'nullable|string|max:120', // <-- Removido 'unique' para permitir auto-generación
            'descripcion' => 'required|string|max:1000',
            'plazo_dias'  => 'required|integer|min:0|max:365',
            'activo'      => 'nullable|boolean',
        ]);

        $slug = $data['slug']
            ? Str::slug($data['slug'], '_')
            : Str::slug($data['nombre'], '_');

        // Si el slug ya existe (porque el usuario lo escribió igual a uno existente), le agregamos un sufijo
        if (TipoTramite::where('slug', $slug)->exists()) {
            $slug .= '_' . rand(10, 99);
        }

        $tramite = TipoTramite::create([
            'slug'        => $slug,
            'nombre'      => $data['nombre'],
            'descripcion' => $data['descripcion'],
            'plazo_dias'  => $data['plazo_dias'],
            'activo'      => $request->boolean('activo', true),
        ]);

        BitacoraEvento::registrar([
            'modulo'      => 'ambiente',
            'tipo_evento' => 'CREACION_TIPO_TRAMITE',
            'nivel'       => 'INFO',
            'descripcion' => "Se creó el trámite: {$tramite->nombre} ({$tramite->slug})",
        ]);

        return back()->with('success', 'Tipo de trámite creado correctamente.');
    }

    public function updateTramite(Request $request, TipoTramite $tramite)
    {
        $data = $request->validate([
            'nombre'      => 'required|string|max:255',
            'descripcion' => 'required|string|max:1000',
            'plazo_dias'  => 'required|integer|min:0|max:365',
            'activo'      => 'nullable|boolean',
        ]);

        // Nota: El slug no se actualiza porque es el identificador y la vista lo bloquea.
        $tramite->update([
            'nombre'      => $data['nombre'],
            'descripcion' => $data['descripcion'],
            'plazo_dias'  => $data['plazo_dias'],
            'activo'      => $request->boolean('activo'),
        ]);

        BitacoraEvento::registrar([
            'modulo'      => 'ambiente',
            'tipo_evento' => 'ACTUALIZACION_TIPO_TRAMITE',
            'nivel'       => 'INFO',
            'descripcion' => "Se actualizó el trámite: {$tramite->nombre}",
        ]);

        return back()->with('success', 'Trámite actualizado.');
    }

    public function destroyTramite(TipoTramite $tramite)
    {
        $nombre = $tramite->nombre;
        $slug = $tramite->slug;
        $tramite->delete();

        BitacoraEvento::registrar([
            'modulo'      => 'ambiente',
            'tipo_evento' => 'ELIMINACION_TIPO_TRAMITE',
            'nivel'       => 'WARNING',
            'descripcion' => "Se eliminó el trámite: {$nombre} ({$slug})",
        ]);

        return back()->with('success', 'Trámite eliminado.');
    }

    public function storeRecaudo(Request $request, TipoTramite $tramite)
    {
        $data = $request->validate([
            'key'            => 'nullable|string|max:100', // <-- Removido Rule::unique
            'label'          => 'required|string|max:255',
            'tipo_requisito' => 'required|in:OBLIGATORIO,CONDICIONAL,DIFERIDO',
            'tipo_documento' => 'required|in:FORMAL,FOTO',
            'hint'           => 'nullable|string|max:500',
        ]);

        $key = $data['key']
            ? Str::slug($data['key'], '_')
            : Str::slug($data['label'], '_');

        // Si la llave ya existe en ESTE trámite, le añadimos un sufijo aleatorio
        if ($tramite->recaudos()->where('key', $key)->exists()) {
            $key .= '_' . rand(10, 99);
        }

        $recaudo = $tramite->recaudos()->create([
            'key'            => $key,
            'label'          => $data['label'],
            'tipo_requisito' => $data['tipo_requisito'],
            'tipo_documento' => $data['tipo_documento'],
            'hint'           => $data['hint'] ?? null,
        ]);

        BitacoraEvento::registrar([
            'modulo'      => 'ambiente',
            'tipo_evento' => 'CREACION_RECAUDO',
            'nivel'       => 'INFO',
            'descripcion' => "Se añadió el recaudo '{$recaudo->label}' al trámite: {$tramite->nombre}",
        ]);

        return back()->with('success', 'Requisito añadido al trámite.');
    }

    public function updateRecaudo(Request $request, RecaudoCatalogo $recaudo)
    {
        $data = $request->validate([
            'label'          => 'required|string|max:255',
            'tipo_requisito' => 'required|in:OBLIGATORIO,CONDICIONAL,DIFERIDO',
            'tipo_documento' => 'required|in:FORMAL,FOTO',
            'hint'           => 'nullable|string|max:500',
        ]);

        $recaudo->update([
            'label'          => $data['label'],
            'tipo_requisito' => $data['tipo_requisito'],
            'tipo_documento' => $data['tipo_documento'],
            'hint'           => $data['hint'] ?? null,
        ]);

        BitacoraEvento::registrar([
            'modulo'      => 'ambiente',
            'tipo_evento' => 'ACTUALIZACION_RECAUDO',
            'nivel'       => 'INFO',
            'descripcion' => "Se actualizó el recaudo '{$recaudo->label}' del trámite: {$recaudo->tipoTramite->nombre}",
        ]);

        return back()->with('success', 'Requisito actualizado.');
    }

    public function destroyRecaudo(RecaudoCatalogo $recaudo)
    {
        $label = $recaudo->label;
        $tramiteNombre = $recaudo->tipoTramite->nombre ?? 'Desconocido';
        
        $recaudo->delete();

        BitacoraEvento::registrar([
            'modulo'      => 'ambiente',
            'tipo_evento' => 'ELIMINACION_RECAUDO',
            'nivel'       => 'WARNING',
            'descripcion' => "Se eliminó el recaudo '{$label}' del trámite: {$tramiteNombre}",
        ]);

        return back()->with('success', 'Requisito eliminado.');
    }
}