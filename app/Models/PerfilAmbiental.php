<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class PerfilAmbiental extends Model
{
    use HasUuids;

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
        'manejadora_contratada',
        'actividades_manejador',
        'sustancias_manejadas',
        'residuos_tipos',
    ];

    protected $casts = [
        'tiene_renma' => 'boolean',
        'genera_efluentes' => 'boolean',
        'genera_emisiones' => 'boolean',
        'genera_peligrosos' => 'boolean',
        'genera_ruidos' => 'boolean',
        'usa_pozo_agua' => 'boolean',
        'actividades_manejador' => 'array',
        'residuos_tipos' => 'array',
    ];

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    public function residuos(): BelongsToMany
    {
        return $this->belongsToMany(Residuo::class, 'perfil_ambiental_residuo', 'perfil_ambiental_id', 'residuo_id');
    }
}