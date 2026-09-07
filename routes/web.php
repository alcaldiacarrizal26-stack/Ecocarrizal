<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Ciudadano\PerfilController;
use App\Http\Controllers\Ciudadano\SolicitudController;
use App\Http\Controllers\Ciudadano\ReporteController;

// Importaciones con alias para evitar conflictos
use App\Http\Controllers\Ciudadano\EmpresaController as CiudadanoEmpresaController;
use App\Http\Controllers\Director\EmpresaController as DirectorEmpresaController;
use Illuminate\Support\Facades\Route;

// -------------------------------------------------------------
// Rutas Públicas (Autenticación)
// -------------------------------------------------------------
Route::get('/', [AuthController::class, 'showHome'])->name('home');
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// -------------------------------------------------------------
// Rutas Autenticadas (General / Despachador)
// -------------------------------------------------------------
Route::middleware(['auth'])->group(function () {
    
    // Despachador principal según rol
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Expedientes y Documentos compartidos
    Route::get('/empresas/{empresa}', [CiudadanoEmpresaController::class, 'show'])->name('empresas.show');
    Route::get('/expedientes/documentos/{documento}', [CiudadanoEmpresaController::class, 'verDocumento'])->name('expedientes.documento.ver');
});

// -------------------------------------------------------------
// Panel Exclusivo del Ciudadano
// -------------------------------------------------------------
Route::middleware(['auth', 'role:ciudadano'])->prefix('ciudadano')->name('ciudadano.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Perfil
    Route::get('/perfil', [PerfilController::class, 'edit'])->name('perfil');
    Route::put('/perfil', [PerfilController::class, 'update'])->name('perfil.update');

    // Reportes
    Route::get('/reportes', [ReporteController::class, 'index'])->name('reportes');
    Route::post('/reportes', [ReporteController::class, 'store'])->name('reportes.store');

    // Solicitudes y Trámites (Apuntando a la vista solicitudes.blade.php)
    Route::get('/solicitudes', [SolicitudController::class, 'index'])->name('solicitudes');
    Route::post('/solicitudes', [SolicitudController::class, 'store'])->name('solicitudes.store');
    Route::put('/solicitudes/{tramite}/pendientes', [SolicitudController::class, 'subirPendientes'])->name('solicitudes.pendientes');

    // Empresas (Ciudadano)
    Route::get('/empresas', [CiudadanoEmpresaController::class, 'index'])->name('empresas');
    Route::post('/empresas', [CiudadanoEmpresaController::class, 'store'])->name('empresas.store');
});

// -------------------------------------------------------------
// Panel Directores (Residuos / Ambiente)
// -------------------------------------------------------------
Route::middleware(['auth', 'role:director'])->prefix('director')->name('director.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Lista de empresas
    Route::get('/residuos/empresas', [DirectorEmpresaController::class, 'indexDirector'])->name('residuos.empresas');
    
    // Ver detalle del expediente (Vista Director)
    Route::get('/residuos/empresas/{empresa}', [DirectorEmpresaController::class, 'show'])->name('residuos.empresas.show');
    
    // Dictamen
    Route::post('/residuos/empresas/{empresa}/evaluar', [DirectorEmpresaController::class, 'evaluarEmpresa'])->name('residuos.empresas.evaluar');
});

// -------------------------------------------------------------
// Panel Inspectores
// -------------------------------------------------------------
Route::middleware(['auth', 'role:inspector'])->prefix('inspector')->name('inspector.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});

// -------------------------------------------------------------
// Panel Administrador
// -------------------------------------------------------------
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});