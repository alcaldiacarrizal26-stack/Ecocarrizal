<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DireccionSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('direcciones')->insertOrIgnore([
            ['id_direccion' => 1, 'nombre_direc' => 'Dirección de Ambiente', 'descripcion_direc' => 'Control Ambiental', 'created_at' => now()],
            ['id_direccion' => 2, 'nombre_direc' => 'Dirección de Residuos', 'descripcion_direc' => 'Control Residual de las empresas', 'created_at' => now()],
        ]);
    }
}