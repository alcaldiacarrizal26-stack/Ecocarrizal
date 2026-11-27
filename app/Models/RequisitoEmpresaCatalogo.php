<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RequisitoEmpresaCatalogo extends Model
{
    protected $table = 'requisitos_empresas_catalogo';

    protected $fillable = [
        'codigo',
        'nombre',
        'descripcion',
        'aplica_a_rol',
        'tipo_requisito',
        'condicion_flag',
        'es_vitalicio_default',
        'orden',
        'activo',
    ];

    protected $casts = [
        'es_vitalicio_default' => 'boolean',
        'activo' => 'boolean',
        'orden' => 'integer',
    ];

    protected $appends = ['es_obligatorio'];

    // Compatibilidad con código legado que usa $req->es_obligatorio
    public function getEsObligatorioAttribute(): bool
    {
        return $this->tipo_requisito === 'OBLIGATORIO';
    }

    // Convierte "GENERADOR,MANEJADOR" en ['GENERADOR', 'MANEJADOR']
    public function getRolesAttribute(): array
    {
        return array_map('trim', explode(',', $this->aplica_a_rol));
    }

    // Helper para verificar si aplica a un rol concreto
    public function aplicaARol(string $rol): bool
    {
        return in_array('TODOS', $this->roles, true)
            || in_array($rol, $this->roles, true);
    }

    // Relación con los documentos subidos por las empresas
    public function documentosExpediente(): HasMany
    {
        return $this->hasMany(DocumentoExpediente::class, 'requisito_id');
    }
}