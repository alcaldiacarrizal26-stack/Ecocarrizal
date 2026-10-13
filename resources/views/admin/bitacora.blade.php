<x-layouts.app title="Bitácora de Auditoría" active="admin_bitacora">
    <div class="p-6 max-w-7xl mx-auto space-y-6" x-data="{ modalOpen: false, eventoSeleccionado: null }">
        
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Bitácora General de Auditoría</h1>
                <p class="text-slate-500 text-sm">Registro cronológico, trazable e imborrable de las transacciones y acciones operativas del sistema.</p>
            </div>
        </div>

        <!-- Filtros Avanzados con diseño sutil -->
        <form method="GET" action="{{ route('admin.bitacora') }}" class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 items-end">
            <div>
                <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Desde (Fecha)</label>
                <input type="date" name="fecha_desde" value="{{ request('fecha_desde') }}" class="w-full bg-slate-50/50 border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-700 focus:bg-white transition">
            </div>

            <div>
                <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Hasta (Fecha)</label>
                <input type="date" name="fecha_hasta" value="{{ request('fecha_hasta') }}" class="w-full bg-slate-50/50 border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-700 focus:bg-white transition">
            </div>

            <div>
                <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Módulo</label>
                <select name="modulo" class="w-full bg-slate-50/50 border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-700 focus:bg-white transition">
                    <option value="">Todos los módulos</option>
                    @foreach($modulos as $mod)
                        <option value="{{ $mod }}" {{ request('modulo') === $mod ? 'selected' : '' }}>{{ strtoupper($mod) }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Nivel de Severidad</label>
                <select name="nivel" class="w-full bg-slate-50/50 border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-700 focus:bg-white transition">
                    <option value="">Todos los niveles</option>
                    <option value="info" {{ request('nivel') === 'info' ? 'selected' : '' }}>INFO</option>
                    <option value="warning" {{ request('nivel') === 'warning' ? 'selected' : '' }}>WARNING</option>
                    <option value="danger" {{ request('nivel') === 'danger' ? 'selected' : '' }}>DANGER</option>
                    <option value="critical" {{ request('nivel') === 'critical' ? 'selected' : '' }}>CRITICAL</option>
                </select>
            </div>

            <div class="flex items-center gap-2">
                <button type="submit" class="w-full py-2 bg-slate-800 hover:bg-slate-900 text-white font-bold rounded-xl text-xs transition shadow-sm cursor-pointer">
                    Filtrar
                </button>
                <a href="{{ route('admin.bitacora') }}" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold rounded-xl text-xs transition text-center whitespace-nowrap">
                    Limpiar
                </a>
            </div>

            <div class="lg:col-span-5">
                <input type="text" name="buscar" value="{{ request('buscar') }}" placeholder="Buscar por descripción o tipo de evento..." class="w-full bg-slate-50/50 border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-700 focus:bg-white transition">
            </div>
        </form>

        <!-- Tabla de Registros -->
        <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-sm">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-100 text-xs uppercase text-slate-600 border-b border-slate-200 font-bold tracking-wider">
                    <tr>
                        <th class="px-6 py-4">Fecha y Hora</th>
                        <th class="px-6 py-4">Usuario</th>
                        <th class="px-6 py-4">Nivel / Módulo</th>
                        <th class="px-6 py-4">Descripción</th>
                        <th class="px-6 py-4 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs">
                    @forelse($bitacoras as $log)
                        <tr class="hover:bg-slate-50/80 transition-colors align-middle">
                            <td class="px-6 py-4 font-mono text-slate-500 whitespace-nowrap">
                                {{ $log->created_at?->format('d/m/Y H:i:s') }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-800">{{ $log->user->nombre ?? 'Sistema' }} {{ $log->user->apellido ?? '' }}</div>
                                <span class="inline-block mt-0.5 px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-slate-100 text-slate-600">
                                    {{ $log->user->role->nombre ?? 'N/A' }}
                                </span>
                            </td>
                            <!-- Nivel y Módulo con separación adecuada y mejor presentación -->
                            <td class="px-6 py-4">
                                <div class="flex flex-col gap-1.5 items-start">
                                    @php
                                        $badgeColor = match($log->nivel) {
                                            'critical' => 'bg-rose-50 text-rose-700 border-rose-200',
                                            'danger' => 'bg-orange-50 text-orange-700 border-orange-200',
                                            'warning' => 'bg-amber-50 text-amber-700 border-amber-200',
                                            default => 'bg-blue-50 text-blue-700 border-blue-200',
                                        };
                                    @endphp
                                    <span class="px-2.5 py-0.5 rounded-md font-mono font-bold text-[10px] border uppercase shadow-2xs {{ $badgeColor }}">
                                        {{ $log->nivel }}
                                    </span>
                                    <span class="px-2.5 py-0.5 rounded-md font-mono font-bold text-[10px] bg-slate-100 text-slate-700 border border-slate-200 uppercase shadow-2xs">
                                        {{ $log->modulo }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-slate-700 font-medium max-w-md">
                                <div class="font-bold uppercase text-[10px] text-indigo-600 mb-0.5 tracking-wider">{{ $log->tipo_evento }}</div>
                                {{ $log->descripcion }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                @if($log->valores_anteriores || $log->valores_nuevos)
                                    <button type="button" @click="eventoSeleccionado = @js($log); modalOpen = true" class="px-3 py-2 bg-slate-800 hover:bg-slate-900 text-white rounded-xl text-xs font-bold transition shadow-sm cursor-pointer inline-flex items-center gap-1.5">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        <span>Ver Cambios</span>
                                    </button>
                                @else
                                    <span class="text-slate-400 italic">Sin cambios</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-slate-400 text-sm">
                                @if(!request()->filled('fecha_desde'))
                                    Por favor, selecciona un rango de fechas y aplica los filtros para consultar la bitácora.
                                @else
                                    No se encontraron registros de auditoría en el rango de fechas seleccionado.
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Paginación -->
        @if(method_exists($bitacoras, 'hasPages') && $bitacoras->hasPages())
            <div class="p-4 bg-white border border-slate-200 rounded-2xl shadow-sm">
                {{ $bitacoras->links() }}
            </div>
        @endif

        <!-- MODAL AMPLIADO Y CÓMODO PARA VER VALORES ANTERIORES Y NUEVOS -->
        <div x-show="modalOpen" class="fixed inset-0 z-50 bg-slate-950/60 backdrop-blur-sm flex items-center justify-center p-4" style="display: none;">
            <div @click.outside="modalOpen = false" class="bg-white rounded-2xl border border-slate-200 w-full max-w-4xl overflow-hidden shadow-2xl space-y-4 p-6 max-h-[92vh] flex flex-col">
                
                <div class="flex items-center justify-between border-b border-slate-100 pb-3 shrink-0">
                    <div>
                        <h3 class="text-lg font-bold text-slate-800">Trazabilidad Detallada del Evento</h3>
                        <p class="text-xs text-slate-400">Comparativa directa de estados en la base de datos</p>
                    </div>
                    <button type="button" @click="modalOpen = false" class="text-slate-400 hover:text-slate-700 text-2xl font-bold px-2">&times;</button>
                </div>

                <template x-if="eventoSeleccionado">
                    <div class="space-y-4 text-xs overflow-y-auto flex-1 pr-1">
                        <div class="bg-slate-50 p-4 rounded-xl border border-slate-100 space-y-1">
                            <span class="text-slate-400 block font-bold uppercase tracking-wider">Descripción del Suceso:</span>
                            <p class="text-slate-800 font-bold text-sm" x-text="eventoSeleccionado.descripcion"></p>
                            <div class="flex gap-4 pt-2 text-[11px] text-slate-500 font-mono">
                                <span>Módulo: <strong class="text-slate-700 uppercase" x-text="eventoSeleccionado.modulo"></strong></span>
                                <span>IP Registro: <strong x-text="eventoSeleccionado.ip_address"></strong></span>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="flex flex-col">
                                <span class="font-bold text-rose-700 uppercase block mb-1 text-sm">Valores Anteriores:</span>
                                <pre class="bg-slate-900 text-rose-300 p-4 rounded-xl overflow-x-auto text-xs font-mono flex-1 min-h-[300px] shadow-inner" x-text="JSON.stringify(eventoSeleccionado.valores_anteriores, null, 2)"></pre>
                            </div>
                            <div class="flex flex-col">
                                <span class="font-bold text-emerald-700 uppercase block mb-1 text-sm">Valores Nuevos:</span>
                                <pre class="bg-slate-900 text-emerald-300 p-4 rounded-xl overflow-x-auto text-xs font-mono flex-1 min-h-[300px] shadow-inner" x-text="JSON.stringify(eventoSeleccionado.valores_nuevos, null, 2)"></pre>
                            </div>
                        </div>
                    </div>
                </template>

            </div>
        </div>

    </div>
</x-layouts.app>