<x-layouts.app title="Empresa - Panel Ciudadano" active="empresas">
<div class="p-4 sm:p-6 max-w-6xl mx-auto space-y-6" x-data="{ openDetailModal: false, selectedEmpresa: null }">
    
    @php
        // Garantizamos obtener el objeto individual de empresa desde la colección o variable directa
        $empresa = $empresa ?? $empresas->first();
    @endphp

    <!-- Encabezado de la Sección -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-5 border-b border-gray-200">
        <div>
            <h1 class="text-xl sm:text-2xl font-extrabold text-gray-900 tracking-tight flex items-center gap-2">
                <svg class="w-6 h-6 text-[#0d5238]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m0 0h4m-4 0V11m0 0h4m-4 0H7m4 0v10" />
                </svg>
                Representación Comercial y Empresas
            </h1>
            <p class="text-xs sm:text-sm text-gray-500 font-medium mt-1">
                Gestión de expediente ambiental comercial ante la Alcaldía del Municipio Carrizal.
            </p>
        </div>

        @if(!$empresa)
            <a 
                href="{{ route('ciudadano.empresas.crear') }}"
                class="px-4 py-2.5 bg-[#0d5238] hover:bg-[#073825] text-white text-xs font-bold rounded-xl flex items-center justify-center gap-2 shadow-xs transition-all cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Vincular Empresa
            </a>
        @endif
    </div>

    <!-- CASO A: El ciudadano no posee empresas vinculadas -->
    @if(!$empresa)
        <div class="bg-white border-2 border-dashed border-gray-200 rounded-3xl p-8 sm:p-12 text-center space-y-4">
            <div class="w-16 h-16 bg-emerald-50 text-[#0d5238] rounded-2xl flex items-center justify-center mx-auto border border-emerald-100">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m0 0h4m-4 0V11m0 0h4m-4 0H7m4 0v10" />
                </svg>
            </div>
            <div class="max-w-md mx-auto space-y-1">
                <h3 class="text-base font-bold text-gray-900">No posees empresas vinculadas</h3>
                <p class="text-xs text-gray-500 leading-relaxed">
                    Como ciudadano registrado, puedes vincular tu firma comercial para consignar el perfil ambiental y solicitar certificaciones de residuos.
                </p>
            </div>
            <a 
                href="{{ route('ciudadano.empresas.crear') }}"
                class="px-5 py-2.5 bg-[#0d5238] hover:bg-[#073825] text-white text-xs font-bold rounded-xl inline-flex items-center gap-2 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Vincular Empresa
            </a>
        </div>
    @else
        <!-- CASO B: El ciudadano ya posee su empresa vinculada -->
        <div class="bg-white border border-gray-200 rounded-3xl p-6 shadow-xs space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4 border-b border-gray-100 pb-4">
                <div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <h2 class="text-lg font-bold text-gray-900">{{ $empresa->razon_social }}</h2>
                        <span class="px-2.5 py-0.5 bg-gray-100 text-gray-700 font-mono text-xs font-extrabold rounded-md border border-gray-200">
                            {{ $empresa->rif }}
                        </span>
                    </div>
                    <p class="text-xs text-gray-500 font-medium mt-0.5">
                        Nombre Comercial: <span class="text-gray-800 font-semibold">{{ $empresa->nombre_comercial ?? $empresa->razon_social }}</span>
                    </p>
                </div>

                <!-- Insignia de Estatus Dinámica -->
                <div>
                    @if($empresa->estatus === 'PENDIENTE_APROBACION')
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800 border border-amber-200">
                            <span class="w-2 h-2 rounded-full bg-amber-500 animate-ping"></span>
                            En Revisión por Dirección de Residuos
                        </span>
                    @elseif($empresa->estatus === 'APROBADO')
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-[#0d5238] border border-emerald-200">
                            Expediente Conforme y Activo
                        </span>
                    @elseif($empresa->estatus === 'OBSERVADO' || $empresa->estatus === 'RECHAZADO')
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-rose-100 text-rose-800 border border-rose-200">
                            Requiere Correcciones
                        </span>
                    @endif
                </div>
            </div>

            <!-- Datos Clave -->
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 text-xs">
                <div class="p-3 bg-gray-50 rounded-2xl border border-gray-100 space-y-1">
                    <span class="text-gray-400 font-semibold block text-[10px] uppercase">Patente Municipal</span>
                    <span class="font-bold text-gray-800 font-mono">{{ $empresa->numero_cuenta_patente }}</span>
                </div>

                <div class="p-3 bg-gray-50 rounded-2xl border border-gray-100 space-y-1">
                    <span class="text-gray-400 font-semibold block text-[10px] uppercase">Sector</span>
                    <span class="font-bold text-gray-800">{{ $empresa->sector }}</span>
                </div>

                <div class="p-3 bg-gray-50 rounded-2xl border border-gray-100 space-y-1">
                    <span class="text-gray-400 font-semibold block text-[10px] uppercase">Correo Comercial</span>
                    <span class="font-bold text-gray-800 truncate block">{{ $empresa->correo_comercial }}</span>
                </div>

                <div class="p-3 bg-gray-50 rounded-2xl border border-gray-100 space-y-1">
                    <span class="text-gray-400 font-semibold block text-[10px] uppercase">Teléfono</span>
                    <span class="font-bold text-gray-800">{{ $empresa->telefono_comercial }}</span>
                </div>
            </div>

            @if($empresa->estatus === 'PENDIENTE_APROBACION')
                <div class="p-4 bg-amber-50 rounded-2xl border border-amber-200 text-xs text-amber-900">
                    <p class="font-bold">Expediente bajo evaluación técnica</p>
                    <p class="text-amber-800 mt-0.5 leading-relaxed">
                        Los recaudos y la declaración de residuos están siendo auditados por la Dirección de Residuos y Desechos Sólidos.
                    </p>
                </div>
            @endif

            <!-- Pie de tarjeta: Fecha y Botones alineados a la derecha -->
            <div class="pt-3 flex items-center justify-between border-t border-gray-100 text-xs">
                <span class="text-gray-400 font-medium">
                    Registrado el: {{ $empresa->created_at ? $empresa->created_at->format('d/m/Y') : 'N/A' }}
                </span>

                <div class="flex items-center gap-2">
                    <button 
                        type="button"
                        @click="selectedEmpresa = {{ $empresa->toJson() }}; openDetailModal = true"
                        class="px-3.5 py-2 bg-emerald-50 text-[#0d5238] border border-emerald-200 hover:bg-emerald-100 rounded-xl font-bold transition-all cursor-pointer"
                    >
                        Ver Detalle
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Modal de Detalle de la Empresa -->
    <div 
        x-show="openDetailModal" 
        x-cloak 
        class="fixed inset-0 z-50 overflow-y-auto"
        aria-labelledby="modal-title" 
        role="dialog" 
        aria-modal="true"
    >
        <!-- Backdrop -->
        <div 
            x-show="openDetailModal"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity"
            @click="openDetailModal = false"
        ></div>

        <!-- Contenedor Centrado -->
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div 
                x-show="openDetailModal"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="relative transform overflow-hidden rounded-3xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-4xl border border-gray-100"
            >
                <template x-if="selectedEmpresa">
                    <div>
                        <!-- Header del Modal -->
                        <div class="bg-[#0d5238] p-6 text-white flex justify-between items-start">
                            <div>
                                <span class="text-xs font-bold text-emerald-200 uppercase tracking-widest block">Expediente Registrado</span>
                                <h3 class="text-xl font-black mt-1" x-text="selectedEmpresa.razon_social"></h3>
                                <p class="text-xs text-emerald-100/80 mt-1">
                                    RIF: <span x-text="selectedEmpresa.rif"></span> • Patente: <span x-text="selectedEmpresa.numero_cuenta_patente"></span>
                                </p>
                            </div>
                            <button 
                                @click="openDetailModal = false" 
                                class="text-white/70 hover:text-white bg-white/10 hover:bg-white/20 rounded-full p-2 transition-all cursor-pointer"
                            >
                                ✕
                            </button>
                        </div>

                        <!-- Cuerpo del Modal -->
                        <div class="p-6 max-h-[75vh] overflow-y-auto space-y-6">

                            <!-- Seccion 1: Datos de Contacto y Ubicación -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="bg-gray-50 p-4 rounded-2xl border border-gray-100 space-y-2 text-xs">
                                    <h4 class="font-extrabold text-gray-800 uppercase tracking-wider border-b border-gray-200 pb-1">Ubicación Territorial</h4>
                                    <p><strong class="text-gray-500">Comuna:</strong> <span x-text="selectedEmpresa.comuna"></span></p>
                                    <p><strong class="text-gray-500">Sector:</strong> <span x-text="selectedEmpresa.sector"></span></p>
                                    <p><strong class="text-gray-500">Calle/Avenida:</strong> <span x-text="selectedEmpresa.calle_avenida"></span></p>
                                    <p><strong class="text-gray-500">Local/Galpón:</strong> <span x-text="selectedEmpresa.edificio_galpon_local"></span></p>
                                </div>

                                <div class="bg-gray-50 p-4 rounded-2xl border border-gray-100 space-y-2 text-xs">
                                    <h4 class="font-extrabold text-gray-800 uppercase tracking-wider border-b border-gray-200 pb-1">Contacto Comercial</h4>
                                    <p><strong class="text-gray-500">Correo:</strong> <span x-text="selectedEmpresa.correo_comercial"></span></p>
                                    <p><strong class="text-gray-500">Teléfono:</strong> <span x-text="selectedEmpresa.telefono_comercial"></span></p>
                                    <p><strong class="text-gray-500">Estatus:</strong> 
                                        <span class="px-2 py-0.5 rounded-md font-bold text-[10px] uppercase"
                                            :class="{
                                                'bg-amber-100 text-amber-800': selectedEmpresa.estatus === 'PENDIENTE_APROBACION',
                                                'bg-emerald-100 text-emerald-800': selectedEmpresa.estatus === 'APROBADO',
                                                'bg-rose-100 text-rose-800': selectedEmpresa.estatus === 'RECHAZADO' || selectedEmpresa.estatus === 'OBSERVADO'
                                            }"
                                            x-text="selectedEmpresa.estatus.replace('_', ' ')">
                                        </span>
                                    </p>
                                </div>
                            </div>

                            <!-- Sección 2: Perfil Ambiental -->
                            <template x-if="selectedEmpresa.perfil_ambiental">
                                <div class="bg-gray-50 p-4 rounded-2xl border border-gray-100 space-y-4 text-xs">
                                    <h4 class="font-extrabold text-[#0d5238] uppercase tracking-wider border-b border-gray-200 pb-1">Diagnóstico Ambiental</h4>
                                    
                                    <!-- Datos Principales del Perfil -->
                                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                        <div>
                                            <span class="text-gray-400 block text-[10px] uppercase font-semibold">Rol Ambiental</span>
                                            <strong class="text-gray-800" x-text="selectedEmpresa.perfil_ambiental.rol_ambiental"></strong>
                                        </div>
                                        <div>
                                            <span class="text-gray-400 block text-[10px] uppercase font-semibold">Subtipo</span>
                                            <strong class="text-gray-800" x-text="selectedEmpresa.perfil_ambiental.subtipo_generador || 'N/A'"></strong>
                                        </div>
                                        <div>
                                            <span class="text-gray-400 block text-[10px] uppercase font-semibold">N° RACDA</span>
                                            <strong class="text-gray-800" x-text="selectedEmpresa.perfil_ambiental.numero_racda || 'No Aplica'"></strong>
                                        </div>
                                    </div>

                                    <!-- Módulos de Impacto Operativo -->
                                    <div class="pt-3 border-t border-gray-200/60">
                                        <span class="text-gray-400 block text-[10px] uppercase font-semibold mb-2">Módulos de Impacto Operativo</span>
                                        <div class="flex flex-wrap gap-2">
                                            <!-- Efluentes Líquidos -->
                                            <span class="px-2.5 py-1 rounded-lg text-[11px] font-bold border flex items-center gap-1.5"
                                                :class="selectedEmpresa.perfil_ambiental.genera_efluentes 
                                                    ? 'bg-emerald-50 text-emerald-800 border-emerald-200' 
                                                    : 'bg-gray-100 text-gray-400 border-gray-200 opacity-60'">
                                                <span x-text="selectedEmpresa.perfil_ambiental.genera_efluentes ? '✓' : '✕'"></span>
                                                Genera Efluentes Líquidos
                                            </span>

                                            <!-- Emisiones / Chimeneas -->
                                            <span class="px-2.5 py-1 rounded-lg text-[11px] font-bold border flex items-center gap-1.5"
                                                :class="selectedEmpresa.perfil_ambiental.genera_emisiones 
                                                    ? 'bg-emerald-50 text-emerald-800 border-emerald-200' 
                                                    : 'bg-gray-100 text-gray-400 border-gray-200 opacity-60'">
                                                <span x-text="selectedEmpresa.perfil_ambiental.genera_emisiones ? '✓' : '✕'"></span>
                                                Emisiones / Chimeneas / Calderas
                                            </span>

                                            <!-- Desechos Peligrosos -->
                                            <span class="px-2.5 py-1 rounded-lg text-[11px] font-bold border flex items-center gap-1.5"
                                                :class="selectedEmpresa.perfil_ambiental.genera_peligrosos 
                                                    ? 'bg-amber-50 text-amber-900 border-amber-300' 
                                                    : 'bg-gray-100 text-gray-400 border-gray-200 opacity-60'">
                                                <span x-text="selectedEmpresa.perfil_ambiental.genera_peligrosos ? '✓' : '✕'"></span>
                                                Desechos Peligrosos / Químicos
                                            </span>
                                        </div>

                                        <!-- Manejadora Contratada (Si aplica) -->
                                        <template x-if="selectedEmpresa.perfil_ambiental.genera_peligrosos && selectedEmpresa.perfil_ambiental.manejadora_contratada">
                                            <div class="mt-2.5 p-2.5 bg-amber-50/60 rounded-xl border border-amber-200/80 text-amber-900">
                                                <span class="text-[10px] uppercase font-bold text-amber-800 block">Empresa Manejadora Contratada (RIF / RACDA)</span>
                                                <span class="font-medium" x-text="selectedEmpresa.perfil_ambiental.manejadora_contratada"></span>
                                            </div>
                                        </template>
                                    </div>

                                    <!-- Residuos Generados Declarados -->
                                    <div class="pt-3 border-t border-gray-200/60" x-data="{
                                        getResiduosList(data) {
                                            if (!data) return [];
                                            if (Array.isArray(data)) return data;
                                            try { return JSON.parse(data); } catch(e) { return []; }
                                        },
                                        getNombreResiduo(key) {
                                            const mapa = {
                                                'organicos': 'Orgánicos / Biodegradables',
                                                'plasticos_carton': 'Plásticos / Cartón / Papel',
                                                'peligrosos_aceites': 'Aceites / Químicos',
                                                'metalicos_chatarra': 'Metálicos / Chatarra',
                                                'hospitalarios_biologicos': 'Biológicos / Médicos'
                                            };
                                            return mapa[key] || key;
                                        }
                                    }">
                                        <span class="text-gray-400 block text-[10px] uppercase font-semibold mb-2">Tipos de Residuos Generados</span>

                                        <template x-if="selectedEmpresa?.perfil_ambiental?.residuos_tipos && getResiduosList(selectedEmpresa.perfil_ambiental.residuos_tipos).length > 0">
                                            <div class="flex flex-wrap gap-1.5">
                                                <template x-for="item in getResiduosList(selectedEmpresa.perfil_ambiental.residuos_tipos)" :key="item">
                                                    <span class="px-2.5 py-1 bg-emerald-50 border border-emerald-200 text-[#0d5238] rounded-lg font-bold text-[11px] flex items-center gap-1">
                                                        <svg class="w-3 h-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                        </svg>
                                                        <span x-text="getNombreResiduo(item)"></span>
                                                    </span>
                                                </template>
                                            </div>
                                        </template>

                                        <template x-if="!selectedEmpresa?.perfil_ambiental?.residuos_tipos || getResiduosList(selectedEmpresa.perfil_ambiental.residuos_tipos).length === 0">
                                            <p class="text-gray-400 italic text-[11px]">No se declararon tipos de residuos generados.</p>
                                        </template>
                                    </div>
                                </div>
                            </template>

                            <!-- Seccion 3: Documentos Subidos -->
                            <div class="space-y-3">
                                <h4 class="text-xs font-extrabold text-gray-800 uppercase tracking-wider border-b border-gray-200 pb-1">Documentos Consignados</h4>
                                
                                <template x-if="selectedEmpresa.documentos_expediente && selectedEmpresa.documentos_expediente.length > 0">
                                    <div class="grid grid-cols-1 gap-2">
                                        <template x-for="doc in selectedEmpresa.documentos_expediente" :key="doc.id">
                                            <div class="p-3 bg-gray-50 rounded-xl border border-gray-200 flex justify-between items-center text-xs">
                                                <div>
                                                    <span class="font-bold text-gray-800 block" x-text="doc.nombre_requisito"></span>
                                                    <span class="text-[10px] text-gray-400 font-mono" x-text="'CÓDIGO: ' + doc.codigo_requisito"></span>
                                                </div>
                                                <a 
                                                    :href="'/ciudadano/empresas/documentos/' + doc.id" 
                                                    target="_blank"
                                                    class="px-3 py-1.5 bg-white border border-gray-300 hover:border-[#0d5238] hover:text-[#0d5238] rounded-lg text-xs font-bold transition-all flex items-center gap-1 shadow-xs cursor-pointer"
                                                >
                                                    📄 Ver Archivo
                                                </a>
                                            </div>
                                        </template>
                                    </div>
                                </template>

                                <template x-if="!selectedEmpresa.documentos_expediente || selectedEmpresa.documentos_expediente.length === 0">
                                    <p class="text-xs text-gray-400 italic">No se han registrado documentos para esta empresa.</p>
                                </template>
                            </div>

                        </div>

                        <!-- Footer -->
                        <div class="bg-gray-50 p-4 rounded-b-3xl flex justify-end border-t border-gray-100">
                            <button 
                                @click="openDetailModal = false" 
                                class="px-5 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 text-xs font-bold rounded-xl transition-all cursor-pointer"
                            >
                                Cerrar
                            </button>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>
</div>
</x-layouts.app>