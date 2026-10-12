<x-layouts.app title="Soporte Técnico" active="soporte">
    <div class="p-6 max-w-5xl mx-auto space-y-6" x-data="{ prioridad: 'MEDIA' }">
        
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Centro de Soporte Técnico</h1>
                <p class="text-slate-500 text-sm">Reporta fallos del sistema, inconsistencias o solicita asistencia técnica al Administrador.</p>
            </div>
        </div>

        @if(session('success'))
            <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-sm font-semibold">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs space-y-1">
                <strong class="font-bold block">Por favor corrige los siguientes errores:</strong>
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Formulario de Envío de Ticket -->
            <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm md:col-span-1">
                <h2 class="text-base font-bold text-slate-800 mb-4">Nuevo Reporte de Fallo</h2>
                
                <form action="{{ route('soporte.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4 text-xs">
                    @csrf
                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1">Asunto del Problema *</label>
                        <input type="text" name="asunto" value="{{ old('asunto') }}" required placeholder="Ej. Error al guardar inspección..." class="w-full bg-slate-50 border border-slate-300 rounded-xl p-2.5">
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1">Prioridad *</label>
                        <select name="prioridad" x-model="prioridad" required class="w-full bg-slate-50 border border-slate-300 rounded-xl p-2.5">
                            <option value="BAJA">Baja</option>
                            <option value="MEDIA">Media</option>
                            <option value="ALTA">Alta (Bloqueante / Crítico)</option>
                        </select>
                    </div>

                    <!-- Justificación obligatoria condicional si es ALTA -->
                    <div x-show="prioridad === 'ALTA'" x-cloak class="p-3 bg-rose-50 border border-rose-200 rounded-xl space-y-1">
                        <label class="block font-bold text-rose-800 uppercase">Justificación de Urgencia Alta *</label>
                        <p class="text-[10px] text-rose-600">Explica por qué este fallo impide continuar con las operaciones del sistema:</p>
                        <textarea name="justificacion_alta" rows="2" placeholder="Describe el bloqueo operativo..." class="w-full bg-white border border-rose-300 rounded-lg p-2 text-xs">{{ old('justificacion_alta') }}</textarea>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1">Descripción Detallada *</label>
                        <textarea name="mensaje" rows="3" required placeholder="Explica detalladamente el error..." class="w-full bg-slate-50 border border-slate-300 rounded-xl p-2.5">{{ old('mensaje') }}</textarea>
                    </div>

                    <!-- Cuadro para anexar evidencia (Captura de pantalla) con Previsualización -->
                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1">Captura de Pantalla / Evidencia</label>
                        
                        <div class="relative border-2 border-dashed border-slate-300 hover:border-emerald-500 bg-slate-50 hover:bg-emerald-50/30 rounded-xl p-4 transition group text-center cursor-pointer">
                            <input type="file" name="evidencia" id="evidencia_input" accept="image/*,.pdf"
                                   class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" onchange="previewSoporteImage(event)">
                            
                            <div class="space-y-1.5 pointer-events-none">
                                <div class="w-8 h-8 bg-white rounded-full flex items-center justify-center mx-auto shadow-sm border border-slate-200 group-hover:scale-110 transition">
                                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <div class="text-xs text-slate-600">
                                    <span class="font-semibold text-emerald-600">Haz clic para subir</span> o arrastra tu archivo
                                </div>
                                <p class="text-[10px] text-slate-400">PNG, JPG, WEBP o PDF (Máx. 5MB)</p>
                            </div>
                        </div>

                        <!-- Previsualización de la Imagen / Archivo seleccionado -->
                        <div id="soporte_preview_container" class="mt-2 hidden">
                            <div class="flex items-center justify-between p-2 bg-emerald-50 border border-emerald-200 rounded-lg text-emerald-900 text-[11px]">
                                <span id="soporte_file_name" class="truncate font-semibold"></span>
                                <button type="button" onclick="limpiarArchivoSoporte()" class="text-rose-600 font-bold hover:text-rose-800 ml-2">&times; Quitar</button>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="w-full py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl transition shadow-sm cursor-pointer">
                        Enviar Reporte
                    </button>
                </form>
            </div>

            <!-- Historial de Tickets del Usuario -->
            <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm md:col-span-2 space-y-4">
                <h2 class="text-base font-bold text-slate-800">Mis Reportes Enviados</h2>

                <div class="space-y-3">
                    @forelse($tickets as $ticket)
                        <div class="border border-slate-100 bg-slate-50/50 p-4 rounded-xl space-y-2 text-xs">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <span class="font-bold text-slate-900 text-sm">{{ $ticket->asunto }}</span>
                                    <span class="px-2 py-0.5 rounded font-bold text-[10px] uppercase 
                                        {{ $ticket->prioridad === 'ALTA' ? 'bg-rose-100 text-rose-800 border border-rose-200' : 'bg-slate-200 text-slate-700' }}">
                                        {{ $ticket->prioridad }}
                                    </span>
                                </div>
                                <span class="px-2.5 py-0.5 rounded-full font-bold text-[10px] uppercase
                                    {{ $ticket->estatus === 'RESUELTO' ? 'bg-emerald-100 text-emerald-800' : ($ticket->estatus === 'EN_PROCESO' ? 'bg-blue-100 text-blue-800' : 'bg-amber-100 text-amber-800') }}">
                                    {{ str_replace('_', ' ', $ticket->estatus) }}
                                </span>
                            </div>
                            <p class="text-slate-600">{{ $ticket->mensaje }}</p>

                            @if($ticket->justificacion_alta)
                                <div class="p-2 bg-rose-50/60 border border-rose-200 rounded-lg text-rose-900 text-[11px]">
                                    <strong>Justificación Urgencia:</strong> {{ $ticket->justificacion_alta }}
                                </div>
                            @endif

                            @if($ticket->evidencia_path)
                                <div>
                                    <a href="{{ asset('storage/' . $ticket->evidencia_path) }}" target="_blank" class="text-emerald-600 font-bold hover:underline inline-flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        Ver Captura / Evidencia Adjunta
                                    </a>
                                </div>
                            @endif

                            @if($ticket->respuesta_admin)
                                <div class="mt-3 p-3 bg-emerald-50/80 border border-emerald-200 rounded-lg text-emerald-900 space-y-1">
                                    <strong class="block uppercase text-[10px] text-emerald-700">Respuesta del Administrador:</strong>
                                    <p>{{ $ticket->respuesta_admin }}</p>
                                </div>
                            @endif
                            <div class="text-[10px] text-slate-400 text-right">
                                Enviado el: {{ $ticket->created_at->format('d/m/Y h:i A') }}
                            </div>
                        </div>
                    @empty
                        <p class="text-slate-400 text-center py-8 text-xs">No has enviado ningún reporte de soporte técnico.</p>
                    @endforelse
                </div>
            </div>
        </div>

    </div>

<script>
    function previewSoporteImage(event) {
        const container = document.getElementById('soporte_preview_container');
        const fileNameSpan = document.getElementById('soporte_file_name');
        const file = event.target.files[0];

        if (file) {
            fileNameSpan.textContent = file.name;
            container.classList.remove('hidden');
        } else {
            limpiarArchivoSoporte();
        }
    }

    function limpiarArchivoSoporte() {
        const input = document.getElementById('evidencia_input');
        const container = document.getElementById('soporte_preview_container');
        input.value = '';
        container.classList.add('hidden');
    }
</script>
</x-layouts.app>