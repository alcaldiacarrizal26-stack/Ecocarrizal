<x-layouts.app title="Gestión de Compensaciones" active="ambiente_compensacion">
<div class="p-6 max-w-7xl mx-auto space-y-6" x-data="{ modalAprobar: false, modalRechazar: false, entregaSeleccionada: null }">

    <!-- Encabezado -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200 dark:border-slate-800 pb-5">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white flex items-center gap-2">
                <svg class="w-7 h-7 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                Compensación Ambiental
            </h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Verificación de entregas de plantas y árboles para solicitudes autorizadas.</p>
        </div>
    </div>

    {{-- Alerta de Notificación --}}
    @if(session('success'))
        <div class="p-4 mb-4 text-sm text-emerald-800 rounded-xl bg-emerald-50 dark:bg-emerald-950/50 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>{{ session('success') }}</span>
            </div>
        </div>
    @endif

    {{-- 1. SECCIÓN DE REVISIÓN DE ABONOS / ENTREGAS PENDIENTES --}}
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="px-6 py-4 bg-amber-50 dark:bg-amber-950/30 border-b border-amber-200 dark:border-amber-900/50 flex items-center justify-between">
            <div class="flex items-center gap-2 text-amber-900 dark:text-amber-300 font-semibold">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>Entregas Pendientes por Verificar</span>
            </div>
            <span class="px-2.5 py-0.5 text-xs font-bold rounded-full bg-amber-200 dark:bg-amber-900 text-amber-900 dark:text-amber-200">
                {{ $entregasPendientes->count() }}
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-slate-600 dark:text-slate-300">
                <thead class="text-xs uppercase bg-slate-50 dark:bg-slate-800/50 text-slate-500 border-b border-slate-200 dark:border-slate-800">
                    <tr>
                        <th class="px-6 py-3.5">Resolución</th>
                        <th class="px-6 py-3.5">Ciudadano</th>
                        <th class="px-6 py-3.5">Cantidad</th>
                        <th class="px-6 py-3.5">Especie Entregada</th>
                        <th class="px-6 py-3.5">Comprobante</th>
                        <th class="px-6 py-3.5">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                    @forelse($entregasPendientes as $entrega)
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30">
                            <td class="px-6 py-4 font-mono font-medium text-slate-900 dark:text-white">
                                {{ $entrega->compensacion->codigo_resolucion ?? 'S/R' }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-medium text-slate-900 dark:text-white">{{ $entrega->user->nombre ?? '' }} {{ $entrega->user->apellido ?? '' }}</div>
                                <div class="text-xs text-slate-400">CI: {{ $entrega->user->cedula ?? 'N/A' }}</div>
                            </td>
                            <td class="px-6 py-4 font-bold text-emerald-600 dark:text-emerald-400">
                                {{ $entrega->cantidad_entregada }} ejemplares
                            </td>
                            <td class="px-6 py-4">
                                {{ $entrega->especie_entregada }}
                            </td>
                            <td class="px-6 py-4">
                                @if($entrega->comprobante_path)
                                    <a href="{{ Storage::url($entrega->comprobante_path) }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium rounded-lg text-slate-700 bg-slate-100 dark:bg-slate-800 dark:text-slate-300 hover:bg-slate-200 transition">
                                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        Ver Foto / Recibo
                                    </a>
                                @else
                                    <span class="text-xs text-slate-400 italic">Sin adjunto</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <button 
                                        @click="entregaSeleccionada = {{ json_encode($entrega) }}; modalAprobar = true"
                                        class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-medium text-xs rounded-lg transition shadow-sm">
                                        Aprobar
                                    </button>
                                    <button 
                                        @click="entregaSeleccionada = {{ json_encode($entrega) }}; modalRechazar = true"
                                        class="px-3 py-1.5 bg-rose-600 hover:bg-rose-700 text-white font-medium text-xs rounded-lg transition shadow-sm">
                                        Rechazar
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-slate-400">
                                No hay entregas pendientes de revisión en este momento.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- 2. LISTADO GENERAL DE EXPEDIENTES DE COMPENSACIÓN --}}
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between">
            <h3 class="font-semibold text-slate-900 dark:text-white">Estado General de Expedientes</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-slate-600 dark:text-slate-300">
                <thead class="text-xs uppercase bg-slate-50 dark:bg-slate-800/50 text-slate-500 border-b border-slate-200 dark:border-slate-800">
                    <tr>
                        <th class="px-6 py-3.5">Código</th>
                        <th class="px-6 py-3.5">Ciudadano</th>
                        <th class="px-6 py-3.5">Especie Solicitada</th>
                        <th class="px-6 py-3.5">Progreso de Entrega</th>
                        <th class="px-6 py-3.5">Estatus</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                    @forelse($compensaciones as $comp)
                        @php
                            $porcentaje = $comp->cantidad_asignada > 0 ? min(100, round(($comp->cantidad_entregada / $comp->cantidad_asignada) * 100)) : 0;
                        @endphp
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30">
                            <td class="px-6 py-4 font-mono font-medium text-slate-900 dark:text-white">
                                {{ $comp->codigo_resolucion }}
                            </td>
                            <td class="px-6 py-4">
                                {{ $comp->user->nombre ?? 'N/A' }} {{ $comp->user->apellido ?? '' }}
                            </td>
                            <td class="px-6 py-4">
                                {{ $comp->especie_requerida }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="w-full max-w-xs">
                                    <div class="flex justify-between text-xs mb-1 font-semibold">
                                        <span class="text-slate-700 dark:text-slate-300">{{ $comp->cantidad_entregada }} de {{ $comp->cantidad_asignada }}</span>
                                        <span class="text-emerald-600 dark:text-emerald-400">{{ $porcentaje }}%</span>
                                    </div>
                                    <div class="w-full bg-slate-200 dark:bg-slate-800 rounded-full h-2">
                                        <div class="bg-emerald-500 h-2 rounded-full transition-all duration-300" style="width: {{ $porcentaje }}%"></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @if($comp->estatus === 'COMPLETADO')
                                    <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-emerald-100 text-emerald-800 dark:bg-emerald-950/50 dark:text-emerald-300">
                                        COMPLETADO
                                    </span>
                                @elseif($comp->estatus === 'PARCIAL')
                                    <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-sky-100 text-sky-800 dark:bg-sky-950/50 dark:text-sky-300">
                                        PARCIAL
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-amber-100 text-amber-800 dark:bg-amber-950/50 dark:text-amber-300">
                                        PENDIENTE
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-slate-400">
                                No se registran expedientes de compensación ambiental.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- MODAL APROBAR --}}
    <div x-show="modalAprobar" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/60 backdrop-blur-sm">
        <div class="bg-white dark:bg-slate-900 rounded-2xl max-w-md w-full p-6 space-y-4 border border-slate-200 dark:border-slate-800 shadow-xl">
            <h3 class="text-lg font-bold text-slate-900 dark:text-white">Aprobar Entrega de Ejemplares</h3>
            <p class="text-sm text-slate-500 dark:text-slate-400">
                ¿Confirma la validación de <strong class="text-slate-900 dark:text-white" x-text="entregaSeleccionada?.cantidad_entregada"></strong> ejemplares entregados por el ciudadano?
            </p>
            <form x-bind:action="'{{ url('/director/ambiente/compensacion/entregas') }}/' + entregaSeleccionada?.id + '/evaluar'" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="estatus" value="APROBADO">
                <div>
                    <label class="block text-xs font-medium text-slate-700 dark:text-slate-300 mb-1">Observación adicional (Opcional):</label>
                    <textarea name="observacion_director" rows="2" class="w-full text-sm rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white p-2.5 focus:ring-2 focus:ring-emerald-500 outline-none"></textarea>
                </div>
                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" @click="modalAprobar = false" class="px-4 py-2 text-xs font-semibold rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300">Cancelar</button>
                    <button type="submit" class="px-4 py-2 text-xs font-semibold rounded-xl bg-emerald-600 text-white hover:bg-emerald-700">Confirmar Aprobación</button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL RECHAZAR --}}
    <div x-show="modalRechazar" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/60 backdrop-blur-sm">
        <div class="bg-white dark:bg-slate-900 rounded-2xl max-w-md w-full p-6 space-y-4 border border-slate-200 dark:border-slate-800 shadow-xl">
            <h3 class="text-lg font-bold text-slate-900 dark:text-white">Rechazar Entrega de Ejemplares</h3>
            <form x-bind:action="'{{ url('/director/ambiente/compensacion/entregas') }}/' + entregaSeleccionada?.id + '/evaluar'" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="estatus" value="RECHAZADO">
                <div>
                    <label class="block text-xs font-medium text-slate-700 dark:text-slate-300 mb-1">Motivo del rechazo (Requerido):</label>
                    <textarea name="observacion_director" rows="3" required class="w-full text-sm rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white p-2.5 focus:ring-2 focus:ring-rose-500 outline-none"></textarea>
                </div>
                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" @click="modalRechazar = false" class="px-4 py-2 text-xs font-semibold rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300">Cancelar</button>
                    <button type="submit" class="px-4 py-2 text-xs font-semibold rounded-xl bg-rose-600 text-white hover:bg-rose-700">Rechazar Entrega</button>
                </div>
            </form>
        </div>
    </div>

</div>
</x-layouts.app>