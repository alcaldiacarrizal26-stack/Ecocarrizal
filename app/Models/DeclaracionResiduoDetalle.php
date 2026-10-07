<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class DeclaracionResiduoDetalle extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'declaracion_residuo_detalles';

    protected $fillable = [
        'declaracion_id',
        'codigo_residuo',
        'nombre_residuo',
        'categoria',
        'tipo_operacion',
        'cantidad',
        'unidad_medida',
        'cantidad_toneladas',
        'manifiesto_path',
    ];

    protected $casts = [
        'cantidad' => 'decimal:3',
        'cantidad_toneladas' => 'decimal:3',
    ];

    public function declaracion()
    {
        return $this->belongsTo(DeclaracionResiduo::class, 'declaracion_id');
    }
}