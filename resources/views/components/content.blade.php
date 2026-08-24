{{-- resources/views/components/content.blade.php --}}
@php
    $servicesData = [
        [
            'id' => 'poda_tala',
            'categoria' => 'permisos',
            'costo' => 'Gratuito (Sujeto a reposición de especies)',
            'titulo' => 'Permiso de Poda y Tala de Árboles',
            'descripcion' => 'Evaluación técnica e inspección botánica para poda fitosanitaria o tala justificada en propiedad privada o espacio público de Carrizal.',
            'tiempo' => '3 a 5 días hábiles',
            'imagen' => 'images/imagen5.jpg',
            'requisitos' => [
                'Copia de la Cédula de Identidad del solicitante',
                'Documento de propiedad o carta de vecindad del inmueble',
                'Fotografías claras del árbol o especie vegetal',
                'Carta explicativa con justificación de seguridad o infraestructura'
            ]
        ],
        [
            'id' => 'racda_municipal',
            'categoria' => 'residuos',
            'costo' => 'Tasa Administrativa Municipal',
            'titulo' => 'Registro RACDA Municipal y Caracterización',
            'descripcion' => 'Registro de Actividades Capaces de Degradar el Ambiente para empresas, comercios e industrias operando dentro del municipio Carrizal.',
            'tiempo' => '5 a 10 días hábiles',
            'imagen' => 'images/imagen7.jpg',
            'requisitos' => [
                'Copia de RIF Comercial actualizado',
                'Patente de Industria y Comercio de Carrizal',
                'Plan de Manejo de Residuos Peligrosos y no Peligrosos',
                'Certificado de Última Inspección de Bomberos'
            ]
        ],
        [
            'id' => 'denuncia_foco',
            'categoria' => 'reportes',
            'costo' => 'Gratuito',
            'titulo' => 'Reporte de Focos de Contaminación y Vertederos Ilegales',
            'descripcion' => 'Atención inmediata ante acumulación indebida de desechos, quema no autorizada de basura o contaminación sónica e hídrica.',
            'tiempo' => 'Atención prioritaria 24-48 horas',
            'imagen' => 'images/imagen8.jpg',
            'requisitos' => [
                'Ubicación exacta o punto de referencia en Carrizal',
                'Descripción detallada del foco o incidente',
                'Fotografía o video de respaldo (Opcional)',
                'Datos del denunciante (Garantía de confidencialidad protegida)'
            ]
        ],
        [
            'id' => 'reforestacion_donacion',
            'categoria' => 'compensacion',
            'costo' => 'Voluntario / Donación de insumos',
            'titulo' => 'Programa de Compensación y Plantatón Carrizal',
            'descripcion' => 'Registro para donación de plantas autóctonas, adopción de jardines comunitarios y siembra programada de especies ornamentales y forestales.',
            'tiempo' => 'Inmediato / Convocatoria programada',
            'imagen' => 'images/imagen6.jpg',
            'requisitos' => [
                'Formulario de voluntario o donante registrado',
                'Indicar disponibilidad de espacio o número de plantas a aportar',
                'Compromiso de mantenimiento inicial para especies adoptadas'
            ]
        ]
    ];
@endphp

