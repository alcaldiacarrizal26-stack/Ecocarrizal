<?php

namespace App\Http\Controllers\Director\Ambiente;

use App\Http\Controllers\Controller;
use App\Models\Compensacion;
use App\Models\EntregaCompensacion;
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

        DB::transaction(function () use ($request, $entregaId) {
            $entrega = EntregaCompensacion::where('id', $entregaId)
                ->where('estatus', 'PENDIENTE')
                ->firstOrFail();

            $compensacion = Compensacion::findOrFail($entrega->compensacion_id);

            $entrega->estatus = $request->estatus;
            $entrega->observacion_director = $request->observacion_director;
            $entrega->fecha_revision = now();
            $entrega->save();

            // Si se aprueba, se suma la cantidad entregada al expediente acumulado
            if ($request->estatus === 'APROBADO') {
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

        return redirect()->back()->with('success', 'Evaluación de entrega procesada con éxito.');
    }
}