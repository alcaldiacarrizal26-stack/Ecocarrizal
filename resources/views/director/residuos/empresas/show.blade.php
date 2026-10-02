<x-layouts.app title="Detalles de la Empresa" active="residuos_empresas">
<div class="space-y-6 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    
    {{-- Header & Navegación --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-slate-900/90 border border-slate-800 p-6 rounded-2xl shadow-xl">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-bold text-white tracking-tight">{{ $empresa->razon_social }}</h1>
                <span class="px-2.5 py-1 text-xs font-semibold rounded-full border bg-slate-800 text-slate-300 border-slate-700">
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
            {{-- 1. Ruta de retorno --}}
            <a href="{{ route('director.residuos.empresas.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 text-sm font-medium rounded-xl transition-all border border-slate-700">
                &larr; Volver a empresas
            </a>
        </div>
    </div>

    <!-- Alertas de Errores de Validación -->
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

    {{-- BLOQUE 1: Datos Generales (3 Columnas) --}}
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
                <span class="block text-xs text-slate-400 mt-0.5">{{ $empresa->sector }}, {{ $empresa->calle_avenida }}</span>
            </div>
        </div>
    </div>

    {{-- BLOQUE 2: Perfil Ambiental (3 Columnas) --}}
    @if($empresa->perfilAmbiental)
    <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-6 shadow-xl">
        <h2 class="text-sm font-bold uppercase tracking-wider text-emerald-400 mb-4 border-b border-slate-800 pb-2">Perfil y Diagnóstico Ambiental</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-slate-950/50 p-4 rounded-xl border border-slate-800/60">
                <span class="block text-xs font-medium text-slate-400 uppercase">Actividad Económica</span>
                <span class="block text-sm font-semibold text-slate-100 mt-1">{{ $empresa->perfilAmbiental->actividad_economica ?? 'No especificado' }}</span>
            </div>
            <div class="bg-slate-950/50 p-4 rounded-xl border border-slate-800/60">
                <span class="block text-xs font-medium text-slate-400 uppercase">Tipo de Desechos</span>
                <span class="block text-sm font-semibold text-slate-100 mt-1">{{ $empresa->perfilAmbiental->tipo_desechos ?? 'No especificado' }}</span>
            </div>
            <div class="bg-slate-950/50 p-4 rounded-xl border border-slate-800/60">
                <span class="block text-xs font-medium text-slate-400 uppercase">Generación Estimada</span>
                <span class="block text-sm font-semibold text-slate-100 mt-1">{{ $empresa->perfilAmbiental->volumen_estimado ?? 'N/A' }}</span>
            </div>
        </div>
    </div>
    @endif

    {{-- BLOQUE 3: Documentación Consolidada --}}
    <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-6 shadow-xl">
        <h2 class="text-sm font-bold uppercase tracking-wider text-emerald-400 mb-4 border-b border-slate-800 pb-2">Expediente Digital y Recaudos</h2>
        
        @if($empresa->documentosExpediente && $empresa->documentosExpediente->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($empresa->documentosExpediente as $doc)
                    <div class="flex items-center justify-between p-3.5 bg-slate-950/60 border border-slate-800/80 rounded-xl hover:border-slate-700 transition-colors">
                        <div class="truncate mr-3">
                            <p class="text-sm font-semibold text-slate-200 truncate">{{ $doc->tipo_documento ?? 'Documento Adjunto' }}</p>
                            <p class="text-xs text-slate-400 mt-0.5">Subido: {{ $doc->created_at?->format('d/m/Y') }}</p>
                        </div>
                        <a href="{{ Route::has('expedientes.documento.ver') ? route('expedientes.documento.ver', $doc) : Storage::url($doc->ruta_archivo ?? '') }}" 
                           target="_blank" 
                           class="shrink-0 px-3 py-1.5 bg-emerald-600/20 text-emerald-300 hover:bg-emerald-600/30 border border-emerald-500/30 text-xs font-semibold rounded-lg transition-colors flex items-center gap-1">
                            Ver PDF
                        </a>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-sm text-slate-400 italic">No hay documentos registrados para este expediente.</p>
        @endif
    </div>

    {{-- BLOQUE 4: Emisión de Veredicto / Evaluación --}}
    <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-6 shadow-xl">
        <h2 class="text-sm font-bold uppercase tracking-wider text-emerald-400 mb-4 border-b border-slate-800 pb-2">Emisión de Veredicto Directoral</h2>
        
        <form action="{{ route('director.residuos.empresas.evaluar', $empresa) }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label for="observaciones_fiscalia" class="block text-sm font-medium text-slate-300 mb-1">Observaciones / Dictamen Técnico</label>
                {{-- 2. Corrección del atributo name --}}
                <textarea id="observaciones_fiscalia" name="observaciones_fiscalia" rows="4" 
                          class="w-full bg-slate-950 border border-slate-800 rounded-xl p-3 text-slate-100 placeholder-slate-500 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition-all"
                          placeholder="Ingrese los comentarios o justificaciones de la decisión...">{{ old('observaciones_fiscalia', $empresa->observaciones_fiscalia) }}</textarea>
            </div>

            {{-- 3. Corrección de nombres de botones para coincidir con la validación del controlador --}}
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
    </div>

</div>
</x-layouts.app>