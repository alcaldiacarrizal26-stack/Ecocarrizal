<x-layouts.app title="Vincular / Actualizar Empresa" active="empresas">
<div class="max-w-5xl mx-auto py-8 px-4 sm:px-6 space-y-6"
     x-data="vincularEmpresaForm({{ json_encode(\App\Services\TerritorioService::getTerritorios()) }}, {{ isset($empresa) ? json_encode($empresa) : 'null' }})">

    <!-- ALERTA DE OBSERVACIONES EN CASO DE RECHAZO U OBSERVACIÓN -->
    @if(isset($empresa) && in_array($empresa->estatus, ['OBSERVADO', 'RECHAZADO']))
        <div class="p-5 bg-rose-50 border border-rose-200 rounded-3xl shadow-sm space-y-2">
            <div class="flex items-center gap-2 text-rose-800 font-bold text-sm">
                <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <span>Su expediente comercial presenta observaciones y requiere corrección</span>
            </div>
            <p class="text-xs text-rose-700 leading-relaxed pl-7">
                <strong>Motivo / Observaciones de la Dirección:</strong> 
                {{ $empresa->observaciones_fiscalia ?? 'Por favor verifique los recaudos y datos suministrados según la normativa ambiental vigente.' }}
            </p>
        </div>
    @endif

    <!-- Encabezado de la Página -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">
                {{ isset($empresa) ? 'Corregir / Actualizar Expediente de Empresa' : 'Vincular Nueva Empresa' }}
            </h1>
            <p class="text-xs text-gray-500 mt-1">Registre su firma comercial y complete el expediente de perfil ambiental.</p>
        </div>
        <a 
            href="{{ route('ciudadano.empresas') }}" 
            class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-gray-200 bg-white text-xs font-bold text-gray-700 shadow-sm hover:bg-gray-50 transition-colors self-start sm:self-auto"
        >
            &larr; Volver a Mis Empresas
        </a>
    </div>

    <!-- Tarjeta Principal / Formulario Completo -->
    <div class="overflow-hidden rounded-3xl bg-white shadow-xl border border-gray-100">
        <!-- Header con Identidad Municipal y Stepper -->
        <div class="bg-[#0d5238] px-8 py-7 text-white flex flex-col sm:flex-row sm:items-center justify-between gap-6 border-b border-emerald-800/40">
            <div class="flex items-center gap-4">
                <div class="h-12 w-12 rounded-2xl bg-white/10 border border-white/20 flex items-center justify-center shrink-0 shadow-inner">
                    <img src="{{ asset('images/carrizal_logo.png') }}" alt="Carrizal Logo" class="h-8 w-8 object-contain">
                </div>
                <div>
                    <h3 class="text-lg font-bold tracking-wide text-white leading-tight">
                        Expediente Comercial y Perfil Ambiental
                    </h3>
                    <p class="text-xs text-emerald-200/90 font-medium mt-1 tracking-wide">
                        Alcaldía del Municipio Carrizal • Dirección de Ecología y Residuos
                    </p>
                </div>
            </div>

            <!-- Indicator Stepper -->
            <div class="flex items-center gap-2 bg-black/25 backdrop-blur-md px-4 py-2 rounded-full text-xs font-semibold text-emerald-100 border border-white/10 self-start sm:self-auto shrink-0">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                <span>Paso <span x-text="step" class="text-white font-bold"></span> de 3</span>
            </div>
        </div>

        <form @submit.prevent="submitForm" @keydown.enter.prevent="handleKeydownEnter($event)" enctype="multipart/form-data" class="p-8 space-y-6">
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
                        <input type="text" x-model="form.razon_social" placeholder="Inversiones Carrizal C.A." class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 text-xs text-gray-900 focus:border-[#0d5238] focus:ring-1 focus:ring-[#0d5238]">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1.5">Nombre Comercial</label>
                        <input type="text" x-model="form.nombre_comercial" placeholder="Bodegón El Pan de Azúcar" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 text-xs text-gray-900 focus:border-[#0d5238] focus:ring-1 focus:ring-[#0d5238]">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1.5">Correo Electrónico Comercial *</label>
                        <input type="email" x-model="form.correo_comercial" placeholder="contacto@empresa.com" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 text-xs text-gray-900 focus:border-[#0d5238] focus:ring-1 focus:ring-[#0d5238]">
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
                            <input type="text" x-model="form.calle_avenida" placeholder="Ej: Av. Sucre, Callejón El Carmen" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 text-xs text-gray-900 focus:border-[#0d5238] focus:ring-1 focus:ring-[#0d5238]">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-gray-700 uppercase mb-1.5">Edificio / Galpón / Local *</label>
                            <input type="text" x-model="form.edificio_galpon_local" placeholder="Ej: C.C. La Cascada, Nivel 1, Local 45" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 text-xs text-gray-900 focus:border-[#0d5238] focus:ring-1 focus:ring-[#0d5238]">
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
                                <input type="radio" name="rol_ambiental" value="GENERADOR" x-model="form.perfil.rol_ambiental" @change="onRolChange('GENERADOR')" class="text-[#0d5238] focus:ring-[#0d5238]">
                                <span class="text-xs font-bold text-gray-900">GENERADOR</span>
                            </div>
                            <span class="text-[10px] text-gray-500 mt-2 block">Produce desechos, efluentes o emisiones por su actividad productiva o comercial.</span>
                        </label>

                        <label 
                            class="p-3.5 rounded-2xl border cursor-pointer transition-all flex flex-col justify-between"
                            :class="form.perfil.rol_ambiental === 'MANEJADOR' ? 'border-[#0d5238] bg-emerald-50/50 ring-1 ring-[#0d5238]' : 'border-gray-200 bg-gray-50/50 hover:border-gray-300'"
                        >
                            <div class="flex items-center gap-2">
                                <input type="radio" name="rol_ambiental" value="MANEJADOR" x-model="form.perfil.rol_ambiental" @change="onRolChange('MANEJADOR')" class="text-[#0d5238] focus:ring-[#0d5238]">
                                <span class="text-xs font-bold text-gray-900">MANEJADOR</span>
                            </div>
                            <span class="text-[10px] text-gray-500 mt-2 block">Recolecta, transporta, almacena, trata o dispone de sustancias/desechos.</span>
                        </label>

                        <label 
                            class="p-3.5 rounded-2xl border cursor-pointer transition-all flex flex-col justify-between"
                            :class="form.perfil.rol_ambiental === 'NO_APLICA' ? 'border-[#0d5238] bg-emerald-50/50 ring-1 ring-[#0d5238]' : 'border-gray-200 bg-gray-50/50 hover:border-gray-300'"
                        >
                            <div class="flex items-center gap-2">
                                <input type="radio" name="rol_ambiental" value="NO_APLICA" x-model="form.perfil.rol_ambiental" @change="onRolChange('NO_APLICA')" class="text-[#0d5238] focus:ring-[#0d5238]">
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

                        <!-- RACDA Fijo en G para Generador -->
                        <div>
                            <label class="block text-[11px] font-bold text-gray-700 uppercase mb-1">N° Registro RACDA *</label>
                            <div class="flex items-center gap-1.5">
                                <span class="px-2.5 py-2 rounded-xl border border-gray-200 text-xs font-mono font-bold text-gray-700 bg-gray-100 select-none">
                                    RACDA-G-
                                </span>
                                <input 
                                    type="text" 
                                    x-model="racdaInput.correlativo" 
                                    @input="formatRacda()" 
                                    maxlength="6" 
                                    placeholder="000000" 
                                    class="w-full px-3 py-2 rounded-xl border border-gray-200 text-xs font-mono font-bold tracking-wider text-gray-900 bg-white focus:border-[#0d5238] focus:ring-1 focus:ring-[#0d5238]"
                                >
                            </div>
                        </div>
                    </div>

                    <!-- Modalidad de Manejo de Residuos por el Generador -->
                    <div class="pt-2 border-t border-gray-200/60">
                        <label class="block text-[11px] font-bold text-gray-800 uppercase mb-1.5">Modalidad de Manejo de Residuos</label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-3">
                            <label class="flex items-center gap-2 p-2.5 bg-white rounded-xl border border-gray-200 cursor-pointer">
                                <input type="radio" name="modalidad_manejadora" value="EXTERNA" x-model="form.perfil.modalidad_manejadora" class="text-[#0d5238] focus:ring-[#0d5238]">
                                <span class="text-xs font-medium text-gray-800">Contrata Empresa Manejadora Externa</span>
                            </label>
                            <label class="flex items-center gap-2 p-2.5 bg-white rounded-xl border border-gray-200 cursor-pointer">
                                <input type="radio" name="modalidad_manejadora" value="INTERNA" x-model="form.perfil.modalidad_manejadora" class="text-[#0d5238] focus:ring-[#0d5238]">
                                <span class="text-xs font-medium text-gray-800">Autogestión / Manejo Interno Propio</span>
                            </label>
                        </div>

                        <!-- Detalle de Empresa Manejadora Externa Normalizado -->
                        <div x-show="form.perfil.modalidad_manejadora === 'EXTERNA'" x-transition class="p-3 bg-emerald-50/40 rounded-xl border border-emerald-200/80 space-y-3">
                            <span class="block text-[10px] font-extrabold text-[#0d5238] uppercase">Datos de la Empresa Manejadora Principal</span>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                <div>
                                    <label class="block text-[10px] font-semibold text-gray-600 mb-1">Nombre / Razón Social *</label>
                                    <input type="text" x-model="form.perfil.manejadora_nombre" placeholder="Ej: Recicladora Los Teques C.A." class="w-full px-2.5 py-1.5 rounded-lg border border-gray-300 text-xs bg-white">
                                </div>

                                <!-- RIF Normalizado de Manejadora -->
                                <div>
                                    <label class="block text-[10px] font-semibold text-gray-600 mb-1">RIF Manejadora *</label>
                                    <div class="flex items-center gap-1">
                                        <select x-model="manejadoraRifInput.tipo" @change="formatManejadoraRif()" class="px-1.5 py-1.5 rounded-lg border border-gray-300 text-xs font-mono font-bold bg-white">
                                            <option value="J">J</option>
                                            <option value="G">G</option>
                                            <option value="V">V</option>
                                            <option value="E">E</option>
                                            <option value="P">P</option>
                                        </select>
                                        <span class="text-gray-400 font-bold text-xs">-</span>
                                        <input type="text" x-model="manejadoraRifInput.numero" @input="formatManejadoraRif()" maxlength="8" placeholder="12345678" class="w-full px-1.5 py-1.5 rounded-lg border border-gray-300 text-xs font-mono text-center bg-white">
                                        <span class="text-gray-400 font-bold text-xs">-</span>
                                        <input type="text" x-model="manejadoraRifInput.digito" @input="formatManejadoraRif()" maxlength="1" placeholder="0" class="w-8 px-1 py-1.5 rounded-lg border border-gray-300 text-xs font-mono text-center bg-white">
                                    </div>
                                </div>

                                <!-- RACDA Normalizado de Manejadora -->
                                <div>
                                    <label class="block text-[10px] font-semibold text-gray-600 mb-1">RACDA Manejadora *</label>
                                    <div class="flex items-center gap-1">
                                        <span class="px-2 py-1.5 rounded-lg border border-gray-200 text-xs font-mono font-bold text-gray-700 bg-gray-100 select-none">
                                            RACDA-M-
                                        </span>
                                        <input type="text" x-model="manejadoraRacdaInput.correlativo" @input="formatManejadoraRacda()" maxlength="6" placeholder="000000" class="w-full px-2 py-1.5 rounded-lg border border-gray-300 text-xs font-mono bg-white">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Evaluación de Módulos Específicos -->
                    <div class="pt-2 border-t border-gray-200/60">
                        <span class="block text-[11px] font-bold text-gray-700 uppercase mb-2">Módulos de Impacto Operativo</span>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-2.5 text-xs">
                            <label class="flex items-center gap-2 p-2.5 bg-white rounded-xl border border-gray-200 cursor-pointer">
                                <input type="checkbox" x-model="form.perfil.genera_efluentes" class="rounded text-[#0d5238]">
                                <span class="font-medium text-gray-700">Genera Efluentes Líquidos</span>
                            </label>
                            <label class="flex items-center gap-2 p-2.5 bg-white rounded-xl border border-gray-200 cursor-pointer">
                                <input type="checkbox" x-model="form.perfil.genera_emisiones" class="rounded text-[#0d5238]">
                                <span class="font-medium text-gray-700">Emisiones / Chimeneas</span>
                            </label>
                            <label class="flex items-center gap-2 p-2.5 bg-white rounded-xl border border-gray-200 cursor-pointer">
                                <input type="checkbox" x-model="form.perfil.genera_peligrosos" class="rounded text-[#0d5238]">
                                <span class="font-medium text-gray-700">Desechos Peligrosos / Químicos</span>
                            </label>
                            <label class="flex items-center gap-2 p-2.5 bg-white rounded-xl border border-gray-200 cursor-pointer">
                                <input type="checkbox" x-model="form.perfil.usa_pozo_agua" class="rounded text-[#0d5238]">
                                <span class="font-medium text-gray-700">Uso de Pozo de Agua</span>
                            </label>
                        </div>
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
                        <!-- RACDA Fijo en M para Manejador -->
                        <div>
                            <label class="block text-[11px] font-bold text-gray-700 uppercase mb-1">N° Registro RACDA *</label>
                            <div class="flex items-center gap-1.5">
                                <span class="px-2.5 py-2 rounded-xl border border-gray-200 text-xs font-mono font-bold text-gray-700 bg-gray-100 select-none">
                                    RACDA-M-
                                </span>
                                <input 
                                    type="text" 
                                    x-model="racdaInput.correlativo" 
                                    @input="formatRacda()" 
                                    maxlength="6" 
                                    placeholder="000000" 
                                    class="w-full px-3 py-2 rounded-xl border border-gray-200 text-xs font-mono font-bold tracking-wider text-gray-900 bg-white focus:border-[#0d5238] focus:ring-1 focus:ring-[#0d5238]"
                                >
                            </div>
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold text-gray-700 uppercase mb-1">Sustancias / Materiales Manejados</label>
                            <input type="text" x-model="form.perfil.sustancias_manejadas" placeholder="Ej: Aceites usados, chatarra, baterías" class="w-full px-3 py-2 rounded-xl border border-gray-200 text-xs bg-white">
                        </div>
                    </div>

                    <!-- Modalidad de Atención del Manejador -->
                    <div class="pt-2 border-t border-gray-200/60">
                        <label class="block text-[11px] font-bold text-gray-800 uppercase mb-1.5">Alcance de Prestatario / Clientes</label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-3">
                            <label class="flex items-center gap-2 p-2.5 bg-white rounded-xl border border-gray-200 cursor-pointer">
                                <input type="radio" name="modalidad_atencion_manejador" value="MULTICLIENTE" x-model="form.perfil.modalidad_atencion_manejador" class="text-[#0d5238] focus:ring-[#0d5238]">
                                <span class="text-xs font-medium text-gray-800">Servicio Público / Multicliente</span>
                            </label>
                            <label class="flex items-center gap-2 p-2.5 bg-white rounded-xl border border-gray-200 cursor-pointer">
                                <input type="radio" name="modalidad_atencion_manejador" value="EXCLUSIVO" x-model="form.perfil.modalidad_atencion_manejador" class="text-[#0d5238] focus:ring-[#0d5238]">
                                <span class="text-xs font-medium text-gray-800">Servicio Exclusivo a 1 Empresa Generadora</span>
                            </label>
                        </div>

                        <!-- Detalle del Generador Exclusivo Normalizado -->
                        <div x-show="form.perfil.modalidad_atencion_manejador === 'EXCLUSIVO'" x-transition class="p-3 bg-emerald-50/40 rounded-xl border border-emerald-200/80 space-y-3">
                            <span class="block text-[10px] font-extrabold text-[#0d5238] uppercase">Datos de la Empresa Generadora Exclusiva</span>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                <div>
                                    <label class="block text-[10px] font-semibold text-gray-600 mb-1">Nombre / Razón Social *</label>
                                    <input type="text" x-model="form.perfil.generador_exclusivo_nombre" placeholder="Ej: Cervecería Polar Planta San Antonio" class="w-full px-2.5 py-1.5 rounded-lg border border-gray-300 text-xs bg-white">
                                </div>

                                <!-- RIF Normalizado del Generador Exclusivo -->
                                <div>
                                    <label class="block text-[10px] font-semibold text-gray-600 mb-1">RIF Generador Exclusivo *</label>
                                    <div class="flex items-center gap-1">
                                        <select x-model="generadorRifInput.tipo" @change="formatGeneradorRif()" class="px-1.5 py-1.5 rounded-lg border border-gray-300 text-xs font-mono font-bold bg-white">
                                            <option value="J">J</option>
                                            <option value="G">G</option>
                                            <option value="V">V</option>
                                            <option value="E">E</option>
                                            <option value="P">P</option>
                                        </select>
                                        <span class="text-gray-400 font-bold text-xs">-</span>
                                        <input type="text" x-model="generadorRifInput.numero" @input="formatGeneradorRif()" maxlength="8" placeholder="12345678" class="w-full px-1.5 py-1.5 rounded-lg border border-gray-300 text-xs font-mono text-center bg-white">
                                        <span class="text-gray-400 font-bold text-xs">-</span>
                                        <input type="text" x-model="generadorRifInput.digito" @input="formatGeneradorRif()" maxlength="1" placeholder="0" class="w-8 px-1 py-1.5 rounded-lg border border-gray-300 text-xs font-mono text-center bg-white">
                                    </div>
                                </div>

                                <!-- RACDA Normalizado del Generador Exclusivo -->
                                <div>
                                    <label class="block text-[10px] font-semibold text-gray-600 mb-1">RACDA Generador *</label>
                                    <div class="flex items-center gap-1">
                                        <span class="px-2 py-1.5 rounded-lg border border-gray-200 text-xs font-mono font-bold text-gray-700 bg-gray-100 select-none">
                                            RACDA-G-
                                        </span>
                                        <input type="text" x-model="generadorRacdaInput.correlativo" @input="formatGeneradorRacda()" maxlength="6" placeholder="000000" class="w-full px-2 py-1.5 rounded-lg border border-gray-300 text-xs font-mono bg-white">
                                    </div>
                                </div>
                            </div>
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

            <!-- PASO 3: Consignación Dinámica de Recaudos y Fechas de Vigencia -->
            <div x-show="step === 3" x-transition.opacity class="space-y-5">
                <div class="border-b border-gray-100 pb-3">
                    <h4 class="text-xs font-extrabold text-gray-800 uppercase tracking-wider">3. Documentación, Fechas de Emisión y Vigencia</h4>
                    <p class="text-[11px] text-gray-500 mt-0.5">Adjunte en digital (PDF/Imagen) los recaudos y especifique sus fechas para el control de renovación.</p>
                </div>

                <div class="grid grid-cols-1 gap-4">
                    
                    <!-- Componente: RENMA -->
                    <div class="p-4 bg-gray-50/80 rounded-2xl border border-gray-200 space-y-3">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div class="space-y-0.5">
                                <span class="text-xs font-bold text-gray-800 block">1. Registro RENMA</span>
                                <span class="text-[10px] text-gray-500 block">Inscripción o actualización ecosocialista (MINEC)</span>
                            </div>
                            <label class="relative flex items-center justify-center px-4 py-2 bg-white border-2 border-dashed border-gray-300 rounded-xl cursor-pointer hover:border-[#0d5238] hover:bg-emerald-50/30 transition-all shrink-0">
                                <span x-text="files.doc_renma ? files.doc_renma.name : 'Actualizar / Seleccionar Archivo'" class="text-xs font-semibold text-gray-600 truncate max-w-[180px]"></span>
                                <input type="file" @change="handleFileUpload($event, 'doc_renma')" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                            </label>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-2 border-t border-gray-200/60 text-xs">
                            <div>
                                <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Fecha Emisión</label>
                                <input type="date" x-model="fechasEmision.doc_renma" class="w-full px-2.5 py-1.5 rounded-lg border border-gray-300 text-xs bg-white">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Fecha Vencimiento</label>
                                <input type="date" x-model="fechasVencimiento.doc_renma" :disabled="esVitalicio.doc_renma" class="w-full px-2.5 py-1.5 rounded-lg border border-gray-300 text-xs bg-white disabled:bg-gray-100 disabled:cursor-not-allowed">
                            </div>
                            <div class="flex items-center pt-5">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" x-model="esVitalicio.doc_renma" @change="if(esVitalicio.doc_renma) fechasVencimiento.doc_renma = ''" class="rounded text-[#0d5238]">
                                    <span class="font-semibold text-gray-700 text-[11px]">¿Es Vitalicio / Indefinido?</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Constancia de No Objeción (Si ROL === NO_APLICA) -->
                    <div x-show="form.perfil.rol_ambiental === 'NO_APLICA'" x-transition class="p-4 bg-amber-50/60 rounded-2xl border border-amber-200 space-y-3">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div class="space-y-0.5">
                                <span class="text-xs font-bold text-amber-900 block">2. Constancia de No Objeción Ambiental</span>
                                <span class="text-[10px] text-amber-700 block">Emitida por la Dirección Estadal del MINEC</span>
                            </div>
                            <label class="relative flex items-center justify-center px-4 py-2 bg-white border-2 border-dashed border-amber-300 rounded-xl cursor-pointer hover:border-amber-600 transition-all shrink-0">
                                <span x-text="files.doc_no_objecion ? files.doc_no_objecion.name : 'Actualizar / Seleccionar Archivo'" class="text-xs font-semibold text-amber-800 truncate max-w-[180px]"></span>
                                <input type="file" @change="handleFileUpload($event, 'doc_no_objecion')" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                            </label>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-2 border-t border-amber-200/60 text-xs">
                            <div>
                                <label class="block text-[10px] font-bold text-amber-800 uppercase mb-1">Fecha Emisión</label>
                                <input type="date" x-model="fechasEmision.doc_no_objecion" class="w-full px-2.5 py-1.5 rounded-lg border border-amber-300 text-xs bg-white">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-amber-800 uppercase mb-1">Fecha Vencimiento</label>
                                <input type="date" x-model="fechasVencimiento.doc_no_objecion" :disabled="esVitalicio.doc_no_objecion" class="w-full px-2.5 py-1.5 rounded-lg border border-amber-300 text-xs bg-white disabled:bg-gray-100 disabled:cursor-not-allowed">
                            </div>
                            <div class="flex items-center pt-5">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" x-model="esVitalicio.doc_no_objecion" @change="if(esVitalicio.doc_no_objecion) fechasVencimiento.doc_no_objecion = ''" class="rounded text-amber-700">
                                    <span class="font-semibold text-amber-900 text-[11px]">¿Es Vitalicio / Indefinido?</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- RACDA (Si ROL GENERADOR o MANEJADOR) -->
                    <div x-show="form.perfil.rol_ambiental !== 'NO_APLICA'" x-transition class="p-4 bg-gray-50/80 rounded-2xl border border-gray-200 space-y-3">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div class="space-y-0.5">
                                <span class="text-xs font-bold text-gray-800 block">3. Registro RACDA (Vence)</span>
                                <span class="text-[10px] text-gray-500 block">Constancia oficial de registro (Minea/MINEC)</span>
                            </div>
                            <label class="relative flex items-center justify-center px-4 py-2 bg-white border-2 border-dashed border-gray-300 rounded-xl cursor-pointer hover:border-[#0d5238] hover:bg-emerald-50/30 transition-all shrink-0">
                                <span x-text="files.doc_racda ? files.doc_racda.name : 'Actualizar / Seleccionar Archivo'" class="text-xs font-semibold text-gray-600 truncate max-w-[180px]"></span>
                                <input type="file" @change="handleFileUpload($event, 'doc_racda')" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                            </label>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-2 border-t border-gray-200/60 text-xs">
                            <div>
                                <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Fecha Emisión</label>
                                <input type="date" x-model="fechasEmision.doc_racda" class="w-full px-2.5 py-1.5 rounded-lg border border-gray-300 text-xs bg-white">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Fecha Vencimiento *</label>
                                <input type="date" x-model="fechasVencimiento.doc_racda" :disabled="esVitalicio.doc_racda" class="w-full px-2.5 py-1.5 rounded-lg border border-gray-300 text-xs bg-white disabled:bg-gray-100 disabled:cursor-not-allowed">
                            </div>
                            <div class="flex items-center pt-5">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" x-model="esVitalicio.doc_racda" @change="if(esVitalicio.doc_racda) fechasVencimiento.doc_racda = ''" class="rounded text-[#0d5238]">
                                    <span class="font-semibold text-gray-700 text-[11px]">¿Es Vitalicio / Indefinido?</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Póliza Ambiental (Obligatoria para MANEJADOR) -->
                    <div x-show="form.perfil.rol_ambiental === 'MANEJADOR'" x-transition class="p-4 bg-gray-50/80 rounded-2xl border border-gray-200 space-y-3">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div class="space-y-0.5">
                                <span class="text-xs font-bold text-gray-800 block">4. Póliza de Seguro de Responsabilidad Ambiental (Vence)</span>
                                <span class="text-[10px] text-gray-500 block">Aprobada por Sudeaseg y vigente</span>
                            </div>
                            <label class="relative flex items-center justify-center px-4 py-2 bg-white border-2 border-dashed border-gray-300 rounded-xl cursor-pointer hover:border-[#0d5238] hover:bg-emerald-50/30 transition-all shrink-0">
                                <span x-text="files.doc_poliza ? files.doc_poliza.name : 'Actualizar / Seleccionar Archivo'" class="text-xs font-semibold text-gray-600 truncate max-w-[180px]"></span>
                                <input type="file" @change="handleFileUpload($event, 'doc_poliza')" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                            </label>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-2 border-t border-gray-200/60 text-xs">
                            <div>
                                <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Fecha Emisión</label>
                                <input type="date" x-model="fechasEmision.doc_poliza" class="w-full px-2.5 py-1.5 rounded-lg border border-gray-300 text-xs bg-white">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Fecha Vencimiento *</label>
                                <input type="date" x-model="fechasVencimiento.doc_poliza" :disabled="esVitalicio.doc_poliza" class="w-full px-2.5 py-1.5 rounded-lg border border-gray-300 text-xs bg-white disabled:bg-gray-100 disabled:cursor-not-allowed">
                            </div>
                            <div class="flex items-center pt-5">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" x-model="esVitalicio.doc_poliza" @change="if(esVitalicio.doc_poliza) fechasVencimiento.doc_poliza = ''" class="rounded text-[#0d5238]">
                                    <span class="font-semibold text-gray-700 text-[11px]">¿Es Vitalicio / Indefinido?</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Caracterización de Efluentes -->
                    <div x-show="form.perfil.genera_efluentes" x-transition class="p-4 bg-gray-50/80 rounded-2xl border border-gray-200 space-y-3">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div class="space-y-0.5">
                                <span class="text-xs font-bold text-gray-800 block">5. Caracterización de Efluentes Líquidos</span>
                                <span class="text-[10px] text-gray-500 block">Análisis por laboratorio acreditado</span>
                            </div>
                            <label class="relative flex items-center justify-center px-4 py-2 bg-white border-2 border-dashed border-gray-300 rounded-xl cursor-pointer hover:border-[#0d5238] hover:bg-emerald-50/30 transition-all shrink-0">
                                <span x-text="files.doc_efluentes ? files.doc_efluentes.name : 'Actualizar / Seleccionar Archivo'" class="text-xs font-semibold text-gray-600 truncate max-w-[180px]"></span>
                                <input type="file" @change="handleFileUpload($event, 'doc_efluentes')" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                            </label>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-2 border-t border-gray-200/60 text-xs">
                            <div>
                                <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Fecha Emisión</label>
                                <input type="date" x-model="fechasEmision.doc_efluentes" class="w-full px-2.5 py-1.5 rounded-lg border border-gray-300 text-xs bg-white">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Fecha Vencimiento</label>
                                <input type="date" x-model="fechasVencimiento.doc_efluentes" :disabled="esVitalicio.doc_efluentes" class="w-full px-2.5 py-1.5 rounded-lg border border-gray-300 text-xs bg-white disabled:bg-gray-100 disabled:cursor-not-allowed">
                            </div>
                            <div class="flex items-center pt-5">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" x-model="esVitalicio.doc_efluentes" @change="if(esVitalicio.doc_efluentes) fechasVencimiento.doc_efluentes = ''" class="rounded text-[#0d5238]">
                                    <span class="font-semibold text-gray-700 text-[11px]">¿Es Vitalicio / Indefinido?</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Emisiones Atmosféricas -->
                    <div x-show="form.perfil.genera_emisiones" x-transition class="p-4 bg-gray-50/80 rounded-2xl border border-gray-200 space-y-3">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div class="space-y-0.5">
                                <span class="text-xs font-bold text-gray-800 block">6. Caracterización de Emisiones Atmosféricas</span>
                                <span class="text-[10px] text-gray-500 block">Medición de chimeneas / fuentes fijas</span>
                            </div>
                            <label class="relative flex items-center justify-center px-4 py-2 bg-white border-2 border-dashed border-gray-300 rounded-xl cursor-pointer hover:border-[#0d5238] hover:bg-emerald-50/30 transition-all shrink-0">
                                <span x-text="files.doc_emisiones ? files.doc_emisiones.name : 'Actualizar / Seleccionar Archivo'" class="text-xs font-semibold text-gray-600 truncate max-w-[180px]"></span>
                                <input type="file" @change="handleFileUpload($event, 'doc_emisiones')" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                            </label>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-2 border-t border-gray-200/60 text-xs">
                            <div>
                                <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Fecha Emisión</label>
                                <input type="date" x-model="fechasEmision.doc_emisiones" class="w-full px-2.5 py-1.5 rounded-lg border border-gray-300 text-xs bg-white">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Fecha Vencimiento</label>
                                <input type="date" x-model="fechasVencimiento.doc_emisiones" :disabled="esVitalicio.doc_emisiones" class="w-full px-2.5 py-1.5 rounded-lg border border-gray-300 text-xs bg-white disabled:bg-gray-100 disabled:cursor-not-allowed">
                            </div>
                            <div class="flex items-center pt-5">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" x-model="esVitalicio.doc_emisiones" @change="if(esVitalicio.doc_emisiones) fechasVencimiento.doc_emisiones = ''" class="rounded text-[#0d5238]">
                                    <span class="font-semibold text-gray-700 text-[11px]">¿Es Vitalicio / Indefinido?</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Plan de Manejo de Desechos Sólidos -->
                    <div class="p-4 bg-gray-50/80 rounded-2xl border border-gray-200 space-y-3">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div class="space-y-0.5">
                                <span class="text-xs font-bold text-gray-800 block">7. Plan de Manejo de Desechos y Residuos Sólidos</span>
                                <span class="text-[10px] text-gray-500 block">Rutas internas, segregación y disposición final</span>
                            </div>
                            <label class="relative flex items-center justify-center px-4 py-2 bg-white border-2 border-dashed border-gray-300 rounded-xl cursor-pointer hover:border-[#0d5238] hover:bg-emerald-50/30 transition-all shrink-0">
                                <span x-text="files.doc_plan_desechos ? files.doc_plan_desechos.name : 'Actualizar / Seleccionar Archivo'" class="text-xs font-semibold text-gray-600 truncate max-w-[180px]"></span>
                                <input type="file" @change="handleFileUpload($event, 'doc_plan_desechos')" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                            </label>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-2 border-t border-gray-200/60 text-xs">
                            <div>
                                <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Fecha Emisión</label>
                                <input type="date" x-model="fechasEmision.doc_plan_desechos" class="w-full px-2.5 py-1.5 rounded-lg border border-gray-300 text-xs bg-white">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Fecha Vencimiento</label>
                                <input type="date" x-model="fechasVencimiento.doc_plan_desechos" :disabled="esVitalicio.doc_plan_desechos" class="w-full px-2.5 py-1.5 rounded-lg border border-gray-300 text-xs bg-white disabled:bg-gray-100 disabled:cursor-not-allowed">
                            </div>
                            <div class="flex items-center pt-5">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" x-model="esVitalicio.doc_plan_desechos" @change="if(esVitalicio.doc_plan_desechos) fechasVencimiento.doc_plan_desechos = ''" class="rounded text-[#0d5238]">
                                    <span class="font-semibold text-gray-700 text-[11px]">¿Es Vitalicio / Indefinido?</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Plan de Emergencia y Contingencia -->
                    <div class="p-4 bg-gray-50/80 rounded-2xl border border-gray-200 space-y-3">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div class="space-y-0.5">
                                <span class="text-xs font-bold text-gray-800 block">8. Plan de Emergencia y Contingencia Ambiental</span>
                                <span class="text-[10px] text-gray-500 block">Protocolos de actuación ante derrames/incendios</span>
                            </div>
                            <label class="relative flex items-center justify-center px-4 py-2 bg-white border-2 border-dashed border-gray-300 rounded-xl cursor-pointer hover:border-[#0d5238] hover:bg-emerald-50/30 transition-all shrink-0">
                                <span x-text="files.doc_plan_emergencia ? files.doc_plan_emergencia.name : 'Actualizar / Seleccionar Archivo'" class="text-xs font-semibold text-gray-600 truncate max-w-[180px]"></span>
                                <input type="file" @change="handleFileUpload($event, 'doc_plan_emergencia')" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                            </label>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-2 border-t border-gray-200/60 text-xs">
                            <div>
                                <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Fecha Emisión</label>
                                <input type="date" x-model="fechasEmision.doc_plan_emergencia" class="w-full px-2.5 py-1.5 rounded-lg border border-gray-300 text-xs bg-white">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-gray-600 uppercase mb-1">Fecha Vencimiento</label>
                                <input type="date" x-model="fechasVencimiento.doc_plan_emergencia" :disabled="esVitalicio.doc_plan_emergencia" class="w-full px-2.5 py-1.5 rounded-lg border border-gray-300 text-xs bg-white disabled:bg-gray-100 disabled:cursor-not-allowed">
                            </div>
                            <div class="flex items-center pt-5">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" x-model="esVitalicio.doc_plan_emergencia" @change="if(esVitalicio.doc_plan_emergencia) fechasVencimiento.doc_plan_emergencia = ''" class="rounded text-[#0d5238]">
                                    <span class="font-semibold text-gray-700 text-[11px]">¿Es Vitalicio / Indefinido?</span>
                                </label>
                            </div>
                        </div>
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
                    <a 
                        href="{{ route('ciudadano.empresas') }}"
                        class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-bold rounded-xl transition-colors cursor-pointer"
                    >
                        Cancelar
                    </a>

                    <button 
                        type="button" 
                        x-show="step < 3" 
                        @click="nextStep()" 
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
                        <span x-text="submitting ? 'Procesando...' : (isEditing ? 'Reenviar Expediente Corregido' : 'Finalizar Registro')"></span>
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- VENTANA DE ALERTA MODAL MODERNA -->
    <div 
        x-show="showAlertModal" 
        x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/60 backdrop-blur-xs"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
    >
        <div 
            @click.outside="showAlertModal = false"
            class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-gray-100 space-y-5 text-center transform transition-all"
        >
            <div class="mx-auto w-12 h-12 rounded-2xl bg-amber-100 text-amber-600 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>

            <div>
                <h3 class="text-base font-bold text-gray-900">Campos Incompletos</h3>
                <p class="text-xs text-gray-500 mt-1 whitespace-pre-line" x-text="alertMessage"></p>
            </div>

            <button 
                type="button" 
                @click="showAlertModal = false"
                class="w-full py-2.5 px-4 rounded-xl bg-[#0d5238] hover:bg-[#073825] text-white text-xs font-bold shadow-md transition-colors cursor-pointer"
            >
                Entendido, completar datos
            </button>
        </div>
    </div>
</div>

<script>
function vincularEmpresaForm(territorios, empresaExistente) {
    return {
        step: 1,
        submitting: false,
        showAlertModal: false,
        alertMessage: '',
        territoriosData: territorios || {},
        sectoresDisponibles: [],
        files: {},
        fechasEmision: {
            doc_renma: '',
            doc_no_objecion: '',
            doc_racda: '',
            doc_poliza: '',
            doc_efluentes: '',
            doc_emisiones: '',
            doc_plan_desechos: '',
            doc_plan_emergencia: ''
        },
        fechasVencimiento: {
            doc_renma: '',
            doc_no_objecion: '',
            doc_racda: '',
            doc_poliza: '',
            doc_efluentes: '',
            doc_emisiones: '',
            doc_plan_desechos: '',
            doc_plan_emergencia: ''
        },
        esVitalicio: {
            doc_renma: false,
            doc_no_objecion: false,
            doc_racda: false,
            doc_poliza: false,
            doc_efluentes: false,
            doc_emisiones: false,
            doc_plan_desechos: true,
            doc_plan_emergencia: true
        },
        
        rifInput: { tipo: 'J', numero: '', digito: '' },
        patenteInput: { tipo_formato: 'SIGAT_OFICIAL', prefix: 'CZ-AE-', correlativo: '' },

        racdaInput: { correlativo: '' },

        manejadoraRifInput: { tipo: 'J', numero: '', digito: '' },
        manejadoraRacdaInput: { correlativo: '' },

        generadorRifInput: { tipo: 'J', numero: '', digito: '' },
        generadorRacdaInput: { correlativo: '' },

        isEditing: false,

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
                modalidad_manejadora: 'EXTERNA',
                manejadora_nombre: '',
                manejadora_rif: '',
                manejadora_racda: '',
                modalidad_atencion_manejador: 'MULTICLIENTE',
                generador_exclusivo_nombre: '',
                generador_exclusivo_rif: '',
                generador_exclusivo_racda: '',
                genera_efluentes: false,
                genera_emisiones: false,
                genera_peligrosos: false,
                genera_ruidos: false,
                usa_pozo_agua: false,
                actividades_manejador: [],
                sustancias_manejadas: '',
                residuos_tipos: []
            }
        },

        init() {
            if (empresaExistente) {
                this.isEditing = true;
                this.loadEmpresaData(empresaExistente);
            }
            this.formatRacda();
        },

        showModal(msg) {
            this.alertMessage = msg;
            this.showAlertModal = true;
        },

        handleKeydownEnter(event) {
            // Prevenir envío accidental al presionar Enter a menos que esté en el botón de finalizar del paso 3
            if (this.step < 3) {
                event.preventDefault();
                this.nextStep();
            } else if (this.step === 3) {
                event.preventDefault();
                this.submitForm();
            }
        },

        loadEmpresaData(data) {
            this.form.razon_social = data.razon_social || '';
            this.form.nombre_comercial = data.nombre_comercial || '';
            this.form.correo_comercial = data.correo_comercial || '';
            
            if (data.rif) {
                const parts = data.rif.split('-');
                if (parts.length === 3) {
                    this.rifInput.tipo = parts[0];
                    this.rifInput.numero = parts[1];
                    this.rifInput.digito = parts[2];
                }
            }

            if (data.telefono_comercial && data.telefono_comercial.length >= 11) {
                this.form.codigo_area = data.telefono_comercial.substring(0, 4);
                this.form.telefono_num = data.telefono_comercial.substring(4);
            }

            this.form.comuna = data.comuna || '';
            this.onComunaChange();
            this.form.sector = data.sector || '';
            this.form.calle_avenida = data.calle_avenida || '';
            this.form.edificio_galpon_local = data.edificio_galpon_local || '';
            this.form.numero_cuenta_patente = data.numero_cuenta_patente || '';
            this.patenteInput.correlativo = data.numero_cuenta_patente ? data.numero_cuenta_patente.replace('CZ-AE-', '') : '';

            // Precarga del Bloque 2: Perfil Ambiental
            if (data.perfil_ambiental) {
                const p = data.perfil_ambiental;
                this.form.perfil.rol_ambiental = p.rol_ambiental || 'GENERADOR';
                this.form.perfil.subtipo_generador = p.subtipo_generador || 'PEQUENO';
                this.form.perfil.genera_efluentes = !!p.genera_efluentes;
                this.form.perfil.genera_emisiones = !!p.genera_emisiones;
                this.form.perfil.genera_peligrosos = !!p.genera_peligrosos;
                this.form.perfil.genera_ruidos = !!p.genera_ruidos;
                this.form.perfil.usa_pozo_agua = !!p.usa_pozo_agua;
                this.form.perfil.modalidad_manejadora = p.modalidad_manejadora || 'EXTERNA';
                this.form.perfil.manejadora_nombre = p.manejadora_nombre || p.manejadora_contratada || '';
                this.form.perfil.manejadora_rif = p.manejadora_rif || '';
                this.form.perfil.manejadora_racda = p.manejadora_racda || '';
                this.form.perfil.modalidad_atencion_manejador = p.modalidad_atencion_manejador || 'MULTICLIENTE';
                this.form.perfil.generador_exclusivo_nombre = p.generador_exclusivo_nombre || '';
                this.form.perfil.sustancias_manejadas = p.sustancias_manejadas || '';
                this.form.perfil.actividades_manejador = p.actividades_manejador || [];
                this.form.perfil.residuos_tipos = p.residuos_tipos || [];

                // Extraer correlativo RACDA si existe
                if (p.numero_racda) {
                    const cleanRacda = p.numero_racda.replace(/^(RACDA-[GM]-)/i, '');
                    this.racdaInput.correlativo = cleanRacda;
                }
            }

            // Precarga del Bloque 3: Fechas y Vigencias de Documentos del Expediente
            if (data.documentos_expediente && Array.isArray(data.documentos_expediente)) {
                data.documentos_expediente.forEach(doc => {
                    // Mapeo inverso del código (ej: REGISTRO_RENMA o doc_renma)
                    const key = doc.codigo_requisito ? doc.codigo_requisito.toLowerCase() : '';
                    
                    if (this.fechasEmision.hasOwnProperty(key)) {
                        this.fechasEmision[key] = doc.fecha_emision ? doc.fecha_emision.substring(0, 10) : '';
                    }
                    if (this.fechasVencimiento.hasOwnProperty(key)) {
                        this.fechasVencimiento[key] = doc.fecha_vencimiento ? doc.fecha_vencimiento.substring(0, 10) : '';
                    }
                    if (this.esVitalicio.hasOwnProperty(key)) {
                        this.esVitalicio[key] = !!doc.es_vitalicio;
                    }
                });
            }
        },

        onRolChange(nuevoRol) {
            this.form.perfil.rol_ambiental = nuevoRol;
            this.formatRacda();
        },

        formatRacda() {
            if (this.form.perfil.rol_ambiental === 'NO_APLICA') {
                this.form.perfil.numero_racda = 'RACDA No aplica';
                return;
            }
            this.racdaInput.correlativo = this.racdaInput.correlativo.replace(/\D/g, '');
            if (this.racdaInput.correlativo) {
                const numPadded = this.racdaInput.correlativo.padStart(6, '0');
                const prefix = this.form.perfil.rol_ambiental === 'GENERADOR' ? 'RACDA-G-' : 'RACDA-M-';
                this.form.perfil.numero_racda = `${prefix}${numPadded}`;
            } else {
                this.form.perfil.numero_racda = '';
            }
        },

        formatManejadoraRif() {
            this.manejadoraRifInput.numero = this.manejadoraRifInput.numero.replace(/\D/g, '');
            this.manejadoraRifInput.digito = this.manejadoraRifInput.digito.replace(/\D/g, '');
            if (this.manejadoraRifInput.numero || this.manejadoraRifInput.digito) {
                this.form.perfil.manejadora_rif = `${this.manejadoraRifInput.tipo}-${this.manejadoraRifInput.numero}-${this.manejadoraRifInput.digito}`;
            } else {
                this.form.perfil.manejadora_rif = '';
            }
        },

        formatManejadoraRacda() {
            this.manejadoraRacdaInput.correlativo = this.manejadoraRacdaInput.correlativo.replace(/\D/g, '');
            if (this.manejadoraRacdaInput.correlativo) {
                const numPadded = this.manejadoraRacdaInput.correlativo.padStart(6, '0');
                this.form.perfil.manejadora_racda = `RACDA-M-${numPadded}`;
            } else {
                this.form.perfil.manejadora_racda = '';
            }
        },

        formatGeneradorRif() {
            this.generadorRifInput.numero = this.generadorRifInput.numero.replace(/\D/g, '');
            this.generadorRifInput.digito = this.generadorRifInput.digito.replace(/\D/g, '');
            if (this.generadorRifInput.numero || this.generadorRifInput.digito) {
                this.form.perfil.generador_exclusivo_rif = `${this.generadorRifInput.tipo}-${this.generadorRifInput.numero}-${this.generadorRifInput.digito}`;
            } else {
                this.form.perfil.generador_exclusivo_rif = '';
            }
        },

        formatGeneradorRacda() {
            this.generadorRacdaInput.correlativo = this.generadorRacdaInput.correlativo.replace(/\D/g, '');
            if (this.generadorRacdaInput.correlativo) {
                const numPadded = this.generadorRacdaInput.correlativo.padStart(6, '0');
                this.form.perfil.generador_exclusivo_racda = `RACDA-G-${numPadded}`;
            } else {
                this.form.perfil.generador_exclusivo_racda = '';
            }
        },

        formatRif() {
            this.rifInput.numero = this.rifInput.numero.replace(/\D/g, '');
            this.rifInput.digito = this.rifInput.digito.replace(/\D/g, '');
            if (this.rifInput.numero.length === 8 && this.$refs.rifDigito) {
                this.$refs.rifDigito.focus();
            }
            this.form.rif = `${this.rifInput.tipo}-${this.rifInput.numero}-${this.rifInput.digito}`;
        },

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

        nextStep() {
            this.formatRif();
            this.formatPatente();

            if (this.step === 1) {
                if (!this.rifInput.numero || !this.rifInput.digito) {
                    this.showModal('Por favor complete el número RIF.');
                    return;
                }
                if (!this.patenteInput.correlativo) {
                    this.showModal('Por favor complete el N° Cuenta / Licencia Patente.');
                    return;
                }
                if (!this.form.razon_social || !this.form.correo_comercial || !this.form.telefono_num) {
                    this.showModal('Por favor complete la Razón Social, Correo y Teléfono comercial.');
                    return;
                }
                if (!this.form.comuna || !this.form.sector || !this.form.calle_avenida || !this.form.edificio_galpon_local) {
                    this.showModal('Por favor complete todos los datos de ubicación territorial.');
                    return;
                }
            }

            // Validación estricta para impedir avanzar al envío si faltan documentos clave en el Paso 3
            if (this.step === 3) {
                if (!this.files.doc_renma) {
                    this.showModal('El registro RENMA es un recaudo obligatorio. Por favor adjunte el archivo PDF o imagen.');
                    return;
                }
                if (this.form.perfil.rol_ambiental !== 'NO_APLICA' && !this.files.doc_racda) {
                    this.showModal('El certificado RACDA es obligatorio para empresas Generadoras o Manejadoras.');
                    return;
                }
                if (this.form.perfil.rol_ambiental === 'MANEJADOR' && !this.files.doc_poliza) {
                    this.showModal('La Póliza de Seguro de Responsabilidad Ambiental es obligatoria para empresas Manejadoras.');
                    return;
                }
            }

            if (this.step < 3) {
                this.step++;
            }
        },

        async submitForm() {
            this.submitting = true;
            
            this.formatRif();
            this.formatPatente();
            this.formatRacda();
            this.formatManejadoraRif();
            this.formatManejadoraRacda();
            this.formatGeneradorRif();
            this.formatGeneradorRacda();

            const formData = new FormData();
            formData.append('rif', this.form.rif);
            formData.append('numero_cuenta_patente', this.form.numero_cuenta_patente);
            formData.append('razon_social', this.form.razon_social);
            formData.append('nombre_comercial', this.form.nombre_comercial || '');
            formData.append('correo_comercial', this.form.correo_comercial);
            formData.append('telefono_comercial', `${this.form.codigo_area}${this.form.telefono_num}`);
            formData.append('comuna', this.form.comuna);
            formData.append('sector', this.form.sector);
            formData.append('calle_avenida', this.form.calle_avenida);
            formData.append('edificio_galpon_local', this.form.edificio_galpon_local);
            
            Object.keys(this.form.perfil).forEach(key => {
                const val = this.form.perfil[key];
                if (Array.isArray(val)) {
                    val.forEach((item, index) => {
                        formData.append(`perfil[${key}][${index}]`, item);
                    });
                } else if (typeof val === 'boolean') {
                    formData.append(`perfil[${key}]`, val ? '1' : '0');
                } else {
                    formData.append(`perfil[${key}]`, val ?? '');
                }
            });

            Object.keys(this.files).forEach(key => {
                if (this.files[key]) {
                    formData.append(`documentos[${key}]`, this.files[key]);
                }
            });

            Object.keys(this.fechasEmision).forEach(key => {
                if (this.fechasEmision[key]) {
                    formData.append(`fechas_emision[${key}]`, this.fechasEmision[key]);
                }
            });

            Object.keys(this.fechasVencimiento).forEach(key => {
                if (this.fechasVencimiento[key]) {
                    formData.append(`fechas_vencimiento[${key}]`, this.fechasVencimiento[key]);
                }
            });

            Object.keys(this.esVitalicio).forEach(key => {
                formData.append(`es_vitalicio[${key}]`, this.esVitalicio[key] ? '1' : '0');
            });

            try {
                const response = await fetch("{{ route('ciudadano.empresas.store') }}", {
                    method: 'POST',
                    headers: { 
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                if (response.ok) {
                    window.location.href = "{{ route('ciudadano.empresas') }}";
                } else {
                    const data = await response.json();
                    if (data.errors) {
                        const messages = Object.values(data.errors).flat().join('\n');
                        this.showModal('Errores de validación:\n' + messages);
                    } else {
                        this.showModal(data.message || 'Ocurrió un error al procesar el formulario.');
                    }
                }
            } catch (error) {
                console.error('Error de red:', error);
                this.showModal('Error de conexión con el servidor.');
            } finally {
                this.submitting = false;
            }
        }
    }
}
</script>
</x-layouts.app>