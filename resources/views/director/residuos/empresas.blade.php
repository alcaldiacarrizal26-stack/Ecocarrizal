<x-layouts.app title="Empresas - Director de Residuos" active="residuos_empresas">
    <div class="p-6 max-w-7xl mx-auto space-y-6">
        
        <!-- Header / Título -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight">Gestión de Empresas</h1>
                <p class="text-sm text-gray-600 dark:text-slate-400">Revisión de expedientes comerciales y emisión de dictámenes ambientales.</p>
            </div>
        </div>

        <!-- Alertas de Flash Session -->
        @if(session('success'))
            <div class="p-4 rounded-xl bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/20 text-emerald-800 dark:text-emerald-400 text-sm flex items-center gap-3">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <!-- Tabla de Empresas -->
        <div class="bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-800 rounded-2xl overflow-hidden shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-gray-700 dark:text-slate-300">
                    <thead class="bg-gray-50 dark:bg-slate-950/60 text-xs uppercase text-gray-500 dark:text-slate-400 border-b border-gray-200 dark:border-slate-800 font-semibold tracking-wider">
                        <tr>
                            <th scope="col" class="px-6 py-4">Empresa / RIF</th>
                            <th scope="col" class="px-6 py-4">Representante</th>
                            <th scope="col" class="px-6 py-4">Estado Dictamen</th>
                            <th scope="col" class="px-6 py-4">Registro</th>
                            <th scope="col" class="px-6 py-4 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-slate-800/60">
                        @forelse($empresas as $empresa)
                            <tr class="hover:bg-gray-50 dark:hover:bg-slate-800/40 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="font-medium text-gray-900 dark:text-white">{{ $empresa->razon_social ?? $empresa->nombre_comercial }}</div>
                                    <div class="text-xs text-gray-500 dark:text-slate-400 font-mono">{{ $empresa->rif }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-medium text-gray-800 dark:text-slate-200">
                                        {{ trim(($empresa->user->nombre ?? '') . ' ' . ($empresa->user->apellido ?? '')) ?: 'N/A' }}
                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-slate-400">
                                        {{ $empresa->user->correo ?? 'Sin correo' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        $estado = strtolower($empresa->estado_evaluacion ?? 'pendiente');
                                        $badgeClasses = match($estado) {
                                            'aprobado' => 'bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border-emerald-200 dark:border-emerald-500/20',
                                            'rechazado' => 'bg-rose-50 dark:bg-rose-500/10 text-rose-700 dark:text-rose-400 border-rose-200 dark:border-rose-500/20',
                                            'observado' => 'bg-amber-50 dark:bg-amber-500/10 text-amber-700 dark:text-amber-400 border-amber-200 dark:border-amber-500/20',
                                            default => 'bg-gray-100 dark:bg-slate-800 text-gray-600 dark:text-slate-400 border-gray-200 dark:border-slate-700',
                                        };
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium border capitalize {{ $badgeClasses }}">
                                        {{ $estado }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-xs text-gray-500 dark:text-slate-400">
                                    {{ $empresa->created_at ? $empresa->created_at->format('d/m/Y') : 'N/A' }}
                                </td>
                                <td class="px-6 py-4 text-right space-x-2">
                                    <a href="{{ route('director.residuos.empresas.show', $empresa) }}" class="px-3 py-1.5 bg-gray-100 dark:bg-slate-700 text-gray-700 dark:text-slate-200 rounded text-xs hover:bg-gray-200 dark:hover:bg-slate-600 font-medium transition-colors">
                                        Ver Expediente
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-gray-500 dark:text-slate-500">
                                    No hay empresas registradas para evaluar en este momento.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            @if($empresas->hasPages())
                <div class="p-4 border-t border-gray-200 dark:border-slate-800 bg-gray-50 dark:bg-slate-950/40">
                    {{ $empresas->links() }}
                </div>
            @endif
        </div>

    </div>
</x-layouts.app>