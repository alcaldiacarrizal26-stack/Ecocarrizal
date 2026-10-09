<?php

use Illuminate\Support\Facades\Route;

//Modelos Utilizados
use App\Models\Inspector;

// Controladores Generales
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InspeccionController;

// Controladores Ciudadano
use App\Http\Controllers\Ciudadano\PerfilController;
use App\Http\Controllers\Ciudadano\SolicitudController;
use App\Http\Controllers\Ciudadano\ReporteController;
use App\Http\Controllers\Ciudadano\EmpresaController as CiudadanoEmpresaController;
use App\Http\Controllers\Ciudadano\DeclaracionResiduoController; 
use App\Http\Controllers\Ciudadano\CompensacionController;

// Controladores Director por Dirección
use App\Http\Controllers\Director\Ambiente\InspectorController as AmbienteInspectorController;
use App\Http\Controllers\Director\Ambiente\ReporteController as AmbienteReporteController;
use App\Http\Controllers\Director\Ambiente\SolicitudController as AmbienteSolicitudController;
use App\Http\Controllers\Director\Ambiente\CompensacionController as AmbienteCompensacionController;
use App\Http\Controllers\Director\Ambiente\AmbienteDashboardController;
use App\Http\Controllers\Director\Residuos\EmpresaController as ResiduosEmpresaController;
use App\Http\Controllers\Director\Residuos\DeclaracionResiduoController as ResiduosDeclaracionResiduoController;

// -------------------------------------------------------------
// Rutas Públicas (Autenticación)
// -------------------------------------------------------------
Route::get('/', [AuthController::class, 'showHome'])->name('home');

// Protección contra fuerza bruta en inicio de sesión y registro
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

    // Módulos con protección contra doble envío / spam
    Route::middleware(['throttle:10,1'])->group(function () {
        Route::post('/reportes', [ReporteController::class, 'store'])->name('reportes.store');
        Route::post('/solicitudes', [SolicitudController::class, 'store'])->name('solicitudes.store');
        Route::put('/solicitudes/{tramite}/pendientes', [SolicitudController::class, 'subirPendientes'])->name('solicitudes.pendientes');
        Route::post('/empresas', [CiudadanoEmpresaController::class, 'store'])->name('empresas.store');
        Route::put('/compensacion/{compensacion}', [CompensacionController::class, 'store'])->name('compensacion.store');
        
        // POST de Declaraciones protegido
        Route::post('/declaraciones', [DeclaracionResiduoController::class, 'store'])->name('declaraciones.store');
    });

    // Consultas de vista
    Route::get('/reportes', [ReporteController::class, 'index'])->name('reportes');
    Route::get('/solicitudes', [SolicitudController::class, 'index'])->name('solicitudes');
    Route::get('/empresas', [CiudadanoEmpresaController::class, 'index'])->name('empresas');
    Route::get('/empresas/crear', [CiudadanoEmpresaController::class, 'create'])->name('empresas.create');

    // Vistas de Declaraciones de Residuos
    Route::get('/declaraciones', [DeclaracionResiduoController::class, 'index'])->name('declaraciones.index');
    Route::get('/declaraciones/nueva', [DeclaracionResiduoController::class, 'create'])->name('declaraciones.create');

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

        // Compensación Ambiental
        Route::get('/compensacion', [AmbienteCompensacionController::class, 'index'])->name('compensacion');
        Route::post('/compensacion/entregas/{entrega}/evaluar', [AmbienteCompensacionController::class, 'evaluarEntrega'])->name('compensacion.evaluar');
    });

    // ---------------------------------------------------------
    // Dirección de Manejo Integral de Residuos
    // ---------------------------------------------------------
    Route::prefix('residuos')->name('residuos.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'dashboardResiduos'])->name('dashboard');
        
        // Módulo Autónomo de Declaraciones de Residuos
        Route::prefix('declaraciones')->name('declaraciones.')->group(function () {
            Route::get('/', [ResiduosDeclaracionResiduoController::class, 'index'])->name('index');
            Route::get('/{declaracion}', [ResiduosDeclaracionResiduoController::class, 'show'])->name('show');
            Route::patch('/{declaracion}/estatus', [ResiduosDeclaracionResiduoController::class, 'updateEstatus'])->name('updateEstatus');
        });

        // Módulo de Gestión de Empresas
        Route::prefix('empresas')->name('empresas.')->group(function () {
            Route::get('/', [ResiduosEmpresaController::class, 'index'])->name('index');
            Route::get('/{empresa}', [ResiduosEmpresaController::class, 'show'])->name('show');
            Route::post('/{empresa}/evaluar', [ResiduosEmpresaController::class, 'evaluar'])->name('evaluar');
            Route::patch('/{empresa}/estatus', [ResiduosEmpresaController::class, 'updateEstatus'])->name('updateEstatus');

            // Fiscalización e Inspecciones
            Route::get('/{empresa}/inspecciones', [ResiduosEmpresaController::class, 'inspecciones'])->name('inspecciones.index');
            Route::post('/{empresa}/inspecciones', [ResiduosEmpresaController::class, 'asignarInspeccion'])->name('inspecciones.store');

            // Monitoreo de Declaraciones por Empresa
            Route::get('/{empresa}/declaraciones', [ResiduosEmpresaController::class, 'declaraciones'])->name('declaraciones');

            // Citaciones y Sanciones Administrativas
            Route::get('/{empresa}/sanciones', [ResiduosEmpresaController::class, 'sanciones'])->name('sanciones.index'); // <-- RUTA FALTANTE AGREGADA
            Route::post('/{empresa}/sanciones', [ResiduosEmpresaController::class, 'asignarSancion'])->name('sanciones.store');
            Route::patch('/sanciones/{sancion}/resolver', [ResiduosEmpresaController::class, 'resolverSancion'])->name('sanciones.resolver');
        });
    });

    // Soporte Director
    Route::view('/soporte', 'soporte')->name('soporte');
});

