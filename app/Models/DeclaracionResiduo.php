<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DeclaracionResiduo extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'declaraciones_residuos';

    protected $fillable = [
        'empresa_id',
        'user_id',
        'periodo',
        'frecuencia',
        'fecha_inicio_periodo',
        'fecha_fin_periodo',
        'toneladas_comunes',
        'toneladas_reciclables',
        'toneladas_peligrosas',
        'manifiestos_archivos',
        'manejadora_rif',
        'manejadora_nombre',
        'estatus',
        'observaciones_fiscalia',
    ];

    protected $casts = [
        'fecha_inicio_periodo' => 'date',
        'fecha_fin_periodo'    => 'date',
        'toneladas_comunes'    => 'decimal:3',
        'toneladas_reciclables' => 'decimal:3',
        'toneladas_peligrosas' => 'decimal:3',
        'manifiestos_archivos' => 'array',
    ];

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function detalles(): HasMany
    {
        return $this->hasMany(DeclaracionResiduoDetalle::class, 'declaracion_id');
    }

    public function getTotalToneladasAttribute(): float
    {
        return (float) ($this->toneladas_comunes + $this->toneladas_reciclables + $this->toneladas_peligrosas);
    }
}