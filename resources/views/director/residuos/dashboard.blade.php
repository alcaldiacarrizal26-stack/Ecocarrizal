<x-layouts.app title="Panel - Director de Residuos" active="inicio">
<div class="max-w-7xl mx-auto space-y-8 pb-10">

    <!-- Encabezado / Saludo Institucional con Fondo Oscuro para Perfecto Contraste -->
    <div class="relative overflow-hidden bg-slate-900 rounded-3xl p-8 text-white shadow-xl border border-slate-800">
        <div class="absolute -right-10 -bottom-10 w-64 h-64 bg-emerald-500/10 rounded-full pointer-events-none blur-2xl"></div>
        
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="space-y-2">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-[11px] font-extrabold uppercase tracking-widest bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 backdrop-blur-md">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    Dirección de Ecología y Residuos Sólidos
                </div>
                <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-white">
                    ¡Bienvenido, {{ auth()->user()->nombre }}!
                </h1>
                <p class="text-xs sm:text-sm text-slate-300 font-medium max-w-2xl leading-relaxed">
                    Panel de control directivo para la validación, dictamen técnico y supervisión de expedientes comerciales y ambientales del Municipio Carrizal.
                </p>
            </div>
            
            <div class="bg-slate-800/80 backdrop-blur-md px-5 py-3 rounded-2xl border border-slate-700 text-right shrink-0 shadow-inner">
                <span class="text-[10px] text-emerald-400 uppercase font-bold tracking-wider block">Fecha Actual</span>
                <span class="text-xs font-semibold text-slate-100 capitalize">{{ now()->isoFormat('dddd, D [de] MMMM [de] YYYY') }}</span>
            </div>
        </div>
    </div>

    <!-- Tarjetas de Estadísticas (Sin logos, solo texto y cifras) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        
        <!-- Pendientes / Por Revisar -->
        <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-xl shadow-slate-100/80 flex flex-col justify-center transition-all duration-300 hover:-translate-y-1 hover:shadow-2xl">
            <p class="text-[11px] font-extrabold text-amber-600 uppercase tracking-widest mb-1">Por Revisar</p>
            <h3 class="text-3xl font-black text-slate-900 mb-0.5">{{ $stats['pendientes'] ?? 0 }}</h3>
            <span class="text-[10px] text-slate-400 font-medium">Expedientes en cola</span>
        </div>

        <!-- Aprobadas -->
        <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-xl shadow-slate-100/80 flex flex-col justify-center transition-all duration-300 hover:-translate-y-1 hover:shadow-2xl">
            <p class="text-[11px] font-extrabold text-emerald-600 uppercase tracking-widest mb-1">Aprobadas</p>
            <h3 class="text-3xl font-black text-slate-900 mb-0.5">{{ $stats['aprobadas'] ?? 0 }}</h3>
            <span class="text-[10px] text-slate-400 font-medium">Comercios validados</span>
        </div>

        <!-- Rechazadas -->
        <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-xl shadow-slate-100/80 flex flex-col justify-center transition-all duration-300 hover:-translate-y-1 hover:shadow-2xl">
            <p class="text-[11px] font-extrabold text-rose-600 uppercase tracking-widest mb-1">Rechazadas</p>
            <h3 class="text-3xl font-black text-slate-900 mb-0.5">{{ $stats['rechazadas'] ?? 0 }}</h3>
            <span class="text-[10px] text-slate-400 font-medium">Con observaciones</span>
        </div>

        <!-- Total Histórico -->
        <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-xl shadow-slate-100/80 flex flex-col justify-center transition-all duration-300 hover:-translate-y-1 hover:shadow-2xl">
            <p class="text-[11px] font-extrabold text-slate-500 uppercase tracking-widest mb-1">Total Histórico</p>
            <h3 class="text-3xl font-black text-slate-900 mb-0.5">{{ $stats['total'] ?? 0 }}</h3>
            <span class="text-[10px] text-slate-400 font-medium">Registros globales</span>
        </div>
    </div>

    <!-- Tabla de Solicitudes Pendientes (Estilo Moderno y Limpio) -->
    <div class="bg-white rounded-3xl border border-slate-100 shadow-xl shadow-slate-100/80 overflow-hidden">
        
        <!-- Cabecera de la Sección -->
        <div class="p-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-slate-50/50">
            <div>
                <h3 class="font-extrabold text-slate-800 text-sm uppercase tracking-wider">Solicitudes de Registro Pendientes</h3>
                <p class="text-xs text-slate-400 mt-0.5">Empresas comerciales en espera de dictamen técnico y revisión de recaudos</p>
            </div>
            <span class="px-3 py-1 bg-amber-100/80 text-amber-800 border border-amber-200 text-xs font-bold rounded-xl self-start sm:self-auto">
                {{ count($empresasPendientes) }} por atender
            </span>
        </div>

        <!-- Tabla Estilizada -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-100/70 text-slate-400 uppercase font-bold tracking-wider border-b border-slate-100">
                    <tr>
                        <th class="p-4.5 pl-6">Empresa / RIF</th>
                        <th class="p-4.5">Sector / Ubicación</th>
                        <th class="p-4.5">Rol Ambiental</th>
                        <th class="p-4.5">Estatus</th>
                        <th class="p-4.5 pr-6 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($empresasPendientes as $empresa)
                        <tr class="hover:bg-emerald-50/30 transition-colors duration-150">
                            <td class="p-4.5 pl-6">
                                <span class="font-bold text-slate-900 block text-sm">{{ $empresa->nombre_comercial ?? 'Sin Nombre Comercial' }}</span>
                                <span class="text-slate-400 font-mono text-[11px]">{{ $empresa->razon_social }} • <strong class="text-slate-600">{{ $empresa->rif }}</strong></span>
                            </td>
                            <td class="p-4.5">
                                <span class="text-slate-800 font-semibold block">{{ $empresa->sector }}</span>
                                <span class="text-slate-400 text-[11px]">{{ $empresa->comuna }}</span>
                            </td>
                            <td class="p-4.5">
                                <span class="px-3 py-1 bg-emerald-50 text-[#0d5238] rounded-xl font-extrabold border border-emerald-200/60 text-[10px] tracking-wide inline-block shadow-xs">
                                    {{ $empresa->perfilAmbiental->rol_ambiental ?? 'N/A' }}
                                </span>
                            </td>
                            <td class="p-4.5">
                                <span class="px-3 py-1 bg-amber-50 text-amber-700 rounded-xl font-extrabold border border-amber-200/60 text-[10px] tracking-wide inline-block shadow-xs">
                                    PENDIENTE
                                </span>
                            </td>
                            <td class="p-4.5 pr-6 text-center">
                                <a href="{{ route('director.residuos.empresas.show', $empresa->id) }}" class="px-4 py-2 bg-[#0d5238] hover:bg-[#073825] text-white rounded-xl font-bold transition-all shadow-md shadow-emerald-900/10 cursor-pointer text-xs inline-flex items-center justify-center gap-1.5 mx-auto">
                                    <span>Revisar Expediente</span>
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-12 text-center text-slate-400 italic">
                                <div class="max-w-xs mx-auto space-y-3">
                                    <div class="w-12 h-12 rounded-2xl bg-slate-100 flex items-center justify-center mx-auto text-xl text-slate-400">🎉</div>
                                    <p class="text-xs font-semibold text-slate-600">¡Excelente trabajo!</p>
                                    <p class="text-[11px] text-slate-400">No hay empresas pendientes por revisión en este momento.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
</x-layouts.app>