// -------------------------------------------------------------
// Panel Unificado de Inspectores
// -------------------------------------------------------------
Route::middleware(['auth', 'role:inspector'])->prefix('inspector')->name('inspector.')->group(function () {
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
// Módulo Compartido de Inspecciones (Directores, Inspectores y Admins)
// -------------------------------------------------------------
Route::middleware(['auth', 'role:director,inspector,admin'])->group(function () {
    Route::get('/inspecciones', [InspeccionController::class, 'index'])->name('inspecciones.index');

    Route::middleware(['throttle:10,1'])->group(function () {
        Route::post('/inspecciones', [InspeccionController::class, 'store'])->name('inspecciones.store');
        Route::patch('/inspecciones/{id}/iniciar-revision', [InspeccionController::class, 'iniciarRevision'])->name('inspecciones.iniciar-revision');
        Route::put('/inspecciones/{id}', [InspeccionController::class, 'update'])->name('inspecciones.update');
    });

    Route::get('/inspecciones/{id}/evaluar', [InspeccionController::class, 'edit'])->name('inspecciones.evaluar');
    Route::get('/inspecciones/{id}', [InspeccionController::class, 'show'])->name('inspecciones.show');
});

// -------------------------------------------------------------
// Backend API para inspectores y directores disponibles
// -------------------------------------------------------------
Route::middleware(['auth', 'role:director,admin'])->group(function () {
    Route::get('/api/inspectores/disponibles', function (\Illuminate\Http\Request $request) {
        try {
            // Traemos los funcionarios (Directores e Inspectores) activos con su relación
            $funcionarios = \App\Models\User::whereHas('role', function ($q) {
                    $q->whereIn('nombre', ['director', 'inspector']);
                })
                ->where('is_active', true)
                ->with(['role', 'direccion', 'inspector'])
                ->get();

            $resultado = $funcionarios->map(function ($user) {
                $nombreCompleto = trim(($user->nombre ?? '') . ' ' . ($user->apellido ?? ''));
                $rolNombre = ucfirst($user->role->nombre ?? 'Funcionario');

                return [
                    'id' => $user->id,
                    'nombre' => $nombreCompleto ?: 'Usuario #' . $user->id,
                    'cedula' => $user->cedula ?? 'S/C', // <-- Mapeo exacto de la columna 'cedula' de users
                    'credencial' => $user->inspector->codigo_credencial ?? $rolNombre,
                    'especialidad' => $user->inspector->especialidad ?? ($user->direccion->nombre_direc ?? $rolNombre),
                    'rol' => $rolNombre,
                ];
            });

            return response()->json($resultado, 200);

        } catch (\Throwable $th) {
            return response()->json([
                'error' => 'Error al obtener personal disponible',
                'detalle' => $th->getMessage(),
                'linea' => $th->getLine(),
                'archivo' => $th->getFile()
            ], 500);
        }
    })->name('api.inspectores.disponibles');
});