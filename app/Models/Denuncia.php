<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Denuncia extends Model
{
    use HasUuids;

    const UPDATED_AT = null; // Solo almacena created_at

    protected $fillable = [
        'id',
        'user_id',
        'tipo_contaminacion',
        'sector',
        'punto_referencia',
        'descripcion',
        'evidencias',
    ];

    protected $casts = [
        'evidencias' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}