<?php

namespace App\Observers;

use App\Models\Solicitud;

class SolicitudObserver
{
    /**
     * Se ejecuta justo antes de persistir los cambios de un registro en la BD.
     */
    public function updating(Solicitud $solicitud): void
    {
        // 1. Usar 'estado' en lugar de 'estatus'
        if ($solicitud->isDirty('estado')) {
            $nuevoEstado = $solicitud->estado;

            // Transición hacia 'en_inspeccion'
            if ($nuevoEstado === 'en_inspeccion' && is_null($solicitud->fecha_inicio_inspeccion)) {
                $solicitud->fecha_inicio_inspeccion = now();
            }

            // Transición hacia estados finales
            // Ajusta los valores de este array a los estados finales reales que retorna tu DataProvider en el test
            if (in_array($nuevoEstado, ['aprobada', 'rechazada', 'atendido', 'desestimado'])) {
                $solicitud->fecha_cierre = now();
            }
        }
    }
}