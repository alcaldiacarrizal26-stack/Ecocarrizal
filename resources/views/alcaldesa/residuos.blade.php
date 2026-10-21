<x-layouts.app title="Manejo de Residuos - Despacho" active="alcaldesa_residuos">
    <div class="space-y-6" x-data="{ 
        activeTab: '{{ request('tab', 'declaraciones') }}',
        modalOpen: false, 
        currentRecord: {} 
    }">
        
        <!-- Cabecera Principal y Navegación de Bloques -->
        <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <span class="px-2.5 py-1 bg-indigo-50 text-indigo-700 text-[10px] font-bold uppercase tracking-wider rounded-lg">Dirección de Residuos</span>
                <h1 class="text-2xl font-bold text-slate-800 mt-1">Gestión Ambiental y Residuos Sólidos</h1>
                <p class="text-xs text-slate-500 mt-0.5">Control de fiscalización comercial RACDA y monitoreo del balance semanal de desechos.</p>
            </div>

            <!-- Botones de Acción según la Pestaña Activa -->
            <div class="flex items-center gap-2">
                <!-- Botón PDF para Balance de Residuos -->
                <button type="button" x-show="activeTab === 'declaraciones'" onclick="imprimirConFechas()" class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold rounded-xl shadow-md transition-all cursor-pointer">
                    <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    <span>Imprimir Reporte Semanal</span>
                </button>

                <!-- Botón PDF con idéntica estética para el Directorio de Establecimientos RACDA -->
                <button type="button" x-show="activeTab === 'empresas'" onclick="imprimirDirectorioEmpresas()" class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold rounded-xl shadow-md transition-all cursor-pointer">
                    <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    <span>Imprimir Directorio de Empresas</span>
                </button>
            </div>
        </div>

        <!-- Selector de Bloques / Pestañas -->
        <div class="flex items-center gap-2 border-b border-slate-200 pb-1">
            <button @click="activeTab = 'declaraciones'" 
                :class="activeTab === 'declaraciones' ? 'bg-indigo-600 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'"
                class="px-5 py-2.5 rounded-2xl text-xs font-bold transition flex items-center gap-2 cursor-pointer">
                <span>📊 Balance de Residuos (Semanal)</span>
            </button>

            <button @click="activeTab = 'empresas'" 
                :class="activeTab === 'empresas' ? 'bg-indigo-600 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'"
                class="px-5 py-2.5 rounded-2xl text-xs font-bold transition flex items-center gap-2 cursor-pointer">
                <span>🏢 Establecimientos RACDA</span>
            </button>
        </div>

        <!-- ========================================================= -->
        <!-- BLOQUE 1: RESIDUOS DECLARADOS EN LA SEMANA X              -->
        <!-- ========================================================= -->
        <div x-show="activeTab === 'declaraciones'" x-cloak class="space-y-6">
            
            <!-- Selector de Rango Semanal con Contraste Corregido -->
            <div class="bg-slate-900 text-white rounded-3xl p-6 shadow-md flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4">
                <div>
                    <span class="px-2 py-1 bg-emerald-500/20 text-emerald-400 text-[10px] font-extrabold uppercase tracking-widest rounded-md">Filtro de Fiscalización</span>
                    <h2 class="text-lg font-black mt-1 text-white">Declaraciones del Lapso Seleccionado</h2>
                    <p class="text-xs text-slate-300 mt-0.5">Consolidado acumulado de volumen en toneladas reportado por comercios e industrias.</p>
                </div>

                <form method="GET" action="{{ route('alcaldesa.residuos') }}" id="filtroFechaForm" class="flex flex-wrap items-center gap-3">
                    <input type="hidden" name="tab" value="declaraciones">
                    <div class="flex items-center gap-2 bg-slate-800 p-2 rounded-2xl border border-slate-700">
                        <div>
                            <label class="block text-[9px] font-bold text-slate-400 uppercase px-1">Desde</label>
                            <input type="date" name="fecha_inicio" value="{{ $fechaInicio }}" class="bg-transparent text-xs font-bold text-white px-2 py-1 outline-none">
                        </div>
                        <span class="text-slate-500 font-bold">→</span>
                        <div>
                            <label class="block text-[9px] font-bold text-slate-400 uppercase px-1">Hasta</label>
                            <input type="date" name="fecha_fin" value="{{ $fechaFin }}" class="bg-transparent text-xs font-bold text-white px-2 py-1 outline-none">
                        </div>
                    </div>

                    <button type="submit" class="px-4 py-3 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl transition shadow-sm cursor-pointer">
                        Actualizar Lapso
                    </button>
                </form>
            </div>

            <!-- Tarjetas de Resumen de Toneladas del Lapso -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-xs space-y-2 border-l-4 border-l-emerald-500">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Toneladas Comunes</span>
                    <div class="flex items-baseline justify-between">
                        <h3 class="text-3xl font-black text-slate-800">{{ number_format($totalesPorCategoria['comunes'], 2) }}</h3>
                        <span class="text-xs font-bold text-emerald-600 bg-emerald-50 px-2.5 py-1 rounded-lg">Ton</span>
                    </div>
                    <p class="text-[11px] text-slate-500">Desechos sólidos no peligrosos.</p>
                </div>

                <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-xs space-y-2 border-l-4 border-l-amber-500">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Toneladas Reciclables</span>
                    <div class="flex items-baseline justify-between">
                        <h3 class="text-3xl font-black text-slate-800">{{ number_format($totalesPorCategoria['reciclables'], 2) }}</h3>
                        <span class="text-xs font-bold text-amber-600 bg-amber-50 px-2.5 py-1 rounded-lg">Ton</span>
                    </div>
                    <p class="text-[11px] text-slate-500">Aprovechamiento y economía circular.</p>
                </div>

                <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-xs space-y-2 border-l-4 border-l-rose-500">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Toneladas Peligrosas</span>
                    <div class="flex items-baseline justify-between">
                        <h3 class="text-3xl font-black text-slate-800">{{ number_format($totalesPorCategoria['peligrosas'], 2) }}</h3>
                        <span class="text-xs font-bold text-rose-600 bg-rose-50 px-2.5 py-1 rounded-lg">Ton</span>
                    </div>
                    <p class="text-[11px] text-slate-500">Residuos fiscalizados de manejo especial.</p>
                </div>
            </div>

            <!-- Tabla de Declaraciones Detalladas con Filtro por Rol -->
            <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-xs space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider">Detalle de Declaraciones Recibidas en el Lapso</h3>

                    <form method="GET" action="{{ route('alcaldesa.residuos') }}" class="flex items-center gap-2">
                        <input type="hidden" name="tab" value="declaraciones">
                        <input type="hidden" name="fecha_inicio" value="{{ $fechaInicio }}">
                        <input type="hidden" name="fecha_fin" value="{{ $fechaFin }}">
                        <select name="rol_declarante" onchange="this.form.submit()" class="px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-700 cursor-pointer">
                            <option value="">Filtrar por Rol (Todos)</option>
                            <option value="GENERADOR" {{ request('rol_declarante') === 'GENERADOR' ? 'selected' : '' }}>Generador</option>
                            <option value="MANEJADOR" {{ request('rol_declarante') === 'MANEJADOR' ? 'selected' : '' }}>Manejador</option>
                        </select>
                    </form>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-slate-200 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                                <th class="py-3 px-4">Empresa / RIF</th>
                                <th class="py-3 px-4">Rol</th>
                                <th class="py-3 px-4 text-right">Comunes (Ton)</th>
                                <th class="py-3 px-4 text-right">Reciclables (Ton)</th>
                                <th class="py-3 px-4 text-right">Peligrosas (Ton)</th>
                                <th class="py-3 px-4 text-center">Fecha Declaración</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-xs text-slate-700">
                            @forelse($declaraciones as $dec)
                                <tr class="hover:bg-slate-50/50 transition">
                                    <td class="py-3.5 px-4">
                                        <span class="font-bold block text-slate-800">{{ $dec->empresa?->razon_social ?? 'Empresa No Encontrada' }}</span>
                                        <span class="font-mono text-[11px] text-indigo-600 font-bold">RIF: {{ $dec->empresa?->rif }}</span>
                                    </td>
                                    <td class="py-3.5 px-4 font-bold uppercase text-slate-600">
                                        {{ $dec->rol_declarante }}
                                    </td>
                                    <td class="py-3.5 px-4 text-right font-mono text-emerald-700 font-bold">
                                        {{ number_format($dec->toneladas_comunes, 3) }} Ton
                                    </td>
                                    <td class="py-3.5 px-4 text-right font-mono text-amber-700 font-bold">
                                        {{ number_format($dec->toneladas_reciclables, 3) }} Ton
                                    </td>
                                    <td class="py-3.5 px-4 text-right font-mono text-rose-700 font-bold">
                                        {{ number_format($dec->toneladas_peligrosas, 3) }} Ton
                                    </td>
                                    <td class="py-3.5 px-4 text-center font-mono text-slate-500">
                                        {{ $dec->created_at?->format('d/m/Y H:i') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-8 text-center text-slate-400">No se registran declaraciones de residuos para el lapso de fechas seleccionado.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="pt-2">
                    {{ $declaraciones->appends(['tab' => 'declaraciones'])->links() }}
                </div>
            </div>

        </div>

        <!-- ========================================================= -->
        <!-- BLOQUE 2: LISTADO DE EMPRESAS Y FISCALIZACIÓN             -->
        <!-- ========================================================= -->
        <div x-show="activeTab === 'empresas'" x-cloak class="space-y-6">
            
            <!-- Barra de Filtros del Directorio -->
            <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-xs">
                <form method="GET" action="{{ route('alcaldesa.residuos') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 items-end w-full">
                    <input type="hidden" name="tab" value="empresas">

                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 mb-1">Buscar Empresa / RIF</label>
                        <input type="text" name="buscar" value="{{ request('buscar') }}" placeholder="Nombre o RIF..." class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:bg-white transition">
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 mb-1">Comuna</label>
                        <select name="comuna" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:bg-white transition cursor-pointer">
                            <option value="">Todas las comunas</option>
                            @foreach($comunasDisponibles as $comuna)
                                <option value="{{ $comuna }}" {{ request('comuna') === $comuna ? 'selected' : '' }}>{{ $comuna }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 mb-1">Rol Ambiental</label>
                        <select name="rol" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:bg-white transition cursor-pointer">
                            <option value="">Todos los roles</option>
                            <option value="GENERADOR" {{ request('rol') === 'GENERADOR' ? 'selected' : '' }}>Generador</option>
                            <option value="MANEJADOR" {{ request('rol') === 'MANEJADOR' ? 'selected' : '' }}>Manejador</option>
                            <option value="NO_APLICA" {{ request('rol') === 'NO_APLICA' ? 'selected' : '' }}>No Aplica</option>
                        </select>
                    </div>

                    <div class="flex items-center gap-1.5">
                        <button type="submit" class="flex-1 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl transition shadow-xs cursor-pointer flex items-center justify-center gap-1">
                            <span>Filtrar Empresas</span>
                        </button>
                        <a href="{{ route('alcaldesa.residuos', ['tab' => 'empresas']) }}" class="py-2.5 px-3 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold text-xs rounded-xl transition flex items-center justify-center">✕</a>
                    </div>
                </form>
            </div>

            <!-- Tabla de Empresas Registradas -->
            <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-xs space-y-4">
                <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider">Directorio de Establecimientos Registrados ({{ $totalEmpresas }})</h3>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-slate-200 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                                <th class="py-3 px-4">RIF / Razón Social</th>
                                <th class="py-3 px-4">Comuna / Sector</th>
                                <th class="py-3 px-4">Rol RACDA</th>
                                <th class="py-3 px-4">Estatus</th>
                                <th class="py-3 px-4 text-right">Acción</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-xs text-slate-700">
                            @forelse($empresas as $empresa)
                                <tr class="hover:bg-slate-50/50 transition">
                                    <td class="py-3.5 px-4">
                                        <span class="font-bold block text-slate-800">{{ $empresa->razon_social }}</span>
                                        <span class="font-mono text-[11px] text-indigo-600 font-bold">RIF: {{ $empresa->rif }}</span>
                                    </td>
                                    <td class="py-3.5 px-4">
                                        <span class="font-bold block text-slate-800">{{ $empresa->comuna }}</span>
                                        <span class="text-[11px] text-slate-400">Sector: {{ $empresa->sector }}</span>
                                    </td>
                                    <td class="py-3.5 px-4 font-medium uppercase text-slate-700">
                                        {{ $empresa->perfilAmbiental?->rol_ambiental ?? 'NO REGISTRADO' }}
                                    </td>
                                    <td class="py-3.5 px-4">
                                        <span class="px-2.5 py-1 text-[10px] font-bold rounded-md uppercase {{ $empresa->estatus === 'APROBADO' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                            {{ $empresa->estatus }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-4 text-right">
                                        <button @click="currentRecord = {{ json_encode($empresa->load(['user', 'perfilAmbiental', 'documentosExpediente', 'declaracionesResiduos' => fn($q) => $q->latest(), 'sanciones' => fn($q) => $q->where('estatus', '!=', 'SUBSANADA')])) }}; modalOpen = true;" class="px-3 py-1.5 bg-indigo-50 hover:bg-indigo-600 hover:text-white text-indigo-700 text-xs font-bold rounded-xl transition cursor-pointer">
                                            Ver Perfil
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-8 text-center text-slate-400">No se encontraron empresas registradas.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="pt-2">
                    {{ $empresas->appends(['tab' => 'empresas'])->links() }}
                </div>
            </div>

        </div>

        <!-- Modal de Detalles del Expediente -->
        <div x-show="modalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/60 backdrop-blur-sm p-4">
            <div @click.away="modalOpen = false" class="bg-white rounded-3xl max-w-3xl w-full p-6 sm:p-8 space-y-6 shadow-2xl border border-slate-200 overflow-y-auto max-h-[90vh]">
                
                <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                    <div>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-indigo-600 bg-indigo-50 px-2.5 py-1 rounded-lg">Expediente Comercial y Ambiental</span>
                        <h3 class="text-xl font-black text-slate-800 mt-1" x-text="currentRecord.razon_social"></h3>
                        <p class="text-xs text-slate-400 font-mono" x-text="'RIF: ' + currentRecord.rif"></p>
                    </div>
                    <button @click="modalOpen = false" class="h-8 w-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center font-bold">✕</button>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 space-y-1.5">
                        <p class="font-extrabold text-slate-400 uppercase tracking-wider text-[10px]">Datos Fiscales</p>
                        <p class="text-slate-700"><strong class="text-slate-900">Patente N°:</strong> <span x-text="currentRecord.numero_cuenta_patente || 'N/D'"></span></p>
                        <p class="text-slate-700"><strong class="text-slate-900">Correo:</strong> <span x-text="currentRecord.correo_comercial"></span></p>
                        <p class="text-slate-700"><strong class="text-slate-900">Teléfono:</strong> <span x-text="currentRecord.telefono_comercial || 'N/D'"></span></p>
                    </div>

                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 space-y-1.5">
                        <p class="font-extrabold text-slate-400 uppercase tracking-wider text-[10px]">Ubicación Territorial</p>
                        <p class="text-slate-700"><strong class="text-slate-900">Comuna:</strong> <span x-text="currentRecord.comuna"></span></p>
                        <p class="text-slate-700"><strong class="text-slate-900">Sector:</strong> <span x-text="currentRecord.sector"></span></p>
                    </div>
                </div>

                <!-- Resumen Operativo y Legal (Último reporte y Sanciones) -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                    <div class="p-4 rounded-2xl bg-indigo-50/50 border border-indigo-100 space-y-1">
                        <p class="font-extrabold text-indigo-900 uppercase tracking-wider text-[10px]">Última Declaración de Residuos</p>
                        <p class="text-indigo-950 font-medium" x-text="currentRecord.declaraciones_residuos && currentRecord.declaraciones_residuos.length > 0 ? 'Registrada el: ' + new Date(currentRecord.declaraciones_residuos[0].created_at).toLocaleDateString() : 'Sin declaraciones registradas'"></p>
                    </div>

                    <div class="p-4 rounded-2xl bg-rose-50/50 border border-rose-100 space-y-1">
                        <p class="font-extrabold text-rose-900 uppercase tracking-wider text-[10px]">Citaciones y Sanciones Pendientes</p>
                        <template x-if="currentRecord.sanciones && currentRecord.sanciones.length > 0">
                            <span class="px-2.5 py-1 bg-rose-600 text-white font-bold rounded-md text-[10px] inline-block">Tiene expedientes activos de citación</span>
                        </template>
                        <template x-if="!currentRecord.sanciones || currentRecord.sanciones.length === 0">
                            <p class="text-slate-600 font-medium">Sin citaciones ni observaciones pendientes.</p>
                        </template>
                    </div>
                </div>

                <!-- Documentos Consignados -->
                <div class="space-y-2.5">
                    <p class="font-extrabold text-slate-700 uppercase tracking-wider text-[11px]">Documentos y Recaudos Consignados</p>
                    <template x-if="currentRecord.documentos_expediente && currentRecord.documentos_expediente.length > 0">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                            <template x-for="doc in currentRecord.documentos_expediente" :key="doc.id">
                                <div class="p-3 rounded-xl border border-slate-200 bg-white flex flex-col justify-between gap-2 shadow-2xs">
                                    <div class="flex items-start justify-between gap-2">
                                        <div class="truncate">
                                            <p class="text-xs font-bold text-slate-800 truncate" x-text="doc.nombre_requisito || 'Documento Oficial'"></p>
                                            <div class="mt-1 flex items-center gap-1.5 flex-wrap">
                                                <template x-if="doc.es_vitalicio">
                                                    <span class="px-2 py-0.5 text-[9px] font-extrabold uppercase bg-emerald-100 text-emerald-800 rounded-md tracking-wider border border-emerald-200">Vitalicio</span>
                                                </template>
                                                <template x-if="!doc.es_vitalicio && doc.fecha_vencimiento">
                                                    <span class="text-[10px] text-slate-600 font-mono font-medium bg-slate-100 px-2 py-0.5 rounded-md">Vence: <strong x-text="new Date(doc.fecha_vencimiento).toLocaleDateString()"></strong></span>
                                                </template>
                                            </div>
                                        </div>
                                        <a :href="'/expedientes/documentos/' + doc.id" target="_blank" class="px-2.5 py-1.5 bg-slate-100 hover:bg-slate-900 hover:text-white text-slate-700 rounded-lg text-[11px] font-bold transition shrink-0">Ver Archivo</a>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </template>
                </div>

                <div class="pt-4 border-t border-slate-100 flex justify-end">
                    <button @click="modalOpen = false" class="px-5 py-2.5 bg-slate-900 text-white font-bold text-xs rounded-xl hover:bg-slate-800 transition cursor-pointer">Cerrar Ventana</button>
                </div>
            </div>
        </div>

    </div>

    <!-- Script de Impresión -->
    <iframe id="pdfPrintFrame" style="display: none;"></iframe>
    <script>
        function imprimirConFechas() {
            const form = document.getElementById('filtroFechaForm');
            const formData = new FormData(form);
            const params = new URLSearchParams(formData).toString();
            const url = "{{ route('alcaldesa.residuos.pdf') }}?" + params;

            const frame = document.getElementById('pdfPrintFrame');
            frame.src = url;
            frame.onload = function() {
                frame.contentWindow.focus();
                frame.contentWindow.print();
            };
        }

        function imprimirDirectorioEmpresas() {
            const url = "{{ route('alcaldesa.empresas.pdf') }}";
            const frame = document.getElementById('pdfPrintFrame');
            frame.src = url;
            frame.onload = function() {
                frame.contentWindow.focus();
                frame.contentWindow.print();
            };
        }
    </script>
</x-layouts.app>