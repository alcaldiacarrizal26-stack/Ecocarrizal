<x-layouts.app title="Evaluar Inspección Ambiental" active="inspecciones">
    <div class="p-6 max-w-4xl mx-auto space-y-6">
        
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-slate-800">Acta de Evaluación de Campo</h1>
            <a href="{{ route('inspecciones.index') }}" class="text-sm font-medium text-slate-500 hover:text-slate-800 transition">
                ← Volver al listado
            </a>
        </div>

        <!-- Alerta global si ocurre un error de validación al enviar -->
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

        <form action="{{ route('inspecciones.store') }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm space-y-6">
            @csrf

            <!-- Campos ocultos requeridos para procesar el origen -->
            <input type="hidden" name="tipo_origen" value="{{ $tipoOrigen }}">
            <input type="hidden" name="origen_id" value="{{ $expediente->id }}">

            @php
                // 1. Obtención flexible del solicitante/denunciante
                $solicitante =$expediente->solicitante ?? $expediente->user ?? $expediente->usuario ?? null;

                // 2. Obtención del evaluador y su rol/cargo
                $evaluador = auth()->user();$nombreEvaluador = $evaluador->nombre ?? $evaluador->name ?? 'Funcionario';
                $apellidoEvaluador =$evaluador->apellido ?? '';
                $cargoEvaluador =$evaluador->role->nombre 
                    ?? $evaluador->cargo 
                    ?? ($evaluador->inspector ? 'Inspector Ambiental' : 'Funcionario Ambiental');

                // 3. Ubicación flexible (Soporta Solicitud y Denuncia)
                $sector = $expediente->sector ?? $expediente->ubicacion_sector ?? 'N/E';
                $comuna = $expediente->comuna ?? $expediente->ubicacion_comuna ?? 'N/E';
            @endphp

            <!-- Metadata del Expediente, Solicitante e Inspector -->
            <div class="bg-slate-50 p-4 rounded-xl border border-slate-100 grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                <div>
                    <span class="text-slate-500 block text-xs uppercase tracking-wider font-semibold">Origen:</span>
                    <strong class="text-slate-800">{{ $tipoOrigen === 'solicitud' ? 'Trámite / Solicitud' : 'Denuncia Ambiental' }}</strong>
                </div>

                <div>
                    <span class="text-slate-500 block text-xs uppercase tracking-wider font-semibold">Código Referencia:</span>
                    <strong class="text-slate-800">{{ $expediente->codigo_expediente ?? $expediente->codigo }}</strong>
                </div>

                <!-- Datos del Solicitante / Denunciante -->
                <div>
                    <span class="text-slate-500 block text-xs uppercase tracking-wider font-semibold">Solicitante / Denunciante:</span>
                    <strong class="text-slate-800">
                        @if($solicitante && !empty($solicitante->nombre ?? $solicitante->name))
                            {{ trim(($solicitante->nombre ?? $solicitante->name) . ' ' . ($solicitante->apellido ?? '')) }}
                        @else
                            {{ $expediente->nombre_completo ?? 'No registrado' }}
                        @endif
                    </strong>
                    <span class="block text-xs text-slate-500">
                        C.I. / RIF: {{ $solicitante->cedula ?? $expediente->cedula ?? 'N/A' }}
                    </span>
                </div>

                <!-- Funcionario Evaluador Corrección \vert{} -->
                <div>
                    <span class="text-slate-500 block text-xs uppercase tracking-wider font-semibold">Funcionario Evaluador:</span>
                    <strong class="text-slate-800">
                        {{ trim($nombreEvaluador . ' ' .$apellidoEvaluador) }}
                    </strong>
                    <span class="block text-xs text-slate-500">
                        C.I.: {{ $evaluador->cedula ?? 'N/A' }} \vert{} Cargo: {{ ucwords($cargoEvaluador) }}
                    </span>
                </div>

                <div>
                    <span class="text-slate-500 block text-xs uppercase tracking-wider font-semibold">Ubicación:</span>
                    <strong class="text-slate-800">{{ $sector }}, {{$comuna }}</strong>
                </div>

                <div>
                    <span class="text-slate-500 block text-xs uppercase tracking-wider font-semibold">Detalles / Motivo:</span>
                    <strong class="text-slate-800">{{ Str::limit($expediente->motivo_descripcion ?? $expediente->descripcion, 60) }}</strong>
                </div>
            </div>

            <!-- Campos del Formulario de Inspección -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Fecha y Hora de Cierre / Evaluación</label>
                    <input type="datetime-local" name="fecha_cierre" value="{{ old('fecha_cierre', now()->format('Y-m-d\TH:i')) }}" required
                           class="w-full rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 text-sm @error('fecha_cierre') border-red-500 @enderror">
                    @error('fecha_cierre')
                        <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Dictamen de la Inspección</label>
                    <select name="dictamen" required class="w-full rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 text-sm @error('dictamen') border-red-500 @enderror">
                        <option value="APROBADO" {{ old('dictamen') === 'APROBADO' ? 'selected' : '' }}>APROBADO (Conforme)</option>
                        <option value="CON_OBSERVACIONES" {{ old('dictamen') === 'CON_OBSERVACIONES' ? 'selected' : '' }}>CON OBSERVACIONES (Requiere adecuación)</option>
                        <option value="REINSPECCION_REQUERIDA" {{ old('dictamen') === 'REINSPECCION_REQUERIDA' ? 'selected' : '' }}>REINSPECCIÓN REQUERIDA</option>
                        <option value="RECHAZADO" {{ old('dictamen') === 'RECHAZADO' ? 'selected' : '' }}>RECHAZADO (No conforme / Improcedente)</option>
                    </select>
                    @error('dictamen')
                        <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <!-- Observaciones Técnicas (Mínimo 10 caracteres) -->
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

            <!-- Recomendaciones (Mínimo 10 caracteres) -->
            <div>
                <div class="flex items-center justify-between mb-1">
                    <label class="block text-sm font-medium text-slate-700">Recomendaciones o Medidas Correctivas</label>
                    <span id="counter_recomendaciones" class="text-xs font-semibold text-amber-600">0 / 10 caracteres mín.</span>
                </div>
                <textarea name="recomendaciones" id="recomendaciones" rows="3" 
                          oninput="updateCount('recomendaciones', 'counter_recomendaciones', 10)"
                          placeholder="Instrucciones o medidas que debe cumplir el solicitante/denunciado (Mínimo 10 caracteres)..."
                          class="w-full rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 text-sm @error('recomendaciones') border-red-500 @enderror">{{ old('recomendaciones') }}</textarea>
                @error('recomendaciones')
                    <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span>
                @enderror
            </div>

            <!-- Carga de Fotografía / Evidencias de Campo -->
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

    <!-- Scripts de Previsualización y Contadores de Caracteres -->
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
        });
    </script>
</x-layouts.app>