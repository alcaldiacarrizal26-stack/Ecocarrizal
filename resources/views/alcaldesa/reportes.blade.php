<x-layouts.app title="Despacho Ejecutivo - Reportes" active="reportes">
    <div class="space-y-6">
        
        <!-- Cabecera Institucional -->
        <div class="bg-white border border-slate-200 rounded-3xl p-6 sm:p-8 shadow-xs flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="px-2.5 py-1 bg-indigo-50 text-indigo-700 text-[10px] font-bold uppercase tracking-wider rounded-lg">Despacho de la Alcaldesa</span>
                    <span class="text-xs text-slate-400 font-medium">• Municipio Carrizal</span>
                </div>
                <h1 class="text-2xl font-bold text-slate-800">Panel Integral de Gestión y Control Municipal</h1>
                <p class="text-xs text-slate-500 mt-1">Indicadores consolidados en tiempo real: Ambiente, Manejo de Residuos y Poder Popular.</p>
            </div>

            <div>
                <button type="button" onclick="window.print()" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition flex items-center gap-2 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    Imprimir Resumen
                </button>
            </div>
        </div>

        <!-- Bloque 1: Métricas Globales por Área -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Ambiente -->
            <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-xs space-y-2">
                <span class="px-2 py-0.5 bg-emerald-100 text-emerald-800 text-[10px] font-bold rounded-md">Área Ambiente</span>
                <div class="flex items-baseline justify-between">
                    <h3 class="text-2xl font-black text-slate-800">{{ $totalSolicitudesAmbiente }}</h3>
                    <span class="text-xs font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-lg">Trámites</span>
                </div>
                <p class="text-[11px] text-slate-500">Compensaciones pendientes: <strong>{{ $compensacionesPendientes }}</strong></p>
            </div>

            <!-- Residuos -->
            <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-xs space-y-2">
                <span class="px-2 py-0.5 bg-indigo-100 text-indigo-800 text-[10px] font-bold rounded-md">Manejo de Residuos</span>
                <div class="flex items-baseline justify-between">
                    <h3 class="text-2xl font-black text-slate-800">{{ $totalEmpresasAprobadas }}</h3>
                    <span class="text-xs font-bold text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-lg">Empresas RACDA</span>
                </div>
                <p class="text-[11px] text-slate-500">Comunes: {{ number_format($toneladasTotalesComunes, 2) }} Ton</p>
            </div>

            <!-- Participación Ciudadana -->
            <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-xs space-y-2">
                <span class="px-2 py-0.5 bg-amber-100 text-amber-800 text-[10px] font-bold rounded-md">Poder Popular</span>
                <div class="flex items-baseline justify-between">
                    <h3 class="text-2xl font-black text-slate-800">{{ $totalDenuncias }}</h3>
                    <span class="text-xs font-bold text-amber-600 bg-amber-50 px-2 py-0.5 rounded-lg">Denuncias</span>
                </div>
                <p class="text-[11px] text-slate-500">Articulación directa con Comunas.</p>
            </div>

            <!-- Total General Toneladas Peligrosas / Reciclables -->
            <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-xs space-y-2">
                <span class="px-2 py-0.5 bg-rose-100 text-rose-800 text-[10px] font-bold rounded-md">Materiales Especiales</span>
                <div class="flex items-baseline justify-between">
                    <h3 class="text-2xl font-black text-slate-800">{{ number_format($toneladasTotalesPeligrosas, 2) }}</h3>
                    <span class="text-xs font-bold text-rose-600 bg-rose-50 px-2 py-0.5 rounded-lg">Ton Peligrosas</span>
                </div>
                <p class="text-[11px] text-slate-500">Reciclables: {{ number_format($toneladasTotalesReciclables, 2) }} Ton</p>
            </div>
        </div>

        <!-- Bloque 2: Detalle por Comunas y Sectores (Poder Popular) -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            
            <!-- Comunas con mayor incidencia / reportes -->
            <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-xs space-y-4">
                <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider">Incidencia por Comunas (Poder Popular)</h3>
                <div class="space-y-3">
                    @forelse($denunciasPorComuna as $item)
                        <div class="p-3 rounded-2xl bg-slate-50 border border-slate-100 flex items-center justify-between">
                            <span class="text-xs font-bold text-slate-700">Comuna: {{ $item->comuna }}</span>
                            <span class="px-2.5 py-1 bg-indigo-50 text-indigo-700 text-xs font-black rounded-lg">{{ $item->total }} reportes</span>
                        </div>
                    @empty
                        <p class="text-xs text-slate-400 text-center py-4">No hay registros de comunas asociados aún.</p>
                    @endforelse
                </div>
            </div>

            <!-- Últimas Denuncias Ciudadanas -->
            <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-xs space-y-4">
                <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider">Últimos Reportes Ambientales Recibidos</h3>
                <div class="space-y-3">
                    @forelse($ultimasDenuncias as $denuncia)
                        <div class="p-3 rounded-2xl bg-slate-50 border border-slate-100 flex items-center justify-between gap-2">
                            <div>
                                <p class="text-xs font-bold text-slate-800">{{ $denuncia->tipo_contaminacion }}</p>
                                <p class="text-[11px] text-slate-500">Sector: {{ $denuncia->sector }} • Comuna: {{ $denuncia->comuna }}</p>
                            </div>
                            <span class="px-2 py-0.5 text-[10px] font-bold rounded-md bg-slate-200 text-slate-700 uppercase">{{ $denuncia->estatus }}</span>
                        </div>
                    @empty
                        <p class="text-xs text-slate-400 text-center py-4">Sin denuncias registradas.</p>
                    @endforelse
                </div>
            </div>

        </div>

    </div>
</x-layouts.app>