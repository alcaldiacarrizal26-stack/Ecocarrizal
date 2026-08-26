<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $table = 'roles';
    protected $primaryKey = 'id_roles'; // <--- Indicar la clave primaria correcta

    protected $fillable = [
        'nombre',
        'label',
    ];

    public function usuarios()
    {
        return $this->hasMany(User::class);
    }
}