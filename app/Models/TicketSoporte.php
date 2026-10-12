<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketSoporte extends Model
{
    use HasUuids, Auditable;

    protected string $moduloBitacora = 'soporte';
    protected $table = 'tickets_soporte';

    protected $fillable = [
        'user_id',
        'asunto',
        'mensaje',
        'prioridad',
        'justificacion_alta',
        'evidencia_path',
        'estatus',
        'respuesta_admin',
        'fecha_resolucion',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}