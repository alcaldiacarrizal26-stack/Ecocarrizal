<x-layouts.app title="Evaluar Inspección Ambiental" active="inspecciones">
    <div class="p-6 max-w-4xl mx-auto space-y-6">
        
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-slate-800">Acta de Evaluación de Campo</h1>
            <a href="{{ route('inspecciones.index') }}" class="text-sm font-medium text-slate-500 hover:text-slate-800 transition">
                ← Volver al listado
            </a>
        </div>

        <!-- Alerta global de errores de validación -->
        @if ($errors->any())
            <div class="p-4 rounded-xl bg-red-50 border border-red-200 text-red-700 text-sm space-y-1">
                <strong class="font-semibold block">Por favor revisa los siguientes errores antes de guardar:</strong>
                <ul class="list-disc list-inside space-y-0.5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @php
            // Determinamos si el expediente actual es una Orden de Empresa ('orden') o un trámite tradicional
            $esOrdenEmpresa = isset($tipoOrigen) &&$tipoOrigen === 'orden';
            $evaluador = auth()->user();$nombreEvaluador = $evaluador->nombre ?? $evaluador->name ?? 'Funcionario';
            $apellidoEvaluador =$evaluador->apellido ?? '';
            $cargoEvaluador =$evaluador->role->nombre ?? 'Inspector Ambiental';

            if ($esOrdenEmpresa) {
                $empresa =$expediente->empresa ?? null;
                $solicitante =$empresa->user ?? null;
                $sector =$empresa->sector ?? 'N/E';
                $comuna =$empresa->comuna ?? 'N/E';
                $esTala = false;
            } else {
                $solicitante =$expediente->solicitante ?? $expediente->user ?? $expediente->usuario ?? null;
                $sector = $expediente->sector ?? $expediente->ubicacion_sector ?? 'N/E';
                $comuna = $expediente->comuna ?? $expediente->ubicacion_comuna ?? 'N/E';
                $esTala = ($tipoOrigen === 'solicitud' && ($expediente->tipo_tramite ?? '') === 'tala');
            }
        @endphp

        <form action="{{ route('inspecciones.store') }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm space-y-6">
            @csrf

            <!-- Campos ocultos requeridos para procesar el origen -->
            <input type="hidden" name="tipo_origen" value="{{ $tipoOrigen }}">
            <input type="hidden" name="origen_id" value="{{ $expediente->id }}">

            <!-- Metadata del Expediente, Solicitante e Inspector -->
            <div class="bg-slate-50 p-4 rounded-xl border border-slate-100 grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                <div>
                    <span class="text-slate-500 block text-xs uppercase tracking-wider font-semibold">Origen:</span>
                    <strong class="text-slate-800">
                        @if($esOrdenEmpresa)
                            Fiscalización de Residuos ({{ $expediente->tipo_inspeccion ?? 'ORDINARIA' }})
                        @else
                            {{ $tipoOrigen === 'solicitud' ? 'Trámite: ' . strtoupper($expediente->tipo_tramite ?? 'Solicitud') : 'Denuncia Ambiental' }}
                        @endif
                    </strong>
                </div>

                <div>
                    <span class="text-slate-500 block text-xs uppercase tracking-wider font-semibold">Código Referencia / Orden:</span>
                    <strong class="text-slate-800">{{ $expediente->codigo_orden ?? $expediente->codigo_expediente ?? $expediente->codigo }}</strong>
                </div>

                <!-- Datos del Solicitante / Representante de la Empresa -->
                <div>
                    <span class="text-slate-500 block text-xs uppercase tracking-wider font-semibold">{{ $esOrdenEmpresa ? 'Razón Social / Empresa:' : 'Solicitante / Denunciante:' }}</span>
                    <strong class="text-slate-800">
                        @if($esOrdenEmpresa)
                            {{ $empresa->razon_social ?? 'Empresa no vinculada' }}
                        @elseif($solicitante && !empty($solicitante->nombre ?? $solicitante->name))
                            {{ trim(($solicitante->nombre ?? $solicitante->name) . ' ' . ($solicitante->apellido ?? '')) }}
                        @else
                            {{ $expediente->nombre_completo ?? 'No registrado' }}
                        @endif
                    </strong>
                    <span class="block text-xs text-slate-500">
                        {{ $esOrdenEmpresa ? 'RIF: ' . ($empresa->rif ?? 'N/A') : 'C.I. / RIF: ' . ($solicitante->cedula ?? $expediente->cedula ?? 'N/A') }}
                    </span>
                </div>

                <!-- Funcionario Evaluador -->
                <div>
                    <span class="text-slate-500 block text-xs uppercase tracking-wider font-semibold">Funcionario Evaluador:</span>
                    <strong class="text-slate-800">
                        {{ trim($nombreEvaluador . ' ' .$apellidoEvaluador) }}
                    </strong>
                    <span class="block text-xs text-slate-500">
                        C.I.: {{ $evaluador->cedula ?? 'N/A' }} | Cargo: {{ ucwords($cargoEvaluador) }}
                    </span>
                </div>

                <div>
                    <span class="text-slate-500 block text-xs uppercase tracking-wider font-semibold">Ubicación:</span>
                    <strong class="text-slate-800">
                        @if($esOrdenEmpresa)
                            {{ $empresa->calle_avenida ?? '' }}, {{ $sector }}, {{$comuna }}
                        @else
                            {{ $sector }}, {{$comuna }}
                        @endif
                    </strong>
                </div>

                <div>
                    <span class="text-slate-500 block text-xs uppercase tracking-wider font-semibold">Detalles / Base Legal / Motivo:</span>
                    <strong class="text-slate-800">
                        @if($esOrdenEmpresa)
                            {{ $expediente->base_legal_inspeccion }} - {{ Str::limit($expediente->observaciones_campo, 40) }}
                        @else
                            {{ Str::limit($expediente->observaciones_campo ?? $expediente->motivo_descripcion ?? $expediente->descripcion, 60) }}
                        @endif
                    </strong>
                </div>
            </div>

            <!-- Campos del Formulario de Inspección -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Fecha y Hora de la Visita</label>
                    <input type="datetime-local" name="fecha_visita" value="{{ old('fecha_visita', now()->format('Y-m-d\TH:i')) }}" required
                           class="w-full rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 text-sm @error('fecha_visita') border-red-500 @enderror">
                    @error('fecha_visita')
                        <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Dictamen de la Inspección</label>
                    <select name="dictamen" id="dictamen_select" required onchange="toggleCompensacionSection()" class="w-full rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 text-sm @error('dictamen') border-red-500 @enderror">
                        <option value="APROBADO" {{ old('dictamen') === 'APROBADO' ? 'selected' : '' }}>APROBADO (Conforme)</option>
                        <option value="CON_OBSERVACIONES" {{ old('dictamen', 'CON_OBSERVACIONES') === 'CON_OBSERVACIONES' ? 'selected' : '' }}>CON OBSERVACIONES (Requiere adecuación)</option>
                        <option value="REINSPECCION_REQUERIDA" {{ old('dictamen') === 'REINSPECCION_REQUERIDA' ? 'selected' : '' }}>REINSPECCIÓN REQUERIDA</option>
                        <option value="RECHAZADO" {{ old('dictamen') === 'RECHAZADO' ? 'selected' : '' }}>RECHAZADO (No conforme / Improcedente)</option>
                    </select>
                    @error('dictamen')
                        <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <!-- Módulo Condicional de Compensación Ambiental por Tala -->
            @if($esTala)
                <div id="seccion_compensacion" class="p-5 rounded-2xl bg-emerald-50/60 border border-emerald-200 space-y-4 transition-all duration-300 hidden">
                    <div class="flex items-center gap-2 text-emerald-800 font-semibold border-b border-emerald-200/80 pb-2">
                        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                        <span>Cálculo de Compensación Ambiental (Tala Autorizada)</span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Árboles Autorizados a Talar</label>
                            <input type="number" name="cantidad_arboles_talar" id="cant_talar" min="1" value="{{ old('cantidad_arboles_talar', 1) }}"
                                   oninput="recalcularCompensacion()"
                                   class="w-full rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Especie a Solicitar (Entrega)</label>
                            <select name="especie_compensacion" class="w-full rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                                <option value="Araguaney" {{ old('especie_compensacion') === 'Araguaney' ? 'selected' : '' }}>Araguaney (Árbol Nacional)</option>
                                <option value="Apamate" {{ old('especie_compensacion') === 'Apamate' ? 'selected' : '' }}>Apamate</option>
                                <option value="Bucare" {{ old('especie_compensacion') === 'Bucare' ? 'selected' : '' }}>Bucare</option>
                                <option value="Cuji Yaque" {{ old('especie_compensacion') === 'Cuji Yaque' ? 'selected' : '' }}>Cují Yaque</option>
                                <option value="Caoba" {{ old('especie_compensacion') === 'Caoba' ? 'selected' : '' }}>Caoba</option>
                            </select>
                        </div>

                        <div class="flex items-center pt-5">
                            <label class="relative flex items-center gap-2 cursor-pointer select-none">
                                <input type="checkbox" name="es_especie_protegida" value="1" id="check_protegida" onchange="recalcularCompensacion()"
                                       {{ old('es_especie_protegida') ? 'checked' : '' }}
                                       class="rounded text-emerald-600 focus:ring-emerald-500 h-4 w-4">
                                <span class="text-xs font-semibold text-amber-900">¿Especie Protegida / Vedada? (Ratio 1:16)</span>
                            </label>
                        </div>
                    </div>

                    <div class="bg-white p-3 rounded-xl border border-emerald-200/60 flex items-center justify-between text-xs sm:text-sm">
                        <span class="text-slate-600">Relación de Compensación Aplicada: <strong id="ratio_text" class="text-slate-800">1:8 (Estándar)</strong></span>
                        <div class="text-emerald-800 font-bold">
                            Total a Compensar: <span id="total_compensar_badge" class="text-base text-emerald-600 font-extrabold">8</span> ejemplares
                        </div>
                    </div>
                </div>
            @endif

            <!-- Observaciones Técnicas -->
            <div>
                <div class="flex items-center justify-between mb-1">
                    <label class="block text-sm font-medium text-slate-700">Observaciones de Campo / Diagnóstico Técnico</label>
                    <span id="counter_observaciones" class="text-xs font-semibold text-amber-600">0 / 10 caracteres mín.</span>
                </div>
                <textarea name="observaciones_campo" id="observaciones_campo" rows="4" required 
                          oninput="updateCount('observaciones_campo', 'counter_observaciones', 10)"
                          placeholder="Escriba los hallazgos verificados durante la visita técnica (Mínimo 10 caracteres)..."
                          class="w-full rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 text-sm @error('observaciones_campo') border-red-500 @enderror">{{ old('observaciones_campo') }}</textarea>
                @error('observaciones_campo')
                    <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span>
                @enderror
            </div>

            <!-- Recomendaciones -->
            <div>
                <div class="flex items-center justify-between mb-1">
                    <label class="block text-sm font-medium text-slate-700">Recomendaciones o Medidas Correctivas</label>
                    <span id="counter_recomendaciones" class="text-xs font-semibold text-amber-600">0 / 10 caracteres mín.</span>
                </div>
                <textarea name="recomendaciones" id="recomendaciones" rows="3" 
                          oninput="updateCount('recomendaciones', 'counter_recomendaciones', 10)"
                          placeholder="Instrucciones o medidas que debe cumplir el establecimiento o ciudadano (Mínimo 10 caracteres)..."
                          class="w-full rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 text-sm @error('recomendaciones') border-red-500 @enderror">{{ old('recomendaciones') }}</textarea>
                @error('recomendaciones')
                    <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span>
                @enderror
            </div>

            <!-- Carga de Evidencias -->
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Evidencias Fotográficas de la Visita</label>
                
                <div class="relative border-2 border-dashed border-slate-300 hover:border-emerald-500 bg-slate-50 hover:bg-emerald-50/30 rounded-2xl p-6 transition group text-center cursor-pointer">
                    <input type="file" name="evidencias[]" id="evidencias_input" multiple accept="image/*"
                           class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" onchange="previewImages(event)">
                    
                    <div class="space-y-2 pointer-events-none">
                        <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center mx-auto shadow-sm border border-slate-200 group-hover:scale-110 transition">
                            <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div class="text-sm text-slate-600">
                            <span class="font-semibold text-emerald-600">Haz clic para subir</span> o arrastra tus fotos aquí
                        </div>
                        <p class="text-xs text-slate-400">Formatos permitidos: PNG, JPG, WEBP (Máx. 5MB por archivo)</p>
                    </div>
                </div>
                @error('evidencias')
                    <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span>
                @enderror
                @error('evidencias.*')
                    <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span>
                @enderror

                <!-- Previsualización de Imágenes -->
                <div id="preview_container" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4 mt-4 hidden"></div>
            </div>

            <!-- Botones de Acción -->
            <div class="flex justify-end gap-3 pt-4 border-t border-slate-100">
                <a href="{{ route('inspecciones.index') }}" class="px-4 py-2.5 rounded-xl text-sm font-medium text-slate-600 hover:bg-slate-100 transition">
                    Cancelar
                </a>
                <button type="submit" class="px-5 py-2.5 rounded-xl text-sm font-medium text-white bg-emerald-600 hover:bg-emerald-700 shadow-sm transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Guardar y Firmar Acta
                </button>
            </div>

        </form>
    </div>

    <!-- Scripts de Interacción -->
    <script>
        function updateCount(inputId, counterId, minLength) {
            const input = document.getElementById(inputId);
            const counter = document.getElementById(counterId);
            if (!input || !counter) return;

            const length = input.value.length;

            if (length < minLength) {
                counter.textContent = `${length} / ${minLength} caracteres mín.`;
                counter.className = 'text-xs font-semibold text-amber-600';
            } else {
                counter.textContent = `${length} caracteres (¡Mínimo cumplido!)`;
                counter.className = 'text-xs font-semibold text-emerald-600';
            }
        }

        function toggleCompensacionSection() {
            const dictamenSelect = document.getElementById('dictamen_select');
            const seccionCompensacion = document.getElementById('seccion_compensacion');
            
            if (!seccionCompensacion || !dictamenSelect) return;

            if (dictamenSelect.value === 'APROBADO') {
                seccionCompensacion.classList.remove('hidden');
                recalcularCompensacion();
            } else {
                seccionCompensacion.classList.add('hidden');
            }
        }

        function recalcularCompensacion() {
            const cantInput = document.getElementById('cant_talar');
            const checkProtegida = document.getElementById('check_protegida');
            const totalBadge = document.getElementById('total_compensar_badge');
            const ratioText = document.getElementById('ratio_text');

            if (!cantInput || !totalBadge) return;

            const cant = Math.max(1, parseInt(cantInput.value) || 1);
            const esProtegida = checkProtegida ? checkProtegida.checked : false;
            const ratio = esProtegida ? 16 : 8;

            totalBadge.textContent = cant * ratio;
            
            if (ratioText) {
                ratioText.textContent = esProtegida ? '1:16 (Protegida/Vedada)' : '1:8 (Estándar)';
            }
        }

        function previewImages(event) {
            const container = document.getElementById('preview_container');
            container.innerHTML = '';
            const files = event.target.files;

            if (files.length > 0) {
                container.classList.remove('hidden');
                
                Array.from(files).forEach((file) => {
                    if (file.type.startsWith('image/')) {
                        const reader = new FileReader();
                        
                        reader.onload = function(e) {
                            const wrapper = document.createElement('div');
                            wrapper.className = 'relative group aspect-square rounded-xl overflow-hidden border border-slate-200 bg-slate-100 shadow-sm';
                            
                            wrapper.innerHTML = `
                                <img src="${e.target.result}" class="w-full h-full object-cover group-hover:scale-105 transition duration-200">
                                <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition flex items-center justify-center p-2 text-white text-xs truncate">
                                    <span class="truncate px-2">${file.name}</span>
                                </div>
                            `;
                            
                            container.appendChild(wrapper);
                        };
                        
                        reader.readAsDataURL(file);
                    }
                });
            } else {
                container.classList.add('hidden');
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            updateCount('observaciones_campo', 'counter_observaciones', 10);
            updateCount('recomendaciones', 'counter_recomendaciones', 10);
            toggleCompensacionSection();
        });
    </script>
</x-layouts.app>