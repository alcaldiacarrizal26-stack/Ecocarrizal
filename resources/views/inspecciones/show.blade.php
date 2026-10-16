<x-layouts.app title="Acta de Inspección - {{ $inspeccion->codigo_acta }}" active="inspecciones">
    <div class="p-6 max-w-4xl mx-auto space-y-6">
        
        <div class="flex items-center justify-between no-print">
            <a href="{{ route('inspecciones.index') }}" class="text-sm text-slate-500 hover:text-slate-800">
                ← Volver al listado
            </a>
            <button onclick="window.print()" class="px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white text-xs font-semibold rounded-xl flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Imprimir Acta
            </button>
        </div>

        <!-- Acta Técnica Imprimible -->
        <div class="bg-white rounded-2xl border border-slate-200 p-8 shadow-sm space-y-6 printable-area">
            
            <!-- Encabezado Institucional -->
            <div class="border-b border-slate-200 pb-6 flex justify-between items-start">
                <div>
                    <h2 class="text-xl font-bold text-slate-900 uppercase">Acta Técnica de Inspección Ambiental</h2>
                    <p class="text-xs text-slate-500">Alcaldía del Municipio Carrizal — Dirección de Manejo Integral de Residuos y Ambiente</p>
                </div>
                <div class="text-right">
                    <span class="block font-mono font-bold text-lg text-emerald-700">{{ $inspeccion->codigo_acta }}</span>
                    <span class="text-xs text-slate-400">Fecha: {{ \Carbon\Carbon::parse($inspeccion->fecha_visita)->format('d/m/Y h:i A') }}</span>
                </div>
            </div>

            <!-- Datos de la Empresa / Orden de Inspección o Trámite -->
            @php
                $empresaRelacionada = $inspeccion->ordenInspeccion->empresa ?? $inspeccion->empresa ?? null;
                $ordenRelacionada = $inspeccion->ordenInspeccion ?? null;
            @endphp

            @if($empresaRelacionada)
            <div class="bg-emerald-50/60 p-4 rounded-xl border border-emerald-100 grid grid-cols-2 gap-4 text-sm">
                <div>
                    <span class="text-xs text-emerald-800 block uppercase font-bold">Empresa Fiscalizada</span>
                    <p class="font-bold text-slate-900">{{ $empresaRelacionada->razon_social }}</p>
                    <p class="text-xs text-slate-600">RIF: {{ $empresaRelacionada->rif }}</p>
                </div>
                <div>
                    <span class="text-xs text-emerald-800 block uppercase font-bold">Carácter de la Inspección</span>
                    @php
                        $tipoInspeccion = $ordenRelacionada->tipo_inspeccion ?? $inspeccion->tipo_inspeccion ?? 'ORDINARIA';
                    @endphp
                    <span class="inline-block mt-1 px-2.5 py-0.5 text-xs font-bold rounded-md {{ $tipoInspeccion === 'DE_OFICIO' ? 'bg-rose-100 text-rose-800' : 'bg-blue-100 text-blue-800' }}">
                        {{ $tipoInspeccion === 'DE_OFICIO' ? 'De Oficio / Sorpresa (Inopinada)' : 'Ordinaria / Programada' }}
                    </span>
                    @if($ordenRelacionada?->base_legal_inspeccion || $inspeccion->base_legal_inspeccion)
                        <p class="text-[11px] text-slate-600 mt-1 italic">Base Legal: {{ $ordenRelacionada->base_legal_inspeccion ?? $inspeccion->base_legal_inspeccion }}</p>
                    @endif
                </div>
            </div>
            @endif

            <!-- Datos del Inspector -->
            <div class="grid grid-cols-2 gap-4 text-sm bg-slate-50 p-4 rounded-xl">
                <div>
                    <span class="text-xs text-slate-400 block uppercase font-bold">Inspector Responsable</span>
                    <p class="font-semibold text-slate-800">{{ $inspeccion->inspector->user->nombre ?? 'N/A' }} {{ $inspeccion->inspector->user->apellido ?? '' }}</p>
                    <p class="text-xs text-slate-500">Credencial: {{ $inspeccion->inspector->codigo_credencial ?? 'S/C' }}</p>
                </div>
                <div>
                    <span class="text-xs text-slate-400 block uppercase font-bold">Dictamen Emitido</span>
                    <span class="inline-block mt-1 px-3 py-1 text-xs font-bold rounded-full bg-slate-200 text-slate-800">
                        {{ str_replace('_', ' ', $inspeccion->dictamen) }}
                    </span>
                </div>
            </div>

            <!-- Orden Adjunta o Antecedente si existe -->
            @php
                $pathOrden = $ordenRelacionada->orden_adjunta_path ?? $inspeccion->orden_adjunta_path ?? null;
            @endphp
            @if($pathOrden)
            <div class="p-3 bg-slate-50 border border-slate-200 rounded-xl flex items-center justify-between text-xs">
                <span class="font-semibold text-slate-700">Documento u Orden de Fiscalización Adjunta:</span>
                <a href="{{ asset('storage/' . $pathOrden) }}" target="_blank" class="text-emerald-600 hover:underline font-bold flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    Ver Archivo Adjunto
                </a>
            </div>
            @endif

            <!-- Detalle de Hallazgos -->
            <div class="space-y-2">
                <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider">Observaciones de Campo</h3>
                <p class="text-sm text-slate-700 leading-relaxed bg-slate-50 p-4 rounded-xl border border-slate-100">
                    {{ $inspeccion->observaciones_campo }}
                </p>
            </div>

            <!-- Recomendaciones -->
            @if($inspeccion->recomendaciones)
            <div class="space-y-2">
                <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider">Recomendaciones y Medidas</h3>
                <p class="text-sm text-slate-700 leading-relaxed bg-amber-50/50 p-4 rounded-xl border border-amber-100">
                    {{ $inspeccion->recomendaciones }}
                </p>
            </div>
            @endif

            <!-- Galería de Evidencias Fotográficas -->
            @if(!empty($inspeccion->fotos_evidencia))
            <div class="space-y-2">
                <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider">Registro Fotográfico de Campo</h3>
                <div class="grid grid-cols-3 gap-4">
                    @foreach ($inspeccion->fotos_evidencia as $foto)
                        <img src="{{ asset('storage/'.$foto) }}" alt="Evidencia" class="w-full h-32 object-cover rounded-xl border border-slate-200">
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Área de Firmas -->
            <div class="pt-16 grid grid-cols-2 gap-8 text-center text-xs text-slate-500">
                <div class="border-t border-slate-300 pt-2">
                    <p class="font-bold text-slate-700">{{ $inspeccion->inspector->user->nombre ?? '' }} {{ $inspeccion->inspector->user->apellido ?? '' }}</p>
                    <p>Firma del Inspector Ambiental / Fiscalizador</p>
                </div>
                <div class="border-t border-slate-300 pt-2">
                    <p class="font-bold text-slate-700">Firma del Representante / Receptante</p>
                    <p>C.I.: ________________________</p>
                </div>
            </div>

        </div>
    </div>
</x-layouts.app>