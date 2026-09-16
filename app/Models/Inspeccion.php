<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Inspeccion extends Model
{
    use HasUuids;

    protected $table = 'inspecciones';

    protected $fillable = [
        'codigo_acta',
        'solicitud_id',
        'denuncia_id',
        'inspector_id',
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
        return $this->belongsTo(Inspector::class);
    }

    public function solicitud(): BelongsTo
    {
        return $this->belongsTo(Solicitud::class);
    }

    public function denuncia(): BelongsTo
    {
        return $this->belongsTo(Denuncia::class);
    }
}