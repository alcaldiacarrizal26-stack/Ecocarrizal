<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Ciudadano\DashboardController;
use App\Http\Controllers\Ciudadano\PerfilController;
use App\Http\Controllers\Ciudadano\TramiteController;
use App\Http\Controllers\Ciudadano\ReporteController;
use App\Http\Controllers\Ciudadano\EmpresaController;
use Illuminate\Support\Facades\Route;

// Vista principal y autenticación
Route::get('/', [AuthController::class, 'showHome'])->name('home');
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// -------------------------------------------------------------
// Rutas compartidas (Ciudadano, Director, Fiscal, Admin)
// -------------------------------------------------------------
Route::middleware(['auth'])->group(function () {
    // Ver el detalle del expediente (retorna ciudadano/empresa.blade.php)
    Route::get('/empresas/{empresa}', [EmpresaController::class, 'show'])
        ->name('empresas.show');

    // Visualizar / Descargar documentos privados
    Route::get('/expedientes/documentos/{documento}', [EmpresaController::class, 'verDocumento'])
        ->name('expedientes.documento.ver');
});

// -------------------------------------------------------------
// Panel Exclusivo del Ciudadano
// -------------------------------------------------------------
Route::middleware(['auth', 'role:ciudadano'])->prefix('ciudadano')->name('ciudadano.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Perfil
    Route::get('/perfil', [PerfilController::class, 'edit'])->name('perfil');
    Route::put('/perfil', [PerfilController::class, 'update'])->name('perfil.update');

    // Reportes y Solicitudes
    Route::get('/reportes', [ReporteController::class, 'index'])->name('reportes');
    Route::post('/reportes', [ReporteController::class, 'store'])->name('reportes.store');

    Route::get('/solicitudes', [ReporteController::class, 'index'])->name('solicitudes');
    Route::post('/solicitudes', [ReporteController::class, 'store'])->name('solicitudes.store');

    // Registro y Listado de Empresas
    Route::get('/empresas', [EmpresaController::class, 'index'])->name('empresas');
    Route::post('/empresas', [EmpresaController::class, 'store'])->name('empresas.store');

    Route::resource('tramites', TramiteController::class)->except(['destroy']);
});