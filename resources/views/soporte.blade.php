<x-layouts.app title="Soporte Técnico" active="soporte">
    <div class="p-6 max-w-7xl mx-auto space-y-6" x-data="{ 
        prioridad: 'MEDIA',
        chatAbierto: false,
        ticketSeleccionado: null,
        abrirChat(ticket) {
            this.ticketSeleccionado = ticket;
            this.chatAbierto = true;
        }
    }">
        
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

        @if(session('error'))
            <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-xl text-sm font-semibold">
                {{ session('error') }}
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

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- COLUMNA IZQUIERDA: Formulario de Nuevo Ticket -->
            <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm lg:col-span-1 h-fit">
                <h2 class="text-base font-bold text-slate-800 mb-4">Nuevo Reporte de Fallo</h2>
                
                <form action="{{ route('soporte.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4 text-xs">
                    @csrf
                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1">Asunto del Problema *</label>
                        <input type="text" name="asunto" value="{{ old('asunto') }}" required placeholder="Ej. Error al guardar inspección..." class="w-full bg-slate-50 border border-slate-300 rounded-xl p-2.5 focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1">Prioridad *</label>
                        <select name="prioridad" x-model="prioridad" required class="w-full bg-slate-50 border border-slate-300 rounded-xl p-2.5 focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                            <option value="BAJA">Baja</option>
                            <option value="MEDIA">Media</option>
                            <option value="ALTA">Alta (Bloqueante / Crítico)</option>
                        </select>
                    </div>

                    <!-- Justificación obligatoria condicional si es ALTA -->
                    <div x-show="prioridad === 'ALTA'" x-cloak class="p-3 bg-rose-50 border border-rose-200 rounded-xl space-y-1">
                        <label class="block font-bold text-rose-800 uppercase">Justificación de Urgencia Alta *</label>
                        <p class="text-[10px] text-rose-600">Explica por qué este fallo impide continuar con las operaciones:</p>
                        <textarea name="justificacion_alta" rows="2" placeholder="Describe el bloqueo operativo..." class="w-full bg-white border border-rose-300 rounded-lg p-2 text-xs focus:ring-2 focus:ring-rose-500 focus:outline-none">{{ old('justificacion_alta') }}</textarea>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1">Descripción Detallada *</label>
                        <textarea name="mensaje" rows="4" required placeholder="Explica detalladamente el error..." class="w-full bg-slate-50 border border-slate-300 rounded-xl p-2.5 focus:ring-2 focus:ring-emerald-500 focus:outline-none">{{ old('mensaje') }}</textarea>
                    </div>

                    <!-- Evidencia (Captura) -->
                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1">Captura de Pantalla / Evidencia</label>
                        <div class="relative border-2 border-dashed border-slate-300 hover:border-emerald-500 bg-slate-50 hover:bg-emerald-50/30 rounded-xl p-4 transition group text-center cursor-pointer">
                            <input type="file" name="evidencia" id="evidencia_input" accept="image/*,.pdf" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" onchange="previewSoporteImage(event)">
                            <div class="space-y-1.5 pointer-events-none">
                                <div class="w-8 h-8 bg-white rounded-full flex items-center justify-center mx-auto shadow-sm border border-slate-200 group-hover:scale-110 transition">
                                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                </div>
                                <div class="text-xs text-slate-600">
                                    <span class="font-semibold text-emerald-600">Haz clic para subir</span> o arrastra tu archivo
                                </div>
                                <p class="text-[10px] text-slate-400">PNG, JPG, WEBP o PDF (Máx. 5MB)</p>
                            </div>
                        </div>
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

            <!-- COLUMNA DERECHA: Mis Tickets (Tarjetas) -->
            <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm lg:col-span-2 space-y-4">
                <h2 class="text-base font-bold text-slate-800">Mis Conversaciones de Soporte</h2>

                <div class="grid grid-cols-1 gap-3">
                    @forelse($tickets as $ticket)
                        @php
                            $ultimoMensaje = $ticket->mensajes->sortByDesc('created_at')->first();
                        @endphp
                        <div @click="abrirChat({{ json_encode($ticket) }})" class="border border-slate-200 bg-slate-50 hover:bg-slate-100 p-4 rounded-xl cursor-pointer transition-colors flex justify-between items-center group">
                            <div class="space-y-1 pr-4">
                                <div class="flex items-center gap-2">
                                    <span class="font-bold text-slate-900 text-sm group-hover:text-emerald-700 transition">{{ $ticket->asunto }}</span>
                                    <span class="px-2 py-0.5 rounded font-bold text-[10px] uppercase 
                                        {{ $ticket->prioridad === 'ALTA' ? 'bg-rose-100 text-rose-800' : 'bg-slate-200 text-slate-700' }}">
                                        {{ $ticket->prioridad }}
                                    </span>
                                </div>
                                <p class="text-xs text-slate-500 line-clamp-1">
                                    {{ $ultimoMensaje ? $ultimoMensaje->mensaje : 'Sin mensajes...' }}
                                </p>
                                <p class="text-[10px] text-slate-400">Última actualización: {{ $ticket->updated_at->diffForHumans() }}</p>
                            </div>
                            <div class="text-right shrink-0">
                                <span class="px-2.5 py-1 rounded-full font-bold text-[10px] uppercase
                                    {{ $ticket->estatus === 'RESUELTO' ? 'bg-emerald-100 text-emerald-800' : ($ticket->estatus === 'EN_PROCESO' ? 'bg-blue-100 text-blue-800' : 'bg-amber-100 text-amber-800') }}">
                                    {{ str_replace('_', ' ', $ticket->estatus) }}
                                </span>
                                <div class="mt-2 text-emerald-600 font-semibold text-xs group-hover:underline">
                                    Abrir Chat &rarr;
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-12">
                            <svg class="w-12 h-12 text-slate-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                            <p class="text-slate-400 text-sm">No has enviado ningún reporte de soporte técnico.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- MODAL DE CHAT -->
        <div x-show="chatAbierto" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/60 backdrop-blur-xs">
            <div @click.outside="chatAbierto = false" class="bg-white w-full max-w-2xl rounded-3xl shadow-2xl flex flex-col h-[80vh] overflow-hidden">
                
                <!-- Encabezado del Chat -->
                <div class="p-4 border-b border-slate-100 flex justify-between items-center bg-slate-50 shrink-0">
                    <div>
                        <h3 class="text-sm font-bold text-slate-800" x-text="ticketSeleccionado?.asunto"></h3>
                        <div class="flex items-center gap-2 mt-1">
                            <span class="text-[10px] text-slate-500" x-text="'Prioridad: ' + ticketSeleccionado?.prioridad"></span>
                            <span class="px-2 py-0.5 rounded-full font-bold text-[9px] uppercase border"
                                :class="{
                                    'bg-emerald-50 text-emerald-700 border-emerald-200': ticketSeleccionado?.estatus === 'RESUELTO',
                                    'bg-blue-50 text-blue-700 border-blue-200': ticketSeleccionado?.estatus === 'EN_PROCESO',
                                    'bg-amber-50 text-amber-700 border-amber-200': ticketSeleccionado?.estatus === 'ABIERTO'
                                }" 
                                x-text="ticketSeleccionado?.estatus ? ticketSeleccionado.estatus.replace('_', ' ') : ''">
                            </span>
                        </div>
                    </div>
                    <button @click="chatAbierto = false" type="button" class="text-slate-400 hover:text-slate-700 text-2xl font-bold cursor-pointer leading-none">&times;</button>
                </div>

                <!-- Historial de Mensajes (Scroll) -->
                <div class="flex-1 overflow-y-auto p-4 space-y-4 bg-slate-50/50">
                    <template x-if="ticketSeleccionado?.justificacion_alta">
                        <div class="p-3 bg-rose-50 border border-rose-200 rounded-xl text-xs text-rose-800 mx-auto text-center w-full max-w-md">
                            <strong>Justificación de urgencia:</strong> <span x-text="ticketSeleccionado.justificacion_alta"></span>
                        </div>
                    </template>

                    <template x-for="msg in ticketSeleccionado?.mensajes" :key="msg.id">
                        <div class="flex flex-col" :class="msg.es_admin ? 'items-start' : 'items-end'">
                            <!-- Burbuja del Mensaje -->
                            <div class="max-w-[80%] rounded-2xl p-3 text-xs shadow-xs"
                                :class="msg.es_admin ? 'bg-white border border-slate-200 text-slate-800 rounded-tl-sm' : 'bg-emerald-600 text-white rounded-tr-sm'">
                                <p class="whitespace-pre-line" x-text="msg.mensaje"></p>
                                
                                <template x-if="msg.evidencia_path">
                                    <a :href="'/storage/' + msg.evidencia_path" target="_blank" class="mt-2 block text-[10px] underline font-bold" :class="msg.es_admin ? 'text-indigo-600' : 'text-emerald-100'">
                                        📎 Ver Archivo Adjunto
                                    </a>
                                </template>
                            </div>
                            
                            <!-- Fecha y Remitente -->
                            <span class="text-[9px] text-slate-400 mt-1 px-1" 
                                x-text="(msg.es_admin ? 'Soporte Técnico' : 'Tú') + ' • ' + new Date(msg.created_at).toLocaleString()">
                            </span>
                        </div>
                    </template>
                </div>

                <!-- Formulario de Respuesta -->
                <div class="p-4 bg-white border-t border-slate-100 shrink-0">
                    <template x-if="ticketSeleccionado?.estatus !== 'RESUELTO'">
                        <!-- CORREGIDO: La ruta usa url() para no depender del nombre de la ruta -->
                        <form x-bind:action="'{{ url('/soporte') }}/' + ticketSeleccionado?.id + '/responder'" method="POST" class="flex gap-2">
                            @csrf
                            <textarea name="mensaje" rows="1" required placeholder="Escribe un mensaje..." class="flex-1 bg-slate-100 border-0 rounded-xl px-4 py-3 text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none resize-none" oninput="this.style.height = '';this.style.height = this.scrollHeight + 'px'"></textarea>
                            <button type="submit" class="px-5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-sm transition cursor-pointer flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                            </button>
                        </form>
                    </template>
                    <template x-if="ticketSeleccionado?.estatus === 'RESUELTO'">
                        <div class="text-center p-2 bg-emerald-50 rounded-xl text-emerald-800 text-xs font-bold">
                            Este ticket ha sido marcado como RESUELTO. Si el problema persiste, por favor crea un nuevo ticket.
                        </div>
                    </template>
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