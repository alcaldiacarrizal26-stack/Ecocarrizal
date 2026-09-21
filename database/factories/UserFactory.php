<?php

namespace Database\Factories;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            // Asigna un rol por defecto creando uno si no existe
            'role_id' => Role::first()->id_roles ?? Role::factory(),
            'direccion_id' => null,

            // Columnas según tu migración
            'cedula' => 'V-' . fake()->unique()->numberBetween(10000000, 30000000),
            'telefono' => fake()->numerify('04##########'),
            'nombre' => fake()->firstName(),
            'apellido' => fake()->lastName(),
            'correo' => fake()->unique()->safeEmail(),

            'accepted_terms' => true,
            'is_active' => true,

            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'remember_token' => Str::random(10),
        ];
    }
}