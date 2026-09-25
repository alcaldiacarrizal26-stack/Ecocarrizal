<?php

namespace App\Services;

class CompensacionService
{
    // Especies bajo veda o protección especial en Venezuela
    public const ESPECIES_PROTEGIDAS = [
        'araguaney',
        'ceiba',
        'caoba',
        'cedro',
        'saman',
        'pino_lasso',
        'apamate'
    ];

    /**
     * Calcula la cantidad de ejemplares requeridos para compensación.
     */
    public static function calcularEjemplares(
        int $cantidadTalar,
        bool $esEspecieProtegida = false,
        int $ratioBase = 8
    ): int {
        $multiplicador = $esEspecieProtegida ? 2 : 1;
        return $cantidadTalar * $ratioBase * $multiplicador;
    }
}