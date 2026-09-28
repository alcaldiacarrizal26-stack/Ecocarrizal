<?php

namespace App\Models;

use App\Traits\Auditable;
use App\Observers\SolicitudObserver;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[ObservedBy([SolicitudObserver::class])]
class Solicitud extends Model
{
    use HasFactory, HasUuids, Auditable;

    protected $table = 'solicitudes';

    protected string $moduloBitacora = 'solicitudes';

    protected $fillable = [
        'codigo_expediente',
        'user_id',
        'inspector_id',
        'comuna',
        'sector',
        'direccion_exacta',
        'motivo_descripcion',
        'datos_especificos',
        'tipo_tramite',
        'estado',
        'fecha_limite_documentos',
        'fecha_asignacion_inspector', // <-- Agregado
        'fecha_inicio_inspeccion',
        'fecha_cierre',
    ];

    protected $casts = [
        'datos_especificos' => 'array',
        'fecha_limite_documentos' => 'datetime',
        'fecha_asignacion_inspector' => 'datetime', // <-- Agregado
        'fecha_inicio_inspeccion' => 'datetime',
        'fecha_cierre' => 'datetime',
    ];

    /* --- Relaciones --- */

    public function solicitante(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function inspector(): BelongsTo
    {
        return $this->belongsTo(Inspector::class, 'inspector_id');
    }

    public function recaudos(): HasMany
    {
        return $this->hasMany(SolicitudRecaudo::class, 'solicitud_id');
    }

    /* --- Accessors Útiles --- */

    public function getAplicaAutorizacionPropietarioAttribute(): bool
    {
        return (bool) ($this->datos_especificos['aplica_autorizacion_propietario'] ?? false);
    }

    public function getAplicaVariablesAmbientalesAttribute(): bool
    {
        return (bool) ($this->datos_especificos['aplica_variables_ambientales'] ?? false);
    }
}