<div class="w-full">
    {{-- --- SERVICES & TRAMITES SECTION --- --}}
    <section id="servicios" class="py-16 bg-white border-t border-slate-100 w-full">
        <div class="w-full max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-12">
            
            <div class="text-center max-w-3xl mx-auto mb-12">
                <h2 class="text-2xl sm:text-3xl font-bold text-[#0d5238] tracking-tight">
                    Servicios y Tramitaciones Ambientales
                </h2>
                <p class="mt-3 text-base text-gray-600">
                    Consulte requisitos, tiempos de respuesta e inicie sus solicitudes en línea con validez legal.
                </p>
            </div>

            {{-- Services Grid --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($servicesData as $service)
                    <div class="group relative bg-[#f8faf9] rounded-2xl p-7 border border-gray-200/80 shadow-sm hover:shadow-md transition-all flex flex-col justify-between">
                        
                        {{-- Layout horizontal --}}
                        <div class="flex flex-row gap-4">
                            <div class="flex-1 flex flex-col min-w-0">
                                <span class="shrink-0 w-fit text-[10px] font-semibold px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-900 mb-2">
                                    {{ $service['costo'] }}
                                </span>
                                <div class="flex items-start justify-between gap-2 mb-2">
                                    <h3 class="text-base font-bold text-gray-900 leading-snug">
                                        {{ $service['titulo'] }}
                                    </h3>
                                </div>

                                <p class="text-xs text-gray-600 mb-2 leading-relaxed line-clamp-3">
                                    {{ $service['descripcion'] }}
                                </p>

                                <div class="mt-auto pt-2 border-t border-gray-200/80 flex items-center justify-between gap-2">
                                    <div class="flex items-center gap-1.5 text-[10px] text-gray-500 font-medium">
                                        <svg class="w-3 h-3 text-[#0d5238]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        <span>{{ $service['tiempo'] }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="relative w-24 h-24 sm:w-28 sm:h-28 md:w-32 md:h-32 shrink-0 rounded-xl overflow-hidden bg-gray-100 group-hover:scale-105 transition-transform duration-300">
                                <img
                                    src="{{ asset($service['imagen']) }}"
                                    alt="{{ $service['titulo'] }}"
                                    class="w-full h-full object-cover"
                                />
                            </div>
                        </div>

                        {{-- Tarjeta flotante de requisitos (Flotante en Hover) --}}
                        <div class="absolute inset-0 pointer-events-none opacity-0 group-hover:opacity-100 group-hover:pointer-events-auto transition-opacity duration-300 z-10 rounded-2xl">
                            <div class="absolute inset-2 bg-white rounded-xl shadow-2xl border border-gray-200/80 p-6 overflow-y-auto flex flex-col justify-between">
                                <div>
                                    <h4 class="text-xs font-bold text-gray-700 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                        <svg class="w-4 h-4 text-[#0d5238]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        <span>Requisitos Obligatorios</span>
                                    </h4>
                                    <ul class="space-y-1.5 text-xs text-gray-600">
                                        @foreach ($service['requisitos'] as $req)
                                            <li class="flex items-start gap-2">
                                                <svg class="w-3.5 h-3.5 text-[#0d5238] shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                <span>{{ $req }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                                <button
                                    type="button"
                                    @click="$dispatch('open-login-modal', { category: '{{ $service['categoria'] }}', subType: '{{ $service['id'] }}' })"
                                    class="mt-3 w-full py-2 bg-[#0d5238] text-white text-xs font-bold rounded-xl hover:bg-[#073825] active:scale-98 transition-all flex items-center justify-center gap-1.5 cursor-pointer shadow-xs"
                                >
                                    <span>Iniciar este Trámite</span>
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                </button>
                            </div>
                        </div>

                    </div>
                @endforeach
            </div>

        </div>
    </section>

    {{-- --- TRANSPARENCIA SECTION --- --}}
    <section id="transparencia" class="py-16 bg-[#fafcfb] border-t border-gray-200 w-full">
        <div class="w-full max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-12">
            <div class="mb-16">
                <div class="text-center max-w-2xl mx-auto mb-10">
                    <h2 class="text-2xl sm:text-3xl font-bold text-[#0d5238] tracking-tight">
                        Ordenanzas y Normativas Ambientales
                    </h2>
                    <p class="mt-2 text-sm text-gray-600">
                        Acceda a la gaceta municipal, marcos regulatorios de conservación e informes de gestión pública.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-white p-6 rounded-2xl border border-gray-200/80 shadow-sm hover:shadow-md hover:-translate-y-1 transition-all duration-200">
                        <div class="w-10 h-10 rounded-xl bg-emerald-50 text-[#0d5238] flex items-center justify-center mb-4">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                        </div>
                        <h3 class="text-base font-bold text-gray-900 mb-2">
                            Ordenanza sobre Gestión de Residuos
                        </h3>
                        <p class="text-xs text-gray-600 mb-4 leading-relaxed">
                            Regula la recolección, clasificación, transporte y saneamiento ambiental de residuos en el municipio Carrizal.
                        </p>
                        <a href="#contacto" class="text-xs font-bold text-[#0d5238] hover:underline inline-flex items-center gap-1">
                            Descargar Gaceta Oficial 
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                        </a>
                    </div>

                    <div class="bg-white p-6 rounded-2xl border border-gray-200/80 shadow-sm hover:shadow-md hover:-translate-y-1 transition-all duration-200">
                        <div class="w-10 h-10 rounded-xl bg-emerald-50 text-[#0d5238] flex items-center justify-center mb-4">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        </div>
                        <h3 class="text-base font-bold text-gray-900 mb-2">
                            Protección de Áreas Verdes y Árboles
                        </h3>
                        <p class="text-xs text-gray-600 mb-4 leading-relaxed">
                            Procedimientos y sanciones administrativas por tala o poda no autorizada de vegetación en áreas urbanas.
                        </p>
                        <a href="#contacto" class="text-xs font-bold text-[#0d5238] hover:underline inline-flex items-center gap-1">
                            Ver Sanciones y Lapsos
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                        </a>
                    </div>

                    <div class="bg-white p-6 rounded-2xl border border-gray-200/80 shadow-sm hover:shadow-md hover:-translate-y-1 transition-all duration-200">
                        <div class="w-10 h-10 rounded-xl bg-emerald-50 text-[#0d5238] flex items-center justify-center mb-4">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                        <h3 class="text-base font-bold text-gray-900 mb-2">
                            Memorias de Gestión Ecosocialista
                        </h3>
                        <p class="text-xs text-gray-600 mb-4 leading-relaxed">
                            Estadísticas de inspecciones, toneladas de reciclaje procesadas y planes de siembra anuales.
                        </p>
                        <a href="#contacto" class="text-xs font-bold text-[#0d5238] hover:underline inline-flex items-center gap-1">
                            Consultar Reporte 2026
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>