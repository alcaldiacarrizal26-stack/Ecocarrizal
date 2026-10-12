<x-layouts.app title="Panel de Administración General" active="inicio">
    <div class="p-6 max-w-7xl mx-auto space-y-6">
        
        <!-- Header de Bienvenida -->
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
            <div>
                <span class="text-xs font-bold text-emerald-600 uppercase tracking-wider">Centro de Control Municipal</span>
                <h1 class="text-2xl font-bold text-slate-800">Bienvenido, {{ auth()->user()->nombre ?? auth()->user()->name }}</h1>
                <p class="text-slate-500 text-sm">Supervisión general de la Dirección de Ambiente, Residuos Comerciales y Soporte del Sistema.</p>
            </div>
            <div class="flex items-center gap-2">
                <span class="px-3 py-1 bg-emerald-100 text-emerald-800 font-bold text-xs rounded-xl border border-emerald-200">
                    Rol: Administrador Master
                </span>
            </div>
        </div>

        <!-- Tarjetas de Métricas Principales (KPIs) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            
            <!-- Total Empresas -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
                <div class="space-y-1">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Empresas Registradas</span>
                    <h3 class="text-2xl font-extrabold text-slate-800">{{ \App\Models\Empresa::count() }}</h3>
                    <span class="text-[11px] text-emerald-600 font-semibold">Módulo Residuos Comerciales</span>
                </div>
                <div class="w-12 h-12 bg-emerald-50 rounded-xl flex items-center justify-center text-emerald-600 border border-emerald-100 shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m0 0h4m-4 0V11m0 0h4m-4 0H7m4 0V7m0 0h4m-4 0H7"/></svg>
                </div>
            </div>

            <!-- Solicitudes y Trámites -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
                <div class="space-y-1">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Trámites Ambientales</span>
                    <h3 class="text-2xl font-extrabold text-slate-800">{{ \App\Models\Solicitud::count() }}</h3>
                    <span class="text-[11px] text-blue-600 font-semibold">Gestión Ambiental</span>
                </div>
                <div class="w-12 h-12 bg-blue-50 rounded-xl flex items-center justify-center text-blue-600 border border-blue-100 shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
            </div>

            <!-- Denuncias -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
                <div class="space-y-1">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Denuncias / Reportes</span>
                    <h3 class="text-2xl font-extrabold text-slate-800">{{ \App\Models\Denuncia::count() }}</h3>
                    <span class="text-[11px] text-amber-600 font-semibold">Atención Ciudadana</span>
                </div>
                <div class="w-12 h-12 bg-amber-50 rounded-xl flex items-center justify-center text-amber-600 border border-amber-100 shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
            </div>

            <!-- Tickets de Soporte Pendientes -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
                <div class="space-y-1">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Soporte Pendiente</span>
                    <h3 class="text-2xl font-extrabold text-slate-800">{{ \App\Models\TicketSoporte::where('estatus', '!=', 'RESUELTO')->count() }}</h3>
                    <span class="text-[11px] text-rose-600 font-semibold">Reportes de Sistema</span>
                </div>
                <div class="w-12 h-12 bg-rose-50 rounded-xl flex items-center justify-center text-rose-600 border border-rose-100 shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                </div>
            </div>

        </div>

        <!-- Tarjetas de Acceso Rápido con Botones Visibles y Estilizados -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            
            <!-- Acceso a Soporte Técnico -->
            <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm flex flex-col justify-between space-y-4">
                <div class="space-y-2">
                    <div class="w-10 h-10 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center font-bold">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                    </div>
                    <h3 class="text-base font-bold text-slate-800">Buzón de Soporte Técnico</h3>
                    <p class="text-xs text-slate-500">Revisa, gestiona y da respuesta a los reportes de errores o bloqueos enviados por los usuarios y directores.</p>
                </div>
                <a href="{{ route('admin.soporte') }}" class="w-full py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl text-xs text-center transition shadow-sm">
                    Administrar Soporte
                </a>
            </div>

            <!-- Acceso a Bitácora del Sistema -->
            <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm flex flex-col justify-between space-y-4">
                <div class="space-y-2">
                    <div class="w-10 h-10 bg-teal-50 text-teal-600 rounded-xl flex items-center justify-center font-bold">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                    </div>
                    <h3 class="text-base font-bold text-slate-800">Bitácora de Auditoría</h3>
                    <p class="text-xs text-slate-500">Consulta el registro detallado e imborrable de todas las transacciones, cambios de estatus y firmas realizadas en el sistema.</p>
                </div>
                <a href="{{ Route::has('admin.bitacora') ? route('admin.bitacora') : url('/admin/bitacora') }}" class="w-full py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl text-xs text-center transition shadow-sm">
                    Ver Bitácora
                </a>
            </div>

            <!-- Acceso a Respaldos de Base de Datos -->
            <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm flex flex-col justify-between space-y-4">
                <div class="space-y-2">
                    <div class="w-10 h-10 bg-amber-50 text-amber-600 rounded-xl flex items-center justify-center font-bold">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8-4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"/></svg>
                    </div>
                    <h3 class="text-base font-bold text-slate-800">Respaldos de Base de Datos</h3>
                    <p class="text-xs text-slate-500">Genera y descarga copias de seguridad seguras (backups) de toda la base de datos PostgreSQL de la alcaldía.</p>
                </div>
                <a href="{{ Route::has('admin.respaldos') ? route('admin.respaldos') : url('/admin/respaldos') }}" class="w-full py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl text-xs text-center transition shadow-sm">
                    Gestionar Backups
                </a>
            </div>

        </div>

    </div>
</x-layouts.app>