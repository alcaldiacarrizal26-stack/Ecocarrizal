<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SolicitudRecaudo extends Model
{
    use HasUuids;

    protected $table = 'solicitud_recaudos';
    const UPDATED_AT = null;

    protected $fillable = [
        'solicitud_id',
        'tipo_recaudo',
        'nombre_archivo',
        'bucket_path',
        'archivo_url',
        'es_evidencia_fotografica',
    ];

    protected $casts = [
        'es_evidencia_fotografica' => 'boolean',
    ];

    public function solicitud(): BelongsTo
    {
        return $this->belongsTo(Solicitud::class);
    }
}