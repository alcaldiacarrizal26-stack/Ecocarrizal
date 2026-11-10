<x-layouts.app title="Gestión de Solicitudes Ambientales" active="ambiente_solicitudes">
<div 
    class="p-6 max-w-7xl mx-auto space-y-6" 
    x-data="{ 
        modalOpen: false, 
        modalAsignarOpen: false, 
        modalRechazarSolicitudOpen: false,
        solicitudActual: null,

        tiposTramites: {
            'inspeccion': {
                nombre: 'Solicitud de Inspección TÉCNICA',
                requisitosObligatorios: [{ key: 'cedula', label: 'Copia de la Cédula de Identidad' }],
                requisitosCondicionales: [],
                requisitosOpcionales: []
            },
            'poda_deshierbe': {
                nombre: 'Solicitud de Permiso para Poda o Deshierbe',
                requisitosObligatorios: [
                    { key: 'cedula', label: 'Copia de la Cédula de Identidad' },
                    { key: 'carta_explicativa', label: 'Carta Explicativa del Trabajo a Realizar' }
                ],
                requisitosCondicionales: [
                    { key: 'autorizacion_propietario', label: 'Autorización del Propietario' },
                    { key: 'variables_ambientales', label: 'Variables Ambientales (MINEC)' },
                    { key: 'registro_mercantil', label: 'Copia del Registro Mercantil' },
                    { key: 'rif_empresa', label: 'Copia del RIF Vigente de la Empresa' }
                ],
                requisitosOpcionales: [
                    { key: 'doc_propiedad', label: 'Documento de Propiedad Registrado' },
                    { key: 'plano_ubicacion', label: 'Plano de Ubicación del Terreno' },
                    { key: 'solvencia_municipal', label: 'Solvencia Municipal Vigente' },
                    { key: 'constancia_catastral', label: 'Constancia Catastral Vigente' }
                ]
            },
            'tala': {
                nombre: 'Solicitud de Permiso para Tala de Árboles',
                requisitosObligatorios: [
                    { key: 'cedula', label: 'Copia de la Cédula de Identidad' },
                    { key: 'carta_explicativa', label: 'Carta Justificativa de Tala' },
                    { key: 'carta_compromiso', label: 'Carta de Compromiso de Compensación Ambiental' }
                ],
                requisitosCondicionales: [
                    { key: 'autorizacion_propietario', label: 'Autorización del Propietario' },
                    { key: 'variables_ambientales', label: 'Variables Ambientales (MINEC)' },
                    { key: 'registro_mercantil', label: 'Copia del Registro Mercantil' },
                    { key: 'rif_empresa', label: 'Copia del RIF Vigente de la Empresa' }
                ],
                requisitosOpcionales: [
                    { key: 'doc_propiedad', label: 'Documento de Propiedad Registrado' },
                    { key: 'plano_ubicacion', label: 'Plano de Ubicación del Terreno' },
                    { key: 'solvencia_municipal', label: 'Solvencia Municipal Vigente' },
                    { key: 'constancia_catastral', label: 'Constancia Catastral Vigente' }
                ]
            },
            'desmalezamiento_conformacion': {
                nombre: 'Permiso de Desmalezamiento y Conformación de Terreno',
                requisitosObligatorios: [
                    { key: 'cedula', label: 'Copia de la Cédula de Identidad' },
                    { key: 'carta_explicativa', label: 'Carta Explicativa del Trabajo a Realizar' }
                ],
                requisitosCondicionales: [
                    { key: 'autorizacion_propietario', label: 'Autorización del Propietario' },
                    { key: 'variables_ambientales', label: 'Variables Ambientales (MINEC)' },
                    { key: 'registro_mercantil', label: 'Copia del Registro Mercantil' },
                    { key: 'rif_empresa', label: 'Copia del RIF Vigente de la Empresa' }
                ],
                requisitosOpcionales: [
                    { key: 'doc_propiedad', label: 'Documento de Propiedad Registrado' },
                    { key: 'plano_ubicacion', label: 'Plano de Ubicación del Terreno' },
                    { key: 'solvencia_municipal', label: 'Solvencia Municipal Vigente' },
                    { key: 'constancia_catastral', label: 'Constancia Catastral Vigente' },
                    { key: 'carta_compromiso', label: 'Carta de Compromiso de Reposición' }
                ]
            }
        },

        get datosEspecificosModal() {
            if (!this.solicitudActual || !this.solicitudActual.datos_especificos) return {};
            let datos = this.solicitudActual.datos_especificos;
            if (typeof datos === 'string') {
                try { datos = JSON.parse(datos); } catch(e) { datos = {}; }
            }
            return datos || {};
        },

        evaluarBool(valor) {
            if (typeof valor === 'boolean') return valor;
            if (typeof valor === 'number') return valor === 1;
            if (typeof valor === 'string') {
                const v = valor.trim().toLowerCase();
                return v === '1' || v === 'true';
            }
            return Boolean(valor);
        },

        obtenerEtiquetaRecaudo(recaudo) {
            if (!this.solicitudActual) return recaudo.nombre_archivo || recaudo.tipo_recaudo;
            const config = this.tiposTramites[this.solicitudActual.tipo_tramite];
            if (!config) return recaudo.nombre_archivo || recaudo.tipo_recaudo;

            const todosRequisitos = [
                ...(config.requisitosObligatorios || []),
                ...(config.requisitosCondicionales || []),
                ...(config.requisitosOpcionales || [])
            ];

            const reqConfig = todosRequisitos.find(r => r.key === recaudo.tipo_recaudo);
            return reqConfig ? reqConfig.label : (recaudo.nombre_archivo || recaudo.tipo_recaudo);
        },

        puedeReasignar(estado) {
            return ['pendiente', 'inspector_asignado', 'requiere_reinspeccion'].includes(estado);
        }
    }"
