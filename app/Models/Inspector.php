<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Inspector extends Model
{
    use HasFactory, Auditable;

    protected string $moduloBitacora = 'inspectores';
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

    // -------------------------------------------------------------
    // Relaciones
    // -------------------------------------------------------------

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

    /**
     * Inspecciones o fiscalizaciones en campo (incluyendo las sorpresa)
     */
    public function inspecciones()
    {
        return $this->hasMany(Inspeccion::class, 'inspector_id');
    }

    // -------------------------------------------------------------
    // Accessors para datos personales vía Relación User
    // -------------------------------------------------------------

    public function getNombreAttribute()
    {
        return $this->user->nombre ?? 'N/A';
    }

    public function getApellidoAttribute()
    {
        return $this->user->apellido ?? '';
    }

    public function getCedulaAttribute()
    {
        return $this->user->cedula ?? 'N/A';
    }

    public function getCorreoAttribute()
    {
        return $this->user->correo ?? $this->user->email ?? 'N/A';
    }

    public function getTelefonoAttribute()
    {
        return $this->user->telefono ?? 'Sin Registrar';
    }

    // -------------------------------------------------------------
    // Scopes y Helpers
    // -------------------------------------------------------------

    /**
     * Scope para filtrar únicamente inspectores disponibles
     */
    public function scopeDisponibles(Builder $query): Builder
    {
        return $query->where('disponible', true);
    }

    /**
     * Scope para filtrar por Dirección adscrita o por especialidad
     */
    public function scopeParaDireccionOEspecialidad(Builder $query, int $direccionId, ?string $especialidad = null): Builder
    {
        return $query->where(function ($q) use ($direccionId, $especialidad) {
            $q->where('direccion_id', $direccionId);

            if ($especialidad) {
                $q->orWhere('especialidad', 'like', "%{$especialidad}%");
            }
        });
    }
}