<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            // 1. Catálogos base independientes
            RoleSeeder::class,
            DireccionSeeder::class,
            TerritorioSeeder::class,
            RequisitoEmpresaSeeder::class,
            
            // 2. Catálogos dependientes (Trámites -> Recaudos)
            TipoTramiteYRecaudoSeeder::class,
            
            // 3. Datos de prueba (Usuarios al final)
            UserSeeder::class,
        ]);
    }
}