<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

// Modelos Utilizados
use App\Models\Inspector;
use App\Models\User;

// Controladores Generales
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InspeccionController;
use App\Http\Controllers\PerfilController;
use App\Http\Controllers\SoporteController;

// Controladores Ciudadano
use App\Http\Controllers\Ciudadano\SolicitudController;
use App\Http\Controllers\Ciudadano\DenunciaController;
use App\Http\Controllers\Ciudadano\EmpresaController as CiudadanoEmpresaController;
use App\Http\Controllers\Ciudadano\DeclaracionResiduoController; 
use App\Http\Controllers\Ciudadano\CompensacionController;

// Controladores Director por Dirección
use App\Http\Controllers\Director\Ambiente\InspectorController as AmbienteInspectorController;
use App\Http\Controllers\Director\Ambiente\DenunciaController as AmbienteDenunciaController;
use App\Http\Controllers\Director\Ambiente\SolicitudController as AmbienteSolicitudController;
use App\Http\Controllers\Director\Ambiente\CompensacionController as AmbienteCompensacionController;
use App\Http\Controllers\Director\Ambiente\AmbienteDashboardController;
use App\Http\Controllers\Director\Residuos\EmpresaController as ResiduosEmpresaController;
use App\Http\Controllers\Director\Residuos\DeclaracionResiduoController as ResiduosDeclaracionResiduoController;

// Controladores Alcaldesa
use App\Http\Controllers\Alcaldesa\ReporteController as AlcaldesaReporteController;

// Conroladores Admin
use App\Http\Controllers\Admin\AlmacenamientoController;
use App\Http\Controllers\Admin\BitacoraController;
use App\Http\Controllers\Admin\RespaldoController;
use App\Http\Controllers\Admin\UsuarioController;

// -------------------------------------------------------------
// Rutas Públicas (Autenticación por Vistas Dedicadas)
// -------------------------------------------------------------
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/marco-legal/pdf/{documento}', [HomeController::class, 'descargarMarcoLegalPdf'])->name('marco.legal.pdf');

Route::middleware(['guest'])->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:6,1');

    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:6,1');

    // Recuperación de Contraseña
    Route::get('/forgot-password', [ForgotPasswordController::class, 'create'])->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'store'])->name('password.email');
    
    Route::get('/reset-password/{token}', [ResetPasswordController::class, 'create'])->name('password.reset');
    Route::post('/reset-password', [ResetPasswordController::class, 'update'])->name('password.update');
});

// Vista de espera de verificación de correo
Route::get('/email/verify-notice', function () {
    if (auth()->check() && auth()->user()->hasVerifiedEmail()) {
        return redirect()->route('dashboard');
    }

    $correoPendiente = session('correo_pendiente');
    
    if (!$correoPendiente) {
        $ultimoUsuario = \App\Models\User::whereNull('email_verified_at')->latest()->first();$correoPendiente = $ultimoUsuario ? $ultimoUsuario->correo : '';
    }

    return view('auth.verify-notice', ['correoPendiente' => $correoPendiente]);
})->name('verification.notice');

Route::post('/email/update-notice', [\App\Http\Controllers\AuthController::class, 'updatePendingEmail'])->name('verification.update-email');

Route::get('/email/verify/{id}/{hash}', function (Request $request, $id,$hash) {
    $user = User::findOrFail($id);

    // Validar que el hash coincida con el correo del usuario
    if (!hash_equals((string) $hash, sha1($user->getEmailForPasswordReset()))) {
        abort(403, 'Enlace de verificación inválido.');
    }

    // Verificar si ya estaba verificado para evitar duplicar la fecha
    if (!$user->hasVerifiedEmail()) {$user->markEmailAsVerified();
    }

    return redirect()->route('login')->with('success', '¡Correo electrónico verificado exitosamente! Ya puedes iniciar sesión.');
})->middleware(['signed'])->name('verification.verify');

