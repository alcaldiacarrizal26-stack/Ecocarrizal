<section id="inicio" class="relative py-20 lg:py-28 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto overflow-hidden">
    <div class="absolute top-0 right-0 w-96 h-96 bg-primary/5 rounded-full blur-3xl -translate-y-1/2 translate-x-1/3 pointer-events-none"></div>
    <div class="absolute bottom-0 left-0 w-72 h-72 bg-secondary/5 rounded-full blur-2xl translate-y-1/2 -translate-x-1/4 pointer-events-none"></div>

    <div class="grid lg:grid-cols-2 gap-12 items-center relative z-10">
        <div class="space-y-6 text-center lg:text-left">
            <span class="inline-flex items-center gap-2 px-4 py-1.5 text-xs font-semibold uppercase tracking-wider bg-primary/10 text-primary border border-primary/20 rounded-full">
                <span class="h-2 w-2 rounded-full bg-primary animate-pulse"></span>
                Sistema Digital de Perfilamiento Ambiental
            </span>

            <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight text-slate-800 leading-tight">
                Gestión sustentable y <br class="hidden sm:inline"/>
                <span class="text-gradient-primary">desarrollo ambiental comunitario</span>
            </h1>

            <p class="text-base sm:text-lg text-slate-600 leading-relaxed font-normal">
                Plataforma oficial para el censo socio‑ambiental, registro comercial, declaración de desechos e indicadores de factibilidad en el Municipio Carrizal.
            </p>

            <div class="pt-2 flex flex-col sm:flex-row justify-center lg:justify-start items-center gap-4">
                <a href="{{ route('register') }}" class="w-full sm:w-auto px-7 py-3.5 bg-primary hover:bg-secondary text-white font-bold text-sm rounded-xl shadow-xl shadow-primary/20 transition-all transform active:scale-95 text-center cursor-pointer">
                    Realizar Declaración Comercial
                </a>
                <a href="{{ route('login') }}" class="w-full sm:w-auto px-7 py-3.5 bg-white hover:bg-slate-50 text-slate-700 font-semibold text-sm rounded-xl border border-slate-300 transition-colors shadow-sm text-center cursor-pointer">
                    Acceso Institucional
                </a>
            </div>
        </div>

        <!-- Imagen de presentación principal -->
        <div class="relative">
            <div class="relative rounded-3xl overflow-hidden shadow-2xl border border-slate-200/80 bg-slate-100 aspect-[4/3] flex items-center justify-center">
                <!-- Puedes cambiar este div o insertar tu etiqueta <img> con una foto institucional real -->
                <div class="absolute inset-0 bg-gradient-to-tr from-primary/20 to-secondary/10 mix-blend-multiply z-10"></div>
                <div class="text-center p-6 relative z-20 space-y-2">
                    <span class="text-6xl">🌲🏙️</span>
                    <p class="text-xs font-bold text-slate-700 uppercase tracking-wider">Municipio Carrizal Ecosocialista</p>
                </div>
            </div>
        </div>
    </div>
</section>