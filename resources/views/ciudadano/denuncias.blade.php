<x-layouts.app title="Reportes y Denuncias" active="denuncias">
    <div 
        class="max-w-7xl mx-auto space-y-8" 
        x-data="{ 
            openModal: false, 
            openDetailModal: false,
            showSuccessAlert: true,
            filtroEstatus: 'todos',
            selectedDenuncia: null,
            isSubmitting: false,

            denunciasLista: {{ json_encode($denuncias->pluck('estatus')->map(fn($e) => strtolower(trim($e)))) }},

            tieneDenunciasVisibles() {
                if (this.filtroEstatus === 'todos') return this.denunciasLista.length > 0;
                return this.denunciasLista.includes(this.filtroEstatus.toLowerCase());
            },

            // Formulario
            tipoContaminacion: '', 
            otroTipoContaminacion: '',
            comuna: '',
            sector: '',
            puntoReferencia: '',
            gravedad: 'baja',
            justificacionGravedad: '',
            descripcion: '',
            evidenciasCount: 0,
            evidenciasNombres: [],
            fileError: '',
            territorios: {{ json_encode($territorios) }},

            handleFileChange(event) {
                const files = Array.from(event.target.files);
                if (files.length === 0) return;

                this.fileError = '';

                if (files.length > 5) {
                    this.fileError = 'Solo puedes adjuntar un máximo de 5 imágenes.';
                    event.target.value = '';
                    this.evidenciasCount = 0;
                    this.evidenciasNombres = [];
                    return;
                }

                const maxSize = 5 * 1024 * 1024;
                const invalidFile = files.find(f => f.size > maxSize);
                if (invalidFile) {
                    this.fileError = `El archivo '${invalidFile.name}' supera el peso máximo permitido (5MB).`;
                    event.target.value = '';
                    this.evidenciasCount = 0;
                    this.evidenciasNombres = [];
                    return;
                }

                this.evidenciasCount = files.length;
                this.evidenciasNombres = files.map(f => f.name);
            },

            verDetalle(denuncia) {
                this.selectedDenuncia = denuncia;
                this.openDetailModal = true;
            },

            get isFormValid() {
                if (!this.tipoContaminacion || !this.comuna || !this.sector) return false;
                if (this.puntoReferencia.trim().length < 3 || this.descripcion.trim().length < 5) return false;
                if (this.tipoContaminacion === 'Otro' && this.otroTipoContaminacion.trim().length < 3) return false;
                
                if (this.gravedad === 'alta' && this.justificacionGravedad.trim().length < 3) return false;
                
                if (this.evidenciasCount < 1 || this.fileError !== '') return false;
                return true;
            }
        }"
    >
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-xl font-bold text-slate-800">Reportes y Denuncias Ambientales</h1>
                <p class="text-xs text-slate-500 mt-0.5">Gestión de alertas ciudadanas en el Municipio Carrizal.</p>
            </div>
            <button 
                @click="openModal = true"
                type="button"
                class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-amber-500 hover:bg-amber-600 active:bg-amber-700 text-white font-bold text-xs rounded-xl shadow-md transition-all cursor-pointer border-0"
            >
                <svg class="w-4 h-4 text-white shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                <span class="text-white">Nueva Denuncia</span>
            </button>
        </div>

        {{-- Alerta de Éxito --}}
        @if(session('success'))
            <div 
                x-show="showSuccessAlert" 
                x-transition 
                class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold rounded-2xl flex items-center justify-between gap-2 shadow-xs"
            >
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span>{{ session('success') }}</span>
                </div>
                <button 
                    @click="showSuccessAlert = false" 
                    type="button" 
                    class="text-emerald-500 hover:text-emerald-800 p-1 rounded-lg transition-colors cursor-pointer"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        @endif

        <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <h3 class="text-sm font-bold text-slate-800">Mis Denuncias Registradas</h3>
                <div class="flex items-center gap-2">
                    <label for="filtro_estatus" class="text-xs font-semibold text-slate-500">Filtrar por estatus:</label>
                    <select 
                        id="filtro_estatus"
                        x-model="filtroEstatus" 
                        class="border border-slate-200 rounded-xl px-3 py-1.5 text-xs bg-white text-slate-800 font-medium focus:ring-2 focus:ring-amber-500 focus:outline-none cursor-pointer"
                    >
                        <option value="todos">Todos los estatus</option>
                        <option value="pendiente">Pendiente</option>
                        <option value="inspector_asignado">Inspector Asignado</option>
                        <option value="en_revision">En Inspección</option>
                        <option value="atendido">Atendido</option>
                        <option value="desestimado">Desestimado</option>
                    </select>
                </div>
            </div>
            
            @if($denuncias->isEmpty())
                <div class="p-8 text-center text-slate-400 text-xs">
                    No has registrado ninguna denuncia ambiental hasta el momento.
                </div>
            @else
                <div>
                    <template x-if="!tieneDenunciasVisibles()">
                        <div class="p-8 text-center text-slate-400 text-xs italic space-y-1">
                            <svg class="w-8 h-8 text-slate-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                            </svg>
                            <p class="font-medium text-slate-500">No hay denuncias que cumplan con el filtro seleccionado.</p>
                            <p class="text-[11px] text-slate-400">Prueba cambiando el estatus en el selector superior.</p>
                        </div>
                    </template>

                    <div x-show="tieneDenunciasVisibles()" x-cloak class="overflow-x-auto">
                        <table class="w-full text-left text-xs text-slate-600">
                            <thead class="bg-slate-50 text-slate-400 uppercase font-bold text-[10px] tracking-wider border-b border-slate-100">
                                <tr>
                                    <th class="p-4">Tipo</th>
                                    <th class="p-4">Ubicación (Comuna / Sector)</th>
                                    <th class="p-4">Gravedad</th>
                                    <th class="p-4">Fecha</th>
                                    <th class="p-4">Estatus</th>
                                    <th class="p-4 text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 font-medium">
                                @foreach ($denuncias as$denuncia)
                                    <tr 
                                        x-show="filtroEstatus === 'todos' || filtroEstatus === '{{ strtolower(trim($denuncia->estatus)) }}'"
                                        class="hover:bg-slate-50/50"
                                    >
                                        <td class="p-4 font-bold text-slate-800">
                                            {{ $denuncia->tipo_contaminacion === 'Otro' ? $denuncia->otro_tipo_contaminacion :$denuncia->tipo_contaminacion }}
                                        </td>
                                        <td class="p-4">{{ $denuncia->comuna }} — {{$denuncia->sector }}</td>
                                        <td class="p-4">
                                            <span class="px-2 py-0.5 rounded-md text-[10px] font-bold uppercase
                                                @if($denuncia->nivel_gravedad === 'alta') bg-rose-100 text-rose-700
                                                @elseif($denuncia->nivel_gravedad === 'media') bg-amber-100 text-amber-700
                                                @else bg-slate-100 text-slate-600 @endif"
                                            >
                                                {{ $denuncia->nivel_gravedad }}
                                            </span>
                                        </td>
                                        <td class="p-4 text-slate-400">{{ $denuncia->created_at->format('d/m/Y') }}</td>
                                        <td class="p-4">
                                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase inline-block border
                                                @if($denuncia->estatus === 'pendiente') bg-amber-50 text-amber-700 border-amber-200
                                                @elseif($denuncia->estatus === 'inspector_asignado') bg-indigo-50 text-indigo-700 border-indigo-200
                                                @elseif($denuncia->estatus === 'en_revision') bg-blue-50 text-blue-700 border-blue-200
                                                @elseif($denuncia->estatus === 'atendido') bg-emerald-50 text-emerald-700 border-emerald-200
                                                @else bg-slate-100 text-slate-600 border-slate-200 @endif"
                                            >
                                                {{ str_replace('_', ' ', $denuncia->estatus) }}
                                            </span>
                                        </td>
                                        <td class="p-4 text-center">
                                            <button 
                                                type="button" 
                                                @click="verDetalle({{ json_encode($denuncia) }})"
                                                class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-lg transition-colors cursor-pointer"
                                            >
                                                Ver Detalle
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>

        {{-- Modal de Detalle de la Denuncia --}}
        <div x-show="openDetailModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/60 backdrop-blur-xs">
            <div @click.outside="openDetailModal = false" class="bg-white w-full max-w-2xl rounded-3xl p-6 shadow-2xl space-y-6 max-h-[90vh] overflow-y-auto">
                
                <div class="flex items-start justify-between border-b border-slate-100 pb-4">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-bold text-amber-600 bg-amber-50 px-2 py-0.5 rounded-md border border-amber-200">
                                Denuncia #<span x-text="selectedDenuncia?.id"></span>
                            </span>
                            <span class="text-xs text-slate-400" x-text="selectedDenuncia?.created_at ? new Date(selectedDenuncia.created_at).toLocaleDateString() : ''"></span>
                        </div>
                        <h3 class="text-base font-bold text-slate-800 mt-1" x-text="selectedDenuncia?.tipo_contaminacion === 'Otro' ? selectedDenuncia?.otro_tipo_contaminacion : selectedDenuncia?.tipo_contaminacion"></h3>
                    </div>
                    <button @click="openDetailModal = false" type="button" class="text-slate-400 hover:text-slate-600 text-xl font-bold cursor-pointer leading-none">&times;</button>
                </div>

                <div class="space-y-5 text-xs text-slate-700">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 bg-slate-50 p-4 rounded-2xl border border-slate-100">
                        <div>
                            <span class="block text-[10px] uppercase font-bold text-slate-400 mb-1">Estatus Actual</span>
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase inline-block"
                                :class="{
                                    'bg-amber-50 text-amber-700 border border-amber-200': selectedDenuncia?.estatus === 'pendiente',
                                    'bg-indigo-50 text-indigo-700 border border-indigo-200': selectedDenuncia?.estatus === 'inspector_asignado',
                                    'bg-blue-50 text-blue-700 border border-blue-200': selectedDenuncia?.estatus === 'en_revision',
                                    'bg-emerald-50 text-emerald-700 border border-emerald-200': selectedDenuncia?.estatus === 'atendido',
                                    'bg-slate-100 text-slate-600 border border-slate-200': selectedDenuncia?.estatus === 'desestimado'
                                }"
                                x-text="selectedDenuncia?.estatus ? selectedDenuncia.estatus.replace('_', ' ') : ''"
                            ></span>
                        </div>
                        <div>
                            <span class="block text-[10px] uppercase font-bold text-slate-400 mb-1">Nivel de Gravedad</span>
                            <span class="px-2 py-0.5 rounded-md text-[10px] font-bold uppercase inline-block"
                                :class="{
                                    'bg-slate-100 text-slate-600': selectedDenuncia?.nivel_gravedad === 'baja',
                                    'bg-amber-100 text-amber-700': selectedDenuncia?.nivel_gravedad === 'media',
                                    'bg-rose-100 text-rose-700': selectedDenuncia?.nivel_gravedad === 'alta'
                                }"
                                x-text="selectedDenuncia?.nivel_gravedad"
                            ></span>
                        </div>
                    </div>

                    <div class="space-y-1">
                        <span class="block text-[10px] uppercase font-bold text-slate-400">Ubicación y Referencia</span>
                        <div class="font-semibold text-slate-800">
                            <span x-text="selectedDenuncia?.comuna"></span> — <span x-text="selectedDenuncia?.sector"></span>
                        </div>
                        <p class="text-slate-600 bg-slate-50/50 p-3 rounded-xl border border-slate-100 italic" x-text="selectedDenuncia?.punto_referencia"></p>
                    </div>

                    <template x-if="selectedDenuncia?.nivel_gravedad === 'alta' && selectedDenuncia?.justificacion_gravedad">
                        <div class="space-y-1 bg-rose-50/50 p-3.5 rounded-xl border border-rose-100">
                            <span class="block text-[10px] uppercase font-bold text-rose-600">Justificación de Gravedad Alta</span>
                            <p class="text-rose-900 font-medium" x-text="selectedDenuncia?.justificacion_gravedad"></p>
                        </div>
                    </template>

                    <div class="space-y-1">
                        <span class="block text-[10px] uppercase font-bold text-slate-400">Descripción del Problema</span>
                        <p class="text-slate-800 bg-slate-50/50 p-3.5 rounded-xl border border-slate-100 whitespace-pre-line leading-relaxed" x-text="selectedDenuncia?.descripcion"></p>
                    </div>

                    <div class="space-y-2">
                        <span class="block text-[10px] uppercase font-bold text-slate-400">Evidencias Adjuntas (<span x-text="selectedDenuncia?.evidencias?.length || 0"></span>)</span>
                        
                        <template x-if="selectedDenuncia?.evidencias?.length > 0">
                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                                <template x-for="(imgUrl, idx) in selectedDenuncia.evidencias" :key="idx">
                                    <div class="relative group rounded-xl overflow-hidden border border-slate-200 bg-slate-50 h-28">
                                        <img :src="imgUrl" alt="Evidencia" class="w-full h-full object-cover">
                                        <a :href="imgUrl" target="_blank" class="absolute inset-0 bg-slate-900/60 opacity-0 group-hover:opacity-100 transition-opacity flex flex-col items-center justify-center text-white text-[10px] font-semibold p-2 text-center gap-1">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                            <span>Abrir en pestaña nueva</span>
                                        </a>
                                    </div>
                                </template>
                            </div>
                        </template>

                        <template x-if="!selectedDenuncia?.evidencias?.length">
                            <p class="text-slate-400 text-xs italic">No se adjuntaron evidencias para este reporte.</p>
                        </template>
                    </div>
                </div>

                <div class="flex justify-end pt-3 border-t border-slate-100">
                    <button type="button" @click="openDetailModal = false" class="px-5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-xl transition-colors cursor-pointer">Cerrar</button>
                </div>
            </div>
        </div>

        {{-- Modal Nueva Denuncia --}}
        <div x-show="openModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/60 backdrop-blur-xs">
            <div @click.outside="openModal = false" class="bg-white w-full max-w-lg rounded-3xl p-6 shadow-2xl space-y-4 max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h3 class="text-sm font-bold text-slate-800">Registrar Denuncia Ambiental</h3>
                    <button @click="openModal = false" type="button" class="text-slate-400 hover:text-slate-600 text-lg font-bold cursor-pointer leading-none">&times;</button>
                </div>

                <form 
                    action="{{ route('ciudadano.denuncias.store') }}" 
                    method="POST" 
                    enctype="multipart/form-data" 
                    class="space-y-4"
                    @submit="isSubmitting = true"
                >
                    @csrf
                    
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Tipo de Contaminación / Incidencia <span class="text-rose-500">*</span></label>
                        <select name="tipo_contaminacion" x-model="tipoContaminacion" required class="w-full border border-slate-200 rounded-xl px-3 py-2 text-xs bg-white text-slate-800 focus:ring-2 focus:ring-amber-500 focus:outline-none">
                            <option value="">Selecciona una opción</option>
                            <option value="Vertedero no autorizado">Vertedero no autorizado / Acumulación de desechos</option>
                            <option value="Quema no autorizada">Quema no autorizada de basura / vegetación</option>
                            <option value="Tala o poda ilegal">Tala o poda ilegal de árboles</option>
                            <option value="Vertido en cauces">Vertido de efluentes o desechos en cauces/quebradas</option>
                            <option value="Sustancias peligrosas">Manejo indebido de sustancias o residuos peligrosos</option>
                            <option value="Otro">Otro tipo de afectación ambiental</option>
                        </select>
                    </div>

                    <div x-show="tipoContaminacion === 'Otro'" x-cloak class="space-y-1">
                        <div class="flex justify-between items-center">
                            <label class="block text-xs font-semibold text-slate-700">Título / Especificación de la incidencia <span class="text-rose-500">*</span></label>
                            <span class="text-[10px]" :class="otroTipoContaminacion.trim().length >= 3 ? 'text-emerald-600' : 'text-slate-400'">
                                <span x-text="otroTipoContaminacion.trim().length"></span>/3 mín
                            </span>
                        </div>
                        <input type="text" name="otro_tipo_contaminacion" x-model="otroTipoContaminacion" maxlength="100" placeholder="Ej. Emisión de polvos industriales" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-xs bg-white text-slate-800 focus:ring-2 focus:ring-amber-500 focus:outline-none">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Circuito / Comuna <span class="text-rose-500">*</span></label>
                            <select name="comuna" x-model="comuna" @change="sector = ''" required class="w-full border border-slate-200 rounded-xl px-3 py-2 text-xs bg-white text-slate-800 focus:ring-2 focus:ring-amber-500 focus:outline-none">
                                <option value="">Selecciona Comuna / Circuito</option>
                                <template x-for="(sectores, comunaName) in territorios" :key="comunaName">
                                    <option :value="comunaName" x-text="comunaName"></option>
                                </template>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Sector <span class="text-rose-500">*</span></label>
                            <select name="sector" x-model="sector" :disabled="!comuna" required class="w-full border border-slate-200 rounded-xl px-3 py-2 text-xs bg-white text-slate-800 focus:ring-2 focus:ring-amber-500 focus:outline-none disabled:bg-slate-100 disabled:text-slate-400">
                                <option value="">Selecciona el Sector</option>
                                <template x-for="sec in (territorios[comuna] || [])" :key="sec">
                                    <option :value="sec" x-text="sec"></option>
                                </template>
                            </select>
                        </div>
                    </div>

                    <div>
                        <div class="flex justify-between items-center mb-1">
                            <label class="block text-xs font-semibold text-slate-700">Dirección Exacta / Referencia <span class="text-rose-500">*</span></label>
                            <span class="text-[10px]" :class="puntoReferencia.trim().length >= 3 ? 'text-emerald-600' : 'text-slate-400'">
                                <span x-text="puntoReferencia.trim().length"></span>/3 mín
                            </span>
                        </div>
                        <input type="text" name="punto_referencia" x-model="puntoReferencia" placeholder="Ej. Av. Principal, frente al comercio X" required class="w-full border border-slate-200 rounded-xl px-3 py-2 text-xs bg-white text-slate-800 focus:ring-2 focus:ring-amber-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Nivel de Gravedad <span class="text-rose-500">*</span></label>
                        <select name="nivel_gravedad" x-model="gravedad" required class="w-full border border-slate-200 rounded-xl px-3 py-2 text-xs bg-white text-slate-800 focus:ring-2 focus:ring-amber-500 focus:outline-none">
                            <option value="baja">Baja (Sin riesgo directo o afectación menor)</option>
                            <option value="media">Media (Afectación comunitaria moderada)</option>
                            <option value="alta">Alta (Riesgo inminente para la salud o ambiente)</option>
                        </select>
                    </div>

                    <div x-show="gravedad === 'alta'" x-cloak class="space-y-1">
                        <div class="flex justify-between items-center">
                            <label class="block text-xs font-semibold text-rose-700">Justificación de Gravedad Alta <span class="text-rose-500">*</span></label>
                            <span class="text-[10px]" :class="justificacionGravedad.trim().length >= 3 ? 'text-emerald-600' : 'text-rose-500'">
                                <span x-text="justificacionGravedad.trim().length"></span>/3 mín
                            </span>
                        </div>
                        <textarea name="justificacion_gravedad" x-model="justificacionGravedad" rows="2" placeholder="Explique por qué considera que la situación requiere atención prioritaria..." class="w-full border border-rose-200 bg-rose-50/30 rounded-xl px-3 py-2 text-xs text-slate-800 focus:ring-2 focus:ring-rose-500 focus:outline-none resize-none"></textarea>
                    </div>

                    <div>
                        <div class="flex justify-between items-center mb-1">
                            <label class="block text-xs font-semibold text-slate-700">Descripción detallada <span class="text-rose-500">*</span></label>
                            <span class="text-[10px]" :class="descripcion.trim().length >= 5 ? 'text-emerald-600' : 'text-slate-400'">
                                <span x-text="descripcion.trim().length"></span>/5 mín
                            </span>
                        </div>
                        <textarea name="descripcion" x-model="descripcion" rows="3" placeholder="Describe detalladamente los hechos acontecidos..." required class="w-full border border-slate-200 rounded-xl px-3 py-2 text-xs bg-white text-slate-800 focus:ring-2 focus:ring-amber-500 focus:outline-none resize-none"></textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                            Evidencias Fotográficas <span class="text-rose-500">*</span>
                        </label>

                        <div 
                            @click="$refs.fileInput.click()"
                            class="relative border-2 border-dashed rounded-2xl p-4 transition-all text-center group cursor-pointer flex flex-col items-center justify-center min-h-[130px]"
                            :class="evidenciasCount > 0 ? 'border-emerald-400 bg-emerald-50/20' : 'border-slate-200 hover:border-amber-400 bg-slate-50/50 hover:bg-amber-50/20'"
                        >
                            <input 
                                x-ref="fileInput"
                                type="file" 
                                name="evidencias[]" 
                                multiple 
                                accept="image/jpeg,image/png,image/webp" 
                                @change="handleFileChange" 
                                class="hidden"
                            >

                            <div class="p-2.5 rounded-full shadow-xs transition-colors mb-2"
                                :class="evidenciasCount > 0 ? 'bg-emerald-100 text-emerald-600' : 'bg-white group-hover:bg-amber-100 text-amber-500'"
                            >
                                <template x-if="evidenciasCount === 0">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 002-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </template>
                                <template x-if="evidenciasCount > 0">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </template>
                            </div>

                            <div class="space-y-1">
                                <template x-if="evidenciasCount === 0">
                                    <div>
                                        <span class="text-xs font-bold text-amber-600 group-hover:underline">Haz clic para adjuntar imágenes</span>
                                        <span class="text-xs text-slate-500"> o arrastra los archivos aquí</span>
                                        <p class="text-[10px] text-slate-400 mt-0.5">JPG, PNG, WEBP (Máx. 5MB por imagen, hasta 5 imágenes)</p>
                                    </div>
                                </template>

                                <template x-if="evidenciasCount > 0">
                                    <div>
                                        <div class="text-xs font-bold text-emerald-700">
                                            <span x-text="evidenciasCount"></span> 
                                            <span x-text="evidenciasCount === 1 ? 'imagen seleccionada' : 'imágenes seleccionadas'"></span>
                                        </div>
                                        <ul class="text-[10px] text-slate-600 max-w-xs truncate space-y-0.5 mt-2 bg-white/60 p-2 rounded-xl border border-emerald-100">
                                            <template x-for="nombre in evidenciasNombres" :key="nombre">
                                                <li x-text="nombre" class="truncate"></li>
                                            </template>
                                        </ul>
                                        <p class="text-[10px] text-emerald-600 mt-1 hover:underline">Haz clic si deseas cambiar la selección</p>
                                    </div>
                                </template>
                            </div>

                            <template x-if="fileError">
                                <p class="text-[11px] font-semibold text-rose-600 mt-2" x-text="fileError"></p>
                            </template>
                        </div>
                    </div>
                    
                    <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                        <button 
                            type="button" 
                            @click="openModal = false" 
                            :disabled="isSubmitting"
                            class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-xl transition-colors cursor-pointer disabled:opacity-50"
                        >
                            Cancelar
                        </button>
                        <button 
                            type="submit" 
                            :disabled="!isFormValid || isSubmitting"
                            class="px-5 py-2 bg-amber-500 hover:bg-amber-600 active:bg-amber-700 text-white font-bold text-xs rounded-xl shadow-xs transition-all cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
                        >
                            <template x-if="isSubmitting">
                                <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </template>
                            <span x-text="isSubmitting ? 'Guardando...' : 'Registrar Denuncia'"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-layouts.app>