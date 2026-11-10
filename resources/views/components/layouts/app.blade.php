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
    :class="{ 'overflow-hidden lg:overflow-auto': sidebarOpen }" 
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

            <!-- Área del Perfil, Notificaciones y Desplegable -->
            <div class="flex items-center gap-3">
                
                <!-- Componente de Notificaciones -->
                <div class="relative" x-data="{ notifOpen: false }">
                    <button 
                        @click="notifOpen = !notifOpen" 
                        type="button"
                        class="relative p-2 text-slate-600 hover:bg-slate-100 rounded-xl transition-colors cursor-pointer"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        
                        <!-- Indicador visual si hay notificaciones sin leer -->
                        @if(auth()->user()->notificaciones()->whereNull('leido_at')->count() > 0)
                            <span class="absolute top-1.5 right-1.5 w-2.5 h-2.5 bg-rose-500 rounded-full ring-2 ring-white animate-pulse"></span>
                        @endif
                    </button>

                    <!-- Desplegable de Notificaciones -->
                    <div 
                        x-show="notifOpen"
                        @click.outside="notifOpen = false"
                        x-cloak
                        x-transition:enter="transition ease-out duration-150"
                        x-transition:enter-start="opacity-0 scale-95"
                        x-transition:enter-end="opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-100"
                        x-transition:leave-start="opacity-100 scale-100"
                        x-transition:leave-end="opacity-0 scale-95"
                        class="fixed right-4 top-[4.5rem] w-80 max-w-[calc(100vw-2rem)] origin-top-right sm:absolute sm:right-0 sm:top-auto sm:mt-2 sm:w-80 sm:origin-top-right bg-white rounded-2xl shadow-2xl border border-slate-200 py-2 z-50 text-xs"
                    >
                        <div class="px-4 py-2.5 border-b border-slate-100 flex items-center justify-between">
                            <span class="font-bold text-slate-800">Notificaciones Recientes</span>
                            <span class="text-[10px] bg-emerald-100 text-emerald-800 font-bold px-2 py-0.5 rounded-full">
                                {{ auth()->user()->notificaciones()->whereNull('leido_at')->count() }} nuevas
                            </span>
                        </div>

                        <div class="max-h-72 overflow-y-auto divide-y divide-slate-50">
                            @forelse(auth()->user()->notificaciones()->latest()->take(5)->get() as $notif)
                                <a 
                                    href="{{ $notif->link_url ?? '#' }}" 
                                    class="block px-4 py-3 hover:bg-slate-50 transition-colors {{ is_null($notif->leido_at) ? 'bg-emerald-50/40' : '' }}"
                                >
                                    <p class="font-bold text-slate-900">{{ $notif->titulo }}</p>
                                    <p class="text-slate-500 mt-0.5 leading-relaxed truncate">{{ $notif->mensaje }}</p>
                                    <span class="text-[9px] text-slate-400 mt-1 block">{{ $notif->created_at->diffForHumans() }}</span>
                                </a>
                            @empty
                                <div class="px-4 py-8 text-center text-slate-400 font-medium">
                                    No tienes notificaciones registradas.
                                </div>
                            @endforelse
                        </div>
                    </div>
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
                    class="fixed right-4 top-[4.5rem] w-72 max-w-[calc(100vw-2rem)] origin-top-right sm:absolute sm:right-0 sm:top-auto sm:mt-2 sm:w-72 sm:origin-top-right bg-white rounded-2xl shadow-2xl border border-slate-200 p-2 z-50 space-y-1"
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