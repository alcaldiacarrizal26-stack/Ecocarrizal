<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Ciudadano\DashboardController;
use App\Http\Controllers\Ciudadano\PerfilController;
use App\Http\Controllers\Ciudadano\TramiteController;
use App\Http\Controllers\Ciudadano\ReporteController;
use Illuminate\Support\Facades\Route;

// Vista principal
Route::get('/', [AuthController::class, 'showHome'])->name('home');

// Rutas de autenticación
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth', 'role:ciudadano'])->prefix('ciudadano')->name('ciudadano.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Rutas de Perfil
    Route::get('/perfil', [PerfilController::class, 'edit'])->name('perfil');
    Route::put('/perfil', [PerfilController::class, 'update'])->name('perfil.update');

    // Rutas de Reportes y Denuncias
    Route::get('/reportes', [ReporteController::class, 'index'])->name('reportes');
    Route::post('/reportes', [ReporteController::class, 'store'])->name('reportes.store');

        // Rutas de Reportes y Denuncias
    Route::get('/solicitudes', [ReporteController::class, 'index'])->name('solicitudes');
    Route::post('/solicitudes', [ReporteController::class, 'store'])->name('solicitudes.store');

    Route::resource('tramites', TramiteController::class)->except(['destroy']);
});