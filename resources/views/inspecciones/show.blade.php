<x-layouts.app title="Acta de Inspección - {{ $inspeccion->codigo_acta }}" active="inspecciones">
    <div class="p-6 max-w-4xl mx-auto space-y-6">
        
        <div class="flex items-center justify-between no-print">
            <a href="{{ route('inspecciones.index') }}" class="text-sm text-slate-500 hover:text-slate-800">
                ← Volver al listado
            </a>
            <button onclick="imprimirActaPdf('{{ route('inspecciones.pdf', $inspeccion->id) }}')" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl flex items-center gap-2 shadow-sm cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Imprimir / Guardar Acta Oficial (QR)
            </button>
        </div>

        <!-- Acta Técnica con Aspecto Profesional Institucional -->
        <div class="bg-white rounded-3xl border border-slate-200 p-8 shadow-xl space-y-6">
            
            <!-- Encabezado Institucional -->
            <div class="border-b border-slate-100 pb-6 flex justify-between items-start">
                <div>
                    <span class="px-3 py-1 bg-emerald-50 text-emerald-700 text-[10px] font-bold uppercase tracking-wider rounded-lg border border-emerald-200">
                        Alcaldía del Municipio Carrizal
                    </span>
                    <h2 class="text-xl font-black text-slate-900 uppercase mt-2">Acta Técnica de Inspección Ambiental</h2>
                    <p class="text-xs text-slate-500">Dirección de Manejo Integral de Residuos y Ambiente</p>
                </div>
                <div class="text-right bg-slate-50 p-3 rounded-2xl border border-slate-100">
                    <span class="block font-mono font-black text-base text-emerald-700">{{ $inspeccion->codigo_acta }}</span>
                    <span class="text-[11px] text-slate-400 font-medium">Fecha: {{ \Carbon\Carbon::parse($inspeccion->fecha_visita)->format('d/m/Y h:i A') }}</span>
                </div>
            </div>

            <!-- Datos de la Empresa / Orden de Inspección o Trámite -->
            @php
                $empresaRelacionada = $inspeccion->ordenInspeccion->empresa ?? $inspeccion->empresa ?? null;
                $ordenRelacionada = $inspeccion->ordenInspeccion ?? null;
            @endphp

            @if($empresaRelacionada)
            <div class="bg-emerald-50/60 p-5 rounded-2xl border border-emerald-100 grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                <div>
                    <span class="text-xs text-emerald-800 block uppercase font-bold">Empresa Fiscalizada</span>
                    <p class="font-bold text-slate-900 text-sm mt-0.5">{{ $empresaRelacionada->razon_social }}</p>
                    <p class="text-slate-600 mt-1">RIF: <strong class="font-mono">{{ $empresaRelacionada->rif }}</strong></p>
                </div>
                <div>
                    <span class="text-xs text-emerald-800 block uppercase font-bold">Carácter de la Inspección</span>
                    @php
                        $tipoInspeccion = $ordenRelacionada->tipo_inspeccion ?? $inspeccion->tipo_inspeccion ?? 'ORDINARIA';
                    @endphp
                    <span class="inline-block mt-1 px-2.5 py-1 text-xs font-bold rounded-lg {{ $tipoInspeccion === 'DE_OFICIO' ? 'bg-rose-100 text-rose-800' : 'bg-blue-100 text-blue-800' }}">
                        {{ $tipoInspeccion === 'DE_OFICIO' ? 'De Oficio / Sorpresa (Inopinada)' : 'Ordinaria / Programada' }}
                    </span>
                </div>
            </div>
            @endif

            <!-- Datos del Inspector Corregidos -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs bg-slate-50 p-5 rounded-2xl border border-slate-200">
                <div>
                    <span class="text-xs text-slate-400 block uppercase font-bold">Inspector Responsable</span>
                    <p class="font-bold text-slate-800 text-sm mt-0.5">{{ $inspeccion->inspector->nombre ?? 'N/A' }} {{ $inspeccion->inspector->apellido ?? '' }}</p>
                    <p class="text-slate-500 mt-1">Credencial / Perfil: 
                        <strong class="font-mono text-emerald-700">
                            {{ $inspeccion->inspector->inspector->codigo_credencial ?? 'FUNCIONARIO DIRECTIVO / S.C.' }}
                        </strong>
                    </p>
                </div>
                <div>
                    <span class="text-xs text-slate-400 block uppercase font-bold">Dictamen Emitido</span>
                    <span class="inline-block mt-1 px-3 py-1.5 text-xs font-bold rounded-xl bg-slate-200 text-slate-800 uppercase">
                        {{ str_replace('_', ' ', $inspeccion->dictamen) }}
                    </span>
                </div>
            </div>

            <!-- Detalle de Hallazgos -->
            <div class="space-y-2">
                <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider">Observaciones de Campo</h3>
                <p class="text-xs text-slate-700 leading-relaxed bg-slate-50 p-5 rounded-2xl border border-slate-200 whitespace-pre-line">
                    {{ $inspeccion->observaciones_campo }}
                </p>
            </div>

            <!-- Recomendaciones -->
            @if($inspeccion->recomendaciones)
            <div class="space-y-2">
                <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider">Recomendaciones y Medidas Correctivas</h3>
                <p class="text-xs text-slate-700 leading-relaxed bg-amber-50/60 p-5 rounded-2xl border border-amber-200 whitespace-pre-line">
                    {{ $inspeccion->recomendaciones }}
                </p>
            </div>
            @endif

            <!-- Galería de Evidencias Fotográficas -->
            @if(!empty($inspeccion->fotos_evidencia))
            <div class="space-y-2">
                <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider">Registro Fotográfico de Campo</h3>
                <div class="grid grid-cols-3 gap-4">
                    @foreach ($inspeccion->fotos_evidencia as $foto)
                        <img src="{{ asset('storage/'.$foto) }}" alt="Evidencia" class="w-full h-36 object-cover rounded-2xl border border-slate-200 shadow-sm">
                    @endforeach
                </div>
            </div>
            @endif

        </div>
    </div>

    <!-- Iframe oculto para impresión nativa sin abrir pestañas -->
    <iframe id="pdfActaFrame" style="display: none;"></iframe>
    <script>
        function imprimirActaPdf(url) {
            const frame = document.getElementById('pdfActaFrame');
            frame.src = url;
            frame.onload = function() {
                try {
                    frame.contentWindow.focus();
                    frame.contentWindow.print();
                } catch (e) {
                    window.open(url, '_blank');
                }
            };
        }
    </script>
</x-layouts.app>