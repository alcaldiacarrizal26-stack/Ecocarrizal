@props(['active' => ''])

@php
    $user = auth()->user();
    $rol = $user->role->nombre ?? 'ciudadano'; 
    $prefix = match(strtolower($rol)) {
        'admin', 'administrador' => 'admin',
        'inspector' => 'inspector',
        default => 'ciudadano',
    };
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
    <div>
        <!-- Header / Brand -->
        <div class="p-5 flex items-center gap-3 border-b border-slate-800">
            <div class="h-10 w-10 rounded-xl bg-emerald-600/20 border border-emerald-500/30 flex items-center justify-center shrink-0">
                <img src="{{ asset('images/carrizal_logo.png') }}" alt="Carrizal Logo" class="h-7 w-7 object-contain">
            </div>
            <div class="overflow-hidden">
                <h2 class="text-sm font-bold text-white tracking-wide truncate">Ecosocialismo</h2>
                <p class="text-[10px] text-emerald-400 font-medium tracking-wider uppercase truncate">Carrizal Digital</p>
            </div>
        </div>

        <!-- Navegación según Rol -->
        <nav class="px-3 py-4 space-y-1.5 font-medium text-sm">

            @if($prefix === 'ciudadano')
                <!-- 1. Inicio Ciudadano -->
                <a 
                    href="{{ route('ciudadano.dashboard') }}" 
                    class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition-all {{ ($active === 'inicio' || request()->routeIs('ciudadano.dashboard')) ? 'bg-emerald-600 text-white font-semibold shadow-lg shadow-emerald-600/20' : 'text-slate-400 hover:text-white hover:bg-slate-800/70' }}"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 00-1-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 00-1 1m-6 0h6"/>
                    </svg>
                    <span>Inicio</span>
                </a>

                <!-- 2. Perfil Comercial -->
                <a 
                    href="{{ Route::has('ciudadano.empresas') ? route('ciudadano.empresas') : url('/ciudadano/empresas') }}" 
                    class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition-all {{ ($active === 'empresas' || request()->routeIs('ciudadano.empresas*')) ? 'bg-emerald-600 text-white font-semibold shadow-lg shadow-emerald-600/20' : 'text-slate-400 hover:text-white hover:bg-slate-800/70' }}"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m0 0h4m-4 0V11m0 0h4m-4 0H7m4 0V7m0 0h4m-4 0H7"/>
                    </svg>
                    <span>Perfil Comercial</span>
                </a>

                <!-- 3. Solicitudes y Trámites -->
                <a 
                    href="{{ Route::has('ciudadano.solicitudes') ? route('ciudadano.solicitudes') : url('/ciudadano/solicitudes') }}" 
                    class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition-all {{ ($active === 'solicitudes' || request()->routeIs('ciudadano.solicitudes*')) ? 'bg-emerald-600 text-white font-semibold shadow-lg shadow-emerald-600/20' : 'text-slate-400 hover:text-white hover:bg-slate-800/70' }}"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <span>Solicitudes y Trámites</span>
                </a>

                <!-- 4. Reportes y Denuncias -->
                <a 
                    href="{{ Route::has('ciudadano.reportes') ? route('ciudadano.reportes') : url('/ciudadano/reportes') }}" 
                    class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition-all {{ ($active === 'reportes' || request()->routeIs('ciudadano.reportes*')) ? 'bg-emerald-600 text-white font-semibold shadow-lg shadow-emerald-600/20' : 'text-slate-400 hover:text-white hover:bg-slate-800/70' }}"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <span>Reportes y Denuncias</span>
                </a>

                <!-- 5. Compensación Ambiental -->
                <a 
                    href="{{ Route::has('ciudadano.compensacion') ? route('ciudadano.compensacion') : url('/ciudadano/compensacion-ambiental') }}" 
                    class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition-all {{ ($active === 'compensacion' || request()->routeIs('ciudadano.compensacion*')) ? 'bg-emerald-600 text-white font-semibold shadow-lg shadow-emerald-600/20' : 'text-slate-400 hover:text-white hover:bg-slate-800/70' }}"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2H6a2 2 0 00-2 2v12a2 2 0 002-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <span>Compensación Ambiental</span>
                </a>
            @endif

            @if($prefix === 'admin')
                <a href="{{ Route::has('admin.dashboard') ? route('admin.dashboard') : url('/admin/dashboard') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-slate-400 hover:text-white hover:bg-slate-800/70">
                    <span>Panel General</span>
                </a>
            @endif

        </nav>
    </div>

    <!-- Footer / Soporte -->
    <div class="p-3 border-t border-slate-800 space-y-1">
        <a 
            href="{{ Route::has($prefix . '.soporte') ? route($prefix . '.soporte') : url('/' . $prefix . '/soporte') }}" 
            class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-slate-400 hover:text-white hover:bg-slate-800/70 transition-all font-medium text-sm {{ $active === 'soporte' ? 'bg-slate-800 text-white' : '' }}"
        >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
            <span>Soporte Técnico</span>
        </a>
    </div>
</aside>