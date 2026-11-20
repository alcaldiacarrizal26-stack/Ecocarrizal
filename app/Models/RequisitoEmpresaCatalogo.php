<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequisitoEmpresaCatalogo extends Model
{
    protected $table = 'requisitos_empresas_catalogo';

    protected $fillable = [
        'codigo',
        'nombre',
        'descripcion',
        'es_obligatorio',
        'activo'
    ];
}