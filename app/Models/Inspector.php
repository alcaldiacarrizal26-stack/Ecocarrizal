<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inspector extends Model
{
    use HasFactory;

    protected $table = 'inspectores';

    protected $fillable = [
        'user_id',
        'direccion_id',
        'codigo_credencial',
        'especialidad',
        'disponible',
    ];

    protected $casts = [
        'disponible' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function direccion()
    {
        return $this->belongsTo(Direccion::class, 'direccion_id', 'id_direccion');
    }

    public function denuncias()
    {
        return $this->hasMany(Denuncia::class, 'inspector_id');
    }

    public function solicitudes()
    {
        return $this->hasMany(Solicitud::class, 'inspector_id');
    }
}