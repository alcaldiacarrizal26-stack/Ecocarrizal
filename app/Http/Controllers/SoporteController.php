<?php

namespace App\Http\Controllers;

use App\Models\TicketSoporte;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Storage;

class SoporteController extends Controller
{
    /**
     * Muestra la vista de soporte para cualquier usuario y sus tickets enviados.
     */
    public function index()
    {
        $user = auth()->user();
        $rol = strtolower($user->role->nombre ?? '');

        if (in_array($rol, ['admin', 'administrador'])) {
            // El admin ve todos los tickets del sistema
            $tickets = TicketSoporte::with('user.role')->latest()->paginate(10);
            return view('admin.soporte', compact('tickets'));
        }

        // Los demás usuarios solo ven sus propios tickets
        $tickets = TicketSoporte::where('user_id', $user->id)->latest()->get();
        return view('soporte', compact('tickets'));
    }

    /**
     * Almacena un nuevo ticket de soporte con protección Anti-Spam y Anti-Concurrency.
     */
    public function store(Request $request)
    {
        $userId = auth()->id();

        // 1. CAPA ANTI-SPAM: Throttle Rate Limiting (Máx. 3 tickets cada 5 minutos)
        $rateLimitKey = 'soporte-submission:' . $userId;
        if (RateLimiter::tooManyAttempts($rateLimitKey, 3)) {
            $seconds = RateLimiter::availableIn($rateLimitKey);
            $minutes = ceil($seconds / 60);

            return redirect()->back()
                ->withInput()
                ->with('error', "Has alcanzado el límite de reportes permitidos. Por favor espera {$minutes} minuto(s) antes de enviar otro reporte.");
        }

        // 2. CAPA ANTI-DUPLICADOS: Prevenir solicitudes idénticas en un margen de 60 segundos
        $duplicateExists = TicketSoporte::where('user_id', $userId)
            ->where('asunto', $request->input('asunto'))
            ->where('created_at', '>=', now()->subSeconds(60))
            ->exists();

        if ($duplicateExists) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Ya has enviado un ticket con este asunto recientemente. Por favor revisa el estatus de tus solicitudes.');
        }

        // 3. VALIDACIÓN DE ENTRADA
        $validated = $request->validate([
            'asunto'             => 'required|string|max:150',
            'mensaje'            => 'required|string|min:10|max:1000',
            'prioridad'          => 'required|in:BAJA,MEDIA,ALTA',
            'justificacion_alta' => 'required_if:prioridad,ALTA|nullable|string|min:10|max:500',
            'evidencia'          => 'nullable|file|mimes:jpg,jpeg,png,webp,pdf|max:5120',
        ], [
            'justificacion_alta.required_if' => 'Es obligatorio justificar el motivo de la urgencia alta para bloquear o notificar al administrador de inmediato.',
        ]);

        // 4. BLINDAJE CONTRA RACE CONDITION: Candado atómico por 5 segundos
        $lockKey = 'soporte_store_lock_' . $userId;

        return Cache::lock($lockKey, 5)->get(function () use ($request, $validated, $userId, $rateLimitKey) {
            $evidenciaPath = null;
            if ($request->hasFile('evidencia')) {
                $evidenciaPath = $request->file('evidencia')->store('soporte/evidencias', 'public');
            }

            TicketSoporte::create([
                'user_id'            => $userId,
                'asunto'             => $validated['asunto'],
                'mensaje'            => $validated['mensaje'],
                'prioridad'          => $validated['prioridad'],
                'justificacion_alta' => $validated['justificacion_alta'] ?? null,
                'evidencia_path'     => $evidenciaPath,
                'estatus'            => 'ABIERTO',
            ]);

            // Registrar intento exitoso en RateLimiter
            RateLimiter::hit($rateLimitKey, 300);

            return redirect()->back()->with('success', 'Tu reporte de soporte técnico ha sido enviado exitosamente.');
        }) ?: back()->withErrors(['asunto' => 'Se está procesando una solicitud de soporte idéntica. Por favor espera un momento.'])->withInput();
    }

    /**
     * Método exclusivo del Admin para responder/resolver un ticket.
     */
    public function actualizarRespuesta(Request $request, $id)
    {
        $ticket = TicketSoporte::findOrFail($id);

        $validated = $request->validate([
            'respuesta_admin' => 'required|string|min:5',
            'estatus'         => 'required|in:EN_PROCESO,RESUELTO',
        ]);

        $ticket->update([
            'respuesta_admin'  => $validated['respuesta_admin'],
            'estatus'          => $validated['estatus'],
            'fecha_resolucion' => $validated['estatus'] === 'RESUELTO' ? now() : null,
        ]);

        return redirect()->back()->with('success', 'Respuesta de soporte guardada y notificada correctamente.');
    }
}