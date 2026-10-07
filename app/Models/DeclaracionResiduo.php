<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class DeclaracionResiduo extends Model
{
    use HasFactory, HasUuids, Auditable;

    protected string $moduloBitacora = 'declaraciones_residuos';

    protected $table = 'declaraciones_residuos';

    protected $fillable = [
        'empresa_id',
        'user_id',
        'rol_declarante',
        'periodo',
        'frecuencia',
        'fecha_inicio_periodo',
        'fecha_fin_periodo',
        'toneladas_comunes',
        'toneladas_reciclables',
        'toneladas_peligrosas',
        'manejadora_rif',
        'manejadora_nombre',
        'generador_origen_rif',
        'generador_origen_nombre',
        'hoja_seguimiento_numero',
        'vehiculo_placa',
        'destino_final_nombre',
        'manifiestos_archivos',
        'estatus',
        'observaciones_fiscalia',
    ];

    protected $casts = [
        'fecha_inicio_periodo' => 'date',
        'fecha_fin_periodo' => 'date',
        'toneladas_comunes' => 'decimal:3',
        'toneladas_reciclables' => 'decimal:3',
        'toneladas_peligrosas' => 'decimal:3',
        'manifiestos_archivos' => 'array',
    ];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function detalles()
    {
        return $this->hasMany(DeclaracionResiduoDetalle::class, 'declaracion_id');
    }
}