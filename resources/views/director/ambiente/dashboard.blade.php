<x-layouts.app title="Panel de Control - Dirección de Ambiente">
    <div class="p-6 space-y-6 bg-slate-50 min-h-screen">

        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Panel de Dirección de Ambiente</h1>
                <p class="text-sm text-slate-500">Control de trámites ambientales, denuncias, autorizaciones e inspecciones en Carrizal.</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('director.ambiente.solicitudes') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-sm rounded-xl transition-all shadow-md shadow-emerald-600/20">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>Gestionar Trámites</span>
                </a>
            </div>
        </div>

        <!-- KPIs Principales -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="p-5 bg-white rounded-2xl border border-slate-200 shadow-sm">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Solicitudes</span>
                    <div class="p-2 rounded-xl bg-emerald-50 text-emerald-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                </div>
                <div class="mt-3 flex items-baseline gap-2">
                    <span class="text-2xl font-bold text-slate-900">{{ $stats['total_solicitudes'] ?? 0 }}</span>
                    <span class="text-xs text-slate-500">tramitaciones</span>
                </div>
            </div>

            <div class="p-5 bg-white rounded-2xl border border-slate-200 shadow-sm">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Solicitudes Pendientes</span>
                    <div class="p-2 rounded-xl bg-amber-50 text-amber-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                </div>
                <div class="mt-3 flex items-baseline gap-2">
                    <span class="text-2xl font-bold text-slate-900">{{ $stats['solicitudes_pendientes'] ?? 0 }}</span>
                    <span class="text-xs text-amber-600 font-medium">por atender</span>
                </div>
            </div>

            <div class="p-5 bg-white rounded-2xl border border-slate-200 shadow-sm">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Denuncias</span>
                    <div class="p-2 rounded-xl bg-indigo-50 text-indigo-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882T19.24 5a1 1 0 01.76 1.05v10.1A1 1 0 0119.06 17l-7.22-.96a2 2 0 00-.56 0L4 17a1 1 0 01-1.06-.95V6.05A1 1 0 013.9 5a25.32 25.32 0 017.1-2.118z"/></svg>
                    </div>
                </div>
                <div class="mt-3 flex items-baseline gap-2">
                    <span class="text-2xl font-bold text-slate-900">{{ $stats['total_denuncias'] ?? 0 }}</span>
                    <span class="text-xs text-slate-500">reportes comunitarios</span>
                </div>
            </div>

            <div class="p-5 bg-white rounded-2xl border border-slate-200 shadow-sm">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Denuncias Críticas</span>
                    <div class="p-2 rounded-xl bg-rose-50 text-rose-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </div>
                </div>
                <div class="mt-3 flex items-baseline gap-2">
                    <span class="text-2xl font-bold text-slate-900">{{ $stats['denuncias_altas'] ?? 0 }}</span>
                    <span class="text-xs text-rose-600 font-medium">prioridad alta</span>
                </div>
            </div>
        </div>

        <!-- Sección de Atención Inmediata / Alertas -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            
            <!-- Cuadro: Denuncias de Gravedad Alta -->
            <div class="bg-white rounded-2xl border border-rose-200 p-5 shadow-sm">
                <div class="flex items-center justify-between pb-3 mb-4 border-b border-slate-100">
                    <div class="flex items-center gap-2">
                        <span class="flex h-2.5 w-2.5 rounded-full bg-rose-500 animate-pulse"></span>
                        <h2 class="text-base font-bold text-slate-900">Denuncias de Gravedad Alta</h2>
                    </div>
                    <span class="text-xs font-semibold px-2 py-0.5 rounded-full bg-rose-50 text-rose-600">Requieren Urgencia</span>
                </div>
                <div class="space-y-3">
                    @forelse($denunciasUrgentes ?? [] as $denuncia)
                        <div class="p-3 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-between">
                            <div>
                                <h4 class="text-xs font-bold text-slate-900">{{ $denuncia->codigo }} - {{ $denuncia->tipo_contaminacion }}</h4>
                                <p class="text-[11px] text-slate-500 truncate max-w-[250px]">{{ $denuncia->sector }} · {{ $denuncia->created_at->diffForHumans() }}</p>
                            </div>
                            <a href="{{ route('director.ambiente.denuncias.show', $denuncia->id) }}" class="px-3 py-1 bg-rose-600 hover:bg-rose-500 text-white text-xs font-semibold rounded-lg transition-all">
                                Atender
                            </a>
                        </div>
                    @empty
                        <p class="text-xs text-slate-400 text-center py-6">No hay denuncias de gravedad alta pendientes de atención.</p>
                    @endforelse
                </div>
            </div>

            <!-- Cuadro: Solicitudes Más Antiguas en Pendiente -->
            <div class="bg-white rounded-2xl border border-amber-200 p-5 shadow-sm">
                <div class="flex items-center justify-between pb-3 mb-4 border-b border-slate-100">
                    <div class="flex items-center gap-2">
                        <span class="flex h-2.5 w-2.5 rounded-full bg-amber-500"></span>
                        <h2 class="text-base font-bold text-slate-900">Solicitudes Antiguas Pendientes</h2>
                    </div>
                    <span class="text-xs font-semibold px-2 py-0.5 rounded-full bg-amber-50 text-amber-600">Por Asignar/Revisar</span>
                </div>
                <div class="space-y-3">
                    @forelse($solicitudesAntiguas ?? [] as $solicitud)
                        <div class="p-3 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-between">
                            <div>
                                <h4 class="text-xs font-bold text-slate-900">{{ $solicitud->codigo_expediente }} · <span class="capitalize font-normal">{{ str_replace('_', ' ', $solicitud->tipo_tramite) }}</span></h4>
                                <p class="text-[11px] text-slate-500">{{ $solicitud->sector }} · Espera: <span class="text-amber-600 font-semibold">{{ $solicitud->created_at->diffForHumans() }}</span></p>
                            </div>
                            <a href="{{ route('director.ambiente.solicitudes.show', $solicitud->id) }}" class="px-3 py-1 bg-slate-200 hover:bg-emerald-600 hover:text-white text-slate-700 text-xs font-semibold rounded-lg transition-all">
                                Revisar
                            </a>
                        </div>
                    @empty
                        <p class="text-xs text-slate-400 text-center py-6">No hay solicitudes acumuladas en estado pendiente.</p>
                    @endforelse
                </div>
            </div>

        </div>

        <!-- Gráficos Estadísticos Categorizados -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            <!-- Gráfico: Tipos de Solicitudes -->
            <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
                <h2 class="text-base font-bold text-slate-900 mb-1">Distribución de Solicitudes</h2>
                <p class="text-xs text-slate-500 mb-4">Clasificación por tipo de trámite registrado en el municipio.</p>
                <div class="h-64 flex justify-center items-center">
                    <canvas id="solicitudesChart"></canvas>
                </div>
            </div>

            <!-- Gráfico: Niveles de Gravedad en Denuncias -->
            <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
                <h2 class="text-base font-bold text-slate-900 mb-1">Categorización de Denuncias</h2>
                <p class="text-xs text-slate-500 mb-4">Proporción de reportes clasificados por nivel de severidad.</p>
                <div class="h-64 flex justify-center items-center">
                    <canvas id="denunciasChart"></canvas>
                </div>
            </div>

        </div>

        <!-- Cuerpo Técnico / Equipo de Inspectores -->
        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
            <div class="flex items-center justify-between pb-4 mb-4 border-b border-slate-100">
                <div>
                    <h2 class="text-base font-bold text-slate-900">Cuerpo de Inspección Activo</h2>
                    <p class="text-xs text-slate-500">Personal disponible para inspecciones y fiscalizaciones en terreno.</p>
                </div>
                <a href="{{ route('director.inspectores.index') }}" class="text-xs font-semibold text-emerald-600 hover:underline">Gestionar Equipo &rarr;</a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                @forelse($inspectores ?? [] as $inspector)
                    <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50 border border-slate-100">
                        <div class="flex items-center gap-3">
                            <div class="h-9 w-9 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-xs">
                                {{ substr($inspector->user->nombre ?? 'I', 0, 1) }}{{ substr($inspector->user->apellido ?? 'A', 0, 1) }}
                            </div>
                            <div>
                                <h4 class="text-xs font-semibold text-slate-900">{{ $inspector->user->nombre }} {{ $inspector->user->apellido }}</h4>
                                <p class="text-[11px] text-slate-500">{{ $inspector->especialidad ?? 'General' }}</p>
                            </div>
                        </div>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $inspector->disponible ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                            {{ $inspector->disponible ? 'Disponible' : 'Ocupado' }}
                        </span>
                    </div>
                @empty
                    <p class="text-xs text-slate-400 text-center col-span-full py-4">No se han registrado inspectores en el equipo.</p>
                @endforelse
            </div>
        </div>

    </div>

    <!-- Integración Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Ajuste del color de las etiquetas de los gráficos a un gris oscuro legible sobre blanco
            const labelColor = '#64748b';

            // Datos Solicitudes por Tipo
            const solicitudesCtx = document.getElementById('solicitudesChart').getContext('2d');
            new Chart(solicitudesCtx, {
                type: 'doughnut',
                data: {
                    labels: {!! json_encode($categoriasSolicitudes['labels'] ?? ['Tala / Poda', 'Ocupación Vegetal', 'Evaluación Riesgo', 'Otros']) !!},
                    datasets: [{
                        data: {!! json_encode($categoriasSolicitudes['values'] ?? [0, 0, 0, 0]) !!},
                        backgroundColor: ['#059669', '#10b981', '#34d399', '#a7f3d0'],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom', labels: { boxWidth: 12, color: labelColor } }
                    }
                }
            });

            // Datos Denuncias por Gravedad
            const denunciasCtx = document.getElementById('denunciasChart').getContext('2d');
            new Chart(denunciasCtx, {
                type: 'doughnut',
                data: {
                    labels: {!! json_encode($gravedadDenuncias['labels'] ?? ['Alta', 'Media', 'Baja']) !!},
                    datasets: [{
                        data: {!! json_encode($gravedadDenuncias['values'] ?? [0, 0, 0]) !!},
                        backgroundColor: ['#e11d48', '#f59e0b', '#3b82f6'],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom', labels: { boxWidth: 12, color: labelColor } }
                    }
                }
            });
        });
    </script>
</x-layouts.app>