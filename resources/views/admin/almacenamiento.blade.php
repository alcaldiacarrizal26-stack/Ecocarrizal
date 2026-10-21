<x-layouts.app title="Almacenamiento y Recursos" active="admin_almacenamiento">
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">
            {{ __('Supervisión de Almacenamiento y Recursos') }}
        </h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        
        <!-- Tarjetas Resumen por Disco -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @foreach($stats as $diskName => $data)
                <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-xl flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-mint uppercase tracking-wider">Disco: {{ strtoupper($diskName) }}</p>
                        <h3 class="text-3xl font-extrabold text-white mt-1">{{ $data['total_size_mb'] }} <span class="text-sm font-normal text-slate-400">MB</span></h3>
                        <p class="text-sm text-slate-400 mt-1">Archivos totales: <span class="font-semibold text-slate-200">{{ $data['total_files'] }}</span></p>
                    </div>
                    <div class="p-4 bg-slate-800/80 rounded-xl text-mint">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8-4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"/></svg>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Tabla de Desglose por Directorios -->
        <div class="bg-slate-900 border border-slate-800 rounded-2xl shadow-xl overflow-hidden">
            <div class="p-6 border-b border-slate-800">
                <h3 class="text-lg font-bold text-white">Desglose por Módulos Institucionales</h3>
                <p class="text-sm text-slate-400">Distribución de peso y cantidad de archivos en los repositorios de la Alcaldía.</p>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-800/50 text-slate-400 text-xs uppercase tracking-wider">
                            <th class="py-3.5 px-6 font-semibold">Módulo / Carpeta</th>
                            <th class="py-3.5 px-6 font-semibold">Disco</th>
                            <th class="py-3.5 px-6 font-semibold">Ruta Física</th>
                            <th class="py-3.5 px-6 font-semibold text-center">Nº Archivos</th>
                            <th class="py-3.5 px-6 font-semibold text-right">Peso Ocupado</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800 text-sm text-slate-300">
                        @foreach($folderStats as $folder)
                            <tr class="hover:bg-slate-800/30 transition-colors">
                                <td class="py-4 px-6 font-medium text-white">{{ $folder['label'] }}</td>
                                <td class="py-4 px-6">
                                    <span class="px-2.5 py-1 text-[10px] font-bold rounded-md bg-slate-800 text-mint border border-slate-700">
                                        {{ $folder['disk'] }}
                                    </span>
                                </td>
                                <td class="py-4 px-6 text-slate-400 font-mono text-xs">{{ $folder['path'] }}</td>
                                <td class="py-4 px-6 text-center font-semibold text-slate-200">{{ $folder['count'] }}</td>
                                <td class="py-4 px-6 text-right font-bold text-mint">{{ $folder['size_mb'] }} MB</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</x-layouts.app>