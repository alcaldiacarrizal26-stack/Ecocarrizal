<x-layouts.app title="Fiscalización y Citaciones - {{ $empresa->razon_social }}" active="empresas">
<div class="p-4 sm:p-6 max-w-6xl mx-auto space-y-6" x-data="{ modalSubsanar: false, sancionSeleccionada: null, fileName: '' }">

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

                <!-- Botón de Acción para Subsanar -->
                <div class="pt-3 border-t border-gray-100 flex justify-end">
                    @if(in_array($s->estatus, ['NOTIFICADA', 'PENDIENTE_NOTIFICACION']))
                        <button type="button" 
                                @click="sancionSeleccionada = {{ json_encode($s) }}; modalSubsanar = true" 
                                class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs rounded-xl transition shadow-xs flex items-center gap-2 cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                            Consignar Pruebas de Subsanación
                        </button>
                    @elseif($s->estatus === 'EN_REVISION')
                        <div class="p-2.5 bg-purple-50 border border-purple-200 text-purple-900 rounded-xl text-xs font-bold flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-purple-600 animate-ping"></span>
                            Evidencias enviadas. En espera de evaluación por la Dirección de Residuos.
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

    <!-- MODAL PARA ADJUNTAR SUBSANACIÓN -->
    <div x-show="modalSubsanar" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div x-show="modalSubsanar" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" class="fixed inset-0 bg-gray-900/60 backdrop-blur-xs" @click="modalSubsanar = false"></div>

        <div x-show="modalSubsanar" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" class="relative bg-white rounded-3xl shadow-2xl max-w-lg w-full overflow-hidden border border-gray-100 z-10 p-6 space-y-4">
            <div class="flex justify-between items-center border-b border-gray-100 pb-3">
                <div>
                    <h3 class="font-extrabold text-gray-900 text-base">Solicitud de Retiro / Subsanación</h3>
                    <p class="text-xs text-gray-500 font-mono" x-text="sancionSeleccionada?.codigo_expediente"></p>
                </div>
                <button type="button" @click="modalSubsanar = false" class="text-gray-400 hover:text-gray-600 font-bold text-lg">&times;</button>
            </div>

            <form :action="'/ciudadano/empresas/sanciones/' + sancionSeleccionada?.id + '/subsanar'" method="POST" enctype="multipart/form-data" class="space-y-4 text-xs">
                @csrf
                <div>
                    <label class="block font-bold text-gray-700 uppercase mb-1">Descripción de las Acciones Correctoras *</label>
                    <textarea name="observaciones_subsanacion" rows="3" required placeholder="Describe las medidas tomadas para solucionar la falta (ej. Saneamiento de área, nuevo contrato manejador...)" class="w-full bg-gray-50 border border-gray-300 rounded-xl p-2.5 text-gray-800 focus:ring-2 focus:ring-emerald-500"></textarea>
                </div>

                <div>
                    <label class="block font-bold text-gray-700 uppercase mb-1">Comprobante / Foto de Evidencia *</label>
                    <input type="file" name="evidencia_subsanacion" required accept=".pdf,.jpg,.png,.jpeg" class="w-full text-xs text-gray-500 file:mr-2 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-emerald-50 file:text-[#0d5238] hover:file:bg-emerald-100">
                </div>

                <div class="flex justify-end gap-2 pt-2 border-t border-gray-100">
                    <button type="button" @click="modalSubsanar = false" class="px-4 py-2 bg-gray-100 text-gray-700 font-bold rounded-xl hover:bg-gray-200">Cancelar</button>
                    <button type="submit" class="px-5 py-2 bg-[#0d5238] text-white font-bold rounded-xl hover:bg-[#073825] shadow-xs">Enviar Pruebas</button>
                </div>
            </form>
        </div>
    </div>

</div>
</x-layouts.app>