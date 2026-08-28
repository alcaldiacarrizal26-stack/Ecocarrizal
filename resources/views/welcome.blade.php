<!DOCTYPE html>
<html lang="es" x-data="{ mobileMenu: false, openAuth: false, authMode: 'login' }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión Ambiental · Alcaldía de Carrizal</title>

    <!-- Tailwind CSS v4 CDN -->
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <!-- Alpine.js CDN -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        [x-cloak] { display: none !important; }

        /* Variables globales de la paleta de colores */
        :root {
            --color-primary: #05772a;
            --color-secondary: #0e7970;
            --color-mint: #78ad8b;
            --color-soft-green: #aecbc5;
            --color-dark-green: #347a4b;
            --color-teal: #66a198;
            --color-gray-light: #c5c6c6;
            --color-white: #ffffff;
            --color-black: #0a0a0a;
        }

        /* Clases utilitarias personalizadas para usar con Tailwind */
        .bg-primary { background-color: var(--color-primary); }
        .bg-primary\/10 { background-color: rgba(5, 119, 42, 0.1); }
        .bg-primary\/5 { background-color: rgba(5, 119, 42, 0.05); }
        .text-primary { color: var(--color-primary); }
        .border-primary { border-color: var(--color-primary); }
        .hover\:bg-primary:hover { background-color: var(--color-primary); }
        .hover\:text-primary:hover { color: var(--color-primary); }
        .hover\:border-primary:hover { border-color: var(--color-primary); }
        .focus\:ring-primary:focus { --tw-ring-color: var(--color-primary); }
        .focus\:border-primary:focus { border-color: var(--color-primary); }
        .shadow-primary\/20 { box-shadow: 0 4px 14px 0 rgba(5, 119, 42, 0.2); }
        .shadow-primary\/10 { box-shadow: 0 4px 14px 0 rgba(5, 119, 42, 0.1); }
        .bg-secondary { background-color: var(--color-secondary); }
        .text-secondary { color: var(--color-secondary); }
        .hover\:bg-secondary:hover { background-color: var(--color-secondary); }
        /* Agrega más según necesites */

        /* Gradiente personalizado */
        .bg-gradient-soft {
            background: linear-gradient(135deg, #f0f7f4 0%, #e4f0eb 100%);
        }
        .text-gradient-primary {
            background: linear-gradient(to right, var(--color-primary), var(--color-secondary), var(--color-mint));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
    </style>
</head>
<body class="bg-white text-slate-800 min-h-screen flex flex-col font-sans antialiased selection:bg-primary selection:text-white">

    <!-- ========== HEADER ========== -->
    <header class="bg-white/95 backdrop-blur-sm border-b border-slate-200/60 sticky top-0 z-40 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
            <!-- Logo -->
            <a href="#" class="flex items-center gap-3 group">
                <div class="h-11 w-11 rounded-2xl bg-primary flex items-center justify-center font-black text-white text-2xl shadow-md shadow-primary/10 group-hover:scale-105 transition-transform">
                    <!-- Aquí puedes poner el logo real -->
                    <img src="{{ asset('images/carrizal_logo.png') }}" alt="Logo Carrizal" class="h-10 w-10 object-contain">
                </div>
                <div class="flex flex-col">
                    <span class="font-bold text-xl tracking-tight text-slate-800 group-hover:text-primary transition-colors">Ecosocialismo</span>
                    <span class="text-[11px] text-primary font-semibold tracking-wider uppercase">Alcaldía de Carrizal</span>
                </div>
            </a>

            <!-- Navegación desktop -->
            <nav class="hidden md:flex items-center gap-8 text-sm font-medium text-slate-600">
                <a href="#inicio" class="hover:text-primary transition-colors">Inicio</a>
                <a href="#servicios" class="hover:text-primary transition-colors">Servicios</a>
                <a href="#beneficios" class="hover:text-primary transition-colors">Beneficios</a>
                <a href="#contacto" class="hover:text-primary transition-colors">Contacto</a>
            </nav>

            <!-- Botones de acción -->
            <div class="hidden md:flex items-center gap-4">
                <button @click="$dispatch('open-auth-modal', { mode: 'login' })" class="px-5 py-2.5 text-sm font-medium text-slate-600 hover:text-primary transition-colors cursor-pointer">
                    Iniciar Sesión
                </button>
                <button @click="$dispatch('open-auth-modal', { mode: 'register' })" class="px-5 py-2.5 text-sm font-semibold bg-primary hover:bg-secondary text-white rounded-xl transition-all shadow-md shadow-primary/20 hover:shadow-primary/30 transform active:scale-95 cursor-pointer">
                    Registrarse
                </button>
            </div>

            <!-- Botón menú móvil -->
            <button @click="mobileMenu = !mobileMenu" class="md:hidden p-2 text-slate-500 hover:text-primary rounded-lg border border-slate-200">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
        </div>

        <!-- Menú móvil desplegable -->
        <div x-show="mobileMenu" x-cloak class="md:hidden border-t border-slate-200 bg-white px-4 pt-3 pb-6 space-y-4 shadow-lg">
            <a href="#inicio" @click="mobileMenu = false" class="block text-slate-700 hover:text-primary text-base font-medium">Inicio</a>
            <a href="#servicios" @click="mobileMenu = false" class="block text-slate-700 hover:text-primary text-base font-medium">Servicios</a>
            <a href="#beneficios" @click="mobileMenu = false" class="block text-slate-700 hover:text-primary text-base font-medium">Beneficios</a>
            <a href="#contacto" @click="mobileMenu = false" class="block text-slate-700 hover:text-primary text-base font-medium">Contacto</a>
            <div class="pt-4 border-t border-slate-200 flex flex-col gap-3">
                <button @click="openAuth = true; authMode = 'login'; mobileMenu = false" class="w-full text-center py-2.5 text-sm text-slate-600 border border-slate-300 rounded-xl font-medium hover:bg-slate-50 cursor-pointer">
                    Iniciar Sesión
                </button>
                <button @click="openAuth = true; authMode = 'register'; mobileMenu = false" class="w-full text-center py-2.5 text-sm font-semibold bg-primary text-white rounded-xl hover:bg-secondary cursor-pointer">
                    Registrarse
                </button>
            </div>
        </div>
    </header>

    <!-- ========== MAIN ========== -->
    <main class="flex-1">
        <!-- HERO -->
        <section id="inicio" class="relative py-20 lg:py-32 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto overflow-hidden">
            <!-- Elementos decorativos -->
            <div class="absolute top-0 right-0 w-96 h-96 bg-primary/5 rounded-full blur-3xl -translate-y-1/2 translate-x-1/3 pointer-events-none"></div>
            <div class="absolute bottom-0 left-0 w-72 h-72 bg-secondary/5 rounded-full blur-2xl translate-y-1/2 -translate-x-1/4 pointer-events-none"></div>

            <div class="text-center max-w-4xl mx-auto space-y-8 relative z-10">
                <span class="inline-flex items-center gap-2 px-4 py-1.5 text-xs font-semibold uppercase tracking-wider bg-primary/10 text-primary border border-primary/20 rounded-full">
                    <span class="h-2 w-2 rounded-full bg-primary animate-pulse"></span>
                    Sistema Digital de Perfilamiento Ambiental
                </span>

                <h1 class="text-4xl sm:text-6xl lg:text-7xl font-extrabold tracking-tight text-slate-800 leading-tight">
                    Gestión sustentable y <br class="hidden sm:inline"/>
                    <span class="text-gradient-primary">desarrollo ambiental comunitario</span>
                </h1>

                <p class="text-lg sm:text-xl text-slate-600 leading-relaxed max-w-3xl mx-auto font-normal">
                    Plataforma para el censo socio‑ambiental, registro comercial, declaración de desechos e indicadores de factibilidad en el Municipio Carrizal.
                </p>

                <div class="pt-4 flex flex-col sm:flex-row justify-center items-center gap-4">
                    <button @click="openAuth = true; authMode = 'register'" class="w-full sm:w-auto px-8 py-4 bg-primary hover:bg-secondary text-white font-bold text-base rounded-xl shadow-xl shadow-primary/20 transition-all transform active:scale-95 cursor-pointer">
                        Realizar Declaración Comercial
                    </button>
                    <button @click="openAuth = true; authMode = 'login'" class="w-full sm:w-auto px-8 py-4 bg-white hover:bg-slate-50 text-slate-700 font-semibold text-base rounded-xl border border-slate-300 transition-colors shadow-sm cursor-pointer">
                        Acceso Institucional
                    </button>
                </div>
            </div>
        </section>

        <!-- SERVICIOS -->
        <section id="servicios" class="py-20 bg-slate-50/80 border-y border-slate-200/60 px-4 sm:px-6 lg:px-8">
            <div class="max-w-7xl mx-auto">
                <div class="text-center max-w-2xl mx-auto mb-16 space-y-3">
                    <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-800">Servicios Integrados</h2>
                    <p class="text-slate-600 text-base">Herramientas automatizadas para el perfilamiento de comercios e instituciones.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <!-- Tarjeta 1 -->
                    <div class="bg-white border border-slate-200/80 rounded-3xl p-8 shadow-sm hover:shadow-md hover:border-primary/30 transition-all duration-300 hover:-translate-y-1">
                        <div class="h-14 w-14 rounded-2xl bg-primary/10 border border-primary/20 text-primary flex items-center justify-center mb-6">
                            <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-slate-800 mb-3">Diagnóstico Comercial</h3>
                        <p class="text-slate-600 text-sm leading-relaxed">Perfilamiento ambiental multi‑paso para establecimientos comerciales y actividades económicas locales.</p>
                    </div>

                    <!-- Tarjeta 2 -->
                    <div class="bg-white border border-slate-200/80 rounded-3xl p-8 shadow-sm hover:shadow-md hover:border-secondary/30 transition-all duration-300 hover:-translate-y-1">
                        <div class="h-14 w-14 rounded-2xl bg-secondary/10 border border-secondary/20 text-secondary flex items-center justify-center mb-6">
                            <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-slate-800 mb-3">Declaración de Desechos</h3>
                        <p class="text-slate-600 text-sm leading-relaxed">Categorización de volumen de residuos sólidos, manejo de material orgánico y reciclaje.</p>
                    </div>

                    <!-- Tarjeta 3 -->
                    <div class="bg-white border border-slate-200/80 rounded-3xl p-8 shadow-sm hover:shadow-md hover:border-primary/30 transition-all duration-300 hover:-translate-y-1">
                        <div class="h-14 w-14 rounded-2xl bg-primary/10 border border-primary/20 text-primary flex items-center justify-center mb-6">
                            <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-slate-800 mb-3">Censo y Métricas</h3>
                        <p class="text-slate-600 text-sm leading-relaxed">Procesamiento de datos comunales y evaluación económica con reportes analíticos consolidados.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- BENEFICIOS -->
        <section id="beneficios" class="py-20 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
            <div class="grid md:grid-cols-2 gap-12 items-center">
                <div>
                    <span class="inline-block px-3 py-1 text-xs font-semibold uppercase tracking-wider bg-primary/10 text-primary rounded-full">Ventajas</span>
                    <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-800 mt-3">¿Por qué usar nuestra plataforma?</h2>
                    <p class="text-slate-600 text-base mt-4 leading-relaxed">Centralizamos los procesos de gestión ambiental, reduciendo tiempos y mejorando la toma de decisiones con datos precisos y actualizados.</p>
                    <ul class="mt-6 space-y-4">
                        <li class="flex items-start gap-3">
                            <span class="inline-block mt-1 w-5 h-5 rounded-full bg-primary/20 text-primary flex items-center justify-center text-xs font-bold">✓</span>
                            <span class="text-slate-700">Acceso 24/7 desde cualquier dispositivo.</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="inline-block mt-1 w-5 h-5 rounded-full bg-primary/20 text-primary flex items-center justify-center text-xs font-bold">✓</span>
                            <span class="text-slate-700">Declaraciones digitales con respaldo legal.</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="inline-block mt-1 w-5 h-5 rounded-full bg-primary/20 text-primary flex items-center justify-center text-xs font-bold">✓</span>
                            <span class="text-slate-700">Reportes automáticos para la toma de decisiones.</span>
                        </li>
                    </ul>
                    <div class="mt-8">
                        <button @click="openAuth = true; authMode = 'register'" class="inline-block px-6 py-3 bg-primary hover:bg-secondary text-white font-semibold rounded-xl shadow-md shadow-primary/20 transition-colors cursor-pointer">
                            Comenzar ahora
                        </button>
                    </div>
                </div>
                <div class="relative">
                    <div class="aspect-square rounded-3xl bg-gradient-soft border border-slate-200 shadow-lg flex items-center justify-center text-slate-400 text-6xl font-bold">
                        <span class="text-primary/30 text-8xl">♻️</span>
                    </div>
                </div>
            </div>
        </section>

        <!-- CALL TO ACTION -->
        <section class="py-16 px-4 sm:px-6 lg:px-8 bg-primary/5 border-y border-slate-200/60">
            <div class="max-w-4xl mx-auto text-center">
                <h2 class="text-3xl font-extrabold text-slate-800">¿Listo para contribuir al desarrollo sustentable?</h2>
                <p class="text-slate-600 mt-3">Regístrate y forma parte del censo ambiental de Carrizal.</p>
                <div class="mt-6">
                    <button @click="openAuth = true; authMode = 'register'" class="px-8 py-3 bg-primary hover:bg-secondary text-white font-semibold rounded-xl shadow-md shadow-primary/20 transition-colors inline-block cursor-pointer">
                        Crear cuenta
                    </button>
                </div>
            </div>
        </section>
    </main>

    <!-- ========== FOOTER ========== -->
    <footer id="contacto" class="border-t border-slate-200 bg-white py-12 px-4 sm:px-6 lg:px-8 text-sm text-slate-500">
        <div class="max-w-7xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-4">
            <p class="text-xs">&copy; {{ date('Y') }} Alcaldía del Municipio Carrizal. Todos los derechos reservados.</p>
            <div class="flex gap-6 text-xs">
                <a href="#" class="hover:text-primary transition-colors">Política de Privacidad</a>
                <a href="#" class="hover:text-primary transition-colors">Términos de Uso</a>
            </div>
        </div>
    </footer>

    <!-- ========== MODAL DE AUTENTICACIÓN ========== -->
    @include('components.auth-modal')

</body>
</html>