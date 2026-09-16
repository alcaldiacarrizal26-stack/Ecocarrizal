<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Direccion extends Model
{
    use HasFactory;

    protected $table = 'direcciones';

    // Solución al QueryException: Define la clave primaria correspondiente a tu migración
    protected $primaryKey = 'id_direccion';

    protected $fillable = [
        'nombre_direc',
        'descripcion_direc',
    ];

    // Relación inversa con usuarios
    public function usuarios()
    {
        return $this->hasMany(User::class, 'direccion_id', 'id_direccion');
    }

    public function inspectores(): HasMany
    {
        return $this->hasMany(Inspector::class, 'direccion_id', 'id_direccion');
    }
}