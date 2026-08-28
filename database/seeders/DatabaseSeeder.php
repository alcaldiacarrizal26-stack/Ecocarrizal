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
            ['id_direccion' => 1, 'nombre_direc' => 'Dirección de Ambiente', 'descripcion_direc' => 'Control Ambiental', 'created_at' => now()],
            ['id_direccion' => 2, 'nombre_direc' => 'Dirección de Residuos', 'descripcion_direc' => 'Control Residual de las empresas', 'created_at' => now()],
        ]);

        // 3. Crear usuario Administrador por defecto (opcional pero útil)
        DB::table('users')->insertOrIgnore([
            'id' => 777,
            'role_id' => 4,
            'cedula' => 'V-30541863',
            'telefono' => '04125994461',
            'nombre' => 'Cristhian',
            'apellido' => 'Blanco',
            'correo' => 'cristhianb397@gmail.com',
            'password' => Hash::make('Cr1$th14n'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

                DB::table('users')->insertOrIgnore([
            'id' => 778,
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