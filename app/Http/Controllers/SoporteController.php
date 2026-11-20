<?php

namespace App\Http\Controllers;

use App\Models\TicketSoporte;
use App\Models\TicketMensaje;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Storage;

class SoporteController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $rol = strtolower($user->role->nombre ?? '');

        if (in_array($rol, ['admin', 'administrador'])) {
            $tickets = TicketSoporte::with(['user.role', 'mensajes.user'])->latest()->paginate(10);
            return view('admin.soporte', compact('tickets'));
        }

        $tickets = TicketSoporte::where('user_id', $user->id)->with('mensajes')->latest()->get();
        return view('soporte', compact('tickets'));
    }

    public function store(Request $request)
    {
        $userId = auth()->id();

        $rateLimitKey = 'soporte-submission:' . $userId;
        if (RateLimiter::tooManyAttempts($rateLimitKey, 3)) {
            $seconds = RateLimiter::availableIn($rateLimitKey);
            return redirect()->back()->with('error', "Límite alcanzado. Espera " . ceil($seconds / 60) . " min.");
        }

        $validated = $request->validate([
            'asunto'             => 'required|string|max:150',
            'mensaje'            => 'required|string|min:10|max:1000', // El primer mensaje del chat
            'prioridad'          => 'required|in:BAJA,MEDIA,ALTA',
            'justificacion_alta' => 'required_if:prioridad,ALTA|nullable|string|min:10|max:500',
            'evidencia'          => 'nullable|file|mimes:jpg,jpeg,png,webp,pdf|max:5120',
        ]);

        $lockKey = 'soporte_store_lock_' . $userId;

        return Cache::lock($lockKey, 5)->get(function () use ($request, $validated, $userId, $rateLimitKey) {
            $evidenciaPath = null;
            if ($request->hasFile('evidencia')) {
                $evidenciaPath = $request->file('evidencia')->store('soporte/evidencias', 'public');
            }

            // 1. Crear el ticket (cabecera)
            $ticket = TicketSoporte::create([
                'user_id'            => $userId,
                'asunto'             => $validated['asunto'],
                'prioridad'          => $validated['prioridad'],
                'justificacion_alta' => $validated['justificacion_alta'] ?? null,
                'estatus'            => 'ABIERTO',
            ]);

            // 2. Guardar el primer mensaje en el chat
            TicketMensaje::create([
                'ticket_id'      => $ticket->id,
                'user_id'        => $userId,
                'es_admin'       => false,
                'mensaje'        => $validated['mensaje'],
                'evidencia_path' => $evidenciaPath,
            ]);

            RateLimiter::hit($rateLimitKey, 300);

            return redirect()->back()->with('success', 'Tu reporte de soporte ha sido enviado exitosamente.');
        });
    }

        public function responder(Request $request, $id)
    {
        $userId = auth()->id();
        
        // Buscar el ticket asegurando que pertenezca al usuario (evita que alguien responda tickets ajenos)
        $ticket = TicketSoporte::where('user_id', $userId)->findOrFail($id);

        // Si el ticket ya está resuelto, no permitir más mensajes
        if ($ticket->estatus === 'RESUELTO') {
            return redirect()->back()->with('error', 'Este ticket ha sido cerrado y no admite más respuestas.');
        }

        $request->validate([
            'mensaje' => 'required|string|min:5|max:1000',
        ]);

        // Registrar el nuevo mensaje del ciudadano
        TicketMensaje::create([
            'ticket_id' => $ticket->id,
            'user_id'   => $userId,
            'es_admin'  => false,
            'mensaje'   => $request->mensaje,
        ]);

        // Si el ticket estaba ABIERTO, al responder pasa a EN_PROCESO
        if ($ticket->estatus === 'ABIERTO') {
            $ticket->update(['estatus' => 'EN_PROCESO']);
        }

        return redirect()->back()->with('success', 'Tu mensaje ha sido enviado al administrador.');
    }

    public function actualizarRespuesta(Request $request, $id)
    {
        $ticket = TicketSoporte::findOrFail($id);

        $validated = $request->validate([
            'mensaje' => 'required|string|min:5', // El admin ahora responde con un mensaje
            'estatus' => 'required|in:ABIERTO,EN_PROCESO,RESUELTO',
            'evidencia' => 'nullable|file|mimes:jpg,jpeg,png,webp,pdf|max:5120',
        ]);

        $evidenciaPath = null;
        if ($request->hasFile('evidencia')) {
            $evidenciaPath = $request->file('evidencia')->store('soporte/evidencias', 'public');
        }

        // 1. Insertar la respuesta del admin en el chat
        TicketMensaje::create([
            'ticket_id'      => $ticket->id,
            'user_id'        => auth()->id(),
            'es_admin'       => true,
            'mensaje'        => $validated['mensaje'],
            'evidencia_path' => $evidenciaPath,
        ]);

        // 2. Actualizar estatus del ticket
        $ticket->update([
            'estatus'          => $validated['estatus'],
            'fecha_resolucion' => $validated['estatus'] === 'RESUELTO' ? now() : null,
        ]);

        return redirect()->back()->with('success', 'Respuesta enviada y estatus actualizado.');
    }
}