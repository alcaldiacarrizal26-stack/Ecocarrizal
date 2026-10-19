<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Panel Ciudadano - Ecosocialismo' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body 
    class="bg-slate-50 font-sans antialiased text-slate-800" 
    x-data="{ sidebarOpen: false, profileMenuOpen: false, confirmLogout: false }"
>

    <!-- Sidebar Fijo -->
    <x-sidebar :active="$active ?? 'inicio'" />

    <!-- Wrapper del Contenido Principal -->
    <div class="lg:pl-64 flex flex-col min-h-screen">
        
        <!-- Header Superior -->
        <header class="h-16 bg-white border-b border-slate-200 px-4 sm:px-6 flex items-center justify-between sticky top-0 z-30 shadow-xs">
            <!-- Botón abrir sidebar móvil -->
            <div class="flex items-center gap-3">
                <button 
                    @click="sidebarOpen = !sidebarOpen" 
                    type="button"
                    class="p-2 text-slate-600 hover:bg-slate-100 rounded-xl lg:hidden cursor-pointer"
                >
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider hidden sm:inline">
                    Plataforma Digital de Ecosocialismo
                </span>
            </div>

            <!-- Área del Perfil y Desplegable -->
            <div class="relative" @click.outside="profileMenuOpen = false">
                <button 
                    @click="profileMenuOpen = !profileMenuOpen" 
                    type="button"
                    class="flex items-center gap-3 p-1.5 rounded-xl hover:bg-slate-100 transition-colors cursor-pointer border border-transparent hover:border-slate-200"
                >
                    <div class="h-9 w-9 rounded-xl bg-emerald-600 text-white flex items-center justify-center font-bold text-sm shadow-xs">
                        {{ strtoupper(substr(auth()->user()->nombre ?? 'C', 0, 1)) }}
                    </div>
                    <div class="text-left hidden md:block">
                        <p class="text-xs font-bold text-slate-800 leading-none">
                            {{ auth()->user()->nombre }} {{ auth()->user()->apellido }}
                        </p>
                        <p class="text-[11px] text-slate-500 font-medium mt-0.5">
                            {{ auth()->user()->cedula ?? 'V-00000000' }}
                        </p>
                    </div>
                    <svg class="w-4 h-4 text-slate-400 transition-transform duration-200" :class="{ 'rotate-180': profileMenuOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                <!-- Menú Desplegable -->
                <div 
                    x-show="profileMenuOpen"
                    x-cloak
                    x-transition:enter="transition ease-out duration-150"
                    x-transition:enter-start="opacity-0 scale-95"
                    x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-100"
                    x-transition:leave-start="opacity-100 scale-100"
                    x-transition:leave-end="opacity-0 scale-95"
                    class="absolute right-0 mt-2 w-72 bg-white rounded-2xl shadow-2xl border border-slate-200 p-2 z-50 space-y-1"
                >
                    <!-- Cabecera del desplegable -->
                    <div class="px-3 py-2.5 bg-slate-50 rounded-xl border border-slate-100">
                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Cuenta Conectada</p>
                        <p class="text-xs font-bold text-slate-800 truncate mt-0.5">
                            {{ auth()->user()->nombre }} {{ auth()->user()->apellido }}
                        </p>
                        <p class="text-[11px] text-slate-500 truncate">
                            {{ auth()->user()->email }}
                        </p>
                    </div>

                    <!-- Opción Ver Perfil -->
                    <a 
                        href="{{ route('perfil.show') }}" 
                        class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold text-slate-700 hover:bg-emerald-50 hover:text-emerald-700 transition-colors"
                    >
                        <div class="p-1.5 rounded-lg bg-slate-100 text-slate-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        <span>Ver Perfil</span>
                    </a>

                    <!-- Botón Cerrar Sesión -->
                    <button 
                        type="button" 
                        @click="profileMenuOpen = false; confirmLogout = true" 
                        class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-bold text-rose-600 bg-rose-50 hover:bg-rose-100 transition-colors cursor-pointer text-left"
                    >
                        <div class="p-1.5 rounded-lg bg-rose-100 text-rose-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                        </div>
                        <span>Cerrar Sesión</span>
                    </button>
                </div>
            </div>
        </header>

        <!-- Contenido principal -->
        <main class="p-4 sm:p-6 lg:p-8 flex-1">
            {{ $slot }}
        </main>
    </div>

    <!-- Modal de Confirmación de Cierre de Sesión -->
    <div 
        x-show="confirmLogout" 
        x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
    >
        <div 
            @click.outside="confirmLogout = false"
            class="bg-white rounded-3xl max-w-sm w-full p-6 shadow-2xl border border-slate-100 space-y-5 text-center transform transition-all"
        >
            <div class="mx-auto w-12 h-12 rounded-2xl bg-rose-100 text-rose-600 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
            </div>

            <div>
                <h3 class="text-base font-bold text-slate-900">¿Cerrar sesión activa?</h3>
                <p class="text-xs text-slate-500 mt-1">Tendrás que volver a ingresar con tus credenciales para acceder a tus trámites.</p>
            </div>

            <div class="flex items-center gap-3">
                <button 
                    type="button" 
                    @click="confirmLogout = false"
                    class="flex-1 py-2.5 px-4 rounded-xl border border-slate-200 text-xs font-semibold text-slate-600 hover:bg-slate-50 transition-colors cursor-pointer"
                >
                    Cancelar
                </button>

                <form method="POST" action="{{ route('logout') }}" class="flex-1">
                    @csrf
                    <button 
                        type="submit" 
                        class="w-full py-2.5 px-4 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-semibold shadow-md shadow-rose-600/20 transition-colors cursor-pointer"
                    >
                        Sí, cerrar sesión
                    </button>
                </form>
            </div>
        </div>
    </div>

</body>
</html>