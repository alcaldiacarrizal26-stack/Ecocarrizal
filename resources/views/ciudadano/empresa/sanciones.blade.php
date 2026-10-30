<x-layouts.app title="Fiscalización y Citaciones - {{ $empresa->razon_social }}" active="empresas">
<div class="p-4 sm:p-6 max-w-5xl mx-auto space-y-6" x-data="ciudadanoSancionesManager()">

    <!-- CABECERA DE NAVEGACIÓN -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-gray-200">
        <div>
            <div class="flex items-center gap-2 text-xs text-gray-400 mb-1">
                <a href="{{ route('ciudadano.empresas') }}" class="hover:underline">Mi Empresa</a>
                <span>/</span>
                <span class="text-gray-700 font-semibold">Fiscalización y Procedimientos</span>
            </div>
            <h1 class="text-xl sm:text-2xl font-black text-gray-900 tracking-tight flex items-center gap-2">
                <svg class="w-6 h-6 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                Procedimientos Sancionatorios y Citaciones
            </h1>
            <p class="text-xs sm:text-sm text-gray-500 mt-0.5">
                {{ $empresa->razon_social }} (RIF: {{ $empresa->rif }})
            </p>
        </div>

        <a href="{{ route('ciudadano.empresas') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold text-xs rounded-xl transition-all inline-flex items-center gap-2 w-fit">
            &larr; Volver al Panel
        </a>
    </div>

    <!-- ALERTAS FLASH -->
    @if(session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl text-xs font-bold flex items-center justify-between shadow-xs">
            <span>{{ session('success') }}</span>
            <button type="button" @click="$el.parentElement.remove()" class="text-emerald-600 font-bold">&times;</button>
        </div>
    @endif

    <!-- FILTROS DE PESTAÑAS (TIPO LISTA / TABS) -->
    <div class="flex items-center gap-2 border-b border-gray-200 pb-3 overflow-x-auto text-xs font-bold">
        <button @click="filtroTab = 'TODAS'" 
                :class="filtroTab === 'TODAS' ? 'bg-[#0d5238] text-white shadow-sm' : 'bg-white text-gray-600 border border-gray-200 hover:bg-gray-50'"
                class="px-4 py-2 rounded-xl transition-all cursor-pointer whitespace-nowrap">
            Todas ({{ $sanciones->count() }})
        </button>
        <button @click="filtroTab = 'ACTIVAS'" 
                :class="filtroTab === 'ACTIVAS' ? 'bg-rose-600 text-white shadow-sm' : 'bg-white text-gray-600 border border-gray-200 hover:bg-gray-50'"
                class="px-4 py-2 rounded-xl transition-all cursor-pointer whitespace-nowrap">
            Pendientes / Activas ({{ $sanciones->whereIn('estatus', ['NOTIFICADA', 'EN_REVISION', 'PENDIENTE_NOTIFICACION'])->count() }})
        </button>
        <button @click="filtroTab = 'RESUELTAS'" 
                :class="filtroTab === 'RESUELTAS' ? 'bg-emerald-600 text-white shadow-sm' : 'bg-white text-gray-600 border border-gray-200 hover:bg-gray-50'"
                class="px-4 py-2 rounded-xl transition-all cursor-pointer whitespace-nowrap">
            Subsanadas / Resueltas ({{ $sanciones->whereIn('estatus', ['SUBSANADA', 'DESESTIMADA'])->count() }})
        </button>
    </div>

    <!-- LISTADO EN FORMATO TIPO LISTA COMPACTA CON ACORDEÓN -->
    <div class="space-y-3">
        @forelse($sanciones as $s)
            @php
                $esActiva = in_array($s->estatus, ['NOTIFICADA', 'EN_REVISION', 'PENDIENTE_NOTIFICACION']);
                $tabCategoria = $esActiva ? 'ACTIVAS' : 'RESUELTAS';
            @endphp

            <div x-show="filtroTab === 'TODAS' || filtroTab === '{{ $tabCategoria }}'"
                 x-data="{ open: false }"
                 class="bg-white border border-gray-200 rounded-2xl shadow-2xs overflow-hidden transition-all duration-200 hover:border-gray-300">
                
                <!-- Fila Principal (Cabecera Compacta tipo Lista) -->
                <div @click="open = !open" class="p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3 cursor-pointer select-none bg-white hover:bg-gray-50/60 transition-colors">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-xl flex items-center justify-center shrink-0 {{ $esActiva ? 'bg-rose-50 text-rose-600' : 'bg-emerald-50 text-emerald-600' }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                @if($esActiva)
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                @else
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                @endif
                            </svg>
                        </div>
                        <div>
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="font-mono font-bold text-gray-900 text-xs">{{ $s->codigo_expediente }}</span>
                                <span class="text-gray-400">•</span>
                                <span class="text-xs font-semibold text-gray-700">{{ str_replace('_', ' ', $s->tipo_infraccion) }}</span>
                            </div>
                            <span class="text-[11px] text-gray-400 block mt-0.5">
                                Incidencia: {{ \Carbon\Carbon::parse($s->fecha_incidencia)->format('d/m/Y') }}
                            </span>
                        </div>
                    </div>

                    <div class="flex items-center justify-between sm:justify-end gap-3">
                        <!-- Insignia de Estatus -->
                        @php
                            $badgeE = match($s->estatus) {
                                'NOTIFICADA' => 'bg-rose-100 text-rose-800 border-rose-200',
                                'EN_REVISION' => 'bg-purple-100 text-purple-800 border-purple-200',
                                'SUBSANADA' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                                'DESESTIMADA' => 'bg-gray-100 text-gray-600 border-gray-200',
                                default => 'bg-gray-100 text-gray-600'
                            };
                        @endphp
                        <span class="px-2.5 py-0.5 rounded-full border text-[10px] font-extrabold uppercase {{ $badgeE }}">
                            {{ str_replace('_', ' ', $s->estatus) }}
                        </span>

                        <!-- Flecha Desplegable -->
                        <div class="w-7 h-7 rounded-lg bg-gray-50 flex items-center justify-center text-gray-500 transition-transform duration-200" :class="open ? 'rotate-180 bg-gray-100 text-gray-800' : ''">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </div>
                    </div>
                </div>

                <!-- Contenido Desplegable de Detalles -->
                <div x-show="open" x-cloak x-transition class="px-4 pb-4 pt-2 border-t border-gray-100 bg-gray-50/50 space-y-4 text-xs">
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-2">
                        <!-- Descripción y Base Legal -->
                        <div class="md:col-span-2 space-y-2">
                            <div>
                                <span class="text-gray-400 font-bold uppercase block text-[10px]">Descripción de los Hallazgos</span>
                                <p class="text-gray-700 mt-1 leading-relaxed">{{ $s->motivo_descripcion }}</p>
                            </div>

                            @if($s->base_legal)
                                <div class="p-2.5 bg-white rounded-xl border border-gray-200 text-gray-600 italic">
                                    <strong>Base Legal:</strong> {{ $s->base_legal }}
                                </div>
                            @endif
                        </div>

                        <!-- Panel Lateral de Datos Financieros y Lapsos -->
                        <div class="bg-white p-3.5 rounded-2xl border border-gray-200 space-y-3 flex flex-col justify-between">
                            <div class="space-y-0.5">
                                <span class="text-gray-400 font-bold uppercase block text-[10px]">Sanción Aplicada</span>
                                <span class="text-sm font-black text-emerald-700 block">
                                    {{ number_format($s->unidades_tributarias, 2, ',', '.') }} U.T.
                                </span>
                            </div>

                            <div class="space-y-0.5 pt-2 border-t border-gray-100">
                                <span class="text-gray-400 font-bold uppercase block text-[10px]">Límite de Subsanación</span>
                                <span class="text-xs font-extrabold text-rose-600 block">
                                    {{ $s->fecha_limite_subsanacion ? \Carbon\Carbon::parse($s->fecha_limite_subsanacion)->format('d/m/Y') : 'Sin plazo fijado' }}
                                </span>
                            </div>

                            <!-- Documento / Acta Adjunta -->
                            @if(!empty($s->evidencias_fiscalia))
                                <div class="pt-2 border-t border-gray-100">
                                    <span class="text-gray-400 block text-[10px] font-bold uppercase mb-1">Acta Oficial</span>
                                    @foreach($s->evidencias_fiscalia as $file)
                                        <a href="{{ route('sanciones.evidencia.ver', $file) }}" target="_blank" class="px-3 py-1.5 bg-gray-50 border border-gray-200 hover:border-emerald-500 text-emerald-700 font-bold text-xs rounded-xl transition-all inline-flex items-center gap-1.5 shadow-2xs">
                                            <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            <span>Ver Documento Adjunto</span>
                                        </a>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Pie de Aviso o Resolución -->
                    <div class="pt-2 border-t border-gray-200/60 flex items-center justify-between">
                        @if($esActiva)
                            <div class="p-3 bg-amber-50 border border-amber-200 text-amber-900 rounded-xl w-full flex items-center gap-2">
                                <svg class="w-4 h-4 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span>Para consignar recaudos y solicitar el levantamiento de esta sanción, asista de forma presencial a la <strong>Dirección de Residuos</strong>.</span>
                            </div>
                        @else
                            <span class="text-xs text-emerald-700 font-bold flex items-center gap-1">
                                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Procedimiento Resuelto el {{ $s->fecha_resolucion ? \Carbon\Carbon::parse($s->fecha_resolucion)->format('d/m/Y') : '' }}
                            </span>
                        @endif
                    </div>

                </div>
            </div>
        @empty
            <div class="bg-white border border-gray-200 rounded-3xl p-12 text-center text-gray-400 space-y-2">
                <p class="text-sm font-bold text-gray-700">No se registran citaciones ni sanciones</p>
                <p class="text-xs text-gray-500">Tu expediente comercial se encuentra totalmente al día ante la fiscalía municipal.</p>
            </div>
        @endforelse
    </div>

</div>

<script>
function ciudadanoSancionesManager() {
    return {
        filtroTab: 'TODAS'
    }
}
</script>
</x-layouts.app>