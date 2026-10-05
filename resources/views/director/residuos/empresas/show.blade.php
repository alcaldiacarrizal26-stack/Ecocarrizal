<x-layouts.app title="Detalles de la Empresa" active="residuos_empresas">
<div class="space-y-6 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    
    {{-- Header & Navegación --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-slate-900/90 border border-slate-800 p-6 rounded-2xl shadow-xl">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-bold text-white tracking-tight">{{ $empresa->razon_social }}</h1>
                <span class="px-2.5 py-1 text-xs font-semibold rounded-full border bg-slate-800 text-slate-300 border-slate-700 uppercase">
                    {{ $empresa->estatus }}
                </span>
            </div>
            <p class="text-sm text-slate-400 mt-1">
                RIF: <span class="font-mono text-emerald-400 font-semibold">{{ $empresa->rif }}</span> 
                @if($empresa->nombre_comercial)
                    | Comercial: <span class="text-slate-200 font-medium">{{ $empresa->nombre_comercial }}</span>
                @endif
            </p>
        </div>
        <div>
            <a href="{{ route('director.residuos.empresas.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 text-sm font-medium rounded-xl transition-all border border-slate-700">
                &larr; Volver a empresas
            </a>
        </div>
    </div>

    <!-- Alertas de Éxito o Mensajes de Sesión -->
    @if (session('success') || session('status'))
        <div class="p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-sm flex items-center gap-2">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            <span>{{ session('success') ?? session('status') }}</span>
        </div>
    @endif

    <!-- Alertas de Errores -->
    @if ($errors->any())
        <div class="p-4 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-400 text-sm space-y-1">
            <p class="font-semibold">Por favor corrige los siguientes errores:</p>
            <ul class="list-disc list-inside text-xs">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- BLOQUE 1: Datos Generales --}}
    <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-6 shadow-xl">
        <h2 class="text-sm font-bold uppercase tracking-wider text-emerald-400 mb-4 border-b border-slate-800 pb-2">Información de la Empresa</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-slate-950/50 p-4 rounded-xl border border-slate-800/60">
                <span class="block text-xs font-medium text-slate-400 uppercase">Patente Comercio</span>
                <span class="block text-base font-semibold text-slate-100 mt-1">{{ $empresa->numero_cuenta_patente ?? 'N/A' }}</span>
            </div>
            <div class="bg-slate-950/50 p-4 rounded-xl border border-slate-800/60">
                <span class="block text-xs font-medium text-slate-400 uppercase">Contacto Comercial</span>
                <span class="block text-sm font-semibold text-slate-100 mt-1">{{ $empresa->correo_comercial }}</span>
                <span class="block text-xs text-slate-400 mt-0.5">{{ $empresa->telefono_comercial }}</span>
            </div>
            <div class="bg-slate-950/50 p-4 rounded-xl border border-slate-800/60">
                <span class="block text-xs font-medium text-slate-400 uppercase">Ubicación</span>
                <span class="block text-sm font-semibold text-slate-100 mt-1">{{ $empresa->comuna }}</span>
                <span class="block text-xs text-slate-400 mt-0.5">{{ $empresa->sector }}, {{ $empresa->calle_avenida }}, {{ $empresa->edificio_galpon_local }}</span>
            </div>
        </div>
    </div>

    {{-- BLOQUE 2: Perfil y Diagnóstico Ambiental --}}
    @php
        $perfil = $empresa->perfilAmbiental;
    @endphp
    <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-6 shadow-xl">
        <h2 class="text-sm font-bold uppercase tracking-wider text-emerald-400 mb-4 border-b border-slate-800 pb-2">Perfil y Diagnóstico Ambiental (Paso 2)</h2>
        
        @if($perfil)
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Rol y Subtipo -->
                <div class="bg-slate-950/50 p-4 rounded-xl border border-slate-800/60">
                    <span class="block text-xs font-medium text-slate-400 uppercase">Rol Operativo Ambiental</span>
                    <span class="block text-sm font-semibold text-slate-100 mt-1">{{ $perfil->rol_ambiental ?? 'GENERADOR' }}</span>
                    @if($perfil->subtipo_generador)
                        <span class="block text-xs text-emerald-400 mt-0.5 font-medium">Categoría: {{ $perfil->subtipo_generador }}</span>
                    @endif
                </div>

                <!-- Registro RACDA Formalizado -->
                <div class="bg-slate-950/50 p-4 rounded-xl border border-slate-800/60">
                    <span class="block text-xs font-medium text-slate-400 uppercase">Registro RACDA</span>
                    @if(!empty($perfil->numero_racda) && \Illuminate\Support\Str::contains($perfil->numero_racda, 'RACDA-'))
                        <span class="block text-sm font-semibold text-emerald-400 mt-1 font-mono">
                            {{ $perfil->numero_racda }}
                        </span>
                        <span class="block text-[11px] text-slate-400 mt-0.5">
                            {{ \Illuminate\Support\Str::startsWith($perfil->numero_racda, 'RACDA-G-') ? 'Registro Generador' : 'Registro Manejador' }}
                        </span>
                    @else
                        <span class="block text-sm font-semibold text-slate-400 mt-1">NO APLICA / SIN REGISTRO</span>
                    @endif
                </div>

                <!-- Módulos de Impacto -->
                <div class="bg-slate-950/50 p-4 rounded-xl border border-slate-800/60">
                    <span class="block text-xs font-medium text-slate-400 uppercase">Impactos Operativos</span>
                    <div class="flex flex-wrap gap-1.5 mt-2">
                        @if($perfil->genera_efluentes) <span class="px-2 py-0.5 text-[10px] font-semibold bg-sky-950/60 text-sky-300 rounded border border-sky-800/50">Efluentes Líquidos</span> @endif
                        @if($perfil->genera_emisiones) <span class="px-2 py-0.5 text-[10px] font-semibold bg-amber-950/60 text-amber-300 rounded border border-amber-800/50">Emisiones / Calderas</span> @endif
                        @if($perfil->genera_peligrosos) <span class="px-2 py-0.5 text-[10px] font-semibold bg-rose-950/60 text-rose-300 rounded border border-rose-800/50">Desechos Peligrosos</span> @endif

                        @if(!$perfil->genera_efluentes && !$perfil->genera_emisiones && !$perfil->genera_peligrosos)
                            <span class="text-xs text-slate-400 italic">No reporta impactos especiales</span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Detalle de Relación Comercial (Empresa Manejadora o Generador Exclusivo) -->
            @if($perfil->rol_ambiental === 'GENERADOR' && $perfil->modalidad_manejadora === 'EXTERNA')
                <div class="mt-4 p-4 bg-slate-950/60 rounded-xl border border-slate-800 text-xs space-y-2">
                    <span class="block text-[11px] font-bold uppercase text-emerald-400 tracking-wider">Empresa Manejadora Contratada</span>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-slate-300">
                        <div><span class="text-slate-500 block">Razón Social:</span> <span class="font-semibold text-slate-100">{{ $perfil->manejadora_nombre ?? 'N/A' }}</span></div>
                        <div><span class="text-slate-500 block">RIF Manejadora:</span> <span class="font-mono text-emerald-400 font-semibold">{{ $perfil->manejadora_rif ?? 'N/A' }}</span></div>
                        <div><span class="text-slate-500 block">RACDA Manejadora:</span> <span class="font-mono text-slate-200">{{ $perfil->manejadora_racda ?? 'N/A' }}</span></div>
                    </div>
                </div>
            @elseif($perfil->rol_ambiental === 'MANEJADOR')
                <!-- Actividades de Manejador -->
                @if(is_array($perfil->actividades_manejador) && count($perfil->actividades_manejador) > 0)
                    <div class="mt-4 p-3.5 bg-slate-950/50 rounded-xl border border-slate-800 text-xs">
                        <span class="block text-slate-400 uppercase font-semibold mb-2 text-[10px]">Actividades de Manejo Autorizadas:</span>
                        <div class="flex flex-wrap gap-1.5">
                            @foreach($perfil->actividades_manejador as $actividad)
                                <span class="px-2.5 py-1 text-[11px] bg-slate-800 text-emerald-300 rounded-lg border border-slate-700 capitalize">
                                    {{ str_replace('_', ' ', $actividad) }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if($perfil->modalidad_atencion_manejador === 'EXCLUSIVO')
                    <div class="mt-4 p-4 bg-slate-950/60 rounded-xl border border-slate-800 text-xs space-y-2">
                        <span class="block text-[11px] font-bold uppercase text-emerald-400 tracking-wider">Generador Exclusivo Atendido</span>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-slate-300">
                            <div><span class="text-slate-500 block">Razón Social:</span> <span class="font-semibold text-slate-100">{{ $perfil->generador_exclusivo_nombre ?? 'N/A' }}</span></div>
                            <div><span class="text-slate-500 block">RIF Generador:</span> <span class="font-mono text-emerald-400 font-semibold">{{ $perfil->generador_exclusivo_rif ?? 'N/A' }}</span></div>
                            <div><span class="text-slate-500 block">RACDA Generador:</span> <span class="font-mono text-slate-200">{{ $perfil->generador_exclusivo_racda ?? 'N/A' }}</span></div>
                        </div>
                    </div>
                @endif
            @endif

            <!-- Tipos de Residuos Físicos -->
            @if(is_array($perfil->residuos_tipos) && count($perfil->residuos_tipos) > 0)
                <div class="mt-4 pt-3 border-t border-slate-800/80">
                    <span class="block text-xs font-semibold text-slate-400 uppercase mb-2">Tipos de Residuos Generados / Declarados</span>
                    <div class="flex flex-wrap gap-1.5">
                        @foreach($perfil->residuos_tipos as $tipo)
                            <span class="px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider bg-slate-800/90 text-slate-300 rounded-lg border border-slate-700">
                                {{ str_replace('_', ' ', $tipo) }}
                            </span>
                        @endforeach
                    </div>
                </div>
            @endif
        @else
            <p class="text-sm text-slate-400 italic">No se ha registrado un perfil ambiental para esta empresa.</p>
        @endif
    </div>

    {{-- BLOQUE 3: Documentación Consolidada y Recaudos --}}
    <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-6 shadow-xl">
        <h2 class="text-sm font-bold uppercase tracking-wider text-emerald-400 mb-4 border-b border-slate-800 pb-2">Expediente Digital y Recaudos</h2>
        
        @if($empresa->documentosExpediente && $empresa->documentosExpediente->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($empresa->documentosExpediente as $doc)
                    <div class="flex items-center justify-between p-3.5 bg-slate-950/60 border border-slate-800/80 rounded-xl hover:border-slate-700 transition-colors">
                        <div class="truncate mr-3">
                            <div class="flex items-center gap-2">
                                <p class="text-sm font-semibold text-slate-200 truncate" title="{{ $doc->nombre_requisito }}">
                                    {{ $doc->nombre_requisito }}
                                </p>
                                {{-- Badge del estado individual del documento --}}
                                <span class="px-1.5 py-0.5 text-[9px] font-semibold uppercase rounded border 
                                    {{ $doc->estado === 'APROBADO' ? 'bg-emerald-500/10 text-emerald-400 border-emerald-500/30' : ($doc->estado === 'OBSERVADO' ? 'bg-amber-500/10 text-amber-400 border-amber-500/30' : 'bg-slate-800 text-slate-400 border-slate-700') }}">
                                    {{ $doc->estado ?? 'PENDIENTE' }}
                                </span>
                            </div>
                            <p class="text-xs text-slate-400 mt-0.5">
                                Cod: <span class="font-mono text-slate-300">{{ $doc->codigo_requisito }}</span>
                                @if($doc->numero_folio) | Folio: {{ $doc->numero_folio }} @endif
                            </p>
                        </div>
                        @if($doc->archivo_path)
                            @php
                                $fileUrl = \Illuminate\Support\Str::startsWith($doc->archivo_path, 'http') 
                                    ? $doc->archivo_path 
                                    : asset('storage/' . $doc->archivo_path);
                            @endphp
                            <a href="{{ $fileUrl }}" 
                               target="_blank" 
                               class="shrink-0 px-3 py-1.5 bg-emerald-600/20 text-emerald-300 hover:bg-emerald-600/30 border border-emerald-500/30 text-xs font-semibold rounded-lg transition-colors flex items-center gap-1">
                                Ver PDF
                            </a>
                        @else
                            <span class="text-xs text-slate-500 italic">Sin archivo</span>
                        @endif
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-sm text-slate-400 italic">No hay documentos cargados en el expediente de esta empresa.</p>
        @endif
    </div>

    {{-- BLOQUE 4: Historial de Declaraciones de Residuos --}}
    <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-6 shadow-xl">
        <div class="flex items-center justify-between mb-4 border-b border-slate-800 pb-2">
            <h2 class="text-sm font-bold uppercase tracking-wider text-emerald-400">Declaraciones de Residuos</h2>
            <span class="text-xs text-slate-400 font-medium">Total registradas: {{ $empresa->declaracionesResiduos ? $empresa->declaracionesResiduos->count() : 0 }}</span>
        </div>

        @if($empresa->declaracionesResiduos && $empresa->declaracionesResiduos->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-300">
                    <thead class="bg-slate-950/60 text-xs uppercase text-slate-400 border-b border-slate-800">
                        <tr>
                            <th class="py-2.5 px-3">Periodo</th>
                            <th class="py-2.5 px-3">Frecuencia</th>
                            <th class="py-2.5 px-3">Comunes (TON)</th>
                            <th class="py-2.5 px-3">Reciclables (TON)</th>
                            <th class="py-2.5 px-3">Peligrosos (TON)</th>
                            <th class="py-2.5 px-3">Estatus</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/60">
                        @foreach($empresa->declaracionesResiduos as $decl)
                            <tr class="hover:bg-slate-950/30 transition-colors">
                                <td class="py-3 px-3 font-semibold text-white font-mono">{{ $decl->periodo }}</td>
                                <td class="py-3 px-3 text-xs uppercase text-slate-400">{{ $decl->frecuencia }}</td>
                                <td class="py-3 px-3 font-mono text-slate-200">{{ number_format($decl->toneladas_comunes, 3) }}</td>
                                <td class="py-3 px-3 font-mono text-emerald-400">{{ number_format($decl->toneladas_reciclables, 3) }}</td>
                                <td class="py-3 px-3 font-mono text-rose-400">{{ number_format($decl->toneladas_peligrosas, 3) }}</td>
                                <td class="py-3 px-3">
                                    <span class="px-2 py-0.5 text-[10px] font-bold uppercase rounded-full border bg-slate-800 text-slate-300 border-slate-700">
                                        {{ $decl->estatus }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-sm text-slate-400 italic">La empresa no ha presentado declaraciones cuantitativas hasta la fecha.</p>
        @endif
    </div>

    {{-- BLOQUE 5: Emisión de Veredicto / Evaluación --}}
    <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-6 shadow-xl">
        <h2 class="text-sm font-bold uppercase tracking-wider text-emerald-400 mb-4 border-b border-slate-800 pb-2">Emisión de Veredicto Directoral</h2>
        
        @if(in_array(strtoupper($empresa->estatus), ['APROBADO', 'RECHAZADO']))
            {{-- Veredicto Cerrado (Lectura Única) --}}
            <div class="p-4 rounded-xl bg-slate-950/60 border border-slate-800 space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-xs uppercase font-medium text-slate-400">Estado Final del Expediente:</span>
                    <span class="px-3 py-1 text-xs font-bold rounded-full border {{ strtoupper($empresa->estatus) === 'APROBADO' ? 'bg-emerald-500/10 text-emerald-400 border-emerald-500/30' : 'bg-rose-500/10 text-rose-400 border-rose-500/30' }}">
                        {{ $empresa->estatus }}
                    </span>
                </div>
                <div>
                    <span class="block text-xs uppercase font-medium text-slate-400 mb-1">Dictamen / Observaciones Registradas:</span>
                    <p class="text-sm text-slate-200 bg-slate-900/80 p-3 rounded-lg border border-slate-800/80 italic">
                        {{ $empresa->observaciones_fiscalia ?? 'Sin observaciones especificadas.' }}
                    </p>
                </div>
                <p class="text-xs text-amber-400/80 flex items-center gap-1 pt-1">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    Este expediente se encuentra procesado. No es posible modificar el veredicto.
                </p>
            </div>
        @else
            {{-- Formulario para emitir veredicto --}}
            <form action="{{ route('director.residuos.empresas.evaluar', $empresa->id) }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label for="observaciones_fiscalia" class="block text-sm font-medium text-slate-300 mb-1">Observaciones / Dictamen Técnico</label>
                    <textarea id="observaciones_fiscalia" name="observaciones_fiscalia" rows="4" 
                              class="w-full bg-slate-950 border border-slate-800 rounded-xl p-3 text-slate-100 placeholder-slate-500 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition-all"
                              placeholder="Ingrese los comentarios o justificaciones de la decisión...">{{ old('observaciones_fiscalia', $empresa->observaciones_fiscalia) }}</textarea>
                </div>

                <div class="flex flex-wrap items-center justify-end gap-3 pt-2">
                    <button type="submit" name="estatus" value="OBSERVADO" 
                            class="px-5 py-2.5 bg-amber-600/20 text-amber-300 border border-amber-500/30 hover:bg-amber-600/30 font-semibold text-sm rounded-xl transition-all">
                        Marcar Observado
                    </button>
                    <button type="submit" name="estatus" value="RECHAZADO" 
                            class="px-5 py-2.5 bg-rose-600/20 text-rose-300 border border-rose-500/30 hover:bg-rose-600/30 font-semibold text-sm rounded-xl transition-all">
                        Rechazar Solicitud
                    </button>
                    <button type="submit" name="estatus" value="APROBADO" 
                            class="px-5 py-2.5 bg-emerald-600 text-white hover:bg-emerald-500 font-semibold text-sm rounded-xl shadow-lg shadow-emerald-900/20 transition-all">
                        Aprobar Expediente
                    </button>
                </div>
            </form>
        @endif
    </div>

</div>
</x-layouts.app>