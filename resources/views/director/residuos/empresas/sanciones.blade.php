<x-layouts.app title="Gestión de Sanciones - {{ $empresa->razon_social }}" active="residuos_empresas">
<div class="p-6 max-w-7xl mx-auto space-y-6" 
     x-data="sancionManager()">

    <!-- ALERTAS -->
    @if(session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-2xl flex items-center justify-between text-emerald-800 text-sm shadow-sm">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span>{{ session('success') }}</span>
            </div>
            <button type="button" @click="$el.parentElement.remove()" class="text-emerald-500 font-bold">&times;</button>
        </div>
    @endif

    <!-- CABECERA DE NAVEGACIÓN -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-500 mb-1">
                <a href="{{ route('director.residuos.empresas.index') }}" class="hover:underline">Empresas</a>
                <span>/</span>
                <a href="{{ route('director.residuos.empresas.show', $empresa->id) }}" class="hover:underline">{{ $empresa->razon_social }}</a>
                <span>/</span>
                <span class="text-slate-700 font-semibold">Sanciones y Procedimientos</span>
            </div>
            <h1 class="text-2xl font-bold text-slate-800 tracking-tight">Expediente Sancionatorio</h1>
            <p class="text-sm text-slate-500">RIF: {{ $empresa->rif }} | Control fiscal y resoluciones administrativas</p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('director.residuos.empresas.show', $empresa->id) }}" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-xl transition">
                &larr; Volver a Empresa
            </a>
            <button type="button" @click="modalNuevaSancion = true" class="px-4 py-2.5 bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold rounded-xl transition shadow-sm flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                Aperturar Sanción / Citación
            </button>
        </div>
    </div>

    <!-- TARJETAS DE MÉTRICAS -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase">Total Procedimientos</p>
                <p class="text-2xl font-bold text-slate-800 mt-1">{{ $sanciones->count() }}</p>
            </div>
            <div class="p-3 bg-slate-100 text-slate-600 rounded-xl">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            </div>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-rose-600 uppercase">Activas / Notificadas</p>
                <p class="text-2xl font-bold text-rose-600 mt-1">{{ $sanciones->whereIn('estatus', ['NOTIFICADA', 'PENDIENTE_NOTIFICACION'])->count() }}</p>
            </div>
            <div class="p-3 bg-rose-50 text-rose-600 rounded-xl">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-emerald-600 uppercase">Subsanadas / Retiradas</p>
                <p class="text-2xl font-bold text-emerald-600 mt-1">{{ $sanciones->where('estatus', 'SUBSANADA')->count() }}</p>
            </div>
            <div class="p-3 bg-emerald-50 text-emerald-600 rounded-xl">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            </div>
        </div>
    </div>

    <!-- TABLA DE EXPEDIENTES SANCIONATORIOS -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
            <h2 class="text-sm font-bold text-slate-800 uppercase tracking-wide">Histórico de Citaciones e Infracciones</h2>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left text-slate-600">
                <thead class="text-[11px] uppercase bg-slate-50 text-slate-500 font-bold border-b border-slate-200">
                    <tr>
                        <th class="px-4 py-3">Expediente</th>
                        <th class="px-4 py-3">Infracción / Base Legal</th>
                        <th class="px-4 py-3 text-center">Gravedad</th>
                        <th class="px-4 py-3">Límite Subsanación</th>
                        <th class="px-4 py-3">Estatus</th>
                        <th class="px-4 py-3">Evidencias</th>
                        <th class="px-4 py-3 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($sanciones as $s)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-4 py-3 font-bold text-slate-800 whitespace-nowrap">
                                {{ $s->codigo_expediente }}
                            </td>
                            <td class="px-4 py-3">
                                <span class="font-semibold text-slate-800 block">{{ str_replace('_', ' ', $s->tipo_infraccion) }}</span>
                                <span class="text-[10px] text-slate-500 block italic">{{ $s->base_legal }}</span>
                                <span class="text-[11px] text-slate-400 line-clamp-1 mt-0.5">{{ $s->motivo_descripcion }}</span>
                            </td>
                            <td class="px-4 py-3 text-center whitespace-nowrap">
                                @php
                                    $badgeG = match($s->gravedad) {
                                        'LEVE' => 'bg-amber-50 text-amber-700 border-amber-200',
                                        'GRAVE' => 'bg-orange-50 text-orange-700 border-orange-200 font-bold',
                                        'MUY_GRAVE' => 'bg-rose-100 text-rose-800 border-rose-300 font-bold',
                                        default => 'bg-slate-100 text-slate-600 border-slate-200'
                                    };
                                @endphp
                                <span class="px-2 py-0.5 rounded-full border text-[10px] uppercase font-semibold {{ $badgeG }}">
                                    {{ $s->gravedad }}
                                </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                @if($s->fecha_limite_subsanacion)
                                    <div class="font-bold text-slate-700">{{ $s->fecha_limite_subsanacion->format('d/m/Y') }}</div>
                                    <div class="text-[10px] text-slate-400">Según LOPA / LOA</div>
                                @else
                                    <span class="text-slate-400 italic">Sin plazo fijado</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                @php
                                    $badgeE = match($s->estatus) {
                                        'NOTIFICADA' => 'bg-rose-50 text-rose-700 border-rose-200',
                                        'EN_REVISION' => 'bg-purple-50 text-purple-700 border-purple-200',
                                        'SUBSANADA' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                        'DESESTIMADA' => 'bg-slate-100 text-slate-600 border-slate-200',
                                        default => 'bg-slate-100 text-slate-600'
                                    };
                                @endphp
                                <span class="px-2.5 py-1 rounded-md border text-[10px] font-bold uppercase {{ $badgeE }}">
                                    {{ str_replace('_', ' ', $s->estatus) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                @if(!empty($s->evidencias_fiscalia))
                                    @foreach($s->evidencias_fiscalia as $file)
                                        <a href="{{ asset('storage/' . $file) }}" target="_blank" class="inline-flex items-center gap-1 text-indigo-600 hover:underline font-semibold text-[11px]">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                            Ver Acta
                                        </a>
                                    @endforeach
                                @else
                                    <span class="text-slate-400 italic">Sin adjuntos</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right whitespace-nowrap">
                                @if(in_array($s->estatus, ['NOTIFICADA', 'EN_REVISION', 'PENDIENTE_NOTIFICACION']))
                                    <button type="button" 
                                            @click="sancionSeleccionada = {{ json_encode($s) }}; modalResolver = true"
                                            class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-[11px] transition shadow-xs">
                                        Evaluar / Retirar Sanción
                                    </button>
                                @else
                                    <span class="text-slate-400 text-[11px] italic">Resuelta el {{ $s->fecha_resolucion ? $s->fecha_resolucion->format('d/m/Y') : '' }}</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-slate-400">
                                La empresa no posee citaciones ni sanciones registradas en su historial.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- MODAL 1: APERTURAR SANCIÓN / CITACIÓN (REDISEÑADO EN 2 COLUMNAS CON SCROLL) -->
    <div x-show="modalNuevaSancion" 
         x-cloak 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6">
        
        <!-- Backdrop Suave -->
        <div x-show="modalNuevaSancion"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="modalNuevaSancion = false"
             class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs"></div>

        <!-- Panel del Modal Ancho (max-w-4xl) -->
        <div x-show="modalNuevaSancion"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95 translate-y-2"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
             x-transition:leave-end="opacity-0 scale-95 translate-y-2"
             class="relative bg-white rounded-3xl shadow-2xl max-w-4xl w-full overflow-hidden border border-slate-100 z-10 my-auto flex flex-col max-h-[90vh]">
            
            <!-- Header Fijo -->
            <div class="p-5 border-b border-slate-100 bg-slate-50/80 flex justify-between items-center shrink-0">
                <div>
                    <h3 class="font-bold text-slate-800 text-base">Aperturar Citación / Sanción Administrativa</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Fundamentación legal y plazos de subsanación (LOPA / LOA)</p>
                </div>
                <button type="button" @click="modalNuevaSancion = false" class="text-slate-400 hover:text-slate-600 font-bold text-lg cursor-pointer">&times;</button>
            </div>

            <!-- Formulario con Scroll Interno -->
            <form action="{{ route('director.residuos.empresas.sanciones.store', $empresa->id) }}" 
                  method="POST" 
                  enctype="multipart/form-data" 
                  class="p-6 space-y-6 overflow-y-auto flex-1 text-xs">
                @csrf

                <!-- Rejilla en 2 Columnas -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-start">
                    
                    <!-- BLOQUE IZQUIERDO: Clasificación de la Infracción -->
                    <div class="space-y-4">
                        <div class="border-b border-slate-100 pb-2">
                            <h4 class="font-bold text-slate-800 uppercase tracking-wider text-[11px]">1. Tipificación y Fundamento Legal</h4>
                        </div>

                        <!-- Concepto / Tipo de Infracción -->
                        <div>
                            <label class="block font-bold text-slate-700 uppercase mb-1">Concepto de Infracción *</label>
                            <select name="tipo_infraccion_select" 
                                    x-model="tipoInfraccion" 
                                    @change="actualizarBaseLegal()" 
                                    required 
                                    class="w-full bg-slate-50 border border-slate-300 rounded-xl p-2.5 text-slate-800 focus:ring-2 focus:ring-rose-500">
                                <option value="FALTA_DECLARACION_JURADA">Falta de Declaración Jurada de Residuos</option>
                                <option value="INCONGRUENCIA_DISPOSICION">Incongruencia / Falsedad en Manifiestos HDS</option>
                                <option value="VERTIDO_NO_AUTORIZADO">Vertido o Almacenamiento Clandestino</option>
                                <option value="OBSTACULIZACION_FISCALIA">Obstaculización de la Función Inspectora</option>
                                <option value="INCUMPLIMIENTO_MEDIDAS">Incumplimiento de Medidas Correctoras</option>
                                <option value="OTRO">Otro (Especificar manualmente)</option>
                            </select>
                        </div>

                        <!-- Campo Texto Si selecciona "OTRO" -->
                        <div x-show="tipoInfraccion === 'OTRO'" x-cloak>
                            <label class="block font-bold text-slate-700 uppercase mb-1">Nombre / Título de Infracción Personalizada *</label>
                            <input type="text" 
                                   name="tipo_infraccion_manual" 
                                   x-model="tipoInfraccionManual" 
                                   placeholder="Ej. Emisión de olores molestos sin tratamiento previo..." 
                                   class="w-full bg-slate-50 border border-slate-300 rounded-xl p-2.5 text-slate-800 focus:ring-2 focus:ring-rose-500">
                        </div>

                        <input type="hidden" name="tipo_infraccion" :value="tipoInfraccion === 'OTRO' ? tipoInfraccionManual : tipoInfraccion">

                        <!-- Base Legal (Solo editable si tipoInfraccion es 'OTRO') -->
                        <div>
                            <div class="flex justify-between items-center mb-1">
                                <label class="block font-bold text-slate-700 uppercase">Base Legal Aplicable *</label>
                                <span class="text-[10px] text-slate-400 font-semibold" x-text="tipoInfraccion === 'OTRO' ? 'Edición manual habilitada' : 'Sugerida automáticamente (Bloqueada)'"></span>
                            </div>
                            <input type="text" 
                                name="base_legal" 
                                x-model="baseLegal" 
                                :readonly="tipoInfraccion !== 'OTRO'"
                                required 
                                placeholder="Ej: Art. 45 Ordenanza Municipal / Art. 72 LOA" 
                                :class="tipoInfraccion !== 'OTRO' ? 'bg-slate-100 text-slate-600 border-slate-200 cursor-not-allowed' : 'bg-slate-50 text-slate-800 border-slate-300 focus:ring-2 focus:ring-rose-500'"
                                class="w-full rounded-xl p-2.5 transition-all">
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block font-bold text-slate-700 uppercase mb-1">Gravedad *</label>
                                <select name="gravedad" required class="w-full bg-slate-50 border border-slate-300 rounded-xl p-2.5 text-slate-800 focus:ring-2 focus:ring-rose-500">
                                    <option value="LEVE">LEVE</option>
                                    <option value="GRAVE">GRAVE</option>
                                    <option value="MUY_GRAVE">MUY GRAVE</option>
                                </select>
                            </div>

                            <div>
                                <label class="block font-bold text-slate-700 uppercase mb-1">Fecha Incidencia *</label>
                                <input type="date" name="fecha_incidencia" value="{{ date('Y-m-d') }}" required class="w-full bg-slate-50 border border-slate-300 rounded-xl p-2.5 text-slate-800">
                            </div>
                        </div>

                        <div>
                            <label class="block font-bold text-slate-700 uppercase mb-1">Motivo / Hallazgos Evidenciados *</label>
                            <textarea name="motivo_descripcion" rows="4" required placeholder="Describe detalladamente los hallazgos durante la inspección o fiscalización..." class="w-full bg-slate-50 border border-slate-300 rounded-xl p-2.5 text-slate-800"></textarea>
                        </div>
                    </div>

                    <!-- BLOQUE DERECHO: Lapsos Legales y Evidencias -->
                    <div class="space-y-4">
                        <div class="border-b border-slate-100 pb-2">
                            <h4 class="font-bold text-slate-800 uppercase tracking-wider text-[11px]">2. Lapsos Administrativos y Evidencias</h4>
                        </div>

                        <!-- Límite de Subsanación según LOPA / LOA -->
                        <div class="p-3.5 bg-slate-50 rounded-2xl border border-slate-200 space-y-3">
                            <div class="flex justify-between items-center">
                                <label class="block font-bold text-slate-700 uppercase">Plazo Legal para Subsanar</label>
                                <span class="text-[10px] text-slate-500 italic">LOPA Art. 85 / LOA</span>
                            </div>

                            <div class="grid grid-cols-3 gap-2">
                                <button type="button" @click="setPlazoDays(10)" class="py-1.5 px-2 bg-white border border-slate-200 hover:border-emerald-500 rounded-xl text-[11px] font-bold text-slate-700 transition cursor-pointer">
                                    10 Días (LOPA)
                                </button>
                                <button type="button" @click="setPlazoDays(15)" class="py-1.5 px-2 bg-white border border-slate-200 hover:border-emerald-500 rounded-xl text-[11px] font-bold text-slate-700 transition cursor-pointer">
                                    15 Días (LOA)
                                </button>
                                <button type="button" @click="setPlazoDays(30)" class="py-1.5 px-2 bg-white border border-slate-200 hover:border-emerald-500 rounded-xl text-[11px] font-bold text-slate-700 transition cursor-pointer">
                                    30 Días (Obra)
                                </button>
                            </div>

                            <div class="grid grid-cols-2 gap-3 pt-1">
                                <div>
                                    <span class="block text-[10px] text-slate-500 mb-1">Fecha Citación</span>
                                    <input type="date" name="fecha_citacion" value="{{ date('Y-m-d') }}" class="w-full bg-white border border-slate-300 rounded-xl p-2 text-slate-800">
                                </div>
                                <div>
                                    <span class="block text-[10px] text-slate-500 mb-1">Fecha Límite</span>
                                    <input type="date" name="fecha_limite_subsanacion" x-model="fechaLimite" class="w-full bg-white border border-slate-300 rounded-xl p-2 text-slate-800 font-bold text-rose-700">
                                </div>
                            </div>
                        </div>

                        <!-- Dropzone Estilo Drag & Drop para Evidencias -->
                        <div>
                            <label class="block font-bold text-slate-700 uppercase mb-1">Adjuntar Acta / Fotos Evidencia</label>
                            <div class="relative border-2 border-dashed border-slate-300 hover:border-rose-500 rounded-2xl p-5 text-center bg-slate-50/50 hover:bg-rose-50/30 transition-all cursor-pointer"
                                 @dragover.prevent="$el.classList.add('border-rose-500', 'bg-rose-50/50')"
                                 @dragleave.prevent="$el.classList.remove('border-rose-500', 'bg-rose-50/50')"
                                 @drop.prevent="$el.classList.remove('border-rose-500', 'bg-rose-50/50'); $refs.fileInput.files = $event.dataTransfer.files; handleFiles($refs.fileInput.files)">
                                
                                <input type="file" 
                                       name="evidencia" 
                                       x-ref="fileInput" 
                                       @change="handleFiles($refs.fileInput.files)" 
                                       accept=".pdf,.jpg,.png,.jpeg" 
                                       class="hidden">

                                <div @click="$refs.fileInput.click()" class="space-y-1.5">
                                    <svg class="w-8 h-8 mx-auto text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 0115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                                    <p class="font-bold text-slate-700">Arrastra el archivo o haz clic para examinar</p>
                                    <p class="text-[10px] text-slate-400">PDF, JPG o PNG hasta 5MB</p>
                                </div>

                                <template x-if="fileName">
                                    <div class="mt-3 p-2 bg-emerald-50 text-emerald-800 rounded-xl text-xs font-bold border border-emerald-200 flex items-center justify-between">
                                        <span class="truncate" x-text="fileName"></span>
                                        <button type="button" @click.stop="fileName = ''; $refs.fileInput.value = ''" class="text-rose-600 font-bold cursor-pointer">&times;</button>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Footer Fijo con Botones de Acción -->
                <div class="flex justify-end gap-2 pt-4 border-t border-slate-100 shrink-0">
                    <button type="button" @click="modalNuevaSancion = false" class="px-4 py-2.5 bg-slate-100 text-slate-700 font-semibold rounded-xl hover:bg-slate-200 transition cursor-pointer">
                        Cancelar
                    </button>
                    <button type="submit" class="px-5 py-2.5 bg-rose-600 text-white font-bold rounded-xl shadow-sm hover:bg-rose-700 transition cursor-pointer flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        Confirmar y Notificar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL 2: RESOLVER / RETIRAR SANCIÓN (CON DROPZONE ESTILIZADO) -->
    <div x-show="modalResolver" 
         x-cloak 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6">
        
        <!-- Backdrop Suave -->
        <div x-show="modalResolver"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="modalResolver = false"
             class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs"></div>

        <!-- Panel del Modal Ancho (max-w-3xl) -->
        <div x-show="modalResolver"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95 translate-y-2"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
             x-transition:leave-end="opacity-0 scale-95 translate-y-2"
             class="relative bg-white rounded-3xl shadow-2xl max-w-3xl w-full overflow-hidden border border-slate-100 z-10 my-auto flex flex-col max-h-[90vh]">
            
            <!-- Header Fijo -->
            <div class="p-5 border-b border-slate-100 bg-slate-50/80 flex justify-between items-center shrink-0">
                <div>
                    <h3 class="font-bold text-slate-800 text-base">Evaluar y Resolver Sanción</h3>
                    <p class="text-xs text-slate-500 font-mono" x-text="sancionSeleccionada?.codigo_expediente"></p>
                </div>
                <button type="button" @click="modalResolver = false" class="text-slate-400 hover:text-slate-600 font-bold text-lg cursor-pointer">&times;</button>
            </div>

            <!-- Cuerpo con Scroll -->
            <form :action="'/director/residuos/empresas/sanciones/' + sancionSeleccionada?.id + '/resolver'" method="POST" enctype="multipart/form-data" class="p-6 space-y-6 overflow-y-auto flex-1 text-xs">
                @csrf
                @method('PATCH')

                <!-- BLOQUE INFORMATIVO: Descargos y Pruebas enviadas por el Ciudadano -->
                <div class="p-4 bg-purple-50/70 rounded-2xl border border-purple-200 space-y-3">
                    <div class="flex items-center justify-between border-b border-purple-200 pb-2">
                        <span class="font-bold text-purple-900 uppercase tracking-wide flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-purple-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            Descargos y Pruebas del Ciudadano
                        </span>
                        <span class="px-2 py-0.5 bg-purple-200 text-purple-800 rounded font-mono text-[10px]" x-text="sancionSeleccionada?.estatus"></span>
                    </div>

                    <!-- Texto Descriptivo de Subsanación -->
                    <div>
                        <span class="text-purple-700 font-semibold uppercase block text-[10px]">Descripción de Acciones Correctoras:</span>
                        <p class="text-slate-700 mt-1 bg-white p-3 rounded-xl border border-purple-100 leading-relaxed" 
                           x-text="sancionSeleccionada?.observaciones_subsanacion || 'El ciudadano no ha ingresado una descripción detallada de subsanación.'">
                        </p>
                    </div>

                    <!-- Evidencias / Archivos Subidos por el Ciudadano -->
                    <div>
                        <span class="text-purple-700 font-semibold uppercase block text-[10px] mb-1">Archivos Adjuntos de Subsanación:</span>
                        <template x-if="sancionSeleccionada?.evidencias_subsanacion && sancionSeleccionada.evidencias_subsanacion.length > 0">
                            <div class="flex flex-wrap gap-2">
                                <template x-for="file in sancionSeleccionada.evidencias_subsanacion" :key="file">
                                    <a :href="'/sanciones/evidencia/' + file" target="_blank" class="px-3 py-1.5 bg-white border border-purple-200 hover:border-purple-500 text-purple-800 rounded-xl font-bold transition flex items-center gap-1.5 shadow-2xs">
                                        <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        <span>Ver Comprobante / Evidencia</span>
                                    </a>
                                </template>
                            </div>
                        </template>
                        <template x-if="!sancionSeleccionada?.evidencias_subsanacion || sancionSeleccionada.evidencias_subsanacion.length === 0">
                            <p class="text-slate-400 italic">No se han adjuntado archivos por parte del ciudadano.</p>
                        </template>
                    </div>
                </div>

                <!-- SECCIÓN DE DICTAMEN DEL DIRECTOR -->
                <div class="space-y-4 pt-2 border-t border-slate-100">
                    <h4 class="font-bold text-slate-800 uppercase tracking-wider text-[11px]">Decisión Administrativa</h4>

                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1">Dictamen Final del Director *</label>
                        <select name="estatus" required class="w-full bg-slate-50 border border-slate-300 rounded-xl p-2.5 text-slate-800 focus:ring-2 focus:ring-emerald-500">
                            <option value="SUBSANADA">SUBSANADA (Aprobada subsanación y retirada la sanción)</option>
                            <option value="DESESTIMADA">DESESTIMADA (Anulada por improcedencia)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1">Observaciones / Justificación de Cierre *</label>
                        <textarea name="observaciones_resolucion" rows="3" required placeholder="Ingresa los motivos o acuerdos alcanzados para el retiro del expediente..." class="w-full bg-slate-50 border border-slate-300 rounded-xl p-2.5 text-slate-800"></textarea>
                    </div>

                    <!-- Dropzone Drag & Drop para Acta / Resolución de Cierre -->
                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1">Adjuntar Acta Oficial de Cierre / Resolución (Opcional)</label>
                        <div class="relative border-2 border-dashed border-slate-300 hover:border-emerald-500 rounded-2xl p-4 text-center bg-slate-50/50 hover:bg-emerald-50/30 transition-all cursor-pointer"
                             @dragover.prevent="$el.classList.add('border-emerald-500', 'bg-emerald-50/50')"
                             @dragleave.prevent="$el.classList.remove('border-emerald-500', 'bg-emerald-50/50')"
                             @drop.prevent="$el.classList.remove('border-emerald-500', 'bg-emerald-50/50'); $refs.resolucionFileInput.files = $event.dataTransfer.files; handleResolucionFile($refs.resolucionFileInput.files)">
                            
                            <input type="file" 
                                   name="evidencia_resolucion" 
                                   x-ref="resolucionFileInput" 
                                   @change="handleResolucionFile($refs.resolucionFileInput.files)" 
                                   accept=".pdf,.jpg,.png,.jpeg" 
                                   class="hidden">

                            <div @click="$refs.resolucionFileInput.click()" class="space-y-1">
                                <svg class="w-7 h-7 mx-auto text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 0115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                                <p class="font-bold text-slate-700 text-xs">Arrastra el archivo de resolución o haz clic para examinar</p>
                                <p class="text-[10px] text-slate-400">PDF, JPG o PNG hasta 5MB</p>
                            </div>

                            <template x-if="fileResolucionName">
                                <div class="mt-2 p-2 bg-emerald-50 text-emerald-800 rounded-xl text-xs font-bold border border-emerald-200 flex items-center justify-between">
                                    <span class="truncate" x-text="fileResolucionName"></span>
                                    <button type="button" @click.stop="fileResolucionName = ''; $refs.resolucionFileInput.value = ''" class="text-rose-600 font-bold cursor-pointer">&times;</button>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- Footer Fijo -->
                <div class="flex justify-end gap-2 pt-4 border-t border-slate-100 shrink-0">
                    <button type="button" @click="modalResolver = false" class="px-4 py-2.5 bg-slate-100 text-slate-700 font-semibold rounded-xl hover:bg-slate-200 transition cursor-pointer">
                        Cancelar
                    </button>
                    <button type="submit" class="px-5 py-2.5 bg-emerald-600 text-white font-bold rounded-xl shadow-sm hover:bg-emerald-700 transition cursor-pointer flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Registrar Resolución y Cerrar
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>

<script>
function sancionManager() {
    return {
        modalNuevaSancion: false,
        modalResolver: false,
        sancionSeleccionada: null,
        tipoInfraccion: 'FALTA_DECLARACION_JURADA',
        tipoInfraccionManual: '',
        baseLegal: 'Art. 45 de la Ordenanza Municipal de Manejo de Residuos y Desechos Sólidos',
        fechaLimite: '',
        fileName: '',
        fileResolucionName: '',

        init() {
            this.setPlazoDays(10); // LOPA por defecto (10 días hábiles)
        },

        actualizarBaseLegal() {
            const bases = {
                'FALTA_DECLARACION_JURADA': 'Art. 45 de la Ordenanza Municipal de Residuos / Art. 72 Ley Orgánica del Ambiente',
                'INCONGRUENCIA_DISPOSICION': 'Art. 52 Ordenanza Municipal / Art. 89 Ley de Gestión Integral de la Basura',
                'VERTIDO_NO_AUTORIZADO': 'Art. 60 Ordenanza Municipal / Art. 83 Ley Penal del Ambiente',
                'OBSTACULIZACION_FISCALIA': 'Art. 38 Ordenanza Municipal / Art. 12 Ley Orgánica de Procedimientos Administrativos',
                'INCUMPLIMIENTO_MEDIDAS': 'Art. 68 Ordenanza Municipal / Art. 110 Ley Orgánica del Ambiente',
            };

            if (this.tipoInfraccion !== 'OTRO') {
                this.baseLegal = bases[this.tipoInfraccion] || 'Ordenanza Municipal de Residuos Sólidos';
            } else {
                this.baseLegal = '';
            }
        },

        setPlazoDays(days) {
            const date = new Date();
            date.setDate(date.getDate() + days);
            this.fechaLimite = date.toISOString().split('T')[0];
        },

        handleFiles(files) {
            if (files && files.length > 0) {
                this.fileName = files[0].name;
            }
        },

        handleResolucionFile(files) {
            if (files && files.length > 0) {
                this.fileResolucionName = files[0].name;
            }
        }
    }
}
</script>
</x-layouts.app>