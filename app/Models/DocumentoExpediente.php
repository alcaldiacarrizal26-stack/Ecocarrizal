<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentoExpediente extends Model
{
    use HasUuids, Auditable;
    protected string $moduloBitacora = 'documentos_expediente';
    protected $table = 'documentos_expediente';
    const UPDATED_AT = null;

    protected $fillable = [
        'empresa_id',
        'codigo_requisito',
        'nombre_requisito',
        'numero_folio',
        'archivo_path',
        'fecha_emision',
        'fecha_vencimiento',
        'es_vitalicio',
        'estado_revision',
        'observaciones',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'fecha_emision' => 'datetime',
        'fecha_vencimiento' => 'datetime',
        'es_vitalicio' => 'boolean',
    ];

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    public function getEstatusVigenciaAttribute(): string
    {
        if ($this->es_vitalicio || !$this->fecha_vencimiento) {
            return 'VITALICIO';
        }

        if ($this->fecha_vencimiento->isPast()) {
            return 'VENCIDO';
        }

        if ($this->fecha_vencimiento->diffInDays(now()) <= 30) {
            return 'POR_VENCER';
        }

        return 'VIGENTE';
    }
}