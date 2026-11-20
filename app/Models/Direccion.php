<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Direccion extends Model
{
    use HasFactory;

    protected $table = 'direcciones';

    protected $fillable = [
        'nombre_direc',
        'descripcion_direc',
    ];

    // Relación inversa con usuarios
    public function usuarios()
    {
        return $this->hasMany(User::class, 'direccion_id', 'id');
    }
}