// -------------------------------------------------------------
// Rutas Autenticadas (General / Despachador)
// -------------------------------------------------------------
Route::middleware(['auth'])->group(function () {
    // Ruta para cerrar sesión
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Despachador principal único: evalúa el rol y redirige según corresponda
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Expedientes y Documentos compartidos
    Route::get('/empresas/{empresa}', [CiudadanoEmpresaController::class, 'show'])->name('empresas.show');
    Route::get('/expedientes/documentos/{documento}', [CiudadanoEmpresaController::class, 'verDocumento'])->name('expedientes.documento.ver');

    // Visor Seguro de Evidencias de Sanciones (Previene error 403 Forbidden)
    Route::get('/sanciones/evidencia/{path}', [ResiduosEmpresaController::class, 'verEvidencia'])
        ->where('path', '.*')
        ->name('sanciones.evidencia.ver');

    // Visor Seguro para Archivos Privados de Declaraciones (Disco Private)
    Route::get('/storage-view/{path}', function ($path) {
        $path = urldecode($path);
        $path = ltrim(str_replace(['..', '//'], '', $path), '/');
        
        // Verificamos si existe en el disco private o local
        if (!\Illuminate\Support\Facades\Storage::disk('private')->exists($path)) {
            // Intentamos respaldo en public por si acaso
            if (!\Illuminate\Support\Facades\Storage::disk('public')->exists($path)) {
                abort(404, 'El archivo o manifiesto solicitado no existe en el almacenamiento.');
            }
            return response()->file(storage_path('app/public/' . $path));
        }

        return response()->file(storage_path('app/private/' . $path));
    })->where('path', '.*')->name('storage.view');

    // Soporte Técnico General
    Route::get('/soporte', [SoporteController::class, 'index'])->name('soporte');
    Route::post('/soporte', [SoporteController::class, 'store'])->name('soporte.store');
    Route::get('/perfil', [PerfilController::class, 'show'])->name('perfil.show');
});

// -------------------------------------------------------------
// Panel Exclusivo del Ciudadano
// -------------------------------------------------------------
Route::middleware(['auth', 'role:ciudadano'])->prefix('ciudadano')->name('ciudadano.')->group(function () {
    Route::get('/dashboard', function () {
        return view('ciudadano.dashboard', ['user' => auth()->user()]);
    })->name('dashboard');

    // Módulos con protección contra doble envío / spam
    Route::middleware(['throttle:10,1'])->group(function () {
        Route::post('/denuncias', [DenunciaController::class, 'store'])->name('denuncias.store');
        Route::post('/solicitudes', [SolicitudController::class, 'store'])->name('solicitudes.store');
        Route::put('/solicitudes/{tramite}/pendientes', [SolicitudController::class, 'subirPendientes'])->name('solicitudes.pendientes');
        Route::post('/empresas', [CiudadanoEmpresaController::class, 'store'])->name('empresas.store');
        Route::put('/compensacion/{compensacion}', [CompensacionController::class, 'store'])->name('compensacion.store');
        Route::post('/compensacion/donar', [CompensacionController::class, 'donar'])->name('compensacion.donar');
        
        // POST de Declaraciones protegido
        Route::post('/declaraciones', [DeclaracionResiduoController::class, 'store'])->name('declaraciones.store');
    });

    // Consultas de vista
    Route::get('/denuncias', [DenunciaController::class, 'index'])->name('denuncias');
    Route::get('/solicitudes', [SolicitudController::class, 'index'])->name('solicitudes');
    Route::get('/empresas', [CiudadanoEmpresaController::class, 'index'])->name('empresas');
    Route::get('/empresas/crear', [CiudadanoEmpresaController::class, 'create'])->name('empresas.create');
    
    // Vista Dedicada de Sanciones para el Ciudadano y Renovación de Documentos
    Route::get('/empresas/sanciones', [CiudadanoEmpresaController::class, 'sancionesIndex'])->name('empresas.sanciones.index');
    Route::post('/empresas/documentos/{documento}/renovar', [CiudadanoEmpresaController::class, 'renovarDocumento'])->name('empresas.documentos.renovar');

    // Vistas de Declaraciones de Residuos
    Route::get('/declaraciones', [DeclaracionResiduoController::class, 'index'])->name('declaraciones.index');
    Route::get('/declaraciones/nueva', [DeclaracionResiduoController::class, 'create'])->name('declaraciones.create');

    // Compensación Ambiental y Soporte
    Route::get('/compensacion', [CompensacionController::class, 'index'])->name('compensacion');
    Route::get('/soporte', [SoporteController::class, 'index'])->name('soporte');
});

// -------------------------------------------------------------
// Panel Directores
// -------------------------------------------------------------

