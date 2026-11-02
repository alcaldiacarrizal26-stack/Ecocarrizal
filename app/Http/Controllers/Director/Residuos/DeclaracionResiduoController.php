<?php

namespace App\Http\Controllers\Director\Residuos;

use App\Http\Controllers\Controller;
use App\Models\DeclaracionResiduo;
use App\Models\Notificacion;
use Illuminate\Http\Request;

class DeclaracionResiduoController extends Controller
{
    /**
     * Muestra el listado global de todas las declaraciones ambientales.
     */
    public function index(Request $request)
    {
        $query = DeclaracionResiduo::with(['empresa', 'detalles'])->latest();

        if ($request->filled('rol')) {
            $query->where('rol_declarante', $request->rol);
        }

        if ($request->filled('estatus')) {
            $query->where('estatus', $request->estatus);
        }

        if ($request->filled('periodo')) {
            $query->where('periodo', 'like', '%' . $request->periodo . '%');
        }

        $declaraciones = $query->paginate(15);

        // Apunta a la nueva carpeta declaracion: director.residuos.declaraciones.index
        return view('director.residuos.declaraciones.index', compact('declaraciones'));
    }

    /**
     * Muestra el detalle particular de una declaración.
     */
    public function show(DeclaracionResiduo $declaracion)
    {
        $declaracion->load(['empresa', 'detalles', 'user']);

        return view('director.residuos.declaraciones.show', compact('declaracion'));
    }

    /**
     * Evalúa o actualiza el estatus de una declaración (Aprobado / Rechazado).
     */
    public function updateEstatus(Request $request, DeclaracionResiduo $declaracion)
    {
        $validated = $request->validate([
            'estatus'          => 'required|in:APROBADA,RECHAZADA',
            'motivo_rechazo'   => 'required_if:estatus,RECHAZADA|nullable|string|max:500',
        ], [
            'estatus.required' => 'Debe seleccionar un veredicto.',
            'motivo_rechazo.required_if' => 'El motivo del rechazo es obligatorio para rechazar la declaración.',
        ]);

        $data = [
            'estatus'        => $validated['estatus'],
            'observaciones'  => $validated['estatus'] === 'RECHAZADA' ? $validated['motivo_rechazo'] : null,
        ];

        $declaracion->update($data);

        // Notificar al usuario propietario de la declaración sobre el cambio de estatus
        if ($declaracion->user_id) {
            $esAprobado = $validated['estatus'] === 'APROBADA';
            $mensajeNotif = $esAprobado 
                ? "Tu declaración para el período {$declaracion->periodo} ha sido APROBADA exitosamente." 
                : "Tu declaración para el período {$declaracion->periodo} ha sido RECHAZADA. Motivo: " . $validated['motivo_rechazo'];

            Notificacion::create([
                'user_id'  => $declaracion->user_id,
                'titulo'   => 'Evaluación de Declaración de Residuos',
                'mensaje'  => $mensajeNotif,
                'tipo'     => $esAprobado ? 'success' : 'danger',
                'link_url' => '#',
            ]);
        }

        return back()->with('success', 'El veredicto de la declaración ha sido registrado correctamente.');
    }
}