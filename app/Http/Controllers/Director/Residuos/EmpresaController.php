<?php

namespace App\Http\Controllers\Director\Residuos;

use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use App\Models\Empresa;
use App\Models\Sancion;
use App\Models\Inspeccion;
use App\Models\OrdenInspeccion;
use App\Models\Inspector;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class EmpresaController extends Controller
{
    public function index(Request $request)
    {
        $query = Empresa::with(['user', 'perfilAmbiental', 'ordenesInspeccion', 'sanciones']);

        if ($request->get('vista') === 'sanciones') {
            $query->whereHas('sanciones', function ($q) {
                $q->whereIn('estatus', ['NOTIFICADA', 'EN_REVISION', 'PENDIENTE_NOTIFICACION']);
            });
        }

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

    public function show($id)
    {
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

        // Doble verificación atómica por si ocurrieron peticiones simultáneas concurrentes
        $empresa->refresh();
        if (in_array(strtoupper($empresa->estatus), ['APROBADO', 'RECHAZADO'])) {
            return redirect()
                ->back()
                ->withErrors(['error' => 'El expediente ya ha sido procesado previamente por otro medio.']);
        }

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

        $inspeccionSugerida = null;
        if ($request->has('inspeccion_id')) {
            $inspeccionSugerida = Inspeccion::find($request->inspeccion_id);
        }

        return view('director.residuos.empresas.sanciones', compact('empresa', 'sanciones', 'inspeccionSugerida'));
    }

    public function asignarSancion(Request $request, Empresa $empresa)
    {
        $validated = $request->validate([
            'tipo_infraccion'          => 'required|string|max:255',
            'gravedad'                  => 'required|in:LEVE,GRAVE,MUY_GRAVE',
            'unidades_tributarias'      => 'required|numeric|min:0', // <--- 1. Añadir validación del monto en U.T.
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
            'unidades_tributarias'     => $validated['unidades_tributarias'], // <--- 2. Guardar el valor recolectado del input
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
     * Resuelve/Retira una sanción tras la evaluación presencial del director.
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
            // Opcional si deseas almacenar la ruta de resolución en bitácora o campo adicional
            $request->file('evidencia_resolucion')->store("sanciones/resoluciones/{$sancion->id}", 'public');
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

    public function inspecciones($empresaId)
    {
        $empresa = Empresa::with(['ordenesInspeccion.inspector', 'ordenesInspeccion.inspeccion'])->findOrFail($empresaId);
        
        $inspectoresDisponibles = Inspector::where('disponible', true)->with('user')->get()->map(function($insp) {
            return (object)[
                'id' => $insp->id,
                'nombre' => $insp->user->nombre . ' ' . $insp->user->apellido,
                'credencial' => $insp->codigo_credencial
            ];
        });

        return view('director.residuos.empresas.inspecciones', compact('empresa', 'inspectoresDisponibles'));
    }

    public function asignarInspeccion(Request $request, $empresaId)
    {
        $request->validate([
            'tipo_inspeccion'       => 'required|in:ORDINARIA,DE_OFICIO',
            'base_legal_inspeccion' => 'required|string',
            'fecha_visita'          => 'required|date',
            'inspector_id'          => [
                'required', 
                'exists:users,id',
                function ($attribute, $value, $fail) {
                    $valido = User::where('id', $value)
                        ->where('is_active', true)
                        ->whereHas('role', function ($q) {
                            $q->whereIn('nombre', ['director', 'inspector']);
                        })->exists();

                    if (!$valido) {
                        $fail('El funcionario seleccionado debe ser obligatoriamente un Director o un Inspector activo.');
                    }
                },
            ],
            'observaciones_campo'   => 'required|string',
            'orden_adjunta'         => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $path = null;
        if ($request->hasFile('orden_adjunta')) {
            $path = $request->file('orden_adjunta')->store("inspecciones/ordenes/{$empresaId}", 'public');
        }

        OrdenInspeccion::create([
            'id'                        => Str::uuid(),
            'codigo_orden'              => 'ORD-RES-' . date('Y') . '-' . strtoupper(Str::random(5)),
            'empresa_id'                => $empresaId,
            'inspector_id'              => $request->inspector_id, // Guarda directamente el ID del usuario
            'director_id'               => auth()->id(),
            'tipo_inspeccion'           => $request->tipo_inspeccion,
            'base_legal_inspeccion'     => $request->base_legal_inspeccion,
            'observaciones_campo'       => $request->observaciones_campo,
            'orden_adjunta_path'        => $path,
            'estatus'                   => 'inspector_asignado',
            'fecha_visita_programada'   => $request->fecha_visita,
            'fecha_asignacion_inspector'=> now(),
        ]);

        return redirect()->back()->with('success', 'Orden de inspección emitida y asignada exitosamente al funcionario.');
    }

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