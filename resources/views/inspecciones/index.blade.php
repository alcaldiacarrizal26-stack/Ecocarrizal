<x-layouts.app title="Módulo de Inspecciones Ambientales" active="inspecciones">
    <div class="p-6 max-w-7xl mx-auto space-y-6">
        
        <!-- Encabezado -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Inspecciones Ambientales</h1>
                <p class="text-slate-500 text-sm">Gestión de visitas de campo, hallazgos y emisión de actas técnicas.</p>
            </div>
        </div>

        <!-- Alertas de Estado -->
        @if(session('success'))
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl text-sm">
                {{ session('success') }}
            </div>
        @endif

        <!-- Sección 1: Asignaciones Pendientes por Evaluar -->
        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
            <h2 class="text-lg font-semibold text-slate-800 mb-4 flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                Expedientes Pendientes de Visita / Evaluación
            </h2>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="bg-slate-50 text-slate-700 uppercase text-xs font-semibold">
                        <tr>
                            <th class="px-4 py-3">Código</th>
                            <th class="px-4 py-3">Tipo / Origen</th>
                            <th class="px-4 py-3">Ubicación</th>
                            <th class="px-4 py-3">Estado</th>
                            <th class="px-4 py-3 text-right">Acción</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($asignacionesPendientes as $item)
                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-3 font-medium text-slate-900">
                                    {{ $item->codigo_expediente ?? $item->codigo }}
                                </td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 rounded-md text-xs font-semibold {{ isset($item->tipo_tramite) ? 'bg-blue-50 text-blue-700' : 'bg-red-50 text-red-700' }}">
                                        {{ isset($item->tipo_tramite) ? 'Solicitud: '.str_replace('_', ' ', $item->tipo_tramite) : 'Denuncia: '.$item->tipo_contaminacion }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    {{ $item->sector }}, {{ $item->comuna }}
                                </td>
                                <td class="px-4 py-3">
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                                        {{ ucfirst(str_replace('_', ' ', $item->estado ?? $item->estatus)) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <a href="{{ route('inspecciones.evaluar', $item->id) }}" 
                                       class="inline-flex items-center gap-1 bg-emerald-600 hover:bg-emerald-700 text-white text-xs px-3 py-1.5 rounded-lg transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        Evaluar / Levantar Acta
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-6 text-center text-slate-400">
                                    No tienes inspecciones o denuncias pendientes de evaluación en este momento.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Sección 2: Historial de Inspecciones Realizadas -->
        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
            <h2 class="text-lg font-semibold text-slate-800 mb-4">Actas e Inspecciones Realizadas</h2>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="bg-slate-50 text-slate-700 uppercase text-xs font-semibold">
                        <tr>
                            <th class="px-4 py-3">N° Acta</th>
                            <th class="px-4 py-3">Fecha Visita</th>
                            <th class="px-4 py-3">Inspector</th>
                            <th class="px-4 py-3">Dictamen</th>
                            <th class="px-4 py-3 text-right">Acción</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($inspeccionesRealizadas as $inspeccion)
                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-3 font-mono font-bold text-slate-900">
                                    {{ $inspeccion->codigo_acta }}
                                </td>
                                <td class="px-4 py-3">
                                    {{ $inspeccion->fecha_visita->format('d/m/Y H:i A') }}
                                </td>
                                <td class="px-4 py-3">
                                    {{ $inspeccion->inspector->user->nombre }} {{ $inspeccion->inspector->user->apellido }}
                                </td>
                                <td class="px-4 py-3">
                                    @php
                                        $badgeClasses = match($inspeccion->dictamen) {
                                            'APROBADO' => 'bg-emerald-100 text-emerald-800',
                                            'CON_OBSERVACIONES' => 'bg-amber-100 text-amber-800',
                                            'RECHAZADO' => 'bg-rose-100 text-rose-800',
                                            'REINSPECCION_REQUERIDA' => 'bg-purple-100 text-purple-800',
                                            default => 'bg-slate-100 text-slate-800'
                                        };
                                    @endphp
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $badgeClasses }}">
                                        {{ str_replace('_', ' ', $inspeccion->dictamen) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <a href="{{ route('inspecciones.show', $inspeccion->id) }}" 
                                       class="text-emerald-600 hover:text-emerald-800 text-xs font-semibold">
                                        Ver Informe →
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-6 text-center text-slate-400">
                                    Aún no hay inspecciones procesadas en el historial.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $inspeccionesRealizadas->links() }}
            </div>
        </div>

    </div>
</x-layouts.app>