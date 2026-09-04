<x-layouts.app title="Panel Director de Residuos" active="inicio">
<div class="max-w-7xl mx-auto space-y-8">

    <!-- Encabezado / Saludo -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 pb-2 border-b border-slate-200">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 border border-emerald-300">
                    DIRECCIÓN DE RESIDUOS
                </span>
                <span class="text-xs font-medium text-slate-500">
                    {{ now()->isoFormat('LLLL') }}
                </span>
            </div>
            <h1 class="text-2xl font-bold text-slate-900">
                ¡Bienvenido, {{ auth()->user()->nombre }}!
            </h1>
            <p class="text-sm text-slate-600 mt-0.5">
                Gestión y revisión de declaraciones de adecuación ambiental para comercios.
            </p>
        </div>
    </div>

    <!-- Tarjetas de Estadísticas (Stats) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mt-6">
        <!-- Pendientes / Por Revisar -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-amber-600 uppercase tracking-wider">Por Revisar</p>
                <h3 class="text-2xl font-black text-slate-900 mt-1">{{ $stats['pendientes'] ?? 0 }}</h3>
            </div>
            <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 border border-amber-200 flex items-center justify-center font-bold text-lg">
                ⏳
            </div>
        </div>

        <!-- Aprobadas -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-emerald-600 uppercase tracking-wider">Aprobadas</p>
                <h3 class="text-2xl font-black text-slate-900 mt-1">{{ $stats['aprobadas'] ?? 0 }}</h3>
            </div>
            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 border border-emerald-200 flex items-center justify-center font-bold text-lg">
                ✓
            </div>
        </div>

        <!-- Rechazadas -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-rose-600 uppercase tracking-wider">Rechazadas</p>
                <h3 class="text-2xl font-black text-slate-900 mt-1">{{ $stats['rechazadas'] ?? 0 }}</h3>
            </div>
            <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 border border-rose-200 flex items-center justify-center font-bold text-lg">
                ✕
            </div>
        </div>

        <!-- Total Histórico -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Histórico</p>
                <h3 class="text-2xl font-black text-slate-900 mt-1">{{ $stats['total'] ?? 0 }}</h3>
            </div>
            <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-600 border border-slate-200 flex items-center justify-center font-bold text-lg">
                ∑
            </div>
        </div>
    </div>

    <!-- Tabla de Solicitudes Pendientes -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-gray-100 flex items-center justify-between">
            <div>
                <h3 class="font-bold text-gray-800 text-sm uppercase tracking-wide">Solicitudes de Registro Pendientes</h3>
                <p class="text-xs text-gray-400">Empresas en espera de dictamen técnico</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-gray-600">
                <thead class="bg-gray-50 text-gray-400 uppercase font-semibold border-b border-gray-100">
                    <tr>
                        <th class="p-4">Empresa / RIF</th>
                        <th class="p-4">Sector / Ubicación</th>
                        <th class="p-4">Rol Ambiental</th>
                        <th class="p-4">Estatus</th>
                        <th class="p-4 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($empresasPendientes as $empresa)
                        <tr class="hover:bg-gray-50/50 transition">
                            <td class="p-4">
                                <span class="font-bold text-gray-800 block text-sm">{{ $empresa->nombre_comercial }}</span>
                                <span class="text-gray-400 text-[11px]">{{ $empresa->razon_social }} | {{ $empresa->rif }}</span>
                            </td>
                            <td class="p-4">
                                <span class="text-gray-700 font-medium block">{{ $empresa->sector }}</span>
                                <span class="text-gray-400 text-[11px]">{{ $empresa->comuna }}</span>
                            </td>
                            <td class="p-4">
                                <span class="px-2.5 py-1 bg-emerald-50 text-[#0d5238] rounded-lg font-bold border border-emerald-100 text-[10px]">
                                    {{ $empresa->perfilAmbiental->rol_ambiental ?? 'N/A' }}
                                </span>
                            </td>
                            <td class="p-4">
                                <span class="px-2.5 py-1 bg-amber-50 text-amber-700 rounded-lg font-bold border border-amber-200/60 text-[10px]">
                                    PENDIENTE
                                </span>
                            </td>
                            <td class="p-4 text-center">
                                <button @click="openModal('{{ $empresa->id }}')" class="px-3 py-1.5 bg-[#0d5238] text-white rounded-xl font-bold hover:bg-[#093a27] transition shadow-xs">
                                    Revisar Empresa
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-8 text-center text-gray-400 italic">
                                No hay empresas pendientes por revisión en este momento.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
</x-layouts.app>