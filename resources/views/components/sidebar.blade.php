@props(['active' => null])

@php
    $user = auth()->user();
    $rol = strtolower($user->role->nombre ?? $user->role ?? 'ciudadano'); 
    $direccionId = (int) ($user->direccion_id ?? 0);

    // Relación 1:1 con la empresa para el ciudadano
    $empresa = $user->empresa ?? null;

    // Mapeo de prefijos según el rol
    $prefix = match(true) {
        in_array($rol, ['admin', 'administrador']) => 'admin',
        in_array($rol, ['alcaldesa']) => 'alcaldesa',
        in_array($rol, ['director', 'director_ambiente', 'director_residuos']) => 'director',
        $rol === 'inspector' => 'inspector',
        default => 'ciudadano',
    };

    // Determinación estricta de visibilidad de áreas
    $esAdmin = in_array($rol, ['admin', 'administrador']);
    
    $verAmbiente = !$esAdmin && ($rol === 'director_ambiente' || ($rol === 'director' && $direccionId === 1));
    $verResiduos = !$esAdmin && ($rol === 'director_residuos' || ($rol === 'director' && $direccionId === 2));

    // Ruta del Dashboard dinámico según el rol correspondiente
    $dashboardRoute = match(true) {
        $prefix === 'admin' => Route::has('admin.dashboard') ? route('admin.dashboard') : url('/admin/dashboard'),
        $prefix === 'alcaldesa' => Route::has('alcaldesa.reportes') ? route('alcaldesa.reportes') : url('/alcaldesa/reportes'),
        $prefix === 'inspector' => Route::has('inspector.dashboard') ? route('inspector.dashboard') : url('/inspector/dashboard'),
        $prefix === 'director' && $verResiduos => Route::has('director.residuos.dashboard') ? route('director.residuos.dashboard') : url('/director/residuos/dashboard'),
        $prefix === 'director' => Route::has('director.ambiente.dashboard') ? route('director.ambiente.dashboard') : url('/director/ambiente/dashboard'),
        default => Route::has('ciudadano.dashboard') ? route('ciudadano.dashboard') : url('/ciudadano/dashboard'),
    };

    // Detección inteligente del Ítem Activo
    $currentActive = $active;

    if (is_null($currentActive) || $currentActive === '') {
        if (request()->routeIs('*.dashboard') || request()->is('*/dashboard')) {
            $currentActive = 'inicio';
        } elseif (request()->routeIs('admin.almacenamiento*') || request()->is('admin/almacenamiento*')) {
            $currentActive = 'admin_almacenamiento';
        } elseif (request()->routeIs('admin.bitacora*') || request()->is('admin/bitacora*')) {
            $currentActive = 'admin_bitacora';
        } elseif (request()->routeIs('admin.respaldos*') || request()->is('admin/respaldos*')) {
            $currentActive = 'admin_respaldos';
        } elseif (request()->routeIs('director.residuos.empresas.sanciones*') || request()->is('director/residuos/empresas/*/sanciones*')) {
            $currentActive = 'residuos_sanciones';
        } elseif (request()->routeIs('director.residuos.declaraciones*') || request()->is('director/residuos/declaraciones*')) {
            $currentActive = 'residuos_declaraciones';
        } elseif (request()->routeIs('director.residuos.empresas*') || request()->is('director/residuos/empresas*')) {
            $currentActive = 'residuos_empresas';
        } elseif (request()->routeIs('*declaraciones*') || request()->is('*/declaraciones*')) {
            $currentActive = 'declaraciones';
        } elseif (request()->routeIs('inspecciones*') || request()->is('inspecciones*')) {
            $currentActive = 'inspecciones';
        } elseif (request()->routeIs('*empresas*') || request()->is('*/empresas*')) {
            $currentActive = $prefix === 'ciudadano' ? 'empresas' : 'residuos_empresas';
        } elseif (request()->routeIs('*ambiente.denuncias*') || request()->routeIs('*denuncias*') || request()->is('*/ambiente/denuncias*')) {
            $currentActive = 'ambiente_denuncias';
        } elseif (request()->routeIs('*ambiente.solicitudes*') || request()->is('*/ambiente/solicitudes*')) {
            $currentActive = 'ambiente_solicitudes';
        } elseif (request()->routeIs('*ambiente.compensacion*') || request()->is('*/ambiente/compensacion*')) {
            $currentActive = 'ambiente_compensacion';
        } elseif (request()->routeIs('*ambiente.inspectores*') || request()->is('*/ambiente/inspectores*')) {
            $currentActive = 'ambiente_inspectores';
        } elseif (request()->routeIs('*.solicitudes*') || request()->is('*/solicitudes*')) {
            $currentActive = 'solicitudes';
        } elseif (request()->routeIs('*.reportes*') || request()->is('*/reportes*')) {
            $currentActive = 'reportes';
        } elseif (request()->routeIs('*.compensacion*') || request()->is('*/compensacion*')) {
            $currentActive = 'compensacion';
        } elseif (request()->routeIs('*.soporte*') || request()->is('*/soporte*')) {
            $currentActive = 'soporte';
        }
    }

    $soporteRoute = Route::has("{$prefix}.soporte") ? route("{$prefix}.soporte") : (Route::has('soporte') ? route('soporte') : url('/soporte'));
