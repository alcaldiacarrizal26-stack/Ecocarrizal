<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RecaudoCatalogo extends Model
{
    protected $table = 'recaudos_catalogo';

    protected $fillable = [
        'tipo_tramite',
        'key',
        'label',
        'tipo_requisito',
        'hint'
    ];
}