<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            DireccionSeeder::class,
            // UserSeeder::class,
            RequisitoEmpresaSeeder::class,
            TerritorioSeeder::class,
            TipoTramiteYRecaudoSeeder::class,
        ]);
    }
}