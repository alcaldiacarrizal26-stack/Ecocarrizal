<x-layouts.app title="Nueva Declaración de Residuos" active="empresa_declaraciones">

    @php
        $perfil = $empresa->perfilAmbiental ?? $perfilAmbiental ?? null;
        $rolAmbiental =$perfil?->rol_ambiental ?? 'GENERADOR';

        $datosIniciales = [
            'rol_ambiental' => $rolAmbiental,
            'manejadora_nombre' => $perfil?->manejadora_contratada ?? '',
            'manejadora_rif'    => $perfil?->manejadora_rif ?? '',
            'manejadora_racda'  => $perfil?->manejadora_racda ?? '',
            'generador_nombre'  => $perfil?->generador_exclusivo_nombre ?? '',
            'generador_rif'     => $perfil?->generador_exclusivo_rif ?? '',
            'generador_racda'   => $perfil?->generador_exclusivo_racda ?? '',
        ];
    @endphp

    <div class="p-6 max-w-5xl mx-auto space-y-6 bg-slate-50 min-h-screen" 
         x-data="declaracionResiduos({{ json_encode($datosIniciales) }})">
        
        <!-- Header con Insignia Dinámica -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
            <div>
                <div class="flex items-center gap-2">
                    <h1 class="text-2xl font-bold text-slate-800 tracking-tight">Declaración de Residuos y Desechos</h1>
                    
                    @if ($rolAmbiental === 'MANEJADOR')
                        <span class="px-2.5 py-0.5 text-xs font-extrabold rounded-full bg-amber-100 text-amber-800 border border-amber-300">
                            EMPRESA MANEJADORA
                        </span>
                    @elseif ($rolAmbiental === 'GENERADOR')
                        <span class="px-2.5 py-0.5 text-xs font-extrabold rounded-full bg-emerald-100 text-emerald-800 border border-emerald-300">
                            EMPRESA GENERADORA
                        </span>
                    @else
                        <span class="px-2.5 py-0.5 text-xs font-extrabold rounded-full bg-slate-100 text-slate-700 border border-slate-300">
                            NO APLICA (BAJO IMPACTO)
                        </span>
                    @endif
                </div>
                <p class="text-sm text-slate-500 mt-1">
                    Empresa: <span class="text-slate-700 font-semibold">{{ $empresa->razon_social ?? $empresa->nombre_comercial }}</span> 
                    <span class="font-mono text-xs text-slate-400">({{ $empresa->rif }})</span>
                </p>
            </div>
            <a href="{{ route('ciudadano.declaraciones.index') }}" 
               class="inline-flex items-center px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-medium rounded-xl border border-slate-300 transition-colors">
                &larr; Volver al Historial
            </a>
        </div>

        <form action="{{ route('ciudadano.declaraciones.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <input type="hidden" name="rol_declarante" value="{{ $rolAmbiental }}">

            <!-- Sección 1: Carga Dinámica del Formulario según Rol -->
            @if ($rolAmbiental === 'MANEJADOR')
                @include('ciudadano.empresa.partials.form_manejador')
            @elseif ($rolAmbiental === 'GENERADOR')
                @include('ciudadano.empresa.partials.form_generador')
            @else
                @include('ciudadano.empresa.partials.form_no_aplica')
            @endif


            <!-- Muestra Secciones 2 y 3 ÚNICAMENTE si no es NO_APLICA -->
            @if ($rolAmbiental !== 'NO_APLICA')
                
                <!-- Sección 2: Desglose por Tipo de Residuo -->
                <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-4">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                        <div>
                            <h2 class="text-base font-semibold text-slate-800">2. Desglose por Tipo de Residuo</h2>
                            <p class="text-xs text-slate-500">Selecciona el residuo e indica las cantidades operadas.</p>
                        </div>
                        <button type="button" @click="agregarRenglon()" 
                                class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-lg transition-colors flex items-center gap-1 shadow-sm">
                            + Agregar Residuo
                        </button>
                    </div>

                    <div class="space-y-4">
                        <template x-for="(item, index) in detalles" :key="index">
                            <div class="bg-slate-50 p-4 rounded-xl border border-slate-200 relative space-y-3">
                                <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-end">
                                    
                                    <!-- Tipo de Residuo -->
                                    <div class="md:col-span-4">
                                        <label class="block text-xs font-medium text-slate-500 mb-1">Tipo de Residuo</label>
                                        <select x-model="item.residuo_id" 
                                                @change="onResiduoSelect(index)" 
                                                class="w-full bg-white border border-slate-300 rounded-lg px-3 py-2 text-sm text-slate-800 focus:outline-none focus:border-emerald-500">
                                            <option value="">-- Seleccione Residuo --</option>
                                            <template x-for="res in catalogoResiduos" :key="res.id">
                                                <option :value="res.id" x-text="res.nombre"></option>
                                            </template>
                                        </select>

                                        <div x-show="item.residuo_id === 'OTRO'" class="mt-2">
                                            <input type="text" 
                                                   x-model="item.custom_nombre" 
                                                   @input="item.nombre_residuo = item.custom_nombre" 
                                                   placeholder="Especifique el tipo de residuo..."
                                                   class="w-full bg-white border border-slate-300 rounded-lg px-3 py-1.5 text-xs text-slate-800 focus:outline-none focus:border-emerald-500">
                                        </div>

                                        <input type="hidden" :name="`detalles[${index}][nombre_residuo]`" :value="item.nombre_residuo">
                                        <input type="hidden" :name="`detalles[${index}][codigo_residuo]`" :value="slugify(item.nombre_residuo)">
                                    </div>

                                    <!-- Categoría Automática -->
                                    <div class="md:col-span-2">
                                        <label class="block text-xs font-medium text-slate-500 mb-1">Categoría</label>
                                        <div class="px-3 py-2 bg-slate-100 border border-slate-200 rounded-lg text-xs font-semibold text-slate-700 text-center uppercase"
                                             x-text="item.categoria">
                                        </div>
                                        <input type="hidden" :name="`detalles[${index}][categoria]`" :value="item.categoria">
                                    </div>

                                    <!-- Operación Manejo (Manejadores) -->
                                    <template x-if="rol_ambiental === 'MANEJADOR'">
                                        <div class="md:col-span-3">
                                            <label class="block text-xs font-medium text-slate-500 mb-1">Tipo Operación</label>
                                            <select :name="`detalles[${index}][tipo_operacion]`" x-model="item.tipo_operacion" required class="w-full bg-white border border-slate-300 rounded-lg px-2.5 py-2 text-xs text-slate-800 focus:outline-none focus:border-emerald-500">
                                                <option value="RECOLECCION_TRANSPORTE">Recolección / Transporte</option>
                                                <option value="ALMACENAMIENTO_TEMPORAL">Almacenamiento Temporal</option>
                                                <option value="TRATAMIENTO">Tratamiento</option>
                                                <option value="RECICLAJE_VALORIZACION">Reciclaje / Valorización</option>
                                                <option value="DISPOSICION_FINAL">Disposición Final</option>
                                            </select>
                                        </div>
                                    </template>

                                    <!-- Cantidad -->
                                    <div :class="rol_ambiental === 'MANEJADOR' ? 'md:col-span-1.5' : 'md:col-span-2'">
                                        <label class="block text-xs font-medium text-slate-500 mb-1">Cantidad</label>
                                        <input type="number" step="0.001" min="0" :name="`detalles[${index}][cantidad]`" x-model.number="item.cantidad" required
                                               class="w-full bg-white border border-slate-300 rounded-lg px-3 py-2 text-sm text-slate-800 focus:outline-none focus:border-emerald-500">
                                    </div>

                                    <!-- Unidad de Medida -->
                                    <div :class="rol_ambiental === 'MANEJADOR' ? 'md:col-span-1.5' : 'md:col-span-3'">
                                        <label class="block text-xs font-medium text-slate-500 mb-1">Unidad</label>
                                        <select :name="`detalles[${index}][unidad_medida]`" x-model="item.unidad_medida" class="w-full bg-white border border-slate-300 rounded-lg px-2 py-2 text-sm text-slate-800 focus:outline-none focus:border-emerald-500">
                                            <option value="TON">Toneladas (TON)</option>
                                            <option value="KG">Kilogramos (KG)</option>
                                            <option value="LITROS">Litros (L)</option>
                                            <option value="M3">Metros Cúbicos (m³)</option>
                                        </select>
                                    </div>

                                    <!-- Eliminar Renglón -->
                                    <div class="md:col-span-1 text-right">
                                        <button type="button" @click="eliminarRenglon(index)" x-show="detalles.length > 1"
                                                class="p-2 text-red-500 hover:bg-red-50 rounded-lg transition-colors">
                                            &times;
                                        </button>
                                    </div>
                                </div>

                                <!-- Adjunto por Renglón -->
                                <div class="pt-3 border-t border-slate-200 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                                    <div class="text-xs text-slate-500">
                                        Toneladas equivalentes: <span class="text-emerald-700 font-mono font-bold" x-text="calcularToneladas(item).toFixed(3) + ' TON'"></span>
                                    </div>

                                    <div class="w-full sm:w-auto">
                                        <div class="relative border-2 border-dashed border-slate-300 hover:border-emerald-500 rounded-lg px-3 py-1.5 text-center bg-white transition-colors cursor-pointer"
                                             :class="item.file_name ? 'border-emerald-500 bg-emerald-50/50' : ''">
                                            <input type="file" :name="`detalles[${index}][manifiesto]`" accept=".pdf,.jpg,.jpeg,.png"
                                                   @change="handleRenglonFile($event, index)"
                                                   class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                                            
                                            <div class="flex items-center gap-2 text-xs text-slate-600">
                                                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 0115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                                                <span x-text="item.file_name ? item.file_name : 'Adjuntar Hoja / Manifiesto (PDF/Imagen)'" class="font-medium truncate max-w-[200px]"></span>
                                                <button type="button" x-show="item.file_name" @click.stop="clearRenglonFile(index)" class="text-red-500 font-bold ml-1">&times;</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- Totales en Tiempo Real -->
                    <div class="bg-slate-100/80 p-4 rounded-xl border border-slate-200 grid grid-cols-1 sm:grid-cols-3 gap-4 text-center">
                        <div>
                            <span class="block text-xs text-slate-500 uppercase font-semibold">Comunes</span>
                            <span class="text-lg font-bold text-slate-800 font-mono" x-text="totalComunes().toFixed(3) + ' TON'"></span>
                        </div>
                        <div>
                            <span class="block text-xs text-slate-500 uppercase font-semibold">Reciclables</span>
                            <span class="text-lg font-bold text-emerald-600 font-mono" x-text="totalReciclables().toFixed(3) + ' TON'"></span>
                        </div>
                        <div>
                            <span class="block text-xs text-slate-500 uppercase font-semibold">Peligrosos</span>
                            <span class="text-lg font-bold text-amber-600 font-mono" x-text="totalPeligrosos().toFixed(3) + ' TON'"></span>
                        </div>
                    </div>
                </div>

                <!-- Sección 3: Anexos Generales -->
                <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-4">
                    <h2 class="text-base font-semibold text-slate-800 border-b border-slate-100 pb-2">3. Anexos / Manifiestos Generales</h2>
                    
                    <div class="border-2 border-dashed border-slate-300 hover:border-emerald-500 rounded-2xl p-6 text-center bg-slate-50 hover:bg-emerald-50/20 transition-all relative cursor-pointer">
                        <input type="file" name="manifiestos[]" multiple accept=".pdf,.jpg,.jpeg,.png"
                               @change="handleAnexosFiles($event)"
                               class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                        
                        <div class="space-y-2 pointer-events-none">
                            <div class="inline-flex p-3 bg-emerald-100 text-emerald-600 rounded-full">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 0115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                            </div>
                            <p class="text-sm font-semibold text-slate-700">Arrastra o haz clic para subir Certificados Generales, Guías o Manifiestos</p>
                            <p class="text-xs text-slate-400">Formatos permitidos: PDF, PNG, JPG (Selección múltiple)</p>
                        </div>
                    </div>

                    <template x-if="anexosNombres.length > 0">
                        <div class="flex flex-wrap gap-2 pt-2">
                            <template x-for="(fname, i) in anexosNombres" :key="i">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-slate-100 border border-slate-200 text-slate-700 rounded-lg text-xs font-mono">
                                    <svg class="w-3.5 h-3.5 text-emerald-600" fill="currentColor" viewBox="0 0 20 20"><path d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z"/></svg>
                                    <span x-text="fname"></span>
                                </span>
                            </template>
                        </div>
                    </template>
                </div>

            @endif

            <!-- Botones de Envio -->
            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('ciudadano.declaraciones.index') }}" 
                   class="px-5 py-2.5 bg-slate-200 hover:bg-slate-300 text-slate-700 text-sm font-medium rounded-xl transition-colors">
                    Cancelar
                </a>
                <button type="submit" 
                        class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold rounded-xl shadow-sm transition-colors">
                    {{ $rolAmbiental === 'NO_APLICA' ? 'Enviar Notificación Simplificada' : 'Enviar Declaración' }}
                </button>
            </div>
        </form>
    </div>

    <script>
    function declaracionResiduos(datosIniciales = {}) {
        return {
            rol_ambiental: datosIniciales.rol_ambiental || 'GENERADOR',
            frecuencia: 'MENSUAL',
            
            manejadora_nombre: datosIniciales.manejadora_nombre || '',
            manejadora_rif: datosIniciales.manejadora_rif || '',
            
            generador_origen_nombre: datosIniciales.generador_nombre || '',
            generador_origen_rif: datosIniciales.generador_rif || '',
            hoja_seguimiento_numero: '',
            vehiculo_placa: '',
            destino_final_nombre: '',

            anexosNombres: [],

            catalogoResiduos: [
                { id: 'DESECHOS_COMUNES', nombre: 'Desechos Comunes / Orgánicos', categoria: 'COMUN' },
                { id: 'CARTON_PAPEL', nombre: 'Cartón y Papel Reciclable', categoria: 'RECICLABLE' },
                { id: 'PLASTICOS', nombre: 'Plásticos y Envases PET', categoria: 'RECICLABLE' },
                { id: 'ACEITES_USADOS', nombre: 'Aceites Usados y Grasas Hidrocarburos', categoria: 'PELIGROSO' },
                { id: 'ELECTRONICOS', nombre: 'RAEEs (Desechos Electrónicos)', categoria: 'PELIGROSO' },
                { id: 'NINGUNO', nombre: 'No genera / Sin generación propia', categoria: 'COMUN' },
                { id: 'OTRO', nombre: 'OTRO (Especificar)', categoria: 'COMUN' }
            ],

            detalles: [
                { 
                    residuo_id: 'DESECHOS_COMUNES', 
                    nombre_residuo: 'Desechos Comunes / Orgánicos', 
                    custom_nombre: '', 
                    categoria: 'COMUN', 
                    tipo_operacion: 'RECOLECCION_TRANSPORTE',
                    cantidad: 0, 
                    unidad_medida: 'KG', 
                    file_name: '' 
                }
            ],

            seleccionarManejadoraPredefinida() {
                if (datosIniciales.manejadora_nombre) {
                    this.manejadora_nombre = datosIniciales.manejadora_nombre;
                    this.manejadora_rif = datosIniciales.manejadora_rif;
                }
            },

            seleccionarGeneradorPredefinido() {
                if (datosIniciales.generador_nombre) {
                    this.generador_origen_nombre = datosIniciales.generador_nombre;
                    this.generador_origen_rif = datosIniciales.generador_rif;
                }
            },

            onResiduoSelect(index) {
                const item = this.detalles[index];
                const res = this.catalogoResiduos.find(r => r.id === item.residuo_id);
                if (res) {
                    item.categoria = res.categoria;
                    if (res.id !== 'OTRO') {
                        item.nombre_residuo = res.nombre;
                        item.custom_nombre = '';
                    } else {
                        item.nombre_residuo = item.custom_nombre || 'Otro Residuo';
                    }
                }
            },

            agregarRenglon() {
                this.detalles.push({ 
                    residuo_id: '', 
                    nombre_residuo: '', 
                    custom_nombre: '', 
                    categoria: 'COMUN', 
                    tipo_operacion: 'RECOLECCION_TRANSPORTE',
                    cantidad: 0, 
                    unidad_medida: 'KG', 
                    file_name: '' 
                });
            },

            eliminarRenglon(index) {
                this.detalles.splice(index, 1);
            },

            handleRenglonFile(event, index) {
                const file = event.target.files[0];
                if (file) {
                    this.detalles[index].file_name = file.name;
                }
            },

            clearRenglonFile(index) {
                this.detalles[index].file_name = '';
            },

            handleAnexosFiles(event) {
                const files = event.target.files;
                this.anexosNombres = [];
                for (let i = 0; i < files.length; i++) {
                    this.anexosNombres.push(files[i].name);
                }
            },

            slugify(text) {
                return text ? text.toString().toLowerCase().trim().replace(/[\s\W-]+/g, '_') : '';
            },

            calcularToneladas(item) {
                const cantidad = parseFloat(item.cantidad) || 0;
                switch(item.unidad_medida) {
                    case 'TON': return cantidad;
                    case 'KG':
                    case 'LITROS': return cantidad * 0.001;
                    case 'M3': return cantidad * 1.0;
                    default: return cantidad * 0.001;
                }
            },

            totalComunes() {
                return this.detalles.filter(i => i.categoria === 'COMUN').reduce((sum, i) => sum + this.calcularToneladas(i), 0);
            },

            totalReciclables() {
                return this.detalles.filter(i => i.categoria === 'RECICLABLE').reduce((sum, i) => sum + this.calcularToneladas(i), 0);
            },

            totalPeligrosos() {
                return this.detalles.filter(i => i.categoria === 'PELIGROSO').reduce((sum, i) => sum + this.calcularToneladas(i), 0);
            }
        }
    }
    </script>
</x-layouts.app>