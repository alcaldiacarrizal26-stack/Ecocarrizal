<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EntregaCompensacion extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'entregas_compensacion';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'compensacion_id',
        'user_id',
        'cantidad_entregada',
        'especie_entregada',
        'comprobante_path',
        'observacion_ciudadano',
        'observacion_director',
        'estatus',
        'fecha_revision',
    ];

    protected $casts = [
        'cantidad_entregada' => 'integer',
        'fecha_revision'     => 'datetime',
    ];

    public function compensacion(): BelongsTo
    {
        return $this->belongsTo(Compensacion::class, 'compensacion_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}