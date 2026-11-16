<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Territorio;
use App\Models\Sector;

class TerritorioSeeder extends Seeder
{
    public function run(): void
    {
        $comunas = [
            'Comuna Árbol de las 4 Raíces' => [
                'Pasatiempo', 'Simón Bolívar', 'Villa Fresca', 'Villa Josefina', 'Yerba Buena',
                'La Esperanza', 'Gran Colombia', 'Los Vecinos', 'Los Pozotes'
            ],
            'Comuna Brisas de Oriente' => [
                'Entrada Principal', 'Subida a la Cuadra', 'La Cruz', 'Callejón 1ro de Mayo',
                'Bajando por la Cancha', 'Tara', 'Las Margaritas', 'Residencias Cascada Mágica',
                'Makro', 'La Campana', 'Los Cerritos', 'C.C. Líder'
            ],
            'Comuna Francisco de Miranda' => [
                'Francisco de Miranda Parte Alta', 'Francisco de Miranda Parte Baja', 'Bertha Mujica',
                'Potrero del Medio', 'Lomas de Urquía', 'Los Martínez', 'Guadalupe Parte Baja',
                'Guadalupe Parte Alta', 'El Samán', 'Los Mujica', 'La Carbonera', 'Santa Isabel',
                'Aurora', 'Colinas del Bosque'
            ],
            'Comuna La Ladera' => [
                'Zona de la Entrada (Arriba)', 'Zona Central', 'Parte Abajo'
            ],
            'Comuna Llano Alto' => [
                'Los Budares Casas', 'Los Budares Edificios', 'Conj. Res. Montebello',
                'Las Villas', 'Los Eucaliptos', 'Los Alcores', 'El Carmen', 'San Francisco',
                'Cocoli', 'Monte Bello Country', 'Res. Monterrey', 'Res. Flamboyan',
                'Res. Ébano', 'Res. Dracenia - Cayena', 'Res. Excelsior', 'Res. Las Galas',
                'Res. Los Jardines', 'Res. Sausalito', 'Res. Los Pinos', 'Res. Alta Vista',
                'Res. Vista del Valle', 'Res. Latino Horizonte'
            ]
        ];

        foreach ($comunas as $nombreComuna => $sectores) {
            $territorio = Territorio::updateOrCreate(
                ['name' => $nombreComuna]
            );

            foreach ($sectores as $sectorName) {
                Sector::firstOrCreate([
                    'territorio_id' => $territorio->id,
                    'name' => $sectorName
                ]);
            }
        }
    }
}