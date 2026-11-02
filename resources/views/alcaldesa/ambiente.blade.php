<x-layouts.app title="Gestión Ambiental - Despacho" active="alcaldesa_ambiente">
    <div class="space-y-6" x-data="{ 
        modalOpen: false, 
        currentRecord: {} 
    }">
        
        <!-- Cabecera Institucional -->
        <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-xs flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <span class="px-2.5 py-1 bg-emerald-50 text-emerald-700 text-[10px] font-bold uppercase tracking-wider rounded-lg">Dirección de Ambiente</span>
                <h1 class="text-2xl font-bold text-slate-800 mt-1">Control de Trámites y Compensaciones Ambientales</h1>
                <p class="text-xs text-slate-500 mt-0.5">Seguimiento institucional de solicitudes de poda, desmalezamiento, tala y planes de reforestación.</p>
            </div>

            <!-- Botón de Impresión / PDF condicionado a las fechas -->
            @if(request()->filled('fecha_inicio') && request()->filled('fecha_fin'))
                <button type="button" onclick="imprimirAmbientePdf()" class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold rounded-xl shadow-md transition-all shrink-0 cursor-pointer">
                    <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    <span>Imprimir Informe Ambiental</span>
                </button>
            @else
                <button type="button" disabled title="Debe seleccionar un rango de fechas para habilitar el reporte" class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-200 text-slate-400 text-xs font-bold rounded-xl cursor-not-allowed shrink-0">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    <span>Imprimir Informe Ambiental</span>
                </button>
            @endif
        </div>

        <!-- Tarjetas de Resumen Ejecutivo -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-xs space-y-2">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Total Trámites</span>
                <div class="flex items-baseline justify-between">
                    <h3 class="text-2xl font-black text-slate-800">{{ $totalTramites }}</h3>
                    <span class="text-xs font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-lg">Registrados</span>
                </div>
                <p class="text-[11px] text-slate-500">Solicitudes generales procesadas.</p>
            </div>

            <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-xs space-y-2">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Trámites Aprobados</span>
                <div class="flex items-baseline justify-between">
                    <h3 class="text-2xl font-black text-slate-800">{{ $tramitesAprobados }}</h3>
                    <span class="text-xs font-bold text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-lg">Conformes</span>
                </div>
                <p class="text-[11px] text-slate-500">Resoluciones y permisos emitidos.</p>
            </div>

            <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-xs space-y-2">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Compensaciones Pendientes</span>
                <div class="flex items-baseline justify-between">
                    <h3 class="text-2xl font-black text-slate-800">{{ $compensacionesPendientes }}</h3>
                    <span class="text-xs font-bold text-amber-600 bg-amber-50 px-2 py-0.5 rounded-lg">Por entregar</span>
                </div>
                <p class="text-[11px] text-slate-500">Planes de siembra en ejecución.</p>
            </div>

            <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-xs space-y-2">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Total Compensaciones</span>
                <div class="flex items-baseline justify-between">
                    <h3 class="text-2xl font-black text-slate-800">{{ $totalCompensaciones }}</h3>
                    <span class="text-xs font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-lg">Histórico</span>
                </div>
                <p class="text-[11px] text-slate-500">Expedientes de reforestación.</p>
            </div>
        </div>

        <!-- Barra de Filtros Fluidos -->
        <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-xs">
            <form method="GET" action="{{ route('alcaldesa.ambiente') }}" id="filtroAmbienteForm" class="flex flex-wrap lg:flex-nowrap items-end gap-3 w-full">
                
                <!-- Tipo de Trámite -->
                <div class="w-full sm:w-auto flex-1.5 min-w-[160px]">
                    <label class="block text-[11px] font-bold text-slate-600 mb-1">Tipo de Trámite</label>
                    <select name="tipo_tramite" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:bg-white transition cursor-pointer">
                        <option value="">Todos los trámites</option>
                        <option value="inspeccion" {{ request('tipo_tramite') === 'inspeccion' ? 'selected' : '' }}>Inspección</option>
                        <option value="poda_deshierbe" {{ request('tipo_tramite') === 'poda_deshierbe' ? 'selected' : '' }}>Poda y Deshierbe</option>
                        <option value="desmalezamiento_conformacion" {{ request('tipo_tramite') === 'desmalezamiento_conformacion' ? 'selected' : '' }}>Desmalezamiento</option>
                        <option value="tala" {{ request('tipo_tramite') === 'tala' ? 'selected' : '' }}>Tala</option>
                    </select>
                </div>

                <!-- Estatus -->
                <div class="w-full sm:w-auto flex-1 min-w-[130px]">
                    <label class="block text-[11px] font-bold text-slate-600 mb-1">Estatus</label>
                    <select name="estado" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:bg-white transition cursor-pointer">
                        <option value="">Todos</option>
                        <option value="pendiente" {{ request('estado') === 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                        <option value="en_revision" {{ request('estado') === 'en_revision' ? 'selected' : '' }}>En Revisión</option>
                        <option value="aprobado" {{ request('estado') === 'aprobado' ? 'selected' : '' }}>Aprobado</option>
                        <option value="rechazado" {{ request('estado') === 'rechazado' ? 'selected' : '' }}>Rechazado</option>
                        <option value="incompleta" {{ request('estado') === 'incompleta' ? 'selected' : '' }}>Incompleta</option>
                    </select>
                </div>

                <!-- Fecha Inicio -->
                <div class="w-full sm:w-auto flex-1 min-w-[135px]">
                    <label class="block text-[11px] font-bold text-slate-600 mb-1">Desde (Requerido)</label>
                    <input type="date" name="fecha_inicio" value="{{ request('fecha_inicio') }}" required class="w-full px-2.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:bg-white transition">
                </div>

                <!-- Fecha Fin -->
                <div class="w-full sm:w-auto flex-1 min-w-[135px]">
                    <label class="block text-[11px] font-bold text-slate-600 mb-1">Hasta (Requerido)</label>
                    <input type="date" name="fecha_fin" value="{{ request('fecha_fin') }}" required class="w-full px-2.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:bg-white transition">
                </div>

                <!-- Botones de Acción -->
                <div class="flex items-center gap-1.5 shrink-0">
                    <button type="submit" title="Aplicar Filtros" class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl transition shadow-xs cursor-pointer flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </button>
                    <a href="{{ route('alcaldesa.ambiente') }}" title="Limpiar Búsqueda" class="py-2.5 px-3 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold text-xs rounded-xl transition flex items-center justify-center">
                        ✕
                    </a>
                </div>

            </form>
        </div>

        @if(!request()->filled('fecha_inicio') || !request()->filled('fecha_fin'))
        <div class="p-8 bg-amber-50 border border-amber-200 rounded-3xl text-center space-y-2">
            <p class="text-xs font-bold text-amber-800 uppercase tracking-wider">Filtro de Fechas Requerido</p>
            <p class="text-xs text-amber-600">Por favor, seleccione un rango de fechas de inicio y cierre para visualizar los datos del balance ambiental y habilitar el reporte PDF.</p>
        </div>
       @else
        <!-- Tabla de Solicitudes Ambientales -->
        <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-xs space-y-4">
            <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider">Historial de Trámites Ambientales</h3>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-slate-200 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                            <th class="py-3 px-4">Expediente</th>
                            <th class="py-3 px-4">Comuna / Sector</th>
                            <th class="py-3 px-4">Tipo de Trámite</th>
                            <th class="py-3 px-4">Emisión / Cierre</th>
                            <th class="py-3 px-4">Estatus</th>
                            <th class="py-3 px-4 text-right">Acción</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-xs text-slate-700">
                        @forelse($solicitudes as $solicitud)
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="py-3.5 px-4 font-mono font-bold text-indigo-600">{{ $solicitud->codigo_expediente }}</td>
                                <td class="py-3.5 px-4">
                                    <span class="font-bold block text-slate-800">{{ $solicitud->comuna }}</span>
                                    <span class="text-[11px] text-slate-400">Sector: {{ $solicitud->sector }}</span>
                                </td>
                                <td class="py-3.5 px-4 font-medium uppercase text-slate-700">{{ str_replace('_', ' ', $solicitud->tipo_tramite) }}</td>
                                <td class="py-3.5 px-4">
                                    <span class="block text-slate-700 font-medium">Emisión: {{ $solicitud->created_at?->format('d/m/Y') }}</span>
                                    @if($solicitud->fecha_cierre)
                                        <span class="text-[11px] text-emerald-600 font-bold">Cierre: {{ \Carbon\Carbon::parse($solicitud->fecha_cierre)->format('d/m/Y') }}</span>
                                    @else
                                        <span class="text-[11px] text-amber-500 font-medium">En proceso</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4">
                                    <span class="px-2.5 py-1 text-[10px] font-bold rounded-md uppercase 
                                        @if($solicitud->estado === 'aprobado') bg-emerald-50 text-emerald-700
                                        @elseif($solicitud->estado === 'rechazado') bg-rose-50 text-rose-700
                                        @else bg-slate-100 text-slate-700 @endif"
                                    >
                                        {{ str_replace('_', ' ', $solicitud->estado) }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-right">
                                    <button @click="currentRecord = {{ json_encode($solicitud->load(['usuario', 'inspector.user', 'recaudos'])) }}; modalOpen = true;" class="px-3 py-1.5 bg-emerald-50 hover:bg-emerald-600 hover:text-white text-emerald-700 text-xs font-bold rounded-xl transition cursor-pointer">
                                        Ver Detalles
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-slate-400">No hay solicitudes ambientales registradas bajo estos filtros.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="pt-2">
                {{ $solicitudes->links() }}
            </div>
        </div>
        @endif

        <!-- Modal de Detalles del Trámite y Recaudos -->
        <div x-show="modalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/60 backdrop-blur-sm p-4">
            <div @click.away="modalOpen = false" class="bg-white rounded-3xl max-w-2xl w-full p-6 sm:p-8 space-y-6 shadow-2xl border border-slate-200 overflow-y-auto max-h-[90vh]">
                
                <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                    <div>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-600 bg-emerald-50 px-2.5 py-1 rounded-lg">Expediente Ambiental</span>
                        <h3 class="text-xl font-bold text-slate-800 mt-1" x-text="currentRecord.codigo_expediente"></h3>
                    </div>
                    <button @click="modalOpen = false" class="h-8 w-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center font-bold">✕</button>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                    <div class="p-4 rounded-2xl bg-slate-50 space-y-1">
                        <p class="font-bold text-slate-400 uppercase tracking-wider text-[10px]">Ubicación del Trámite</p>
                        <p class="font-bold text-slate-800" x-text="'Comuna: ' + (currentRecord.comuna || 'N/D')"></p>
                        <p class="text-slate-600" x-text="'Dirección: ' + (currentRecord.direccion_exacta || 'N/D')"></p>
                    </div>

                    <div class="p-4 rounded-2xl bg-slate-50 space-y-1">
                        <p class="font-bold text-slate-400 uppercase tracking-wider text-[10px]">Solicitante</p>
                        <p class="font-bold text-slate-800" x-text="currentRecord.user ? (currentRecord.user.nombre + ' ' + currentRecord.user.apellido) : 'N/D'"></p>
                        <p class="text-slate-600" x-text="currentRecord.user ? currentRecord.user.correo : ''"></p>
                    </div>
                </div>

                <div class="space-y-2">
                    <p class="text-xs font-bold text-slate-700 uppercase tracking-wider">Motivo y Descripción</p>
                    <p class="text-xs text-slate-600 bg-slate-50 p-4 rounded-2xl border border-slate-100" x-text="currentRecord.motivo_descripcion"></p>
                </div>

                <!-- Recaudos y Documentos Adjuntos -->
                <div class="space-y-2">
                    <p class="text-xs font-bold text-slate-700 uppercase tracking-wider">Recaudos y Documentación</p>
                    <template x-if="currentRecord.recaudos && currentRecord.recaudos.length > 0">
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                            <template x-for="recaudo in currentRecord.recaudos">
                                <a :href="recaudo.archivo_url" target="_blank" class="block border border-slate-200 rounded-xl overflow-hidden hover:opacity-90 transition bg-slate-50 p-2">
                                    <p class="text-[10px] font-bold text-indigo-600 uppercase truncate" x-text="recaudo.tipo_recaudo"></p>
                                    <p class="text-[11px] text-slate-700 font-medium truncate" x-text="recaudo.nombre_archivo"></p>
                                </a>
                            </template>
                        </div>
                    </template>
                    <template x-if="!currentRecord.recaudos || currentRecord.recaudos.length === 0">
                        <p class="text-xs text-slate-400 italic">No hay recaudos o archivos adjuntos en este trámite.</p>
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

    <!-- Script para abrir el PDF de Ambiente mediante iframe oculto -->
    <iframe id="pdfAmbienteFrame" style="display: none;"></iframe>
    <script>
        function imprimirAmbientePdf() {
            const form = document.getElementById('filtroAmbienteForm');
            const formData = new FormData(form);
            const params = new URLSearchParams(formData).toString();
            const url = "{{ route('alcaldesa.ambiente.pdf') }}?" + params;

            const frame = document.getElementById('pdfAmbienteFrame');
            frame.src = url;
            frame.onload = function() {
                frame.contentWindow.focus();
                frame.contentWindow.print();
            };
        }
    </script>
</x-layouts.app>