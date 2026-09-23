<?php

namespace App\Observers;

use App\Models\Denuncia;

class DenunciaObserver
{
    /**
     * Se ejecuta justo antes de persistir los cambios de un registro en la BD.
     */
    public function updating(Denuncia $denuncia): void
    {
        if ($denuncia->isDirty('estatus')) {
            $nuevoEstatus = $denuncia->estatus;

            // Transición hacia 'en_inspeccion'
            if ($nuevoEstatus === 'en_inspeccion' && is_null($denuncia->fecha_inicio_inspeccion)) {
                $denuncia->fecha_inicio_inspeccion = now();
            }

            // Transición hacia estados finales
            if (in_array($nuevoEstatus, ['atendido', 'desestimado'])) {
                $denuncia->fecha_cierre = now();
            }
        }
    }
}