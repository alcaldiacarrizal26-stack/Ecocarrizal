<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Roles
        DB::table('roles')->insertOrIgnore([
            ['id_roles' => 1, 'nombre' => 'admin', 'descripcion' => 'Administrador del sistema', 'created_at' => now()],
            ['id_roles' => 2, 'nombre' => 'director', 'descripcion' => 'Director de línea', 'created_at' => now()],
            ['id_roles' => 3, 'nombre' => 'inspector', 'descripcion' => 'Inspector ambiental', 'created_at' => now()],
            ['id_roles' => 4, 'nombre' => 'ciudadano', 'descripcion' => 'Ciudadano / Usuario externo', 'created_at' => now()],
        ]);

        // 2. Direcciones
        DB::table('direcciones')->insertOrIgnore([
            ['id_direccion' => 1, 'nombre_direc' => 'Dirección de Ambiente', 'descripcion_direc' => 'Control Ambiental', 'created_at' => now()],
            ['id_direccion' => 2, 'nombre_direc' => 'Dirección de Residuos', 'descripcion_direc' => 'Control Residual de las empresas', 'created_at' => now()],
        ]);

        // 3. Usuario Ciudadano
        DB::table('users')->updateOrInsert(
            ['cedula' => 'V-30541863'],
            [
                'role_id' => 4,
                'direccion_id' => null,
                'telefono' => '04125994461',
                'nombre' => 'Cristhian',
                'apellido' => 'Blanco',
                'correo' => 'cristhianb397@gmail.com',
                'password' => Hash::make('Cr1$th14n'),
                'accepted_terms' => true,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // 4. Usuario Administrador
        DB::table('users')->updateOrInsert(
            ['cedula' => 'V-12345678'],
            [
                'role_id' => 1,
                'direccion_id' => 1,
                'telefono' => '04121234567',
                'nombre' => 'Admin',
                'apellido' => 'Ecosocialismo',
                'correo' => 'admin@carrizal.gob.ve',
                'password' => Hash::make('password123'),
                'accepted_terms' => true,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // 5. Ajustar la secuencia del ID en PostgreSQL
        DB::statement("SELECT setval(pg_get_serial_sequence('users', 'id'), coalesce(max(id), 1)) FROM users;");
    }
}