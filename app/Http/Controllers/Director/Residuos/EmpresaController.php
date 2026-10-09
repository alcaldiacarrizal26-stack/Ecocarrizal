<?php

namespace App\Http\Controllers\Director\Residuos;

use App\Http\Controllers\Controller;
use App\Models\Empresa;
use Illuminate\Http\Request;

class EmpresaController extends Controller
{
    /**
     * Muestra el listado general de empresas registradas para la Dirección de Residuos.
     */
    public function index(Request $request)
    {
        $query = Empresa::with(['user', 'perfilAmbiental']);

        // Filtro opcional por búsqueda (RIF o Razón Social)
        if ($request->filled('buscar')) {
            $buscar = $request->input('buscar');
            $query->where(function ($q) use ($buscar) {
                $q->where('rif', 'like', "%{$buscar}%")
                  ->orWhere('razon_social', 'like', "%{$buscar}%");
            });
        }

        // Filtro opcional por estatus de la empresa
        if ($request->filled('estatus')) {
            $query->where('estatus', $request->input('estatus'));
        }

        $empresas = $query->latest()->paginate(15)->appends($request->all());

        return view('director.residuos.empresas.index', compact('empresas'));
    }

    /**
     * Muestra la vista detallada del expediente para revisión del Director.
     */
    public function show($id)
    {
        // Cargamos las relaciones exactamente como están definidas en Empresa.php
        $empresa = Empresa::with(['perfilAmbiental', 'documentosExpediente'])->findOrFail($id);

        return view('director.residuos.empresas.show', compact('empresa'));
    }

    public function evaluar(Request $request, $id)
    {
        $empresa = Empresa::findOrFail($id);

        // Prevenir modificación si ya tiene veredicto final
        if (in_array(strtoupper($empresa->estatus), ['APROBADO', 'RECHAZADO'])) {
            return redirect()
                ->back()
                ->withErrors(['error' => 'No es posible modificar el veredicto de una empresa cuyo expediente ya fue APROBADO o RECHAZADO.']);
        }

        $validated = $request->validate([
            'estatus' => 'required|in:APROBADO,RECHAZADO,OBSERVADO',
            'observaciones_fiscalia' => 'required_if:estatus,RECHAZADO,OBSERVADO|nullable|string|max:1000',
        ], [
            'observaciones_fiscalia.required_if' => 'Es obligatorio indicar el motivo u observación cuando el veredicto es Rechazado u Observado.',
        ]);

        $empresa->update([
            'estatus' => $validated['estatus'],
            'observaciones_fiscalia' => $validated['observaciones_fiscalia'] ?? $empresa->observaciones_fiscalia,
        ]);

        return redirect()
            ->route('director.residuos.empresas.show', $empresa->id)
            ->with('success', 'El veredicto para la empresa ' . $empresa->razon_social . ' ha sido registrado exitosamente.');
    }

    public function sanciones(Empresa $empresa)
    {
        $empresa->load(['perfilAmbiental']);
        $sanciones = Sancion::where('empresa_id', $empresa->id)
            ->with(['director', 'inspeccion'])
            ->latest()
            ->get();

        return view('director.residuos.empresas.sanciones', compact('empresa', 'sanciones'));
    }

    /**
     * Registra una nueva sanción / citación administrativa.
     */
    public function asignarSancion(Request $request, Empresa $empresa)
    {
        $validated = $request->validate([
            'tipo_infraccion'          => 'required|string|max:255',
            'gravedad'                  => 'required|in:LEVE,GRAVE,MUY_GRAVE',
            'motivo_descripcion'        => 'required|string',
            'base_legal'                => 'nullable|string',
            'fecha_incidencia'          => 'required|date',
            'fecha_citacion'            => 'nullable|date',
            'fecha_limite_subsanacion'  => 'nullable|date|after_or_equal:fecha_incidencia',
            'evidencia'                 => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $rutasEvidencia = [];
        if ($request->hasFile('evidencia')) {
            $path = $request->file('evidencia')->store('sanciones/fiscalia', 'public');
            $rutasEvidencia[] = $path;
        }

        $codigo = 'SAN-RES-' . date('Y') . '-' . strtoupper(Str::random(5));

        Sancion::create([
            'codigo_expediente'        => $codigo,
            'empresa_id'               => $empresa->id,
            'director_id'              => auth()->id(),
            'tipo_infraccion'          => $validated['tipo_infraccion'],
            'gravedad'                 => $validated['gravedad'],
            'motivo_descripcion'       => $validated['motivo_descripcion'],
            'base_legal'               => $validated['base_legal'] ?? 'Ordenanza Municipal de Manejo de Residuos y Desechos Sólidos',
            'fecha_incidencia'         => $validated['fecha_incidencia'],
            'fecha_citacion'           => $validated['fecha_citacion'] ?? null,
            'fecha_limite_subsanacion' => $validated['fecha_limite_subsanacion'] ?? null,
            'evidencias_fiscalia'      => $rutasEvidencia,
            'estatus'                  => 'NOTIFICADA',
        ]);

        return redirect()->back()->with('success', "Procedimiento sancionatorio {$codigo} aperturado correctamente.");
    }

    /**
     * Resuelve/Retira una sanción tras evaluar la subsanación.
     */
    public function resolverSancion(Request $request, $sancionId)
    {
        $sancion = Sancion::findOrFail($sancionId);

        $validated = $request->validate([
            'estatus'                  => 'required|in:SUBSANADA,DESESTIMADA',
            'observaciones_resolucion' => 'required|string',
            'evidencia_resolucion'     => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $evidencias = $sancion->evidencias_subsanacion ?? [];
        if ($request->hasFile('evidencia_resolucion')) {
            $path = $request->file('evidencia_resolucion')->store('sanciones/resoluciones', 'public');
            $evidencias[] = $path;
        }

        $sancion->update([
            'estatus'                  => $validated['estatus'],
            'observaciones_resolucion' => $validated['observaciones_resolucion'],
            'evidencias_subsanacion'   => $evidencias,
            'fecha_resolucion'         => now(),
        ]);

        $mensaje = $validated['estatus'] === 'SUBSANADA' 
            ? 'La sanción ha sido marcada como SUBSANADA y retirada con éxito.' 
            : 'La sanción ha sido DESESTIMADA.';

        return redirect()->back()->with('success', $mensaje);
    }
}