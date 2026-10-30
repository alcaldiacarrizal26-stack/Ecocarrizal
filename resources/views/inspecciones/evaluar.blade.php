<x-layouts.app title="Evaluar Inspección Ambiental" active="inspecciones">
    <div class="max-w-4xl mx-auto space-y-6">
        
        <!-- Encabezado -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-xl font-bold text-slate-800">Acta de Evaluación de Campo</h1>
                <p class="text-xs text-slate-500 mt-0.5">Registro técnico de verificación, hallazgos y emisión de veredicto.</p>
            </div>
            <a href="{{ route('inspecciones.index') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-white hover:bg-slate-50 border border-slate-200 text-slate-700 text-xs font-semibold rounded-xl shadow-xs transition">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                <span>Volver al listado</span>
            </a>
        </div>

        <!-- Alerta global de errores de validación -->
        @if ($errors->any())
            <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold space-y-1 shadow-xs">
                <p class="font-bold">Por favor revisa los siguientes errores antes de guardar:</p>
                <ul class="list-disc list-inside space-y-0.5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @php
            // Determinamos si el expediente actual es una Orden de Empresa ('orden') o un trámite tradicional
            $esOrdenEmpresa = isset($tipoOrigen) && $tipoOrigen === 'orden';
            $evaluador = auth()->user();
            $nombreEvaluador = $evaluador->nombre ?? $evaluador->name ?? 'Funcionario';
            $apellidoEvaluador = $evaluador->apellido ?? '';
            $cargoEvaluador = $evaluador->role->nombre ?? 'Inspector Ambiental';

            if ($esOrdenEmpresa) {
                $empresa = $expediente->empresa ?? null;
                $solicitante = $empresa->user ?? null;
                $sector = $empresa->sector ?? 'N/E';
                $comuna = $empresa->comuna ?? 'N/E';
                $esTala = false;
            } else {
                $solicitante = $expediente->solicitante ?? $expediente->user ?? $expediente->usuario ?? null;
                $sector = $expediente->sector ?? $expediente->ubicacion_sector ?? 'N/E';
                $comuna = $expediente->comuna ?? $expediente->ubicacion_comuna ?? 'N/E';
                $esTala = ($tipoOrigen === 'solicitud' && ($expediente->tipo_tramite ?? '') === 'tala');
            }

            $estadoActual = $expediente->estado ?? $expediente->estatus ?? '';
        @endphp

        <!-- Aviso de Reinspección -->
        @if($estadoActual === 'requiere_reinspeccion')
            <div class="p-4 bg-purple-50 border border-purple-200 text-purple-900 rounded-2xl text-xs flex items-center gap-3 shadow-xs">
                <div class="p-2 bg-purple-100 text-purple-700 rounded-xl shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                </div>
                <div>
                    <span class="font-bold block text-sm">Aviso de Reinspección Activa</span>
                    <p class="text-purple-700 mt-0.5">Este expediente se encuentra en proceso de reinspección tras observaciones previas. Asegúrate de verificar el cumplimiento de las medidas correctivas solicitadas.</p>
                </div>
            </div>
        @endif

        <form action="{{ route('inspecciones.store') }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-3xl border border-slate-200/80 p-6 sm:p-8 shadow-xs space-y-6">
            @csrf

            <!-- Campos ocultos requeridos para procesar el origen -->
            <input type="hidden" name="tipo_origen" value="{{ $tipoOrigen }}">
            <input type="hidden" name="origen_id" value="{{ $expediente->id }}">

            <!-- Metadata del Expediente, Solicitante e Inspector -->
            <div class="bg-slate-50/80 p-5 rounded-2xl border border-slate-100 grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                <div>
                    <span class="text-slate-400 font-bold uppercase tracking-wider block mb-0.5">Origen:</span>
                    <strong class="text-slate-800 text-sm">
                        @if($esOrdenEmpresa)
                            Fiscalización de Residuos ({{ $expediente->tipo_inspeccion ?? 'ORDINARIA' }})
                        @else
                            {{ $tipoOrigen === 'solicitud' ? 'Trámite: ' . strtoupper($expediente->tipo_tramite ?? 'Solicitud') : 'Denuncia Ambiental' }}
                        @endif
                    </strong>
                </div>

                <div>
                    <span class="text-slate-400 font-bold uppercase tracking-wider block mb-0.5">Código Referencia / Orden:</span>
                    <strong class="text-slate-800 text-sm font-mono text-indigo-600">{{ $expediente->codigo_orden ?? $expediente->codigo_expediente ?? $expediente->codigo }}</strong>
                </div>

                <!-- Datos del Solicitante / Representante de la Empresa -->
                <div>
                    <span class="text-slate-400 font-bold uppercase tracking-wider block mb-0.5">{{ $esOrdenEmpresa ? 'Razón Social / Empresa:' : 'Solicitante / Denunciante:' }}</span>
                    <strong class="text-slate-800 text-sm">
                        @if($esOrdenEmpresa)
                            {{ $empresa->razon_social ?? 'Empresa no vinculada' }}
                        @elseif($solicitante && !empty($solicitante->nombre ?? $solicitante->name))
                            {{ trim(($solicitante->nombre ?? $solicitante->name) . ' ' . ($solicitante->apellido ?? '')) }}
                        @else
                            {{ $expediente->nombre_completo ?? 'No registrado' }}
                        @endif
                    </strong>
                    <span class="block text-[11px] text-slate-500 mt-0.5">
                        {{ $esOrdenEmpresa ? 'RIF: ' . ($empresa->rif ?? 'N/A') : 'C.I. / RIF: ' . ($solicitante->cedula ?? $expediente->cedula ?? 'N/A') }}
                    </span>
                </div>

                <!-- Funcionario Evaluador -->
                <div>
                    <span class="text-slate-400 font-bold uppercase tracking-wider block mb-0.5">Funcionario Evaluador:</span>
                    <strong class="text-slate-800 text-sm">
                        {{ trim($nombreEvaluador . ' ' . $apellidoEvaluador) }}
                    </strong>
                    <span class="block text-[11px] text-slate-500 mt-0.5">
                        C.I.: {{ $evaluador->cedula ?? 'N/A' }} | Cargo: {{ ucwords($cargoEvaluador) }}
                    </span>
                </div>

                <div>
                    <span class="text-slate-400 font-bold uppercase tracking-wider block mb-0.5">Ubicación:</span>
                    <strong class="text-slate-800 text-sm">
                        @if($esOrdenEmpresa)
                            {{ $empresa->calle_avenida ?? '' }}, {{ $sector }}, {{ $comuna }}
                        @else
                            {{ $sector }}, {{ $comuna }}
                        @endif
                    </strong>
                </div>

                <div>
                    <span class="text-slate-400 font-bold uppercase tracking-wider block mb-0.5">Detalles / Base Legal / Motivo:</span>
                    <strong class="text-slate-800 text-sm font-normal">
                        @if($esOrdenEmpresa)
                            {{ $expediente->base_legal_inspeccion }} - {{ Str::limit($expediente->observaciones_campo, 40) }}
                        @else
                            {{ Str::limit($expediente->observaciones_campo ?? $expediente->motivo_descripcion ?? $expediente->descripcion, 60) }}
                        @endif
                    </strong>
                </div>
            </div>

            <!-- Campos del Formulario de Inspección -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">Fecha y Hora de la Visita <span class="text-rose-500">*</span></label>
                    <input type="datetime-local" name="fecha_visita" value="{{ old('fecha_visita', now()->format('Y-m-d\TH:i')) }}" required
                           class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:bg-white focus:border-indigo-600 transition @error('fecha_visita') border-rose-500 @enderror">
                    @error('fecha_visita')
                        <span class="text-[11px] text-rose-500 mt-1 block font-semibold">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">Dictamen de la Inspección <span class="text-rose-500">*</span></label>
                    <select name="dictamen" id="dictamen_select" required onchange="toggleCompensacionSection()" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:bg-white focus:border-indigo-600 transition @error('dictamen') border-rose-500 @enderror">
                        <option value="APROBADO" {{ old('dictamen') === 'APROBADO' ? 'selected' : '' }}>APROBADO (Conforme)</option>
                        <option value="CON_OBSERVACIONES" {{ old('dictamen', 'CON_OBSERVACIONES') === 'CON_OBSERVACIONES' ? 'selected' : '' }}>CON OBSERVACIONES (Requiere adecuación)</option>
                        <option value="REINSPECCION_REQUERIDA" {{ old('dictamen') === 'REINSPECCION_REQUERIDA' ? 'selected' : '' }}>REINSPECCIÓN REQUERIDA</option>
                        <option value="RECHAZADO" {{ old('dictamen') === 'RECHAZADO' ? 'selected' : '' }}>RECHAZADO (No conforme / Improcedente)</option>
                    </select>
                    @error('dictamen')
                        <span class="text-[11px] text-rose-500 mt-1 block font-semibold">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <!-- Módulo Condicional de Compensación Ambiental por Tala -->
            @if($esTala)
                <div id="seccion_compensacion" class="p-5 rounded-2xl bg-emerald-50/60 border border-emerald-200 space-y-4 transition-all duration-300 hidden">
                    <div class="flex items-center gap-2 text-emerald-900 font-bold text-xs uppercase tracking-wider border-b border-emerald-200/80 pb-2">
                        <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                        <span>Cálculo de Compensación Ambiental (Tala Autorizada - Relación 1x8)</span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Árboles Autorizados a Talar</label>
                            <input type="number" name="cantidad_arboles_talar" id="cant_talar" min="1" value="{{ old('cantidad_arboles_talar', 1) }}"
                                   oninput="recalcularCompensacion()"
                                   class="w-full px-4 py-2.5 bg-white border border-emerald-200 rounded-xl text-xs text-slate-800 focus:border-indigo-600 transition">
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Especie a Solicitar (Entrega)</label>
                            <select name="especie_compensacion" class="w-full px-4 py-2.5 bg-white border border-emerald-200 rounded-xl text-xs text-slate-800 focus:border-indigo-600 transition">
                                <option value="Araguaney" {{ old('especie_compensacion') === 'Araguaney' ? 'selected' : '' }}>Araguaney (Árbol Nacional)</option>
                                <option value="Apamate" {{ old('especie_compensacion') === 'Apamate' ? 'selected' : '' }}>Apamate</option>
                                <option value="Bucare" {{ old('especie_compensacion') === 'Bucare' ? 'selected' : '' }}>Bucare</option>
                                <option value="Cuji Yaque" {{ old('especie_compensacion') === 'Cuji Yaque' ? 'selected' : '' }}>Cují Yaque</option>
                                <option value="Caoba" {{ old('especie_compensacion') === 'Caoba' ? 'selected' : '' }}>Caoba</option>
                            </select>
                        </div>
                    </div>

                    <div class="bg-white p-3.5 rounded-xl border border-emerald-200/80 flex items-center justify-between text-xs font-medium">
                        <span class="text-slate-600">Relación de Compensación Aplicada: <strong id="ratio_text" class="text-slate-800 font-bold">1:8 (Estándar Único)</strong></span>
                        <div class="text-emerald-900 font-bold">
                            Total a Compensar: <span id="total_compensar_badge" class="text-sm text-emerald-600 font-extrabold">8</span> ejemplares
                        </div>
                    </div>
                </div>
            @endif

            <!-- Observaciones Técnicas -->
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600">Observaciones de Campo / Diagnóstico Técnico <span class="text-rose-500">*</span></label>
                    <span id="counter_observaciones" class="text-[11px] font-bold text-amber-600">0 / 10 caracteres mín.</span>
                </div>
                <textarea name="observaciones_campo" id="observaciones_campo" rows="4" required 
                          oninput="updateCount('observaciones_campo', 'counter_observaciones', 10)"
                          placeholder="Escriba los hallazgos verificados durante la visita técnica (Mínimo 10 caracteres)..."
                          class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:bg-white focus:border-indigo-600 transition resize-none @error('observaciones_campo') border-rose-500 @enderror">{{ old('observaciones_campo') }}</textarea>
                @error('observaciones_campo')
                    <span class="text-[11px] text-rose-500 mt-1 block font-semibold">{{ $message }}</span>
                @enderror
            </div>

            <!-- Recomendaciones -->
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600">Recomendaciones o Medidas Correctivas</label>
                    <span id="counter_recomendaciones" class="text-[11px] font-bold text-amber-600">0 / 10 caracteres mín.</span>
                </div>
                <textarea name="recomendaciones" id="recomendaciones" rows="3" 
                          oninput="updateCount('recomendaciones', 'counter_recomendaciones', 10)"
                          placeholder="Instrucciones o medidas que debe cumplir el establecimiento o ciudadano (Mínimo 10 caracteres)..."
                          class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:bg-white focus:border-indigo-600 transition resize-none @error('recomendaciones') border-rose-500 @enderror">{{ old('recomendaciones') }}</textarea>
                @error('recomendaciones')
                    <span class="text-[11px] text-rose-500 mt-1 block font-semibold">{{ $message }}</span>
                @enderror
            </div>

            <!-- Carga de Evidencias -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">Evidencias Fotográficas de la Visita</label>
                
                <div class="relative border-2 border-dashed border-slate-200 hover:border-indigo-500 bg-slate-50/50 rounded-2xl p-6 transition group text-center cursor-pointer">
                    <input type="file" name="evidencias[]" id="evidencias_input" multiple accept="image/*"
                           class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" onchange="previewImages(event)">
                    
                    <div class="space-y-2 pointer-events-none">
                        <div class="w-10 h-10 bg-white rounded-2xl flex items-center justify-center mx-auto shadow-xs border border-slate-200 group-hover:scale-105 transition">
                            <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div class="text-xs text-slate-600 font-semibold">
                            <span class="text-indigo-600">Haz clic para buscar archivos</span> o arrástralos aquí
                        </div>
                        <p class="text-[10px] text-slate-400">PNG, JPG, WEBP (Máx. 5MB por archivo)</p>
                    </div>
                </div>
                @error('evidencias')
                    <span class="text-[11px] text-rose-500 mt-1 block font-semibold">{{ $message }}</span>
                @enderror
                @error('evidencias.*')
                    <span class="text-[11px] text-rose-500 mt-1 block font-semibold">{{ $message }}</span>
                @enderror

                <!-- Previsualización de Imágenes -->
                <div id="preview_container" class="grid grid-cols-2 sm:grid-cols-4 gap-3 mt-3 hidden"></div>
            </div>

            <!-- Botones de Acción -->
            <div class="flex items-center justify-end gap-2 pt-4 border-t border-slate-100">
                <a href="{{ route('inspecciones.index') }}" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-xl transition cursor-pointer">
                    Cancelar
                </a>
                <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow-xs transition cursor-pointer border-0">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span>Guardar y Firmar Acta</span>
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
                counter.className = 'text-[11px] font-bold text-amber-600';
            } else {
                counter.textContent = `${length} caracteres (¡Mínimo cumplido!)`;
                counter.className = 'text-[11px] font-bold text-emerald-600';
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
            const totalBadge = document.getElementById('total_compensar_badge');
            const ratioText = document.getElementById('ratio_text');

            if (!cantInput || !totalBadge) return;

            const cant = Math.max(1, parseInt(cantInput.value) || 1);
            const ratio = 8; // Ratio fijo universal 1x8

            totalBadge.textContent = cant * ratio;
            
            if (ratioText) {
                ratioText.textContent = '1:8 (Estándar Único)';
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
                            wrapper.className = 'relative group aspect-square rounded-2xl overflow-hidden border border-slate-200 bg-slate-100 shadow-xs';
                            
                            wrapper.innerHTML = `
                                <img src="${e.target.result}" class="w-full h-full object-cover group-hover:scale-105 transition duration-200">
                                <div class="absolute inset-0 bg-slate-950/40 opacity-0 group-hover:opacity-100 transition flex items-center justify-center p-2 text-white text-[10px] truncate">
                                    <span class="truncate px-2 font-medium">${file.name}</span>
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