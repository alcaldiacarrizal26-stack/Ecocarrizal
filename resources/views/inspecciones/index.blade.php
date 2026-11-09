<x-layouts.app title="Módulo de Inspecciones Ambientales" active="inspecciones">
    <div class="p-6 max-w-7xl mx-auto space-y-6">
        
    <!-- Encabezado -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Inspecciones Ambientales</h1>
            <p class="text-slate-500 text-sm">Gestión de visitas de campo, hallazgos, fiscalización de residuos y emisión de actas técnicas.</p>
        </div>
    </div>

    <!-- Alertas de Estado -->
    @if(session('success'))
        <div x-data="{ show: true }" 
             x-show="show" 
             x-transition:leave="transition ease-in duration-200"
             class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl text-sm flex items-center justify-between gap-3 shadow-sm">
            
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>{{ session('success') }}</span>
            </div>

            <button @click="show = false" type="button" class="text-emerald-500 hover:text-emerald-800 p-1 rounded-lg">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    @endif

    <!-- Sección 1: Asignaciones Pendientes por Evaluar (Con Buscador y Filtros) -->
    <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm space-y-4">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <h2 class="text-lg font-semibold text-slate-800 flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                Expedientes y Órdenes Pendientes de Visita / Evaluación
            </h2>
        </div>

        <!-- Filtros y Buscador Dinámico -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3 pt-2">
            <!-- Buscador de texto -->
            <div class="relative">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </span>
                <input type="text" x-model="search" placeholder="Buscar por código, empresa, sector..." 
                       class="w-full pl-9 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:bg-white focus:border-emerald-500 transition">
            </div>

            <!-- Filtro por Tipo / Origen -->
            <div>
                <select x-model="tipoFiltro" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:bg-white focus:border-emerald-500 transition">
                    <option value="todos">Todos los Orígenes / Tipos</option>
                    <option value="orden">Fiscalización de Residuos (Órdenes)</option>
                    <option value="solicitud">Solicitudes Ambientales</option>
                    <option value="denuncia">Denuncias Ambientales</option>
                </select>
            </div>

            <!-- Filtro por Estado -->
            <div>
                <select x-model="estadoFiltro" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:bg-white focus:border-emerald-500 transition">
                    <option value="todos">Todos los Estados</option>
                    <option value="inspector_asignado">Inspector Asignado</option>
                    <option value="en_inspeccion">En Inspección</option>
                    <option value="requiere_reinspeccion">Requiere Reinspección</option>
                </select>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-slate-700 uppercase text-xs font-semibold">
                    <tr>
                        <th class="px-4 py-3">Código / Orden</th>
                        <th class="px-4 py-3">Tipo / Origen</th>
                        <th class="px-4 py-3">Entidad / Ubicación</th>
                        <th class="px-4 py-3">Inspector Asignado</th>
                        <th class="px-4 py-3">Estado</th>
                        <th class="px-4 py-3 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($asignacionesPendientes as $item)
                        @php
                            $isDirectorOrAdmin = in_array($rol, ['director', 'director_ambiente', 'director_residuos', 'admin']);
                            $assignedInspectorId =$item->inspector_id ?? null;
                            $esAsignadoAlUsuario = ($assignedInspectorId ===$currentInspectorId);
                            
                            // Directores y admins ven todo, pero solo ejecutan si está asignado a ellos o si son Admin total
                            $puedeEjecutar = !$isDirectorOrAdmin || $esAsignadoAlUsuario || in_array($rol, ['admin', 'administrador']);

                            // Variables para filtrado Alpine.js
                            $codigoTexto = strtolower($item->codigo_orden ?? $item->codigo_expediente ?? $item->codigo ?? '');
                            $entidadTexto = strtolower(isset($item->empresa) ?$item->empresa->razon_social . ' ' . $item->empresa->rif : ($item->sector ?? ''));
                            $tipoClave = isset($item->codigo_orden) ? 'orden' : (isset($item->tipo_tramite) ? 'solicitud' : 'denuncia');$estadoValor = $item->estado ?? $item->estatus ?? 'pendiente';
                        @endphp
                        <tr class="hover:bg-slate-50"
                            x-show="(search === '' || '{{ $codigoTexto }}'.includes(search.toLowerCase()) \vert{}\vert{} '{{$entidadTexto }}'.includes(search.toLowerCase())) &&
                                     (tipoFiltro === 'todos' || tipoFiltro === '{{ $tipoClave }}') &&
                                     (estadoFiltro === 'todos' || estadoFiltro === '{{ $estadoValor }}')">
                            <td class="px-4 py-3 font-medium text-slate-900 font-mono">
                                {{ $item->codigo_orden ?? $item->codigo_expediente ?? $item->codigo ?? 'N/A' }}
                            </td>
                            <td class="px-4 py-3">
                                @if(isset($item->codigo_orden))
                                    <span class="px-2 py-1 rounded-md text-xs font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                        Fiscalización Residuos: {{ $item->tipo_inspeccion }}
                                    </span>
                                @elseif(isset($item->tipo_tramite))
                                    <span class="px-2 py-1 rounded-md text-xs font-semibold bg-blue-50 text-blue-700">
                                        Solicitud: {{ str_replace('_', ' ', $item->tipo_tramite) }}
                                    </span>
                                @else
                                    <span class="px-2 py-1 rounded-md text-xs font-semibold bg-red-50 text-red-700">
                                        Denuncia: {{ $item->tipo_contaminacion }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @if(isset($item->empresa))
                                    <div class="font-bold text-slate-800">{{ $item->empresa->razon_social }}</div>
                                    <div class="text-xs text-slate-400">RIF: {{ $item->empresa->rif }}</div>
                                @else
                                    {{ $item->sector ?? 'N/A' }}, {{ $item->comuna ?? '' }}
                                @endif
                            </td>
                            <td class="px-4 py-3 text-xs">
                                @if($item->inspector)
                                    <span class="font-medium text-slate-700">
                                        {{ $item->inspector->nombre ?? $item->inspector->user->nombre ?? $item->inspector->name ?? 'Funcionario' }} 
                                        {{ $item->inspector->apellido ?? $item->inspector->user->apellido ?? '' }}
                                    </span>
                                @else
                                    <span class="text-amber-600 font-semibold">Sin asignar</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @php
                                    $statusClasses = match($estadoValor) {
                                        'en_inspeccion' => 'bg-indigo-100 text-indigo-800 border border-indigo-200',
                                        'requiere_reinspeccion' => 'bg-purple-100 text-purple-800 border border-purple-200',
                                        'inspector_asignado' => 'bg-sky-100 text-sky-800',
                                        'pendiente' => 'bg-amber-100 text-amber-800',
                                        default => 'bg-slate-100 text-slate-800'
                                    };
                                @endphp
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusClasses }}">
                                    {{ ucfirst(str_replace('_', ' ', $estadoValor)) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                @if($puedeEjecutar)
                                    <div class="flex items-center justify-end gap-2">
                                        @if(in_array($estadoValor, ['inspector_asignado', 'requiere_reinspeccion']))
                                            <form action="{{ route('inspecciones.iniciar-revision', $item->id) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="inline-flex items-center gap-1 bg-indigo-600 hover:bg-indigo-700 text-white text-xs px-3 py-1.5 rounded-lg transition">
                                                    {{ $estadoValor === 'requiere_reinspeccion' ? 'Iniciar Reinspección' : 'Iniciar Revisión' }}
                                                </button>
                                            </form>
                                        @endif

                                        <a href="{{ route('inspecciones.evaluar', $item->id) }}" 
                                        class="inline-flex items-center gap-1 bg-emerald-600 hover:bg-emerald-700 text-white text-xs px-3 py-1.5 rounded-lg transition">
                                            Emitir Veredicto
                                        </a>
                                    </div>
                                @else
                                    <span class="text-xs text-slate-400 italic bg-slate-100 px-2.5 py-1 rounded-md inline-block">
                                        Solo supervisión (Asignado a otro funcionario)
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-6 text-center text-slate-400">
                                No hay expedientes pendientes de visita o evaluación en este momento.
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
                        <th class="px-4 py-3">Entidad / Origen</th>
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
                                {{ \Carbon\Carbon::parse($inspeccion->fecha_visita)->format('d/m/Y') }}
                            </td>
                            <td class="px-4 py-3">
                                {{ $inspeccion->ordenInspeccion->empresa->razon_social ?? $inspeccion->empresa->razon_social ?? 'Trámite / Denuncia General' }}
                            </td>
                            <td class="px-4 py-3">
                                {{ $inspeccion->inspector->nombre ?? 'N/A' }} {{ $inspeccion->inspector->apellido ?? '' }}
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
                            <td colspan="6" class="px-4 py-6 text-center text-slate-400">
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