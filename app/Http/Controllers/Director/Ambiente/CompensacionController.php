<?php

namespace App\Http\Controllers\Director\Ambiente;

use App\Http\Controllers\Controller;
use App\Models\Compensacion;
use App\Models\EntregaCompensacion;
use App\Models\Notificacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CompensacionController extends Controller
{
    /**
     * Muestra el panel de compensación ambiental para el Director de Ambiente.
     */
    public function index()
    {
        $compensaciones = Compensacion::with(['user', 'solicitud', 'entregas.user'])
            ->latest()
            ->get();

        $entregasPendientes = EntregaCompensacion::with(['compensacion.user', 'user'])
            ->where('estatus', 'PENDIENTE')
            ->latest()
            ->get();

        return view('director.ambiente.compensacion', compact('compensaciones', 'entregasPendientes'));
    }

    /**
     * Revisa y procesa una entrega/abono de ejemplares entregado por el ciudadano.
     */
    public function evaluarEntrega(Request $request, $entregaId)
    {
        $request->validate([
            'estatus'              => 'required|in:APROBADO,RECHAZADO',
            'observacion_director' => 'nullable|string|max:500',
        ]);

        $ciudadanoId = null;
        $estatusEvaluacion = $request->estatus;

        DB::transaction(function () use ($request, $entregaId, &$ciudadanoId, $estatusEvaluacion) {
            $entrega = EntregaCompensacion::where('id', $entregaId)
                ->where('estatus', 'PENDIENTE')
                ->firstOrFail();

            $compensacion = Compensacion::findOrFail($entrega->compensacion_id);
            $ciudadanoId = $compensacion->user_id;

            $entrega->estatus = $estatusEvaluacion;
            $entrega->observacion_director = $request->observacion_director;
            $entrega->fecha_revision = now();
            $entrega->save();

            // Si se aprueba, se suma la cantidad entregada al expediente acumulado
            if ($estatusEvaluacion === 'APROBADO') {
                $nuevaCantidadEntregada = $compensacion->cantidad_entregada + $entrega->cantidad_entregada;
                $compensacion->cantidad_entregada = $nuevaCantidadEntregada;

                if ($nuevaCantidadEntregada >= $compensacion->cantidad_asignada) {
                    $compensacion->estatus = 'COMPLETADO';
                } else {
                    $compensacion->estatus = 'PARCIAL';
                }

                $compensacion->save();
            }
        });

        // Enviar notificación al ciudadano según el resultado de la evaluación
        if ($ciudadanoId) {
            $mensajeNotif = $estatusEvaluacion === 'APROBADO' 
                ? 'Tu entrega de compensación ambiental ha sido verificada y APROBADA exitosamente.' 
                : 'Tu entrega de compensación ambiental ha sido REVISADA y presenta observaciones o rechazo.';

            Notificacion::create([
                'user_id'  => $ciudadanoId,
                'titulo'   => 'Actualización de Compensación Ambiental',
                'mensaje'  => $mensajeNotif,
                'tipo'     => $estatusEvaluacion === 'APROBADO' ? 'success' : 'warning',
                'link_url' => '#',
            ]);
        }

        return redirect()->back()->with('success', 'Evaluación de entrega procesada con éxito.');
    }
}