Route::middleware(['auth', 'role:director,admin'])->prefix('director')->name('director.')->group(function () {

    // Gestión de Inspectores (Compartida para Directores de Ambiente y Residuos)
    Route::get('/inspectores', [\App\Http\Controllers\Director\InspectorController::class, 'index'])->name('inspectores.index');
    Route::post('/inspectores', [\App\Http\Controllers\Director\InspectorController::class, 'store'])->name('inspectores.store');
    Route::put('/inspectores/{inspector}', [\App\Http\Controllers\Director\InspectorController::class, 'update'])->name('inspectores.update');
    Route::patch('/inspectores/{inspector}/toggle', [\App\Http\Controllers\Director\InspectorController::class, 'toggleStatus'])->name('inspectores.toggle');
    
    // ---------------------------------------------------------
    // Dirección de Ambiente
    // ---------------------------------------------------------
    Route::prefix('ambiente')->name('ambiente.')->group(function () {
        Route::get('/dashboard', [AmbienteDashboardController::class, 'index'])->name('dashboard');

        // Solicitudes (Trámites Ambientales)
        Route::get('/solicitudes', [AmbienteSolicitudController::class, 'index'])->name('solicitudes');
        Route::get('/solicitudes/exportar', [AmbienteSolicitudController::class, 'export'])->middleware('throttle:15,1')->name('solicitudes.exportar');
        Route::put('/solicitudes/{solicitud}/asignar', [AmbienteSolicitudController::class, 'asignarInspector'])->name('solicitudes.asignar');
        Route::get('/solicitudes/{solicitud}', [AmbienteSolicitudController::class, 'show'])->name('solicitudes.show');
        Route::put('/solicitudes/{solicitud}/rechazar', [AmbienteSolicitudController::class, 'rechazar'])->name('director.ambiente.solicitudes.rechazar');

        // Denuncias / Reportes
        Route::get('/denuncias', [AmbienteDenunciaController::class, 'index'])->name('denuncias');
        Route::get('/denuncias/exportar', [AmbienteDenunciaController::class, 'export'])->middleware('throttle:15,1')->name('denuncias.export');
        Route::get('/denuncias/{id}', [AmbienteDenunciaController::class, 'show'])->name('denuncias.show');
        Route::post('/denuncias/{id}/asignar', [AmbienteDenunciaController::class, 'asignarInspector'])->name('denuncias.asignar');
        Route::put('/denuncias/{denuncia}/asignar', [AmbienteDenunciaController::class, 'asignar'])->name('denuncias.asignar');
        Route::put('/denuncias/{denuncia}/rechazar', [AmbienteDenunciaController::class, 'rechazar'])->name('director.ambiente.denuncias.rechazar');

        // Compensación Ambiental
        Route::get('/compensacion', [AmbienteCompensacionController::class, 'index'])->name('compensacion');
        Route::post('/compensacion/entregas/{entrega}/evaluar', [AmbienteCompensacionController::class, 'evaluarEntrega'])->name('compensacion.evaluar');
    });

    // ---------------------------------------------------------
    // Dirección de Manejo Integral de Residuos
    // ---------------------------------------------------------
    Route::prefix('residuos')->name('residuos.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'dashboardResiduos'])->name('dashboard');
        
        Route::prefix('declaraciones')->name('declaraciones.')->group(function () {
            Route::get('/', [ResiduosDeclaracionResiduoController::class, 'index'])->name('index');
            Route::get('/{declaracion}', [ResiduosDeclaracionResiduoController::class, 'show'])->name('show');
            Route::match(['put', 'patch'], '/{declaracion}', [ResiduosDeclaracionResiduoController::class, 'updateEstatus'])->name('update');
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
            Route::delete('/inspecciones/{orden}/eliminar', [ResiduosEmpresaController::class, 'eliminarInspeccion'])->name('inspecciones.destroy');

            // Monitoreo de Declaraciones por Empresa
            Route::get('/{empresa}/declaraciones', [ResiduosEmpresaController::class, 'declaraciones'])->name('declaraciones');

            // Citaciones y Sanciones Administrativas
            Route::get('/{empresa}/sanciones', [ResiduosEmpresaController::class, 'sanciones'])->name('sanciones.index');
            Route::post('/{empresa}/sanciones', [ResiduosEmpresaController::class, 'asignarSancion'])->name('sanciones.store');
            Route::patch('/sanciones/{sancion}/resolver', [ResiduosEmpresaController::class, 'resolverSancion'])->name('sanciones.resolver');
        });
    });

    // Soporte Director
    Route::get('/soporte', [SoporteController::class, 'index'])->name('soporte');
});

