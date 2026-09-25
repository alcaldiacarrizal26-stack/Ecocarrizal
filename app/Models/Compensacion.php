<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Compensacion extends Model
{
    use HasFactory, HasUuids;

    /**
     * Nombre de la tabla asociada al modelo.
     *
     * @var string
     */
    protected $table = 'compensaciones';

    /**
     * El tipo de clave primaria es UUID.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * Indica si la clave primaria es auto-incrementable.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * Atributos asignables en masa.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'codigo_resolucion',
        'solicitud_id',
        'user_id',
        'inspeccion_id',
        'especie_requerida',
        'cantidad_asignada',
        'cantidad_entregada',
        'comprobante_path',
        'fecha_entrega',
        'observaciones',
        'estatus',
    ];

    /**
     * Atributos que deben ser convertidos a tipos nativos.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'cantidad_asignada'  => 'integer',
        'cantidad_entregada' => 'integer',
        'fecha_entrega'      => 'datetime',
        'created_at'         => 'datetime',
        'updated_at'         => 'datetime',
    ];

    /* ==========================================
     | Relaciones
     ========================================== */

    /**
     * Relación con la solicitud asociada.
     */
    public function solicitud(): BelongsTo
    {
        return $this->belongsTo(Solicitud::class, 'solicitud_id');
    }

    /**
     * Relación con el usuario/solicitante.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relación con la inspección asociada (opcional).
     */
    public function inspeccion(): BelongsTo
    {
        return $this->belongsTo(Inspeccion::class, 'inspeccion_id');
    }
}