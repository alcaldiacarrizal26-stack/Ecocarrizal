<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Profile extends Model
{
    use HasUuids;

    protected $primaryKey = 'id_profiles';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_profiles',
        'email',
        'nombres',
        'apellidos',
        'cedula',
        'telefono',
        'accepted_terms',
        'role_id',
        'is_active',
        'direction_id',
    ];

    protected $casts = [
        'accepted_terms' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_profiles', 'id');
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'role_id', 'id_roles');
    }

    public function direccion(): BelongsTo
    {
        return $this->belongsTo(Direccion::class, 'direction_id', 'id_direccion');
    }
}