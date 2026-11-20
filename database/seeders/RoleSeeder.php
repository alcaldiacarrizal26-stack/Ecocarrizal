<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('roles')->insertOrIgnore([
            ['id' => 1, 'nombre' => 'admin', 'descripcion' => 'Administrador del sistema', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'nombre' => 'director', 'descripcion' => 'Director de línea', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'nombre' => 'inspector', 'descripcion' => 'Inspector ambiental', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4, 'nombre' => 'ciudadano', 'descripcion' => 'Ciudadano / Usuario externo', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 5, 'nombre' => 'alcaldesa', 'descripcion' => 'Despacho Ejecutivo / Alcaldesa', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}