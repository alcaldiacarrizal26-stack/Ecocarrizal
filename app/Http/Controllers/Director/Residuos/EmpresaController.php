<?php

namespace App\Http\Controllers\Director\Residuos;

use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use App\Models\Empresa;
use App\Models\Sancion;
use App\Models\Inspeccion;
use App\Models\OrdenInspeccion;
use App\Models\Inspector;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class EmpresaController extends Controller
{
    /**
     * Muestra el listado general de empresas registradas para la Dirección de Residuos.
     */
    public function index(Request $request)
    {
        $query = Empresa::with(['user', 'perfilAmbiental', 'ordenesInspeccion', 'sanciones']);

        // Si viene desde la opción "Sanciones y Citaciones" del sidebar
        if ($request->get('vista') === 'sanciones') {
            $query->whereHas('sanciones', function ($q) {
                $q->whereIn('estatus', ['NOTIFICADA', 'EN_REVISION', 'PENDIENTE_NOTIFICACION']);
            });
        }

        // Filtros adicionales si los usas
        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->where(function ($q) use ($buscar) {
                $q->where('razon_social', 'like', "%{$buscar}%")
                ->orWhere('rif', 'like', "%{$buscar}%");
            });
        }

        $empresas = $query->latest()->paginate(10);

        return view('director.residuos.empresas.index', compact('empresas'));
    }

    /**
     * Muestra la vista detallada del expediente para revisión del Director.
     */
    public function show($id)
    {
        // Cargamos todas las relaciones necesarias para que la vista tenga la información completa
        $empresa = Empresa::with([
            'perfilAmbiental', 
            'documentosExpediente', 
            'sanciones', 
            'declaracionesResiduos',
            'user'
        ])->findOrFail($id);

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

    public function sanciones(Request $request, Empresa $empresa)
    {
        $empresa->load(['perfilAmbiental']);
        $sanciones = Sancion::where('empresa_id', $empresa->id)
            ->with(['director', 'inspeccion'])
            ->latest()
            ->get();

        // Si viene desde una inspección específica para sancionar directo
        $inspeccionSugerida = null;
        if ($request->has('inspeccion_id')) {
            $inspeccionSugerida = Inspeccion::find($request->inspeccion_id);
        }

        return view('director.residuos.empresas.sanciones', compact('empresa', 'sanciones', 'inspeccionSugerida'));
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
            'fecha_limite_subsanacion'  => 'nullable|date',
            'inspeccion_id'             => 'nullable|uuid|exists:inspecciones,id',
            'evidencia'                 => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $rutasEvidencia = [];
        if ($request->hasFile('evidencia')) {
            $path = $request->file('evidencia')->store('sanciones/fiscalia', 'public');
            $rutasEvidencia[] = $path;
        } elseif ($request->filled('inspeccion_id')) {
            // Si proviene de un acta de inspección, heredamos automáticamente las fotos de evidencia del acta
            $inspeccionAsociada = Inspeccion::find($request->inspeccion_id);
            if ($inspeccionAsociada && !empty($inspeccionAsociada->fotos_evidencia)) {
                $rutasEvidencia = $inspeccionAsociada->fotos_evidencia;
            }
        }

        $codigo = 'SAN-RES-' . date('Y') . '-' . strtoupper(Str::random(5));

        Sancion::create([
            'codigo_expediente'        => $codigo,
            'empresa_id'               => $empresa->id,
            'director_id'              => auth()->id(),
            'inspeccion_id'            => $validated['inspeccion_id'] ?? null,
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

        return redirect()->route('director.residuos.empresas.sanciones.index', $empresa->id)
            ->with('success', "Procedimiento sancionatorio {$codigo} aperturado y notificado correctamente.");
    }

    /**
     * Resuelve/Retira una sanción tras evaluar la subsanación.
     */
    public function resolverSancion(Request $request, Sancion $sancion)
    {
        $request->validate([
            'estatus'                   => 'required|in:SUBSANADA,DESESTIMADA',
            'observaciones_resolucion'  => 'required|string',
            'evidencia_resolucion'      => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $data = [
            'estatus'                  => $request->estatus,
            'observaciones_resolucion' => $request->observaciones_resolucion,
            'fecha_resolucion'         => now(),
        ];

        if ($request->hasFile('evidencia_resolucion')) {
            $path = $request->file('evidencia_resolucion')->store("sanciones/resoluciones/{$sancion->id}", 'public');
        }

        $sancion->update($data);

        return redirect()->back()->with('success', 'El procedimiento sancionatorio ha sido resuelto y actualizado exitosamente.');
    }

    public function verEvidencia($path)
    {
        $path = str_replace(['..', '//'], '', $path);

        if (!Storage::disk('public')->exists($path)) {
            abort(404, 'El archivo o acta solicitada no existe.');
        }

        return response()->file(storage_path('app/public/' . $path));
    }

    /**
     * Muestra el listado de órdenes de inspección de la empresa y los inspectores disponibles.
     */
    public function inspecciones($empresaId)
    {
        $empresa = Empresa::with(['ordenesInspeccion.inspector.user', 'ordenesInspeccion.inspeccion'])->findOrFail($empresaId);
        
        $inspectoresDisponibles = Inspector::where('disponible', true)->with('user')->get()->map(function($insp) {
            return (object)[
                'id' => $insp->id,
                'nombre' => $insp->user->nombre . ' ' . $insp->user->apellido,
                'credencial' => $insp->codigo_credencial
            ];
        });

        return view('director.residuos.empresas.inspecciones', compact('empresa', 'inspectoresDisponibles'));
    }

    /**
     * Almacena una nueva orden de inspección (Ordinaria o De Oficio/Sorpresa).
     */
    public function asignarInspeccion(Request $request, $empresaId)
    {
        $request->validate([
            'tipo_inspeccion'       => 'required|in:ORDINARIA,DE_OFICIO',
            'base_legal_inspeccion' => 'required|string',
            'fecha_visita'          => 'required|date',
            'inspector_id'          => 'required', 
            'observaciones_campo'   => 'required|string',
            'orden_adjunta'         => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $inspector = Inspector::where('id', $request->inspector_id)
            ->orWhere('user_id', $request->inspector_id)
            ->first();

        if (!$inspector) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['inspector_id' => 'El funcionario seleccionado no es un inspector registrado válido.']);
        }

        $path = null;
        if ($request->hasFile('orden_adjunta')) {
            $path = $request->file('orden_adjunta')->store("inspecciones/ordenes/{$empresaId}", 'public');
        }

        OrdenInspeccion::create([
            'id'                        => Str::uuid(),
            'codigo_orden'              => 'ORD-RES-' . date('Y') . '-' . strtoupper(Str::random(5)),
            'empresa_id'                => $empresaId,
            'inspector_id'              => $inspector->id,
            'director_id'               => auth()->id(),
            'tipo_inspeccion'           => $request->tipo_inspeccion,
            'base_legal_inspeccion'     => $request->base_legal_inspeccion,
            'observaciones_campo'       => $request->observaciones_campo,
            'orden_adjunta_path'        => $path,
            'estatus'                   => 'inspector_asignado',
            'fecha_visita_programada'   => $request->fecha_visita,
            'fecha_asignacion_inspector'=> now(),
        ]);

        return redirect()->back()->with('success', 'Orden de inspección ambiental emitida y asignada exitosamente.');
    }

    /**
     * Elimina una orden de inspección si aún no ha sido iniciada/atendida.
     */
    public function eliminarInspeccion($id)
    {
        $orden = OrdenInspeccion::findOrFail($id);

        if (!in_array($orden->estatus, ['pendiente', 'inspector_asignado'])) {
            return redirect()->back()->withErrors(['error' => 'No se puede eliminar una orden de inspección que ya se encuentra en curso o atendida.']);
        }

        if ($orden->orden_adjunta_path && Storage::disk('public')->exists($orden->orden_adjunta_path)) {
            Storage::disk('public')->delete($orden->orden_adjunta_path);
        }

        $orden->delete();

        return redirect()->back()->with('success', 'La orden de inspección ha sido eliminada exitosamente.');
    }
}