<div 
    x-show="modalOpen" 
    x-cloak
    class="fixed inset-0 z-50 overflow-y-auto"
    aria-labelledby="modal-title" 
    role="dialog" 
    aria-modal="true"
    x-data="vincularEmpresaModal({{ json_encode(\App\Services\TerritorioService::getTerritorios()) }})"
>
    <!-- Overlay -->
    <div 
        x-show="modalOpen"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-slate-950/70 backdrop-blur-sm transition-opacity"
        @click="modalOpen = false"
    ></div>

    <div class="flex min-h-full items-center justify-center p-4 sm:p-6 text-center">
        <div 
            x-show="modalOpen"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            class="relative transform overflow-hidden rounded-3xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-4xl border border-gray-100"
        >
            <!-- Header -->
            <div class="bg-[#0d5238] px-8 py-7 text-white flex flex-col sm:flex-row sm:items-center justify-between gap-6 border-b border-emerald-800/40">
                <div class="flex items-center gap-4">
                    <div class="h-12 w-12 rounded-2xl bg-white/10 border border-white/20 flex items-center justify-center shrink-0 shadow-inner">
                        <img src="{{ asset('images/carrizal_logo.png') }}" alt="Carrizal Logo" class="h-8 w-8 object-contain">
                    </div>
                    <div>
                        <h3 class="text-lg font-bold tracking-wide text-white leading-tight" id="modal-title">
                            Expediente Comercial y Perfil Ambiental
                        </h3>
                        <p class="text-xs text-emerald-200/90 font-medium mt-1 tracking-wide">
                            Alcaldía del Municipio Carrizal • Dirección de Ecología y Residuos
                        </p>
                    </div>
                </div>

                <!-- Stepper -->
                <div class="flex items-center gap-2 bg-black/25 backdrop-blur-md px-4 py-2 rounded-full text-xs font-semibold text-emerald-100 border border-white/10 self-start sm:self-auto shrink-0">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span>Paso <span x-text="step" class="text-white font-bold"></span> de 3</span>
                </div>
            </div>

            <form @submit.prevent="submitForm" enctype="multipart/form-data" class="p-8 space-y-6">
                @csrf

                <!-- PASO 1: Identificación Legal y Territorio -->
                <div x-show="step === 1" x-transition.opacity class="space-y-5">
                    <div class="border-b border-gray-100 pb-3">
                        <h4 class="text-xs font-extrabold text-gray-800 uppercase tracking-wider">1. Identificación y Ubicación Comercial</h4>
                        <p class="text-[11px] text-gray-500 mt-0.5">Datos legales, tributarios y delimitación territorial en Carrizal.</p>
                    </div>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <!-- RIF Segmentado -->
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase mb-1.5">RIF Comercial *</label>
                            <div class="flex items-center gap-1.5">
                                <select 
                                    x-model="rifInput.tipo" 
                                    @change="formatRif()"
                                    class="px-2 py-2.5 rounded-xl border border-gray-200 text-xs font-mono font-bold text-gray-800 bg-gray-50 focus:border-[#0d5238] focus:ring-1 focus:ring-[#0d5238]"
                                >
                                    <option value="J">J</option>
                                    <option value="G">G</option>
                                    <option value="V">V</option>
                                    <option value="E">E</option>
                                    <option value="P">P</option>
                                </select>
                                <span class="text-gray-400 font-bold">-</span>
                                <input 
                                    type="text" 
                                    x-model="rifInput.numero" 
                                    @input="formatRif()"
                                    x-ref="rifNumero"
                                    maxlength="8" 
                                    required 
                                    placeholder="12345678" 
                                    class="w-full px-3 py-2.5 rounded-xl border border-gray-200 text-xs font-mono font-bold tracking-wider text-gray-900 focus:border-[#0d5238] focus:ring-1 focus:ring-[#0d5238] text-center"
                                >
                                <span class="text-gray-400 font-bold">-</span>
                                <input 
                                    type="text" 
                                    x-model="rifInput.digito" 
                                    @input="formatRif()"
                                    x-ref="rifDigito"
                                    maxlength="1" 
                                    required 
                                    placeholder="0" 
                                    class="w-12 px-2 py-2.5 rounded-xl border border-gray-200 text-xs font-mono font-bold tracking-wider text-gray-900 focus:border-[#0d5238] focus:ring-1 focus:ring-[#0d5238] text-center"
                                >
                            </div>
                            <span class="text-[10px] text-gray-400 mt-1 block">Formato: J-12345678-0</span>
                        </div>

                        <!-- Patente Municipal SIGAT Adaptativa -->
                        <div>
                            <div class="flex items-center justify-between mb-1.5">
                                <label class="block text-xs font-bold text-gray-700 uppercase">N° Cuenta / Licencia Patente *</label>
                                <button 
                                    type="button" 
                                    @click="patenteInput.tipo_formato = (patenteInput.tipo_formato === 'SIGAT_OFICIAL' ? 'CODIGO_CONTRIBUYENTE' : 'SIGAT_OFICIAL'); formatPatente();"
                                    class="text-[10px] text-[#0d5238] hover:underline font-semibold cursor-pointer"
                                >
                                    <span x-text="patenteInput.tipo_formato === 'SIGAT_OFICIAL' ? 'Usar Cód. Contribuyente' : 'Usar Formato CZ-AE'"></span>
                                </button>
                            </div>

                            <!-- Formato Estándar CZ-AE -->
                            <div x-show="patenteInput.tipo_formato === 'SIGAT_OFICIAL'" class="flex items-center gap-1.5">
                                <span class="px-3 py-2.5 rounded-xl border border-gray-200 text-xs font-mono font-bold text-gray-600 bg-gray-100 shrink-0">
                                    CZ-AE-
                                </span>
                                <input 
                                    type="text" 
                                    x-model="patenteInput.correlativo" 
                                    @input="formatPatente()"
                                    maxlength="6" 
                                    :required="patenteInput.tipo_formato === 'SIGAT_OFICIAL'"
                                    placeholder="004521" 
                                    class="w-full px-3 py-2.5 rounded-xl border border-gray-200 text-xs font-mono font-bold tracking-wider text-gray-900 focus:border-[#0d5238] focus:ring-1 focus:ring-[#0d5238]"
                                >
                            </div>

                            <!-- Formato Código Contribuyente (N° Anual SIGAT) -->
                            <div x-show="patenteInput.tipo_formato === 'CODIGO_CONTRIBUYENTE'">
                                <input 
                                    type="text" 
                                    x-model="patenteInput.correlativo" 
                                    @input="formatPatente()"
                                    maxlength="10" 
                                    :required="patenteInput.tipo_formato === 'CODIGO_CONTRIBUYENTE'"
                                    placeholder="Ej: 20240182" 
                                    class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 text-xs font-mono font-bold tracking-wider text-gray-900 focus:border-[#0d5238] focus:ring-1 focus:ring-[#0d5238]"
                                >
                            </div>
                            <span class="text-[10px] text-gray-400 mt-1 block" x-text="patenteInput.tipo_formato === 'SIGAT_OFICIAL' ? 'Estructura oficial SIGAT Carrizal (CZ-AE-Nº)' : 'Código interno del contribuyente en SIGAT (ej: 2022XXXX)'"></span>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase mb-1.5">Razón Social *</label>
                            <input type="text" x-model="form.razon_social" required placeholder="Inversiones Carrizal C.A." class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 text-xs text-gray-900 focus:border-[#0d5238] focus:ring-1 focus:ring-[#0d5238]">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase mb-1.5">Nombre Comercial</label>
                            <input type="text" x-model="form.nombre_comercial" placeholder="Bodegón El Pan de Azúcar" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 text-xs text-gray-900 focus:border-[#0d5238] focus:ring-1 focus:ring-[#0d5238]">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase mb-1.5">Correo Electrónico Comercial *</label>
                            <input type="email" x-model="form.correo_comercial" required placeholder="contacto@empresa.com" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 text-xs text-gray-900 focus:border-[#0d5238] focus:ring-1 focus:ring-[#0d5238]">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase mb-1.5">Teléfono Comercial *</label>
                            <div class="flex items-center gap-2">
                                <select 
                                    x-model="form.codigo_area" 
                                    class="px-2.5 py-2.5 rounded-xl border border-gray-200 text-xs font-mono font-bold text-gray-800 bg-gray-50 focus:border-[#0d5238] focus:ring-1 focus:ring-[#0d5238]"
                                >
                                    <option value="0412">0412</option>
                                    <option value="0414">0414</option>
                                    <option value="0424">0424</option>
                                    <option value="0416">0416</option>
                                    <option value="0426">0426</option>
                                    <option value="0212">0212</option>
                                </select>
                                <input 
                                    type="text" 
                                    x-model="form.telefono_num" 
                                    x-on:input="form.telefono_num = $event.target.value.replace(/\D/g, '')"
                                    maxlength="7"
                                    required 
                                    placeholder="1234567" 
                                    class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 text-xs font-mono font-bold tracking-wider text-gray-900 focus:border-[#0d5238] focus:ring-1 focus:ring-[#0d5238]"
                                >
                            </div>
                        </div>
                    </div>

                    <!-- Estructura Territorial vinculada a TerritorioService -->
                    <div class="pt-2 space-y-4">
                        <h5 class="text-xs font-bold text-[#0d5238] uppercase tracking-wide">Ubicación Geográfica y Sectorial (Carrizal)</h5>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[11px] font-bold text-gray-700 uppercase mb-1.5">Circuito / Comuna *</label>
                                <select 
                                    x-model="form.comuna" 
                                    @change="onComunaChange()" 
                                    required 
                                    class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 text-xs text-gray-900 focus:border-[#0d5238] focus:ring-1 focus:ring-[#0d5238]"
                                >
                                    <option value="">Seleccione Circuito o Comuna...</option>
                                    <template x-for="(sectores, key) in territoriosData" :key="key">
                                        <option :value="key" x-text="key"></option>
                                    </template>
                                </select>
                            </div>

                            <div>
                                <label class="block text-[11px] font-bold text-gray-700 uppercase mb-1.5">Sector *</label>
                                <select 
                                    x-model="form.sector" 
                                    :disabled="!form.comuna" 
                                    required 
                                    class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 text-xs text-gray-900 focus:border-[#0d5238] focus:ring-1 focus:ring-[#0d5238] disabled:bg-gray-100 disabled:cursor-not-allowed"
                                >
                                    <option value="">Seleccione Sector...</option>
                                    <template x-for="sec in sectoresDisponibles" :key="sec">
                                        <option :value="sec" x-text="sec"></option>
                                    </template>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[11px] font-bold text-gray-700 uppercase mb-1.5">Calle / Avenida *</label>
                                <input type="text" x-model="form.calle_avenida" required placeholder="Ej: Av. Sucre, Callejón El Carmen" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 text-xs text-gray-900">
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-gray-700 uppercase mb-1.5">Edificio / Galpón / Local *</label>
                                <input type="text" x-model="form.edificio_galpon_local" required placeholder="Ej: C.C. La Cascada, Nivel 1, Local 45" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 text-xs text-gray-900">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- PASO 2: Clasificación de Impacto RACDA / MINEC -->
                <div x-show="step === 2" x-transition.opacity class="space-y-5">
                    <div class="border-b border-gray-100 pb-3">
                        <h4 class="text-xs font-extrabold text-gray-800 uppercase tracking-wider">2. Diagnóstico de Impacto Ambiental (RACDA / MINEC)</h4>
                        <p class="text-[11px] text-gray-500 mt-0.5">Determine la relación de su actividad económica con normativas ambientales.</p>
                    </div>

                    <!-- Selección Principal de Rol Ambiental -->
                    <div>
                        <label class="block text-xs font-bold text-gray-800 uppercase mb-2">Rol Operativo Ambiental *</label>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <label 
                                class="p-3.5 rounded-2xl border cursor-pointer transition-all flex flex-col justify-between"
                                :class="form.perfil.rol_ambiental === 'GENERADOR' ? 'border-[#0d5238] bg-emerald-50/50 ring-1 ring-[#0d5238]' : 'border-gray-200 bg-gray-50/50 hover:border-gray-300'"
                            >
                                <div class="flex items-center gap-2">
                                    <input type="radio" name="rol_ambiental" value="GENERADOR" x-model="form.perfil.rol_ambiental" class="text-[#0d5238] focus:ring-[#0d5238]">
                                    <span class="text-xs font-bold text-gray-900">GENERADOR</span>
                                </div>
                                <span class="text-[10px] text-gray-500 mt-2 block">Produce desechos, efluentes o emisiones por su actividad productiva o comercial.</span>
                            </label>

                            <label 
                                class="p-3.5 rounded-2xl border cursor-pointer transition-all flex flex-col justify-between"
                                :class="form.perfil.rol_ambiental === 'MANEJADOR' ? 'border-[#0d5238] bg-emerald-50/50 ring-1 ring-[#0d5238]' : 'border-gray-200 bg-gray-50/50 hover:border-gray-300'"
                            >
                                <div class="flex items-center gap-2">
                                    <input type="radio" name="rol_ambiental" value="MANEJADOR" x-model="form.perfil.rol_ambiental" class="text-[#0d5238] focus:ring-[#0d5238]">
                                    <span class="text-xs font-bold text-gray-900">MANEJADOR</span>
                                </div>
                                <span class="text-[10px] text-gray-500 mt-2 block">Recolecta, transporta, almacena, trata o dispone de sustancias/desechos.</span>
                            </label>

                            <label 
                                class="p-3.5 rounded-2xl border cursor-pointer transition-all flex flex-col justify-between"
                                :class="form.perfil.rol_ambiental === 'NO_APLICA' ? 'border-[#0d5238] bg-emerald-50/50 ring-1 ring-[#0d5238]' : 'border-gray-200 bg-gray-50/50 hover:border-gray-300'"
                            >
                                <div class="flex items-center gap-2">
                                    <input type="radio" name="rol_ambiental" value="NO_APLICA" x-model="form.perfil.rol_ambiental" class="text-[#0d5238] focus:ring-[#0d5238]">
                                    <span class="text-xs font-bold text-gray-900">NO APLICA</span>
                                </div>
                                <span class="text-[10px] text-gray-500 mt-2 block">Actividad administrativa / comercial de bajo impacto sin residuos peligrosos.</span>
                            </label>
                        </div>
                    </div>

                    <!-- Módulo Dinámico: ROL GENERADOR -->
                    <div x-show="form.perfil.rol_ambiental === 'GENERADOR'" x-transition class="space-y-4 p-4 bg-gray-50/80 rounded-2xl border border-gray-100">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[11px] font-bold text-gray-700 uppercase mb-1">Clasificación de Generador</label>
                                <select x-model="form.perfil.subtipo_generador" class="w-full px-3 py-2 rounded-xl border border-gray-200 text-xs bg-white text-gray-900">
                                    <option value="PEQUENO">Pequeño Generador</option>
                                    <option value="MEDIANO">Mediano Generador</option>
                                    <option value="GRAN">Gran Generador / Industrial</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-gray-700 uppercase mb-1">N° Registro RACDA (Si posee)</label>
                                <input type="text" x-model="form.perfil.numero_racda" placeholder="RACDA-G-12345" class="w-full px-3 py-2 rounded-xl border border-gray-200 text-xs font-mono font-bold uppercase bg-white">
                            </div>
                        </div>

                        <!-- Evaluación de Módulos Específicos -->
                        <div class="pt-2">
                            <span class="block text-[11px] font-bold text-gray-700 uppercase mb-2">Módulos de Impacto Operativo</span>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5 text-xs">
                                <label class="flex items-center gap-2 p-2.5 bg-white rounded-xl border border-gray-200 cursor-pointer">
                                    <input type="checkbox" x-model="form.perfil.genera_efluentes" class="rounded text-[#0d5238]">
                                    <span class="font-medium text-gray-700">Genera Efluentes Líquidos</span>
                                </label>
                                <label class="flex items-center gap-2 p-2.5 bg-white rounded-xl border border-gray-200 cursor-pointer">
                                    <input type="checkbox" x-model="form.perfil.genera_emisiones" class="rounded text-[#0d5238]">
                                    <span class="font-medium text-gray-700">Emisiones / Chimeneas / Calderas</span>
                                </label>
                                <label class="flex items-center gap-2 p-2.5 bg-white rounded-xl border border-gray-200 cursor-pointer">
                                    <input type="checkbox" x-model="form.perfil.genera_peligrosos" class="rounded text-[#0d5238]">
                                    <span class="font-medium text-gray-700">Desechos Peligrosos / Químicos</span>
                                </label>
                            </div>
                        </div>

                        <div x-show="form.perfil.genera_peligrosos" x-transition class="pt-2">
                            <label class="block text-[11px] font-bold text-amber-800 uppercase mb-1">Empresa Manejadora Contratada (RIF / RACDA)</label>
                            <input type="text" x-model="form.perfil.manejadora_contratada" placeholder="Ej: J-99999999-0 / RACDA-M-54321" class="w-full px-3 py-2 rounded-xl border border-amber-300 text-xs bg-amber-50/30">
                        </div>
                    </div>

                    <!-- Módulo Dinámico: ROL MANEJADOR -->
                    <div x-show="form.perfil.rol_ambiental === 'MANEJADOR'" x-transition class="space-y-4 p-4 bg-gray-50/80 rounded-2xl border border-gray-100">
                        <div>
                            <label class="block text-[11px] font-bold text-gray-700 uppercase mb-2">Actividades de Manejo Autorizadas</label>
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 text-xs">
                                <label class="flex items-center gap-2 p-2 bg-white rounded-lg border border-gray-200 cursor-pointer">
                                    <input type="checkbox" value="recoleccion_transporte" x-model="form.perfil.actividades_manejador" class="rounded text-[#0d5238]">
                                    <span class="text-gray-700">Transporte</span>
                                </label>
                                <label class="flex items-center gap-2 p-2 bg-white rounded-lg border border-gray-200 cursor-pointer">
                                    <input type="checkbox" value="almacenamiento" x-model="form.perfil.actividades_manejador" class="rounded text-[#0d5238]">
                                    <span class="text-gray-700">Almacenamiento</span>
                                </label>
                                <label class="flex items-center gap-2 p-2 bg-white rounded-lg border border-gray-200 cursor-pointer">
                                    <input type="checkbox" value="recuperacion_tratamiento" x-model="form.perfil.actividades_manejador" class="rounded text-[#0d5238]">
                                    <span class="text-gray-700">Tratamiento</span>
                                </label>
                                <label class="flex items-center gap-2 p-2 bg-white rounded-lg border border-gray-200 cursor-pointer">
                                    <input type="checkbox" value="disposicion_final" x-model="form.perfil.actividades_manejador" class="rounded text-[#0d5238]">
                                    <span class="text-gray-700">Disposición Final</span>
                                </label>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[11px] font-bold text-gray-700 uppercase mb-1">N° Registro RACDA Manejador *</label>
                                <input type="text" x-model="form.perfil.numero_racda" placeholder="RACDA-M-98765" class="w-full px-3 py-2 rounded-xl border border-gray-200 text-xs font-mono font-bold uppercase bg-white">
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-gray-700 uppercase mb-1">Sustancias / Materiales Manejados</label>
                                <input type="text" x-model="form.perfil.sustancias_manejadas" placeholder="Ej: Aceites usados, chatarra, baterías" class="w-full px-3 py-2 rounded-xl border border-gray-200 text-xs bg-white">
                            </div>
                        </div>
                    </div>

                    <!-- Tipos de Residuos Físicos Generados -->
                    <div>
                        <label class="block text-xs font-bold text-gray-800 uppercase mb-2">Tipos de Residuos Generados en Planta / Comercio</label>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-2.5 text-xs">
                            <label class="flex items-center gap-2.5 p-3 bg-gray-50 rounded-xl border border-gray-100 cursor-pointer hover:bg-emerald-50/40">
                                <input type="checkbox" value="organicos" x-model="form.perfil.residuos_tipos" class="rounded text-[#0d5238] focus:ring-[#0d5238]">
                                <span class="font-medium text-gray-700">Orgánicos / Biodegradables</span>
                            </label>
                            <label class="flex items-center gap-2.5 p-3 bg-gray-50 rounded-xl border border-gray-100 cursor-pointer hover:bg-emerald-50/40">
                                <input type="checkbox" value="plasticos_carton" x-model="form.perfil.residuos_tipos" class="rounded text-[#0d5238] focus:ring-[#0d5238]">
                                <span class="font-medium text-gray-700">Plásticos / Cartón / Papel</span>
                            </label>
                            <label class="flex items-center gap-2.5 p-3 bg-gray-50 rounded-xl border border-gray-100 cursor-pointer hover:bg-emerald-50/40">
                                <input type="checkbox" value="peligrosos_aceites" x-model="form.perfil.residuos_tipos" class="rounded text-[#0d5238] focus:ring-[#0d5238]">
                                <span class="font-medium text-gray-700">Aceites / Químicos</span>
                            </label>
                            <label class="flex items-center gap-2.5 p-3 bg-gray-50 rounded-xl border border-gray-100 cursor-pointer hover:bg-emerald-50/40">
                                <input type="checkbox" value="metalicos_chatarra" x-model="form.perfil.residuos_tipos" class="rounded text-[#0d5238] focus:ring-[#0d5238]">
                                <span class="font-medium text-gray-700">Metálicos / Chatarra</span>
                            </label>
                            <label class="flex items-center gap-2.5 p-3 bg-gray-50 rounded-xl border border-gray-100 cursor-pointer hover:bg-emerald-50/40">
                                <input type="checkbox" value="hospitalarios_biologicos" x-model="form.perfil.residuos_tipos" class="rounded text-[#0d5238] focus:ring-[#0d5238]">
                                <span class="font-medium text-gray-700">Biológicos / Médicos</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- PASO 3: Consignación Dinámica de Recaudos (Mejora Estética de Carga) -->
                <div x-show="step === 3" x-transition.opacity class="space-y-5">
                    <div class="border-b border-gray-100 pb-3">
                        <h4 class="text-xs font-extrabold text-gray-800 uppercase tracking-wider">3. Documentación y Requisitos Ambientales</h4>
                        <p class="text-[11px] text-gray-500 mt-0.5">Adjunte en digital (PDF/Imagen) los recaudos obligatorios según su perfil.</p>
                    </div>

                    <div class="grid grid-cols-1 gap-3.5 max-h-[380px] overflow-y-auto pr-1">
                        
                        <!-- RENMA -->
                        <div class="p-3.5 bg-gray-50/80 rounded-2xl border border-gray-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div class="space-y-0.5">
                                <span class="text-xs font-bold text-gray-800 block">1. Registro RENMA</span>
                                <span class="text-[10px] text-gray-500 block">Inscripción o actualización ecosocialista (MINEC)</span>
                            </div>
                            <label class="relative flex items-center justify-center px-4 py-2 bg-white border-2 border-dashed border-gray-300 rounded-xl cursor-pointer hover:border-[#0d5238] hover:bg-emerald-50/30 transition-all shrink-0">
                                <span x-text="files.doc_renma ? files.doc_renma.name : 'Seleccionar Archivo'" class="text-xs font-semibold text-gray-600 truncate max-w-[180px]"></span>
                                <input type="file" @change="handleFileUpload($event, 'doc_renma')" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                            </label>
                        </div>

                        <!-- Constancia de No Objeción (Si ROL === NO_APLICA) -->
                        <div x-show="form.perfil.rol_ambiental === 'NO_APLICA'" x-transition class="p-3.5 bg-amber-50/60 rounded-2xl border border-amber-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div class="space-y-0.5">
                                <span class="text-xs font-bold text-amber-900 block">2. Constancia de No Objeción Ambiental</span>
                                <span class="text-[10px] text-amber-700 block">Emitida por la Dirección Estadal del MINEC</span>
                            </div>
                            <label class="relative flex items-center justify-center px-4 py-2 bg-white border-2 border-dashed border-amber-300 rounded-xl cursor-pointer hover:border-amber-600 transition-all shrink-0">
                                <span x-text="files.doc_no_objecion ? files.doc_no_objecion.name : 'Seleccionar Archivo'" class="text-xs font-semibold text-amber-800 truncate max-w-[180px]"></span>
                                <input type="file" @change="handleFileUpload($event, 'doc_no_objecion')" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                            </label>
                        </div>

                        <!-- RACDA (Si ROL GENERADOR o MANEJADOR) -->
                        <div x-show="form.perfil.rol_ambiental !== 'NO_APLICA'" x-transition class="p-3.5 bg-gray-50/80 rounded-2xl border border-gray-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div class="space-y-0.5">
                                <span class="text-xs font-bold text-gray-800 block">3. Registro RACDA</span>
                                <span class="text-[10px] text-gray-500 block">Constancia oficial de registro (Minea/MINEC)</span>
                            </div>
                            <label class="relative flex items-center justify-center px-4 py-2 bg-white border-2 border-dashed border-gray-300 rounded-xl cursor-pointer hover:border-[#0d5238] hover:bg-emerald-50/30 transition-all shrink-0">
                                <span x-text="files.doc_racda ? files.doc_racda.name : 'Seleccionar Archivo'" class="text-xs font-semibold text-gray-600 truncate max-w-[180px]"></span>
                                <input type="file" @change="handleFileUpload($event, 'doc_racda')" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                            </label>
                        </div>

                        <!-- Póliza Ambiental (Obligatoria para MANEJADOR) -->
                        <div x-show="form.perfil.rol_ambiental === 'MANEJADOR'" x-transition class="p-3.5 bg-gray-50/80 rounded-2xl border border-gray-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div class="space-y-0.5">
                                <span class="text-xs font-bold text-gray-800 block">4. Póliza de Seguro de Responsabilidad Ambiental</span>
                                <span class="text-[10px] text-gray-500 block">Aprobada por Sudeaseg y vigente</span>
                            </div>
                            <label class="relative flex items-center justify-center px-4 py-2 bg-white border-2 border-dashed border-gray-300 rounded-xl cursor-pointer hover:border-[#0d5238] hover:bg-emerald-50/30 transition-all shrink-0">
                                <span x-text="files.doc_poliza ? files.doc_poliza.name : 'Seleccionar Archivo'" class="text-xs font-semibold text-gray-600 truncate max-w-[180px]"></span>
                                <input type="file" @change="handleFileUpload($event, 'doc_poliza')" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                            </label>
                        </div>

                        <!-- Caracterización de Efluentes -->
                        <div x-show="form.perfil.genera_efluentes" x-transition class="p-3.5 bg-gray-50/80 rounded-2xl border border-gray-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div class="space-y-0.5">
                                <span class="text-xs font-bold text-gray-800 block">5. Caracterización de Efluentes Líquidos</span>
                                <span class="text-[10px] text-gray-500 block">Análisis por laboratorio acreditado</span>
                            </div>
                            <label class="relative flex items-center justify-center px-4 py-2 bg-white border-2 border-dashed border-gray-300 rounded-xl cursor-pointer hover:border-[#0d5238] hover:bg-emerald-50/30 transition-all shrink-0">
                                <span x-text="files.doc_efluentes ? files.doc_efluentes.name : 'Seleccionar Archivo'" class="text-xs font-semibold text-gray-600 truncate max-w-[180px]"></span>
                                <input type="file" @change="handleFileUpload($event, 'doc_efluentes')" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                            </label>
                        </div>

                        <!-- Emisiones Atmosféricas -->
                        <div x-show="form.perfil.genera_emisiones" x-transition class="p-3.5 bg-gray-50/80 rounded-2xl border border-gray-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div class="space-y-0.5">
                                <span class="text-xs font-bold text-gray-800 block">6. Caracterización de Emisiones Atmosféricas</span>
                                <span class="text-[10px] text-gray-500 block">Medición de chimeneas / fuentes fijas</span>
                            </div>
                            <label class="relative flex items-center justify-center px-4 py-2 bg-white border-2 border-dashed border-gray-300 rounded-xl cursor-pointer hover:border-[#0d5238] hover:bg-emerald-50/30 transition-all shrink-0">
                                <span x-text="files.doc_emisiones ? files.doc_emisiones.name : 'Seleccionar Archivo'" class="text-xs font-semibold text-gray-600 truncate max-w-[180px]"></span>
                                <input type="file" @change="handleFileUpload($event, 'doc_emisiones')" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                            </label>
                        </div>

                        <!-- Plan de Manejo de Desechos Sólidos -->
                        <div class="p-3.5 bg-gray-50/80 rounded-2xl border border-gray-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div class="space-y-0.5">
                                <span class="text-xs font-bold text-gray-800 block">7. Plan de Manejo de Desechos y Residuos Sólidos</span>
                                <span class="text-[10px] text-gray-500 block">Rutas internas, segregación y disposición final</span>
                            </div>
                            <label class="relative flex items-center justify-center px-4 py-2 bg-white border-2 border-dashed border-gray-300 rounded-xl cursor-pointer hover:border-[#0d5238] hover:bg-emerald-50/30 transition-all shrink-0">
                                <span x-text="files.doc_plan_desechos ? files.doc_plan_desechos.name : 'Seleccionar Archivo'" class="text-xs font-semibold text-gray-600 truncate max-w-[180px]"></span>
                                <input type="file" @change="handleFileUpload($event, 'doc_plan_desechos')" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                            </label>
                        </div>

                        <!-- Plan de Emergencia y Contingencia -->
                        <div class="p-3.5 bg-gray-50/80 rounded-2xl border border-gray-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div class="space-y-0.5">
                                <span class="text-xs font-bold text-gray-800 block">8. Plan de Emergencia y Contingencia Ambiental</span>
                                <span class="text-[10px] text-gray-500 block">Protocolos de actuación ante derrames/incendios</span>
                            </div>
                            <label class="relative flex items-center justify-center px-4 py-2 bg-white border-2 border-dashed border-gray-300 rounded-xl cursor-pointer hover:border-[#0d5238] hover:bg-emerald-50/30 transition-all shrink-0">
                                <span x-text="files.doc_plan_emergencia ? files.doc_plan_emergencia.name : 'Seleccionar Archivo'" class="text-xs font-semibold text-gray-600 truncate max-w-[180px]"></span>
                                <input type="file" @change="handleFileUpload($event, 'doc_plan_emergencia')" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                            </label>
                        </div>

                    </div>
                </div>

                <!-- Footer / Navegación -->
                <div class="pt-6 border-t border-gray-100 flex items-center justify-between">
                    <div>
                        <button 
                            type="button" 
                            x-show="step > 1" 
                            @click="step--" 
                            class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-bold rounded-xl transition-colors cursor-pointer"
                        >
                            Anterior
                        </button>
                    </div>

                    <div class="flex items-center gap-3">
                        <button 
                            type="button" 
                            @click="modalOpen = false"
                            class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-bold rounded-xl transition-colors cursor-pointer"
                        >
                            Cancelar
                        </button>

                        <button 
                            type="button" 
                            x-show="step < 3" 
                            @click="step++" 
                            class="px-6 py-2.5 bg-[#0d5238] hover:bg-[#073825] text-white text-xs font-bold rounded-xl transition-all shadow-md cursor-pointer"
                        >
                            Siguiente
                        </button>

                        <button 
                            type="submit" 
                            x-show="step === 3" 
                            :disabled="submitting"
                            class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl transition-all shadow-md cursor-pointer flex items-center gap-2 disabled:opacity-50"
                        >
                            <span x-text="submitting ? 'Procesando...' : 'Finalizar Registro'"></span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function vincularEmpresaModal(territorios) {
    return {
        step: 1,
        submitting: false,
        territoriosData: territorios || {},
        sectoresDisponibles: [],
        files: {},
        
        // Estructura desglosada para RIF y Patente SIGAT
        rifInput: {
            tipo: 'J',
            numero: '',
            digito: ''
        },
        patenteInput: {
            tipo_formato: 'SIGAT_OFICIAL', // 'SIGAT_OFICIAL' (CZ-AE-000000) o 'CODIGO_CONTRIBUYENTE' (202XXXXX)
            prefix: 'CZ-AE-',
            correlativo: ''
        },

        form: {
            rif: '',
            numero_cuenta_patente: '',
            razon_social: '',
            nombre_comercial: '',
            correo_comercial: '',
            codigo_area: '0412',
            telefono_num: '',
            comuna: '',
            sector: '',
            calle_avenida: '',
            edificio_galpon_local: '',
            perfil: {
                rol_ambiental: 'GENERADOR',
                subtipo_generador: 'PEQUENO',
                numero_racda: '',
                genera_efluentes: false,
                genera_emisiones: false,
                genera_peligrosos: false,
                manejadora_contratada: '',
                actividades_manejador: [],
                sustancias_manejadas: '',
                residuos_tipos: []
            }
        },

        // Auto-tabulación y formateo de RIF
        formatRif() {
            this.rifInput.numero = this.rifInput.numero.replace(/\D/g, '');
            this.rifInput.digito = this.rifInput.digito.replace(/\D/g, '');

            if (this.rifInput.numero.length === 8 && $refs.rifDigito) {
                $refs.rifDigito.focus();
            }

            this.form.rif = `${this.rifInput.tipo}-${this.rifInput.numero}-${this.rifInput.digito}`;
        },

        // Auto-formateo de Patente Municipal (SIGAT)
        formatPatente() {
            this.patenteInput.correlativo = this.patenteInput.correlativo.replace(/\D/g, '');

            if (this.patenteInput.tipo_formato === 'SIGAT_OFICIAL') {
                this.form.numero_cuenta_patente = `${this.patenteInput.prefix}${this.patenteInput.correlativo}`;
            } else {
                this.form.numero_cuenta_patente = this.patenteInput.correlativo;
            }
        },

        onComunaChange() {
            if (this.form.comuna && this.territoriosData[this.form.comuna]) {
                this.sectoresDisponibles = this.territoriosData[this.form.comuna];
            } else {
                this.sectoresDisponibles = [];
            }
            this.form.sector = '';
        },

        handleFileUpload(event, key) {
            if (event.target.files.length > 0) {
                this.files[key] = event.target.files[0];
            }
        },

        async submitForm() {
            this.submitting = true;
            
            // Unificación final de datos estructurados
            this.form.rif = `${this.rifInput.tipo}-${this.rifInput.numero}-${this.rifInput.digito}`;
            
            if (this.patenteInput.tipo_formato === 'SIGAT_OFICIAL') {
                this.form.numero_cuenta_patente = `CZ-AE-${this.patenteInput.correlativo.padStart(6, '0')}`;
            } else {
                this.form.numero_cuenta_patente = this.patenteInput.correlativo;
            }

            const formData = new FormData();
            formData.append('rif', this.form.rif);
            formData.append('numero_cuenta_patente', this.form.numero_cuenta_patente);
            formData.append('razon_social', this.form.razon_social);
            formData.append('nombre_comercial', this.form.nombre_comercial);
            formData.append('correo_comercial', this.form.correo_comercial);
            formData.append('telefono_comercial', `${this.form.codigo_area}${this.form.telefono_num}`);
            formData.append('comuna', this.form.comuna);
            formData.append('sector', this.form.sector);
            formData.append('calle_avenida', this.form.calle_avenida);
            formData.append('edificio_galpon_local', this.form.edificio_galpon_local);
            formData.append('perfil', JSON.stringify(this.form.perfil));

            Object.keys(this.files).forEach(key => {
                if (this.files[key]) {
                    formData.append(`documentos[${key}]`, this.files[key]);
                }
            });

            try {
                const response = await fetch("{{ route('ciudadano.empresas.store') }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        // Eliminamos 'Accept: application/json' para que Laravel responda con HTML clásico en caso de error 500
                    },
                    body: formData
                });

                if (response.ok) {
                    window.location.reload();
                } else {
                    // Reemplaza el contenido de la ventana actual con la pantalla de error de Laravel
                    const errorHtml = await response.text();
                    document.open();
                    document.write(errorHtml);
                    document.close();
                }
            } catch (error) {
                console.error('Error de red:', error);
            } finally {
                this.submitting = false;
            }
        }
    }
}
</script>