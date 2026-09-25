<?php

use Illuminate\Support\Facades\Route;

// Controladores Generales
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InspeccionController;

// Controladores Ciudadano
use App\Http\Controllers\Ciudadano\PerfilController;
use App\Http\Controllers\Ciudadano\SolicitudController;
use App\Http\Controllers\Ciudadano\ReporteController;
use App\Http\Controllers\Ciudadano\EmpresaController as CiudadanoEmpresaController;
use App\Http\Controllers\Ciudadano\CompensacionController;

// Controladores Director por Dirección
use App\Http\Controllers\Director\Ambiente\InspectorController as AmbienteInspectorController;
use App\Http\Controllers\Director\Ambiente\ReporteController as AmbienteReporteController;
use App\Http\Controllers\Director\Ambiente\SolicitudController as AmbienteSolicitudController;
use App\Http\Controllers\Director\Ambiente\AmbienteDashboardController;
use App\Http\Controllers\Director\Residuos\EmpresaController as ResiduosEmpresaController;

// -------------------------------------------------------------
// Rutas Públicas (Autenticación)
// -------------------------------------------------------------
Route::get('/', [AuthController::class, 'showHome'])->name('home');

// Protección contra fuerza bruta en inicio de sesión y registro (6 intentos por minuto)
Route::middleware(['guest', 'throttle:6,1'])->group(function () {
    Route::post('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/register', [AuthController::class, 'register'])->name('register');
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// -------------------------------------------------------------
// Rutas Autenticadas (General / Despachador)
// -------------------------------------------------------------
Route::middleware(['auth'])->group(function () {
    // Despachador principal único: evalúa el rol y redirige según corresponda
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Expedientes y Documentos compartidos
    Route::get('/empresas/{empresa}', [CiudadanoEmpresaController::class, 'show'])->name('empresas.show');
    Route::get('/expedientes/documentos/{documento}', [CiudadanoEmpresaController::class, 'verDocumento'])->name('expedientes.documento.ver');
});

// -------------------------------------------------------------
// Panel Exclusivo del Ciudadano
// -------------------------------------------------------------
Route::middleware(['auth', 'role:ciudadano'])->prefix('ciudadano')->name('ciudadano.')->group(function () {
    Route::get('/dashboard', function () {
        return view('ciudadano.dashboard', ['user' => auth()->user()]);
    })->name('dashboard');
    
    // Perfil
    Route::get('/perfil', [PerfilController::class, 'edit'])->name('perfil');
    Route::put('/perfil', [PerfilController::class, 'update'])->name('perfil.update');

    // Módulos con protección contra doble envío (envíos de formularios restringidos a 10 por minuto)
    Route::middleware(['throttle:10,1'])->group(function () {
        // Reportes (Denuncias)
        Route::post('/reportes', [ReporteController::class, 'store'])->name('reportes.store');

        // Solicitudes y Trámites
        Route::post('/solicitudes', [SolicitudController::class, 'store'])->name('solicitudes.store');
        Route::put('/solicitudes/{tramite}/pendientes', [SolicitudController::class, 'subirPendientes'])->name('solicitudes.pendientes');

        // Empresas (Perfil Comercial / RACDA)
        Route::post('/empresas', [CiudadanoEmpresaController::class, 'store'])->name('empresas.store');

        // Compensación Ambiental
        Route::post('/compensacion', [CompensacionController::class, 'store'])->name('compensacion.store');
    });

    // Consultas de vista
    Route::get('/reportes', [ReporteController::class, 'index'])->name('reportes');
    Route::get('/solicitudes', [SolicitudController::class, 'index'])->name('solicitudes');
    Route::get('/empresas', [CiudadanoEmpresaController::class, 'index'])->name('empresas');

    // Compensación Ambiental y Soporte
    Route::get('/compensacion', [CompensacionController::class, 'index'])->name('compensacion');
    Route::view('/soporte', 'soporte')->name('soporte');
});

// -------------------------------------------------------------
// Panel Directores
// -------------------------------------------------------------
Route::middleware(['auth', 'role:director,admin'])->prefix('director')->name('director.')->group(function () {
    
    // ---------------------------------------------------------
    // Dirección de Ambiente
    // ---------------------------------------------------------
    Route::prefix('ambiente')->name('ambiente.')->group(function () {
        // Dashboard principal
        Route::get('/dashboard', [AmbienteDashboardController::class, 'index'])->name('dashboard');
        
        // Gestión de Inspectores
        Route::get('/inspectores', [AmbienteInspectorController::class, 'index'])->name('inspectores');
        Route::post('/inspectores', [AmbienteInspectorController::class, 'store'])->name('inspectores.store');
        Route::patch('/inspectores/{inspector}/toggle', [AmbienteInspectorController::class, 'toggleStatus'])->name('inspectores.toggle');

        // Solicitudes (Trámites Ambientales)
        Route::get('/solicitudes', [AmbienteSolicitudController::class, 'index'])->name('solicitudes');
        Route::get('/solicitudes/exportar', [AmbienteSolicitudController::class, 'export'])->middleware('throttle:15,1')->name('solicitudes.exportar');
        Route::put('/solicitudes/{solicitud}/asignar', [AmbienteSolicitudController::class, 'asignarInspector'])->name('solicitudes.asignar');
        Route::get('/solicitudes/{solicitud}', [AmbienteSolicitudController::class, 'show'])->name('solicitudes.show');

        // Denuncias / Reportes
        Route::get('/reportes', [AmbienteReporteController::class, 'index'])->name('reportes');
        Route::get('/reportes/exportar', [AmbienteReporteController::class, 'export'])->middleware('throttle:15,1')->name('reportes.export');
        Route::get('/denuncias/{id}', [AmbienteReporteController::class, 'show'])->name('denuncias.show');
        Route::post('/denuncias/{id}/asignar', [AmbienteReporteController::class, 'asignarInspector'])->name('denuncias.asignar');
        Route::put('/reportes/{denuncia}/asignar', [AmbienteReporteController::class, 'asignar'])->name('reportes.asignar');
    });

    // ---------------------------------------------------------
    // Dirección de Manejo Integral de Residuos
    // ---------------------------------------------------------
    Route::prefix('residuos')->name('residuos.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'dashboardResiduos'])->name('dashboard');
        
        // Fiscalización y Expedientes de Empresas
        Route::get('/empresas', [ResiduosEmpresaController::class, 'index'])->name('empresas');
        Route::get('/empresas/{empresa}', [ResiduosEmpresaController::class, 'show'])->name('empresas.show');
        Route::patch('/empresas/{empresa}/estatus', [ResiduosEmpresaController::class, 'updateEstatus'])->name('empresas.updateEstatus');
    });

    // Soporte Director
    Route::view('/soporte', 'soporte')->name('soporte');
});

// -------------------------------------------------------------
// Panel Unificado de Inspectores
// -------------------------------------------------------------
Route::middleware(['auth', 'role:inspector'])->prefix('inspector')->name('inspector.')->group(function () {
    // Dashboard principal del inspector
    Route::get('/dashboard', function () {
        return view('inspector.dashboard', ['user' => auth()->user()]);
    })->name('dashboard');

    Route::view('/soporte', 'soporte')->name('soporte');
});

// -------------------------------------------------------------
// Panel Administrador
// -------------------------------------------------------------
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard', ['user' => auth()->user()]);
    })->name('dashboard');
    
    Route::view('/soporte', 'soporte')->name('soporte');
});

