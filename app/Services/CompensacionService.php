<?php

namespace App\Services;

class CompensacionService
{
    /**
     * Calcula la cantidad de ejemplares requeridos para compensación (Ratio fijo 1x8).
     */
    public static function calcularEjemplares(int $cantidadTalar, int $ratioBase = 8): int
    {
        return $cantidadTalar * $ratioBase;
    }
}