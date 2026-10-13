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
            ['id_roles' => 5, 'nombre' => 'alcaldesa', 'descripcion' => 'Despacho Ejecutivo / Alcaldesa', 'created_at' => now()],
        ]);

        // 2. Direcciones
        DB::table('direcciones')->insertOrIgnore([
            ['id_direccion' => 1, 'nombre_direc' => 'Dirección de Ambiente', 'descripcion_direc' => 'Control Ambiental', 'created_at' => now()],
            ['id_direccion' => 2, 'nombre_direc' => 'Dirección de Residuos', 'descripcion_direc' => 'Control Residual de las empresas', 'created_at' => now()],
        ]);

        // 3. Usuario Ciudadano (Rol 4 - Sin dirección)[cite: 7]
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

        // 4. Usuario Administrador (Rol 1 - Sin dirección)[cite: 7]
        DB::table('users')->updateOrInsert(
            ['cedula' => 'V-12345678'],
            [
                'role_id' => 1,
                'direccion_id' => null,
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

        // 5. Usuario Alcaldesa (Rol 5 - Sin dirección)[cite: 7]
        DB::table('users')->updateOrInsert(
            ['cedula' => 'V-00000001'],
            [
                'role_id' => 5,
                'direccion_id' => null,
                'telefono' => '04120000000',
                'nombre' => 'Alcaldesa',
                'apellido' => 'Municipal',
                'correo' => 'alcaldesa@carrizal.gob.ve',
                'password' => Hash::make('password123'),
                'accepted_terms' => true,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // 6. Usuario Director (Rol 2 - Dirección de Ambiente - Sí lleva dirección)[cite: 7]
        DB::table('users')->updateOrInsert(
            ['cedula' => 'V-87654321'],
            [
                'role_id' => 2,
                'direccion_id' => 1,
                'telefono' => '04149876543',
                'nombre' => 'Carlos',
                'apellido' => 'Mendoza',
                'correo' => 'director@ambiente.com',
                'password' => Hash::make('12345678'),
                'accepted_terms' => true,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // 7. Usuario Director (Rol 2 - Dirección de Residuos - Sí lleva dirección)[cite: 7]
        DB::table('users')->updateOrInsert(
            ['cedula' => 'V-98765432'],
            [
                'role_id' => 2,
                'direccion_id' => 2,
                'telefono' => '04129876943',
                'nombre' => 'Simon',
                'apellido' => 'Bolivar',
                'correo' => 'director@residuos.com',
                'password' => Hash::make('12345678'),
                'accepted_terms' => true,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // 8. Usuario Inspector (Rol 3 - Dirección de Ambiente - Sí lleva dirección)[cite: 7]
        DB::table('users')->updateOrInsert(
            ['cedula' => 'V-11223344'],
            [
                'role_id' => 3,
                'direccion_id' => 1,
                'telefono' => '04241112233',
                'nombre' => 'Roberto',
                'apellido' => 'Gómez',
                'correo' => 'inspector@ambiente.com',
                'password' => Hash::make('12345678'),
                'accepted_terms' => true,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // 9. Registro en la tabla de inspectores[cite: 7]
        $inspectorUser = DB::table('users')->where('cedula', 'V-11223344')->first();

        if ($inspectorUser) {
            DB::table('inspectores')->updateOrInsert(
                ['user_id' => $inspectorUser->id],
                [
                    'direccion_id' => 1,
                    'codigo_credencial' => 'INS-2026-001',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        // 10. Ajustar secuencias de ID en PostgreSQL[cite: 7]
        DB::statement("SELECT setval(pg_get_serial_sequence('users', 'id'), coalesce(max(id), 1)) FROM users;");
        
        if (DB::getDriverName() === 'pgsql') {
            DB::statement("SELECT setval(pg_get_serial_sequence('inspectores', 'id'), coalesce(max(id), 1)) FROM inspectores;");
        }
    }
}