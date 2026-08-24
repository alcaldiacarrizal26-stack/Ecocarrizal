<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, HasUuids, Notifiable;

    protected $fillable = ['id', 'name', 'email', 'password'];

    protected $hidden = ['password', 'remember_token'];

    public function profile(): HasOne
    {
        return $this->hasOne(Profile::class, 'id_profiles', 'id');
    }

    public function denuncias(): HasMany
    {
        return $this->hasMany(Denuncia::class);
    }

    public function solicitudes(): HasMany
    {
        return $this->hasMany(Solicitud::class);
    }

    public function empresas(): HasMany
    {
        return $this->hasMany(Empresa::class);
    }
}