<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Request;

class BitacoraEvento extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'bitacora_eventos';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'user_id',
        'modulo',
        'tipo_evento',
        'nivel',
        'descripcion',
        'auditable_type',
        'auditable_id',
        'valores_anteriores',
        'valores_nuevos',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'valores_anteriores' => 'array',
        'valores_nuevos'     => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function auditable()
    {
        return $this->morphTo();
    }

    /**
     * Registrar evento de auditoría o fallo manualmente
     */
    public static function registrar(array $datos)
    {
        $tipo_evento = $datos['tipo_evento'];
        $descripcion = $datos['descripcion'];
        $modulo = $datos['modulo'] ?? 'general';
        $nivel = $datos['nivel'] ?? 'info';
        $model = $datos['model'] ?? null;
        $anteriores = $datos['anteriores'] ?? null;
        $nuevos = $datos['nuevos'] ?? null;

        return self::create([
            'user_id'            => auth()->id(),
            'modulo'             => $modulo,
            'tipo_evento'        => $tipo_evento, // 'crear', 'editar', 'eliminar', 'intento_fallido'
            'nivel'              => $nivel,       // 'info', 'warning', 'danger', 'critical'
            'descripcion'        => $descripcion,
            'auditable_type'     => $model ? get_class($model) : null,
            'auditable_id'       => $model ? (string) $model->getKey() : null,
            'valores_anteriores' => $anteriores,
            'valores_nuevos'     => $nuevos,
            'ip_address'         => Request::ip(),
            'user_agent'         => Request::userAgent(),
        ]);
    }
}