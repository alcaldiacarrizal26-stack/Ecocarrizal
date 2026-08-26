<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Llenar Roles
        DB::table('roles')->insertOrIgnore([
            ['id_roles' => 1, 'nombre' => 'admin', 'descripcion' => 'Administrador del sistema', 'created_at' => now()],
            ['id_roles' => 2, 'nombre' => 'director', 'descripcion' => 'Director de línea', 'created_at' => now()],
            ['id_roles' => 3, 'nombre' => 'inspector', 'descripcion' => 'Inspector ambiental', 'created_at' => now()],
            ['id_roles' => 4, 'nombre' => 'ciudadano', 'descripcion' => 'Ciudadano / Usuario externo', 'created_at' => now()],
        ]);

        // 2. Llenar Direcciones (según tu estructura de la alcaldía)
        DB::table('direcciones')->insertOrIgnore([
            ['id_direccion' => 1, 'nombre_direc' => 'Dirección de Ecosocialismo', 'descripcion_direc' => 'Gestión ambiental municipal', 'created_at' => now()],
            ['id_direccion' => 2, 'nombre_direc' => 'Ingeniería y Ambiente', 'descripcion_direc' => 'Control de obras y talas', 'created_at' => now()],
        ]);

        // 3. Crear usuario Administrador por defecto (opcional pero útil)
        DB::table('users')->insertOrIgnore([
            'id' => 1,
            'role_id' => 1, // Admin
            'direccion_id' => 1,
            'cedula' => 'V-12345678',
            'telefono' => '04121234567',
            'nombre' => 'Admin',
            'apellido' => 'Ecosocialismo',
            'correo' => 'admin@carrizal.gob.ve',
            'password' => Hash::make('password123'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}