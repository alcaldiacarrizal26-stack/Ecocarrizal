<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentoExpediente extends Model
{
    use HasUuids;

    protected $table = 'documentos_expediente';
    const UPDATED_AT = null;

    protected $fillable = [
        'empresa_id',
        'codigo_requisito',
        'nombre_requisito',
        'numero_folio',
        'archivo_path',
        'estado',
        'observaciones',
    ];

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }
}