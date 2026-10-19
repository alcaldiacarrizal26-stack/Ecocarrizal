<x-layouts.app title="Manejo de Residuos - Despacho" active="alcaldesa_residuos">
    <div class="space-y-6" x-data="{ 
        modalOpen: false, 
        currentRecord: {} 
    }">
        
        <!-- Cabecera Institucional y Botón de Reporte PDF -->
        <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-xs flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <span class="px-2.5 py-1 bg-indigo-50 text-indigo-700 text-[10px] font-bold uppercase tracking-wider rounded-lg">Dirección de Residuos</span>
                <h1 class="text-2xl font-bold text-slate-800 mt-1">Control Comercial RACDA y Declaraciones</h1>
                <p class="text-xs text-slate-500 mt-0.5">Seguimiento de empresas registradas, perfiles ambientales y balance de toneladas de desechos.</p>
            </div>

            <!-- Botón de Impresión de Reporte -->
            <button type="button" onclick="imprimirInformeEnMismaVista('{{ route('alcaldesa.residuos.pdf') }}')" class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold rounded-xl shadow-md transition-all shrink-0 cursor-pointer">
                <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                <span>Imprimir / Ver Informe PDF</span>
            </button>
        </div>

        <!-- Tarjetas de Resumen Ejecutivo -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-xs space-y-2">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Total Empresas RACDA</span>
                <div class="flex items-baseline justify-between">
                    <h3 class="text-2xl font-black text-slate-800">{{ $totalEmpresas }}</h3>
                    <span class="text-xs font-bold text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-lg">Registradas</span>
                </div>
                <p class="text-[11px] text-slate-500">Comercios e industrias supervisados.</p>
            </div>

            <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-xs space-y-2">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Toneladas Comunes</span>
                <div class="flex items-baseline justify-between">
                    <h3 class="text-2xl font-black text-slate-800">{{ number_format($totalesPorCategoria['comunes'], 2) }}</h3>
                    <span class="text-xs font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-lg">Ton</span>
                </div>
                <p class="text-[11px] text-slate-500">Acumulado de declaraciones.</p>
            </div>

            <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-xs space-y-2">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Toneladas Reciclables</span>
                <div class="flex items-baseline justify-between">
                    <h3 class="text-2xl font-black text-slate-800">{{ number_format($totalesPorCategoria['reciclables'], 2) }}</h3>
                    <span class="text-xs font-bold text-amber-600 bg-amber-50 px-2 py-0.5 rounded-lg">Ton</span>
                </div>
                <p class="text-[11px] text-slate-500">Valorización y aprovechamiento.</p>
            </div>

            <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-xs space-y-2">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Toneladas Peligrosas</span>
                <div class="flex items-baseline justify-between">
                    <h3 class="text-2xl font-black text-slate-800">{{ number_format($totalesPorCategoria['peligrosas'], 2) }}</h3>
                    <span class="text-xs font-bold text-rose-600 bg-rose-50 px-2 py-0.5 rounded-lg">Ton</span>
                </div>
                <p class="text-[11px] text-slate-500">Control especial fiscalizado.</p>
            </div>
        </div>

        <!-- Barra de Filtros (Incluyendo Lapso Semanal) -->
        <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-xs">
            <form method="GET" action="{{ route('alcaldesa.residuos') }}" id="filtroForm" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3 items-end w-full">
                
                <!-- Búsqueda -->
                <div>
                    <label class="block text-[11px] font-bold text-slate-600 mb-1">Buscar Empresa / RIF</label>
                    <input type="text" name="buscar" value="{{ request('buscar') }}" placeholder="Nombre o RIF..." class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:bg-white transition">
                </div>

                <!-- Fecha Inicio Semana -->
                <div>
                    <label class="block text-[11px] font-bold text-slate-600 mb-1">Desde (Inicio Lapso)</label>
                    <input type="date" name="fecha_inicio" value="{{ $fechaInicio ?? request('fecha_inicio') }}" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:bg-white transition">
                </div>

                <!-- Fecha Fin Semana -->
                <div>
                    <label class="block text-[11px] font-bold text-slate-600 mb-1">Hasta (Cierre Lapso)</label>
                    <input type="date" name="fecha_fin" value="{{ $fechaFin ?? request('fecha_fin') }}" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:bg-white transition">
                </div>

                <!-- Comuna -->
                <div>
                    <label class="block text-[11px] font-bold text-slate-600 mb-1">Comuna</label>
                    <select name="comuna" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:bg-white transition cursor-pointer">
                        <option value="">Todas las comunas</option>
                        @foreach($comunasDisponibles ?? [] as $comuna)
                            <option value="{{ $comuna }}" {{ request('comuna') === $comuna ? 'selected' : '' }}>{{ $comuna }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Rol -->
                <div>
                    <label class="block text-[11px] font-bold text-slate-600 mb-1">Rol Ambiental</label>
                    <select name="rol" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:bg-white transition cursor-pointer">
                        <option value="">Todos los roles</option>
                        <option value="GENERADOR" {{ request('rol') === 'GENERADOR' ? 'selected' : '' }}>Generador</option>
                        <option value="MANEJADOR" {{ request('rol') === 'MANEJADOR' ? 'selected' : '' }}>Manejador</option>
                        <option value="NO_APLICA" {{ request('rol') === 'NO_APLICA' ? 'selected' : '' }}>No Aplica</option>
                    </select>
                </div>

                <!-- Botones de Acción -->
                <div class="flex items-center gap-1.5">
                    <button type="submit" class="flex-1 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl transition shadow-xs cursor-pointer flex items-center justify-center gap-1">
                        <span>Filtrar</span>
                    </button>
                    <a href="{{ route('alcaldesa.residuos') }}" class="py-2.5 px-3 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold text-xs rounded-xl transition flex items-center justify-center">
                        ✕
                    </a>
                </div>

            </form>
        </div>

        <!-- Tabla de Empresas Registradas -->
        <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-xs space-y-4">
            <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider">Directorio de Empresas y Establecimientos</h3>

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
                                    <button @click="currentRecord = {{ json_encode($empresa->load(['user', 'perfilAmbiental', 'documentosExpediente'])) }}; modalOpen = true;" class="px-3 py-1.5 bg-indigo-50 hover:bg-indigo-600 hover:text-white text-indigo-700 text-xs font-bold rounded-xl transition cursor-pointer">
                                        Ver Perfil
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-slate-400">No se encontraron empresas registradas con los filtros aplicados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="pt-2">
                {{ $empresas->links() }}
            </div>
        </div>

        <!-- 3. Modal de Detalles Mejorada con Documentos Asociados -->
        <div x-show="modalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/60 backdrop-blur-sm p-4">
            <div @click.away="modalOpen = false" class="bg-white rounded-3xl max-w-3xl w-full p-6 sm:p-8 space-y-6 shadow-2xl border border-slate-200 overflow-y-auto max-h-[90vh]">
                
                <!-- Cabecera de la Modal -->
                <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                    <div>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-indigo-600 bg-indigo-50 px-2.5 py-1 rounded-lg">Expediente Comercial y Ambiental</span>
                        <h3 class="text-xl font-black text-slate-800 mt-1" x-text="currentRecord.razon_social"></h3>
                        <p class="text-xs text-slate-400 font-mono" x-text="'RIF: ' + currentRecord.rif"></p>
                    </div>
                    <button @click="modalOpen = false" class="h-8 w-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center font-bold">✕</button>
                </div>

                <!-- Información General en Cuadrícula -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 space-y-1.5">
                        <p class="font-extrabold text-slate-400 uppercase tracking-wider text-[10px]">Datos de Contacto y Fiscales</p>
                        <p class="text-slate-700"><strong class="text-slate-900">Patente N°:</strong> <span x-text="currentRecord.numero_cuenta_patente || 'N/D'"></span></p>
                        <p class="text-slate-700"><strong class="text-slate-900">Correo:</strong> <span x-text="currentRecord.correo_comercial"></span></p>
                        <p class="text-slate-700"><strong class="text-slate-900">Teléfono:</strong> <span x-text="currentRecord.telefono_comercial || 'N/D'"></span></p>
                    </div>

                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 space-y-1.5">
                        <p class="font-extrabold text-slate-400 uppercase tracking-wider text-[10px]">Ubicación Territorial</p>
                        <p class="text-slate-700"><strong class="text-slate-900">Comuna:</strong> <span x-text="currentRecord.comuna"></span></p>
                        <p class="text-slate-700"><strong class="text-slate-900">Sector:</strong> <span x-text="currentRecord.sector"></span></p>
                        <p class="text-slate-700"><strong class="text-slate-900">Dirección:</strong> <span x-text="(currentRecord.calle_avenida || '') + ' ' + (currentRecord.edificio_galpon_local || '')"></span></p>
                    </div>
                </div>

                <!-- Clasificación RACDA -->
                <div class="p-4 rounded-2xl bg-indigo-50/60 border border-indigo-100 space-y-2 text-xs">
                    <p class="font-extrabold text-indigo-900 uppercase tracking-wider text-[10px]">Perfil Ambiental y RACDA</p>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 text-indigo-950 font-medium">
                        <p><strong class="font-bold">Rol:</strong> <span x-text="currentRecord.perfil_ambiental?.rol_ambiental || 'NO ASIGNADO'"></span></p>
                        <p><strong class="font-bold">Subtipo:</strong> <span x-text="currentRecord.perfil_ambiental?.subtipo_generador || 'N/A'"></span></p>
                        <p><strong class="font-bold">Nro. RACDA:</strong> <span x-text="currentRecord.perfil_ambiental?.numero_racda || 'Pendiente'"></span></p>
                    </div>
                </div>

                <!-- Documentos Asociados al Expediente -->
                <div class="space-y-2.5">
                    <p class="font-extrabold text-slate-700 uppercase tracking-wider text-[11px]">Documentos y Recaudos Consignados</p>
                    
                    <template x-if="currentRecord.documentos_expediente && currentRecord.documentos_expediente.length > 0">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                            <template x-for="doc in currentRecord.documentos_expediente" :key="doc.id">
                                <div class="p-3 rounded-xl border border-slate-200 bg-white flex flex-col justify-between gap-2 shadow-2xs">
                                    <div class="flex items-start justify-between gap-2">
                                        <div class="truncate">
                                            <!-- Nombre del requisito -->
                                            <p class="text-xs font-bold text-slate-800 truncate" x-text="doc.nombre_requisito || 'Documento Oficial'"></p>
                                            
                                            <!-- Indicador de Vigencia / Vencimiento / Vitalicio -->
                                            <div class="mt-1 flex items-center gap-1.5 flex-wrap">
                                                <template x-if="doc.es_vitalicio">
                                                    <span class="px-2 py-0.5 text-[9px] font-extrabold uppercase bg-emerald-100 text-emerald-800 rounded-md tracking-wider border border-emerald-200">
                                                        Vitalicio
                                                    </span>
                                                </template>
                                                <template x-if="!doc.es_vitalicio && doc.fecha_vencimiento">
                                                    <span class="text-[10px] text-slate-600 font-mono font-medium bg-slate-100 px-2 py-0.5 rounded-md">
                                                        Vence: <strong x-text="new Date(doc.fecha_vencimiento).toLocaleDateString()"></strong>
                                                    </span>
                                                </template>
                                                <template x-if="!doc.es_vitalicio && !doc.fecha_vencimiento">
                                                    <span class="text-[10px] text-amber-600 italic">Sin fecha especificada</span>
                                                </template>
                                            </div>
                                        </div>

                                        <!-- Botón de Acceso -->
                                        <a :href="'/expedientes/documentos/' + doc.id" target="_blank" class="px-2.5 py-1.5 bg-slate-100 hover:bg-slate-900 hover:text-white text-slate-700 rounded-lg text-[11px] font-bold transition shrink-0">
                                            Ver Archivo
                                        </a>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </template>

                    <template x-if="!currentRecord.documentos_expediente || currentRecord.documentos_expediente.length === 0">
                        <div class="p-4 rounded-xl border border-dashed border-slate-200 text-center text-slate-400 text-xs italic">
                            No hay documentos digitales adjuntos en este expediente.
                        </div>
                    </template>
                </div>

                <!-- Pie de Modal -->
                <div class="pt-4 border-t border-slate-100 flex justify-end">
                    <button @click="modalOpen = false" class="px-5 py-2.5 bg-slate-900 text-white font-bold text-xs rounded-xl hover:bg-slate-800 transition cursor-pointer">
                        Cerrar Ventana
                    </button>
                </div>

            </div>
        </div>

    </div>

    <!-- Script e Iframe oculto para impresión en la misma vista -->
    <iframe id="pdfPrintFrame" style="display: none;"></iframe>
    <script>
        function imprimirInformeEnMismaVista(url) {
            const frame = document.getElementById('pdfPrintFrame');
            frame.src = url;
            frame.onload = function() {
                frame.contentWindow.focus();
                frame.contentWindow.print();
            };
        }
    </script>
</x-layouts.app>