<x-layouts.app title="Administración de Soporte Técnico" active="soporte">
    <div class="p-6 max-w-7xl mx-auto space-y-6" 
         x-data="{ 
            modalOpen: false, 
            ticketSeleccionado: { user: {}, asunto: '', justificacion_alta: '', estatus: 'EN_PROCESO', mensajes: [] },
            abrirGestion(ticket) {
                this.ticketSeleccionado = ticket;
                this.modalOpen = true;
            }
         }">
        
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Buzón de Soporte Técnico General</h1>
                <p class="text-slate-500 text-sm">Gestión y resolución de fallos reportados por directores, inspectores y ciudadanos.</p>
            </div>
        </div>

        @if(session('success'))
            <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-sm font-semibold shadow-sm">
                {{ session('success') }}
            </div>
        @endif
        
        @if(session('error'))
            <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-xl text-sm font-semibold shadow-sm">
                {{ session('error') }}
            </div>
        @endif

        <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-sm">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-100 text-xs uppercase text-slate-600 border-b border-slate-200 font-bold tracking-wider">
                    <tr>
                        <th class="px-6 py-4">Usuario / Rol</th>
                        <th class="px-6 py-4">Asunto del Reporte</th>
                        <th class="px-6 py-4">Prioridad / Estatus</th>
                        <th class="px-6 py-4 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($tickets as $ticket)
                        <tr class="hover:bg-slate-50/80 transition-colors align-middle">
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-800">{{ $ticket->user->nombre ?? 'N/A' }} {{ $ticket->user->apellido ?? '' }}</div>
                                <div class="text-xs text-slate-400 font-mono">{{ $ticket->user->correo ?? 'Sin correo' }}</div>
                                <span class="inline-block mt-1 px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-slate-100 text-slate-600">
                                    {{ $ticket->user->role->nombre ?? 'Usuario' }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <strong class="text-slate-900 block text-sm">{{ $ticket->asunto }}</strong>
                                <span class="text-xs text-slate-400">Enviado: {{ $ticket->created_at->format('d/m/Y h:i A') }}</span>
                            </td>
                            <td class="px-6 py-4 space-y-1.5">
                                <span class="px-2.5 py-0.5 rounded-full font-bold text-[10px] uppercase 
                                    {{ $ticket->prioridad === 'ALTA' ? 'bg-rose-100 text-rose-800 border border-rose-200' : 'bg-slate-100 text-slate-700' }}">
                                    {{ $ticket->prioridad }}
                                </span>
                                <div>
                                    <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase
                                        {{ $ticket->estatus === 'RESUELTO' ? 'bg-emerald-100 text-emerald-800' : ($ticket->estatus === 'EN_PROCESO' ? 'bg-blue-100 text-blue-800' : 'bg-amber-100 text-amber-800') }}">
                                        {{ str_replace('_', ' ', $ticket->estatus) }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-right space-x-2">
                                <button type="button" @click="abrirGestion(@js($ticket))" class="px-3 py-2 bg-slate-800 hover:bg-slate-900 text-white rounded-xl text-xs font-bold transition inline-flex items-center gap-1.5 shadow-sm cursor-pointer">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                                    <span>Gestionar Chat</span>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-slate-400 text-sm">No hay reportes de soporte pendientes.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Paginación -->
        @if($tickets->hasPages())
            <div class="p-4 bg-white border border-slate-200 rounded-2xl shadow-sm">
                {{ $tickets->links() }}
            </div>
        @endif

        <!-- MODAL DE CHAT PARA EL ADMIN -->
        <div x-show="modalOpen" x-cloak class="fixed inset-0 z-50 bg-slate-950/60 backdrop-blur-sm flex items-center justify-center p-4">
            <div @click.outside="modalOpen = false" class="bg-white rounded-3xl border border-slate-200 w-full max-w-3xl overflow-hidden shadow-2xl flex flex-col h-[85vh]">
                
                <!-- Encabezado del Modal -->
                <div class="p-5 border-b border-slate-100 flex justify-between items-center bg-slate-50 shrink-0">
                    <div>
                        <h3 class="text-lg font-bold text-slate-800" x-text="ticketSeleccionado.asunto"></h3>
                        <div class="flex items-center gap-2 mt-1">
                            <span class="text-xs text-slate-500 font-medium" x-text="'Reportado por: ' + (ticketSeleccionado.user.nombre || '') + ' ' + (ticketSeleccionado.user.apellido || '')"></span>
                            <span class="text-[10px] font-bold uppercase px-2 py-0.5 rounded-full" 
                                  :class="ticketSeleccionado.prioridad === 'ALTA' ? 'bg-rose-100 text-rose-700' : 'bg-slate-200 text-slate-600'"
                                  x-text="'Prioridad: ' + ticketSeleccionado.prioridad"></span>
                        </div>
                    </div>
                    <button type="button" @click="modalOpen = false" class="text-slate-400 hover:text-slate-700 text-2xl font-bold leading-none cursor-pointer">&times;</button>
                </div>

                <!-- Historial de Mensajes (Chat) -->
                <div class="flex-1 overflow-y-auto p-5 space-y-4 bg-slate-50/50">
                    <!-- Justificación Alta -->
                    <template x-if="ticketSeleccionado.justificacion_alta">
                        <div class="p-3 bg-rose-50 border border-rose-200 rounded-xl text-xs text-rose-800 text-center w-full max-w-lg mx-auto">
                            <strong>Justificación de urgencia:</strong> <span x-text="ticketSeleccionado.justificacion_alta"></span>
                        </div>
                    </template>

                    <!-- Burbujas del chat -->
                    <template x-for="msg in ticketSeleccionado.mensajes" :key="msg.id">
                        <div class="flex flex-col" :class="msg.es_admin ? 'items-end' : 'items-start'">
                            <!-- Burbuja -->
                            <div class="max-w-[75%] rounded-2xl p-3 text-sm shadow-sm"
                                :class="msg.es_admin ? 'bg-indigo-600 text-white rounded-tr-sm' : 'bg-white border border-slate-200 text-slate-800 rounded-tl-sm'">
                                <p class="whitespace-pre-line" x-text="msg.mensaje"></p>
                                
                                <!-- Evidencia adjunta en el mensaje -->
                                <template x-if="msg.evidencia_path">
                                    <a :href="'/storage/' + msg.evidencia_path" target="_blank" class="mt-2 block text-[11px] underline font-bold" :class="msg.es_admin ? 'text-indigo-200 hover:text-white' : 'text-indigo-600 hover:text-indigo-800'">
                                        📎 Ver Archivo Adjunto
                                    </a>
                                </template>
                            </div>
                            
                            <!-- Fecha y Autor -->
                            <span class="text-[10px] text-slate-400 mt-1 px-1" 
                                x-text="(msg.es_admin ? 'Tú (Soporte)' : (ticketSeleccionado.user.nombre || 'Usuario')) + ' • ' + new Date(msg.created_at).toLocaleString()">
                            </span>
                        </div>
                    </template>
                </div>

                <!-- Formulario de Respuesta y Cambio de Estatus -->
                <div class="p-5 bg-white border-t border-slate-100 shrink-0">
                    <form :action="'/admin/soporte/' + ticketSeleccionado.id + '/responder'" method="POST" enctype="multipart/form-data" class="space-y-4">
                        @csrf
                        @method('PATCH')

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <!-- Campo de texto para el mensaje -->
                            <div class="md:col-span-2">
                                <label class="block font-bold text-slate-700 uppercase mb-1 text-xs">Respuesta / Mensaje *</label>
                                <textarea name="mensaje" rows="2" required placeholder="Escribe la solución o pregunta al usuario..." class="w-full bg-slate-50 border border-slate-300 rounded-xl p-3 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none resize-none"></textarea>
                            </div>

                            <!-- Estatus y Archivo Adjunto -->
                            <div class="space-y-3">
                                <div>
                                    <label class="block font-bold text-slate-700 uppercase mb-1 text-xs">Estatus del Ticket *</label>
                                    <select name="estatus" x-model="ticketSeleccionado.estatus" required class="w-full bg-slate-50 border border-slate-300 rounded-xl p-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                                        <option value="ABIERTO">Abierto</option>
                                        <option value="EN_PROCESO">En Proceso</option>
                                        <option value="RESUELTO">Resuelto</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block font-bold text-slate-700 uppercase mb-1 text-xs">Adjuntar Archivo (Opcional)</label>
                                    <input type="file" name="evidencia" accept="image/*,.pdf" class="w-full text-xs text-slate-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer">
                                </div>
                            </div>
                        </div>

                        <!-- Botón de Enviar -->
                        <div class="flex justify-end gap-2 pt-2 border-t border-slate-100">
                            <button type="button" @click="modalOpen = false" class="px-4 py-2 bg-slate-100 text-slate-600 font-bold rounded-xl hover:bg-slate-200 transition text-sm cursor-pointer">Cancelar</button>
                            <button type="submit" class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl transition shadow-sm cursor-pointer text-sm">
                                Enviar Respuesta
                            </button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
</x-layouts.app>