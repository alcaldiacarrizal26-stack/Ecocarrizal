<x-layouts.app title="Gestión de Inspectores" active="ambiente_inspectores">
    <div x-data="{ 
        modalRegisterOpen: false,
        modalDetailsOpen: false,
        selectedInspector: null,
        searchCedula: '',
        
        // Form states
        showPassword: false,
        showPasswordConfirm: false,
        nacionalidad: 'V',
        cedulaNum: '',
        codigoTelefono: '0412',
        telefonoNum: '',
        prefijoCredencial: 'CZ-INSP-',
        correlativoCredencial: '',
        nombre: '',
        apellido: '',
        correo: '',
        especialidad: '',
        password: '',
        passwordConfirm: '',
        
        isFormValid() {
            return this.cedulaNum.trim().length >= 6 &&
                this.correlativoCredencial.trim().length === 3 &&
                this.nombre.trim() !== '' &&
                this.apellido.trim() !== '' &&
                this.correo.trim() !== '' &&
                this.telefonoNum.trim().length === 7 &&
                this.especialidad.trim() !== '' &&
                this.password.trim() !== '' &&
                this.passwordConfirm.trim() !== '' &&
                (this.password === this.passwordConfirm);
        },

        openDetails(inspector) {
            this.selectedInspector = inspector;
            this.modalDetailsOpen = true;
        }
    }" class="p-6">
        
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Gestión de Inspectores</h1>
                <p class="text-sm text-slate-500">Dirección de Ambiente - Acreditación y control de funcionarios actuantes</p>
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

        <!-- Filtros y Opciones de Tabla -->
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

        <!-- Tabla de Inspectores -->
        <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="bg-slate-50 text-xs text-slate-500 font-semibold uppercase tracking-wider border-b border-slate-200">
                        <tr>
                            <th class="px-6 py-4">Credencial</th>
                            <th class="px-6 py-4">Funcionario</th>
                            <th class="px-6 py-4">Cédula</th>
                            <th class="px-6 py-4">Contacto</th>
                            <th class="px-6 py-4 text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($inspectores as $inspector)
                            <tr 
                                x-show="!searchCedula || '{{ $inspector->user->cedula }}'.toLowerCase().includes(searchCedula.toLowerCase())"
                                class="hover:bg-slate-50/80 transition-colors"
                            >
                                <td class="px-6 py-4 font-mono font-bold text-emerald-700">
                                    {{ $inspector->codigo_credencial }}
                                </td>
                                <td class="px-6 py-4 font-medium text-slate-900">
                                    {{ $inspector->user->nombre }} {{ $inspector->user->apellido }}
                                </td>
                                <td class="px-6 py-4 text-slate-700 font-medium whitespace-nowrap">{{ $inspector->user->cedula }}</td>
                                <td class="px-6 py-4">
                                    <div class="text-xs text-slate-800 font-medium">{{ $inspector->user->correo }}</div>
                                    <div class="text-xs text-slate-500 mt-0.5">{{ $inspector->user->telefono }}</div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                <button 
                                    @click="openDetails({{ json_encode([
                                        'id' => $inspector->id,
                                        'nombre' => $inspector->user->nombre . ' ' . $inspector->user->apellido,
                                        'cedula' => $inspector->user->cedula ?? 'N/A',
                                        'correo' => $inspector->user->correo ?? $inspector->user->email,
                                        'telefono' => $inspector->user->telefono ?? 'Sin Registrar',
                                        'credencial' => $inspector->codigo_credencial ?? ('INS-' . str_pad($inspector->id, 4, '0', STR_PAD_LEFT)),
                                        'especialidad' => $inspector->especialidad ?? 'General',
                                        'disponible' => (bool) $inspector->disponible,
                                        'toggle_url' => route('director.ambiente.inspectores.toggle', $inspector->id),
                                        'casos' => $inspector->denuncias->map(fn($d) => [
                                            'id' => 'denuncia_' . $d->id,
                                            'codigo' => $d->codigo,
                                            'descripcion' => 'Denuncia: ' . $d->tipo_contaminacion . ' (' . $d->sector . ')',
                                            'estatus' => ucfirst(str_replace('_', ' ', $d->estatus)),
                                        ])->concat($inspector->solicitudes->map(fn($s) => [
                                            'id' => 'solicitud_' . $s->id,
                                            'codigo' => $s->codigo_expediente,
                                            'descripcion' => 'Tramitación: ' . $s->tipo_tramite . ' (' . $s->sector . ')',
                                            'estatus' => ucfirst(str_replace('_', ' ', $s->estado)),
                                        ]))->values()
                                    ]) }})"
                                    class="px-3 py-1.5 bg-slate-100 hover:bg-emerald-50 text-slate-700 hover:text-emerald-700 border border-slate-200 hover:border-emerald-200 text-xs font-semibold rounded-lg transition-all cursor-pointer inline-flex items-center gap-1.5"
                                >
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    <span>Detalles</span>
                                </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-slate-400">
                                    <svg class="w-12 h-12 mx-auto text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                    No hay inspectores registrados en la Dirección de Ambiente.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Modal de Detalles del Inspector -->
        <div 
                x-show="modalDetailsOpen" 
                x-cloak 
                class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm"
            >
                <div @click.away="modalDetailsOpen = false" class="bg-white border border-slate-200 rounded-2xl w-full max-w-3xl overflow-hidden shadow-2xl p-6 transition-all">
                    
                    <template x-if="selectedInspector">
                        <div>
                            <!-- Header Modal -->
                            <div class="flex justify-between items-start pb-4 border-b border-slate-100">
                                <div>
                                    <div class="flex items-center gap-2">
                                        <h3 class="text-xl font-bold text-slate-900" x-text="selectedInspector.nombre"></h3>
                                        <span 
                                            :class="selectedInspector.disponible ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-amber-50 text-amber-700 border-amber-200'"
                                            class="px-2.5 py-0.5 text-xs font-semibold rounded-full border"
                                            x-text="selectedInspector.disponible ? 'Disponible' : 'No Disponible'"
                                        ></span>
                                    </div>
                                    <p class="text-xs text-slate-500 font-mono mt-0.5" x-text="'Credencial: ' + selectedInspector.credencial"></p>
                                </div>
                                <button @click="modalDetailsOpen = false" class="text-slate-400 hover:text-slate-600 text-xl font-bold cursor-pointer">&times;</button>
                            </div>

                            <!-- Información General -->
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 py-4 border-b border-slate-100 text-xs">
                                <div>
                                    <span class="text-slate-400 block font-medium">Cédula</span>
                                    <span class="font-bold text-slate-800" x-text="selectedInspector.cedula"></span>
                                </div>
                                <div>
                                    <span class="text-slate-400 block font-medium">Especialidad</span>
                                    <span class="font-bold text-slate-800" x-text="selectedInspector.especialidad"></span>
                                </div>
                                <div>
                                    <span class="text-slate-400 block font-medium">Correo</span>
                                    <span class="font-bold text-slate-800 truncate block" x-text="selectedInspector.correo"></span>
                                </div>
                                <div>
                                    <span class="text-slate-400 block font-medium">Teléfono</span>
                                    <span class="font-bold text-slate-800" x-text="selectedInspector.telefono"></span>
                                </div>
                            </div>

                            <!-- Historial / Casos Asignados -->
                            <div class="py-4">
                                <h4 class="text-sm font-bold text-slate-800 mb-3 flex items-center justify-between">
                                    <span>Casos y Solicitudes Asignadas</span>
                                    <span class="text-xs bg-slate-100 text-slate-600 px-2 py-0.5 rounded-md font-mono" x-text="(selectedInspector.casos ? selectedInspector.casos.length : 0) + ' asignados'"></span>
                                </h4>

                                <div class="max-h-48 overflow-y-auto space-y-2 pr-1">
                                    <template x-if="selectedInspector.casos && selectedInspector.casos.length > 0">
                                        <div>
                                            <template x-for="caso in selectedInspector.casos" :key="caso.id">
                                                <div class="p-3 bg-slate-50 rounded-xl border border-slate-200 flex justify-between items-center text-xs mb-2">
                                                    <div>
                                                        <div class="font-semibold text-slate-800" x-text="caso.codigo"></div>
                                                        <div class="text-slate-500" x-text="caso.descripcion"></div>
                                                    </div>
                                                    <span class="px-2 py-1 rounded-md bg-white border border-slate-200 font-medium text-slate-600" x-text="caso.estatus"></span>
                                                </div>
                                            </template>
                                        </div>
                                    </template>

                                    <template x-if="!selectedInspector.casos || selectedInspector.casos.length === 0">
                                        <div class="text-center py-6 text-slate-400 bg-slate-50 rounded-xl border border-dashed border-slate-200 text-xs">
                                            El inspector actualmente no posee reportes ni solicitudes en ejecución.
                                        </div>
                                    </template>
                                </div>
                            </div>

                            <!-- Footer Modal con Acción de Estatus -->
                            <div class="flex justify-between items-center pt-4 border-t border-slate-100">
                                <form :action="selectedInspector.toggle_url" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button 
                                        type="submit" 
                                        :class="selectedInspector.disponible ? 'bg-amber-50 text-amber-700 hover:bg-amber-100 border-amber-200' : 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100 border-emerald-200'"
                                        class="px-3 py-2 text-xs font-semibold rounded-xl border transition-colors cursor-pointer"
                                        x-text="selectedInspector.disponible ? 'Marcar NO Disponible' : 'Marcar Disponible'"
                                    ></button>
                                </form>

                                <button type="button" @click="modalDetailsOpen = false" class="px-4 py-2 bg-slate-100 text-slate-600 text-xs font-semibold rounded-xl hover:bg-slate-200 transition-colors cursor-pointer">Cerrar</button>
                            </div>
                        </div>
                    </template>

                </div>
            </div>
        </div>

        <!-- Modal de Registro de Inspector -->
        <div 
            x-show="modalRegisterOpen" 
            x-cloak 
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm"
        >
            <div @click.away="modalRegisterOpen = false" class="bg-white border border-slate-200 rounded-2xl w-full max-w-2xl overflow-hidden shadow-2xl p-6 transition-all">
                <div class="flex justify-between items-center pb-4 border-b border-slate-100">
                    <div>
                        <h3 class="text-lg font-bold text-slate-900">Registrar Inspector de Ambiente</h3>
                        <p class="text-xs text-slate-500">Ingrese los datos institucionales del nuevo funcionario</p>
                    </div>
                    <button @click="modalRegisterOpen = false" class="text-slate-400 hover:text-slate-600 text-xl font-bold cursor-pointer">&times;</button>
                </div>

                <form action="{{ route('director.ambiente.inspectores.store') }}" method="POST" class="mt-5 space-y-4">
                    @csrf

                    <input type="hidden" name="cedula" :value="nacionalidad + '-' + cedulaNum">
                    <input type="hidden" name="telefono" :value="codigoTelefono + '-' + telefonoNum">
                    <input type="hidden" name="codigo_credencial" :value="'CZ-INSP-' + correlativoCredencial">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Cédula -->
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Cédula de Identidad <span class="text-rose-500">*</span></label>
                            <div class="flex rounded-xl border border-slate-200 overflow-hidden focus-within:border-emerald-500 focus-within:ring-1 focus-within:ring-emerald-500">
                                <select x-model="nacionalidad" class="bg-slate-50 text-slate-700 text-xs font-semibold px-3 py-2 border-r border-slate-200 focus:outline-none">
                                    <option value="V">V-</option>
                                    <option value="E">E-</option>
                                </select>
                                <input 
                                    type="text" 
                                    x-model="cedulaNum"
                                    @input="cedulaNum = cedulaNum.replace(/[^0-9]/g, '').slice(0, 8)" 
                                    placeholder="12345678" 
                                    required 
                                    class="w-full px-3 py-2 text-sm text-slate-800 placeholder-slate-400 focus:outline-none"
                                >
                            </div>
                        </div>

                        <!-- Credencial -->
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Código Credencial <span class="text-rose-500">*</span></label>
                            <div class="flex rounded-xl border border-slate-200 overflow-hidden focus-within:border-emerald-500 focus-within:ring-1 focus-within:ring-emerald-500">
                                <span class="flex-1 bg-slate-50 text-slate-600 text-xs font-mono font-semibold px-3 py-2 border-r border-slate-200 flex items-center">
                                    CZ-INSP-
                                </span>
                                <input 
                                    type="text" 
                                    x-model="correlativoCredencial"
                                    @input="correlativoCredencial = correlativoCredencial.replace(/[^0-9]/g, '').slice(0, 3)" 
                                    placeholder="001" 
                                    required 
                                    class="w-20 px-3 py-2 text-sm text-slate-800 font-mono font-semibold placeholder-slate-400 focus:outline-none text-center"
                                >
                            </div>
                        </div>

                        <!-- Nombres -->
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Nombres <span class="text-rose-500">*</span></label>
                            <input type="text" name="nombre" x-model="nombre" required class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 text-sm text-slate-800 placeholder-slate-400 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 focus:outline-none">
                        </div>

                        <!-- Apellidos -->
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Apellidos <span class="text-rose-500">*</span></label>
                            <input type="text" name="apellido" x-model="apellido" required class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 text-sm text-slate-800 placeholder-slate-400 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 focus:outline-none">
                        </div>

                        <!-- Correo Electrónico -->
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Correo Electrónico <span class="text-rose-500">*</span></label>
                            <input type="email" name="correo" x-model="correo" required class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 text-sm text-slate-800 placeholder-slate-400 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 focus:outline-none">
                        </div>

                        <!-- Teléfono Móvil -->
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Teléfono Móvil <span class="text-rose-500">*</span></label>
                            <div class="flex rounded-xl border border-slate-200 overflow-hidden focus-within:border-emerald-500 focus-within:ring-1 focus-within:ring-emerald-500">
                                <select x-model="codigoTelefono" class="bg-slate-50 text-slate-700 text-xs font-semibold px-2 py-2 border-r border-slate-200 focus:outline-none">
                                    <option value="0412">0412</option>
                                    <option value="0414">0414</option>
                                    <option value="0424">0424</option>
                                    <option value="0416">0416</option>
                                    <option value="0426">0426</option>
                                </select>
                                <input 
                                    type="text" 
                                    x-model="telefonoNum"
                                    @input="telefonoNum = telefonoNum.replace(/[^0-9]/g, '').slice(0, 7)" 
                                    placeholder="1234567" 
                                    required 
                                    class="w-full px-3 py-2 text-sm text-slate-800 placeholder-slate-400 focus:outline-none"
                                >
                            </div>
                        </div>

                        <!-- Especialidad -->
                        <div class="md:col-span-2">
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Especialidad <span class="text-rose-500">*</span></label>
                            <input type="text" name="especialidad" x-model="especialidad" required placeholder="Ej. Control de Contaminación, Control Foliar, Ruidos Molestos" class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 text-sm text-slate-800 placeholder-slate-400 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 focus:outline-none">
                        </div>

                        <!-- Contraseña Inicial -->
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Contraseña Inicial <span class="text-rose-500">*</span></label>
                            <div class="relative">
                                <input 
                                    :type="showPassword ? 'text' : 'password'" 
                                    name="password" 
                                    x-model="password"
                                    required 
                                    placeholder="••••••••"
                                    class="w-full px-3 py-2 bg-white border border-slate-200 rounded-xl text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500 transition-all pr-10"
                                >
                                <button 
                                    type="button" 
                                    @click="showPassword = !showPassword" 
                                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 transition-colors cursor-pointer"
                                >
                                    <svg x-show="!showPassword" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    <svg x-show="showPassword" x-cloak class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858-5.908a10.012 10.012 0 014.122-.963c4.478 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21M3 3l18 18"/>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Confirmar Contraseña -->
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Confirmar Contraseña <span class="text-rose-500">*</span></label>
                            <div class="relative">
                                <input 
                                    :type="showPasswordConfirm ? 'text' : 'password'" 
                                    name="password_confirmation" 
                                    x-model="passwordConfirm"
                                    required 
                                    placeholder="••••••••"
                                    class="w-full px-3 py-2 bg-white border border-slate-200 rounded-xl text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500 transition-all pr-10"
                                >
                                <button 
                                    type="button" 
                                    @click="showPasswordConfirm = !showPasswordConfirm" 
                                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 transition-colors cursor-pointer"
                                >
                                    <svg x-show="!showPasswordConfirm" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    <svg x-show="showPasswordConfirm" x-cloak class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858-5.908a10.012 10.012 0 014.132 5.411m0 0L21 21M3 3l18 18"/>
                                    </svg>
                                </button>
                            </div>
                        </div>

                    </div>

                    <!-- Botones -->
                    <div class="flex justify-end gap-3 pt-4 border-t border-slate-100">
                        <button type="button" @click="modalRegisterOpen = false" class="px-4 py-2 bg-slate-100 text-slate-600 text-sm font-semibold rounded-xl hover:bg-slate-200 transition-colors cursor-pointer">Cancelar</button>
                        
                        <button 
                            type="submit" 
                            :disabled="!isFormValid()" 
                            :class="isFormValid() ? 'bg-emerald-600 hover:bg-emerald-700 text-white cursor-pointer shadow-md shadow-emerald-600/10' : 'bg-slate-300 text-slate-500 cursor-not-allowed opacity-60'" 
                            class="px-4 py-2 text-sm font-semibold rounded-xl transition-all"
                        >
                            Guardar Inspector
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-layouts.app>