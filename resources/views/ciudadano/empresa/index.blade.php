<x-layouts.app title="Empresa - Panel Ciudadano" active="empresas">
<div class="p-4 sm:p-6 max-w-6xl mx-auto space-y-6">
    
    @php
        $empresa = $empresa ?? $empresas->first();
    @endphp

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
            <a 
                href="{{ route('ciudadano.empresas.create') }}"
                class="px-4 py-2.5 bg-[#0d5238] hover:bg-[#073825] text-white text-xs font-bold rounded-xl flex items-center justify-center gap-2 shadow-xs transition-all cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Vincular Empresa
            </a>
        @endif
    </div>

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
            <a 
                href="{{ route('ciudadano.empresas.create') }}"
                class="px-5 py-2.5 bg-[#0d5238] hover:bg-[#073825] text-white text-xs font-bold rounded-xl inline-flex items-center gap-2 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Vincular Empresa
            </a>
        </div>
    @else
        @php
            $sancionesActivas = $empresa->sanciones ? $empresa->sanciones->whereIn('estatus', ['NOTIFICADA', 'EN_REVISION', 'PENDIENTE_NOTIFICACION']) : collect();
        @endphp

        <!-- Alerta con Redirección a la Vista Dedicada de Sanciones -->
        @if($sancionesActivas->count() > 0)
            <div class="p-4 bg-rose-50 border border-rose-200 rounded-3xl flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 text-rose-900 shadow-xs">
                <div class="flex items-start gap-3">
                    <div class="p-2 bg-rose-100 rounded-2xl text-rose-700 shrink-0 mt-0.5 sm:mt-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </div>
                    <div>
                        <h4 class="text-xs font-bold uppercase tracking-wide text-rose-800">Aviso de Fiscalización Administrativa</h4>
                        <p class="text-xs text-rose-700 mt-0.5">
                            Tu firma comercial posee <strong>{{ $sancionesActivas->count() }} citaciones o procedimiento(s) pendiente(s)</strong> emitidos por la Dirección de Residuos.
                        </p>
                    </div>
                </div>
                <a href="{{ route('ciudadano.empresas.sanciones.index') }}" class="px-3.5 py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-bold transition shadow-xs whitespace-nowrap self-end sm:self-center flex items-center gap-1.5">
                    <span>Gestionar Citaciones</span>
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>
        @endif

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
                    @elseif($empresa->estatus === 'OBSERVADO' || $empresa->estatus === 'RECHAZADO')
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-rose-100 text-rose-800 border border-rose-200">
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

            <!-- NUEVA SECCIÓN: Expediente de Documentos y Vigencia -->
            <div class="pt-4 border-t border-gray-100 space-y-3">
                <div class="flex items-center justify-between">
                    <h3 class="text-xs font-bold text-gray-800 uppercase tracking-wide">Recaudos y Vigencia de Documentos</h3>
                    <span class="text-[11px] text-gray-500">Control de vencimientos (RACDA, Póliza, etc.)</span>
                </div>

                <div class="grid grid-cols-1 gap-3">
                    @forelse($empresa->documentosExpediente as $doc)
                        @php
                            $vencido = $doc->fecha_vencimiento && \Carbon\Carbon::parse($doc->fecha_vencimiento)->isPast();
                            $porVencer = $doc->fecha_vencimiento && !is_null($doc->fecha_vencimiento) && \Carbon\Carbon::parse($doc->fecha_vencimiento)->diffInDays(now()) <= 30 && !$vencido;
                        @endphp
                        
                        <div x-data="{ openRenovar: false, esVitalicioRenovacion: false }" class="p-4 bg-gray-50/80 rounded-2xl border border-gray-200/80 space-y-3 text-xs">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                <div class="space-y-0.5">
                                    <div class="flex items-center gap-2">
                                        <span class="font-bold text-gray-900 text-sm">{{ $doc->nombre_requisito }}</span>
                                        <span class="px-2 py-0.5 text-[10px] font-mono font-bold rounded uppercase {{ $doc->estado_revision === 'VALIDO' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-850' }}">
                                            {{ $doc->estado_revision }}
                                        </span>
                                    </div>
                                    <div class="text-xs text-gray-500 flex items-center gap-3 pt-1">
                                        <span>Emisión: <strong>{{ $doc->fecha_emision ? \Carbon\Carbon::parse($doc->fecha_emision)->format('d/m/Y') : 'No registrada' }}</strong></span>
                                        <span>•</span>
                                        @if($doc->es_vitalicio)
                                            <span class="text-emerald-700 font-semibold">Vigencia: Vitalicio / Indefinido</span>
                                        @else
                                            <span>Vencimiento: <strong>{{ $doc->fecha_vencimiento ? \Carbon\Carbon::parse($doc->fecha_vencimiento)->format('d/m/Y') : 'No especificada' }}</strong></span>
                                        @endif
                                    </div>
                                </div>

                                <div class="flex items-center gap-2.5 self-end sm:self-center">
                                    @if($vencido)
                                        <span class="px-2.5 py-1 bg-rose-100 text-rose-800 text-[10px] font-bold rounded-lg border border-rose-200">
                                            ¡Documento Vencido!
                                        </span>
                                    @elseif($porVencer)
                                        <span class="px-2.5 py-1 bg-amber-100 text-amber-800 text-[10px] font-bold rounded-lg border border-amber-200">
                                            Próximo a Vencer
                                        </span>
                                    @endif

                                    @if($doc->archivo_path)
                                        <a href="{{ route('expedientes.documento.ver', $doc->id) }}" target="_blank" class="px-3 py-1.5 bg-white border border-gray-200 text-gray-700 hover:bg-gray-100 rounded-xl font-semibold transition shadow-xs flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            <span>Ver Archivo</span>
                                        </a>
                                    @endif

                                    @if($vencido || $porVencer)
                                        <button @click="openRenovar = !openRenovar" class="px-3 py-1.5 bg-[#0d5238] hover:bg-[#073825] text-white rounded-xl font-bold transition shadow-xs flex items-center gap-1 cursor-pointer">
                                            <span x-text="openRenovar ? 'Cancelar' : '🔄 Actualizar Papel'"></span>
                                        </button>
                                    @endif
                                </div>
                            </div>

                            <!-- Formulario Desplegable de Renovación Integrado junto a las fechas -->
                            <div x-show="openRenovar" x-cloak x-transition class="pt-3 border-t border-gray-200/60 mt-2">
                                <form method="POST" action="{{ route('ciudadano.empresas.documentos.renovar', $doc->id) }}" enctype="multipart/form-data" class="p-4 bg-white rounded-2xl border border-emerald-200/80 shadow-xs space-y-3">
                                    @csrf
                                    <div class="flex items-center justify-between pb-1 border-b border-gray-100">
                                        <span class="text-xs font-bold text-[#0d5238] uppercase tracking-wide">Renovación de Recaudo Digital</span>
                                        <span class="text-[10px] text-gray-400">Adjunte el soporte actualizado y sus nuevas fechas</span>
                                    </div>

                                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 items-end">
                                        <div>
                                            <label class="block text-[10px] font-bold text-gray-700 uppercase mb-1">Nuevo Archivo (PDF/IMG) *</label>
                                            <input type="file" name="archivo" required class="w-full text-xs text-gray-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-[#0d5238] hover:file:bg-emerald-100 cursor-pointer">
                                        </div>

                                        <div>
                                            <label class="block text-[10px] font-bold text-gray-700 uppercase mb-1">Nueva Fecha Emisión *</label>
                                            <input type="date" name="fecha_emision" required class="w-full px-3 py-2 rounded-xl border border-gray-200 text-xs bg-white text-gray-800">
                                        </div>

                                        <div>
                                            <label class="block text-[10px] font-bold text-gray-700 uppercase mb-1">Nueva Fecha Vencimiento</label>
                                            <input type="date" name="fecha_vencimiento" :disabled="esVitalicioRenovacion" class="w-full px-3 py-2 rounded-xl border border-gray-200 text-xs bg-white text-gray-800 disabled:bg-gray-100 disabled:cursor-not-allowed">
                                        </div>

                                        <div class="flex items-center justify-between lg:justify-start gap-3">
                                            <label class="flex items-center gap-2 cursor-pointer pt-2">
                                                <input type="checkbox" name="es_vitalicio" value="1" x-model="esVitalicioRenovacion" class="rounded text-[#0d5238]">
                                                <span class="font-semibold text-gray-700 text-[11px]">Vitalicio</span>
                                            </label>

                                            <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl shadow-xs transition cursor-pointer">
                                                Guardar Cambios
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-gray-400 italic py-2">No hay documentos cargados en el expediente de la empresa.</p>
                    @endforelse
                </div>
            </div>

            <div class="pt-3 flex items-center justify-between border-t border-gray-100 text-xs">
                <span class="text-gray-400 font-medium">
                    Registrado el: {{ $empresa->created_at ? $empresa->created_at->format('d/m/Y') : 'N/A' }}
                </span>

                <div class="flex items-center gap-2">
                    <a href="{{ route('ciudadano.empresas.sanciones.index') }}" class="px-3.5 py-2 bg-rose-50 text-rose-700 border border-rose-200 hover:bg-rose-100 rounded-xl font-bold transition-all flex items-center gap-1">
                        <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        <span>Historial Citaciones ({{ $empresa->sanciones ? $empresa->sanciones->count() : 0 }})</span>
                    </a>
                </div>
            </div>
        </div>
    @endif
</div>
</x-layouts.app>