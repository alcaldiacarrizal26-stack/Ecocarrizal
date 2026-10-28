<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Auth\Notifications\ResetPassword as ResetPasswordNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements CanResetPasswordContract
{
    use HasFactory, Notifiable, Auditable, CanResetPassword;

    protected string $moduloBitacora = 'users';

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
        'email_verified_at',
    ];

    protected $hidden = [
        'password',
        'rememberToken',
    ];

    protected $casts = [
        'accepted_terms' => 'boolean',
        'is_active' => 'boolean',
        'email_verified_at' => 'datetime',
    ];

    // Relaciones
    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id', 'id_roles');
    }

    public function direccion()
    {
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

    public function empresa()
    {
        return $this->hasOne(Empresa::class, 'user_id');
    }

    public function tieneEmpresa(): bool
    {
        return !is_null($this->empresa);
    }

    public function tieneEmpresaAprobada(): bool
    {
        return $this->empresa && $this->empresa->estatus === 'APROBADO';
    }

    /**
     * Mapea la columna de correo del usuario para el restablecimiento de contraseña.
     */
    public function getEmailForPasswordReset(): string
    {
        return $this->correo;
    }

    /**
     * Personaliza el envío de la notificación de restablecimiento de contraseña.
     */
    public function sendPasswordResetNotification($token): void
    {
        // Generamos la URL absoluta con el token y el correo
        $url = route('password.reset', [
            'token' => $token,
            'correo' => $this->correo,
        ]);

        // Enviamos el correo directamente usando nuestra plantilla base HTML
        \Illuminate\Support\Facades\Mail::send('emails.plantilla-base', [
            'titulo'      => 'Recuperación de Contraseña — Alcaldía de Carrizal',
            'subtitulo'   => 'Restablecimiento de credenciales',
            'contenido'   => 'Has solicitado restablecer tu contraseña para acceder a la plataforma de Ecosocialismo. Haz clic en el botón inferior para proceder con el cambio.<br><br>Este enlace de seguridad expirará en 60 minutos.',
            'textoAccion' => 'Restablecer Contraseña',
            'urlAccion'   => $url
        ], function ($message) {
            $message->to($this->correo)
                    ->subject('Recuperación de Contraseña - Alcaldía de Carrizal');
        });
    }
}