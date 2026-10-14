<x-layouts.app title="Detalles de la Empresa" active="residuos_empresas">
<div class="space-y-6 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    
    {{-- Header & Navegación institucional --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-slate-900/90 border border-slate-800 p-6 rounded-3xl shadow-xl">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-bold text-white tracking-tight">{{ $empresa->razon_social }}</h1>
                <span class="px-3 py-1 text-xs font-semibold rounded-lg border bg-slate-800 text-slate-300 border-slate-700 uppercase tracking-wider">
                    {{ $empresa->estatus }}
                </span>
            </div>
            <p class="text-xs text-slate-400 mt-1.5 flex flex-wrap items-center gap-2">
                <span>RIF: <strong class="font-mono text-emerald-400">{{ $empresa->rif }}</strong></span>
                @if($empresa->nombre_comercial)
                    <span class="text-slate-600">•</span>
                    <span>Comercial: <strong class="text-slate-200">{{ $empresa->nombre_comercial }}</strong></span>
                @endif
            </p>
        </div>
        <div>
            <a href="{{ route('director.residuos.empresas.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-bold rounded-xl transition-all border border-slate-700">
                &larr; Volver al listado
            </a>
        </div>
    </div>

    <!-- Alertas de Sesión -->
    @if (session('success') || session('status'))
        <div class="p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-xs flex items-center gap-2.5">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            <span>{{ session('success') ?? session('status') }}</span>
        </div>
    @endif

    <!-- Alertas de Errores -->
    @if ($errors->any())
        <div class="p-4 rounded-2xl bg-rose-500/10 border border-rose-500/20 text-rose-400 text-xs space-y-1">
            <p class="font-bold">Por favor corrige los siguientes requerimientos:</p>
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- BLOQUE 1: Datos Generales --}}
    <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-xs space-y-4">
        <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400 border-b border-slate-100 pb-3">Información General y Registro Comercial</h2>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100 space-y-1">
                <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Patente de Comercio</span>
                <span class="block text-sm font-bold text-slate-800">{{ $empresa->numero_cuenta_patente ?? 'N/A' }}</span>
            </div>
            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100 space-y-1">
                <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Contacto Comercial</span>
                <span class="block text-xs font-bold text-slate-800 truncate">{{ $empresa->correo_comercial }}</span>
                <span class="block text-[11px] text-slate-500">{{ $empresa->telefono_comercial }}</span>
            </div>
            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100 space-y-1">
                <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Ubicación Territorial</span>
                <span class="block text-xs font-bold text-slate-800">{{ $empresa->comuna }}</span>
                <span class="block text-[11px] text-slate-500 truncate">{{ $empresa->sector }}, {{ $empresa->calle_avenida }}</span>
            </div>
            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100 space-y-1">
                <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Ciudadano Registrante</span>
                <span class="block text-xs font-bold text-slate-800">{{ $empresa->user->nombre ?? 'N/A' }} {{ $empresa->user->apellido ?? '' }}</span>
                <span class="block text-[11px] text-slate-500">C.I: {{ $empresa->user->cedula ?? 'N/A' }}</span>
                <span class="block text-[10px] text-slate-400 mt-0.5">Fecha: {{ $empresa->created_at?->format('d/m/Y H:i') ?? 'N/A' }}</span>
            </div>
        </div>
    </div>

    {{-- BLOQUE 2: Perfil Ambiental y Gestión de Residuos --}}
    @php
        $perfil = $empresa->perfilAmbiental;
        $numRacda = $perfil ? trim($perfil->numero_racda ?? '') : '';
        $tieneRacda = !empty($numRacda) && strtolower($numRacda) !== 'racda no aplica' && strtolower($numRacda) !== 'no aplica';

        $residuosUnificados = [];
        if ($perfil) {
            $comunes     = is_array($perfil->residuos_comunes) ? $perfil->residuos_comunes : json_decode($perfil->residuos_comunes ?? '[]', true);
            $reciclables = is_array($perfil->residuos_reciclables) ? $perfil->residuos_reciclables : json_decode($perfil->residuos_reciclables ?? '[]', true);
            $peligrosos  = is_array($perfil->residuos_peligrosos) ? $perfil->residuos_peligrosos : json_decode($perfil->residuos_peligrosos ?? '[]', true);
            $legacy      = is_array($perfil->residuos_tipos) ? $perfil->residuos_tipos : json_decode($perfil->residuos_tipos ?? '[]', true);

            $residuosUnificados = array_unique(array_merge(
                is_array($comunes) ? $comunes : [],
                is_array($reciclables) ? $reciclables : [],
                is_array($peligrosos) ? $peligrosos : [],
                is_array($legacy) ? $legacy : []
            ));
        }

        if (!is_array($residuosUnificados)) {
            $residuosUnificados = [];
        }

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

    <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-xs space-y-4">
        <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400 border-b border-slate-100 pb-3">Perfil y Diagnóstico Ambiental</h2>
        
        @if($perfil)
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100 space-y-1">
                    <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Rol Operativo Ambiental</span>
                    <span class="block text-sm font-bold text-slate-800">{{ $perfil->rol_ambiental ?? 'GENERADOR' }}</span>
                    @if($perfil->subtipo_generador)
                        <span class="block text-[11px] text-indigo-600 font-semibold">Categoría: {{ $perfil->subtipo_generador }}</span>
                    @endif
                </div>

                <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100 space-y-1">
                    <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Registro RACDA</span>
                    @if($tieneRacda)
                        <span class="block text-sm font-bold text-emerald-600 font-mono">INSCRITO</span>
                        <span class="block text-xs font-mono text-slate-600">N°: {{ $numRacda }}</span>
                    @else
                        <span class="block text-sm font-bold text-slate-500">NO APLICA</span>
                        <span class="block text-[11px] text-slate-400 italic">Exento de registro RACDA</span>
                    @endif
                </div>

                <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100 space-y-1">
                    <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Módulos de Generación</span>
                    <div class="flex flex-wrap gap-1.5 mt-2">
                        @if($perfil->genera_efluentes) <span class="px-2 py-0.5 text-[10px] font-bold bg-sky-50 text-sky-700 rounded-md border border-sky-200">Efluentes</span> @endif
                        @if($perfil->genera_emisiones) <span class="px-2 py-0.5 text-[10px] font-bold bg-amber-50 text-amber-700 rounded-md border border-amber-200">Emisiones</span> @endif
                        @if($perfil->genera_peligrosos) <span class="px-2 py-0.5 text-[10px] font-bold bg-rose-50 text-rose-700 rounded-md border border-rose-200">Peligrosos</span> @endif
                        @if($perfil->genera_ruidos) <span class="px-2 py-0.5 text-[10px] font-bold bg-slate-100 text-slate-700 rounded-md border border-slate-200">Ruidos</span> @endif
                        @if($perfil->usa_pozo_agua) <span class="px-2 py-0.5 text-[10px] font-bold bg-blue-50 text-blue-700 rounded-md border border-blue-200">Pozo Agua</span> @endif

                        @if(!$perfil->genera_efluentes && !$perfil->genera_emisiones && !$perfil->genera_peligrosos && !$perfil->genera_ruidos && !$perfil->usa_pozo_agua)
                            <span class="text-xs text-slate-400 italic">Sin módulos especiales</span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Logística de Residuos --}}
            <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100 space-y-3">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700">Modalidad de Recolección y Logística</h3>

                @if(($perfil->rol_ambiental ?? 'GENERADOR') === 'GENERADOR')
                    @php $modalidad = strtoupper(trim($perfil->modalidad_manejadora ?? 'PROPIA')); @endphp
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                        <div class="p-3 bg-white rounded-xl border border-slate-200">
                            <span class="text-slate-400 block mb-1">Esquema Operativo:</span>
                            @if($modalidad === 'PROPIA' || $modalidad === 'INTERNA')
                                <span class="font-bold text-slate-700">Manejo Propio (Autogestión Interna)</span>
                            @else
                                <span class="font-bold text-indigo-600">Contrata Empresa Manejadora Externa</span>
                            @endif
                        </div>

                        @if($modalidad === 'EXTERNA')
                            <div class="p-3 bg-white rounded-xl border border-slate-200 space-y-1">
                                <span class="text-slate-400 block">Datos del Operador Externo:</span>
                                <p class="text-slate-800 font-bold">{{ $perfil->manejadora_contratada ?? 'No especificada' }}</p>
                                @if($perfil->manejadora_rif) <p class="text-slate-500 font-mono text-[11px]">RIF: {{ $perfil->manejadora_rif }}</p> @endif
                            </div>
                        @endif
                    </div>
                @endif
            </div>

            @if(count($residuosUnificados) > 0)
                <div class="pt-2">
                    <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">Tipos de Residuos Declarados:</span>
                    <div class="flex flex-wrap gap-1.5">
                        @foreach($residuosUnificados as $tipo)
                            @if(is_string($tipo))
                                <span class="px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider bg-slate-100 text-slate-700 rounded-lg border border-slate-200">
                                    {{ str_replace(['_', '"', '[', ']'], [' ', '', '', ''], $tipo) }}
                                </span>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endif
        @else
            <p class="text-xs text-slate-400 italic">No se ha registrado un perfil ambiental para esta empresa.</p>
        @endif
    </div>

    {{-- BLOQUE 3: Expediente Digital --}}
    <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-xs space-y-4">
        <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400 border-b border-slate-100 pb-3">Expediente Digital y Recaudos</h2>
        
        @if($empresa->documentosExpediente && $empresa->documentosExpediente->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                @foreach($empresa->documentosExpediente as $doc)
                    <div class="flex items-center justify-between p-3.5 bg-slate-50 border border-slate-100 rounded-2xl">
                        <div class="truncate mr-3">
                            <div class="flex items-center gap-2">
                                <p class="text-xs font-bold text-slate-800 truncate" title="{{ $doc->nombre_requisito }}">
                                    {{ $doc->nombre_requisito }}
                                </p>
                                <span class="px-2 py-0.5 text-[9px] font-bold uppercase rounded-md border 
                                    {{ ($doc->estado_revision ?? $doc->estado) === 'VALIDO' || ($doc->estado_revision ?? $doc->estado) === 'APROBADO' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-slate-100 text-slate-600 border-slate-200' }}">
                                    {{ $doc->estado_revision ?? $doc->estado ?? 'PENDIENTE' }}
                                </span>
                            </div>
                            <p class="text-[11px] text-slate-400 mt-0.5 font-mono">Cod: {{ $doc->codigo_requisito }}</p>
                        </div>
                        @if($doc->archivo_path)
                            @php
                                $pathLimpio = ltrim($doc->archivo_path, '/');
                                $fileUrl = \Illuminate\Support\Str::startsWith($pathLimpio, 'http') 
                                    ? $pathLimpio 
                                    : asset('storage/' . \Illuminate\Support\Str::after($pathLimpio, 'public/'));
                            @endphp
                            <a href="{{ $fileUrl }}" target="_blank" class="shrink-0 px-3 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-xs font-bold rounded-xl transition">
                                Ver Archivo
                            </a>
                        @else
                            <span class="text-[11px] text-slate-400 italic">Sin archivo</span>
                        @endif
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-xs text-slate-400 italic">No hay documentos cargados en el expediente digital.</p>
        @endif
    </div>

    {{-- BLOQUE 4: Declaraciones de Residuos --}}
    <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-xs space-y-4">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400">Declaraciones de Residuos Comerciales</h2>
            <span class="text-xs font-bold text-slate-600">Total: {{ $empresa->declaracionesResiduos ? $empresa->declaracionesResiduos->count() : 0 }}</span>
        </div>

        @if($empresa->declaracionesResiduos && $empresa->declaracionesResiduos->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-slate-100 text-slate-400 uppercase text-[10px] font-bold">
                            <th class="py-2 px-3">Periodo</th>
                            <th class="py-2 px-3">Frecuencia</th>
                            <th class="py-2 px-3">Comunes (TON)</th>
                            <th class="py-2 px-3">Reciclables (TON)</th>
                            <th class="py-2 px-3">Peligrosas (TON)</th>
                            <th class="py-2 px-3">Estatus</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50 text-slate-700">
                        @foreach($empresa->declaracionesResiduos as $decl)
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="py-3 px-3 font-bold font-mono text-indigo-600">{{ $decl->periodo }}</td>
                                <td class="py-3 px-3 uppercase text-[11px]">{{ $decl->frecuencia }}</td>
                                <td class="py-3 px-3 font-mono">{{ number_format($decl->toneladas_comunes, 3) }}</td>
                                <td class="py-3 px-3 font-mono text-emerald-600 font-bold">{{ number_format($decl->toneladas_reciclables, 3) }}</td>
                                <td class="py-3 px-3 font-mono text-rose-600 font-bold">{{ number_format($decl->toneladas_peligrosas, 3) }}</td>
                                <td class="py-3 px-3">
                                    <span class="px-2.5 py-0.5 text-[10px] font-bold uppercase rounded-md bg-slate-100 text-slate-700">
                                        {{ $decl->estatus }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-xs text-slate-400 italic">La empresa no ha presentado declaraciones cuantitativas hasta la fecha.</p>
        @endif
    </div>

    {{-- BLOQUE 5: Sanciones y Multas --}}
    <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-xs space-y-4">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h2 class="text-xs font-bold uppercase tracking-wider text-rose-600">Control de Sanciones e Infracciones</h2>
            <span class="text-xs font-bold text-slate-600">Total: {{ $empresa->sanciones ? $empresa->sanciones->count() : 0 }}</span>
        </div>

        @if($empresa->sanciones && $empresa->sanciones->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-slate-100 text-slate-400 uppercase text-[10px] font-bold">
                            <th class="py-2 px-3">Código</th>
                            <th class="py-2 px-3">Tipo / Motivo</th>
                            <th class="py-2 px-3">Gravedad</th>
                            <th class="py-2 px-3">Fecha Incidencia</th>
                            <th class="py-2 px-3">Estatus</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50 text-slate-700">
                        @foreach($empresa->sanciones as $sancion)
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="py-3 px-3 font-bold font-mono text-rose-600">{{ $sancion->codigo_expediente ?? 'N/A' }}</td>
                                <td class="py-3 px-3">
                                    <strong class="block text-slate-800">{{ $sancion->tipo_infraccion }}</strong>
                                    <span class="text-[11px] text-slate-500">{{ $sancion->motivo_descripcion }}</span>
                                </td>
                                <td class="py-3 px-3">
                                    <span class="px-2 py-0.5 text-[10px] font-bold rounded uppercase bg-rose-100 text-rose-800">
                                        {{ $sancion->gravedad }}
                                    </span>
                                </td>
                                <td class="py-3 px-3 text-slate-500">{{ $sancion->fecha_incidencia ? \Carbon\Carbon::parse($sancion->fecha_incidencia)->format('d/m/Y') : 'N/A' }}</td>
                                <td class="py-3 px-3">
                                    <span class="px-2.5 py-0.5 text-[10px] font-bold uppercase rounded-md bg-rose-50 text-rose-700">
                                        {{ $sancion->estatus ?? 'ACTIVA' }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-xs text-slate-400 italic">La empresa no registra sanciones o multas ambientales activas.</p>
        @endif
    </div>

    {{-- BLOQUE 6: Veredicto Directoral --}}
    <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-xs space-y-4">
        <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400 border-b border-slate-100 pb-3">Emisión de Veredicto Directoral</h2>
        
        @if(in_array(strtoupper($empresa->estatus), ['APROBADO', 'RECHAZADO']))
            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 space-y-2">
                <div class="flex items-center justify-between text-xs">
                    <span class="font-bold text-slate-500 uppercase">Estado Final del Expediente:</span>
                    <span class="px-3 py-1 font-bold rounded-lg uppercase {{ strtoupper($empresa->estatus) === 'APROBADO' ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                        {{ $empresa->estatus }}
                    </span>
                </div>
                <div>
                    <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Dictamen Registrado:</span>
                    <p class="text-xs text-slate-700 bg-white p-3 rounded-xl border border-slate-200 italic">
                        {{ $empresa->observaciones_fiscalia ?? 'Sin observaciones especificadas.' }}
                    </p>
                </div>
            </div>
        @else
            <form action="{{ route('director.residuos.empresas.evaluar', $empresa->id) }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label for="observaciones_fiscalia" class="block text-xs font-bold text-slate-700 mb-1">Observaciones / Dictamen Técnico</label>
                    <textarea id="observaciones_fiscalia" name="observaciones_fiscalia" rows="3" 
                              class="w-full bg-slate-50 border border-slate-200 rounded-2xl p-3 text-xs text-slate-800 placeholder-slate-400 focus:bg-white transition outline-none"
                              placeholder="Ingrese los comentarios o justificaciones de la decisión...">{{ old('observaciones_fiscalia', $empresa->observaciones_fiscalia) }}</textarea>
                </div>

                <div class="flex flex-wrap items-center justify-end gap-2.5 pt-2">
                    <button type="submit" name="estatus" value="OBSERVADO" 
                            class="px-4 py-2.5 bg-amber-50 hover:bg-amber-100 text-amber-700 border border-amber-200 font-bold text-xs rounded-xl transition cursor-pointer">
                        Marcar Observado
                    </button>
                    <button type="submit" name="estatus" value="RECHAZADO" 
                            class="px-4 py-2.5 bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 font-bold text-xs rounded-xl transition cursor-pointer">
                        Rechazar Solicitud
                    </button>
                    <button type="submit" name="estatus" value="APROBADO" 
                            class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow-xs transition cursor-pointer">
                        Aprobar Expediente
                    </button>
                </div>
            </form>
        @endif
    </div>

</div>
</x-layouts.app>