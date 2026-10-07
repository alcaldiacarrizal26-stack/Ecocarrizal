<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class PerfilAmbiental extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'perfiles_ambientales';

    protected $fillable = [
        'empresa_id',
        'rol_ambiental',
        'subtipo_generador',
        'numero_racda',
        'genera_efluentes',
        'genera_emisiones',
        'genera_peligrosos',
        'genera_ruidos',
        'usa_pozo_agua',
        'modalidad_manejadora',
        'manejadora_contratada',
        'manejadora_rif',
        'manejadora_racda',
        'modalidad_atencion_manejador',
        'generador_exclusivo_nombre',
        'generador_exclusivo_rif',
        'generador_exclusivo_racda',
        'actividades_manejador',
        'sustancias_manejadas',
        'residuos_tipos',
        'residuos_comunes',
        'residuos_reciclables',
        'residuos_peligrosos',
    ];

    protected $casts = [
        'genera_efluentes' => 'boolean',
        'genera_emisiones' => 'boolean',
        'genera_peligrosos' => 'boolean',
        'genera_ruidos' => 'boolean',
        'usa_pozo_agua' => 'boolean',
        'actividades_manejador' => 'array',
        'residuos_tipos' => 'array',
        'residuos_comunes' => 'array',
        'residuos_reciclables' => 'array',
        'residuos_peligrosos' => 'array',
    ];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }

    // Accessors útiles
    public function getEsManejadorAttribute(): bool
    {
        return $this->rol_ambiental === 'MANEJADOR';
    }

    public function getEsGeneradorAttribute(): bool
    {
        return $this->rol_ambiental === 'GENERADOR';
    }
}