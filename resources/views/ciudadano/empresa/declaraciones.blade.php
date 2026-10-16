<x-layouts.app title="Declaraciones de Residuos - Ecosocialismo" active="empresa_declaraciones">
    <div class="p-6 max-w-7xl mx-auto space-y-6 bg-slate-50 min-h-screen">
        
        <!-- Header y Botón Acción -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-800 tracking-tight">Declaraciones de Residuos</h1>
                <p class="text-sm text-slate-500">
                    Empresa: <span class="font-semibold text-slate-700">{{ $empresa->razon_social ?? $empresa->nombre_comercial }}</span> 
                    <span class="font-mono text-xs text-slate-400">({{ $empresa->rif }})</span>
                </p>
            </div>
            <div>
                <a href="{{ route('ciudadano.declaraciones.create') }}" class="py-2.5 px-4 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white font-medium text-sm rounded-xl shadow-sm transition-colors inline-flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    <span>Nueva Declaración</span>
                </a>
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

        @if(session('warning'))
            <div class="p-4 rounded-xl bg-amber-50 border border-amber-200 text-amber-800 text-sm flex items-center gap-3 shadow-sm">
                <svg class="w-5 h-5 shrink-0 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <span>{{ session('warning') }}</span>
            </div>
        @endif

        <!-- Tarjetas de Resumen Acumulado -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm flex items-center gap-4">
                <div class="p-3 bg-slate-100 text-slate-600 rounded-xl">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Comunes Generados</p>
                    <p class="text-xl font-bold text-slate-800 font-mono">{{ number_format($declaraciones->getCollection()->sum('toneladas_comunes'), 2) }} <span class="text-xs font-normal text-slate-400">Ton</span></p>
                </div>
            </div>

            <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm flex items-center gap-4">
                <div class="p-3 bg-emerald-50 text-emerald-600 rounded-xl">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Reciclables / Recupe.</p>
                    <p class="text-xl font-bold text-slate-800 font-mono">{{ number_format($declaraciones->getCollection()->sum('toneladas_reciclables'), 2) }} <span class="text-xs font-normal text-slate-400">Ton</span></p>
                </div>
            </div>

            <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm flex items-center gap-4">
                <div class="p-3 bg-amber-50 text-amber-600 rounded-xl">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Peligrosos / Especiales</p>
                    <p class="text-xl font-bold text-slate-800 font-mono">{{ number_format($declaraciones->getCollection()->sum('toneladas_peligrosas'), 2) }} <span class="text-xs font-normal text-slate-400">Ton</span></p>
                </div>
            </div>
        </div>

        <!-- Tabla de Declaraciones -->
        <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="bg-slate-100 text-xs uppercase text-slate-600 border-b border-slate-200 font-bold tracking-wider">
                        <tr>
                            <th scope="col" class="px-6 py-4">Período / Rango</th>
                            <th scope="col" class="px-6 py-4 text-center">Desglose (Ton)</th>
                            <th scope="col" class="px-6 py-4 text-center">Total (Ton)</th>
                            <th scope="col" class="px-6 py-4 text-center">Ítems</th>
                            <th scope="col" class="px-6 py-4">Soportes</th>
                            <th scope="col" class="px-6 py-4">Estatus</th>
                            <th scope="col" class="px-6 py-4 text-right">Acción</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @forelse($declaraciones as $dec)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="font-bold text-slate-800 font-mono">{{ $dec->periodo }}</div>
                                    <div class="text-xs text-slate-400">
                                        {{ \Carbon\Carbon::parse($dec->fecha_inicio_periodo)->format('d/m/Y') }} al {{ \Carbon\Carbon::parse($dec->fecha_fin_periodo)->format('d/m/Y') }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="text-xs space-y-0.5 font-mono">
                                        <span class="text-slate-600">Org: <b>{{ number_format($dec->toneladas_comunes, 2) }}</b></span> | 
                                        <span class="text-emerald-600">Rec: <b>{{ number_format($dec->toneladas_reciclables, 2) }}</b></span> | 
                                        <span class="text-amber-600">Pel: <b>{{ number_format($dec->toneladas_peligrosas, 2) }}</b></span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center font-bold font-mono text-slate-800">
                                    {{ number_format($dec->toneladas_comunes + $dec->toneladas_reciclables + $dec->toneladas_peligrosas, 2) }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-slate-100 text-slate-600">
                                        {{ $dec->detalles->count() }} renglón(es)
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-xs">
                                    @if(!empty($dec->manifiestos_archivos) && count($dec->manifiestos_archivos) > 0)
                                        <span class="inline-flex items-center gap-1 text-emerald-700 bg-emerald-50 border border-emerald-200 px-2.5 py-1 rounded-lg font-medium">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                                            </svg>
                                            {{ count($dec->manifiestos_archivos) }} Archivo(s)
                                        </span>
                                    @else
                                        <span class="text-slate-400 italic">Sin adjuntos</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        $badgeClasses = match($dec->estatus) {
                                            'CONFORME'  => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                            'OBSERVADA' => 'bg-amber-50 text-amber-700 border-amber-200',
                                            'BORRADOR'  => 'bg-slate-100 text-slate-600 border-slate-200',
                                            default     => 'bg-blue-50 text-blue-700 border-blue-200', // PRESENTADA
                                        };
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold border {{ $badgeClasses }}">
                                        {{ ucfirst(strtolower($dec->estatus)) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    @if($dec->observaciones_fiscalia)
                                        <button type="button" title="Ver Observaciones" 
                                            onclick="alert('Observaciones de la Dirección:\n\n{{ addslashes($dec->observaciones_fiscalia) }}')"
                                            class="p-1.5 bg-amber-50 hover:bg-amber-100 text-amber-700 rounded-lg transition-colors border border-amber-200">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                        </button>
                                    @else
                                        <span class="text-xs text-slate-400">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-slate-400">
                                    <p class="text-sm">No se han registrado declaraciones de residuos para esta empresa.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            @if($declaraciones->hasPages())
                <div class="p-4 border-t border-slate-200 bg-slate-50">
                    {{ $declaraciones->links() }}
                </div>
            @endif
        </div>

    </div>
</x-layouts.app>