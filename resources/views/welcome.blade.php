<!DOCTYPE html>
<html lang="es" x-data="{ 
    openAuth: {{ $errors->any() ? 'true' : 'false' }}, 
    authMode: '{{ $errors->has('nombre') || $errors->has('cedula') || $errors->has('apellido') ? 'register' : 'login' }}', 
    mobileMenu: false, 
    activeTab: 1 
}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión Ambiental - Plataforma Oficial</title>

    <!-- Tailwind CSS v4 CDN -->
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <!-- Alpine.js CDN -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-slate-950 text-slate-100 min-h-screen flex flex-col font-sans antialiased selection:bg-emerald-500 selection:text-slate-950">

    <!-- Header / Navegación -->
    <header class="border-b border-slate-800/80 bg-slate-950/90 backdrop-blur-md sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
            
            <a href="#" class="flex items-center gap-3 group">
                <div class="h-11 w-11 rounded-2xl bg-gradient-to-br from-emerald-400 via-teal-500 to-emerald-600 flex items-center justify-center font-black text-slate-950 text-2xl shadow-lg shadow-emerald-500/20 group-hover:scale-105 transition-transform">
                    E
                </div>
                <div class="flex flex-col">
                    <span class="font-bold text-xl tracking-tight text-white group-hover:text-emerald-400 transition-colors">Ecosocialismo</span>
                    <span class="text-[11px] text-emerald-400 font-semibold tracking-wider uppercase">Alcaldía de Carrizal</span>
                </div>
            </a>

            <nav class="hidden md:flex items-center gap-8 text-sm font-medium text-slate-300">
                <a href="#inicio" class="hover:text-emerald-400 transition-colors">Inicio</a>
                <a href="#servicios" class="hover:text-emerald-400 transition-colors">Servicios y Módulos</a>
                <a href="#metricas" class="hover:text-emerald-400 transition-colors">Métricas</a>
                <a href="#contacto" class="hover:text-emerald-400 transition-colors">Contacto</a>
            </nav>

            <div class="hidden md:flex items-center gap-4">
                <button @click="openAuth = true; authMode = 'login'" class="px-5 py-2.5 text-sm font-medium text-slate-300 hover:text-white transition-colors cursor-pointer">
                    Iniciar Sesión
                </button>
                <button @click="openAuth = true; authMode = 'register'" class="px-5 py-2.5 text-sm font-semibold bg-emerald-500 hover:bg-emerald-400 text-slate-950 rounded-xl transition-all shadow-lg shadow-emerald-500/20 hover:shadow-emerald-500/30 transform active:scale-95 cursor-pointer">
                    Registrarse
                </button>
            </div>

            <button @click="mobileMenu = !mobileMenu" class="md:hidden p-2 text-slate-400 hover:text-white rounded-lg border border-slate-800">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
        </div>

        <div x-show="mobileMenu" x-cloak class="md:hidden border-b border-slate-800 bg-slate-950 px-4 pt-3 pb-6 space-y-4">
            <a href="#inicio" @click="mobileMenu = false" class="block text-slate-300 hover:text-emerald-400 text-base font-medium">Inicio</a>
            <a href="#servicios" @click="mobileMenu = false" class="block text-slate-300 hover:text-emerald-400 text-base font-medium">Servicios y Módulos</a>
            <a href="#metricas" @click="mobileMenu = false" class="block text-slate-300 hover:text-emerald-400 text-base font-medium">Métricas</a>
            <div class="pt-4 border-t border-slate-800 flex flex-col gap-3">
                <button @click="openAuth = true; authMode = 'login'; mobileMenu = false" class="w-full text-center py-2.5 text-sm text-slate-300 border border-slate-800 rounded-xl font-medium">
                    Iniciar Sesión
                </button>
                <button @click="openAuth = true; authMode = 'register'; mobileMenu = false" class="w-full text-center py-2.5 text-sm font-semibold bg-emerald-500 text-slate-950 rounded-xl">
                    Registrarse
                </button>
            </div>
        </div>
    </header>

    <main class="flex-1">
        <!-- Hero Section -->
        <section id="inicio" class="relative py-20 lg:py-32 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto overflow-hidden">
            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[300px] bg-emerald-500/10 blur-[120px] pointer-events-none rounded-full"></div>

            <div class="text-center max-w-4xl mx-auto space-y-8 relative z-10">
                <span class="inline-flex items-center gap-2 px-4 py-1.5 text-xs font-semibold uppercase tracking-wider bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 rounded-full">
                    <span class="h-2 w-2 rounded-full bg-emerald-400 animate-ping"></span>
                    Sistema Digital de Perfilamiento Ambiental
                </span>

                <h1 class="text-4xl sm:text-6xl lg:text-7xl font-black tracking-tight text-white leading-tight">
                    Gestión sustentable y <br class="hidden sm:inline"/>
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-emerald-400 via-teal-300 to-cyan-400">
                        desarrollo ambiental comunitario
                    </span>
                </h1>

                <p class="text-lg sm:text-xl text-slate-400 leading-relaxed max-w-3xl mx-auto font-normal">
                    Plataforma para el censo socio-ambiental, registro comercial, declaración de desechos e indicadores de factibilidad en el Municipio Carrizal.
                </p>

                <div class="pt-4 flex flex-col sm:flex-row justify-center items-center gap-4">
                    <button @click="openAuth = true; authMode = 'register'" class="w-full sm:w-auto px-8 py-4 bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-bold text-base rounded-xl shadow-xl shadow-emerald-500/25 transition-all transform active:scale-95 cursor-pointer">
                        Realizar Declaración Comercial
                    </button>
                    <button @click="openAuth = true; authMode = 'login'" class="w-full sm:w-auto px-8 py-4 bg-slate-900 hover:bg-slate-800 text-slate-200 font-semibold text-base rounded-xl border border-slate-800 transition-colors cursor-pointer">
                        Acceso Institucional
                    </button>
                </div>
            </div>
        </section>

        <!-- Módulos -->
        <section id="servicios" class="py-20 bg-slate-900/50 border-y border-slate-800/80 px-4 sm:px-6 lg:px-8 relative">
            <div class="max-w-7xl mx-auto">
                <div class="text-center max-w-2xl mx-auto mb-16 space-y-3">
                    <h2 class="text-3xl sm:text-4xl font-extrabold text-white">Servicios Integrados</h2>
                    <p class="text-slate-400 text-base">Herramientas automatizadas para el perfilamiento de comercios e instituciones.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div class="bg-slate-950 border border-slate-800/80 rounded-3xl p-8 hover:border-emerald-500/40 transition-all duration-300 hover:-translate-y-1 shadow-xl">
                        <div class="h-14 w-14 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 flex items-center justify-center mb-6">
                            <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-white mb-3">Diagnóstico Comercial</h3>
                        <p class="text-slate-400 text-sm leading-relaxed mb-6">
                            Perfilamiento ambiental multi-paso para establecimientos comerciales y actividades económicas locales.
                        </p>
                    </div>

                    <div class="bg-slate-950 border border-slate-800/80 rounded-3xl p-8 hover:border-teal-500/40 transition-all duration-300 hover:-translate-y-1 shadow-xl">
                        <div class="h-14 w-14 rounded-2xl bg-teal-500/10 border border-teal-500/20 text-teal-400 flex items-center justify-center mb-6">
                            <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-white mb-3">Declaración de Desechos</h3>
                        <p class="text-slate-400 text-sm leading-relaxed mb-6">
                            Categorización de volumen de residuos sólidos, manejo de material orgánico y reciclaje.
                        </p>
                    </div>

                    <div class="bg-slate-950 border border-slate-800/80 rounded-3xl p-8 hover:border-cyan-500/40 transition-all duration-300 hover:-translate-y-1 shadow-xl">
                        <div class="h-14 w-14 rounded-2xl bg-cyan-500/10 border border-cyan-500/20 text-cyan-400 flex items-center justify-center mb-6">
                            <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-white mb-3">Censo y Métricas</h3>
                        <p class="text-slate-400 text-sm leading-relaxed mb-6">
                            Procesamiento de datos comunales y evaluación económica con reportes analíticos consolidados.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Métricas -->
        <section id="metricas" class="py-16 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 text-center">
                <div class="bg-slate-900/40 border border-slate-800 p-6 rounded-2xl">
                    <div class="text-3xl sm:text-4xl font-black text-emerald-400">100%</div>
                    <div class="text-xs font-medium text-slate-400 mt-1 uppercase tracking-wider">Digitalizado</div>
                </div>
                <div class="bg-slate-900/40 border border-slate-800 p-6 rounded-2xl">
                    <div class="text-3xl sm:text-4xl font-black text-teal-400">24/7</div>
                    <div class="text-xs font-medium text-slate-400 mt-1 uppercase tracking-wider">Disponibilidad</div>
                </div>
                <div class="bg-slate-900/40 border border-slate-800 p-6 rounded-2xl">
                    <div class="text-3xl sm:text-4xl font-black text-cyan-400">Carrizal</div>
                    <div class="text-xs font-medium text-slate-400 mt-1 uppercase tracking-wider">Municipio</div>
                </div>
                <div class="bg-slate-900/40 border border-slate-800 p-6 rounded-2xl">
                    <div class="text-3xl sm:text-4xl font-black text-emerald-400">+1000</div>
                    <div class="text-xs font-medium text-slate-400 mt-1 uppercase tracking-wider">Registros</div>
                </div>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer id="contacto" class="border-t border-slate-800 bg-slate-950 py-12 px-4 sm:px-6 lg:px-8 text-sm text-slate-400">
        <div class="max-w-7xl mx-auto flex flex-col sm:flex-row items-center justify-between text-xs text-slate-500 gap-4">
            <p>&copy; {{ date('Y') }} Alcaldía del Municipio Carrizal. Todos los derechos reservados.</p>
        </div>
    </footer>

    <!-- Modal de Autenticación -->
    <x-auth-modal />

</body>
</html>