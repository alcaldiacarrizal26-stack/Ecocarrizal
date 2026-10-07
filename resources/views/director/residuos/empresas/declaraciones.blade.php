<x-layouts.app title="Gestión General de Declaraciones Ambientales" active="director_residuos_declaraciones">
    <div class="p-6 max-w-7xl mx-auto space-y-6 bg-slate-50 min-h-screen">
        
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
            <div>
                <h1 class="text-2xl font-bold text-slate-800 tracking-tight">Declaraciones de Residuos Recibidas</h1>
                <p class="text-sm text-slate-500 mt-1">
                    Monitoreo y fiscalización ambiental de las notificaciones presentadas por las empresas del Municipio Carrizal.
                </p>
            </div>
            <div class="flex items-center gap-2">
                <span class="px-3 py-1 bg-emerald-100 text-emerald-800 text-xs font-bold rounded-full border border-emerald-300">
                    Dirección de Manejo Integral de Residuos
                </span>
            </div>
        </div>

        <!-- Filtros de Búsqueda -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm">
            <form method="GET" action="{{ route('director.residuos.declaraciones.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-500 uppercase mb-1">Rol Declarante</label>
                    <select name="rol" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        <option value="">-- Todos los Roles --</option>
                        <option value="GENERADOR" {{ request('rol') === 'GENERADOR' ? 'selected' : '' }}>Generador</option>
                        <option value="MANEJADOR" {{ request('rol') === 'MANEJADOR' ? 'selected' : '' }}>Manejador</option>
                        <option value="NO_APLICA" {{ request('rol') === 'NO_APLICA' ? 'selected' : '' }}>No Aplica (Bajo Impacto)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-500 uppercase mb-1">Estatus</label>
                    <select name="estatus" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        <option value="">-- Todos los Estatus --</option>
                        <option value="PRESENTADA" {{ request('estatus') === 'PRESENTADA' ? 'selected' : '' }}>Presentada</option>
                        <option value="EN_REVISION" {{ request('estatus') === 'EN_REVISION' ? 'selected' : '' }}>En Revisión</option>
                        <option value="APROBADA" {{ request('estatus') === 'APROBADA' ? 'selected' : '' }}>Aprobada</option>
                        <option value="OBSERVADA" {{ request('estatus') === 'OBSERVADA' ? 'selected' : '' }}>Observada</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-500 uppercase mb-1">Período</label>
                    <input type="text" name="periodo" value="{{ request('periodo') }}" placeholder="Ej: 2026-03 o 2026-ANUAL"
                           class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                </div>

                <div class="flex items-end gap-2">
                    <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2 px-4 rounded-xl text-xs transition-colors shadow-sm">
                        Filtrar
                    </button>
                    <a href="{{ route('director.residuos.declaraciones.index') }}" class="bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold py-2 px-3 rounded-xl text-xs transition-colors">
                        Limpiar
                    </a>
                </div>
            </form>
        </div>

        <!-- Tabla de Declaraciones -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-xs uppercase text-slate-500 font-semibold tracking-wider">
                            <th class="p-4">Empresa / RIF</th>
                            <th class="p-4">Rol</th>
                            <th class="p-4">Período / Frecuencia</th>
                            <th class="p-4">Resumen Desechos (TON)</th>
                            <th class="p-4">Fecha Presentación</th>
                            <th class="p-4">Estatus</th>
                            <th class="p-4 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm">
                        @forelse($declaraciones as $item)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <!-- Empresa -->
                                <td class="p-4">
                                    <div class="font-bold text-slate-800">{{ $item->empresa->razon_social ?? $item->empresa->nombre_comercial ?? 'N/A' }}</div>
                                    <div class="text-xs font-mono text-slate-400">{{ $item->empresa->rif ?? 'N/A' }}</div>
                                </td>

                                <!-- Rol -->
                                <td class="p-4">
                                    @if($item->rol_declarante === 'MANEJADOR')
                                        <span class="px-2 py-0.5 text-[11px] font-bold rounded-md bg-amber-100 text-amber-800 border border-amber-200">
                                            MANEJADOR
                                        </span>
                                    @elseif($item->rol_declarante === 'GENERADOR')
                                        <span class="px-2 py-0.5 text-[11px] font-bold rounded-md bg-emerald-100 text-emerald-800 border border-emerald-200">
                                            GENERADOR
                                        </span>
                                    @else
                                        <span class="px-2 py-0.5 text-[11px] font-bold rounded-md bg-slate-100 text-slate-600 border border-slate-200">
                                            NO APLICA
                                        </span>
                                    @endif
                                </td>

                                <!-- Período -->
                                <td class="p-4">
                                    <div class="font-medium text-slate-700">{{ $item->periodo }}</div>
                                    <div class="text-xs text-slate-400">{{ $item->frecuencia }}</div>
                                </td>

                                <!-- Resumen Toneladas -->
                                <td class="p-4 font-mono text-xs">
                                    @if($item->rol_declarante === 'NO_APLICA')
                                        <span class="text-slate-400 italic">Declaración en Cero</span>
                                    @else
                                        <div class="text-slate-700">Comunes: <strong class="text-slate-900">{{ number_format($item->toneladas_comunes, 3) }}</strong></div>
                                        <div class="text-emerald-600">Reciclables: <strong>{{ number_format($item->toneladas_reciclables, 3) }}</strong></div>
                                        <div class="text-amber-600">Peligrosas: <strong>{{ number_format($item->toneladas_peligrosas, 3) }}</strong></div>
                                    @endif
                                </td>

                                <!-- Fecha -->
                                <td class="p-4 text-xs text-slate-500">
                                    {{ $item->created_at->format('d/m/Y h:i A') }}
                                </td>

                                <!-- Estatus -->
                                <td class="p-4">
                                    @switch($item->estatus)
                                        @case('APROBADA')
                                            <span class="px-2.5 py-1 text-xs font-bold rounded-full bg-emerald-100 text-emerald-800 border border-emerald-300">
                                                Aprobada
                                            </span>
                                            @break
                                        @case('OBSERVADA')
                                            <span class="px-2.5 py-1 text-xs font-bold rounded-full bg-amber-100 text-amber-800 border border-amber-300">
                                                Observada
                                            </span>
                                            @break
                                        @case('RECHAZADA')
                                            <span class="px-2.5 py-1 text-xs font-bold rounded-full bg-rose-100 text-rose-800 border border-rose-300">
                                                Rechazada
                                            </span>
                                            @break
                                        @default
                                            <span class="px-2.5 py-1 text-xs font-bold rounded-full bg-blue-100 text-blue-800 border border-blue-300">
                                                Presentada
                                            </span>
                                    @endswitch
                                </td>

                                <!-- Acciones -->
                                <td class="p-4 text-right">
                                    <a href="{{ route('director.residuos.empresas.show', $item->empresa_id) }}" 
                                       class="inline-flex items-center px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-lg transition-colors border border-slate-300">
                                        Ver Empresa &rarr;
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="p-8 text-center text-slate-400 text-sm">
                                    No se encontraron declaraciones registradas en el sistema.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($declaraciones->hasPages())
                <div class="p-4 border-t border-slate-100">
                    {{ $declaraciones->links() }}
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>