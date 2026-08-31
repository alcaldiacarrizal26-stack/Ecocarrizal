<?php

namespace App\Services;

class TerritorioService
{
    /**
     * Retorna la estructura oficial de Comunas/Circuitos y Sectores de Carrizal.
     */
    public static function getTerritorios(): array
    {
        return [
            'Circuito 1' => [
                'Potrerito 2', 'Barola', 'Los Hidalgos', 'El Sitio', 'Las Américas',
                'Santa Eduviges', 'Potrerito 1', 'Casco Central', 'Terrazas del Trigo',
                'Las Aguaditas y Res. Agua Linda', 'Lomas de Paramacay'
            ],
            'Circuito 2' => [
                'Santa Bárbara', 'Doña Josefa', 'El Tanque', 'Calle El Carmen', 'Ezequiel Zamora',
                'Amigos Reunidos', 'Calle Mérida', 'Doña Josefina', 'El Trigo', 'Federación', 
                'Principal', 'La Cruz', 'Brisas de la Colina', 'Santa Isabel Parte Baja', 
                'Urb. Montañalta', 'Colinas de Carrizal', 'Pan de Azúcar'
            ],
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
    }
}