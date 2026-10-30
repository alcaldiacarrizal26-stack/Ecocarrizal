<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrdenInspeccion extends Model
{
    use HasFactory, HasUuids, Auditable;

        protected string $moduloBitacora = 'ordenes_inspeccion';
    protected $table = 'ordenes_inspeccion';

    protected $fillable = [
        'codigo_orden',
        'empresa_id',
        'inspector_id',
        'director_id',
        'tipo_inspeccion',
        'base_legal_inspeccion',
        'observaciones_campo',
        'orden_adjunta_path',
        'estatus',
        'fecha_visita_programada',
        'fecha_asignacion_inspector',
        'fecha_inicio_inspeccion',
        'fecha_cierre',
    ];

    protected $casts = [
        'fecha_visita_programada' => 'datetime',
        'fecha_asignacion_inspector' => 'datetime',
        'fecha_inicio_inspeccion' => 'datetime',
        'fecha_cierre' => 'datetime',
    ];

    // Relación con Empresa
    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }

    // Relación con Inspector
    public function inspector()
    {
        return $this->belongsTo(User::class, 'inspector_id');
    }

    // Relación con Director (Usuario)
    public function director()
    {
        return $this->belongsTo(User::class, 'director_id');
    }

    // Relación inversa con el Acta de Inspección resultante
    public function inspeccion()
    {
        return $this->hasOne(Inspeccion::class, 'orden_inspeccion_id');
    }
}