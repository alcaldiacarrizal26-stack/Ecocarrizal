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

    <!-- Alertas de Éxito / Mensajes de Sesión -->
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

    {{-- BLOQUE 2: Perfil Ambiental y Gestión de Residuos --}}
    @php
        $perfil = $empresa->perfilAmbiental;

        // Limpieza de RACDA
        $numRacda = $perfil ? trim($perfil->numero_racda ?? '') : '';
        $tieneRacda = !empty($numRacda) && strtolower($numRacda) !== 'racda no aplica' && strtolower($numRacda) !== 'no aplica';

        // Decodificación de Tipos de Residuos
        $residuosTiposList = [];
        if ($perfil && $perfil->residuos_tipos) {
            if (is_array($perfil->residuos_tipos)) {
                $residuosTiposList = $perfil->residuos_tipos;
            } else {
                $decoded = json_decode($perfil->residuos_tipos, true);
                if (is_string($decoded)) $decoded = json_decode($decoded, true);
                $residuosTiposList = is_array($decoded) ? $decoded : [];
            }
        }

        // Decodificación de Actividades de Manejador
        $actividadesManejador = [];
        if ($perfil && $perfil->actividades_manejador) {
            if (is_array($perfil->actividades_manejador)) {
                $actividadesManejador = $perfil->actividades_manejador;
            } else {
                $decodedAct = json_decode($perfil->actividades_manejador, true);
                if (is_string($decodedAct)) $decodedAct = json_decode($decodedAct, true);
                $actividadesManejador = is_array($decodedAct) ? $decodedAct : [];
            }
        }
    @endphp

    <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-6 shadow-xl">
        <h2 class="text-sm font-bold uppercase tracking-wider text-emerald-400 mb-4 border-b border-slate-800 pb-2">Perfil y Diagnóstico Ambiental (Paso 2)</h2>
        
        @if($perfil)
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Rol Operativo -->
                <div class="bg-slate-950/50 p-4 rounded-xl border border-slate-800/60">
                    <span class="block text-xs font-medium text-slate-400 uppercase">Rol Operativo Ambiental</span>
                    <span class="block text-sm font-semibold text-slate-100 mt-1">{{ $perfil->rol_ambiental ?? 'GENERADOR' }}</span>
                    @if($perfil->subtipo_generador)
                        <span class="block text-xs text-emerald-400 mt-0.5 font-medium">Categoría: {{ $perfil->subtipo_generador }}</span>
                    @endif
                </div>

                <!-- Registro RACDA -->
                <div class="bg-slate-950/50 p-4 rounded-xl border border-slate-800/60">
                    <span class="block text-xs font-medium text-slate-400 uppercase">Registro RACDA</span>
                    @if($tieneRacda && $perfil->tipo_racda && $perfil->tipo_racda !== 'NO_APLICA')
                        <span class="block text-sm font-semibold text-emerald-400 mt-1 font-mono">
                            {{ str_replace('_', ' ', $perfil->tipo_racda) }}
                        </span>
                        <span class="block text-xs font-mono text-slate-300 mt-0.5">N°: {{ $numRacda }}</span>
                    @else
                        <span class="block text-sm font-semibold text-slate-400 mt-1">NO APLICA</span>
                        <span class="block text-[11px] text-slate-500 italic mt-0.5">Sin registro / Exento de RACDA</span>
                    @endif
                </div>

                <!-- Módulos de Generación -->
                <div class="bg-slate-950/50 p-4 rounded-xl border border-slate-800/60">
                    <span class="block text-xs font-medium text-slate-400 uppercase">Módulos de Generación</span>
                    <div class="flex flex-wrap gap-1.5 mt-2">
                        @if($perfil->genera_efluentes) <span class="px-2 py-0.5 text-[10px] font-semibold bg-sky-950/60 text-sky-300 rounded border border-sky-800/50">Efluentes</span> @endif
                        @if($perfil->genera_emisiones) <span class="px-2 py-0.5 text-[10px] font-semibold bg-amber-950/60 text-amber-300 rounded border border-amber-800/50">Emisiones</span> @endif
                        @if($perfil->genera_peligrosos) <span class="px-2 py-0.5 text-[10px] font-semibold bg-rose-950/60 text-rose-300 rounded border border-rose-800/50">Desechos Peligrosos</span> @endif
                        @if($perfil->genera_ruidos) <span class="px-2 py-0.5 text-[10px] font-semibold bg-slate-800 text-slate-300 rounded border border-slate-700">Ruidos</span> @endif
                        @if($perfil->usa_pozo_agua) <span class="px-2 py-0.5 text-[10px] font-semibold bg-blue-950/60 text-blue-300 rounded border border-blue-800/50">Pozo Agua</span> @endif

                        @if(!$perfil->genera_efluentes && !$perfil->genera_emisiones && !$perfil->genera_peligrosos && !$perfil->genera_ruidos && !$perfil->usa_pozo_agua)
                            <span class="text-xs text-slate-400 italic">Sin módulos especiales reportados</span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- DETALLE DE RECOLECCIÓN Y MANEJO DE RESIDUOS (PROPIA VS EXTERNA Y CLIENTES) --}}
            <div class="mt-6 p-4 bg-slate-950/70 rounded-xl border border-slate-800 space-y-4">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-300 flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                    Modalidad de Recolección y Logística de Residuos
                </h3>

                @if(($perfil->rol_ambiental ?? 'GENERADOR') === 'GENERADOR')
                    {{-- Si es Generador: Verificar si maneja por sí mismo o externa --}}
                    @php
                        $modalidad = $perfil->modalidad_manejadora ?? ($perfil->manejadora_contratada ? 'EXTERNA' : 'PROPIA');
                    @endphp

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                        <div class="p-3 bg-slate-900/80 rounded-lg border border-slate-800">
                            <span class="text-slate-400 block mb-1">Modalidad de Manejo:</span>
                            @if($modalidad === 'PROPIA')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-emerald-950/60 text-emerald-300 font-semibold rounded border border-emerald-800/60">
                                    Manejo Propio (Autogestión de Residuos)
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-sky-950/60 text-sky-300 font-semibold rounded border border-sky-800/60">
                                    Contrata Empresa Manejadora Externa
                                </span>
                            @endif
                        </div>

                        @if($modalidad === 'EXTERNA')
                            <div class="p-3 bg-slate-900/80 rounded-lg border border-slate-800 space-y-1">
                                <span class="text-slate-400 block">Datos de la Empresa Contratada:</span>
                                <p class="text-slate-200 font-semibold">{{ $perfil->manejadora_nombre ?? $perfil->manejadora_contratada ?? 'No especificada' }}</p>
                                @if($perfil->manejadora_rif) <p class="text-slate-400 font-mono">RIF: {{ $perfil->manejadora_rif }}</p> @endif
                                @if($perfil->manejadora_racda) <p class="text-emerald-400 font-mono">RACDA: {{ $perfil->manejadora_racda }}</p> @endif
                            </div>
                        @endif
                    </div>

                @elseif($perfil->rol_ambiental === 'MANEJADOR')
                    {{-- Si es Manejador: Verificar si es exclusivo de un generador o transporte a varios --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                        <div class="p-3 bg-slate-900/80 rounded-lg border border-slate-800">
                            <span class="text-slate-400 block mb-1">Esquema de Cobertura / Clientes:</span>
                            @if(($perfil->modalidad_atencion_manejador ?? 'MULTIPLE') === 'EXCLUSIVO')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-purple-950/60 text-purple-300 font-semibold rounded border border-purple-800/60">
                                    Atención Exclusiva (Un solo Generador)
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-indigo-950/60 text-indigo-300 font-semibold rounded border border-indigo-800/60">
                                    Servicio Multicliente (Transporta / Maneja para Varios)
                                </span>
                            @endif
                        </div>

                        @if(($perfil->modalidad_atencion_manejador ?? '') === 'EXCLUSIVO')
                            <div class="p-3 bg-slate-900/80 rounded-lg border border-slate-800 space-y-1">
                                <span class="text-slate-400 block">Generador Exclusivo Atendido:</span>
                                <p class="text-slate-200 font-semibold">{{ $perfil->generador_exclusivo_nombre ?? 'N/A' }}</p>
                                @if($perfil->generador_exclusivo_rif) <p class="text-slate-400 font-mono">RIF: {{ $perfil->generador_exclusivo_rif }}</p> @endif
                                @if($perfil->generador_exclusivo_racda) <p class="text-emerald-400 font-mono">RACDA: {{ $perfil->generador_exclusivo_racda }}</p> @endif
                            </div>
                        @endif
                    </div>

                    @if(count($actividadesManejador) > 0)
                        <div class="pt-2">
                            <span class="block text-slate-400 text-xs font-semibold mb-2">Actividades de Manejo Autorizadas:</span>
                            <div class="flex flex-wrap gap-1.5">
                                @foreach($actividadesManejador as $act)
                                    <span class="px-2.5 py-1 text-[11px] bg-slate-800 text-emerald-300 rounded-lg border border-slate-700 capitalize">
                                        {{ str_replace('_', ' ', $act) }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif
                @endif
            </div>

            <!-- Tipos de Residuos Declarados -->
            @if(count($residuosTiposList) > 0)
                <div class="mt-4 pt-3 border-t border-slate-800/80">
                    <span class="block text-xs font-semibold text-slate-400 uppercase mb-2">Tipos de Residuos Declarados:</span>
                    <div class="flex flex-wrap gap-1.5">
                        @foreach($residuosTiposList as $tipo)
                            <span class="px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider bg-slate-800/90 text-slate-300 rounded-lg border border-slate-700">
                                {{ str_replace(['_', '"', '[', ']'], [' ', '', '', ''], $tipo) }}
                            </span>
                        @endforeach
                    </div>
                </div>
            @endif
        @else
            <p class="text-sm text-slate-400 italic">No se ha registrado un perfil ambiental para esta empresa.</p>
        @endif
    </div>

    {{-- BLOQUE 3: Documentación y Expediente Digital --}}
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
                                $pathLimpio = ltrim($doc->archivo_path, '/');
                                $fileUrl = \Illuminate\Support\Str::startsWith($pathLimpio, 'http') 
                                    ? $pathLimpio 
                                    : asset('storage/' . \Illuminate\Support\Str::after($pathLimpio, 'public/'));
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

    {{-- BLOQUE 4: Declaraciones de Residuos --}}
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

    {{-- BLOQUE 5: Formulario de Evaluación Directoral --}}
    <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-6 shadow-xl">
        <h2 class="text-sm font-bold uppercase tracking-wider text-emerald-400 mb-4 border-b border-slate-800 pb-2">Emisión de Veredicto Directoral</h2>
        
        @if(in_array(strtoupper($empresa->estatus), ['APROBADO', 'RECHAZADO']))
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
            </div>
        @else
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