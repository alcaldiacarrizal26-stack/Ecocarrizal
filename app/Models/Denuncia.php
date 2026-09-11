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
        'codigo',
        'user_id',
        'inspector_id', // Agregado para permitir asignación de responsable
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

            $ultimoUltimoRegistro = static::where('codigo', 'LIKE', $prefix . '%')
                ->latest('created_at')
                ->first();

            if ($ultimoUltimoRegistro) {
                $partes = explode('-', $ultimoUltimoRegistro->codigo);
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

    /**
     * Relación con el inspector asignado (Tabla 'inspectores')
     */
    public function inspector()
    {
        return $this->belongsTo(Inspector::class, 'inspector_id');
    }
}