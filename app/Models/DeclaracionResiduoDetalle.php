<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeclaracionResiduoDetalle extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'declaracion_residuo_detalles';

    protected $fillable = [
        'declaracion_id',
        'categoria',
        'codigo_residuo',
        'nombre_residuo',
        'cantidad',
        'unidad_medida',
        'cantidad_toneladas',
        'manifiesto_path',
    ];

    protected $casts = [
        'cantidad'           => 'decimal:3',
        'cantidad_toneladas' => 'decimal:3',
    ];

    public function declaracion(): BelongsTo
    {
        return $this->belongsTo(DeclaracionResiduo::class, 'declaracion_id');
    }
}