// -------------------------------------------------------------
// Módulo Compartido de Inspecciones (Directores e Inspectores)
// -------------------------------------------------------------
Route::middleware(['auth', 'role:director,inspector,admin'])->group(function () {
    // 1. Listado principal
    Route::get('/inspecciones', [InspeccionController::class, 'index'])->name('inspecciones.index');

    // Modificaciones / Envíos con límite de tasa de 10 peticiones/minuto
    Route::middleware(['throttle:10,1'])->group(function () {
        // 2. Guardar el informe / evaluación (POST)
        Route::post('/inspecciones', [InspeccionController::class, 'store'])->name('inspecciones.store');

        // 3. Iniciar inspección
        Route::patch('/inspecciones/{id}/iniciar-revision', [InspeccionController::class, 'iniciarRevision'])
            ->name('inspecciones.iniciar-revision');

        // 6. Actualizar inspección existente (PUT)
        Route::put('/inspecciones/{id}', [InspeccionController::class, 'update'])->name('inspecciones.update');
    });

    // 4. Formulario para evaluar / levantar acta (GET)
    Route::get('/inspecciones/{id}/evaluar', [InspeccionController::class, 'edit'])->name('inspecciones.evaluar');

    // 5. Ver detalles de una inspección terminada (GET)
    Route::get('/inspecciones/{id}', [InspeccionController::class, 'show'])->name('inspecciones.show');
});