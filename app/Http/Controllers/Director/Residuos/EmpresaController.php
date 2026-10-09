<?php

namespace App\Http\Controllers\Director\Residuos;

use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use App\Models\Empresa;
use App\Models\Sancion;
use App\Models\Inspeccion;
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
        $query = Empresa::with(['user', 'perfilAmbiental']);

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
            // Opcional: puedes guardar actas de cierre en un campo json o texto si lo prefieres
            $path = $request->file('evidencia_resolucion')->store("sanciones/resoluciones/{$sancion->id}", 'public');
            // Si tienes campo para resolución, puedes agregarlo aquí
        }

        $sancion->update($data);

        return redirect()->back()->with('success', 'El procedimiento sancionatorio ha sido resuelto y actualizado exitosamente.');
    }

    public function verEvidencia($path)
    {
        // Limpiamos el path recibido para evitar desbordamiento de directorios
        $path = str_replace(['..', '//'], '', $path);

        if (!Storage::disk('public')->exists($path)) {
            abort(404, 'El archivo o acta solicitada no existe.');
        }

        return response()->file(storage_path('app/public/' . $path));
    }

    /**
     * Muestra el listado de inspecciones de la empresa y los inspectores disponibles.
     */
    public function inspecciones($empresaId)
    {
        $empresa = Empresa::with(['inspecciones.inspector.user'])->findOrFail($empresaId);
        
        // Obtenemos los inspectores disponibles para el formulario
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
            'inspector_id'          => 'required', // Recibe el valor enviado por el select (que corresponde al usuario o inspector)
            'observaciones_campo'   => 'required|string',
            'orden_adjunta'         => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        // Buscamos si el ID enviado corresponde a la tabla 'inspectores' o a la tabla 'users'
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

        Inspeccion::create([
            'id'                    => Str::uuid(),
            'codigo_acta'           => 'INS-RES-' . date('Y') . '-' . strtoupper(Str::random(5)),
            'empresa_id'            => $empresaId,
            'inspector_id'          => $inspector->id,
            'tipo_inspeccion'       => $request->tipo_inspeccion,
            'base_legal_inspeccion' => $request->base_legal_inspeccion,
            'fecha_visita'          => $request->fecha_visita,
            'observaciones_campo'   => $request->observaciones_campo,
            'orden_adjunta_path'    => $path,
            'dictamen'              => null, // <-- Inicialmente sin dictamen para que aparezca como pendiente
        ]);

        return redirect()->back()->with('success', 'Orden de inspección ambiental emitida y asignada exitosamente.');
    }
}