<?php

namespace Database\Factories;

use App\Models\Solicitud;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class SolicitudFactory extends Factory
{
    protected $model = Solicitud::class;

    public function definition(): array
    {
        return [
            'id' => (string) Str::uuid(),
            'codigo_expediente' => 'SOL-' . strtoupper(Str::random(8)),
            'user_id' => User::factory(),
            'inspector_id' => null,
            'tipo_tramite' => 'inspeccion',
            'estado' => 'pendiente',
            'comuna' => fake()->word(),
            'sector' => fake()->word(),
            'direccion_exacta' => fake()->address(),
            'motivo_descripcion' => fake()->paragraph(),
            'datos_especificos' => null,
            'fecha_limite_documentos' => null,
            'fecha_inicio_inspeccion' => null,
            'fecha_cierre' => null,
        ];
    }
}