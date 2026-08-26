<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'role_id',
        'direccion_id',
        'cedula',
        'telefono',
        'nombre',
        'apellido',
        'correo',
        'password',
    ];

    protected $hidden = [
        'password',
        'rememberToken',
    ];

    // Accessor para obtener la Cédula/RIF formateado (ej. V-12345678)
    public function getDocumentoAttribute(): string
    {
        return "{$this->nacionalidad}-{$this->cedula_num}";
    }

    // Accessor para obtener el número telefónico completo
    public function getTelefonoAttribute(): string
    {
        return "{$this->prefijo_telefono}{$this->telefono_num}";
    }

    // Relaciones
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function direccion()
    {
        return $this->belongsTo(Direccion::class);
    }

    // Helper Métodos de Verificación de Roles
    public function isAdmin(): bool
    {
        return $this->role?->nombre === 'admin';
    }

    public function isDirector(): bool
    {
        return $this->role?->nombre === 'director';
    }

    public function isInspector(): bool
    {
        return $this->role?->nombre === 'inspector';
    }

    public function isCiudadano(): bool
    {
        return $this->role?->nombre === 'ciudadano';
    }

    // Helper de Verificación de Dirección
    public function perteneceADireccion(string $slugDireccion): bool
    {
        return $this->direccion?->slug === $slugDireccion;
    }
}