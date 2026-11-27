<?php

namespace App\Http\Controllers\Director\Residuos;

use App\Http\Controllers\Controller;
use App\Models\BitacoraEvento;
use App\Models\RequisitoEmpresaCatalogo;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CatalogoRequisitoController extends Controller
{
    public function index()
    {
        $requisitos = RequisitoEmpresaCatalogo::orderBy('orden')
            ->orderBy('codigo')
            ->orderBy('aplica_a_rol')
            ->get();

        // Agrupar por código para mostrar variantes juntas (ej: DOC_POLIZA para MANEJADOR y GENERADOR)
        $agrupados = $requisitos->groupBy('codigo');

        return view('director.residuos.catalogo', compact('requisitos', 'agrupados'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'codigo'                => 'required|string|max:100',
            'nombre'                => 'required|string|max:255',
            'descripcion'           => 'nullable|string|max:1000',
            'aplica_a_rol'          => 'required|in:TODOS,GENERADOR,MANEJADOR,NO_APLICA,GENERADOR,MANEJADOR',
            'tipo_requisito'        => 'required|in:OBLIGATORIO,CONDICIONAL,OPCIONAL',
            'condicion_flag'        => 'nullable|in:genera_peligrosos,genera_efluentes,genera_emisiones,genera_riesgo_ambiental,usa_pozo_agua,modalidad_externa,es_persona_juridica',
            'es_vitalicio_default'  => 'nullable|boolean',
            'orden'                 => 'nullable|integer|min:0',
            'activo'                => 'nullable|boolean',
        ]);

        // Verificar unicidad compuesta (codigo + aplica_a_rol)
        $existe = RequisitoEmpresaCatalogo::where('codigo', strtoupper(trim($data['codigo'])))
            ->where('aplica_a_rol', $data['aplica_a_rol'])
            ->exists();

        if ($existe) {
            return back()
                ->withErrors(['codigo' => 'Ya existe un requisito con este código para el rol seleccionado.'])
                ->withInput();
        }

        $requisito = RequisitoEmpresaCatalogo::create([
            'codigo'                => strtoupper(trim($data['codigo'])),
            'nombre'                => trim($data['nombre']),
            'descripcion'           => $data['descripcion'] ?? null,
            'aplica_a_rol'          => $data['aplica_a_rol'],
            'tipo_requisito'        => $data['tipo_requisito'],
            'condicion_flag'        => $data['condicion_flag'] ?? null,
            'es_vitalicio_default'  => $request->boolean('es_vitalicio_default'),
            'orden'                 => $data['orden'] ?? 0,
            'activo'                => $request->boolean('activo', true),
        ]);

        BitacoraEvento::registrar([
            'modulo'      => 'residuos',
            'tipo_evento' => 'CREACION_REQUISITO_EMPRESA',
            'nivel'       => 'INFO',
            'descripcion' => "Se creó el requisito: {$requisito->nombre} ({$requisito->codigo} - {$requisito->aplica_a_rol})",
        ]);

        return back()->with('success', 'Requisito creado correctamente.');
    }

    public function update(Request $request, RequisitoEmpresaCatalogo $requisito)
    {
        $data = $request->validate([
            'nombre'                => 'required|string|max:255',
            'descripcion'           => 'nullable|string|max:1000',
            'tipo_requisito'        => 'required|in:OBLIGATORIO,CONDICIONAL,OPCIONAL',
            'condicion_flag'        => 'nullable|in:genera_peligrosos,genera_efluentes,genera_emisiones,genera_riesgo_ambiental,usa_pozo_agua,modalidad_externa,es_persona_juridica',
            'es_vitalicio_default'  => 'nullable|boolean',
            'orden'                 => 'nullable|integer|min:0',
            'activo'                => 'nullable|boolean',
        ]);

        $requisito->update([
            'nombre'                => trim($data['nombre']),
            'descripcion'           => $data['descripcion'] ?? null,
            'tipo_requisito'        => $data['tipo_requisito'],
            'condicion_flag'        => $data['condicion_flag'] ?? null,
            'es_vitalicio_default'  => $request->boolean('es_vitalicio_default'),
            'orden'                 => $data['orden'] ?? $requisito->orden,
            'activo'                => $request->boolean('activo'),
        ]);

        BitacoraEvento::registrar([
            'modulo'      => 'residuos',
            'tipo_evento' => 'ACTUALIZACION_REQUISITO_EMPRESA',
            'nivel'       => 'INFO',
            'descripcion' => "Se actualizó el requisito: {$requisito->nombre} ({$requisito->codigo})",
        ]);

        return back()->with('success', 'Requisito actualizado.');
    }

    public function destroy(RequisitoEmpresaCatalogo $requisito)
    {
        // 🛡️ Verificar que no tenga documentos de empresas asociados
        if ($requisito->documentosExpediente()->exists()) {
            return back()->withErrors([
                'error' => 'No se puede eliminar este requisito porque ya tiene documentos de empresas asociados. Considera desactivarlo en lugar de eliminarlo.'
            ]);
        }

        $nombre = $requisito->nombre;
        $codigo = $requisito->codigo;
        $rol = $requisito->aplica_a_rol;

        $requisito->delete();

        BitacoraEvento::registrar([
            'modulo'      => 'residuos',
            'tipo_evento' => 'ELIMINACION_REQUISITO_EMPRESA',
            'nivel'       => 'WARNING',
            'descripcion' => "Se eliminó el requisito: {$nombre} ({$codigo} - {$rol})",
        ]);

        return back()->with('success', 'Requisito eliminado correctamente.');
    }

    public function toggleActivo(RequisitoEmpresaCatalogo $requisito)
    {
        $requisito->update(['activo' => !$requisito->activo]);

        BitacoraEvento::registrar([
            'modulo'      => 'residuos',
            'tipo_evento' => 'CAMBIO_ESTATUS_REQUISITO',
            'nivel'       => 'INFO',
            'descripcion' => "Se " . ($requisito->activo ? 'activó' : 'desactivó') . " el requisito: {$requisito->nombre} ({$requisito->codigo})",
        ]);

        return back()->with(
            'success',
            "Requisito " . ($requisito->activo ? 'activado' : 'desactivado') . " correctamente."
        );
    }
}