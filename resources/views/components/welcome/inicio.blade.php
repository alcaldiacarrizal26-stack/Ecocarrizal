@php
    // Insertar aquí las rutas locales (ej: asset('images/carrizal_1.jpg')) o URLs públicas de las imágenes
    $carruselImagenes = [
        [
            'url' => asset('images/imagen1.jpg'), // <-- Ruta Imagen 1
            'titulo' => 'Carrizal Ecosocialista',
            'subtitulo' => 'Compromiso ambiental y desarrollo sustentable'
        ],
        [
            'url' => asset('images/imagen2.jpg'), // <-- Ruta Imagen 2
            'titulo' => 'Gestión Territorial',
            'subtitulo' => 'Atención directa a nuestras comunas y sectores'
        ],
        [
            'url' => asset('images/imagen3.png'), // <-- Ruta Imagen 3
            'titulo' => 'Fiscalización y Residuos',
            'subtitulo' => 'Acompañamiento a la actividad comercial e industrial'
        ],
        [
            'url' => asset('images/imagen4.png'), // <-- Ruta Imagen 4
            'titulo' => 'Participación Ciudadana',
            'subtitulo' => 'Seguimiento y control de incidencias comunitarias'
        ],
    ];
@endphp

<section id="inicio" class="relative py-20 lg:py-28 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto overflow-hidden">
    <div class="absolute top-0 right-0 w-96 h-96 bg-primary/5 rounded-full blur-3xl -translate-y-1/2 translate-x-1/3 pointer-events-none"></div>
    <div class="absolute bottom-0 left-0 w-72 h-72 bg-secondary/5 rounded-full blur-2xl translate-y-1/2 -translate-x-1/4 pointer-events-none"></div>

    <div class="grid lg:grid-cols-2 gap-12 items-center relative z-10">
        <!-- Columna Izquierda: Texto e Identidad -->
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

        <!-- Columna Derecha: Carrusel Interactivo de 4 Imágenes -->
        <div class="relative" 
             x-data="{ 
                 activeSlide: 0, 
                 slides: {{ json_encode($carruselImagenes) }},
                 timer: null,
                 startAutoPlay() {
                     this.timer = setInterval(() => {
                         this.activeSlide = (this.activeSlide + 1) % this.slides.length;
                     }, 5000);
                 },
                 stopAutoPlay() {
                     clearInterval(this.timer);
                 }
             }" 
             x-init="startAutoPlay()" 
             @mouseenter="stopAutoPlay()" 
             @mouseleave="startAutoPlay()">

            <!-- Marco decorativo exterior -->
            <div class="relative rounded-3xl overflow-hidden shadow-2xl border border-slate-200/80 bg-slate-900 aspect-[4/3] group">
                
                <!-- Diapositivas con transición suave -->
                <template x-for="(slide, index) in slides" :key="index">
                    <div x-show="activeSlide === index"
                         x-transition:enter="transition ease-out duration-700"
                         x-transition:enter-start="opacity-0 scale-105"
                         x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-500"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         class="absolute inset-0 w-full h-full">
                        
                        <!-- Imagen de fondo -->
                        <img :src="slide.url" 
                             :alt="slide.titulo" 
                             class="w-full h-full object-cover" 
                             onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1542601906990-b4d3fb778b09?q=80&w=1000&auto=format&fit=crop';">
                        
                        <!-- Overlay con Degradado Superior/Inferior -->
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-950/85 via-slate-950/20 to-transparent"></div>

                        <!-- Leyenda de Texto sobre la Imagen -->
                        <div class="absolute bottom-0 inset-x-0 p-6 text-white space-y-1">
                            <span class="inline-block px-2.5 py-1 bg-emerald-500/80 backdrop-blur-md text-[10px] font-extrabold uppercase tracking-widest rounded-md text-white">
                                Municipio Carrizal
                            </span>
                            <h3 class="text-xl font-bold leading-tight" x-text="slide.titulo"></h3>
                            <p class="text-xs text-slate-200 font-medium line-clamp-1" x-text="slide.subtitulo"></p>
                        </div>
                    </div>
                </template>

                <!-- Botón Anterior -->
                <button @click="activeSlide = (activeSlide - 1 + slides.length) % slides.length" 
                        type="button" 
                        class="absolute left-3 top-1/2 -translate-y-1/2 w-10 h-10 rounded-full bg-slate-900/40 hover:bg-slate-900/80 text-white backdrop-blur-md flex items-center justify-center transition opacity-0 group-hover:opacity-100 cursor-pointer border border-white/20">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
                    </svg>
                </button>

                <!-- Botón Siguiente -->
                <button @click="activeSlide = (activeSlide + 1) % slides.length" 
                        type="button" 
                        class="absolute right-3 top-1/2 -translate-y-1/2 w-10 h-10 rounded-full bg-slate-900/40 hover:bg-slate-900/80 text-white backdrop-blur-md flex items-center justify-center transition opacity-0 group-hover:opacity-100 cursor-pointer border border-white/20">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                    </svg>
                </button>

                <!-- Indicadores de Posición (Puntos) -->
                <div class="absolute top-4 right-4 flex items-center gap-1.5 z-20 bg-slate-950/40 backdrop-blur-md px-3 py-1.5 rounded-full border border-white/10">
                    <template x-for="(slide, index) in slides" :key="index">
                        <button @click="activeSlide = index" 
                                type="button" 
                                :class="activeSlide === index ? 'w-5 bg-emerald-400' : 'w-1.5 bg-white/50 hover:bg-white'" 
                                class="h-1.5 rounded-full transition-all duration-300 cursor-pointer"></button>
                    </template>
                </div>

            </div>
        </div>
    </div>
</section>