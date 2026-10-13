<x-layouts.app title="Poder Popular - Despacho" active="alcaldesa_poder_popular">
    <div class="space-y-6" x-data="{ 
        modalOpen: false, 
        currentRecord: {} 
    }">
        
        <!-- Cabecera -->
        <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-xs flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <span class="px-2.5 py-1 bg-amber-50 text-amber-700 text-[10px] font-bold uppercase tracking-wider rounded-lg">Poder Popular y Comunas</span>
                <h1 class="text-2xl font-bold text-slate-800 mt-1">Control de Reportes y Solicitudes Comunitarias</h1>
                <p class="text-xs text-slate-500 mt-0.5">Seguimiento institucional de incidencias y trámites en el Municipio Carrizal.</p>
            </div>
        </div>

        <!-- Sección de Filtros Fluidos (Ocupa el 100% del ancho horizontal) -->
        <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-xs">
            <form method="GET" action="{{ route('alcaldesa.poderPopular') }}" class="flex flex-wrap lg:flex-nowrap items-end gap-3 w-full">
                
                <!-- Tipo de Registro -->
                <div class="w-full sm:w-auto flex-1 min-w-[130px]">
                    <label class="block text-[11px] font-bold text-slate-600 mb-1">Tipo de Registro</label>
                    <select name="tipo" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:bg-white transition cursor-pointer">
                        <option value="denuncia" {{ $tipoRegistro === 'denuncia' ? 'selected' : '' }}>Denuncias</option>
                        <option value="solicitud" {{ $tipoRegistro === 'solicitud' ? 'selected' : '' }}>Solicitudes</option>
                    </select>
                </div>

                <!-- Código -->
                <div class="w-full sm:w-auto flex-1 min-w-[120px]">
                    <label class="block text-[11px] font-bold text-slate-600 mb-1">Código</label>
                    <input type="text" name="codigo" value="{{ request('codigo') }}" placeholder="Ej. 2026-..." class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:bg-white transition">
                </div>

                <!-- Comuna (Mayor proporción en la barra) -->
                <div class="w-full sm:w-auto flex-[1.5] min-w-[180px]">
                    <label class="block text-[11px] font-bold text-slate-600 mb-1">Comuna</label>
                    <select name="comuna" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:bg-white transition cursor-pointer">
                        <option value="">Todas las Comunas</option>
                        @foreach($comunasList as $comunaName)
                            <option value="{{ $comunaName }}" {{ request('comuna') === $comunaName ? 'selected' : '' }}>{{ $comunaName }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Fecha Inicio -->
                <div class="w-full sm:w-auto flex-1 min-w-[135px]">
                    <label class="block text-[11px] font-bold text-slate-600 mb-1">Desde</label>
                    <input type="date" name="fecha_inicio" value="{{ request('fecha_inicio') }}" class="w-full px-2.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:bg-white transition">
                </div>

                <!-- Fecha Fin -->
                <div class="w-full sm:w-auto flex-1 min-w-[135px]">
                    <label class="block text-[11px] font-bold text-slate-600 mb-1">Hasta</label>
                    <input type="date" name="fecha_fin" value="{{ request('fecha_fin') }}" class="w-full px-2.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:bg-white transition">
                </div>

                <!-- Botones de Acción -->
                <div class="flex items-center gap-1.5 shrink-0">
                    <button type="submit" title="Aplicar Filtros" class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl transition shadow-xs cursor-pointer flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </button>
                    <a href="{{ route('alcaldesa.poderPopular') }}" title="Limpiar Búsqueda" class="py-2.5 px-3 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold text-xs rounded-xl transition flex items-center justify-center">
                        ✕
                    </a>
                </div>

            </form>
        </div>

        <!-- Tabla de Registros -->
        <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-xs space-y-4">
            <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider">
                Resultados de {{ ucfirst($tipoRegistro) }}s
            </h3>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-slate-200 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                            <th class="py-3 px-4">Código / Expediente</th>
                            <th class="py-3 px-4">Comuna / Sector</th>
                            <th class="py-3 px-4">Motivo / Tipo</th>
                            <th class="py-3 px-4">Fechas (Emisión / Cierre)</th>
                            <th class="py-3 px-4">Estatus</th>
                            <th class="py-3 px-4 text-right">Acción</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-xs text-slate-700">
                        @forelse($registros as $item)
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="py-3.5 px-4 font-mono font-bold text-indigo-600">
                                    {{ $tipoRegistro === 'solicitud' ? $item->codigo_expediente : $item->codigo }}
                                </td>
                                <td class="py-3.5 px-4">
                                    <span class="font-bold block text-slate-800">{{ $item->comuna }}</span>
                                    <span class="text-[11px] text-slate-400">Sector: {{ $item->sector ?? 'N/D' }}</span>
                                </td>
                                <td class="py-3.5 px-4 font-medium">
                                    {{ $tipoRegistro === 'solicitud' ? ucfirst($item->tipo_tramite) : $item->tipo_contaminacion }}
                                </td>
                                <td class="py-3.5 px-4">
                                    <span class="block text-slate-700 font-medium">Emisión: {{ $item->created_at?->format('d/m/Y') }}</span>
                                    @if($item->fecha_cierre)
                                        <span class="text-[11px] text-emerald-600 font-bold">Cierre: {{ \Carbon\Carbon::parse($item->fecha_cierre)->format('d/m/Y') }}</span>
                                    @else
                                        <span class="text-[11px] text-amber-500 font-medium">En proceso</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4">
                                    <span class="px-2 py-0.5 text-[10px] font-bold rounded-md uppercase bg-slate-100 text-slate-700">
                                        {{ $tipoRegistro === 'solicitud' ? $item->estado : $item->estatus }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-right">
                                    <button @click="currentRecord = {{ json_encode($item) }}; modalOpen = true;" class="px-3 py-1.5 bg-indigo-50 hover:bg-indigo-600 hover:text-white text-indigo-700 text-xs font-bold rounded-xl transition cursor-pointer">
                                        Ver Detalles
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-slate-400">No se encontraron registros bajo los filtros seleccionados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="pt-2">
                {{ $registros->links() }}
            </div>
        </div>

        <!-- Modal de Detalles y Evidencias -->
        <div x-show="modalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/60 backdrop-blur-sm p-4">
            <div @click.away="modalOpen = false" class="bg-white rounded-3xl max-w-2xl w-full p-6 sm:p-8 space-y-6 shadow-2xl border border-slate-200 overflow-y-auto max-h-[90vh]">
                
                <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                    <div>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-indigo-600 bg-indigo-50 px-2.5 py-1 rounded-lg">Detalle del Expediente</span>
                        <h3 class="text-xl font-bold text-slate-800 mt-1" x-text="currentRecord.codigo || currentRecord.codigo_expediente"></h3>
                    </div>
                    <button @click="modalOpen = false" class="h-8 w-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center font-bold">✕</button>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                    <div class="p-4 rounded-2xl bg-slate-50 space-y-1">
                        <p class="font-bold text-slate-400 uppercase tracking-wider text-[10px]">Ubicación Comunal</p>
                        <p class="font-bold text-slate-800" x-text="'Comuna: ' + (currentRecord.comuna || 'N/D')"></p>
                        <p class="text-slate-600" x-text="'Sector: ' + (currentRecord.sector || currentRecord.direccion_exacta || 'N/D')"></p>
                    </div>

                    <div class="p-4 rounded-2xl bg-slate-50 space-y-1">
                        <p class="font-bold text-slate-400 uppercase tracking-wider text-[10px]">Ciudadano / Solicitante</p>
                        <p class="font-bold text-slate-800" x-text="currentRecord.user ? (currentRecord.user.nombre + ' ' + currentRecord.user.apellido) : 'N/D'"></p>
                        <p class="text-slate-600" x-text="currentRecord.user ? currentRecord.user.correo : ''"></p>
                    </div>
                </div>

                <div class="space-y-2">
                    <p class="text-xs font-bold text-slate-700 uppercase tracking-wider">Descripción del Caso</p>
                    <p class="text-xs text-slate-600 bg-slate-50 p-4 rounded-2xl border border-slate-100" x-text="currentRecord.descripcion || currentRecord.motivo_descripcion"></p>
                </div>

                <!-- Sección de Evidencias Fotográficas o Adjuntos -->
                <div class="space-y-2">
                    <p class="text-xs font-bold text-slate-700 uppercase tracking-wider">Evidencias Fotográficas / Archivos</p>
                    
                    <!-- Si es Solicitud con Recaudos -->
                    <template x-if="currentRecord.recaudos && currentRecord.recaudos.length > 0">
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                            <template x-for="recaudo in currentRecord.recaudos">
                                <a :href="recaudo.archivo_url" target="_blank" class="block border border-slate-200 rounded-xl overflow-hidden hover:opacity-90 transition">
                                    <img :src="recaudo.archivo_url" class="h-24 w-full object-cover" alt="Evidencia">
                                    <p class="p-2 text-[10px] font-bold text-slate-600 truncate" x-text="recaudo.nombre_archivo"></p>
                                </a>
                            </template>
                        </div>
                    </template>

                    <!-- Si es Denuncia con Evidencias JSON -->
                    <template x-if="currentRecord.evidencias && Array.isArray(currentRecord.evidencias) && currentRecord.evidencias.length > 0">
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                            <template x-for="evidencia in currentRecord.evidencias">
                                <a :href="evidencia" target="_blank" class="block border border-slate-200 rounded-xl overflow-hidden hover:opacity-90 transition">
                                    <img :src="evidencia" class="h-24 w-full object-cover" alt="Evidencia Denuncia">
                                </a>
                            </template>
                        </div>
                    </template>

                    <template x-if="(!currentRecord.recaudos || currentRecord.recaudos.length === 0) && (!currentRecord.evidencias || currentRecord.evidencias.length === 0)">
                        <p class="text-xs text-slate-400 italic">No hay archivos o evidencias fotográficas adjuntas en este expediente.</p>
                    </template>
                </div>

                <div class="pt-4 border-t border-slate-100 flex justify-end">
                    <button @click="modalOpen = false" class="px-5 py-2.5 bg-slate-900 text-white font-bold text-xs rounded-xl hover:bg-slate-800 transition cursor-pointer">
                        Cerrar Ventana
                    </button>
                </div>

            </div>
        </div>

    </div>
</x-layouts.app>