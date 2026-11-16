<?php

namespace App\Services;

use App\Models\Territorio;
use App\Models\Sector;

class TerritorioService
{
    /**
     * Retorna la estructura oficial de Comunas y Sectores desde la BD.
     */
    public static function getTerritorios(): array
    {
        return Territorio::with('sectores')->get()->mapWithKeys(function ($territorio) {
            return [$territorio->name => $territorio->sectores->pluck('name')->toArray()];
        })->toArray();
    }

    /**
     * Compara y verifica si un sector existe dentro de una comuna específica en la BD.
     */
    public static function verificarExistencia(string $territorioName, string $sectorName): bool
    {
        return Sector::where('name', $sectorName)
            ->whereHas('territorio', function ($query) use ($territorioName) {
                $query->where('name', $territorioName);
            })->exists();
    }
}