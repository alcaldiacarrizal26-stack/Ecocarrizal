<x-layouts.app title="Inspecciones y Fiscalización - {{ $empresa->razon_social }}" active="residuos">
<div class="p-4 sm:p-6 max-w-6xl mx-auto space-y-6" x-data="{ 
    modalNuevaInspeccion: false,
    tipoInspeccion: 'ORDINARIA',
    baseLegalInspeccion: 'Art. 71 de la Ley Orgánica del Ambiente - Control Ordinario',
    fileInspeccionName: '',

    actualizarBaseLegalInspeccion() {
        if (this.tipoInspeccion === 'DE_OFICIO') {
            this.baseLegalInspeccion = 'Art. 72 de la Ley Orgánica del Ambiente - Inspección Inopinada / De Oficio';
        } else {
            this.baseLegalInspeccion = 'Art. 71 de la Ley Orgánica del Ambiente - Control Ordinario';
        }
    },

    handleInspeccionFiles(files) {
        if (files && files.length > 0) {
            this.fileInspeccionName = files[0].name;
        }
    },

    abrirModalInspeccion() {
        this.modalNuevaInspeccion = true;
        // Cargar los inspectores disponibles mediante la API existente para el componente buscador
        fetch('/api/inspectores/disponibles', {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(res => res.json())
        .then(data => {
            window.dispatchEvent(new CustomEvent('cargar-inspectores-select', { detail: data }));
        })
        .catch(err => console.error('Error al cargar inspectores:', err));
    }
}">

    <!-- Cabecera -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-200">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-400 mb-1">
                <a href="{{ route('director.residuos.empresas.index') }}" class="hover:underline">Empresas</a>
                <span>/</span>
                <span class="text-slate-700 font-semibold">Inspecciones y Fiscalización</span>
            </div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2">
                <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012-2m-6 9l2 2 4-4"/></svg>
                Órdenes de Inspección (Ordinarias y de Oficio)
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">
                {{ $empresa->razon_social }} (RIF: {{ $empresa->rif }})
            </p>
        </div>

        <div class="flex items-center gap-2">
            <!-- Botón Volver al Listado -->
            <a href="{{ route('director.residuos.empresas.index') }}" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition flex items-center gap-1.5">
                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Volver al Listado
            </a>

            <!-- Botón Aperturar Inspección -->
            <button type="button" @click="abrirModalInspeccion()" class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-xs transition flex items-center gap-2 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Aperturar Inspección
            </button>
        </div>
    </div>

    <!-- Alertas Flash -->
    @if(session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl text-xs font-bold shadow-xs">
            {{ session('success') }}
        </div>
    @endif

    <!-- Listado de Inspecciones Registradas -->
    <div class="space-y-4">
        @forelse($empresa->inspecciones as $inspeccion)
            <div class="bg-white border border-slate-200 rounded-3xl p-5 shadow-xs space-y-3">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <div class="flex items-center gap-3">
                        <span class="px-3 py-1 bg-slate-100 text-slate-800 font-mono font-extrabold text-xs rounded-lg border border-slate-200">
                            {{ $inspeccion->codigo_acta }}
                        </span>
                        <span class="px-2.5 py-1 text-[10px] font-bold uppercase rounded-md {{ $inspeccion->tipo_inspeccion === 'DE_OFICIO' ? 'bg-rose-100 text-rose-800 border border-rose-200' : 'bg-blue-100 text-blue-800 border border-blue-200' }}">
                            {{ $inspeccion->tipo_inspeccion === 'DE_OFICIO' ? 'De Oficio / Sorpresa' : 'Ordinaria' }}
                        </span>
                    </div>
                    <span class="text-xs font-bold text-slate-500">
                        Fecha Visita: {{ \Carbon\Carbon::parse($inspeccion->fecha_visita)->format('d/m/Y') }}
                    </span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs">
                    <div class="md:col-span-2 space-y-1">
                        <span class="text-slate-400 font-bold uppercase text-[10px] block">Motivo / Hallazgos de Campo</span>
                        <p class="text-slate-700 leading-relaxed">{{ $inspeccion->observaciones_campo }}</p>
                        @if($inspeccion->base_legal_inspeccion)
                            <p class="text-slate-500 italic pt-1"><strong>Base Legal:</strong> {{ $inspeccion->base_legal_inspeccion }}</p>
                        @endif
                    </div>

                    <div class="bg-slate-50 p-3.5 rounded-2xl border border-slate-100 space-y-2">
                        <div>
                            <span class="text-slate-400 font-bold uppercase text-[10px] block">Inspector Asignado</span>
                            <span class="font-bold text-slate-800">{{ $inspeccion->inspector->user->nombre ?? 'N/A' }} {{ $inspeccion->inspector->user->apellido ?? '' }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 font-bold uppercase text-[10px] block">Dictamen</span>
                            <span class="font-extrabold text-amber-600">{{ $inspeccion->dictamen }}</span>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white border border-slate-200 rounded-3xl p-12 text-center text-slate-400 space-y-2">
                <p class="text-sm font-bold text-slate-700">No se registran inspecciones para esta empresa</p>
                <p class="text-xs text-slate-500">Utiliza el botón superior para aperturar una fiscalización ordinaria o de oficio.</p>
            </div>
        @endforelse
    </div>

    <!-- MODAL: NUEVA INSPECCIÓN CON BUSCADOR DE INSPECTOR REUTILIZABLE -->
    <div x-show="modalNuevaInspeccion" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6">
        <div x-show="modalNuevaInspeccion" x-transition.opacity @click="modalNuevaInspeccion = false" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs"></div>

        <div x-show="modalNuevaInspeccion" x-transition class="relative bg-white rounded-3xl shadow-2xl max-w-4xl w-full overflow-hidden border border-slate-100 z-10 my-auto flex flex-col max-h-[90vh]">
            <div class="p-5 border-b border-slate-100 bg-slate-50/80 flex justify-between items-center shrink-0">
                <div>
                    <h3 class="font-bold text-slate-800 text-base">Aperturar Orden de Inspección Ambiental</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Control fiscalizador amparado en la Ley Orgánica del Ambiente</p>
                </div>
                <button type="button" @click="modalNuevaInspeccion = false" class="text-slate-400 hover:text-slate-600 font-bold text-lg cursor-pointer">&times;</button>
            </div>

            <form action="{{ route('director.residuos.empresas.inspecciones.store', $empresa->id) }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-6 overflow-y-auto flex-1 text-xs">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-start">
                    <!-- Columna Izquierda -->
                    <div class="space-y-4">
                        <div class="border-b border-slate-100 pb-2">
                            <h4 class="font-bold text-slate-800 uppercase tracking-wider text-[11px]">1. Modalidad y Base Legal</h4>
                        </div>

                        <div>
                            <label class="block font-bold text-slate-700 uppercase mb-1">Carácter de la Inspección *</label>
                            <select name="tipo_inspeccion" x-model="tipoInspeccion" @change="actualizarBaseLegalInspeccion()" required class="w-full bg-slate-50 border border-slate-300 rounded-xl p-2.5 text-slate-800 focus:ring-2 focus:ring-emerald-500">
                                <option value="ORDINARIA">Ordinaria / Programada (Control Previo)</option>
                                <option value="DE_OFICIO">De Oficio / Sorpresa (Inopinada - Art. 72 LOA)</option>
                            </select>
                        </div>

                        <div>
                            <div class="flex justify-between items-center mb-1">
                                <label class="block font-bold text-slate-700 uppercase">Fundamento Legal Aplicable *</label>
                                <span class="text-[10px] text-slate-400 font-semibold" x-text="tipoInspeccion === 'DE_OFICIO' ? 'Facultad inopinada' : 'Control regular'"></span>
                            </div>
                            <input type="text" name="base_legal_inspeccion" x-model="baseLegalInspeccion" required class="w-full bg-slate-100 text-slate-600 border border-slate-200 rounded-xl p-2.5">
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block font-bold text-slate-700 uppercase mb-1">Fecha Programada *</label>
                                <input type="date" name="fecha_visita" value="{{ date('Y-m-d') }}" required class="w-full bg-slate-50 border border-slate-300 rounded-xl p-2.5 text-slate-800">
                            </div>
                            <div>
                                <!-- Componente Reutilizable con Buscador de Inspector -->
                                <x-select-inspector name="inspector_id" :required="true" />
                            </div>
                        </div>

                        <div>
                            <label class="block font-bold text-slate-700 uppercase mb-1">Motivo u Objetivo de la Fiscalización *</label>
                            <textarea name="observaciones_campo" rows="4" required placeholder="Describa el motivo específico de la visita..." class="w-full bg-slate-50 border border-slate-300 rounded-xl p-2.5 text-slate-800"></textarea>
                        </div>
                    </div>

                    <!-- Columna Derecha -->
                    <div class="space-y-4">
                        <div class="border-b border-slate-100 pb-2">
                            <h4 class="font-bold text-slate-800 uppercase tracking-wider text-[11px]">2. Instrucciones y Resguardo</h4>
                        </div>

                        <div class="p-3.5 bg-emerald-50/60 rounded-2xl border border-emerald-200 space-y-2 text-emerald-900">
                            <h5 class="font-bold uppercase text-[11px]">Protocolo Legal</h5>
                            <p class="text-[11px] leading-relaxed text-emerald-800">
                                Las inspecciones <strong>De Oficio / Sorpresa</strong> se amparan en el artículo 72 de la Ley Orgánica del Ambiente, permitiendo el acceso inmediato a las instalaciones comerciales para constatar el acatamiento de las normativas de residuos.
                            </p>
                        </div>

                        <div>
                            <label class="block font-bold text-slate-700 uppercase mb-1">Adjuntar Orden / Antecedente (Opcional)</label>
                            <div class="relative border-2 border-dashed border-slate-300 hover:border-emerald-500 rounded-2xl p-5 text-center bg-slate-50/50 hover:bg-emerald-50/30 transition cursor-pointer"
                                 @dragover.prevent @drop.prevent="handleInspeccionFiles($event.dataTransfer.files)">
                                <input type="file" name="orden_adjunta" x-ref="fileInspeccionInput" @change="handleInspeccionFiles($refs.fileInspeccionInput.files)" accept=".pdf,.jpg,.png,.jpeg" class="hidden">
                                <div @click="$refs.fileInspeccionInput.click()" class="space-y-1.5">
                                    <svg class="w-8 h-8 mx-auto text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 0115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                                    <p class="font-bold text-slate-700">Arrastra el documento o haz clic</p>
                                </div>
                                <template x-if="fileInspeccionName">
                                    <div class="mt-3 p-2 bg-emerald-50 text-emerald-800 rounded-xl text-xs font-bold border border-emerald-200 flex items-center justify-between">
                                        <span class="truncate" x-text="fileInspeccionName"></span>
                                        <button type="button" @click.stop="fileInspeccionName = ''; $refs.fileInspeccionInput.value = ''" class="text-rose-600 font-bold">&times;</button>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-2 pt-4 border-t border-slate-100 shrink-0">
                    <button type="button" @click="modalNuevaInspeccion = false" class="px-4 py-2.5 bg-slate-100 text-slate-700 font-semibold rounded-xl hover:bg-slate-200 transition">Cancelar</button>
                    <button type="submit" class="px-5 py-2.5 bg-emerald-600 text-white font-bold rounded-xl shadow-sm hover:bg-emerald-700 transition flex items-center gap-1.5">Emitir Orden de Inspección</button>
                </div>
            </form>
        </div>
    </div>

</div>
</x-layouts.app>