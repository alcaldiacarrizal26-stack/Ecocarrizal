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

    // Desactiva la actualización automática de timestamps.
    // Se gestiona solo 'created_at' manualmente o por default en la BD.
    public $timestamps = false;

    protected $fillable = [
        'solicitud_id',
        'tipo_recaudo',
        'nombre_archivo',
        'bucket_path',
        'archivo_url',
        'es_evidencia_fotografica',
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
}