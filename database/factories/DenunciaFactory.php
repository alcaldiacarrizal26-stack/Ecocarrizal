<?php

namespace Database\Factories;

use App\Models\Denuncia;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class DenunciaFactory extends Factory
{
    protected $model = Denuncia::class;

    public function definition(): array
    {
        return [
            'id' => (string) Str::uuid(),
            'codigo' => 'DEN-' . strtoupper(Str::random(8)),
            'user_id' => User::factory(),
            'inspector_id' => null,
            'tipo_contaminacion' => 'sónica',
            'otro_tipo_contaminacion' => null,
            'comuna' => fake()->word(),
            'sector' => fake()->word(),
            'punto_referencia' => fake()->sentence(),
            'nivel_gravedad' => 'baja',
            'justificacion_gravedad' => null,
            'descripcion' => fake()->paragraph(),
            'evidencias' => null,
            'estatus' => 'pendiente',
            'fecha_inicio_inspeccion' => null,
            'fecha_cierre' => null,
        ];
    }
}