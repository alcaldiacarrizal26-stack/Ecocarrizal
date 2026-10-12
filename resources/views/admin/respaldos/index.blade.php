<x-layouts.app title="Respaldos de Base de Datos" active="admin_respaldos">
    <div class="p-6 max-w-7xl mx-auto space-y-6" 
         x-data="{ 
            confirmDeleteOpen: false, 
            archivoAEliminar: '', 
            abrirConfirmacion(nombre) {
                this.archivoAEliminar = nombre;
                this.confirmDeleteOpen = true;
            } 
         }">
        
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Respaldos de Base de Datos</h1>
                <p class="text-slate-500 text-sm">Generación, descarga y administración de copias de seguridad de la base de datos PostgreSQL y archivos adjuntos.</p>
            </div>
            
            <form action="{{ route('admin.respaldos.store') }}" method="POST">
                @csrf
                <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl text-xs transition shadow-sm cursor-pointer inline-flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                    <span>Generar Nuevo Respaldo</span>
                </button>
            </form>
        </div>

        <!-- Alerta de Éxito con botón de cierre -->
        @if(session('success'))
            <div x-data="{ show: true }" x-show="show" x-transition class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-sm font-semibold shadow-sm flex items-center justify-between">
                <span>{{ session('success') }}</span>
                <button @click="show = false" class="text-emerald-600 hover:text-emerald-900 font-bold text-lg px-2 cursor-pointer">&times;</button>
            </div>
        @endif

        <!-- Alerta de Error con botón de cierre -->
        @if($errors->any())
            <div x-data="{ show: true }" x-show="show" x-transition class="p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-xl text-sm font-semibold shadow-sm flex items-center justify-between">
                <span>{{ $errors->first() }}</span>
                <button @click="show = false" class="text-rose-600 hover:text-rose-900 font-bold text-lg px-2 cursor-pointer">&times;</button>
            </div>
        @endif

        <!-- Tabla de Backups Disponibles -->
        <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-sm">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-100 text-xs uppercase text-slate-600 border-b border-slate-200 font-bold tracking-wider">
                    <tr>
                        <th class="px-6 py-4">Nombre del Archivo</th>
                        <th class="px-6 py-4">Tamaño</th>
                        <th class="px-6 py-4">Fecha de Creación</th>
                        <th class="px-6 py-4 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs">
                    @forelse($archivos as $backup)
                        <tr class="hover:bg-slate-50/80 transition-colors align-middle">
                            <td class="px-6 py-4 font-mono font-bold text-slate-800">
                                {{ $backup['nombre'] }}
                            </td>
                            <td class="px-6 py-4 font-mono text-slate-500">
                                {{ $backup['tamano'] }}
                            </td>
                            <td class="px-6 py-4 font-mono text-slate-500">
                                {{ $backup['fecha'] }}
                            </td>
                            <td class="px-6 py-4 text-right space-x-2">
                                <a href="{{ route('admin.respaldos.download', ['file' => $backup['nombre']]) }}" class="px-3 py-2 bg-slate-800 hover:bg-slate-900 text-white rounded-xl text-xs font-bold transition inline-flex items-center gap-1.5 shadow-sm">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                    <span>Descargar</span>
                                </a>

                                <!-- Botón que activa el modal personalizado en lugar del alert nativo -->
                                <button type="button" @click="abrirConfirmacion('{{ $backup['nombre'] }}')" class="px-3 py-2 bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 rounded-xl text-xs font-bold transition inline-flex items-center gap-1.5 shadow-sm cursor-pointer">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    <span>Eliminar</span>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-slate-400 text-sm">No hay respaldos de base de datos o documentos generados todavía.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- MODAL DE CONFIRMACIÓN ESTÉTICO PARA ELIMINAR -->
        <div x-show="confirmDeleteOpen" class="fixed inset-0 z-50 bg-slate-950/60 backdrop-blur-sm flex items-center justify-center p-4" style="display: none;">
            <div @click.outside="confirmDeleteOpen = false" class="bg-white rounded-2xl border border-slate-200 w-full max-w-md overflow-hidden shadow-2xl p-6 space-y-5">
                
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-rose-50 text-rose-600 rounded-2xl flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-800">¿Eliminar este respaldo?</h3>
                        <p class="text-xs text-slate-500">Esta acción no se puede deshacer y el archivo se borrará permanentemente del servidor.</p>
                    </div>
                </div>

                <div class="bg-slate-50 p-3 rounded-xl border border-slate-200">
                    <span class="text-[10px] font-bold text-slate-400 uppercase block">Archivo seleccionado:</span>
                    <strong class="text-xs font-mono text-slate-800 break-all" x-text="archivoAEliminar"></strong>
                </div>

                <form action="{{ route('admin.respaldos.destroy') }}" method="POST" class="flex justify-end gap-2 pt-2">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" name="file" x-model="archivoAEliminar">
                    
                    <button type="button" @click="confirmDeleteOpen = false" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold rounded-xl text-xs transition cursor-pointer">
                        Cancelar
                    </button>
                    <button type="submit" class="px-5 py-2 bg-rose-600 hover:bg-rose-700 text-white font-bold rounded-xl text-xs transition shadow-sm cursor-pointer">
                        Sí, Eliminar Archivo
                    </button>
                </form>

            </div>
        </div>

    </div>
</x-layouts.app>