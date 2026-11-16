<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TipoTramite extends Model
{
    protected $table = 'tipos_tramites';

    protected $fillable = [
        'slug',
        'nombre',
        'descripcion',
        'plazo_dias',
    ];

    public function recaudos(): HasMany
    {
        return $this->hasMany(RecaudoCatalogo::class, 'tipo_tramite', 'slug');
    }
}