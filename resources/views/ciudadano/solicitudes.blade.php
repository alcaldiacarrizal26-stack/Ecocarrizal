<x-layouts.app>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Gestión de Solicitudes y Trámites') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="solicitudForm()">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Alertas de éxito -->
            @if (session('success'))
                <div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400" role="alert">
                    <span class="font-medium">¡Éxito!</span> {{ session('success') }}
                </div>
            @endif

            <!-- Encabezado y Botón de Nueva Solicitud -->
            <div class="flex justify-between items-center bg-white p-6 rounded-lg shadow-sm">
                <div>
                    <h3 class="text-lg font-bold text-gray-900">Mis Solicitudes Registradas</h3>
                    <p class="text-sm text-gray-600">Consulta el estado de tus trámites o inicia una nueva solicitud ambiental.</p>
                </div>
                <button 
                    @click="openModal = true" 
                    class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg shadow transition ease-in-out duration-150">
                    + Nueva Solicitud
                </button>
            </div>

            <!-- Tabla de Solicitudes del Usuario -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if ($tramites->isEmpty())
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <p class="mt-2 text-sm text-gray-500">No tienes solicitudes registradas hasta el momento.</p>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Código</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trámite</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ubicación</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estatus</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($tramites as $tramite)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">#{{ $tramite->id }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $tramite->tipo_tramite }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $tramite->comuna }} - {{ $tramite->sector }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $tramite->created_at->format('d/m/Y') }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                    {{ $tramite->estatus ?? 'En Revisión' }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

            <!-- MODAL: Formulario de Nueva Solicitud -->
            <div x-show="openModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
                <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                    
                    <div @click="openModal = false" class="fixed inset-0 transition-opacity" aria-hidden="true">
                        <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                    </div>

                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                    <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-3xl sm:w-full">
                        <form action="{{ route('ciudadano.solicitudes.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            
                            <div class="bg-white px-6 pt-5 pb-4 sm:p-6 sm:pb-4">
                                <div class="flex justify-between items-center pb-3 border-b mb-4">
                                    <h3 class="text-lg font-semibold text-gray-900">Crear Nueva Solicitud</h3>
                                    <button type="button" @click="openModal = false" class="text-gray-400 hover:text-gray-600">&times;</button>
                                </div>

                                <div class="space-y-4">
                                    <!-- Selector de Tipo de Trámite -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Tipo de Trámite / Solicitud</label>
                                        <select name="tipo_tramite" x-model="selectedTramiteKey" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" required>
                                            <option value="">-- Seleccione un Trámite --</option>
                                            <template x-for="(tramite, key) in tiposTramites" :key="key">
                                                <option :value="key" x-text="tramite.nombre"></option>
                                            </template>
                                        </select>
                                    </div>

                                    <!-- Panel de Requisitos Dinámicos -->
                                    <template x-if="selectedTramiteKey && tiposTramites[selectedTramiteKey]">
                                        <div class="p-4 bg-blue-50 border-l-4 border-blue-500 rounded-r-md">
                                            <h4 class="text-sm font-semibold text-blue-900 mb-1" x-text="tiposTramites[selectedTramiteKey].nombre"></h4>
                                            <p class="text-xs text-blue-700 mb-2" x-text="tiposTramites[selectedTramiteKey].descripcion"></p>
                                            <p class="text-xs font-semibold text-blue-900 mb-1">Requisitos exigidos para este trámite:</p>
                                            <ul class="list-disc list-inside text-xs text-blue-800 space-y-1">
                                                <template x-for="req in tiposTramites[selectedTramiteKey].requisitos" :key="req">
                                                    <li x-text="req"></li>
                                                </template>
                                            </ul>
                                        </div>
                                    </template>

                                    <!-- Fila Territorial: Comuna / Circuito y Sector -->
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Comuna / Circuito</label>
                                            <select name="comuna" x-model="selectedComuna" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" required>
                                                <option value="">-- Seleccione Comuna/Circuito --</option>
                                                <template x-for="(sectores, comuna) in territorios" :key="comuna">
                                                    <option :value="comuna" x-text="comuna"></option>
                                                </template>
                                            </select>
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Sector</label>
                                            <select name="sector" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" :disabled="!selectedComuna" required>
                                                <option value="">-- Seleccione Sector --</option>
                                                <template x-for="sector in sectoresDisponibles" :key="sector">
                                                    <option :value="sector" x-text="sector"></option>
                                                </template>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Dirección Exacta -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Dirección Exacta o Punto de Referencia</label>
                                        <input type="text" name="direccion_exacta" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" placeholder="Ej. Calle Principal, Casa Nro. 12, frente al abasto..." required>
                                    </div>

                                    <!-- Motivo / Descripción -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Exposición de Motivos / Detalles de la Solicitud</label>
                                        <textarea name="motivo_solicitud" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" placeholder="Describa brevemente los detalles o la justificación de su solicitud..." required></textarea>
                                    </div>

                                    <!-- Carga de Archivos / Documentos -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Adjuntar Documentos / Requisitos</label>
                                        <p class="text-xs text-gray-500 mb-1">Cargue los archivos requeridos (Formatos: PDF, PNG, JPG - Máx 10MB por archivo).</p>
                                        <input type="file" name="documentos[]" multiple class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" required>
                                    </div>
                                </div>
                            </div>

                            <!-- Botones del Modal -->
                            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-2">
                                <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none sm:w-auto sm:text-sm">
                                    Enviar Solicitud
                                </button>
                                <button type="button" @click="openModal = false" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:w-auto sm:text-sm">
                                    Cancelar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Script de Estado de Alpine.js -->
    <script>
        function solicitudForm() {
            return {
                openModal: false,
                territorios: @json($territorios),
                tiposTramites: @json($tiposTramites),
                selectedComuna: '',
                selectedTramiteKey: '',
                
                get sectoresDisponibles() {
                    return this.selectedComuna && this.territorios[this.selectedComuna] 
                        ? this.territorios[this.selectedComuna] 
                        : [];
                }
            }
        }
    </script>
</x-layouts.app>