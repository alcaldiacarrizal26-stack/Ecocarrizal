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

    {{-- BLOQUE 2: Perfil Ambiental Dinámico (Estética Clara y Lógica por Rol) --}}
    @if($empresa->perfilAmbiental)
    @php
        $perfil = $empresa->perfilAmbiental;
        $esManejador = strtoupper($perfil->rol_ambiental) === 'MANEJADOR';
    @endphp
    <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-xs space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between border-b border-slate-100 pb-3 gap-3">
            <div>
                <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400">Perfil y Diagnóstico Ambiental</h2>
                <p class="text-xs text-slate-600 mt-0.5">Clasificación operativa y parámetros de control ecológico</p>
            </div>
            <span class="px-3 py-1 text-xs font-extrabold rounded-full border self-start sm:self-auto {{ $esManejador ? 'bg-amber-50 text-amber-700 border-amber-200' : 'bg-emerald-50 text-emerald-700 border-emerald-200' }}">
                ROL: {{ $perfil->rol_ambiental ?? 'GENERADOR' }}
            </span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @if(!$esManejador)
                <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100 space-y-1">
                    <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Clasificación Generador</span>
                    <span class="block text-sm font-bold text-slate-800 mt-1">{{ $perfil->subtipo_generador ?? 'No especificado' }}</span>
                </div>
                <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100 space-y-1">
                    <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">N° RACDA Principal</span>
                    <span class="block text-sm font-bold text-emerald-700 font-mono mt-1">{{ $perfil->numero_racda ?? 'N/A' }}</span>
                </div>
                <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100 space-y-1">
                    <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Modalidad de Manejo</span>
                    <span class="block text-sm font-bold text-slate-800 mt-1">{{ $perfil->modalidad_manejadora ?? 'Externa' }}</span>
                    @if($perfil->manejadora_nombre)
                        <span class="block text-[11px] text-slate-500 mt-0.5">Contratada: <strong class="text-slate-700">{{ $perfil->manejadora_nombre }}</strong> (RIF: {{ $perfil->manejadora_rif }})</span>
                    @endif
                </div>
            @else
                <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100 space-y-1">
                    <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">N° RACDA Manejador</span>
                    <span class="block text-sm font-bold text-amber-700 font-mono mt-1">{{ $perfil->numero_racda ?? 'N/A' }}</span>
                </div>
                <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100 space-y-1">
                    <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Sustancias / Materiales Manejados</span>
                    <span class="block text-sm font-bold text-slate-800 mt-1">{{ $perfil->sustancias_manejadas ?? 'No especificado' }}</span>
                </div>
                <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100 space-y-1">
                    <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Alcance de Atención</span>
                    <span class="block text-sm font-bold text-slate-800 mt-1">{{ $perfil->modalidad_atencion_manejador ?? 'MULTICLIENTE' }}</span>
                    @if($perfil->generador_exclusivo_nombre)
                        <span class="block text-[11px] text-slate-500 mt-0.5">Exclusivo para: <strong class="text-slate-700">{{ $perfil->generador_exclusivo_nombre }}</strong></span>
                    @endif
                </div>
            @endif
        </div>

        {{-- Parámetros según Rol --}}
        @if(!$esManejador)
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 pt-2 text-xs">
                <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100 flex items-center justify-between">
                    <span class="text-slate-500 font-medium">Efluentes Líquidos</span>
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $perfil->genera_efluentes ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-200 text-slate-600' }}">{{ $perfil->genera_efluentes ? 'SÍ' : 'NO' }}</span>
                </div>
                <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100 flex items-center justify-between">
                    <span class="text-slate-500 font-medium">Emisiones Atmosf.</span>
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $perfil->genera_emisiones ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-200 text-slate-600' }}">{{ $perfil->genera_emisiones ? 'SÍ' : 'NO' }}</span>
                </div>
                <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100 flex items-center justify-between">
                    <span class="text-slate-500 font-medium">Desechos Peligrosos</span>
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $perfil->genera_peligrosos ? 'bg-amber-100 text-amber-800' : 'bg-slate-200 text-slate-600' }}">{{ $perfil->genera_peligrosos ? 'SÍ' : 'NO' }}</span>
                </div>
                <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100 flex items-center justify-between">
                    <span class="text-slate-500 font-medium">Pozo de Agua</span>
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $perfil->usa_pozo_agua ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-200 text-slate-600' }}">{{ $perfil->usa_pozo_agua ? 'SÍ' : 'NO' }}</span>
                </div>
            </div>
        @else
            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 space-y-2">
                <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Actividades de Manejo Autorizadas</span>
                <div class="flex flex-wrap gap-2">
                    @if(!empty($perfil->actividades_manejador))
                        @foreach($perfil->actividades_manejador as $actividad)
                            <span class="px-3 py-1 bg-emerald-100 text-emerald-800 border border-emerald-200 rounded-xl text-xs font-bold uppercase">
                                {{ str_replace('_', ' ', $actividad) }}
                            </span>
                        @endforeach
                    @else
                        <span class="text-xs text-slate-400 italic">No se especificaron actividades secundarias de manejo.</span>
                    @endif
                </div>
            </div>
        @endif
    </div>
    @endif

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

    {{-- BLOQUE 6: Veredicto Directoral (Con Input Oculto Corregido) --}}
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
            <form action="{{ route('director.residuos.empresas.evaluar', $empresa->id) }}" 
                  method="POST" 
                  class="space-y-4"
                  x-data="{ submitting: false, estatusSeleccionado: '' }"
                  @submit="if(submitting) { $event.preventDefault(); } else { submitting = true; }">
                @csrf
                <input type="hidden" name="estatus" x-model="estatusSeleccionado">

                <div>
                    <label for="observaciones_fiscalia" class="block text-xs font-bold text-slate-700 mb-1">Observaciones / Dictamen Técnico</label>
                    <textarea id="observaciones_fiscalia" name="observaciones_fiscalia" rows="3" 
                              class="w-full bg-slate-50 border border-slate-200 rounded-2xl p-3 text-xs text-slate-800 placeholder-slate-400 focus:bg-white transition outline-none"
                              placeholder="Ingrese los comentarios o justificaciones de la decisión...">{{ old('observaciones_fiscalia', $empresa->observaciones_fiscalia) }}</textarea>
                </div>

                <div class="flex flex-wrap items-center justify-end gap-2.5 pt-2">
                    <button type="submit" 
                            @click="estatusSeleccionado = 'OBSERVADO'"
                            :disabled="submitting"
                            :class="submitting ? 'opacity-50 cursor-not-allowed' : ''"
                            class="px-4 py-2.5 bg-amber-50 hover:bg-amber-100 text-amber-700 border border-amber-200 font-bold text-xs rounded-xl transition cursor-pointer">
                        <span x-text="submitting && estatusSeleccionado === 'OBSERVADO' ? 'Procesando...' : 'Marcar Observado'"></span>
                    </button>
                    <button type="submit" 
                            @click="estatusSeleccionado = 'RECHAZADO'"
                            :disabled="submitting"
                            :class="submitting ? 'opacity-50 cursor-not-allowed' : ''"
                            class="px-4 py-2.5 bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 font-bold text-xs rounded-xl transition cursor-pointer">
                        <span x-text="submitting && estatusSeleccionado === 'RECHAZADO' ? 'Procesando...' : 'Rechazar Solicitud'"></span>
                    </button>
                    <button type="submit" 
                            @click="estatusSeleccionado = 'APROBADO'"
                            :disabled="submitting"
                            :class="submitting ? 'opacity-50 cursor-not-allowed' : ''"
                            class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow-xs transition cursor-pointer">
                        <span x-text="submitting && estatusSeleccionado === 'APROBADO' ? 'Procesando...' : 'Aprobar Expediente'"></span>
                    </button>
                </div>
            </form>
        @endif
    </div>

</div>
</x-layouts.app>