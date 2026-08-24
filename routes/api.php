<?php

use App\Http\Controllers\Api\SolicitudController;
use App\Http\Controllers\Api\DenunciaController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {

    // Accesible por cualquier usuario autenticado
    Route::post('/denuncias', [DenunciaController::class, 'store']);

    // Solo accesible por Administrador o Director
    Route::middleware('role:administrador,director')->group(function () {
        Route::get('/solicitudes/gestion', [SolicitudController::class, 'index']);
        Route::patch('/solicitudes/{id}/estado', [SolicitudController::class, 'updateStatus']);
    });

    // Solo accesible por Inspectores/Fiscales
    Route::middleware('role:inspector,fiscal')->group(function () {
        Route::post('/solicitudes/{id}/inspeccion', [SolicitudController::class, 'registrarInspeccion']);
    });
});