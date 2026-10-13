<x-layouts.app title="Administración de Soporte Técnico" active="soporte">
    <div class="p-6 max-w-7xl mx-auto space-y-6" 
         x-data="{ 
            modalOpen: false, 
            ticketSeleccionado: { user: {}, asunto: '', mensaje: '', justificacion_alta: '', evidencia_path: '', respuesta_admin: '', estatus: 'EN_PROCESO' },
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
                                        {{ $ticket->estatus === 'RESUELTO' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                        {{ str_replace('_', ' ', $ticket->estatus) }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-right space-x-2">
                                <!-- Botón limpio que pasa el objeto directamente mediante un método seguro -->
                                <button type="button" @click="abrirGestion(@js($ticket))" class="px-3 py-2 bg-slate-800 hover:bg-slate-900 text-white rounded-xl text-xs font-bold transition inline-flex items-center gap-1.5 shadow-sm cursor-pointer">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    <span>Gestionar Ticket</span>
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

        <!-- MODAL DESPLEGABLE DE DETALLES Y RESPUESTA (Sin x-cloak para evitar bloqueos visuales) -->
        <div x-show="modalOpen" class="fixed inset-0 z-50 bg-slate-950/60 backdrop-blur-sm flex items-center justify-center p-4" style="display: none;">
            <div @click.outside="modalOpen = false" class="bg-white rounded-2xl border border-slate-200 w-full max-w-2xl overflow-hidden shadow-2xl space-y-4 p-6 max-h-[90vh] overflow-y-auto">
                
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h3 class="text-lg font-bold text-slate-800">Detalles del Reporte de Soporte</h3>
                    <button type="button" @click="modalOpen = false" class="text-slate-400 hover:text-slate-700 text-xl font-bold">&times;</button>
                </div>

                <div class="space-y-4 text-xs">
                    <!-- Info del Emisor -->
                    <div class="bg-slate-50 p-3 rounded-xl border border-slate-100 grid grid-cols-2 gap-2">
                        <div>
                            <span class="text-slate-400 block font-bold uppercase">Reportado por:</span>
                            <strong class="text-slate-800 text-sm" x-text="(ticketSeleccionado.user.nombre || '') + ' ' + (ticketSeleccionado.user.apellido || '')"></strong>
                        </div>
                        <div>
                            <span class="text-slate-400 block font-bold uppercase">Correo Institucional:</span>
                            <strong class="text-slate-800" x-text="ticketSeleccionado.user.correo || 'N/A'"></strong>
                        </div>
                    </div>

                    <!-- Asunto y Mensaje -->
                    <div>
                        <span class="text-slate-400 block font-bold uppercase mb-1">Asunto y Descripción del Problema:</span>
                        <div class="p-3 bg-slate-50 rounded-xl border border-slate-200 space-y-1">
                            <strong class="text-slate-900 text-sm block" x-text="ticketSeleccionado.asunto"></strong>
                            <p class="text-slate-600 leading-relaxed" x-text="ticketSeleccionado.mensaje"></p>
                        </div>
                    </div>

                    <!-- Justificación si es Alta -->
                    <template x-if="ticketSeleccionado.justificacion_alta">
                        <div class="p-3 bg-rose-50 border border-rose-200 rounded-xl text-rose-900 space-y-1">
                            <span class="font-bold uppercase text-[10px] text-rose-700">Justificación de Urgencia Alta:</span>
                            <p x-text="ticketSeleccionado.justificacion_alta"></p>
                        </div>
                    </template>

                    <!-- Evidencia Adjunta si existe -->
                    <template x-if="ticketSeleccionado.evidencia_path">
                        <div>
                            <span class="text-slate-400 block font-bold uppercase mb-1">Evidencia / Captura de Pantalla:</span>
                            <a :href="'/storage/' + ticketSeleccionado.evidencia_path" target="_blank" class="px-3 py-2 bg-emerald-50 border border-emerald-200 text-emerald-800 font-bold rounded-xl inline-flex items-center gap-2 hover:bg-emerald-100 transition">
                                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                Ver Captura Adjunta en Pantalla Completa
                            </a>
                        </div>
                    </template>

                    <!-- Formulario de Respuesta del Admin -->
                    <form :action="'/admin/soporte/' + ticketSeleccionado.id + '/responder'" method="POST" class="space-y-3 pt-2 border-t border-slate-100">
                        @csrf
                        @method('PATCH')

                        <div>
                            <label class="block font-bold text-slate-700 uppercase mb-1">Respuesta o Solución Técnica *</label>
                            <textarea name="respuesta_admin" rows="3" required x-model="ticketSeleccionado.respuesta_admin" placeholder="Escribe las instrucciones o el estatus de solución para el usuario..." class="w-full bg-slate-50 border border-slate-300 rounded-xl p-2.5 text-xs"></textarea>
                        </div>

                        <div class="flex items-center justify-between">
                            <div>
                                <label class="block font-bold text-slate-700 uppercase mb-1">Actualizar Estatus *</label>
                                <select name="estatus" x-model="ticketSeleccionado.estatus" class="bg-slate-50 border border-slate-300 rounded-xl p-2 text-xs">
                                    <option value="EN_PROCESO">En Proceso</option>
                                    <option value="RESUELTO">Resuelto</option>
                                </select>
                            </div>

                            <div class="flex gap-2 pt-5">
                                <button type="button" @click="modalOpen = false" class="px-4 py-2 bg-slate-100 text-slate-600 font-bold rounded-xl hover:bg-slate-200 transition">Cancelar</button>
                                <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl transition shadow-sm cursor-pointer">Guardar y Enviar Respuesta</button>
                            </div>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
</x-layouts.app>