>

    <!-- ALERTAS DE NOTIFICACIÓN -->
    @if(session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-xl flex items-center justify-between text-emerald-800 text-sm shadow-sm" role="alert">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>{{ session('success') }}</span>
            </div>
            <button type="button" @click="$el.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="p-4 bg-rose-50 border border-rose-200 rounded-xl flex items-center justify-between text-rose-800 text-sm shadow-sm" role="alert">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2 text-rose-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>{{ session('error') }}</span>
            </div>
            <button type="button" @click="$el.parentElement.remove()" class="text-rose-500 hover:text-rose-700">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
    @endif

    <!-- Encabezado -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Solicitudes de Trámites Ambientales</h1>
            <p class="text-sm text-slate-500">Evaluación de expediente, validación de recaudos y asignación técnica</p>
        </div>

        <a href="{{ route('director.ambiente.solicitudes.exportar') }}" class="inline-flex items-center justify-center px-4 py-2 bg-slate-800 text-white text-xs font-medium rounded-xl hover:bg-slate-700 transition self-start md:self-auto shadow-sm">
            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            Exportar Listado
        </a>
    </div>

    <!-- Cards de Estadísticas -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase">Total Solicitudes</p>
                <p class="text-2xl font-bold text-slate-800 mt-1">{{ number_format($stats['total_solicitudes'] ?? 0) }}</p>
            </div>
            <div class="p-2.5 bg-slate-100 text-slate-600 rounded-xl">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            </div>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-amber-600 uppercase">Doc. Incompleta</p>
                <p class="text-2xl font-bold text-amber-600 mt-1">
                    {{ number_format($stats['incompleta'] ?? $stats['documentos_pendientes'] ?? 0) }}
                </p>
            </div>
            <div class="p-2.5 bg-amber-50 text-amber-600 rounded-xl">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
            </div>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-indigo-600 uppercase">En Inspección</p>
                <p class="text-2xl font-bold text-indigo-600 mt-1">{{ number_format($stats['en_inspeccion'] ?? 0) }}</p>
            </div>
            <div class="p-2.5 bg-indigo-50 text-indigo-600 rounded-xl">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-emerald-600 uppercase">Aprobadas</p>
                <p class="text-2xl font-bold text-emerald-600 mt-1">{{ number_format($stats['aprobada'] ?? $stats['aprobadas'] ?? 0) }}</p>
            </div>
            <div class="p-2.5 bg-emerald-50 text-emerald-600 rounded-xl">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            </div>
        </div>
    </div>

    <!-- Barra de Filtro Adaptativa para Solicitudes -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm">
        <form method="GET" action="{{ route('director.ambiente.solicitudes') }}" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
            <div class="flex items-center justify-between sm:justify-start gap-2 flex-1">
                <label for="filtro_estado" class="text-xs font-bold text-slate-500 uppercase tracking-wider whitespace-nowrap">Filtrar por estado:</label>
                <select 
                    id="filtro_estado" 
                    name="estado" 
                    class="bg-slate-50/50 border border-slate-200 text-slate-800 text-xs rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 block p-2.5 font-medium cursor-pointer w-full sm:w-auto"
                >
                    <option value="todos" {{ request('estado') == 'todos' || !request('estado') ? 'selected' : '' }}>Todos los estados</option>
                    <option value="incompleta" {{ request('estado') == 'incompleta' ? 'selected' : '' }}>Documentación incompleta</option>
                    <option value="pendiente" {{ request('estado') == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                    <option value="inspector_asignado" {{ request('estado') == 'inspector_asignado' ? 'selected' : '' }}>Inspector asignado</option>
                    <option value="en_inspeccion" {{ request('estado') == 'en_inspeccion' ? 'selected' : '' }}>En inspección</option>
                    <option value="aprobada" {{ request('estado') == 'aprobada' ? 'selected' : '' }}>Aprobada</option>
                    <option value="rechazada" {{ request('estado') == 'rechazada' ? 'selected' : '' }}>Rechazada</option>
                </select>
            </div>

            <div class="flex items-center gap-2">
                <button type="submit" class="w-full sm:w-auto px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl transition shadow-sm cursor-pointer text-center">
                    Filtrar Resultados
                </button>
                @if(request('estado') && request('estado') !== 'todos')
                    <a href="{{ route('director.ambiente.solicitudes') }}" class="px-3 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-bold rounded-xl transition text-center">
                        Limpiar
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- TABLA DE SOLICITUDES -->
    <div class="bg-white rounded-2xl border border-gray-200 shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th scope="col" class="px-6 py-3 whitespace-nowrap">Expediente</th>
                        <th scope="col" class="px-6 py-3 whitespace-nowrap">Tipo de Trámite</th>
                        <th scope="col" class="px-6 py-3 whitespace-nowrap">Solicitante</th>
                        <th scope="col" class="px-6 py-3 whitespace-nowrap">Estado del Flujo</th>
                        <th scope="col" class="px-6 py-3 whitespace-nowrap">Inspector Asignado</th>
                        <th scope="col" class="px-6 py-3 whitespace-nowrap">Fecha</th>
                        <th scope="col" class="px-6 py-3 text-right whitespace-nowrap">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($solicitudes as $solicitud)
                        @php
                            $userSolicitante = $solicitud->solicitante ?? $solicitud->user;
                        @endphp
                        <tr class="bg-white border-b hover:bg-gray-50 transition">
                            <td class="px-6 py-4 font-bold text-emerald-700 whitespace-nowrap">
                                {{ $solicitud->codigo_expediente }}
                            </td>
                            <td class="px-6 py-4 font-medium text-gray-800 whitespace-nowrap capitalize">
                                {{ str_replace('_', ' ', $solicitud->tipo_tramite) }}
                            </td>
                            <td class="px-6 py-4 max-w-[200px]">
                                <div class="font-medium text-gray-800 truncate">{{ $userSolicitante->nombre ?? 'N/A' }} {{ $userSolicitante->apellido ?? '' }}</div>
                                <div class="text-xs text-gray-400 truncate">{{ $userSolicitante->cedula ?? $userSolicitante->email ?? 'Sin dato' }}</div>
                            </td>

                            <!-- Badges del Estado -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                @switch($solicitud->estado)
                                    @case('incompleta')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-bold" style="background-color: #FEF3C7; color: #92400E; border: 1px solid #FDE68A;">
                                            Documentación incompleta
                                        </span>
                                        @break
                                    @case('pendiente')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold" style="background-color: #EFF6FF; color: #1E40AF; border: 1px solid #BFDBFE;">
                                            Pendiente
                                        </span>
                                        @break
                                    @case('inspector_asignado')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold" style="background-color: #EEF2FF; color: #3730A3; border: 1px solid #C7D2FE;">
                                            Inspector asignado
                                        </span>
                                        @break
                                    @case('en_inspeccion')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold" style="background-color: #F3E8FF; color: #6B21A8; border: 1px solid #E9D5FF;">
                                            En inspección
                                        </span>
                                        @break
                                    @case('aprobada')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold" style="background-color: #D1FAE5; color: #065F46; border: 1px solid #A7F3D0;">
                                            Aprobada
                                        </span>
                                        @break
                                    @case('rechazada')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold" style="background-color: #FFE4E6; color: #9F1239; border: 1px solid #FECDD3;">
                                            Rechazada
                                        </span>
                                        @break
                                    @default
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium" style="background-color: #F1F5F9; color: #334155;">
                                            {{ str_replace('_', ' ', $solicitud->estado) }}
                                        </span>
                                @endswitch
                            </td>

                            <!-- Inspector Asignado -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($solicitud->inspector)
                                    <div class="flex items-center gap-1.5 text-xs text-indigo-700 bg-indigo-50 border border-indigo-100 px-2.5 py-1 rounded-md w-fit font-medium">
                                        <svg class="w-3.5 h-3.5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                        <span>{{ $solicitud->inspector->nombre }} {{ $solicitud->inspector->apellido }}</span>
                                    </div>
                                @else
                                    <span class="text-xs text-gray-400 italic">Sin Asignar</span>
                                @endif
                            </td>

                            <td class="px-6 py-4 text-xs text-gray-500 whitespace-nowrap">
                                {{ $solicitud->created_at ? $solicitud->created_at->format('d/m/Y h:i A') : 'N/A' }}
                            </td>

                            <!-- ACCIONES CON RESTRICCIÓN DE RECHAZO SOLO PARA PENDIENTES -->
                            <td class="px-6 py-4 text-right whitespace-nowrap space-x-1">
                                <template x-if="puedeReasignar('{{ $solicitud->estado }}')">
                                    <button 
                                        type="button"
                                        title="{{ $solicitud->inspector ? 'Reasignar Inspector' : 'Asignar Inspector' }}"
                                        @click="solicitudActual = {{ json_encode($solicitud->load(['usuario', 'inspector', 'recaudos'])) }}; modalAsignarOpen = true"
                                        class="p-2 text-indigo-600 hover:text-indigo-900 bg-indigo-50 hover:bg-indigo-100 rounded-xl transition-colors inline-flex items-center cursor-pointer shadow-sm"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                                    </button>
                                </template>

                                <button 
                                    type="button"
                                    title="Ver Detalles"
                                    @click="solicitudActual = {{ json_encode($solicitud->load(['usuario', 'inspector', 'recaudos'])) }}; modalOpen = true"
                                    class="p-2 text-emerald-600 hover:text-emerald-900 bg-emerald-50 hover:bg-emerald-100 rounded-xl transition-colors inline-flex items-center cursor-pointer shadow-sm"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </button>

                                <!-- BOTÓN DE RECHAZO SOLO SI EL ESTADO ES 'pendiente' -->
                                <template x-if="'{{ $solicitud->estado }}' === 'pendiente'">
                                    <button 
                                        type="button"
                                        title="Rechazar Expediente"
                                        @click="solicitudActual = {{ json_encode($solicitud) }}; modalRechazarSolicitudOpen = true"
                                        class="p-2 text-rose-600 hover:text-rose-900 bg-rose-50 hover:bg-rose-100 rounded-xl transition-colors inline-flex items-center cursor-pointer shadow-sm"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path></svg>
                                    </button>
                                </template>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                                No se encontraron solicitudes registradas.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(method_exists($solicitudes, 'links'))
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $solicitudes->withQueryString()->links() }}
            </div>
        @endif
    </div>

    <!-- MODAL DE DETALLES DE LA SOLICITUD -->
    <div 
        x-show="modalOpen" 
        x-cloak
        class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/50 backdrop-blur-sm flex items-center justify-center p-4"
    >
        <div 
            @click.away="modalOpen = false"
            class="bg-white rounded-xl shadow-xl max-w-2xl w-full overflow-hidden transform transition-all border border-gray-100"
        >
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200 flex justify-between items-center">
                <div>
                    <h3 class="text-lg font-bold text-gray-800">
                        Expediente: <span class="text-emerald-600" x-text="solicitudActual?.codigo_expediente"></span>
                    </h3>
                    <p class="text-xs text-gray-500" x-text="'Fecha de creación: ' + (solicitudActual?.created_at ? new Date(solicitudActual.created_at).toLocaleString() : '')"></p>
                </div>
                <button @click="modalOpen = false" class="text-gray-400 hover:text-gray-600 focus:outline-none">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <div class="p-6 space-y-4 max-h-[75vh] overflow-y-auto">
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-gray-50 p-3 rounded-lg border border-gray-100">
                        <span class="block text-xs text-gray-400 uppercase font-semibold">Tipo de Trámite</span>
                        <span class="text-sm font-medium text-gray-800 capitalize" x-text="solicitudActual?.tipo_tramite?.replace(/_/g, ' ')"></span>
                    </div>
                    <div class="bg-gray-50 p-3 rounded-lg border border-gray-100">
                        <span class="block text-xs text-gray-400 uppercase font-semibold">Estado de la Solicitud</span>
                        <span class="text-sm font-bold text-slate-800 capitalize" x-text="solicitudActual?.estado?.replace(/_/g, ' ')"></span>
                    </div>
                </div>

                <template x-if="solicitudActual?.estado === 'incompleta'">
                    <div class="bg-amber-50 p-3.5 rounded-xl border border-amber-200 flex items-start gap-3">
                        <svg class="w-5 h-5 text-amber-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                        <div class="text-xs text-amber-900">
                            <strong class="font-bold block mb-0.5">Asignación bloqueada: Documentación incompleta</strong>
                            El ciudadano debe consignar los documentos o requisitos faltantes antes de poder asignar un inspector técnico a este expediente.
                        </div>
                    </div>
                </template>

                <div class="bg-indigo-50/60 p-3 rounded-lg border border-indigo-100 flex items-center justify-between">
                    <div>
                        <span class="block text-xs text-indigo-600 uppercase font-bold">Inspector Técnico Asignado</span>
                        <span class="text-sm font-medium text-indigo-950" 
                              x-text="solicitudActual?.inspector ? (solicitudActual.inspector.nombre + ' ' + (solicitudActual.inspector.apellido || '')).trim() : 'Ninguno (Sin asignar)'">
                        </span>
                    </div>
                    <span class="text-xs px-2.5 py-1 rounded-md bg-white border border-indigo-200 text-indigo-700 capitalize font-medium" x-text="solicitudActual?.estado?.replace(/_/g, ' ') || 'Sin Estatus'"></span>
                </div>

                <div class="bg-slate-50 p-3.5 rounded-xl border border-slate-200 space-y-2">
                    <span class="block text-xs text-slate-500 uppercase font-bold">Condiciones Específicas del Expediente</span>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 text-xs">
                        <div class="p-2 bg-white rounded-lg border border-slate-200 flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full" :class="evaluarBool(datosEspecificosModal?.aplica_autorizacion_propietario) ? 'bg-amber-500' : 'bg-emerald-500'"></span>
                            <span class="font-medium text-slate-700" x-text="evaluarBool(datosEspecificosModal?.aplica_autorizacion_propietario) ? 'Requiere Autorización de Propietario' : 'Es Propietario Directo'"></span>
                        </div>

                        <div class="p-2 bg-white rounded-lg border border-slate-200 flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full" :class="evaluarBool(datosEspecificosModal?.aplica_variables_ambientales) ? 'bg-indigo-500' : 'bg-slate-300'"></span>
                            <span class="font-medium text-slate-700" x-text="evaluarBool(datosEspecificosModal?.aplica_variables_ambientales) ? 'Aplica MINEC / Zona Protectora' : 'No Aplica MINEC'"></span>
                        </div>

                        <div class="p-2 bg-white rounded-lg border border-slate-200 flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full" :class="evaluarBool(datosEspecificosModal?.es_persona_juridica) ? 'bg-sky-500' : 'bg-slate-300'"></span>
                            <span class="font-medium text-slate-700" x-text="evaluarBool(datosEspecificosModal?.es_persona_juridica) ? 'Persona Jurídica (Empresa)' : 'Persona Natural'"></span>
                        </div>
                    </div>
                </div>

                <div>
                    <span class="block text-xs text-gray-400 uppercase font-semibold mb-1">Datos del Solicitante</span>
                    <p class="text-sm text-gray-700 bg-gray-50 p-3 rounded-lg border border-gray-100">
                        <strong x-text="(solicitudActual?.solicitante?.nombre || solicitudActual?.user?.nombre || '') + ' ' + (solicitudActual?.solicitante?.apellido || solicitudActual?.user?.apellido || '')"></strong><br>
                        <span class="text-xs text-gray-500" x-text="'Cédula/RIF: ' + (solicitudActual?.solicitante?.cedula || solicitudActual?.user?.cedula || 'N/A') + ' | Correo: ' + (solicitudActual?.solicitante?.email || solicitudActual?.user?.email || 'N/A')"></span>
                    </p>
                </div>

                <div>
                    <span class="block text-xs text-gray-400 uppercase font-semibold mb-1">Ubicación de la Actividad / Sector</span>
                    <p class="text-sm text-gray-700 bg-gray-50 p-3 rounded-lg border border-gray-100">
                        <strong x-text="solicitudActual?.sector || 'Sector no especificado'"></strong> (<span x-text="solicitudActual?.comuna || 'Sin comuna'"></span>)<br>
                        <span class="text-xs text-gray-500" x-text="solicitudActual?.direccion_exacta || 'Sin dirección de referencia adicional'"></span>
                    </p>
                </div>

                <template x-if="solicitudActual?.motivo_descripcion">
                    <div>
                        <span class="block text-xs text-gray-400 uppercase font-semibold mb-1">Descripción del Motivo</span>
                        <p class="text-sm text-gray-700 bg-gray-50 p-3 rounded-lg border border-gray-100 leading-relaxed whitespace-pre-line" x-text="solicitudActual.motivo_descripcion"></p>
                    </div>
                </template>

                <template x-if="solicitudActual?.recaudos && solicitudActual.recaudos.length">
                    <div>
                        <span class="block text-xs text-gray-400 uppercase font-semibold mb-2">Recaudos Consignados</span>
                        <div class="space-y-2">
                            <template x-for="(rec, idx) in solicitudActual.recaudos" :key="idx">
                                <div class="flex items-center justify-between p-2.5 bg-slate-50 rounded-xl border border-slate-200 text-xs">
                                    <div class="flex items-center gap-2 truncate pr-2">
                                        <svg class="w-4 h-4 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                        <div class="truncate">
                                            <p class="font-bold text-slate-800 truncate" x-text="obtenerEtiquetaRecaudo(rec)"></p>
                                            <p class="text-[11px] text-slate-500 truncate" x-text="rec.nombre_archivo || 'Archivo adjunto'"></p>
                                        </div>
                                    </div>
                                    <a :href="rec.archivo_url || ('/storage/' + rec.bucket_path)" target="_blank" class="text-emerald-600 hover:text-emerald-800 font-semibold text-xs hover:underline flex-shrink-0">
                                        Ver Documento
                                    </a>
                                </div>
                            </template>
                        </div>
                    </div>
                </template>
            </div>

            <div class="px-6 py-3 bg-gray-50 border-t border-gray-200 flex justify-between items-center">
                <template x-if="puedeReasignar(solicitudActual?.estado)">
                    <button 
                        @click="modalOpen = false; modalAsignarOpen = true"
                        class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-medium rounded-lg transition-colors inline-flex items-center shadow-sm cursor-pointer"
                    >
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                        <span x-text="solicitudActual?.inspector ? 'Reasignar Inspector' : 'Asignar Inspector'"></span>
                    </button>
                </template>

                <template x-if="!puedeReasignar(solicitudActual?.estado)">
                    <span class="text-xs text-slate-400 italic">Asignación no disponible para este estado</span>
                </template>

                <button @click="modalOpen = false" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 text-xs font-medium rounded-lg transition-colors cursor-pointer">
                    Cerrar
                </button>
            </div>
        </div>
    </div>

    <!-- MODAL DE ASIGNAR / REASIGNAR INSPECTOR -->
    <div 
        x-show="modalAsignarOpen" 
        x-cloak
        class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/50 backdrop-blur-sm flex items-center justify-center p-4"
    >
        <div 
            @click.away="modalAsignarOpen = false"
            class="bg-white rounded-xl shadow-xl max-w-md w-full overflow-hidden transform transition-all border border-gray-100"
        >
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200 flex justify-between items-center">
                <h3 class="text-base font-bold text-gray-800">
                    <span x-text="solicitudActual?.inspector ? 'Reasignar Inspector' : 'Asignar Inspector'"></span> al Expediente 
                    <span class="text-indigo-600" x-text="solicitudActual?.codigo_expediente"></span>
                </h3>
                <button @click="modalAsignarOpen = false" class="text-gray-400 hover:text-gray-600 focus:outline-none">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <form :action="'/director/ambiente/solicitudes/' + solicitudActual?.id + '/asignar'" method="POST" class="p-6 space-y-4">
                @csrf
                @method('PUT')

                <div class="bg-gray-50 p-3 rounded-lg border border-gray-200 text-xs text-gray-600 space-y-1">
                    <div class="flex justify-between">
                        <span>Inspector Actual:</span>
                        <strong class="text-indigo-600" 
                                x-text="solicitudActual?.inspector ? (solicitudActual.inspector.nombre + ' ' + (solicitudActual.inspector.apellido || '')).trim() : 'Ninguno (Sin asignar)'">
                        </strong>
                    </div>
                </div>

                <div>
                    <label for="user_id_solicitud" class="block text-xs font-semibold text-gray-700 uppercase mb-2">Seleccionar Inspector</label>
                    <select name="user_id" id="user_id_solicitud" class="w-full bg-white border border-slate-200 text-slate-800 text-xs rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 block p-2.5 font-medium cursor-pointer" required>
                        <option value="" disabled selected>Seleccione un inspector...</option>
                        @foreach ($funcionarios as $funcionario)
                            <option value="{{ $funcionario->id }}">
                                {{ $funcionario->nombre }} {{ $funcionario->apellido }} — {{ ucfirst($funcionario->role->nombre ?? 'Funcionario') }}
                            </option>
                        @endforeach
                    </select>

                    @error('user_id')
                        <p class="text-rose-500 text-xs mt-1.5 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" @click="modalAsignarOpen = false" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 text-xs font-medium rounded-lg transition-colors cursor-pointer">
                        Cancelar
                    </button>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-medium rounded-lg transition-colors shadow-sm cursor-pointer">
                        Confirmar Asignación
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL RECHAZAR SOLICITUD -->
    <div x-show="modalRechazarSolicitudOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/50 backdrop-blur-sm">
        <div @click.away="modalRechazarSolicitudOpen = false" class="bg-white rounded-xl shadow-xl max-w-md w-full p-6 space-y-4">
            <h3 class="text-base font-bold text-gray-800">Rechazar Expediente: <span class="text-rose-600" x-text="solicitudActual?.codigo_expediente"></span></h3>
            <form :action="'/director/ambiente/solicitudes/' + solicitudActual?.id + '/rechazar'" method="POST" class="space-y-3">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Motivo del Rechazo / Improcedencia <span class="text-rose-500">*</span></label>
                    <textarea name="motivo_rechazo" rows="3" class="w-full border border-slate-200 rounded-xl p-2.5 text-xs focus:ring-rose-500 focus:border-rose-500" placeholder="Indique la razón legal o técnica del rechazo..." required></textarea>
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" @click="modalRechazarSolicitudOpen = false" class="px-3 py-1.5 bg-gray-200 text-gray-800 text-xs rounded-lg cursor-pointer">Cancelar</button>
                    <button type="submit" class="px-3 py-1.5 bg-rose-600 hover:bg-rose-700 text-white text-xs font-semibold rounded-lg cursor-pointer">Confirmar Rechazo</button>
                </div>
            </form>
        </div>
    </div>

</div>
</x-layouts.app>