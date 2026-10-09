<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Sancion extends Model
{
    use HasFactory, HasUuids, Auditable;

    protected string $moduloBitacora = 'sanciones';
    protected $table = 'sanciones';

    protected $fillable = [
        'codigo_expediente',
        'empresa_id',
        'director_id',
        'inspeccion_id',
        'tipo_infraccion',
        'gravedad',
        'motivo_descripcion',
        'base_legal',
        'fecha_incidencia',
        'fecha_citacion',
        'fecha_limite_subsanacion',
        'evidencias_fiscalia',
        'evidencias_subsanacion',
        'observaciones_resolucion',
        'estatus',
        'fecha_resolucion',
    ];

    protected $casts = [
        'fecha_incidencia' => 'date',
        'fecha_citacion' => 'date',
        'fecha_limite_subsanacion' => 'date',
        'fecha_resolucion' => 'datetime',
        'evidencias_fiscalia' => 'array',
        'evidencias_subsanacion' => 'array',
    ];

    // Relaciones
    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }

    public function director()
    {
        return $this->belongsTo(User::class, 'director_id');
    }

    public function inspeccion()
    {
        return $this->belongsTo(Inspeccion::class, 'inspeccion_id');
    }
}