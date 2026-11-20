<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SolicitudRecaudo extends Model
{
    use HasUuids, Auditable;

    protected string $moduloBitacora = 'solicitud_recaudos';

    protected $table = 'solicitud_recaudos';

    protected $fillable = [
        'solicitud_id',
        'recaudo_catalogo_id',
        'nombre_archivo',
        'bucket_path',
        'archivo_url',
        'created_at',
    ];

    protected $casts = [
        'es_evidencia_fotografica' => 'boolean',
        'created_at'               => 'datetime',
    ];

    public function solicitud(): BelongsTo
    {
        return $this->belongsTo(Solicitud::class, 'solicitud_id');
    }

    public function recaudoCatalogo(): BelongsTo // <-- NUEVA RELACIÓN
    {
        return $this->belongsTo(RecaudoCatalogo::class, 'recaudo_catalogo_id');
    }
}