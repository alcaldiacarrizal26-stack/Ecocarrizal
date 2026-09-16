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
        'accepted_terms',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'rememberToken',
    ];

    protected $casts = [
        'accepted_terms' => 'boolean',
        'is_active' => 'boolean',
    ];

    // Relaciones
    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id', 'id_roles');
    }

    public function direccion()
    {
        // Se definen explícitamente la FK de users y la PK de direcciones
        return $this->belongsTo(Direccion::class, 'direccion_id', 'id_direccion');
    }

    public function inspector()
    {
        return $this->hasOne(Inspector::class, 'user_id');
    }

    // Helper Métodos de Verificación de Roles
    public function isAdmin(): bool
    {
        return strtolower($this->role?->nombre ?? '') === 'admin';
    }

    public function isDirector(): bool
    {
        return strtolower($this->role?->nombre ?? '') === 'director';
    }

    public function isInspector(): bool
    {
        return strtolower($this->role?->nombre ?? '') === 'inspector';
    }

    public function isCiudadano(): bool
    {
        return strtolower($this->role?->nombre ?? '') === 'ciudadano';
    }

    public function hasAnyRole(array|string ...$roles): bool
    {
        $roleName = strtolower($this->role?->nombre ?? '');
        $flatRoles = collect($roles)->flatten()->map(fn($r) => strtolower($r))->toArray();

        return in_array($roleName, $flatRoles, true);
    }

    // Helper de Verificación de Dirección
    public function perteneceADireccion(string $nombreDireccion): bool
    {
        return strtolower($this->direccion?->nombre_direc ?? '') === strtolower($nombreDireccion);
    }

    // Accessors
    public function getNombreCompletoAttribute(): string
    {
        return "{$this->nombre} {$this->apellido}";
    }

    public function denuncias(): HasMany
    {
        return $this->hasMany(Denuncia::class);
    }

    public function solicitudes(): HasMany
    {
        return $this->hasMany(Solicitud::class);
    }
}