<x-layouts.app title="Gestión de Denuncias" active="ambiente_reportes">
<div class="p-6 max-w-7xl mx-auto space-y-6" x-data="{ modalOpen: false, modalAsignarOpen: false, denunciaActual: null }">

    <!-- ALERTAS DE NOTIFICACIÓN DE SESIÓN -->
    @if(session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-xl flex items-center justify-between text-emerald-800 text-sm shadow-sm" role="alert">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>{{ session('success') }}</span>
            </div>
            <button type="button" @click="$el.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="p-4 bg-rose-50 border border-rose-200 rounded-xl flex items-center justify-between text-rose-800 text-sm shadow-sm" role="alert">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2 text-rose-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>{{ session('error') }}</span>
            </div>
            <button type="button" @click="$el.parentElement.remove()" class="text-rose-500 hover:text-rose-700">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
    @endif

    <!-- Encabezado -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Reportes de Denuncias Ambientales</h1>
            <p class="text-sm text-slate-500">Gestión y seguimiento de incidencias reportadas por los ciudadanos</p>
        </div>

        <a href="{{ route('director.ambiente.reportes.export') }}" class="inline-flex items-center justify-center px-4 py-2 bg-slate-800 text-white text-xs font-medium rounded-xl hover:bg-slate-700 transition self-start md:self-auto shadow-sm">
            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            Exportar Reporte
        </a>
    </div>

    <!-- Cards de Estadísticas -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase">Total Denuncias</p>
                <p class="text-2xl font-bold text-slate-800 mt-1">{{ number_format($stats['total_denuncias'] ?? 0) }}</p>
            </div>
            <div class="p-2.5 bg-slate-100 text-slate-600 rounded-xl">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            </div>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-amber-600 uppercase">Pendientes</p>
                <p class="text-2xl font-bold text-amber-600 mt-1">{{ number_format($stats['pendientes'] ?? 0) }}</p>
            </div>
            <div class="p-2.5 bg-amber-50 text-amber-600 rounded-xl">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-purple-600 uppercase">En Inspección (Mes)</p>
                <p class="text-2xl font-bold text-purple-600 mt-1">{{ number_format($stats['inspecciones_mes'] ?? 0) }}</p>
            </div>
            <div class="p-2.5 bg-purple-50 text-purple-600 rounded-xl">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-emerald-600 uppercase">Atendidas</p>
                <p class="text-2xl font-bold text-emerald-600 mt-1">{{ number_format($stats['atendidas'] ?? 0) }}</p>
            </div>
            <div class="p-2.5 bg-emerald-50 text-emerald-600 rounded-xl">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            </div>
        </div>
    </div>

    <!-- Barra de Filtro Manual -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm">
        <form method="GET" action="{{ route('director.ambiente.reportes') }}" class="flex items-center gap-3">
            <label for="filtro_estatus" class="text-xs font-semibold text-slate-500 whitespace-nowrap">Filtrar por estatus:</label>
            <select 
                id="filtro_estatus"
                name="estatus"
                class="bg-white border border-slate-200 text-slate-800 text-xs rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 block p-2 font-medium cursor-pointer"
            >
                <option value="todos" {{ ($filtroEstatus ?? request('estatus')) == 'todos' || !request('estatus') ? 'selected' : '' }}>Todos los estatus</option>
                <option value="pendiente" {{ ($filtroEstatus ?? request('estatus')) == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                <option value="inspector_asignado" {{ ($filtroEstatus ?? request('estatus')) == 'inspector_asignado' ? 'selected' : '' }}>Inspector Asignado</option>
                <option value="en_revision" {{ ($filtroEstatus ?? request('estatus')) == 'en_revision' ? 'selected' : '' }}>En Revisión</option>
                <option value="atendido" {{ ($filtroEstatus ?? request('estatus')) == 'atendido' ? 'selected' : '' }}>Atendido</option>
                <option value="desestimado" {{ ($filtroEstatus ?? request('estatus')) == 'desestimado' ? 'selected' : '' }}>Desestimado</option>
            </select>

            <button type="submit" class="px-3.5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-medium rounded-xl transition shadow-sm">
                Filtrar
            </button>
        </form>
    </div>

    <!-- TABLA DE DENUNCIAS -->
    <div class="bg-white rounded-2xl border border-gray-200 shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th scope="col" class="px-6 py-3 whitespace-nowrap">Código</th>
                        <th scope="col" class="px-6 py-3 whitespace-nowrap">Tipo Contaminación</th>
                        <th scope="col" class="px-6 py-3 whitespace-nowrap">Sector / Comuna</th>
                        <th scope="col" class="px-6 py-3 text-center whitespace-nowrap">Gravedad</th>
                        <th scope="col" class="px-6 py-3 whitespace-nowrap">Estatus</th>
                        <th scope="col" class="px-6 py-3 whitespace-nowrap">Inspector Asignado</th>
                        <th scope="col" class="px-6 py-3 whitespace-nowrap">Fecha</th>
                        <th scope="col" class="px-6 py-3 text-right whitespace-nowrap">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($denuncias as $denuncia)
                        <tr class="bg-white border-b hover:bg-gray-50 transition">
                            <td class="px-6 py-4 font-bold text-emerald-700 whitespace-nowrap">
                                {{ $denuncia->codigo }}
                            </td>
                            <td class="px-6 py-4 capitalize font-medium text-gray-800 whitespace-nowrap">
                                {{ strtolower($denuncia->tipo_contaminacion) === 'otro' ? ($denuncia->otro_tipo_contaminacion ?? 'Otro') : str_replace('_', ' ', $denuncia->tipo_contaminacion) }}
                            </td>
                            <td class="px-6 py-4 max-w-[240px]" title="{{ $denuncia->sector }} ({{ $denuncia->comuna }})">
                                <div class="font-medium text-gray-800 truncate">{{ $denuncia->sector }}</div>
                                <div class="text-xs text-gray-400 truncate">{{ $denuncia->comuna }}</div>
                            </td>

                            <!-- BADGE DE GRAVEDAD -->
                            <td class="px-6 py-4 text-center whitespace-nowrap">
                                @php
                                    $gravedad = strtolower($denuncia->nivel_gravedad);
                                    $badgeClasses = match($gravedad) {
                                        'baja' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                                        'media' => 'bg-amber-100 text-amber-800 border-amber-200',
                                        'alta' => 'bg-rose-100 text-rose-800 border-rose-200 font-bold',
                                        default => 'bg-gray-100 text-gray-800 border-gray-200',
                                    };
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border {{ $badgeClasses }}">
                                    <span class="w-1.5 h-1.5 mr-1.5 rounded-full {{ $gravedad === 'alta' ? 'bg-rose-500' : ($gravedad === 'media' ? 'bg-amber-500' : 'bg-emerald-500') }}"></span>
                                    {{ ucfirst($gravedad) }}
                                </span>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-xs px-2.5 py-1 rounded-md bg-slate-100 text-slate-700 capitalize font-medium">
                                    {{ str_replace('_', ' ', $denuncia->estatus) }}
                                </span>
                            </td>

                            <!-- COLUMNA: INSPECTOR ASIGNADO -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($denuncia->inspector && $denuncia->inspector->user)
                                    <div class="flex items-center gap-1.5 text-xs text-indigo-700 bg-indigo-50 border border-indigo-100 px-2.5 py-1 rounded-md w-fit font-medium">
                                        <svg class="w-3.5 h-3.5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                        <span>{{ $denuncia->inspector->user->nombre ?? $denuncia->inspector->user->name }}</span>
                                    </div>
                                @else
                                    <span class="text-xs text-gray-400 italic">Sin Asignar</span>
                                @endif
                            </td>

                            <td class="px-6 py-4 text-xs text-gray-500 whitespace-nowrap">
                                {{ $denuncia->created_at ? $denuncia->created_at->format('d/m/Y h:i A') : 'N/A' }}
                            </td>
                            <td class="px-6 py-4 text-right whitespace-nowrap space-x-2">
                                <button 
                                    @click="denunciaActual = {{ json_encode($denuncia->load(['inspector.user'])) }}; modalAsignarOpen = true"
                                    class="text-indigo-600 hover:text-indigo-900 font-medium text-xs bg-indigo-50 hover:bg-indigo-100 px-3 py-1.5 rounded-md transition-colors inline-flex items-center"
                                >
                                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                                    {{ $denuncia->inspector_id || $denuncia->inspector ? 'Reasignar' : 'Asignar Inspector' }}
                                </button>
                                <button 
                                    @click="denunciaActual = {{ json_encode($denuncia->load(['inspector.user'])) }}; modalOpen = true"
                                    class="text-emerald-600 hover:text-emerald-900 font-medium text-xs bg-emerald-50 hover:bg-emerald-100 px-3 py-1.5 rounded-md transition-colors inline-flex items-center"
                                >
                                    Ver Detalle
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-8 text-center text-gray-500">
                                No se encontraron denuncias registradas para el filtro seleccionado.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Paginación -->
        @if(method_exists($denuncias, 'links'))
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $denuncias->withQueryString()->links() }}
            </div>
        @endif
    </div>

    <!-- MODAL DE DETALLES DEL REPORTE -->
    <div 
        x-show="modalOpen" 
        x-cloak
        class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/50 backdrop-blur-sm flex items-center justify-center p-4"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
    >
        <div 
            @click.away="modalOpen = false"
            class="bg-white rounded-xl shadow-xl max-w-2xl w-full overflow-hidden transform transition-all border border-gray-100"
        >
            <!-- Cabecera Modal -->
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200 flex justify-between items-center">
                <div>
                    <h3 class="text-lg font-bold text-gray-800">
                        Detalle del Reporte: <span class="text-emerald-600" x-text="denunciaActual?.codigo"></span>
                    </h3>
                    <p class="text-xs text-gray-500" x-text="'Registrado el ' + (denunciaActual?.created_at ? new Date(denunciaActual.created_at).toLocaleString() : '')"></p>
                </div>
                <button @click="modalOpen = false" class="text-gray-400 hover:text-gray-600 focus:outline-none">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <!-- Cuerpo Modal -->
            <div class="p-6 space-y-4 max-h-[75vh] overflow-y-auto">
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-gray-50 p-3 rounded-lg border border-gray-100">
                        <span class="block text-xs text-gray-400 uppercase font-semibold">Tipo Contaminación</span>
                        <span class="text-sm font-medium text-gray-800 capitalize" 
                              x-text="denunciaActual?.tipo_contaminacion?.toLowerCase() === 'otro' ? (denunciaActual?.otro_tipo_contaminacion || 'Otro') : denunciaActual?.tipo_contaminacion?.replace('_', ' ')">
                        </span>
                    </div>
                    <div class="bg-gray-50 p-3 rounded-lg border border-gray-100">
                        <span class="block text-xs text-gray-400 uppercase font-semibold">Nivel de Gravedad</span>
                        <span class="text-sm font-bold capitalize" 
                              :class="{
                                  'text-emerald-600': denunciaActual?.nivel_gravedad === 'baja',
                                  'text-amber-600': denunciaActual?.nivel_gravedad === 'media',
                                  'text-rose-600': denunciaActual?.nivel_gravedad === 'alta'
                              }"
                              x-text="denunciaActual?.nivel_gravedad">
                        </span>
                    </div>
                </div>

                <!-- SECCIÓN: INSPECTOR ASIGNADO -->
                <div class="bg-indigo-50/60 p-3 rounded-lg border border-indigo-100 flex items-center justify-between">
                    <div>
                        <span class="block text-xs text-indigo-600 uppercase font-bold">Inspector Asignado</span>
                        <span class="text-sm font-medium text-indigo-950" 
                              x-text="denunciaActual?.inspector?.user ? (denunciaActual.inspector.user.nombre + ' ' + (denunciaActual.inspector.user.apellido || '')).trim() : (denunciaActual?.inspector?.user?.name || 'Ninguno (Sin asignar)')">
                        </span>
                    </div>
                    <span class="text-xs px-2.5 py-1 rounded-md bg-white border border-indigo-200 text-indigo-700 capitalize font-medium" x-text="denunciaActual?.estatus?.replace('_', ' ') || 'Sin Estatus'"></span>
                </div>

                <!-- Justificación de Gravedad (si existe) -->
                <template x-if="denunciaActual?.justificacion_gravedad">
                    <div class="bg-rose-50/60 p-3 rounded-lg border border-rose-100">
                        <span class="block text-xs text-rose-600 uppercase font-bold mb-1">Justificación de Gravedad Alta</span>
                        <p class="text-xs text-rose-900 leading-relaxed whitespace-pre-line" x-text="denunciaActual.justificacion_gravedad"></p>
                    </div>
                </template>

                <div>
                    <span class="block text-xs text-gray-400 uppercase font-semibold mb-1">Ubicación y Referencia</span>
                    <p class="text-sm text-gray-700 bg-gray-50 p-3 rounded-lg border border-gray-100">
                        <strong x-text="denunciaActual?.sector"></strong> (<span x-text="denunciaActual?.comuna"></span>)<br>
                        <span class="text-xs text-gray-500" x-text="'Punto de Ref: ' + (denunciaActual?.punto_referencia || 'N/A')"></span>
                    </p>
                </div>

                <div>
                    <span class="block text-xs text-gray-400 uppercase font-semibold mb-1">Descripción del Problema</span>
                    <p class="text-sm text-gray-700 bg-gray-50 p-3 rounded-lg border border-gray-100 leading-relaxed whitespace-pre-line" x-text="denunciaActual?.descripcion"></p>
                </div>

                <!-- Evidencias Adjuntas -->
                <template x-if="denunciaActual?.evidencias && denunciaActual.evidencias.length">
                    <div>
                        <span class="block text-xs text-gray-400 uppercase font-semibold mb-2">Evidencias Adjuntas</span>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <template x-for="(foto, index) in denunciaActual.evidencias" :key="index">
                                <a :href="'/storage/' + foto" target="_blank" class="group block border rounded-xl overflow-hidden bg-slate-900/5 hover:border-emerald-500 transition relative">
                                    <div class="h-48 w-full flex items-center justify-center p-2 bg-slate-100">
                                        <img :src="'/storage/' + foto" class="max-h-full max-w-full object-contain rounded-md" alt="Evidencia">
                                    </div>
                                    <div class="absolute inset-0 bg-slate-900/20 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                        <span class="bg-white/90 text-slate-800 text-xs font-semibold px-2.5 py-1 rounded-md shadow-sm">Ver tamaño completo</span>
                                    </div>
                                </a>
                            </template>
                        </div>
                    </div>
                </template>
            </div>

            <!-- Pie del Modal -->
            <div class="px-6 py-3 bg-gray-50 border-t border-gray-200 flex justify-between items-center">
                <button 
                    @click="modalOpen = false; modalAsignarOpen = true"
                    class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-medium rounded-lg transition-colors inline-flex items-center"
                >
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                    Asignar Inspector
                </button>
                <button @click="modalOpen = false" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 text-sm font-medium rounded-lg transition-colors">
                    Cerrar
                </button>
            </div>
        </div>
    </div>

    <!-- MODAL DE ASIGNAR INSPECTOR -->
    <div 
        x-show="modalAsignarOpen" 
        x-cloak
        class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/50 backdrop-blur-sm flex items-center justify-center p-4"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
    >
        <div 
            @click.away="modalAsignarOpen = false"
            class="bg-white rounded-xl shadow-xl max-w-md w-full overflow-hidden transform transition-all border border-gray-100"
        >
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200 flex justify-between items-center">
                <h3 class="text-base font-bold text-gray-800">
                    Asignar Inspector al Reporte <span class="text-indigo-600" x-text="denunciaActual?.codigo"></span>
                </h3>
                <button @click="modalAsignarOpen = false" class="text-gray-400 hover:text-gray-600 focus:outline-none">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <form :action="'/director/ambiente/reportes/' + denunciaActual?.id + '/asignar'" method="POST" class="p-6 space-y-4">
                @csrf
                @method('PUT')

                <!-- RESUMEN ASIGNACIÓN PREVIA -->
                <div class="bg-gray-50 p-3 rounded-lg border border-gray-200 text-xs text-gray-600 space-y-1">
                    <div class="flex justify-between">
                        <span>Inspector Actual:</span>
                        <strong class="text-indigo-600" 
                                x-text="denunciaActual?.inspector?.user ? (denunciaActual.inspector.user.nombre + ' ' + (denunciaActual.inspector.user.apellido || '')).trim() : (denunciaActual?.inspector?.user?.name || 'Ninguno')">
                        </strong>
                    </div>
                </div>

                <div>
                    <label for="user_id" class="block text-xs font-semibold text-gray-700 uppercase mb-2">Seleccionar Inspector</label>
                    <select name="user_id" id="user_id" class="w-full bg-white border border-slate-200 text-slate-800 text-xs rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 block p-2.5 font-medium cursor-pointer" required>
                        <option value="" disabled selected>Seleccione un funcionario...</option>
                        @foreach ($funcionarios as $funcionario)
                            <option value="{{ $funcionario->id }}">
                                {{ $funcionario->nombre }} {{ $funcionario->apellido }} — {{ ucfirst($funcionario->role->nombre ?? 'Funcionario') }}
                                @if(isset($funcionario->direccion))
                                    ({{ $funcionario->direccion->nombre_direc }})
                                @endif
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" @click="modalAsignarOpen = false" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 text-xs font-medium rounded-lg transition-colors">
                        Cancelar
                    </button>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-medium rounded-lg transition-colors shadow-sm">
                        Confirmar Asignación
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
</x-layouts.app>