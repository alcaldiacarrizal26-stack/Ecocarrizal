<x-layouts.app title="Empresas - Director de Residuos" active="residuos_empresas">
    <div class="p-6 max-w-7xl mx-auto space-y-6 bg-slate-50 min-h-screen">
        
        <!-- Header / Título -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-800 tracking-tight">Gestión de Empresas</h1>
                <p class="text-sm text-slate-500">Revisión de expedientes comerciales y emisión de dictámenes ambientales.</p>
            </div>
        </div>

        <!-- Alertas de Flash Session -->
        @if(session('success'))
            <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center gap-3 shadow-sm">
                <svg class="w-5 h-5 shrink-0 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <!-- Filtros y Búsqueda -->
        <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm">
            <form method="GET" action="{{ route('director.residuos.empresas.index') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label for="buscar" class="block text-xs font-semibold text-slate-700 mb-1.5">Buscar Empresa / RIF</label>
                    <input 
                        type="text" 
                        name="buscar" 
                        id="buscar" 
                        value="{{ request('buscar') }}" 
                        placeholder="Ej. J-12345678-0 o Nombre..." 
                        class="w-full text-sm rounded-xl border border-slate-300 bg-slate-50 text-slate-800 placeholder-slate-400 focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all"
                    >
                </div>

                <div>
                    <label for="estatus" class="block text-xs font-semibold text-slate-700 mb-1.5">Estatus del Dictamen</label>
                    <select 
                        name="estatus" 
                        id="estatus" 
                        class="w-full text-sm rounded-xl border border-slate-300 bg-slate-50 text-slate-800 focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all"
                    >
                        <option value="">Todos los estatus</option>
                        <option value="PENDIENTE" {{ request('estatus') === 'PENDIENTE' ? 'selected' : '' }}>Pendiente</option>
                        <option value="APROBADO" {{ request('estatus') === 'APROBADO' ? 'selected' : '' }}>Aprobado</option>
                        <option value="OBSERVADO" {{ request('estatus') === 'OBSERVADO' ? 'selected' : '' }}>Observado</option>
                        <option value="RECHAZADO" {{ request('estatus') === 'RECHAZADO' ? 'selected' : '' }}>Rechazado</option>
                    </select>
                </div>

                <div class="flex items-end gap-2">
                    <button type="submit" class="w-full py-2.5 px-4 bg-emerald-600 hover:bg-emerald-700 text-white font-medium text-sm rounded-xl shadow-sm transition-colors">
                        Filtrar
                    </button>
                    @if(request()->hasAny(['buscar', 'estatus']))
                        <a href="{{ route('director.residuos.empresas.index') }}" class="py-2.5 px-3 bg-slate-100 hover:bg-slate-200 text-slate-600 text-sm font-medium rounded-xl transition-colors">
                            Limpiar
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Tabla de Empresas -->
        <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="bg-slate-100 text-xs uppercase text-slate-600 border-b border-slate-200 font-bold tracking-wider">
                        <tr>
                            <th scope="col" class="px-6 py-4">Empresa / RIF</th>
                            <th scope="col" class="px-6 py-4">Representante</th>
                            <th scope="col" class="px-6 py-4">Estado Dictamen</th>
                            <th scope="col" class="px-6 py-4">Registro</th>
                            <th scope="col" class="px-6 py-4 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @forelse($empresas as $empresa)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="font-bold text-slate-800">
                                        {{ Str::title($empresa->razon_social ?? $empresa->nombre_comercial) }}
                                    </div>
                                    <div class="text-xs text-slate-400 font-mono tracking-wide mt-0.5">{{ $empresa->rif }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-medium text-slate-700">
                                        {{ trim(($empresa->user->nombre ?? '') . ' ' . ($empresa->user->apellido ?? '')) ?: 'N/A' }}
                                    </div>
                                    <div class="text-xs text-slate-400">
                                        {{ $empresa->user->correo ?? $empresa->user->email ?? 'Sin correo' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        $estado = strtoupper($empresa->estatus ?? 'PENDIENTE');
                                        $badgeClasses = match($estado) {
                                            'APROBADO' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                            'RECHAZADO' => 'bg-rose-50 text-rose-700 border-rose-200',
                                            'OBSERVADO' => 'bg-amber-50 text-amber-700 border-amber-200',
                                            default => 'bg-slate-100 text-slate-600 border-slate-200',
                                        };
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold border capitalize {{ $badgeClasses }}">
                                        {{ strtolower($estado) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-xs text-slate-500">
                                    {{ $empresa->created_at ? $empresa->created_at->format('d/m/Y') : 'N/A' }}
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('director.residuos.empresas.show', $empresa) }}" class="px-3 py-1.5 bg-slate-800 hover:bg-slate-900 text-white rounded-lg text-xs font-medium transition-colors inline-flex items-center gap-1.5 shadow-sm">
                                        <span>Ver Expediente</span>
                                        <svg class="w-3.5 h-3.5 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                        </svg>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-slate-400">
                                    <p class="text-sm">No se encontraron empresas registradas con los criterios especificados.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            @if($empresas->hasPages())
                <div class="p-4 border-t border-slate-200 bg-slate-50">
                    {{ $empresas->links() }}
                </div>
            @endif
        </div>

    </div>
</x-layouts.app>