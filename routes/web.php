<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Ciudadano\DashboardController;
use App\Http\Controllers\Ciudadano\PerfilController;
use App\Http\Controllers\Ciudadano\TramiteController;
use App\Http\Controllers\Ciudadano\ReporteController;
use App\Http\Controllers\Ciudadano\EmpresaController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DocumentoController;

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

        // Rutas de Solicitudes
    Route::get('/solicitudes', [ReporteController::class, 'index'])->name('solicitudes');
    Route::post('/solicitudes', [ReporteController::class, 'store'])->name('solicitudes.store');

        // Rutas de Empresas
    Route::get('/empresas', [EmpresaController::class, 'index'])->name('empresas');
    Route::post('/empresas', [EmpresaController::class, 'store'])->name('empresas.store');

    Route::resource('tramites', TramiteController::class)->except(['destroy']);
});

Route::middleware(['auth'])->group(function () {
    Route::get('/documentos/{documento}', [DocumentoController::class, 'show'])->name('documentos.show');
});