<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Denuncia extends Model
{
    use HasFactory, HasUuids, Auditable;

    protected string $moduloBitacora = 'denuncias';

    protected $table = 'denuncias';
    
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'codigo',
        'user_id',
        'inspector_id',
        'tipo_contaminacion',
        'otro_tipo_contaminacion',
        'comuna',
        'sector',
        'punto_referencia',
        'nivel_gravedad',
        'justificacion_gravedad',
        'descripcion',
        'evidencias',
        'estatus',
        'fecha_asignacion_inspector', // <-- Agregado
        'fecha_inicio_inspeccion',
        'fecha_cierre',
    ];

    protected $casts = [
        'evidencias' => 'array',
        'fecha_asignacion_inspector' => 'datetime', // <-- Agregado
        'fecha_inicio_inspeccion' => 'datetime',
        'fecha_cierre' => 'datetime',
    ];

    protected static function booted()
    {
        static::creating(function ($denuncia) {
            $anioActual = date('Y');
            $prefix = 'DEN-' . $anioActual . '-';

            $ultimoRegistro = static::where('codigo', 'LIKE', $prefix . '%')
                ->latest('created_at')
                ->first();

            if ($ultimoRegistro) {
                $partes = explode('-', $ultimoRegistro->codigo);
                $numero = (int) end($partes) + 1;
            } else {
                $numero = 1;
            }

            $denuncia->codigo = $prefix . str_pad($numero, 4, '0', STR_PAD_LEFT);
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function inspector()
    {
        return $this->belongsTo(Inspector::class, 'inspector_id');
    }
}