// -------------------------------------------------------------
// Panel Unificado de Inspectores
// -------------------------------------------------------------
Route::middleware(['auth', 'role:inspector'])->prefix('inspector')->name('inspector.')->group(function () {
    Route::get('/dashboard', function () {
        return view('inspector.dashboard', ['user' => auth()->user()]);
    })->name('dashboard');

    Route::get('/soporte', [SoporteController::class, 'index'])->name('soporte');
});

// -------------------------------------------------------------
// Panel Administrador
// -------------------------------------------------------------
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', function () {
        return view('admin.dashboard', ['user' => auth()->user()]);
    })->name('dashboard');
    
    // Soporte Admin (Buzón general y respuestas)
    Route::get('/soporte', [SoporteController::class, 'index'])->name('soporte');
    Route::patch('/soporte/{id}/responder', [SoporteController::class, 'actualizarRespuesta'])->name('soporte.responder');

    Route::get('/almacenamiento', [AlmacenamientoController::class, 'index'])->name('almacenamiento');

    // Rutas de administración adicionales
    Route::get('/bitacora', [BitacoraController::class, 'index'])->name('bitacora');

    //Respaldos de Base de Datos
    Route::get('/respaldos', [RespaldoController::class, 'index'])->name('respaldos');
    Route::post('/respaldos', [RespaldoController::class, 'store'])->name('respaldos.store');
    Route::get('/respaldos/download', [RespaldoController::class, 'download'])->name('respaldos.download');
    Route::delete('/respaldos', [RespaldoController::class, 'destroy'])->name('respaldos.destroy');

    // Gestión de Usuarios
    Route::get('/usuarios', [UsuarioController::class, 'index'])->name('usuarios.index');
    Route::patch('/usuarios/{usuario}/toggle', [UsuarioController::class, 'toggleStatus'])->name('usuarios.toggle');
});

// -------------------------------------------------------------
// Panel Alcaldesa (Acceso a Reportes y Métricas)
// -------------------------------------------------------------
Route::middleware(['auth', 'role:alcaldesa,admin'])->prefix('alcaldesa')->name('alcaldesa.')->group(function () {
    Route::get('/reportes', [AlcaldesaReporteController::class, 'index'])->name('reportes');
    Route::get('/ambiente', [AlcaldesaReporteController::class, 'ambiente'])->name('ambiente');
    Route::get('/residuos', [AlcaldesaReporteController::class, 'residuos'])->name('residuos');
    
    // Rutas separadas e independientes para cada PDF
    Route::get('/residuos/pdf', [AlcaldesaReporteController::class, 'exportarResiduosPdf'])->name('residuos.pdf');
    Route::get('/empresas/pdf', [AlcaldesaReporteController::class, 'exportarEmpresasPdf'])->name('empresas.pdf');
    Route::get('/ambiente/pdf', [AlcaldesaReporteController::class, 'exportarAmbientePdf'])->name('ambiente.pdf');
    
    Route::get('/poder-popular', [AlcaldesaReporteController::class, 'poderPopular'])->name('poderPopular');
    Route::get('/poder-popular/pdf', [AlcaldesaReporteController::class, 'exportarPoderPopularPdf'])->name('poderPopular.pdf');
});

// -------------------------------------------------------------
// Módulo Compartido de Inspecciones (Directores, Inspectores y Admins)
// -------------------------------------------------------------
Route::middleware(['auth', 'role:director,director_ambiente,director_residuos,inspector,admin'])->group(function () {
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
            $funcionarios = \App\Models\User::whereHas('role', function ($q) {$q->whereIn('nombre', ['director', 'inspector']);
                })
                ->where('is_active', true)
                ->with(['role', 'direccion', 'inspector'])
                ->get();

            $resultado =$funcionarios->map(function ($user) {$nombreCompleto = trim(($user->nombre ?? '') . ' ' . ($user->apellido ?? ''));
                $rolNombre = ucfirst($user->role->nombre ?? 'Funcionario');

                return [
                    'id' => $user->id,
                    'nombre' => $nombreCompleto ?: 'Usuario #' . $user->id,
                    'cedula' => $user->cedula ?? 'S/C',
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