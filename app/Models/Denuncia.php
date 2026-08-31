<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Denuncia extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'denuncias';
    
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'user_id',
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
    ];

    protected $casts = [
        'evidencias' => 'array',
    ];

    protected static function booted()
    {
        static::creating(function ($denuncia) {
            $anioActual = date('Y');
            $prefix = 'DEN-' . $anioActual . '-';

            // Obtener el último correlativo registrado del año actual
            $ultimoUltimoRegistro = static::where('codigo', 'LIKE', $prefix . '%')
                ->latest('id')
                ->first();

            if ($ultimoUltimoRegistro) {
                // Extraer el número actual e incrementarlo
                $partes = explode('-', $ultimoUltimoRegistro->codigo);
                $numero = (int) end($partes) + 1;
            } else {
                $numero = 1;
            }

            // Formatear a 4 dígitos rellenos con ceros a la izquierda (ej. 0001)
            $denuncia->codigo = $prefix . str_pad($numero, 4, '0', STR_PAD_LEFT);
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}