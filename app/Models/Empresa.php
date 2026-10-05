<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Empresa extends Model
{
    use HasUuids, Auditable;
    protected string $moduloBitacora = 'empresas';
    protected $fillable = [
        'user_id',
        'rif',
        'razon_social',
        'nombre_comercial',
        'numero_cuenta_patente',
        'correo_comercial',
        'telefono_comercial',
        'comuna',
        'sector',
        'calle_avenida',
        'edificio_galpon_local',
        'estatus',
        'observaciones_fiscalia',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function perfilAmbiental(): HasOne
    {
        return $this->hasOne(PerfilAmbiental::class);
    }

    public function documentosExpediente()
    {
        return $this->hasMany(DocumentoExpediente::class, 'empresa_id', 'id');
    }

    public function declaracionesResiduos()
    {
        return $this->hasMany(DeclaracionResiduo::class, 'empresa_id', 'id');
    }
}