@endphp

<!-- Overlay para pantallas móviles -->
<div 
    x-show="sidebarOpen" 
    x-cloak
    @click="sidebarOpen = false" 
    x-transition:enter="transition-opacity ease-linear duration-300"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition-opacity ease-linear duration-300"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm z-40 lg:hidden"
></div>

<!-- Sidebar Principal -->
<aside 
    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
    class="fixed top-0 left-0 z-50 h-screen w-64 bg-slate-900 border-r border-slate-800 text-slate-300 flex flex-col justify-between transition-transform duration-300 ease-in-out shrink-0"
>
    <div class="overflow-y-auto">
        <!-- Header / Identificación institucional -->
        <div class="p-5 flex items-center gap-3 border-b border-slate-800">
            <!-- Contenedor horizontal limpio sin el cuadro forzado -->
            <div class="flex items-center justify-center shrink-0">
                <img src="{{ asset('images/carrizal_logo.png') }}" alt="Carrizal Logo" class="h-8 w-auto object-contain">
            </div>
            <div class="overflow-hidden">
                <h2 class="text-sm font-bold text-white tracking-wide truncate">Ecosocialismo</h2>
                <p class="text-[10px] text-mint font-semibold tracking-wider uppercase truncate">Carrizal Digital</p>
            </div>
        </div>

        <!-- Navegación por Roles -->
        <nav class="px-3 py-4 space-y-1.5 font-medium text-sm">

            {{-- 1. MENÚ CIUDADANO --}}
            @if($prefix === 'ciudadano')
                <a href="{{ $dashboardRoute }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition-all {{ $currentActive === 'inicio' ? 'bg-primary text-white font-semibold shadow-lg shadow-primary/25' : 'text-slate-400 hover:text-white hover:bg-slate-800/70' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 00-1-1v-4a1 1 0 011-1h2a1 1 0 00-1 1m-6 0h6"/></svg>
                    <span>Inicio</span>
                </a>

                <a href="{{ Route::has('ciudadano.solicitudes') ? route('ciudadano.solicitudes') : url('/ciudadano/solicitudes') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition-all {{ $currentActive === 'solicitudes' ? 'bg-primary text-white font-semibold shadow-lg shadow-primary/25' : 'text-slate-400 hover:text-white hover:bg-slate-800/70' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span>Solicitudes y Trámites</span>
                </a>

                <a href="{{ Route::has('ciudadano.denuncias') ? route('ciudadano.denuncias') : url('/ciudadano/denuncias') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition-all {{ $currentActive === 'denuncias' ? 'bg-primary text-white font-semibold shadow-lg shadow-primary/25' : 'text-slate-400 hover:text-white hover:bg-slate-800/70' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <span>Reportes y Denuncias</span>
                </a>

                <a href="{{ Route::has('ciudadano.compensacion') ? route('ciudadano.compensacion') : url('/ciudadano/compensacion') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition-all {{ $currentActive === 'compensacion' ? 'bg-primary text-white font-semibold shadow-lg shadow-primary/25' : 'text-slate-400 hover:text-white hover:bg-slate-800/70' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <span>Compensación Ambiental</span>
                </a>

                {{-- Módulo de Gestión Comercial --}}
                <div class="pt-3 pb-1 px-3 text-[10px] font-bold text-mint uppercase tracking-wider opacity-80">
                    Área Comercial
                </div>

                @if(!$empresa)
                    <a href="{{ Route::has('ciudadano.empresas.create') ? route('ciudadano.empresas.create') : url('/ciudadano/empresas/crear') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition-all {{ $currentActive === 'empresas' ? 'bg-primary text-white font-semibold shadow-lg shadow-primary/25' : 'text-slate-400 hover:text-white hover:bg-slate-800/70' }}">
                        <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                        <span>Registrar Empresa</span>
                    </a>
                @elseif($empresa->estatus === 'APROBADO')
                    <a href="{{ Route::has('ciudadano.empresas') ? route('ciudadano.empresas') : url('/ciudadano/empresas') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition-all {{ $currentActive === 'empresas' ? 'bg-primary text-white font-semibold shadow-lg shadow-primary/25' : 'text-slate-400 hover:text-white hover:bg-slate-800/70' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m0 0h4m-4 0V11m0 0h4m-4 0H7m4 0V7m0 0h4m-4 0H7"/></svg>
                        <span class="truncate">{{ $empresa->razon_social ?? 'Mi Empresa' }}</span>
                    </a>

                    <a href="{{ Route::has('ciudadano.declaraciones.index') ? route('ciudadano.declaraciones.index') : url('/ciudadano/declaraciones') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition-all {{ $currentActive === 'declaraciones' ? 'bg-primary text-white font-semibold shadow-lg shadow-primary/25' : 'text-slate-400 hover:text-white hover:bg-slate-800/70' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <span>Declaración Residuos</span>
                    </a>
                @else
                    <a href="{{ Route::has('ciudadano.empresas') ? route('ciudadano.empresas') : url('/ciudadano/empresas') }}" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl transition-all {{ $currentActive === 'empresas' ? 'bg-primary text-white font-semibold shadow-lg shadow-primary/25' : 'text-slate-400 hover:text-white hover:bg-slate-800/70' }}">
                        <div class="flex items-center gap-3 overflow-hidden">
                            <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span class="truncate">Estatus Empresa</span>
                        </div>
                        <span class="text-[10px] px-2 py-0.5 rounded-md font-bold uppercase tracking-wider {{ $empresa->estatus === 'PENDIENTE' ? 'bg-amber-500/20 text-amber-300 border border-amber-500/30' : 'bg-rose-500/20 text-rose-300 border border-rose-500/30' }}">
                            {{ $empresa->estatus }}
                        </span>
                    </a>
                @endif
            @endif

            {{-- 2. MENÚ INSPECTOR --}}
            @if($prefix === 'inspector')
                <a href="{{ $dashboardRoute }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition-all {{ $currentActive === 'inicio' ? 'bg-primary text-white font-semibold shadow-lg shadow-primary/25' : 'text-slate-400 hover:text-white hover:bg-slate-800/70' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 00-1-1v-4a1 1 0 011-1h2a1 1 0 00-1 1m-6 0h6"/></svg>
                    <span>Inicio</span>
                </a>

                <div class="pt-3 pb-1 px-3 text-[10px] font-bold text-mint uppercase tracking-wider opacity-80">
                    Módulo Campo
                </div>

                <a href="{{ Route::has('inspecciones.index') ? route('inspecciones.index') : url('/inspecciones') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition-all {{ $currentActive === 'inspecciones' ? 'bg-primary text-white font-semibold shadow-lg shadow-primary/25' : 'text-slate-400 hover:text-white hover:bg-slate-800/70' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                    <span>Mis Inspecciones</span>
                </a>
            @endif

            {{-- 3. MENÚ ADMINISTRADOR MASTER --}}
            @if($prefix === 'admin')
                <a href="{{ $dashboardRoute }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition-all {{ $currentActive === 'inicio' ? 'bg-primary text-white font-semibold shadow-lg shadow-primary/25' : 'text-slate-400 hover:text-white hover:bg-slate-800/70' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 00-1-1v-4a1 1 0 011-1h2a1 1 0 00-1 1m-6 0h6"/></svg>
                    <span>Inicio Admin</span>
                </a>

                <div class="pt-3 pb-1 px-3 text-[10px] font-bold text-mint uppercase tracking-wider opacity-80">
                    Administración Sistema
                </div>
                <a href="{{ Route::has('admin.almacenamiento') ? route('admin.almacenamiento') : url('/admin/almacenamiento') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition-all {{ $currentActive === 'admin_almacenamiento' ? 'bg-primary text-white font-semibold shadow-lg shadow-primary/25' : 'text-slate-400 hover:text-white hover:bg-slate-800/70' }}">
                    <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8-4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"/></svg>
                    <span>Almacenamiento y Recursos</span>
                </a>
                <a href="{{ Route::has('admin.bitacora') ? route('admin.bitacora') : url('/admin/bitacora') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition-all {{ $currentActive === 'admin_bitacora' ? 'bg-primary text-white font-semibold shadow-lg shadow-primary/25' : 'text-slate-400 hover:text-white hover:bg-slate-800/70' }}">
                    <svg class="w-5 h-5 text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                    <span>Bitácora Auditoría</span>
                </a>
                <a href="{{ Route::has('admin.respaldos') ? route('admin.respaldos') : url('/admin/respaldos') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition-all {{ $currentActive === 'admin_respaldos' ? 'bg-primary text-white font-semibold shadow-lg shadow-primary/25' : 'text-slate-400 hover:text-white hover:bg-slate-800/70' }}">
                    <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8-4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"/></svg>
                    <span>Respaldos Base de Datos</span>
                </a>
                <a href="{{ route('admin.usuarios.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition-all {{ $currentActive === 'admin_usuarios' ? 'bg-primary text-white font-semibold shadow-lg shadow-primary/25' : 'text-slate-400 hover:text-white hover:bg-slate-800/70' }}">
                    <svg class="w-5 h-5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    <span>Gestión de Usuarios</span>
                </a>
            @endif

            {{-- 4. MENÚ DIRECTOR OPERATIVO (Con Contadores por Cantidad) --}}
            @if($prefix === 'director')
                <a href="{{ $dashboardRoute }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition-all {{ $currentActive === 'inicio' ? 'bg-primary text-white font-semibold shadow-lg shadow-primary/25' : 'text-slate-400 hover:text-white hover:bg-slate-800/70' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 00-1-1v-4a1 1 0 011-1h2a1 1 0 00-1 1m-6 0h6"/></svg>
                    <span>Inicio Director</span>
                </a>

                {{-- Módulo Compartido de Inspectores para ambos Directores --}}
                <div class="pt-3 pb-1 px-3 text-[10px] font-bold text-mint uppercase tracking-wider opacity-80">
                    Personal de Campo
                </div>
                <a href="{{ Route::has('director.inspectores.index') ? route('director.inspectores.index') : url('/director/inspectores') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition-all {{ $currentActive === 'ambiente_inspectores' ? 'bg-primary text-white font-semibold shadow-lg shadow-primary/25' : 'text-slate-400 hover:text-white hover:bg-slate-800/70' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    <span>Inspectores</span>
                </a>

                <a href="{{ Route::has('inspecciones.index') ? route('inspecciones.index') : url('/inspecciones') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition-all {{ $currentActive === 'inspecciones' ? 'bg-primary text-white font-semibold shadow-lg shadow-primary/25' : 'text-slate-400 hover:text-white hover:bg-slate-800/70' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                    <span>Inspecciones de Campo</span>
                </a>

                @if($verAmbiente)
                    <div class="pt-3 pb-1 px-3 text-[10px] font-bold text-mint uppercase tracking-wider opacity-80">
                        Gestión Ambiental
                    </div>
                    <a href="{{ Route::has('director.ambiente.denuncias') ? route('director.ambiente.denuncias') : url('/director/ambiente/denuncias') }}" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl transition-all {{ $currentActive === 'ambiente_denuncias' ? 'bg-primary text-white font-semibold shadow-lg shadow-primary/25' : 'text-slate-400 hover:text-white hover:bg-slate-800/70' }}">
                        <div class="flex items-center gap-3 overflow-hidden">
                            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            <span class="truncate">Reportes y Denuncias</span>
                        </div>
                        @if(isset($countDenuncias) && $countDenuncias > 0)
                            <span class="shrink-0 px-2 py-0.5 text-[10px] font-bold bg-rose-500/20 text-rose-300 border border-rose-500/30 rounded-full">
                                {{ $countDenuncias }}
                            </span>
                        @endif
                    </a>
                    
                    <a href="{{ Route::has('director.ambiente.solicitudes') ? route('director.ambiente.solicitudes') : url('/director/ambiente/solicitudes') }}" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl transition-all {{ $currentActive === 'ambiente_solicitudes' ? 'bg-primary text-white font-semibold shadow-lg shadow-primary/25' : 'text-slate-400 hover:text-white hover:bg-slate-800/70' }}">
                        <div class="flex items-center gap-3 overflow-hidden">
                            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            <span class="truncate">Solicitudes y Trámites</span>
                        </div>
                        @if(isset($countSolicitudes) && $countSolicitudes > 0)
                            <span class="shrink-0 px-2 py-0.5 text-[10px] font-bold bg-amber-500/20 text-amber-300 border border-amber-500/30 rounded-full">
                                {{ $countSolicitudes }}
                            </span>
                        @endif
                    </a>
                    
                    <a href="{{ Route::has('director.ambiente.compensacion') ? route('director.ambiente.compensacion') : url('/director/ambiente/compensacion') }}" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl transition-all {{ $currentActive === 'ambiente_compensacion' ? 'bg-primary text-white font-semibold shadow-lg shadow-primary/25' : 'text-slate-400 hover:text-white hover:bg-slate-800/70' }}">
                        <div class="flex items-center gap-3 overflow-hidden">
                            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                            <span class="truncate">Compensación Ambiental</span>
                        </div>
                        @if(isset($countCompensaciones) && $countCompensaciones > 0)
                            <span class="shrink-0 px-2 py-0.5 text-[10px] font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 rounded-full">
                                {{ $countCompensaciones }}
                            </span>
                        @endif
                    </a>
                @endif

                {{-- Dirección de Residuos --}}
                @if($verResiduos)
                    <div class="pt-3 pb-1 px-3 text-[10px] font-bold text-mint uppercase tracking-wider opacity-80">
                        Residuos Comerciales
                    </div>
                    <a href="{{ Route::has('director.residuos.empresas.index') ? route('director.residuos.empresas.index') : url('/director/residuos/empresas') }}" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl transition-all {{ $currentActive === 'residuos_empresas' ? 'bg-primary text-white font-semibold shadow-lg shadow-primary/25' : 'text-slate-400 hover:text-white hover:bg-slate-800/70' }}">
                        <div class="flex items-center gap-3 overflow-hidden">
                            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m0 0h4m-4 0V11m0 0h4m-4 0H7m4 0V7m0 0h4m-4 0H7"/></svg>
                            <span class="truncate">Gestión de Empresas</span>
                        </div>
                        @if(isset($countEmpresas) && $countEmpresas > 0)
                            <span class="shrink-0 px-2 py-0.5 text-[10px] font-bold bg-indigo-500/20 text-indigo-300 border border-indigo-500/30 rounded-full">
                                {{ $countEmpresas }}
                            </span>
                        @endif
                    </a>
                    
                    <a href="{{ Route::has('director.residuos.declaraciones.index') ? route('director.residuos.declaraciones.index') : url('/director/residuos/declaraciones') }}" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl transition-all {{ $currentActive === 'residuos_declaraciones' ? 'bg-primary text-white font-semibold shadow-lg shadow-primary/25' : 'text-slate-400 hover:text-white hover:bg-slate-800/70' }}">
                        <div class="flex items-center gap-3 overflow-hidden">
                            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            <span class="truncate">Revisión Declaraciones</span>
                        </div>
                        @if(isset($countDeclaraciones) && $countDeclaraciones > 0)
                            <span class="shrink-0 px-2 py-0.5 text-[10px] font-bold bg-mint/20 text-mint border border-mint/30 rounded-full">
                                {{ $countDeclaraciones }}
                            </span>
                        @endif
                    </a>
                @endif
            @endif

            {{-- 5. MENÚ ALCALDESA / DESPACHO EJECUTIVO --}}
            @if($prefix === 'alcaldesa')
                <div class="pt-2 pb-1 px-3 text-[10px] font-bold text-mint uppercase tracking-wider opacity-80">
                    Despacho Ejecutivo
                </div>

                <a href="{{ route('alcaldesa.reportes') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition-all {{ $currentActive === 'reportes' ? 'bg-primary text-white font-semibold shadow-lg shadow-primary/25' : 'text-slate-400 hover:text-white hover:bg-slate-800/70' }}">
                    <svg class="w-5 h-5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    <span>Resumen Ejecutivo</span>
                </a>

                <a href="{{ route('alcaldesa.ambiente') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition-all {{ $currentActive === 'alcaldesa_ambiente' ? 'bg-primary text-white font-semibold shadow-lg shadow-primary/25' : 'text-slate-400 hover:text-white hover:bg-slate-800/70' }}">
                    <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                    <span>Gestión Ambiental</span>
                </a>

                <a href="{{ route('alcaldesa.residuos') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition-all {{ $currentActive === 'alcaldesa_residuos' ? 'bg-primary text-white font-semibold shadow-lg shadow-primary/25' : 'text-slate-400 hover:text-white hover:bg-slate-800/70' }}">
                    <svg class="w-5 h-5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m0 0h4m-4 0V11m0 0h4m-4 0H7m4 0V7m0 0h4m-4 0H7"/></svg>
                    <span>Manejo de Residuos</span>
                </a>

                <a href="{{ route('alcaldesa.poderPopular') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition-all {{ $currentActive === 'alcaldesa_poder_popular' ? 'bg-primary text-white font-semibold shadow-lg shadow-primary/25' : 'text-slate-400 hover:text-white hover:bg-slate-800/70' }}">
                    <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    <span>Poder Popular / Comunas</span>
                </a>
            @endif

        </nav>
    </div>

    <!-- Footer / Soporte Técnico -->
    <div class="p-3 border-t border-slate-800 space-y-1 shrink-0">
        <a href="{{ $soporteRoute }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition-all font-medium text-sm {{ $currentActive === 'soporte' ? 'bg-slate-800 text-white' : 'text-slate-400 hover:text-white hover:bg-slate-800/70' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636l3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
            <span>Soporte Técnico</span>
        </a>
    </div>
</aside>