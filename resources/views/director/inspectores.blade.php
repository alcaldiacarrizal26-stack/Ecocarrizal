<x-layouts.app title="Gestión de Inspectores" active="inspectores">
    <div x-data="{ 
        modalRegisterOpen: false,
        modalDetailsOpen: false,
        isEditing: false,
        selectedInspector: null,
        searchCedula: '',
        activeTab: 'denuncias',
        
        // Form states (Registro)
        nacionalidad: 'V',
        cedulaNum: '',
        codigoTelefono: '0412',
        telefonoNum: '',
        correlativoCredencial: '',
        nombre: '',
        apellido: '',
        correo: '',
        especialidad: '',

        // Edit states
        editNac: 'V',
        editCedulaNum: '',
        editCodTel: '0412',
        editTelNum: '',
        editCredencial: '',
        editNombre: '',
        editApellido: '',
        editCorreo: '',
        editEspecialidad: '',
        editUpdateUrl: '',
        
        isFormValid() {
            return this.cedulaNum.trim().length >= 6 &&
                this.correlativoCredencial.trim().length === 3 &&
                this.nombre.trim() !== '' &&
                this.apellido.trim() !== '' &&
                this.correo.trim() !== '' &&
                this.telefonoNum.trim().length === 7 &&
                this.especialidad.trim() !== '';
        },

        openDetails(inspectorData) {
            this.selectedInspector = inspectorData;
            this.isEditing = false;
            this.activeTab = 'denuncias';
            this.modalDetailsOpen = true;
        },

        initEdit() {
            let cedParts = this.selectedInspector.cedula.split('-');
            this.editNac = cedParts[0] || 'V';
            this.editCedulaNum = cedParts[1] || this.selectedInspector.cedula;

            let telParts = this.selectedInspector.telefono.split('-');
            this.editCodTel = telParts[0] || '0412';
            this.editTelNum = telParts[1] || this.selectedInspector.telefono;

            let cred = this.selectedInspector.credencial;
            this.editCredencial = cred.startsWith('CZ-INSP-') ? cred.replace('CZ-INSP-', '') : cred;

            this.editNombre = this.selectedInspector.nombre.split(' ')[0] || '';
            this.editApellido = this.selectedInspector.nombre.split(' ').slice(1).join(' ') || '';
            this.editCorreo = this.selectedInspector.correo;
            this.editEspecialidad = this.selectedInspector.especialidad;
            this.editUpdateUrl = this.selectedInspector.update_url;
            
            this.isEditing = true;
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

        @if($errors->any())
            <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-sm font-medium">
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
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
                        @php
                            $inspectorData = [
                                'id' => $inspector->id,
                                'nombre' => trim(($inspector->user->nombre ?? '') . ' ' . ($inspector->user->apellido ?? '')),
                                'cedula' => $inspector->user->cedula ?? 'N/A',
                                'correo' => $inspector->user->correo ?? $inspector->user->email ?? 'N/A',
                                'telefono' => $inspector->user->telefono ?? 'Sin Registrar',
                                'credencial' => $inspector->codigo_credencial,
                                'especialidad' => $inspector->especialidad ?? 'General',
                                'disponible' => (bool) $inspector->disponible,
                                'bloqueado' => !is_null($inspector->user->email_verified_at), // <--- Bandera basada en email_verified_at
                                'toggle_url' => route('director.inspectores.toggle', $inspector->id),
                                'update_url' => route('director.inspectores.update', $inspector->id),
                                'denuncias' => $inspector->denuncias->map(fn($d) => [
                                    'id' => $d->id, 'codigo' => $d->codigo, 'tipo' => $d->tipo_contaminacion ?? 'Ambiental', 'sector' => $d->sector ?? 'N/A', 'estatus' => ucfirst(str_replace('_', ' ', $d->estatus))
                                ])->values(),
                                'solicitudes' => $inspector->solicitudes->map(fn($s) => [
                                    'id' => $s->id, 'codigo' => $s->codigo_expediente, 'tipo' => $s->tipo_tramite ?? 'Trámite', 'sector' => $s->sector ?? 'N/A', 'estatus' => ucfirst(str_replace('_', ' ', $s->estado))
                                ])->values(),
                            ];
                        @endphp
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
                                        type="button"
                                        @click="openDetails({{ json_encode($inspectorData) }})"
                                        class="px-3 py-1.5 bg-slate-100 hover:bg-emerald-50 text-slate-700 hover:text-emerald-700 border border-slate-200 text-xs font-semibold rounded-lg transition-all cursor-pointer"
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

        <!-- MODAL DE DETALLES Y EDICIÓN -->
        <div x-show="modalDetailsOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm">
            <div @click.away="modalDetailsOpen = false" class="bg-white border border-slate-200/80 rounded-3xl w-full max-w-2xl overflow-hidden shadow-2xl p-6 transition-all space-y-4">
                
                <template x-if="selectedInspector">
                    <div class="space-y-4">
                        
                        <!-- Header del Modal -->
                        <div class="flex justify-between items-start pb-3 border-b border-slate-100">
                            <div>
                                <div class="flex items-center gap-2">
                                    <h3 class="text-base font-bold text-slate-900" x-text="isEditing ? 'Editar Inspector' : selectedInspector.nombre"></h3>
                                    <span 
                                        x-show="!isEditing"
                                        :class="selectedInspector.disponible ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-rose-50 text-rose-700 border-rose-200'"
                                        class="px-2.5 py-0.5 text-[10px] font-bold uppercase rounded-full border"
                                        x-text="selectedInspector.disponible ? 'Disponible' : 'No Disponible'"
                                    ></span>
                                </div>
                                <p class="text-xs text-slate-400 font-mono mt-0.5" x-text="isEditing ? 'Modifique los campos permitidos del funcionario' : 'Credencial: ' + selectedInspector.credencial"></p>
                            </div>
                            <button @click="modalDetailsOpen = false" class="text-slate-400 hover:text-slate-600 text-xl font-bold cursor-pointer leading-none">&times;</button>
                        </div>

                        <!-- VISTA NORMAL: DETALLES -->
                        <div x-show="!isEditing" class="space-y-4">
                            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100 grid grid-cols-2 md:grid-cols-4 gap-3 text-xs">
                                <div>
                                    <span class="text-slate-400 block font-semibold text-[10px] uppercase">Cédula</span>
                                    <span class="font-bold text-slate-800" x-text="selectedInspector.cedula"></span>
                                </div>
                                <div>
                                    <span class="text-slate-400 block font-semibold text-[10px] uppercase">Especialidad</span>
                                    <span class="font-bold text-slate-800" x-text="selectedInspector.especialidad"></span>
                                </div>
                                <div>
                                    <span class="text-slate-400 block font-semibold text-[10px] uppercase">Correo</span>
                                    <span class="font-bold text-slate-800 truncate block" x-text="selectedInspector.correo"></span>
                                </div>
                                <div>
                                    <span class="text-slate-400 block font-semibold text-[10px] uppercase">Teléfono</span>
                                    <span class="font-bold text-slate-800" x-text="selectedInspector.telefono"></span>
                                </div>
                            </div>

                            <!-- Pestañas de Casos -->
                            <div class="space-y-3">
                                <div class="flex border-b border-slate-100 gap-2">
                                    <button 
                                        type="button"
                                        @click="activeTab = 'denuncias'" 
                                        :class="activeTab === 'denuncias' ? 'border-emerald-600 text-emerald-700 bg-emerald-50/50' : 'border-transparent text-slate-400 hover:text-slate-600'"
                                        class="py-1.5 px-3 text-xs font-bold border-b-2 rounded-t-lg transition-all flex items-center gap-1.5 cursor-pointer"
                                    >
                                        <span>Denuncias</span>
                                        <span class="px-1.5 py-0.2 rounded-full text-[10px] bg-amber-100 text-amber-800 font-extrabold" x-text="selectedInspector.denuncias.length"></span>
                                    </button>

                                    <button 
                                        type="button"
                                        @click="activeTab = 'solicitudes'" 
                                        :class="activeTab === 'solicitudes' ? 'border-emerald-600 text-emerald-700 bg-emerald-50/50' : 'border-transparent text-slate-400 hover:text-slate-600'"
                                        class="py-1.5 px-3 text-xs font-bold border-b-2 rounded-t-lg transition-all flex items-center gap-1.5 cursor-pointer"
                                    >
                                        <span>Solicitudes</span>
                                        <span class="px-1.5 py-0.2 rounded-full text-[10px] bg-blue-100 text-blue-800 font-extrabold" x-text="selectedInspector.solicitudes.length"></span>
                                    </button>
                                </div>

                                <div x-show="activeTab === 'denuncias'" class="max-h-40 overflow-y-auto space-y-2 pr-1">
                                    <template x-if="selectedInspector.denuncias.length > 0">
                                        <div class="space-y-2">
                                            <template x-for="denuncia in selectedInspector.denuncias" :key="'den_' + denuncia.id">
                                                <div class="p-2.5 bg-slate-50 rounded-xl border border-slate-100 flex justify-between items-center text-xs">
                                                    <div>
                                                        <span class="font-bold text-amber-800" x-text="denuncia.codigo"></span>
                                                        <p class="text-slate-500 text-[11px]" x-text="denuncia.tipo + ' — ' + denuncia.sector"></p>
                                                    </div>
                                                    <span class="px-2 py-0.5 rounded-md bg-white border border-slate-200 font-semibold text-slate-700 text-[10px]" x-text="denuncia.estatus"></span>
                                                </div>
                                            </template>
                                        </div>
                                    </template>
                                    <template x-if="selectedInspector.denuncias.length === 0">
                                        <p class="text-center py-6 text-slate-400 text-xs italic">No posee denuncias asignadas.</p>
                                    </template>
                                </div>

                                <div x-show="activeTab === 'solicitudes'" class="max-h-40 overflow-y-auto space-y-2 pr-1">
                                    <template x-if="selectedInspector.solicitudes.length > 0">
                                        <div class="space-y-2">
                                            <template x-for="solicitud in selectedInspector.solicitudes" :key="'sol_' + solicitud.id">
                                                <div class="p-2.5 bg-slate-50 rounded-xl border border-slate-100 flex justify-between items-center text-xs">
                                                    <div>
                                                        <span class="font-bold text-blue-800" x-text="solicitud.codigo"></span>
                                                        <p class="text-slate-500 text-[11px]" x-text="solicitud.tipo + ' — ' + solicitud.sector"></p>
                                                    </div>
                                                    <span class="px-2 py-0.5 rounded-md bg-white border border-slate-200 font-semibold text-slate-700 text-[10px]" x-text="solicitud.estatus"></span>
                                                </div>
                                            </template>
                                        </div>
                                    </template>
                                    <template x-if="selectedInspector.solicitudes.length === 0">
                                        <p class="text-center py-6 text-slate-400 text-xs italic">No posee solicitudes asignadas.</p>
                                    </template>
                                </div>
                            </div>
                        </div>

                        <!-- VISTA DE EDICIÓN: FORMULARIO -->
                        <form x-show="isEditing" :action="editUpdateUrl" method="POST" class="space-y-4">
                            @csrf
                            @method('PUT')

                            <input type="hidden" name="cedula" :value="editNac + '-' + editCedulaNum">
                            <input type="hidden" name="telefono" :value="editCodTel + '-' + editTelNum">
                            <input type="hidden" name="codigo_credencial" :value="'CZ-INSP-' + editCredencial">

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">Cédula <span class="text-rose-500">*</span></label>
                                    <div class="flex rounded-xl border border-slate-200 overflow-hidden">
                                        <select x-model="editNac" class="bg-slate-50 text-xs font-semibold px-2 py-2 border-r">
                                            <option value="V">V-</option><option value="E">E-</option>
                                        </select>
                                        <input type="text" x-model="editCedulaNum" required class="w-full px-3 py-2 text-xs focus:outline-none">
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">Credencial <span class="text-rose-500">*</span></label>
                                    <div class="flex rounded-xl border border-slate-200 overflow-hidden">
                                        <span class="bg-slate-50 text-[10px] font-mono px-2 py-2 border-r flex items-center text-slate-500">CZ-INSP-</span>
                                        <input type="text" x-model="editCredencial" required class="w-20 px-2 py-2 text-xs font-mono font-semibold text-center focus:outline-none">
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">Nombres <span class="text-rose-500">*</span></label>
                                    <input type="text" name="nombre" x-model="editNombre" required class="w-full border border-slate-200 rounded-xl px-3 py-2 text-xs focus:outline-none">
                                </div>

                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">Apellidos <span class="text-rose-500">*</span></label>
                                    <input type="text" name="apellido" x-model="editApellido" required class="w-full border border-slate-200 rounded-xl px-3 py-2 text-xs focus:outline-none">
                                </div>

                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">Correo Electrónico <span class="text-rose-500">*</span></label>
                                    <input type="email" name="correo" x-model="editCorreo" required class="w-full border border-slate-200 rounded-xl px-3 py-2 text-xs focus:outline-none">
                                </div>

                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">Teléfono <span class="text-rose-500">*</span></label>
                                    <div class="flex rounded-xl border border-slate-200 overflow-hidden">
                                        <select x-model="editCodTel" class="bg-slate-50 text-xs font-semibold px-2 py-2 border-r">
                                            <option value="0412">0412</option><option value="0414">0414</option><option value="0424">0424</option><option value="0416">0416</option><option value="0426">0426</option>
                                        </select>
                                        <input type="text" x-model="editTelNum" required class="w-full px-3 py-2 text-xs focus:outline-none">
                                    </div>
                                </div>

                                <div class="md:col-span-2">
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">Especialidad <span class="text-rose-500">*</span></label>
                                    <input type="text" name="especialidad" x-model="editEspecialidad" required class="w-full border border-slate-200 rounded-xl px-3 py-2 text-xs focus:outline-none">
                                </div>
                            </div>

                            <div class="flex justify-end gap-2 pt-3 border-t border-slate-100">
                                <button type="button" @click="isEditing = false" class="px-3 py-1.5 bg-slate-100 text-slate-600 text-xs font-semibold rounded-xl hover:bg-slate-200 cursor-pointer">Cancelar</button>
                                <button type="submit" class="px-4 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-xl shadow-sm cursor-pointer">Guardar Cambios</button>
                            </div>
                        </form>

                        <!-- Footer del Modal -->
                        <div x-show="!isEditing" class="flex justify-between items-center pt-3 border-t border-slate-100">
                            <div class="flex items-center gap-2">
                                <form :action="selectedInspector.toggle_url" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button 
                                        type="submit" 
                                        :class="selectedInspector.disponible ? 'bg-amber-50 text-amber-700 hover:bg-amber-100 border-amber-200' : 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100 border-emerald-200'"
                                        class="px-3 py-1.5 text-xs font-semibold rounded-xl border transition-colors cursor-pointer"
                                        x-text="selectedInspector.disponible ? 'Marcar NO Disponible' : 'Marcar Disponible'"
                                    ></button>
                                </form>

                                <!-- Botón de edición condicional (Se oculta y muestra aviso si ya está bloqueado) -->
                                <template x-if="!selectedInspector.bloqueado">
                                    <button 
                                        type="button" 
                                        @click="initEdit()"
                                        class="px-3 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-700 border border-blue-200 text-xs font-semibold rounded-xl transition-colors cursor-pointer"
                                    >
                                        Editar Datos
                                    </button>
                                </template>
                                <template x-if="selectedInspector.bloqueado">
                                    <span class="text-[11px] font-medium text-amber-600 bg-amber-50 px-2.5 py-1 rounded-lg border border-amber-200">
                                        🔒 Edición bloqueada (Cuenta activada)
                                    </span>
                                </template>
                            </div>

                            <button type="button" @click="modalDetailsOpen = false" class="px-4 py-1.5 bg-slate-100 text-slate-600 text-xs font-semibold rounded-xl hover:bg-slate-200 transition-colors cursor-pointer">Cerrar</button>
                        </div>

                    </div>
                </template>

            </div>
        </div>

        <!-- MODAL DE REGISTRO -->
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
                        <button type="button" @click="modalRegisterOpen = false" class="px-4 py-2 bg-slate-100 text-slate-600 text-xs font-semibold rounded-xl hover:bg-slate-200 transition-colors cursor-pointer">Cancelar sus</button>
                        <button type="submit" :disabled="!isFormValid()" :class="isFormValid() ? 'bg-emerald-600 hover:bg-emerald-700 text-white cursor-pointer shadow-sm' : 'bg-slate-200 text-slate-400 cursor-not-allowed opacity-60'" class="px-5 py-2 text-xs font-semibold rounded-xl transition-all">
                            Registrar y Enviar Credenciales
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-layouts.app>