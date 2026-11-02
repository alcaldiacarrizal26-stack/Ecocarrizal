<x-layouts.app title="Evaluación de Declaración de Residuos" active="director_residuos_declaraciones">
    <div class="p-6 max-w-5xl mx-auto space-y-6 bg-slate-50 min-h-screen">
        
        <!-- Header de Navegación -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
            <div>
                <div class="flex items-center gap-3">
                    <h1 class="text-xl font-bold text-slate-800 tracking-tight">Declaración Período: {{ $declaracion->periodo }}</h1>
                    <span class="px-3 py-1 text-xs font-bold rounded-full uppercase 
                        {{ $declaracion->estatus === 'APROBADA' ? 'bg-emerald-100 text-emerald-800 border border-emerald-300' : ($declaracion->estatus === 'RECHAZADA' ? 'bg-rose-100 text-rose-800 border border-rose-300' : 'bg-blue-100 text-blue-800 border border-blue-300') }}">
                        {{ $declaracion->estatus }}
                    </span>
                </div>
                <p class="text-xs text-slate-500 mt-1">
                    Establecimiento: <strong class="text-slate-700">{{ $declaracion->empresa->razon_social ?? 'N/A' }}</strong> (RIF: {{ $declaracion->empresa->rif ?? 'N/A' }})
                </p>
            </div>
            <div>
                <a href="{{ route('director.residuos.declaraciones.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition-all border border-slate-300">
                    &larr; Volver al listado
                </a>
            </div>
        </div>

        <!-- Alertas de Sesión -->
        @if (session('success'))
            <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs flex items-center gap-2.5 shadow-sm">
                <svg class="w-4 h-4 shrink-0 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <!-- Errores de Validación -->
        @if ($errors->any())
            <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs space-y-1">
                <p class="font-bold">Corrige los siguientes campos:</p>
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            
            <!-- Resumen Cuantitativo, Evidencias Generales y Desglose (2 Columnas) -->
            <div class="md:col-span-2 space-y-6">
                
                <!-- Resumen Cuantitativo y Fechas -->
                <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-4">
                    <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400 border-b border-slate-100 pb-3">Resumen de Toneladas Declaradas</h2>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div class="bg-slate-50 p-4 rounded-xl border border-slate-100 space-y-1">
                            <span class="block text-[10px] font-bold text-slate-400 uppercase">Desechos Comunes</span>
                            <span class="block text-lg font-mono font-bold text-slate-800">{{ number_format($declaracion->toneladas_comunes, 3) }} <span class="text-xs font-normal text-slate-500">TON</span></span>
                        </div>
                        <div class="bg-slate-50 p-4 rounded-xl border border-slate-100 space-y-1">
                            <span class="block text-[10px] font-bold text-emerald-600 uppercase">Desechos Reciclables</span>
                            <span class="block text-lg font-mono font-bold text-emerald-700">{{ number_format($declaracion->toneladas_reciclables, 3) }} <span class="text-xs font-normal text-slate-500">TON</span></span>
                        </div>
                        <div class="bg-slate-50 p-4 rounded-xl border border-slate-100 space-y-1">
                            <span class="block text-[10px] font-bold text-amber-600 uppercase">Desechos Peligrosos</span>
                            <span class="block text-lg font-mono font-bold text-amber-700">{{ number_format($declaracion->toneladas_peligrosas, 3) }} <span class="text-xs font-normal text-slate-500">TON</span></span>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-2 text-xs">
                        <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                            <span class="text-slate-400 block font-semibold uppercase text-[10px]">Rol Declarante</span>
                            <span class="font-bold text-slate-800 uppercase mt-0.5 block">{{ $declaracion->rol_declarante }}</span>
                        </div>
                        <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                            <span class="text-slate-400 block font-semibold uppercase text-[10px]">Fecha Inicio</span>
                            <span class="font-bold text-slate-800 font-mono mt-0.5 block">
                                {{ $declaracion->fecha_inicio_periodo ? \Carbon\Carbon::parse($declaracion->fecha_inicio_periodo)->format('d/m/Y') : 'N/A' }}
                            </span>
                        </div>
                        <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                            <span class="text-slate-400 block font-semibold uppercase text-[10px]">Fecha Fin</span>
                            <span class="font-bold text-slate-800 font-mono mt-0.5 block">
                                {{ $declaracion->fecha_fin_periodo ? \Carbon\Carbon::parse($declaracion->fecha_fin_periodo)->format('d/m/Y') : 'N/A' }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- 1. Archivos / Evidencias Generales Anexadas -->
                @php
                    $archivosGenerales = is_string($declaracion->manifiestos_archivos) 
                        ? json_decode($declaracion->manifiestos_archivos, true) 
                        : $declaracion->manifiestos_archivos;
                @endphp
                @if(!empty($archivosGenerales) && is_array($archivosGenerales))
                    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-4">
                        <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400 border-b border-slate-100 pb-3">Documentos y Archivos Generales Anexados</h2>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            @foreach($archivosGenerales as $archivo)
                                @php
                                    $rawPath = $archivo['path'] ?? '';
                                    $pathLimpio = str_replace(['public/', 'storage/'], '', ltrim($rawPath, '/'));
                                    $fileUrl = route('storage.view', ['path' => $pathLimpio]);
                                @endphp
                                <div class="flex items-center justify-between p-3.5 bg-slate-50 border border-slate-100 rounded-xl text-xs">
                                    <div class="truncate mr-2">
                                        <p class="font-bold text-slate-800 truncate" title="{{ $archivo['nombre_original'] ?? 'Archivo General' }}">
                                            {{ $archivo['nombre_original'] ?? 'Archivo General' }}
                                        </p>
                                        <span class="text-[10px] text-slate-400 uppercase">{{ $archivo['mime_type'] ?? 'Documento' }}</span>
                                    </div>
                                    <a href="{{ $fileUrl }}" target="_blank" class="shrink-0 px-3 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-bold rounded-lg transition">
                                        Ver Archivo
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- 2. Desglose de Residuos con sus Manifiestos Individuales -->
                <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-4">
                    <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400 border-b border-slate-100 pb-3">Desglose de Tipos de Residuos y Manifiestos Adjuntos</h2>
                    
                    @if($declaracion->detalles && $declaracion->detalles->count() > 0)
                        <div class="overflow-x-auto rounded-xl border border-slate-100">
                            <table class="w-full text-left text-xs divide-y divide-slate-100">
                                <thead class="bg-slate-50 text-slate-400 uppercase text-[10px] font-bold">
                                    <tr>
                                        <th class="py-3 px-4">Categoría / Operación</th>
                                        <th class="py-3 px-4">Nombre del Residuo</th>
                                        <th class="py-3 px-4">Cantidad Original</th>
                                        <th class="py-3 px-4">Cantidad (TON)</th>
                                        <th class="py-3 px-4 text-right">Manifiesto / Evidencia</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-50 text-slate-700 bg-white">
                                    @foreach($declaracion->detalles as $det)
                                        <tr class="hover:bg-slate-50/50 transition">
                                            <td class="py-3.5 px-4">
                                                <span class="font-bold text-slate-800 block">{{ $det->categoria ?? 'N/A' }}</span>
                                                <span class="text-[10px] text-slate-400 uppercase">{{ $det->tipo_operacion ?? 'N/A' }}</span>
                                            </td>
                                            <td class="py-3.5 px-4 font-semibold text-slate-700">{{ $det->nombre_residuo ?? 'N/A' }}</td>
                                            <td class="py-3.5 px-4 font-mono text-slate-600">{{ number_format($det->cantidad ?? 0, 2) }} {{ $det->unidad_medida }}</td>
                                            <td class="py-3.5 px-4 font-mono font-bold text-indigo-600">{{ number_format($det->cantidad_toneladas ?? 0, 3) }} TON</td>
                                            <td class="py-3.5 px-4 text-right">
                                                @if(!empty($det->manifiesto_path))
                                                    @php
                                                        $rawDetPath = $det->manifiesto_path;
                                                        $pathDetalle = str_replace(['public/', 'storage/'], '', ltrim($rawDetPath, '/'));
                                                        $urlDetalle = route('storage.view', ['path' => $pathDetalle]);
                                                    @endphp
                                                    <a href="{{ $urlDetalle }}" target="_blank" class="inline-flex items-center px-2.5 py-1 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 font-bold rounded-lg transition border border-emerald-200">
                                                        Ver Manifiesto
                                                    </a>
                                                @else
                                                    <span class="text-slate-400 italic">Sin archivo</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="p-4 bg-slate-50 rounded-xl border border-slate-100 text-center">
                            <p class="text-xs text-slate-400 italic">No hay ítems específicos desglosados en esta declaración.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Panel de Veredicto Directoral -->
            <div class="space-y-6">
                <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-4">
                    <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400 border-b border-slate-100 pb-3">Dictamen y Veredicto</h2>
                    
                    <form action="{{ route('director.residuos.declaraciones.update', $declaracion->id) }}" method="POST" class="space-y-4"
                          x-data="{ submitting: false, estatusSeleccionado: '{{ $declaracion->estatus }}' }"
                          @submit="if(submitting) { $event.preventDefault(); } else { submitting = true; }">
                        @csrf
                        @method('PUT')
                        
                        <input type="hidden" name="estatus" x-model="estatusSeleccionado">

                        <div x-show="estatusSeleccionado === 'RECHAZADA'" x-cloak class="space-y-1">
                            <label for="motivo_rechazo" class="block text-xs font-bold text-rose-600 mb-1">Motivo del Rechazo *</label>
                            <textarea id="motivo_rechazo" name="motivo_rechazo" rows="3" 
                                      class="w-full bg-rose-50/50 border border-rose-200 rounded-xl p-3 text-xs text-slate-800 focus:bg-white transition outline-none"
                                      placeholder="Explique detalladamente el motivo por el cual se rechaza la declaración..."></textarea>
                        </div>

                        <div>
                            <label for="observaciones" class="block text-xs font-semibold text-slate-700 mb-1">Observaciones Generales (Opcional)</label>
                            <textarea id="observaciones" name="observaciones" rows="3" 
                                      class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3 text-xs text-slate-800 focus:bg-white transition outline-none"
                                      placeholder="Comentarios adicionales para el contribuyente...">{{ old('observaciones', $declaracion->observaciones) }}</textarea>
                        </div>

                        <div class="flex flex-col gap-2.5 pt-2">
                            <button type="submit" @click="estatusSeleccionado = 'APROBADA'"
                                    class="w-full py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-sm transition cursor-pointer">
                                Aprobar Declaración
                            </button>
                            <button type="submit" @click="estatusSeleccionado = 'RECHAZADA'"
                                    class="w-full py-3 bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 font-bold text-xs rounded-xl transition cursor-pointer">
                                Rechazar Declaración
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</x-layouts.app>