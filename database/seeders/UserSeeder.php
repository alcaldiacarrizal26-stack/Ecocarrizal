<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // IDs numéricos de roles y direcciones
        $roleAdmin     = 1;
        $roleDirector  = 2;
        $roleInspector = 3;
        $roleCiudadano = 4;
        $roleAlcaldesa = 5;

        $dirAmbiente = 1;
        $dirResiduos = 2;

        // 1. Usuario Ciudadano
        DB::table('users')->updateOrInsert(
            ['cedula' => 'V-30541863'],
            [
                'id' => (string) Str::uuid(),
                'role_id' => $roleCiudadano,
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
                'email_verified_at' => now(),
            ]
        );

        // 2. Usuario Administrador
        DB::table('users')->updateOrInsert(
            ['cedula' => 'V-12345678'],
            [
                'id' => (string) Str::uuid(),
                'role_id' => $roleAdmin,
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
                'email_verified_at' => now(),
            ]
        );

        // 3. Usuario Alcaldesa
        DB::table('users')->updateOrInsert(
            ['cedula' => 'V-00000001'],
            [
                'id' => (string) Str::uuid(),
                'role_id' => $roleAlcaldesa,
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
                'email_verified_at' => now(),
            ]
        );

        // 4. Usuario Director (Dirección de Ambiente)
        DB::table('users')->updateOrInsert(
            ['cedula' => 'V-87654321'],
            [
                'id' => (string) Str::uuid(),
                'role_id' => $roleDirector,
                'direccion_id' => $dirAmbiente,
                'telefono' => '04149876543',
                'nombre' => 'Carlos',
                'apellido' => 'Mendoza',
                'correo' => 'director@ambiente.com',
                'password' => Hash::make('12345678'),
                'accepted_terms' => true,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
                'email_verified_at' => now(),
            ]
        );

        // 5. Usuario Director (Dirección de Residuos)
        DB::table('users')->updateOrInsert(
            ['cedula' => 'V-98765432'],
            [
                'id' => (string) Str::uuid(),
                'role_id' => $roleDirector,
                'direccion_id' => $dirResiduos,
                'telefono' => '04129876943',
                'nombre' => 'Simon',
                'apellido' => 'Bolivar',
                'correo' => 'director@residuos.com',
                'password' => Hash::make('12345678'),
                'accepted_terms' => true,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
                'email_verified_at' => now(),
            ]
        );

        // 6. Usuario Inspector
        DB::table('users')->updateOrInsert(
            ['cedula' => 'V-11223344'],
            [
                'id' => (string) Str::uuid(),
                'role_id' => $roleInspector,
                'direccion_id' => $dirAmbiente,
                'telefono' => '04241112233',
                'nombre' => 'Roberto',
                'apellido' => 'Gómez',
                'correo' => 'inspector@ambiente.com',
                'password' => Hash::make('12345678'),
                'accepted_terms' => true,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
                'email_verified_at' => now(),
            ]
        );

        // 7. Registro en credenciales_inspector
        $inspectorUser = DB::table('users')->where('cedula', 'V-11223344')->first();

        if ($inspectorUser) {
            DB::table('credenciales_inspector')->updateOrInsert(
                ['user_id' => $inspectorUser->id],
                [
                    'codigo_credencial' => 'INS-2026-001',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}