<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeclaracionResiduo extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'declaraciones_residuos';

    /**
     * Los atributos asignables en masa.
     */
    protected $fillable = [
        'empresa_id',
        'user_id',
        'periodo',
        'fecha_inicio_periodo',
        'fecha_fin_periodo',
        'toneladas_comunes',
        'toneladas_reciclables',
        'toneladas_peligrosas',
        'manifiestos_archivos',
        'manejadora_rif',
        'estatus',
        'observaciones_fiscalia',
    ];

    /**
     * Casts para conversión automática de datos.
     */
    protected $casts = [
        'fecha_inicio_periodo' => 'date',
        'fecha_fin_periodo'    => 'date',
        'toneladas_comunes'    => 'decimal:2',
        'toneladas_reciclables' => 'decimal:2',
        'toneladas_peligrosas' => 'decimal:2',
        'manifiestos_archivos' => 'array',
    ];

    /**
     * Relación con la Empresa declarante.
     */
    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }

    /**
     * Relación con el Usuario que realiza la declaración.
     */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Atributo dinámico para calcular el tonelaje total declarado.
     */
    public function getTotalToneladasAttribute(): float
    {
        return (float) ($this->toneladas_comunes + $this->toneladas_reciclables + $this->toneladas_peligrosas);
    }
}