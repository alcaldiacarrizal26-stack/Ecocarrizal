<x-layouts.app title="Gestión de Inspectores" active="ambiente_inspectores">
    <div x-data="{ modalOpen: false }" class="p-6">
        
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight">Gestión de Inspectores</h1>
                <p class="text-sm text-slate-500 dark:text-slate-400">Dirección de Ambiente - Acreditación y control de funcionarios actuantes</p>
            </div>
            <button 
                @click="modalOpen = true" 
                class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white text-sm font-semibold rounded-xl shadow-lg shadow-emerald-600/20 transition-all flex items-center gap-2"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                <span>Registrar Inspector</span>
            </button>
        </div>

        @if(session('success'))
            <div class="mb-6 p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-sm">
                {{ session('success') }}
            </div>
        @endif

        <!-- Tabla de Inspectores -->
        <div class="bg-slate-900 border border-slate-800 rounded-2xl overflow-hidden shadow-xl">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-300">
                    <thead class="bg-slate-800/60 text-xs text-slate-400 uppercase tracking-wider border-b border-slate-800">
                        <tr>
                            <th class="px-6 py-4">Credencial</th>
                            <th class="px-6 py-4">Funcionario</th>
                            <th class="px-6 py-4">Cédula</th>
                            <th class="px-6 py-4">Contacto</th>
                            <th class="px-6 py-4">Especialidad</th>
                            <th class="px-6 py-4">Estatus</th>
                            <th class="px-6 py-4 text-right">Acción</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/60">
                        @forelse($inspectores as $inspector)
                            <tr class="hover:bg-slate-800/40 transition-colors">
                                <td class="px-6 py-4 font-mono font-bold text-emerald-400">
                                    {{ $inspector->codigo_credencial }}
                                </td>
                                <td class="px-6 py-4 font-medium text-white">
                                    {{ $inspector->user->nombre }} {{ $inspector->user->apellido }}
                                </td>
                                <td class="px-6 py-4">{{ $inspector->user->cedula }}</td>
                                <td class="px-6 py-4">
                                    <div class="text-xs text-slate-300">{{ $inspector->user->correo }}</div>
                                    <div class="text-xs text-slate-500">{{ $inspector->user->telefono }}</div>
                                </td>
                                <td class="px-6 py-4 text-slate-400">
                                    {{ $inspector->especialidad ?? 'General' }}
                                </td>
                                <td class="px-6 py-4">
                                    @if($inspector->disponible)
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                            Disponible
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-500/10 text-amber-400 border border-amber-500/20">
                                            No Disponible
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <form action="{{ route('director.ambiente.inspectores.toggle', $inspector->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="text-xs font-medium text-slate-400 hover:text-white underline">
                                            {{ $inspector->disponible ? 'Marcar No Disponible' : 'Marcar Disponible' }}
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-8 text-center text-slate-500">
                                    No hay inspectores registrados en la Dirección de Ambiente.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Modal de Registro -->
        <div x-show="modalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm">
            <div @click.away="modalOpen = false" class="bg-slate-900 border border-slate-800 rounded-2xl w-full max-w-xl overflow-hidden shadow-2xl p-6">
                <div class="flex justify-between items-center pb-4 border-b border-slate-800">
                    <h3 class="text-lg font-bold text-white">Registrar Inspector de Ambiente</h3>
                    <button @click="modalOpen = false" class="text-slate-400 hover:text-white">&times;</button>
                </div>

                <form action="{{ route('director.ambiente.inspectores.store') }}" method="POST" class="mt-4 space-y-4">
                    @csrf
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs text-slate-400 mb-1">Cédula</label>
                            <input type="text" name="cedula" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-sm text-white focus:border-emerald-500 focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-xs text-slate-400 mb-1">Código Credencial / Carnet</label>
                            <input type="text" name="codigo_credencial" placeholder="CZ-INSP-001" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-sm text-white focus:border-emerald-500 focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-xs text-slate-400 mb-1">Nombres</label>
                            <input type="text" name="nombre" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-sm text-white focus:border-emerald-500 focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-xs text-slate-400 mb-1">Apellidos</label>
                            <input type="text" name="apellido" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-sm text-white focus:border-emerald-500 focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-xs text-slate-400 mb-1">Correo Electrónico</label>
                            <input type="email" name="correo" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-sm text-white focus:border-emerald-500 focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-xs text-slate-400 mb-1">Teléfono</label>
                            <input type="text" name="telefono" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-sm text-white focus:border-emerald-500 focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-xs text-slate-400 mb-1">Especialidad (Opcional)</label>
                            <input type="text" name="especialidad" placeholder="Flora, Ruido, Rastro..." class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-sm text-white focus:border-emerald-500 focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-xs text-slate-400 mb-1">Contraseña Inicial</label>
                            <input type="password" name="password" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-sm text-white focus:border-emerald-500 focus:outline-none">
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 pt-4 border-t border-slate-800">
                        <button type="button" @click="modalOpen = false" class="px-4 py-2 bg-slate-800 text-slate-300 text-sm font-medium rounded-xl hover:bg-slate-700">Cancelar</button>
                        <button type="submit" class="px-4 py-2 bg-emerald-600 text-white text-sm font-semibold rounded-xl hover:bg-emerald-500 shadow-lg shadow-emerald-600/20">Guardar Inspector</button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-layouts.app>