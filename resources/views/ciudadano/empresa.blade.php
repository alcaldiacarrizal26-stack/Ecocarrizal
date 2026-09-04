<x-layouts.app title="Empresa - Panel Ciudadano" active="empresas">
<div class="p-4 sm:p-6 max-w-6xl mx-auto space-y-6" x-data="{ modalOpen: false }">
    
    <!-- Encabezado de la Sección -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-5 border-b border-gray-200">
        <div>
            <h1 class="text-xl sm:text-2xl font-extrabold text-gray-900 tracking-tight flex items-center gap-2">
                <svg class="w-6 h-6 text-[#0d5238]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m0 0h4m-4 0V11m0 0h4m-4 0H7m4 0v10" />
                </svg>
                Representación Comercial y Empresas
            </h1>
            <p class="text-xs sm:text-sm text-gray-500 font-medium mt-1">
                Gestión de expediente ambiental comercial ante la Alcaldía del Municipio Carrizal.
            </p>
        </div>

        @if(!$empresa)
            <button 
                type="button" 
                @click="modalOpen = true"
                class="px-4 py-2.5 bg-[#0d5238] hover:bg-[#073825] text-white text-xs font-bold rounded-xl flex items-center justify-center gap-2 shadow-xs transition-all cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Vincular Empresa
            </button>
        @endif
    </div>

    <!-- CASO A: El ciudadano no posee empresas vinculadas -->
    @if(!$empresa)
        <div class="bg-white border-2 border-dashed border-gray-200 rounded-3xl p-8 sm:p-12 text-center space-y-4">
            <div class="w-16 h-16 bg-emerald-50 text-[#0d5238] rounded-2xl flex items-center justify-center mx-auto border border-emerald-100">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m0 0h4m-4 0V11m0 0h4m-4 0H7m4 0v10" />
                </svg>
            </div>
            <div class="max-w-md mx-auto space-y-1">
                <h3 class="text-base font-bold text-gray-900">No posees empresas vinculadas</h3>
                <p class="text-xs text-gray-500 leading-relaxed">
                    Como ciudadano registrado, puedes vincular tu firma comercial para consignar el perfil ambiental y solicitar certificaciones de residuos.
                </p>
            </div>
            <button 
                type="button" 
                @click="modalOpen = true"
                class="px-5 py-2.5 bg-[#0d5238] hover:bg-[#073825] text-white text-xs font-bold rounded-xl inline-flex items-center gap-2 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Vincular Empresa
            </button>
        </div>
    @else
        <!-- CASO B: El ciudadano ya posee su empresa vinculada -->
        <div class="bg-white border border-gray-200 rounded-3xl p-6 shadow-xs space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4 border-b border-gray-100 pb-4">
                <div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <h2 class="text-lg font-bold text-gray-900">{{ $empresa->razon_social }}</h2>
                        <span class="px-2.5 py-0.5 bg-gray-100 text-gray-700 font-mono text-xs font-extrabold rounded-md border border-gray-200">
                            {{ $empresa->rif }}
                        </span>
                    </div>
                    <p class="text-xs text-gray-500 font-medium mt-0.5">
                        Nombre Comercial: <span class="text-gray-800 font-semibold">{{ $empresa->nombre_comercial ?? $empresa->razon_social }}</span>
                    </p>
                </div>

                <!-- Insignia de Estatus Dinámica -->
                <div>
                    @if($empresa->estatus === 'PENDIENTE_APROBACION')
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800 border border-amber-200">
                            <span class="w-2 h-2 rounded-full bg-amber-500 animate-ping"></span>
                            En Revisión por Dirección de Residuos
                        </span>
                    @elseif($empresa->estatus === 'APROBADO')
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-[#0d5238] border border-emerald-200">
                            Expediente Conforme y Activo
                        </span>
                    @elseif($empresa->estatus === 'OBSERVADO')
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-orange-100 text-orange-800 border border-orange-200">
                            Requiere Correcciones
                        </span>
                    @endif
                </div>
            </div>

            <!-- Datos Clave -->
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 text-xs">
                <div class="p-3 bg-gray-50 rounded-2xl border border-gray-100 space-y-1">
                    <span class="text-gray-400 font-semibold block text-[10px] uppercase">Patente Municipal</span>
                    <span class="font-bold text-gray-800 font-mono">{{ $empresa->numero_cuenta_patente }}</span>
                </div>

                <div class="p-3 bg-gray-50 rounded-2xl border border-gray-100 space-y-1">
                    <span class="text-gray-400 font-semibold block text-[10px] uppercase">Sector</span>
                    <span class="font-bold text-gray-800">{{ $empresa->sector }}</span>
                </div>

                <div class="p-3 bg-gray-50 rounded-2xl border border-gray-100 space-y-1">
                    <span class="text-gray-400 font-semibold block text-[10px] uppercase">Correo Comercial</span>
                    <span class="font-bold text-gray-800 truncate block">{{ $empresa->correo_comercial }}</span>
                </div>

                <div class="p-3 bg-gray-50 rounded-2xl border border-gray-100 space-y-1">
                    <span class="text-gray-400 font-semibold block text-[10px] uppercase">Teléfono</span>
                    <span class="font-bold text-gray-800">{{ $empresa->telefono_comercial }}</span>
                </div>
            </div>

            @if($empresa->estatus === 'PENDIENTE_APROBACION')
                <div class="p-4 bg-amber-50 rounded-2xl border border-amber-200 text-xs text-amber-900">
                    <p class="font-bold">Expediente bajo evaluación técnica</p>
                    <p class="text-amber-800 mt-0.5 leading-relaxed">
                        Los recaudos y la declaración de residuos están siendo auditados por la Dirección de Residuos y Desechos Sólidos.
                    </p>
                </div>
            @endif

            <div class="pt-2 flex items-center justify-between border-t border-gray-100 text-xs">
                <span class="text-gray-400 font-medium">Registrado el: {{ $empresa->created_at->format('d/m/Y') }}</span>
                <button type="button" @click="modalOpen = true" class="px-3.5 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-xl transition-colors cursor-pointer">
                    Editar Datos
                </button>
            </div>
        </div>
    @endif

    <!-- Incluir la Modal Blade/Alpine que creamos anteriormente -->
    @include('ciudadano.partials.vincular-modal')
</div>
</x-layouts.app>