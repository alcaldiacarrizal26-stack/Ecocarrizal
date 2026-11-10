<x-layouts.app title="Gestión de Compensaciones" active="ambiente_compensacion">
<div class="p-6 max-w-7xl mx-auto space-y-6 text-slate-800" x-data="{ modalAprobar: false, modalRechazar: false, entregaSeleccionada: null }">

    <!-- Encabezado -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200 pb-5">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 flex items-center gap-2">
                <svg class="w-7 h-7 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                Compensación Ambiental
            </h1>
            <p class="text-sm text-slate-500 mt-1">Verificación de entregas de plantas y árboles para solicitudes autorizadas.</p>
        </div>
    </div>

    {{-- Alerta de Notificación --}}
    @if(session('success'))
        <div class="p-4 mb-4 text-sm text-emerald-900 rounded-xl bg-emerald-50 border border-emerald-200 flex items-center justify-between shadow-sm">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
        </div>
    @endif

    {{-- 1. SECCIÓN DE REVISIÓN DE ABONOS / ENTREGAS PENDIENTES --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 bg-amber-50/70 border-b border-amber-200 flex items-center justify-between">
            <div class="flex items-center gap-2 text-amber-900 font-bold text-sm">
                <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>Entregas Pendientes por Verificar</span>
            </div>
            <span class="px-2.5 py-0.5 text-xs font-bold rounded-full bg-amber-200 text-amber-900">
                {{ $entregasPendientes->count() }}
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-slate-700 min-w-[750px]">
                <thead class="text-xs uppercase bg-slate-100 text-slate-700 border-b border-slate-200 font-bold tracking-wider">
                    <tr>
                        <th class="px-6 py-3.5">Resolución</th>
                        <th class="px-6 py-3.5">Ciudadano</th>
                        <th class="px-6 py-3.5">Cantidad</th>
                        <th class="px-6 py-3.5">Especie Entregada</th>
                        <th class="px-6 py-3.5">Comprobante</th>
                        <th class="px-6 py-3.5 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs">
                    @forelse($entregasPendientes as $entrega)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-6 py-4 font-mono font-bold text-slate-900">
                                {{ $entrega->compensacion->codigo_resolucion ?? 'S/R' }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-900">{{ $entrega->user->nombre ?? '' }} {{ $entrega->user->apellido ?? '' }}</div>
                                <div class="text-slate-500 font-mono text-[11px]">CI: {{ $entrega->user->cedula ?? 'N/A' }}</div>
                            </td>
                            <td class="px-6 py-4 font-bold text-emerald-700">
                                {{ $entrega->cantidad_entregada }} ejemplares
                            </td>
                            <td class="px-6 py-4 font-medium text-slate-800">
                                {{ $entrega->especie_entregada }}
                            </td>
                            <td class="px-6 py-4">
                                @if($entrega->comprobante_path)
                                    <a href="{{ Storage::url($entrega->comprobante_path) }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg text-slate-700 bg-slate-100 hover:bg-slate-200 transition shadow-2xs">
                                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        Ver Foto / Recibo
                                    </a>
                                @else
                                    <span class="text-slate-400 italic text-[11px]">Sin adjunto</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <button 
                                        @click="entregaSeleccionada = {{ json_encode($entrega) }}; modalAprobar = true"
                                        class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl transition shadow-sm cursor-pointer">
                                        Aprobar
                                    </button>
                                    <button 
                                        @click="entregaSeleccionada = {{ json_encode($entrega) }}; modalRechazar = true"
                                        class="px-3 py-1.5 bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs rounded-xl transition shadow-sm cursor-pointer">
                                        Rechazar
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center text-slate-400 text-sm">
                                No hay entregas pendientes de revisión en este momento.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- 2. LISTADO GENERAL DE EXPEDIENTES DE COMPENSACIÓN --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200 bg-slate-50/50 flex items-center justify-between">
            <h3 class="font-bold text-slate-900 text-sm uppercase tracking-wider">Estado General de Expedientes</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-slate-700 min-w-[750px]">
                <thead class="text-xs uppercase bg-slate-100 text-slate-700 border-b border-slate-200 font-bold tracking-wider">
                    <tr>
                        <th class="px-6 py-3.5">Código</th>
                        <th class="px-6 py-3.5">Ciudadano</th>
                        <th class="px-6 py-3.5">Especie Solicitada</th>
                        <th class="px-6 py-3.5">Progreso de Entrega</th>
                        <th class="px-6 py-3.5 text-center">Estatus</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs">
                    @forelse($compensaciones as $comp)
                        @php
                            $porcentaje = $comp->cantidad_asignada > 0 ? min(100, round(($comp->cantidad_entregada / $comp->cantidad_asignada) * 100)) : 0;
                        @endphp
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-6 py-4 font-mono font-bold text-slate-900">
                                {{ $comp->codigo_resolucion }}
                            </td>
                            <td class="px-6 py-4 font-medium text-slate-800">
                                {{ $comp->user->nombre ?? 'N/A' }} {{ $comp->user->apellido ?? '' }}
                            </td>
                            <td class="px-6 py-4 font-medium text-slate-800">
                                {{ $comp->especie_requerida }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="w-full max-w-xs">
                                    <div class="flex justify-between text-[11px] mb-1 font-bold">
                                        <span class="text-slate-700">{{ $comp->cantidad_entregada }} de {{ $comp->cantidad_asignada }}</span>
                                        <span class="text-emerald-700">{{ $porcentaje }}%</span>
                                    </div>
                                    <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden border border-slate-200">
                                        <div class="bg-emerald-500 h-2 rounded-full transition-all duration-300" style="width: {{ $porcentaje }}%"></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($comp->estatus === 'COMPLETADO')
                                    <span class="px-2.5 py-1 text-[10px] font-bold rounded-full bg-emerald-100 text-emerald-800 border border-emerald-200 uppercase inline-block">
                                        Completado
                                    </span>
                                @elseif($comp->estatus === 'PARCIAL')
                                    <span class="px-2.5 py-1 text-[10px] font-bold rounded-full bg-sky-100 text-sky-800 border border-sky-200 uppercase inline-block">
                                        Parcial
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 text-[10px] font-bold rounded-full bg-amber-100 text-amber-800 border border-amber-200 uppercase inline-block">
                                        Pendiente
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center text-slate-400 text-sm">
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
        <div class="bg-white rounded-2xl max-w-md w-full p-6 space-y-4 border border-slate-200 shadow-2xl">
            <h3 class="text-lg font-bold text-slate-900">Aprobar Entrega de Ejemplares</h3>
            <p class="text-xs text-slate-600 leading-relaxed">
                ¿Confirma la validación de <strong class="text-slate-900 font-bold" x-text="entregaSeleccionada?.cantidad_entregada"></strong> ejemplares entregados por el ciudadano?
            </p>
            <form x-bind:action="'{{ url('/director/ambiente/compensacion/entregas') }}/' + entregaSeleccionada?.id + '/evaluar'" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="estatus" value="APROBADO">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Observación adicional (Opcional):</label>
                    <textarea name="observacion_director" rows="2" class="w-full text-xs rounded-xl border border-slate-200 bg-slate-50/50 text-slate-800 p-3 focus:bg-white focus:ring-2 focus:ring-emerald-500 outline-none transition"></textarea>
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" @click="modalAprobar = false" class="px-4 py-2 text-xs font-bold rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 cursor-pointer transition">Cancelar</button>
                    <button type="submit" class="px-4 py-2 text-xs font-bold rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white cursor-pointer transition shadow-sm">Confirmar Aprobación</button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL RECHAZAR --}}
    <div x-show="modalRechazar" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/60 backdrop-blur-sm">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 space-y-4 border border-slate-200 shadow-2xl">
            <h3 class="text-lg font-bold text-slate-900">Rechazar Entrega de Ejemplares</h3>
            <form x-bind:action="'{{ url('/director/ambiente/compensacion/entregas') }}/' + entregaSeleccionada?.id + '/evaluar'" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="estatus" value="RECHAZADO">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Motivo del rechazo <span class="text-rose-500">*</span></label>
                    <textarea name="observacion_director" rows="3" required class="w-full text-xs rounded-xl border border-slate-200 bg-slate-50/50 text-slate-800 p-3 focus:bg-white focus:ring-2 focus:ring-rose-500 outline-none transition" placeholder="Indique la justificación técnica..."></textarea>
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" @click="modalRechazar = false" class="px-4 py-2 text-xs font-bold rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 cursor-pointer transition">Cancelar</button>
                    <button type="submit" class="px-4 py-2 text-xs font-bold rounded-xl bg-rose-600 hover:bg-rose-700 text-white cursor-pointer transition shadow-sm">Rechazar Entrega</button>
                </div>
            </form>
        </div>
    </div>

</div>
</x-layouts.app>