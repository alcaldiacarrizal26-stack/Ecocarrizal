<x-layouts.app title="Gestión de Inspectores" active="inspectores">
    <div x-data="{ 
        modalRegisterOpen: false,
        modalDetailsOpen: false,
        selectedInspector: null,
        searchCedula: '',
        activeTab: 'denuncias',
        
        // Form states
        nacionalidad: 'V',
        cedulaNum: '',
        codigoTelefono: '0412',
        telefonoNum: '',
        correlativoCredencial: '',
        nombre: '',
        apellido: '',
        correo: '',
        especialidad: '',
        direccionId: '1',
        
        isFormValid() {
            return this.cedulaNum.trim().length >= 6 &&
                this.correlativoCredencial.trim().length === 3 &&
                this.nombre.trim() !== '' &&
                this.apellido.trim() !== '' &&
                this.correo.trim() !== '' &&
                this.telefonoNum.trim().length === 7 &&
                this.especialidad.trim() !== '';
        },

        openDetails(inspector) {
            this.selectedInspector = inspector;
            this.activeTab = 'denuncias';
            this.modalDetailsOpen = true;
        }
    }" class="p-6">
        
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Gestión de Inspectores</h1>
                <p class="text-sm text-slate-500">Panel Directivo - Acreditación y control de funcionarios actuantes</p>
            </div>
            <button 
                @click="modalRegisterOpen = true" 
                class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-xl shadow-md shadow-emerald-600/10 transition-all flex items-center gap-2 cursor-pointer"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                <span>Registrar Inspector</span>
            </button>
        </div>

        @if(session('success'))
            <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm font-medium flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <!-- Tabla y Filtros -->
        <div class="mb-4 flex flex-col sm:flex-row justify-between items-center gap-4 bg-white p-4 rounded-2xl border border-slate-200 shadow-sm">
            <div class="text-sm text-slate-600 font-medium">
                Total registrado: <span class="text-emerald-700 font-bold">{{ $inspectores->count() }}</span> inspectores
            </div>
            <div class="w-full sm:w-72 relative">
                <input 
                    type="text" 
                    x-model="searchCedula" 
                    placeholder="Buscar por cédula..." 
                    class="w-full pl-9 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-800 placeholder-slate-400 focus:bg-white focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 focus:outline-none transition-all"
                >
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
        </div>

        <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="bg-slate-50 text-xs text-slate-500 font-semibold uppercase tracking-wider border-b border-slate-200">
                        <tr>
                            <th class="px-6 py-4">Credencial</th>
                            <th class="px-6 py-4">Funcionario</th>
                            <th class="px-6 py-4">Cédula</th>
                            <th class="px-6 py-4">Dirección</th>
                            <th class="px-6 py-4 text-center">Casos Totales</th>
                            <th class="px-6 py-4 text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($inspectores as $inspector)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="px-6 py-4 font-mono font-bold text-emerald-700">{{ $inspector->codigo_credencial }}</td>
                                <td class="px-6 py-4 font-medium text-slate-900">{{ $inspector->user->nombre }} {{ $inspector->user->apellido }}</td>
                                <td class="px-6 py-4 text-slate-700 font-medium whitespace-nowrap">{{ $inspector->user->cedula }}</td>
                                <td class="px-6 py-4 text-xs font-semibold text-slate-600">
                                    {{ $inspector->direccion->nombre_direc ?? 'General' }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-slate-100 text-slate-800 border">
                                        {{ $inspector->denuncias->count() + $inspector->solicitudes->count() }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <button 
                                        @click="openDetails({{ json_encode([
                                            'id' => $inspector->id,
                                            'nombre' => $inspector->user->nombre . ' ' . $inspector->user->apellido,
                                            'cedula' => $inspector->user->cedula ?? 'N/A',
                                            'correo' => $inspector->user->correo ?? $inspector->user->email,
                                            'telefono' => $inspector->user->telefono ?? 'Sin Registrar',
                                            'credencial' => $inspector->codigo_credencial,
                                            'especialidad' => $inspector->especialidad ?? 'General',
                                            'disponible' => (bool) $inspector->disponible,
                                            'toggle_url' => route('director.inspectores.toggle', $inspector->id),
                                            'denuncias' => $inspector->denuncias->map(fn($d) => ['id' => $d->id, 'codigo' => $d->codigo, 'tipo' => $d->tipo_contaminacion ?? 'Ambiental', 'sector' => $d->sector ?? 'N/A', 'estatus' => ucfirst(str_replace('_', ' ', $d->estatus))])->values(),
                                            'solicitudes' => $inspector->solicitudes->map(fn($s) => ['id' => $s->id, 'codigo' => $s->codigo_expediente, 'tipo' => $s->tipo_tramite ?? 'Trámite', 'sector' => $s->sector ?? 'N/A', 'estatus' => ucfirst(str_replace('_', ' ', $s->estado))])->values(),
                                        ]) }})"
                                        class="px-3 py-1.5 bg-slate-100 hover:bg-emerald-50 text-slate-700 hover:text-emerald-700 border text-xs font-semibold rounded-lg transition-all cursor-pointer"
                                    >
                                        Detalles
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-slate-400">No hay inspectores registrados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Modal de Registro de Inspector -->
        <div x-show="modalRegisterOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm">
            <div @click.away="modalRegisterOpen = false" class="bg-white border border-slate-200/80 rounded-3xl w-full max-w-2xl overflow-hidden shadow-2xl p-6 transition-all space-y-4">
                
                <div class="flex justify-between items-center pb-3 border-b border-slate-100">
                    <div>
                        <h3 class="text-base font-bold text-slate-900">Registrar Nuevo Inspector</h3>
                        <p class="text-xs text-slate-400">Se le enviará un correo automático para que configure su contraseña de acceso.</p>
                    </div>
                    <button @click="modalRegisterOpen = false" class="text-slate-400 hover:text-slate-600 text-xl font-bold cursor-pointer leading-none">&times;</button>
                </div>

                <form action="{{ route('director.inspectores.store') }}" method="POST" class="space-y-4">
                    @csrf

                    <input type="hidden" name="cedula" :value="nacionalidad + '-' + cedulaNum">
                    <input type="hidden" name="telefono" :value="codigoTelefono + '-' + telefonoNum">
                    <input type="hidden" name="codigo_credencial" :value="'CZ-INSP-' + correlativoCredencial">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Cédula de Identidad <span class="text-rose-500">*</span></label>
                            <div class="flex rounded-xl border border-slate-200 overflow-hidden focus-within:border-emerald-500">
                                <select x-model="nacionalidad" class="bg-slate-50 text-xs font-semibold px-3 py-2 border-r border-slate-200 focus:outline-none">
                                    <option value="V">V-</option><option value="E">E-</option>
                                </select>
                                <input type="text" x-model="cedulaNum" @input="cedulaNum = cedulaNum.replace(/[^0-9]/g, '').slice(0, 8)" placeholder="12345678" required class="w-full px-3 py-2 text-sm text-slate-800 focus:outline-none">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Código Credencial <span class="text-rose-500">*</span></label>
                            <div class="flex rounded-xl border border-slate-200 overflow-hidden focus-within:border-emerald-500">
                                <span class="bg-slate-50 text-xs font-mono font-semibold px-3 py-2 border-r border-slate-200 flex items-center text-slate-500">CZ-INSP-</span>
                                <input type="text" x-model="correlativoCredencial" @input="correlativoCredencial = correlativoCredencial.replace(/[^0-9]/g, '').slice(0, 3)" placeholder="001" required class="w-20 px-3 py-2 text-sm font-mono font-semibold text-center text-slate-800 focus:outline-none">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Nombres <span class="text-rose-500">*</span></label>
                            <input type="text" name="nombre" x-model="nombre" required class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm text-slate-800 focus:border-emerald-500 focus:outline-none">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Apellidos <span class="text-rose-500">*</span></label>
                            <input type="text" name="apellido" x-model="apellido" required class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm text-slate-800 focus:border-emerald-500 focus:outline-none">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Correo Electrónico <span class="text-rose-500">*</span></label>
                            <input type="email" name="correo" x-model="correo" required placeholder="correo@ejemplo.com" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm text-slate-800 focus:border-emerald-500 focus:outline-none">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Teléfono Móvil <span class="text-rose-500">*</span></label>
                            <div class="flex rounded-xl border border-slate-200 overflow-hidden focus-within:border-emerald-500">
                                <select x-model="codigoTelefono" class="bg-slate-50 text-xs font-semibold px-2 py-2 border-r border-slate-200 focus:outline-none">
                                    <option value="0412">0412</option><option value="0414">0414</option><option value="0424">0424</option><option value="0416">0416</option><option value="0426">0426</option>
                                </select>
                                <input type="text" x-model="telefonoNum" @input="telefonoNum = telefonoNum.replace(/[^0-9]/g, '').slice(0, 7)" placeholder="1234567" required class="w-full px-3 py-2 text-sm text-slate-800 focus:outline-none">
                            </div>
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Especialidad <span class="text-rose-500">*</span></label>
                            <input type="text" name="especialidad" x-model="especialidad" required placeholder="Ej. Control de Contaminación" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm text-slate-800 focus:border-emerald-500 focus:outline-none">
                        </div>
                    </div>

                    <div class="flex justify-end gap-2 pt-3 border-t border-slate-100">
                        <button type="button" @click="modalRegisterOpen = false" class="px-4 py-2 bg-slate-100 text-slate-600 text-xs font-semibold rounded-xl hover:bg-slate-200 transition-colors cursor-pointer">Cancelar</button>
                        <button type="submit" :disabled="!isFormValid()" :class="isFormValid() ? 'bg-emerald-600 hover:bg-emerald-700 text-white cursor-pointer shadow-sm' : 'bg-slate-200 text-slate-400 cursor-not-allowed opacity-60'" class="px-5 py-2 text-xs font-semibold rounded-xl transition-all">
                            Registrar y Enviar Credenciales
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-layouts.app>