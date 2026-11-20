<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketMensaje extends Model
{
    use HasUuids;

    protected $table = 'ticket_mensajes';

    protected $fillable = [
        'ticket_id',
        'user_id',
        'es_admin',
        'mensaje',
        'evidencia_path',
        'editado_at',
    ];

    protected $casts = [
        'es_admin' => 'boolean',
        'editado_at' => 'datetime',
    ];

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(TicketSoporte::class, 'ticket_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}