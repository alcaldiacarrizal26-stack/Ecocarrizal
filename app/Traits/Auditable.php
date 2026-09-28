<?php

namespace App\Traits;

use App\Models\BitacoraEvento;

trait Auditable
{
    public static function bootAuditable()
    {
        static::created(function ($model) {
            BitacoraEvento::registrar([
                'modulo'      => $model->getModuloBitacora(),
                'tipo_evento' => 'crear',
                'nivel'       => 'info',
                'descripcion' => "Registro creado en {$model->getTable()}",
                'model'       => $model,
                'nuevos'      => $model->getAttributes(),
            ]);
        });

        static::updated(function ($model) {
            BitacoraEvento::registrar([
                'modulo'      => $model->getModuloBitacora(),
                'tipo_evento' => 'editar',
                'nivel'       => 'info',
                'descripcion' => "Registro actualizado en {$model->getTable()}",
                'model'       => $model,
                'anteriores'  => $model->getOriginal(),
                'nuevos'      => $model->getChanges(),
            ]);
        });

        static::deleted(function ($model) {
            BitacoraEvento::registrar([
                'modulo'      => $model->getModuloBitacora(),
                'tipo_evento' => 'eliminar',
                'nivel'       => 'danger',
                'descripcion' => "Registro eliminado en {$model->getTable()}",
                'model'       => $model,
                'anteriores'  => $model->getAttributes(),
            ]);
        });
    }

    public function getModuloBitacora(): string
    {
        return property_exists($this, 'moduloBitacora') ? $this->moduloBitacora : $this->getTable();
    }
}