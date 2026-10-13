<x-layouts.app title="Manejo de Residuos - Despacho" active="alcaldesa_residuos">
    <div class="space-y-6" x-data="{ 
        modalOpen: false, 
        currentRecord: {} 
    }">
        
        <!-- Cabecera Institucional -->
        <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-xs flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <span class="px-2.5 py-1 bg-indigo-50 text-indigo-700 text-[10px] font-bold uppercase tracking-wider rounded-lg">Dirección de Residuos</span>
                <h1 class="text-2xl font-bold text-slate-800 mt-1">Control Comercial RACDA y Declaraciones</h1>
                <p class="text-xs text-slate-500 mt-0.5">Seguimiento de empresas registradas, perfiles ambientales y balance de toneladas de desechos.</p>
            </div>
        </div>

        <!-- Tarjetas de Resumen Ejecutivo de Residuos -->
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

        <!-- Barra de Filtros Fluidos -->
        <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-xs">
            <form method="GET" action="{{ route('alcaldesa.residuos') }}" class="flex flex-wrap lg:flex-nowrap items-end gap-3 w-full">
                
                <!-- Búsqueda por Razón Social o RIF -->
                <div class="w-full sm:w-auto flex-2 min-w-[200px]">
                    <label class="block text-[11px] font-bold text-slate-600 mb-1">Buscar Empresa / RIF</label>
                    <input type="text" name="buscar" value="{{ request('buscar') }}" placeholder="Nombre o RIF..." class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:bg-white transition">
                </div>

                <!-- Estatus -->
                <div class="w-full sm:w-auto flex-1 min-w-[150px]">
                    <label class="block text-[11px] font-bold text-slate-600 mb-1">Estatus Comercial</label>
                    <select name="estatus" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:bg-white transition cursor-pointer">
                        <option value="">Todos los estatus</option>
                        <option value="APROBADO" {{ request('estatus') === 'APROBADO' ? 'selected' : '' }}>Aprobado</option>
                        <option value="PENDIENTE_APROBACION" {{ request('estatus') === 'PENDIENTE_APROBACION' ? 'selected' : '' }}>Pendiente</option>
                        <option value="RECHAZADO" {{ request('estatus') === 'RECHAZADO' ? 'selected' : '' }}>Rechazado</option>
                    </select>
                </div>

                <!-- Botones de Acción -->
                <div class="flex items-center gap-1.5 shrink-0">
                    <button type="submit" title="Aplicar Filtros" class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl transition shadow-xs cursor-pointer flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </button>
                    <a href="{{ route('alcaldesa.residuos') }}" title="Limpiar Búsqueda" class="py-2.5 px-3 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold text-xs rounded-xl transition flex items-center justify-center">
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
                                    <button @click="currentRecord = {{ json_encode($empresa->load(['user', 'perfilAmbiental'])) }}; modalOpen = true;" class="px-3 py-1.5 bg-indigo-50 hover:bg-indigo-600 hover:text-white text-indigo-700 text-xs font-bold rounded-xl transition cursor-pointer">
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

        <!-- Modal de Detalles de la Empresa / Perfil Ambiental -->
        <div x-show="modalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/60 backdrop-blur-sm p-4">
            <div @click.away="modalOpen = false" class="bg-white rounded-3xl max-w-2xl w-full p-6 sm:p-8 space-y-6 shadow-2xl border border-slate-200 overflow-y-auto max-h-[90vh]">
                
                <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                    <div>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-indigo-600 bg-indigo-50 px-2.5 py-1 rounded-lg">Expediente Comercial</span>
                        <h3 class="text-xl font-bold text-slate-800 mt-1" x-text="currentRecord.razon_social"></h3>
                    </div>
                    <button @click="modalOpen = false" class="h-8 w-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center font-bold">✕</button>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                    <div class="p-4 rounded-2xl bg-slate-50 space-y-1">
                        <p class="font-bold text-slate-400 uppercase tracking-wider text-[10px]">Datos Fiscales</p>
                        <p class="font-bold text-slate-800" x-text="'RIF: ' + currentRecord.rif"></p>
                        <p class="text-slate-600" x-text="'Nro. Patente: ' + (currentRecord.numero_cuenta_patente || 'N/D')"></p>
                        <p class="text-slate-600" x-text="'Correo: ' + currentRecord.correo_comercial"></p>
                    </div>

                    <div class="p-4 rounded-2xl bg-slate-50 space-y-1">
                        <p class="font-bold text-slate-400 uppercase tracking-wider text-[10px]">Ubicación Comercial</p>
                        <p class="font-bold text-slate-800" x-text="'Comuna: ' + currentRecord.comuna"></p>
                        <p class="text-slate-600" x-text="'Sector: ' + currentRecord.sector"></p>
                        <p class="text-slate-600" x-text="'Calle / Local: ' + (currentRecord.calle_avenida || '') + ' ' + (currentRecord.edificio_galpon_local || '')"></p>
                    </div>
                </div>

                <div class="p-4 rounded-2xl bg-indigo-50/50 border border-indigo-100 space-y-2 text-xs">
                    <p class="font-bold text-indigo-900 uppercase tracking-wider text-[10px]">Clasificación Ambiental RACDA</p>
                    <div class="grid grid-cols-2 gap-2 text-indigo-950">
                        <p x-text="'Rol: ' + (currentRecord.perfil_ambiental?.rol_ambiental || 'NO ASIGNADO')"></p>
                        <p x-text="'Nro. RACDA: ' + (currentRecord.perfil_ambiental?.numero_racda || 'Pendiente')"></p>
                    </div>
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