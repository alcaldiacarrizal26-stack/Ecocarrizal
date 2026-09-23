@props(['active' => null])

@php
    $user = auth()->user();
    $rol = strtolower($user->role->nombre ?? 'ciudadano'); 
    $direccionId = (int) ($user->direccion_id ?? 0);

    // Mapeo de prefijos según el rol
    $prefix = match(true) {
        in_array($rol, ['admin', 'administrador', 'director', 'director_ambiente', 'director_residuos']) => 'director',
        $rol === 'inspector' => 'inspector',
        default => 'ciudadano',
    };

    // Determinación de visibilidad
    $esAdmin = in_array($rol, ['admin', 'administrador']);
    $verAmbiente = $esAdmin || $direccionId === 1 || $rol === 'director_ambiente';
    $verResiduos = $esAdmin || $direccionId === 2 || $rol === 'director_residuos';

    // Ruta del Dashboard dinámico
    $dashboardRoute = match(true) {
        $prefix === 'inspector' => Route::has('inspector.dashboard') ? route('inspector.dashboard') : url('/inspector/dashboard'),
        $verResiduos && !$verAmbiente => Route::has('director.residuos.dashboard') 
            ? route('director.residuos.dashboard') 
            : (Route::has("{$prefix}.dashboard") ? route("{$prefix}.dashboard") : url("/{$prefix}/dashboard")),
        default => Route::has("{$prefix}.ambiente.dashboard") 
            ? route("{$prefix}.ambiente.dashboard") 
            : (Route::has("{$prefix}.dashboard") ? route("{$prefix}.dashboard") : url("/{$prefix}/dashboard")),
    };

    // Ítem activo
    $currentActive = $active;

    if (is_null($currentActive) || $currentActive === '') {
        if (request()->routeIs('*.dashboard') || request()->is('*/dashboard')) {
            $currentActive = 'inicio';
        } elseif (request()->routeIs('*inspecciones*') || request()->is('*/inspecciones*')) {
            $currentActive = 'inspecciones';
        } elseif (request()->routeIs('*empresas*') || request()->is('*/empresas*')) {
            $currentActive = $prefix === 'ciudadano' ? 'empresas' : 'residuos_empresas';
        } elseif (request()->routeIs('*ambiente.reportes*') || request()->is('*/ambiente/reportes*')) {
            $currentActive = 'ambiente_reportes';
        } elseif (request()->routeIs('*ambiente.solicitudes*') || request()->is('*/ambiente/solicitudes*')) {
            $currentActive = 'ambiente_solicitudes';
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
@endphp

<!-- Overlay para pantallas pequeñas -->
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
        <!-- Header / Brand -->
        <div class="p-5 flex items-center gap-3 border-b border-slate-800">
            <div class="h-10 w-10 rounded-xl bg-primary/20 border border-primary/30 flex items-center justify-center shrink-0">
                <img src="{{ asset('images/carrizal_logo.png') }}" alt="Carrizal Logo" class="h-7 w-7 object-contain">
            </div>
            <div class="overflow-hidden">
                <h2 class="text-sm font-bold text-white tracking-wide truncate">Ecosocialismo</h2>
                <p class="text-[10px] text-mint font-semibold tracking-wider uppercase truncate">Carrizal Digital</p>
            </div>
        </div>

        <!-- Navegación según Rol -->
        <nav class="px-3 py-4 space-y-1.5 font-medium text-sm">

            {{-- 1. Menú Ciudadano --}}
            @if($prefix === 'ciudadano')
                <a href="{{ $dashboardRoute }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition-all {{ $currentActive === 'inicio' ? 'bg-primary text-white font-semibold shadow-lg shadow-primary/25' : 'text-slate-400 hover:text-white hover:bg-slate-800/70' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 00-1-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 00-1 1m-6 0h6"/></svg>
                    <span>Inicio</span>
                </a>
                <a href="{{ Route::has('ciudadano.empresas') ? route('ciudadano.empresas') : url('/ciudadano/empresas') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition-all {{ $currentActive === 'empresas' ? 'bg-primary text-white font-semibold shadow-lg shadow-primary/25' : 'text-slate-400 hover:text-white hover:bg-slate-800/70' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m0 0h4m-4 0V11m0 0h4m-4 0H7m4 0V7m0 0h4m-4 0H7"/></svg>
                    <span>Perfil Comercial</span>
                </a>
                <a href="{{ Route::has('ciudadano.solicitudes') ? route('ciudadano.solicitudes') : url('/ciudadano/solicitudes') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition-all {{ $currentActive === 'solicitudes' ? 'bg-primary text-white font-semibold shadow-lg shadow-primary/25' : 'text-slate-400 hover:text-white hover:bg-slate-800/70' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span>Solicitudes y Trámites</span>
                </a>
                <a href="{{ Route::has('ciudadano.reportes') ? route('ciudadano.reportes') : url('/ciudadano/reportes') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition-all {{ $currentActive === 'reportes' ? 'bg-primary text-white font-semibold shadow-lg shadow-primary/25' : 'text-slate-400 hover:text-white hover:bg-slate-800/70' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <span>Reportes y Denuncias</span>
                </a>
                <a href="{{ Route::has('ciudadano.compensacion') ? route('ciudadano.compensacion') : url('/ciudadano/compensacion-ambiental') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition-all {{ $currentActive === 'compensacion' ? 'bg-primary text-white font-semibold shadow-lg shadow-primary/25' : 'text-slate-400 hover:text-white hover:bg-slate-800/70' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2H6a2 2 0 00-2 2v12a2 2 0 002-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <span>Compensación Ambiental</span>
                </a>
            @endif

            {{-- 2. Menú Inspector --}}
            @if($prefix === 'inspector')
                <a href="{{ $dashboardRoute }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition-all {{ $currentActive === 'inicio' ? 'bg-primary text-white font-semibold shadow-lg shadow-primary/25' : 'text-slate-400 hover:text-white hover:bg-slate-800/70' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 00-1-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 00-1 1m-6 0h6"/></svg>
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

            {{-- 3. Menú Director / Admin --}}
            @if($prefix === 'director')
                <a href="{{ $dashboardRoute }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition-all {{ $currentActive === 'inicio' ? 'bg-primary text-white font-semibold shadow-lg shadow-primary/25' : 'text-slate-400 hover:text-white hover:bg-slate-800/70' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 00-1-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 00-1 1m-6 0h6"/></svg>
                    <span>Inicio</span>
                </a>

                @if($verAmbiente)
                    <div class="pt-3 pb-1 px-3 text-[10px] font-bold text-mint uppercase tracking-wider opacity-80">
                        Gestión Ambiental
                    </div>
                    <a href="{{ Route::has('director.ambiente.reportes') ? route('director.ambiente.reportes') : url('/director/ambiente/reportes') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition-all {{ $currentActive === 'ambiente_reportes' ? 'bg-primary text-white font-semibold shadow-lg shadow-primary/25' : 'text-slate-400 hover:text-white hover:bg-slate-800/70' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        <span>Reportes y Denuncias</span>
                    </a>
                    <a href="{{ Route::has('director.ambiente.solicitudes') ? route('director.ambiente.solicitudes') : url('/director/ambiente/solicitudes') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition-all {{ $currentActive === 'ambiente_solicitudes' ? 'bg-primary text-white font-semibold shadow-lg shadow-primary/25' : 'text-slate-400 hover:text-white hover:bg-slate-800/70' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <span>Solicitudes y Trámites</span>
                    </a>
                    <a href="{{ Route::has('director.ambiente.inspectores') ? route('director.ambiente.inspectores') : url('/director/ambiente/inspectores') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition-all {{ $currentActive === 'ambiente_inspectores' ? 'bg-primary text-white font-semibold shadow-lg shadow-primary/25' : 'text-slate-400 hover:text-white hover:bg-slate-800/70' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        <span>Inspectores</span>
                    </a>
                @endif

                @if($verResiduos)
                    <div class="pt-3 pb-1 px-3 text-[10px] font-bold text-mint uppercase tracking-wider opacity-80">
                        Residuos Comercial
                    </div>
                    <a href="{{ Route::has('director.residuos.empresas') ? route('director.residuos.empresas') : url('/director/residuos/empresas') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition-all {{ $currentActive === 'residuos_empresas' ? 'bg-primary text-white font-semibold shadow-lg shadow-primary/25' : 'text-slate-400 hover:text-white hover:bg-slate-800/70' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m0 0h4m-4 0V11m0 0h4m-4 0H7m4 0V7m0 0h4m-4 0H7"/></svg>
                        <span>Gestión de Empresas</span>
                    </a>
                @endif

                {{-- Módulo Campo para Directores --}}
                <div class="pt-3 pb-1 px-3 text-[10px] font-bold text-mint uppercase tracking-wider opacity-80">
                    Módulo Campo
                </div>
                <a href="{{ Route::has('inspecciones.index') ? route('inspecciones.index') : url('/inspecciones') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition-all {{ $currentActive === 'inspecciones' ? 'bg-primary text-white font-semibold shadow-lg shadow-primary/25' : 'text-slate-400 hover:text-white hover:bg-slate-800/70' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                    <span>Inspecciones de Campo</span>
                </a>
            @endif

        </nav>
    </div>

    <!-- Footer / Soporte -->
    <div class="p-3 border-t border-slate-800 space-y-1 shrink-0">
        <a href="{{ Route::has($prefix . '.soporte') ? route($prefix . '.soporte') : url('/' . $prefix . '/soporte') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition-all font-medium text-sm {{ $currentActive === 'soporte' ? 'bg-slate-800 text-white' : 'text-slate-400 hover:text-white hover:bg-slate-800/70' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
            <span>Soporte Técnico</span>
        </a>
    </div>
</aside>