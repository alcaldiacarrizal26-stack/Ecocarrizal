<?php

namespace App\Http\Controllers;

use App\Models\TicketSoporte;
use Illuminate\Http\Request;

class SoporteController extends Controller
{
    // Muestra la vista de soporte para cualquier usuario y sus tickets enviados
    public function index()
    {
        $user = auth()->user();
        $rol = strtolower($user->role->nombre ?? '');

        if (in_array($rol, ['admin', 'administrador'])) {
            // El admin ve todos los tickets del sistema
            $tickets = TicketSoporte::with('user.role')->latest()->paginate(10);
            return view('admin.soporte.index', compact('tickets'));
        }

        // Los demás usuarios solo ven sus propios tickets
        $tickets = TicketSoporte::where('user_id', $user->id)->latest()->get();
        return view('soporte', compact('tickets'));
    }

    // Almacena un nuevo ticket de soporte
    public function store(Request $request)
    {
        $validated = $request->validate([
            'asunto'             => 'required|string|max:150',
            'mensaje'            => 'required|string|min:10|max:1000',
            'prioridad'          => 'required|in:BAJA,MEDIA,ALTA',
            'justificacion_alta' => 'required_if:prioridad,ALTA|nullable|string|min:10|max:500',
            'evidencia'          => 'nullable|file|mimes:jpg,jpeg,png,webp,pdf|max:5120',
        ], [
            'justificacion_alta.required_if' => 'Es obligatorio justificar el motivo de la urgencia alta para bloquear o notificar al administrador de inmediato.',
        ]);

        $evidenciaPath = null;
        if ($request->hasFile('evidencia')) {
            $evidenciaPath = $request->file('evidencia')->store('soporte/evidencias', 'public');
        }

        TicketSoporte::create([
            'user_id'            => auth()->id(),
            'asunto'             => $validated['asunto'],
            'mensaje'            => $validated['mensaje'],
            'prioridad'          => $validated['prioridad'],
            'justificacion_alta' => $validated['justificacion_alta'] ?? null,
            'evidencia_path'     => $evidenciaPath,
            'estatus'            => 'ABIERTO',
        ]);

        return redirect()->back()->with('success', 'Tu reporte de soporte técnico con evidencia ha sido enviado exitosamente.');
    }

    // Método exclusivo del Admin para responder/resolver un ticket
    public function actualizarRespuesta(Request $request, $id)
    {
        $ticket = TicketSoporte::findOrFail($id);

        $validated = $request->validate([
            'respuesta_admin' => 'required|string|min:5',
            'estatus' => 'required|in:EN_PROCESO,RESUELTO',
        ]);

        $ticket->update([
            'respuesta_admin' => $validated['respuesta_admin'],
            'estatus' => $validated['estatus'],
            'fecha_resolucion' => $validated['estatus'] === 'RESUELTO' ? now() : null,
        ]);

        return redirect()->back()->with('success', 'Respuesta de soporte guardada y notificada correctamente.');
    }
}