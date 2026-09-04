<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Solicitud extends Model
{
    use HasUuids;

    protected $table = 'solicitudes';

    protected $fillable = [
        'codigo_expediente',
        'user_id',
        'tipo_tramite',
        'sector',
        'direccion_exacta',
        'motivo_descripcion',
        'datos_especificos',
        'estado',
        'proximo_paso',
    ];

    protected $casts = [
        'datos_especificos' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function recaudos(): HasMany
    {
        return $this->hasMany(SolicitudRecaudo::class);
    }
}