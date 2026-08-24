{{-- resources/views/components/hero.blade.php --}}
<div 
    x-data="{
        currentSlide: 0,
        slidesCount: 4,
        timer: null,
        init() {
            this.timer = setInterval(() => {
                this.nextSlide();
            }, 5000);
        },
        nextSlide() {
            this.currentSlide = (this.currentSlide + 1) % this.slidesCount;
        },
        prevSlide() {
            this.currentSlide = (this.currentSlide - 1 + this.slidesCount) % this.slidesCount;
        }
    }"
    class="w-full"
>
    {{-- --- HERO SECTION --- --}}
    <section id="inicio" class="relative pt-8 pb-12 md:pt-12 md:pb-16 bg-[#fafcfb] overflow-hidden w-full">
        <div class="w-full max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-12">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-center">
                
                {{-- Left Column --}}
                <div class="lg:col-span-6 flex flex-col justify-center z-10 space-y-6">
                    <h1 class="text-3xl sm:text-4xl md:text-5xl lg:text-[2.85rem] font-bold text-gray-900 tracking-tight leading-[1.18]">
                        Gestión ambiental responsable para una <br />
                        <span class="text-[#0d5238]">Carrizal más sostenible</span>
                    </h1>
                    <p class="text-base sm:text-lg text-gray-600 max-w-xl font-normal leading-relaxed">
                        Plataforma oficial para la gestión de trámites, permisos, inspecciones y certificaciones ambientales.
                    </p>
                    <div class="pt-2 flex flex-wrap items-center gap-4">
                        <button
                            type="button"
                            @click="$dispatch('open-login-modal')"
                            class="inline-flex items-center justify-center px-7 py-3.5 rounded-xl text-sm sm:text-base font-bold text-white bg-[#0d5238] hover:bg-[#073c28] active:scale-98 transition-all shadow-sm cursor-pointer"
                        >
                            <span>Realizar una Solicitud</span>
                        </button>
                    </div>
                </div>

                {{-- Right Column: Carousel con Curvas Decorativas SVG --}}
                <div class="lg:col-span-6 flex justify-center lg:justify-end relative w-full">
                    <div class="relative w-full max-w-4xl p-4 sm:p-6">
                        
                        {{-- LÍNEAS SVG DECORATIVAS --}}
                        <div class="absolute inset-y-0 -left-12 sm:-left-24 right-0 pointer-events-none z-0 overflow-visible opacity-85">
                            <svg viewBox="0 0 600 400" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-full" preserveAspectRatio="none">
                                <path d="M 280,-20 C 60,80 60,320 280,420" stroke="#22c55e" stroke-width="28" stroke-opacity="0.2" stroke-linecap="round"/>
                                <path d="M 240,-15 C 30,85 30,315 240,415" stroke="#84cc16" stroke-width="18" stroke-opacity="0.3" stroke-linecap="round"/>
                                <path d="M 210,-10 C 0,90 0,310 210,410" stroke="#0d5238" stroke-width="3.5" stroke-opacity="0.6" stroke-linecap="round"/>
                                <path d="M 180,-5 C -20,95 -20,305 180,405" stroke="#15803d" stroke-width="2.5" stroke-opacity="0.7" stroke-linecap="round"/>
                                <path d="M 150,0 C -40,100 -40,300 150,400" stroke="#84cc16" stroke-width="2" stroke-opacity="0.8" stroke-linecap="round"/>
                                <path d="M 120,10 C -60,110 -60,290 120,390" stroke="#a3e635" stroke-width="1.5" stroke-opacity="0.75" stroke-linecap="round"/>
                                <path d="M 90,20 C -80,120 -80,280 90,380" stroke="#16a34a" stroke-width="1" stroke-opacity="0.5" stroke-linecap="round"/>
                            </svg>
                        </div>

                        {{-- CONTENEDOR DEL CARRUSEL --}}
                        <div class="w-full aspect-16/10 rounded-l-[340px] md:rounded-l-[420px] rounded-r-3xl overflow-hidden shadow-[0_25px_60px_-15px_rgba(0,0,0,0.4)] border-4 border-white bg-emerald-950/20 relative group z-10">
                            
                            {{-- Slide 1 --}}
                            <div 
                                class="absolute inset-0 transition-opacity duration-700 ease-in-out"
                                :class="currentSlide === 0 ? 'opacity-100 z-10' : 'opacity-0 z-0 pointer-events-none'"
                            >
                                <img src="{{ asset('images/imagen1.jpg') }}" alt="Montañas y valles verdes del Municipio Carrizal" class="w-full h-full object-cover object-center group-hover:scale-105 transition-transform duration-700 ease-out" />
                                <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-black/10"></div>
                            </div>

                            {{-- Slide 2 --}}
                            <div 
                                class="absolute inset-0 transition-opacity duration-700 ease-in-out"
                                :class="currentSlide === 1 ? 'opacity-100 z-10' : 'opacity-0 z-0 pointer-events-none'"
                            >
                                <img src="{{ asset('images/imagen8.jpg') }}" alt="Bosques y vegetación nativa protegida" class="w-full h-full object-cover object-center group-hover:scale-105 transition-transform duration-700 ease-out" />
                                <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-black/10"></div>
                            </div>

                            {{-- Slide 3 --}}
                            <div 
                                class="absolute inset-0 transition-opacity duration-700 ease-in-out"
                                :class="currentSlide === 2 ? 'opacity-100 z-10' : 'opacity-0 z-0 pointer-events-none'"
                            >
                                <img src="{{ asset('images/imagen3.png') }}" alt="Programas de reforestación y conservación ambiental" class="w-full h-full object-cover object-center group-hover:scale-105 transition-transform duration-700 ease-out" />
                                <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-black/10"></div>
                            </div>

                            {{-- Slide 4 --}}
                            <div 
                                class="absolute inset-0 transition-opacity duration-700 ease-in-out"
                                :class="currentSlide === 3 ? 'opacity-100 z-10' : 'opacity-0 z-0 pointer-events-none'"
                            >
                                <img src="{{ asset('images/imagen4.png') }}" alt="Ecosistemas de neblina y cuencas hídricas" class="w-full h-full object-cover object-center group-hover:scale-105 transition-transform duration-700 ease-out" />
                                <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-black/10"></div>
                            </div>

                            {{-- Botones de Navegación --}}
                            <button
                                type="button"
                                @click="prevSlide()"
                                aria-label="Anterior"
                                class="absolute left-4 top-1/2 -translate-y-1/2 p-2 rounded-full bg-black/40 text-white hover:bg-black/70 backdrop-blur-sm transition-all cursor-pointer opacity-0 group-hover:opacity-100 z-30"
                            >
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                            </button>
                            <button
                                type="button"
                                @click="nextSlide()"
                                aria-label="Siguiente"
                                class="absolute right-4 top-1/2 -translate-y-1/2 p-2 rounded-full bg-black/40 text-white hover:bg-black/70 backdrop-blur-sm transition-all cursor-pointer opacity-0 group-hover:opacity-100 z-30"
                            >
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </button>

                            {{-- Indicadores de Diapositiva --}}
                            <div class="absolute bottom-4 right-6 z-30 flex items-center gap-1.5">
                                <template x-for="(i, idx) in slidesCount" :key="idx">
                                    <button
                                        type="button"
                                        @click="currentSlide = idx"
                                        :aria-label="`Ir a imagen ${idx + 1}`"
                                        :class="idx === currentSlide ? 'w-6 bg-white' : 'w-2 bg-white/50 hover:bg-white/80'"
                                        class="h-2 rounded-full transition-all cursor-pointer"
                                    ></button>
                                </template>
                            </div>

                        </div>

                        {{-- Resplandor posterior --}}
                        <div class="absolute -z-10 -bottom-6 -right-6 w-72 h-72 bg-emerald-100/60 rounded-full blur-3xl pointer-events-none"></div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    {{-- --- FEATURE CARDS SECTION --- --}}
    <section class="py-10 sm:py-12 -mt-4 bg-[#fafcfb] w-full border-t border-gray-100/80">
        <div class="w-full max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-12">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                
                {{-- Card 1 --}}
                <div
                    @click="$dispatch('open-login-modal', { category: 'permisos' })"
                    class="group bg-[#f8faf9] rounded-2xl p-6 border border-gray-200/70 hover:border-emerald-700/40 hover:bg-white hover:shadow-md transition-all duration-200 cursor-pointer flex flex-col items-center text-center justify-between"
                >
                    <div class="flex flex-col items-center text-center w-full">
                        <div class="w-14 h-14 rounded-full flex items-center justify-center transition-transform group-hover:scale-105 mb-5 mx-auto bg-[#0d5238] text-white">
                            <svg class="w-8 h-8 stroke-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                        <h3 class="text-base sm:text-lg font-bold text-gray-900 group-hover:text-[#0d5238] transition-colors mb-2 leading-snug">
                            Permisos Ambientales
                        </h3>
                        <p class="text-xs sm:text-sm text-gray-600 leading-relaxed font-normal">
                            Poda, tala, recolección y autorizaciones.
                        </p>
                    </div>
                </div>

                {{-- Card 2 --}}
                <div
                    @click="$dispatch('open-login-modal', { category: 'residuos' })"
                    class="group bg-[#f8faf9] rounded-2xl p-6 border border-gray-200/70 hover:border-emerald-700/40 hover:bg-white hover:shadow-md transition-all duration-200 cursor-pointer flex flex-col items-center text-center justify-between"
                >
                    <div class="flex flex-col items-center text-center w-full">
                        <div class="w-14 h-14 rounded-full flex items-center justify-center transition-transform group-hover:scale-105 mb-5 mx-auto bg-[#0d5238] text-white">
                            <svg class="w-8 h-8 stroke-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </div>
                        <h3 class="text-base sm:text-lg font-bold text-gray-900 group-hover:text-[#0d5238] transition-colors mb-2 leading-snug">
                            Gestión de Residuos
                        </h3>
                        <p class="text-xs sm:text-sm text-gray-600 leading-relaxed font-normal">
                            Registros RACDA, caracterizaciones y conformidades.
                        </p>
                    </div>
                </div>

                {{-- Card 3 --}}
                <div
                    @click="$dispatch('open-login-modal', { category: 'reportes' })"
                    class="group bg-[#f8faf9] rounded-2xl p-6 border border-gray-200/70 hover:border-emerald-700/40 hover:bg-white hover:shadow-md transition-all duration-200 cursor-pointer flex flex-col items-center text-center justify-between"
                >
                    <div class="flex flex-col items-center text-center w-full">
                        <div class="w-14 h-14 rounded-full flex items-center justify-center transition-transform group-hover:scale-105 mb-5 mx-auto bg-[#0d5238] text-white">
                            <svg class="w-8 h-8 stroke-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                        </div>
                        <h3 class="text-base sm:text-lg font-bold text-gray-900 group-hover:text-[#0d5238] transition-colors mb-2 leading-snug">
                            Reportes y Denuncias
                        </h3>
                        <p class="text-xs sm:text-sm text-gray-600 leading-relaxed font-normal">
                            Focos de contaminación y protección animal.
                        </p>
                    </div>
                </div>

                {{-- Card 4 --}}
                <div
                    @click="$dispatch('open-login-modal', { category: 'compensacion' })"
                    class="group bg-[#f8faf9] rounded-2xl p-6 border border-gray-200/70 hover:border-emerald-700/40 hover:bg-white hover:shadow-md transition-all duration-200 cursor-pointer flex flex-col items-center text-center justify-between"
                >
                    <div class="flex flex-col items-center text-center w-full">
                        <div class="w-14 h-14 rounded-full flex items-center justify-center transition-transform group-hover:scale-105 mb-5 mx-auto bg-[#0d5238] text-white">
                            <svg class="w-8 h-8 stroke-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                        </div>
                        <h3 class="text-base sm:text-lg font-bold text-gray-900 group-hover:text-[#0d5238] transition-colors mb-2 leading-snug">
                            Compensación Ambiental
                        </h3>
                        <p class="text-xs sm:text-sm text-gray-600 leading-relaxed font-normal">
                            Reforestación y reposición de áreas verdes.
                        </p>
                    </div>
                </div>

            </div>
        </div>
    </section>
</div>