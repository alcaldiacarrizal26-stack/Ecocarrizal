<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Inspeccion extends Model
{
    use HasUuids, Auditable;

    protected string $moduloBitacora = 'inspecciones';
    protected $table = 'inspecciones';

    protected $fillable = [
        'codigo_acta',
        'tipo_inspeccion',
        'base_legal_inspeccion',
        'orden_adjunta_path',
        'solicitud_id',
        'denuncia_id',
        'orden_inspeccion_id',
        'inspector_id',
        'empresa_id',
        'fecha_visita',
        'observaciones_campo',
        'hallazgos',
        'fotos_evidencia',
        'recomendaciones',
        'dictamen',
    ];

    protected $casts = [
        'fecha_visita' => 'datetime',
        'hallazgos' => 'array',
        'fotos_evidencia' => 'array',
    ];

    public function inspector(): BelongsTo
    {
        return $this->belongsTo(User::class, 'inspector_id');
    }

    public function solicitud(): BelongsTo
    {
        return $this->belongsTo(Solicitud::class);
    }

    public function denuncia(): BelongsTo
    {
        return $this->belongsTo(Denuncia::class);
    }

    public function ordenInspeccion(): BelongsTo
    {
        return $this->belongsTo(OrdenInspeccion::class, 'orden_inspeccion_id', 'id');
    }

    public function sancion()
    {
        return $this->hasOne(Sancion::class, 'inspeccion_id', 'id');
    }

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class, 'empresa_id', 'id');
    }
}