<x-layouts.app title="Fiscalización y Citaciones - {{ $empresa->razon_social }}" active="empresas">
<div class="p-4 sm:p-6 max-w-6xl mx-auto space-y-6">

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
                {{ $empresa->razon_social }} ({{ $empresa->rif }})
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

    <!-- LISTADO DE PROCEDIMIENTOS -->
    <div class="space-y-4">
        @forelse($sanciones as $s)
            <div class="bg-white border border-gray-200 rounded-3xl p-5 shadow-xs space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-gray-100 pb-3">
                    <div class="flex items-center gap-3">
                        <span class="px-3 py-1 bg-gray-100 text-gray-800 font-mono font-extrabold text-xs rounded-lg border border-gray-200">
                            {{ $s->codigo_expediente }}
                        </span>
                        <span class="text-xs font-bold text-gray-500 uppercase">
                            Incidencia: {{ \Carbon\Carbon::parse($s->fecha_incidencia)->format('d/m/Y') }}
                        </span>
                    </div>

                    @php
                        $badgeE = match($s->estatus) {
                            'NOTIFICADA' => 'bg-rose-100 text-rose-800 border-rose-200',
                            'EN_REVISION' => 'bg-purple-100 text-purple-800 border-purple-200',
                            'SUBSANADA' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                            'DESESTIMADA' => 'bg-gray-100 text-gray-600 border-gray-200',
                            default => 'bg-gray-100 text-gray-600'
                        };
                    @endphp
                    <span class="px-3 py-1 rounded-full border text-xs font-extrabold uppercase {{ $badgeE }} w-fit">
                        {{ str_replace('_', ' ', $s->estatus) }}
                    </span>
                </div>

                <!-- Detalle de la infracción -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs">
                    <div class="md:col-span-2 space-y-2">
                        <div>
                            <span class="text-gray-400 font-bold uppercase block text-[10px]">Infracción Notificada</span>
                            <h3 class="font-bold text-gray-900 text-sm mt-0.5">{{ str_replace('_', ' ', $s->tipo_infraccion) }}</h3>
                            <p class="text-gray-600 mt-1 leading-relaxed">{{ $s->motivo_descripcion }}</p>
                        </div>

                        @if($s->base_legal)
                            <div class="p-2.5 bg-gray-50 rounded-xl border border-gray-100 text-gray-500 italic">
                                <strong>Base Legal:</strong> {{ $s->base_legal }}
                            </div>
                        @endif
                    </div>

                    <div class="bg-gray-50 p-3.5 rounded-2xl border border-gray-100 space-y-2.5 flex flex-col justify-between">
                        <div class="space-y-1">
                            <span class="text-gray-400 font-bold uppercase block text-[10px]">Límite de Subsanación</span>
                            <span class="text-sm font-extrabold text-rose-600 block">
                                {{ $s->fecha_limite_subsanacion ? \Carbon\Carbon::parse($s->fecha_limite_subsanacion)->format('d/m/Y') : 'Sin plazo fijado' }}
                            </span>
                        </div>

                        <!-- Ver Acta / Foto emitida por Alcaldía -->
                        @if(!empty($s->evidencias_fiscalia))
                            <div class="pt-2 border-t border-gray-200/60">
                                <span class="text-gray-400 block text-[10px] font-bold uppercase mb-1">Acta / Notificación Oficial</span>
                                @foreach($s->evidencias_fiscalia as $file)
                                    <a href="{{ route('sanciones.evidencia.ver', $file) }}" target="_blank" class="px-3 py-1.5 bg-white border border-gray-300 hover:border-rose-500 text-rose-700 font-bold text-xs rounded-xl transition-all flex items-center gap-1.5 shadow-2xs w-fit">
                                        <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        <span>Ver Documento Adjunto</span>
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Aviso Institucional de Retiro Presencial -->
                <div class="pt-3 border-t border-gray-100 flex items-center justify-between text-xs">
                    @if(in_array($s->estatus, ['NOTIFICADA', 'PENDIENTE_NOTIFICACION']))
                        <div class="p-3 bg-amber-50 border border-amber-200 text-amber-900 rounded-xl w-full flex items-center gap-2">
                            <svg class="w-5 h-5 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>Para consignar recaudos y solicitar el retiro o levantamiento de esta sanción, el representante legal debe asistir de forma presencial a las instalaciones de la <strong>Dirección de Manejo de Residuos</strong>.</span>
                        </div>
                    @elseif($s->estatus === 'EN_REVISION')
                        <div class="p-3 bg-purple-50 border border-purple-200 text-purple-900 rounded-xl w-full flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-purple-600 animate-ping"></span>
                            <span>Expediente bajo revisión directa en las oficinas de la Dirección de Residuos tras inspección presencial.</span>
                        </div>
                    @else
                        <span class="text-xs text-emerald-700 font-bold flex items-center gap-1">
                            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Procedimiento Resuelto el {{ $s->fecha_resolucion ? \Carbon\Carbon::parse($s->fecha_resolucion)->format('d/m/Y') : '' }}
                        </span>
                    @endif
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
</x-layouts.app>