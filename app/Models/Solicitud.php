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
        'inspector_id',
        'comuna',
        'sector',
        'direccion_exacta',
        'motivo_descripcion',
        'datos_especificos',
        'proximo_paso',
        'tipo_tramite',
        'estado',
    ];

    protected $casts = [
        'datos_especificos' => 'array',
    ];

    /* --- Relaciones --- */

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function solicitante(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function inspector(): BelongsTo
    {
        return $this->belongsTo(User::class, 'inspector_id');
    }

    public function recaudos(): HasMany
    {
        return $this->hasMany(SolicitudRecaudo::class, 'solicitud_id');
    }

    /* --- Accessors Útiles --- */

    // Permite consultar $solicitud->aplica_autorizacion_propietario directamente
    public function getAplicaAutorizacionPropietarioAttribute(): bool
    {
        return (bool) ($this->datos_especificos['aplica_autorizacion_propietario'] ?? false);
    }

    // Permite consultar $solicitud->aplica_variables_ambientales directamente
    public function getAplicaVariablesAmbientalesAttribute(): bool
    {
        return (bool) ($this->datos_especificos['aplica_variables_ambientales'] ?? false);
    }
}