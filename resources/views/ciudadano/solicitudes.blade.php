<x-layouts.app title="Solicitudes - Panel Ciudadano" active="solicitudes">
    <div 
        class="max-w-7xl mx-auto space-y-8" 
        x-data="{ 
            openModalCrear: false, 
            openModalDetalle: false,
            showSuccessAlert: true,
            solicitudSeleccionada: null,
            archivosAnexos: {},
            isSubmitting: false,
            
            territorios: {{ json_encode($territorios) }},
            
            tiposTramites: {
                'inspeccion': {
                    nombre: 'Solicitud de Inspección TÉCNICA',
                    descripcion: 'Evaluación e inspección en sitio por parte del personal técnico ambiental.',
                    plazoDias: 0,
                    requisitosObligatorios: [
                        { key: 'cedula', label: 'Copia de la Cédula de Identidad', hint: 'PDF o imagen legible' }
                    ],
                    requisitosCondicionales: [],
                    requisitosOpcionales: []
                },
                'poda_deshierbe': {
                    nombre: 'Solicitud de Permiso para Poda o Deshierbe',
                    descripcion: 'Autorización técnica para labores de mantenimiento, fitosanitarias o poda de formación de vegetación.',
                    plazoDias: 31,
                    requisitosObligatorios: [
                        { key: 'cedula', label: 'Copia de la Cédula de Identidad', hint: 'Requisito inicial obligatorio' },
                        { key: 'carta_explicativa', label: 'Carta Explicativa del Trabajo a Realizar', hint: 'Detallar motivos y ramas a intervenir' }
                    ],
                    requisitosCondicionales: [
                        { key: 'autorizacion_propietario', label: 'Autorización del Propietario', hint: 'Si no es el propietario directo del inmueble/terreno', flag: 'aplicaAutorizacionPropietario' },
                        { key: 'variables_ambientales', label: 'Variables Ambientales (MINEC)', hint: 'Si aplica en Zona Protectora o requiere afectación vegetal ante el MINEC', flag: 'aplicaVariablesAmbientales' },
                        { key: 'registro_mercantil', label: 'Copia del Registro Mercantil', hint: 'Requerido para Personas Jurídicas / Empresas', flag: 'esPersonaJuridica' },
                        { key: 'rif_empresa', label: 'Copia del RIF Vigente de la Empresa', hint: 'Requerido para Personas Jurídicas / Empresas', flag: 'esPersonaJuridica' }
                    ],
                    requisitosOpcionales: [
                        { key: 'doc_propiedad', label: 'Documento de Propiedad Registrado', hint: 'Título o documento legal' },
                        { key: 'plano_ubicacion', label: 'Plano de Ubicación del Terreno', hint: 'Croquis o plano de mensura' },
                        { key: 'solvencia_municipal', label: 'Solvencia Municipal Vigente', hint: 'Emitida por la Alcaldía' },
                        { key: 'constancia_catastral', label: 'Constancia Catastral Vigente', hint: 'Catastro municipal' }
                    ]
                },
                'tala': {
                    nombre: 'Solicitud de Permiso para Tala de Árboles',
                    descripcion: 'Autorización técnica para el derribo/remoción de ejemplares arbóreos por riesgo, infraestructura o enfermedad.',
                    plazoDias: 31,
                    requisitosObligatorios: [
                        { key: 'cedula', label: 'Copia de la Cédula de Identidad', hint: 'Requisito inicial obligatorio' },
                        { key: 'carta_explicativa', label: 'Carta Justificativa de Tala', hint: 'Detallar motivos de riesgo o estado fitosanitario' },
                        { key: 'carta_compromiso', label: 'Carta de Compromiso de Compensación Ambiental', hint: 'Compromiso firmado para reforestación o donación' }
                    ],
                    requisitosCondicionales: [
                        { key: 'autorizacion_propietario', label: 'Autorización del Propietario', hint: 'Si no es el propietario directo del inmueble/terreno', flag: 'aplicaAutorizacionPropietario' },
                        { key: 'variables_ambientales', label: 'Variables Ambientales (MINEC)', hint: 'Si aplica en Zona Protectora o resguardo hídrico', flag: 'aplicaVariablesAmbientales' },
                        { key: 'registro_mercantil', label: 'Copia del Registro Mercantil', hint: 'Requerido para Personas Jurídicas / Empresas', flag: 'esPersonaJuridica' },
                        { key: 'rif_empresa', label: 'Copia del RIF Vigente de la Empresa', hint: 'Requerido para Personas Jurídicas / Empresas', flag: 'esPersonaJuridica' }
                    ],
                    requisitosOpcionales: [
                        { key: 'doc_propiedad', label: 'Documento de Propiedad Registrado', hint: 'Título o documento legal' },
                        { key: 'plano_ubicacion', label: 'Plano de Ubicación del Terreno', hint: 'Croquis o plano de mensura' },
                        { key: 'solvencia_municipal', label: 'Solvencia Municipal Vigente', hint: 'Emitida por la Alcaldía' },
                        { key: 'constancia_catastral', label: 'Constancia Catastral Vigente', hint: 'Catastro municipal' }
                    ]
                },
                'desmalezamiento_conformacion': {
                    nombre: 'Permiso de Desmalezamiento y Conformación de Terreno',
                    descripcion: 'Permisos para trabajos de limpieza profunda, acondicionamiento y movimiento menor de suelos.',
                    plazoDias: 31,
                    requisitosObligatorios: [
                        { key: 'cedula', label: 'Copia de la Cédula de Identidad', hint: 'Requisito inicial obligatorio' },
                        { key: 'carta_explicativa', label: 'Carta Explicativa del Trabajo a Realizar', hint: 'Detallar intervención de suelo' }
                    ],
                    requisitosCondicionales: [
                        { key: 'autorizacion_propietario', label: 'Autorización del Propietario', hint: 'Si no es el propietario directo del inmueble/terreno', flag: 'aplicaAutorizacionPropietario' },
                        { key: 'variables_ambientales', label: 'Variables Ambientales (MINEC)', hint: 'Si aplica en Zona Protectora o requiere afectación vegetal ante el MINEC', flag: 'aplicaVariablesAmbientales' },
                        { key: 'registro_mercantil', label: 'Copia del Registro Mercantil', hint: 'Requerido para Personas Jurídicas / Empresas', flag: 'esPersonaJuridica' },
                        { key: 'rif_empresa', label: 'Copia del RIF Vigente de la Empresa', hint: 'Requerido para Personas Jurídicas / Empresas', flag: 'esPersonaJuridica' }
                    ],
                    requisitosOpcionales: [
                        { key: 'doc_propiedad', label: 'Documento de Propiedad Registrado', hint: 'Título o documento legal' },
                        { key: 'plano_ubicacion', label: 'Plano de Ubicación del Terreno', hint: 'Croquis o plano de mensura' },
                        { key: 'solvencia_municipal', label: 'Solvencia Municipal Vigente', hint: 'Emitida por la Alcaldía' },
                        { key: 'constancia_catastral', label: 'Constancia Catastral Vigente', hint: 'Catastro municipal' },
                        { key: 'carta_compromiso', label: 'Carta de Compromiso de Reposición', hint: 'En caso de afectación a bienes públicos' }
                    ]
                }
            },
            
            selectedComuna: '',
            selectedSector: '',
            selectedTramiteKey: '',
            direccionExacta: '',
            motivoSolicitud: '',
            
            aplicaAutorizacionPropietario: false,
            aplicaVariablesAmbientales: false,
            esPersonaJuridica: false,
            archivosSubidos: {},

            submitForm(form) {
                if (this.isSubmitting) return;
                this.isSubmitting = true;
                form.submit();
            },

            onTramiteChange() {
                this.archivosSubidos = {};
                this.aplicaAutorizacionPropietario = false;
                this.aplicaVariablesAmbientales = false;
                this.esPersonaJuridica = false;
            },

            handleFileChange(key, input) {
                if (input.files && input.files[0]) {
                    this.archivosSubidos[key] = input.files[0].name;
                } else {
                    delete this.archivosSubidos[key];
                }
            },

            handleAnexoFileChange(key, input) {
                if (input.files && input.files[0]) {
                    this.archivosAnexos[key] = input.files[0].name;
                } else {
                    delete this.archivosAnexos[key];
                }
            },

            verDetalle(item) {
                this.solicitudSeleccionada = item;
                this.archivosAnexos = {};
                this.openModalDetalle = true;
            },

            get sectoresDisponibles() {
                return (this.selectedComuna && this.territorios[this.selectedComuna]) 
                    ? this.territorios[this.selectedComuna] 
                    : [];
            },

            get esFormularioValido() {
                if (!this.selectedTramiteKey || !this.selectedComuna || !this.selectedSector || !this.direccionExacta.trim() || !this.motivoSolicitud.trim()) {
                    return false;
                }
                const tramite = this.tiposTramites[this.selectedTramiteKey];
                if (!tramite) return false;
                
                // La cédula es el único requisito inicial estricto para registrar la solicitud
                if (!this.archivosSubidos['cedula']) return false;

                return true;
            },

            get clavesRecaudosConsignados() {
                if (!this.solicitudSeleccionada || !this.solicitudSeleccionada.recaudos) return [];
                return this.solicitudSeleccionada.recaudos.map(r => r.tipo_recaudo);
            },

            get datosEspecificosModal() {
                if (!this.solicitudSeleccionada) return {};
                let datos = this.solicitudSeleccionada.datos_especificos;
                if (typeof datos === 'string') {
                    try { datos = JSON.parse(datos); } catch(e) { datos = {}; }
                }
                return datos || {};
            },

            get requisitosPendientesModal() {
                if (!this.solicitudSeleccionada) return [];
                const config = this.tiposTramites[this.solicitudSeleccionada.tipo_tramite];
                if (!config) return [];
                
                const consignados = this.clavesRecaudosConsignados;
                const datos = this.datosEspecificosModal;

                const condicionales = (config.requisitosCondicionales || []).filter(req => {
                    if (req.key === 'autorizacion_propietario') return Boolean(datos.aplica_autorizacion_propietario);
                    if (req.key === 'variables_ambientales') return Boolean(datos.aplica_variables_ambientales);
                    if (req.key === 'registro_mercantil' || req.key === 'rif_empresa') return Boolean(datos.es_persona_juridica);
                    return false;
                });

                const todos = [...config.requisitosObligatorios, ...condicionales, ...config.requisitosOpcionales];
                return todos.filter(req => !consignados.includes(req.key));
            },

            get requisitosConsignadosModal() {
                if (!this.solicitudSeleccionada || !this.solicitudSeleccionada.recaudos) return [];
                const config = this.tiposTramites[this.solicitudSeleccionada.tipo_tramite];
                const todosRequisitos = config 
                    ? [...config.requisitosObligatorios, ...config.requisitosCondicionales, ...config.requisitosOpcionales] 
                    : [];

                return this.solicitudSeleccionada.recaudos.map(recaudo => {
                    const reqConfig = todosRequisitos.find(r => r.key === recaudo.tipo_recaudo);
                    return {
                        id: recaudo.id,
                        key: recaudo.tipo_recaudo,
                        label: reqConfig ? reqConfig.label : recaudo.nombre_archivo,
                        nombre_archivo: recaudo.nombre_archivo,
                        archivo_url: recaudo.archivo_url
                    };
                });
            },

            obtenerTiempoRestante(fechaLimite) {
                if (!fechaLimite) return null;

                const limite = new Date(fechaLimite);
                const hoy = new Date();
                
                limite.setHours(23, 59, 59, 999);
                
                const diferenciaMs = limite - hoy;

                if (diferenciaMs <= 0) {
                    return { expirado: true, texto: 'Plazo Vencido', fechaFormatted: limite.toLocaleDateString('es-ES') };
                }

                const totalDias = Math.ceil(diferenciaMs / (1000 * 60 * 60 * 24));
                const meses = Math.floor(totalDias / 30);
                const diasRestantes = totalDias % 30;

                let partes = [];
                if (meses > 0) partes.push(`${meses} ${meses === 1 ? 'mes' : 'meses'}`);
                if (diasRestantes > 0) partes.push(`${diasRestantes} ${diasRestantes === 1 ? 'día' : 'días'}`);

                return {
                    expirado: false,
                    totalDias: totalDias,
                    texto: partes.join(' y '),
                    fechaFormatted: limite.toLocaleDateString('es-ES')
                };
            }
        }"
    >
        {{-- Encabezado --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-xl font-bold text-slate-800">Gestión de Solicitudes y Trámites</h1>
                <p class="text-xs text-slate-500 mt-0.5">Consulta el estado de tus trámites o inicia una nueva solicitud ambiental.</p>
            </div>
            <button 
                @click="openModalCrear = true"
                type="button"
                class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow-md transition-all cursor-pointer border-0"
            >
                <svg class="w-4 h-4 text-white shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                <span>Nueva Solicitud</span>
            </button>
        </div>

        {{-- Alertas de Éxito y Error --}}
        @if (session('success'))
            <div x-show="showSuccessAlert" x-transition class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold rounded-2xl flex items-center justify-between gap-2 shadow-xs">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span>¡Éxito! {{ session('success') }}</span>
                </div>
                <button @click="showSuccessAlert = false" type="button" class="text-emerald-500 hover:text-emerald-800 p-1 rounded-lg cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        @endif

        @if (session('error'))
            <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold rounded-2xl flex items-center justify-between gap-2 shadow-xs">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>{{ session('error') }}</span>
                </div>
            </div>
        @endif

        {{-- Tabla de Solicitudes --}}
        <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                <h3 class="text-sm font-bold text-slate-800">Mis Solicitudes Registradas</h3>
            </div>

            @if ($tramites->isEmpty())
                <div class="p-8 text-center text-slate-400 text-xs">
                    <svg class="mx-auto h-10 w-10 text-slate-300 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    No tienes solicitudes registradas hasta el momento.
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-slate-600">
                        <thead class="bg-slate-50 text-slate-400 uppercase font-bold text-[10px] tracking-wider border-b border-slate-100">
                            <tr>
                                <th class="p-4">Código</th>
                                <th class="p-4">Trámite</th>
                                <th class="p-4">Ubicación</th>
                                <th class="p-4">Fecha Solicitud</th>
                                <th class="p-4">Estatus</th>
                                <th class="p-4 text-center">Plazo Faltantes</th>
                                <th class="p-4 text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 font-medium">
                            @foreach ($tramites as $tramite)
                                <tr class="hover:bg-slate-50/50 transition-colors">
                                    <td class="p-4 font-bold text-slate-800">{{ $tramite->codigo_expediente ?? '#' . $tramite->id }}</td>
                                    <td class="p-4 text-slate-700 font-semibold">
                                        {{ $tiposTramites[$tramite->tipo_tramite]['nombre'] ?? $tramite->tipo_tramite }}
                                    </td>
                                    <td class="p-4">{{ $tramite->comuna }} — {{ $tramite->sector }}</td>
                                    <td class="p-4 text-slate-400">{{ $tramite->created_at->format('d/m/Y') }}</td>
                                    <td class="p-4">
                                        @if($tramite->estado === 'incompleta')
                                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase inline-block border bg-amber-50 text-amber-700 border-amber-200">
                                                Documentación Incompleta
                                            </span>
                                        @elseif($tramite->estado === 'aprobado')
                                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase inline-block border bg-emerald-50 text-emerald-700 border-emerald-200">
                                                Aprobado
                                            </span>
                                        @elseif($tramite->estado === 'rechazado')
                                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase inline-block border bg-rose-50 text-rose-700 border-rose-200">
                                                Rechazado
                                            </span>
                                        @else
                                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase inline-block border bg-blue-50 text-blue-700 border-blue-200">
                                                {{ ucfirst($tramite->estado ?? 'Pendiente') }}
                                            </span>
                                        @endif
                                    </td>
                                    
                                    {{-- COLUMNA DE TIMER/PLAZO --}}
                                    <td class="p-4 text-center">
                                        @php
                                            $plazosDias = [
                                                'poda_deshierbe' => 31,
                                                'tala' => 31,
                                                'desmalezamiento_conformacion' => 31,
                                                'inspeccion' => 0,
                                            ];

                                            $diasPlazo = $plazosDias[$tramite->tipo_tramite] ?? 0;
                                            $fechaLimite = $tramite->fecha_limite_documentos 
                                                ? \Carbon\Carbon::parse($tramite->fecha_limite_documentos) 
                                                : ($diasPlazo > 0 && $tramite->estado === 'incompleta' ? $tramite->created_at->addDays($diasPlazo) : null);
                                        @endphp

                                        @if($fechaLimite && $tramite->estado === 'incompleta')
                                            <div x-data="{ timer: obtenerTiempoRestante('{{ $fechaLimite->toIso8601String() }}') }">
                                                <template x-if="timer && !timer.expirado">
                                                    <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl border bg-amber-50/80 text-amber-800 border-amber-200 text-[10px] font-bold" :title="'Fecha límite: ' + timer.fechaFormatted">
                                                        <svg class="w-3.5 h-3.5 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                        <span>Quedan <strong x-text="timer.texto"></strong></span>
                                                    </div>
                                                </template>
                                                <template x-if="timer && timer.expirado">
                                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl border bg-rose-50 text-rose-700 border-rose-200 text-[10px] font-bold">
                                                        Plazo Vencido
                                                    </span>
                                                </template>
                                            </div>
                                        @else
                                            <span class="text-slate-300 text-[11px]">—</span>
                                        @endif
                                    </td>

                                    <td class="p-4 text-center">
                                        <button 
                                            @click="verDetalle({{ json_encode($tramite) }})" 
                                            type="button" 
                                            class="px-3 py-1.5 bg-slate-100 hover:bg-indigo-50 text-slate-600 hover:text-indigo-600 font-bold text-[11px] rounded-xl transition-colors cursor-pointer"
                                        >
                                            Ver Detalle
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        {{-- MODAL 1: NUEVA SOLICITUD --}}
        <div x-show="openModalCrear" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/60 backdrop-blur-xs">
            <div @click.outside="openModalCrear = false" class="bg-white w-full max-w-5xl rounded-3xl p-6 shadow-2xl space-y-4 max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <div>
                        <h3 class="text-sm font-bold text-slate-800">Crear Nueva Solicitud</h3>
                        <p class="text-[11px] text-slate-400">Completa los datos del trámite y adjunta los documentos requeridos.</p>
                    </div>
                    <button @click="openModalCrear = false" type="button" class="text-slate-400 hover:text-slate-600 text-lg font-bold cursor-pointer leading-none">&times;</button>
                </div>

                <form @submit.prevent="submitForm($el)" action="{{ route('ciudadano.solicitudes.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 w-full">
                        <div class="space-y-3 w-full">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Tipo de Trámite <span class="text-rose-500">*</span></label>
                                <select name="tipo_tramite" x-model="selectedTramiteKey" @change="onTramiteChange()" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-xs bg-white text-slate-800 focus:ring-2 focus:ring-indigo-500 focus:outline-none" required>
                                    <option value="">-- Seleccione un Trámite --</option>
                                    <template x-for="(tramite, key) in tiposTramites" :key="key">
                                        <option :value="key" x-text="tramite.nombre"></option>
                                    </template>
                                </select>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">Comuna <span class="text-rose-500">*</span></label>
                                    <select name="comuna" x-model="selectedComuna" @change="selectedSector = ''" class="w-full border border-slate-200 rounded-xl px-2.5 py-2 text-xs bg-white text-slate-800 focus:ring-2 focus:ring-indigo-500 focus:outline-none" required>
                                        <option value="">-- Comuna --</option>
                                        <template x-for="(sectores, comuna) in territorios" :key="comuna">
                                            <option :value="comuna" x-text="comuna"></option>
                                        </template>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">Sector <span class="text-rose-500">*</span></label>
                                    <select name="sector" x-model="selectedSector" class="w-full border border-slate-200 rounded-xl px-2.5 py-2 text-xs bg-white text-slate-800 focus:ring-2 focus:ring-indigo-500 focus:outline-none disabled:bg-slate-100" :disabled="!selectedComuna" required>
                                        <option value="">-- Sector --</option>
                                        <template x-for="sector in sectoresDisponibles" :key="sector">
                                            <option :value="sector" x-text="sector"></option>
                                        </template>
                                    </select>
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Dirección Exacta <span class="text-rose-500">*</span></label>
                                <input type="text" name="direccion_exacta" x-model="direccionExacta" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-xs bg-white text-slate-800 focus:ring-2 focus:ring-indigo-500 focus:outline-none" placeholder="Calle, casa/local, punto de referencia..." required>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Exposición de Motivos <span class="text-rose-500">*</span></label>
                                <textarea name="motivo_solicitud" x-model="motivoSolicitud" rows="3" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-xs bg-white text-slate-800 focus:ring-2 focus:ring-indigo-500 focus:outline-none resize-none" placeholder="Explicación o detalles de la solicitud..." required></textarea>
                            </div>

                            <template x-if="selectedTramiteKey && tiposTramites[selectedTramiteKey].requisitosCondicionales.length > 0">
                                <div class="p-3 bg-slate-50 border border-slate-200 rounded-2xl space-y-2 mt-2">
                                    <h4 class="text-xs font-bold text-slate-800">Verificación de Condiciones:</h4>
                                    
                                    <label class="flex items-start gap-2 cursor-pointer">
                                        <input type="checkbox" name="aplica_autorizacion_propietario" x-model="aplicaAutorizacionPropietario" value="1" class="mt-0.5 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                        <span class="text-[11px] text-slate-600">No soy el propietario directo (requiere adjuntar Autorización).</span>
                                    </label>

                                    <label class="flex items-start gap-2 cursor-pointer">
                                        <input type="checkbox" name="aplica_variables_ambientales" x-model="aplicaVariablesAmbientales" value="1" class="mt-0.5 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                        <span class="text-[11px] text-slate-600">El terreno requiere Acreditación de Variables Ambientales (MINEC).</span>
                                    </label>

                                    <label class="flex items-start gap-2 cursor-pointer">
                                        <input type="checkbox" name="es_persona_juridica" x-model="esPersonaJuridica" value="1" class="mt-0.5 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                        <span class="text-[11px] text-slate-600">La solicitud es realizada a nombre de una Persona Jurídica / Empresa (requiere Registro Mercantil y RIF).</span>
                                    </label>
                                </div>
                            </template>
                        </div>

                        <div class="space-y-3 w-full">
                            <template x-if="!selectedTramiteKey">
                                <div class="h-full min-h-[220px] flex flex-col items-center justify-center p-6 border-2 border-dashed border-slate-200 rounded-2xl bg-slate-50/50 text-center">
                                    <span class="text-xs font-bold text-slate-500">Selecciona un tipo de trámite</span>
                                </div>
                            </template>

                            <template x-if="selectedTramiteKey && tiposTramites[selectedTramiteKey]">
                                <div class="space-y-3 w-full">
                                    <template x-if="tiposTramites[selectedTramiteKey].plazoDias > 0">
                                        <div class="p-3 bg-amber-50 border border-amber-200 rounded-2xl flex items-start gap-2 text-amber-900 text-[11px]">
                                            <svg class="w-4 h-4 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            <div>
                                                <strong class="font-bold">Plazo de Entrega:</strong>
                                                <span>Dispones de <strong x-text="tiposTramites[selectedTramiteKey].plazoDias + ' días'"></strong> para entregar los anexos faltantes antes del descarte automático.</span>
                                            </div>
                                        </div>
                                    </template>

                                    <div class="space-y-2">
                                        <h4 class="text-xs font-bold text-slate-800 flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-rose-500"></span> Documentos Obligatorios</h4>
                                        <template x-for="req in tiposTramites[selectedTramiteKey].requisitosObligatorios" :key="req.key">
                                            <div class="border rounded-xl p-2.5 bg-white flex items-center justify-between gap-2" :class="archivosSubidos[req.key] ? 'border-emerald-400 bg-emerald-50/30' : 'border-slate-200'">
                                                <div class="min-w-0 flex-1">
                                                    <p class="text-xs font-bold text-slate-700 truncate" x-text="req.label"></p>
                                                    <p class="text-[10px] truncate" :class="archivosSubidos[req.key] ? 'text-emerald-700 font-semibold' : 'text-slate-400'" x-text="archivosSubidos[req.key] ? '✓ ' + archivosSubidos[req.key] : req.hint"></p>
                                                </div>
                                                <label :for="'file-' + req.key" class="px-3 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-bold text-[11px] rounded-xl cursor-pointer shrink-0">
                                                    <span x-text="archivosSubidos[req.key] ? 'Cambiar' : 'Adjuntar'"></span>
                                                </label>
                                                <input type="file" :id="'file-' + req.key" :name="'documentos[' + req.key + ']'" @change="handleFileChange(req.key, $el)" class="hidden">
                                            </div>
                                        </template>
                                    </div>

                                    <template x-if="tiposTramites[selectedTramiteKey].requisitosCondicionales.length > 0">
                                        <div class="space-y-2 pt-2 border-t border-slate-100">
                                            <template x-for="req in tiposTramites[selectedTramiteKey].requisitosCondicionales" :key="req.key">
                                                <div x-show="$data[req.flag]" class="border rounded-xl p-2.5 bg-amber-50/20 border-amber-300 flex items-center justify-between gap-2">
                                                    <div class="min-w-0 flex-1">
                                                        <p class="text-xs font-bold text-slate-800 truncate" x-text="req.label + ' *'"></p>
                                                        <p class="text-[10px] truncate" :class="archivosSubidos[req.key] ? 'text-emerald-700 font-semibold' : 'text-amber-600'" x-text="archivosSubidos[req.key] ? '✓ ' + archivosSubidos[req.key] : req.hint"></p>
                                                    </div>
                                                    <label :for="'file-' + req.key" class="px-3 py-1.5 bg-amber-100 hover:bg-amber-200 text-amber-800 font-bold text-[11px] rounded-xl cursor-pointer shrink-0">
                                                        <span x-text="archivosSubidos[req.key] ? 'Cambiar' : 'Adjuntar'"></span>
                                                    </label>
                                                    <input type="file" :id="'file-' + req.key" :name="'documentos[' + req.key + ']'" @change="handleFileChange(req.key, $el)" class="hidden">
                                                </div>
                                            </template>
                                        </div>
                                    </template>

                                    <template x-if="tiposTramites[selectedTramiteKey].requisitosOpcionales.length > 0">
                                        <div class="space-y-2 pt-2 border-t border-slate-100">
                                            <h4 class="text-xs font-bold text-slate-800 flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-amber-400"></span> Documentos Complementarios</h4>
                                            <div class="space-y-2 max-h-[180px] overflow-y-auto pr-1">
                                                <template x-for="req in tiposTramites[selectedTramiteKey].requisitosOpcionales" :key="req.key">
                                                    <div class="border rounded-xl p-2.5 bg-white flex items-center justify-between gap-2" :class="archivosSubidos[req.key] ? 'border-emerald-400 bg-emerald-50/30' : 'border-slate-200'">
                                                        <div class="min-w-0 flex-1">
                                                            <p class="text-xs font-bold text-slate-700 truncate" x-text="req.label"></p>
                                                            <p class="text-[10px] truncate" :class="archivosSubidos[req.key] ? 'text-emerald-700 font-semibold' : 'text-slate-400'" x-text="archivosSubidos[req.key] ? '✓ ' + archivosSubidos[req.key] : req.hint"></p>
                                                        </div>
                                                        <label :for="'file-' + req.key" class="px-3 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-bold text-[11px] rounded-xl cursor-pointer shrink-0">
                                                            <span x-text="archivosSubidos[req.key] ? 'Cambiar' : 'Adjuntar'"></span>
                                                        </label>
                                                        <input type="file" :id="'file-' + req.key" :name="'documentos[' + req.key + ']'" @change="handleFileChange(req.key, $el)" class="hidden">
                                                    </div>
                                                </template>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </template>
                        </div>
                    </div>

                    <div class="flex justify-end gap-2 pt-4 border-t border-slate-100">
                        <button type="button" @click="openModalCrear = false" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-xl cursor-pointer">Cancelar</button>
                        <button 
                            type="submit" 
                            :disabled="!esFormularioValido || isSubmitting"
                            class="px-5 py-2 bg-amber-500 hover:bg-amber-600 active:bg-amber-700 text-white font-bold text-xs rounded-xl shadow-xs transition-all cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
                        >
                            <template x-if="isSubmitting">
                                <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </template>
                            <span x-text="isSubmitting ? 'Procesando...' : 'Registrar Solicitud'"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- MODAL 2: DETALLE DEL EXPEDIENTE Y SUBIDA DE FALTANTES --}}
        <div x-show="openModalDetalle" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/60 backdrop-blur-xs">
            <div @click.outside="openModalDetalle = false" class="bg-white w-full max-w-3xl rounded-3xl p-6 shadow-2xl space-y-6 max-h-[90vh] overflow-y-auto">
                
                <template x-if="solicitudSeleccionada">
                    <div class="space-y-6">
                        <div class="flex items-start justify-between border-b border-slate-100 pb-4">
                            <div>
                                <span class="text-xs font-bold text-indigo-600 tracking-wide uppercase" x-text="solicitudSeleccionada.codigo_expediente"></span>
                                <h3 class="text-base font-bold text-slate-800" x-text="tiposTramites[solicitudSeleccionada.tipo_tramite]?.nombre || solicitudSeleccionada.tipo_tramite"></h3>
                                <p class="text-xs text-slate-400" x-text="solicitudSeleccionada.comuna + ' — ' + solicitudSeleccionada.sector"></p>
                            </div>
                            <button @click="openModalDetalle = false" type="button" class="text-slate-400 hover:text-slate-600 text-xl font-bold cursor-pointer leading-none">&times;</button>
                        </div>

                        <div class="bg-slate-50 rounded-2xl p-4 grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs border border-slate-100">
                            <div>
                                <span class="text-slate-400 font-semibold block text-[10px] uppercase">Dirección Exacta:</span>
                                <p class="text-slate-700 font-medium mt-0.5" x-text="solicitudSeleccionada.direccion_exacta"></p>
                            </div>
                            <div>
                                <span class="text-slate-400 font-semibold block text-[10px] uppercase">Estatus Actual:</span>
                                <span class="font-bold inline-block mt-0.5 text-indigo-700 capitalize" x-text="solicitudSeleccionada.estado || 'Pendiente'"></span>
                            </div>
                            <div class="sm:col-span-2">
                                <span class="text-slate-400 font-semibold block text-[10px] uppercase">Exposición de Motivos:</span>
                                <p class="text-slate-700 font-medium mt-0.5" x-text="solicitudSeleccionada.motivo_descripcion || solicitudSeleccionada.motivo_solicitud"></p>
                            </div>

                            <div class="sm:col-span-2 flex flex-wrap gap-2 pt-2 border-t border-slate-200/60">
                                <span 
                                    class="px-2.5 py-1 rounded-lg text-[10px] font-bold border"
                                    :class="datosEspecificosModal.aplica_autorizacion_propietario ? 'bg-amber-50 text-amber-800 border-amber-200' : 'bg-slate-100 text-slate-500 border-slate-200'"
                                >
                                    <span x-text="datosEspecificosModal.aplica_autorizacion_propietario ? '✓ Requiere Autorización de Propietario' : '✗ No requiere Autorización de Propietario'"></span>
                                </span>
                                <span 
                                    class="px-2.5 py-1 rounded-lg text-[10px] font-bold border"
                                    :class="datosEspecificosModal.aplica_variables_ambientales ? 'bg-amber-50 text-amber-800 border-amber-200' : 'bg-slate-100 text-slate-500 border-slate-200'"
                                >
                                    <span x-text="datosEspecificosModal.aplica_variables_ambientales ? '✓ Requiere Acreditación MINEC' : '✗ No requiere MINEC'"></span>
                                </span>
                                <span 
                                    class="px-2.5 py-1 rounded-lg text-[10px] font-bold border"
                                    :class="datosEspecificosModal.es_persona_juridica ? 'bg-amber-50 text-amber-800 border-amber-200' : 'bg-slate-100 text-slate-500 border-slate-200'"
                                >
                                    <span x-text="datosEspecificosModal.es_persona_juridica ? '✓ Persona Jurídica / Empresa' : '✗ Persona Natural'"></span>
                                </span>
                            </div>
                        </div>

                        <!-- Requisitos Consignados -->
                        <div class="space-y-2">
                            <h4 class="text-xs font-bold text-slate-800 flex items-center gap-2">
                                <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                <span>Documentos Consignados (<span x-text="requisitosConsignadosModal.length"></span>)</span>
                            </h4>
                            
                            <template x-if="requisitosConsignadosModal.length === 0">
                                <p class="text-xs text-slate-400 italic">No se han adjuntado documentos aún.</p>
                            </template>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                <template x-for="req in requisitosConsignadosModal" :key="req.id">
                                    <div class="p-2.5 rounded-xl bg-emerald-50/50 border border-emerald-200/80 flex items-center justify-between text-xs gap-2">
                                        <div class="min-w-0 flex-1">
                                            <p class="font-bold text-emerald-900 truncate" x-text="req.label"></p>
                                            <p class="text-[10px] text-emerald-600 truncate" x-text="req.nombre_archivo"></p>
                                        </div>
                                        <a :href="req.archivo_url" target="_blank" class="text-[10px] font-bold text-indigo-600 hover:text-indigo-800 bg-white px-2 py-1 rounded-lg border border-indigo-100 shrink-0">
                                            Ver
                                        </a>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <!-- Requisitos Pendientes -->
                        <div class="space-y-3 pt-2 border-t border-slate-100" x-data="{ infoTimer: obtenerTiempoRestante(solicitudSeleccionada.fecha_limite_documentos) }">
                            <div class="flex items-center justify-between">
                                <h4 class="text-xs font-bold text-slate-800 flex items-center gap-2">
                                    <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <span>Documentos Pendientes (<span x-text="requisitosPendientesModal.length"></span>)</span>
                                </h4>
                            </div>

                            <template x-if="solicitudSeleccionada.fecha_limite_documentos && requisitosPendientesModal.length > 0">
                                <div 
                                    class="p-3.5 rounded-2xl border flex items-center justify-between gap-3"
                                    :class="infoTimer && infoTimer.expirado ? 'bg-rose-50 border-rose-200 text-rose-900' : 'bg-amber-50 border-amber-200 text-amber-900'"
                                >
                                    <div class="flex items-center gap-3">
                                        <div class="p-2 rounded-xl" :class="infoTimer && infoTimer.expirado ? 'bg-rose-100 text-rose-600' : 'bg-amber-100 text-amber-600'">
                                            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        </div>
                                        <div class="text-xs">
                                            <p class="font-bold">
                                                <span x-show="infoTimer && !infoTimer.expirado">Tiempo restante para la entrega:</span>
                                                <span x-show="infoTimer && infoTimer.expirado">¡Plazo de entrega expirado!</span>
                                            </p>
                                            <p class="text-[11px] opacity-80" x-show="infoTimer && !infoTimer.expirado">
                                                Tienes hasta el <strong x-text="infoTimer.fechaFormatted"></strong> para consignar los faltantes (<strong x-text="infoTimer.texto"></strong>).
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </template>

                            <template x-if="requisitosPendientesModal.length === 0">
                                <div class="p-4 bg-emerald-50 rounded-2xl border border-emerald-200 text-center text-xs text-emerald-800 font-bold">
                                    Has consignado la totalidad de los documentos para este expediente. El trámite ha pasado a estatus pendiente de revisión.
                                </div>
                            </template>

                            <template x-if="requisitosPendientesModal.length > 0">
                                <form :action="'/ciudadano/solicitudes/' + solicitudSeleccionada.id + '/pendientes'" method="POST" enctype="multipart/form-data" class="space-y-3">
                                    @csrf
                                    @method('PUT')

                                    <div class="space-y-2 max-h-[220px] overflow-y-auto pr-1">
                                        <template x-for="req in requisitosPendientesModal" :key="req.key">
                                            <div class="border rounded-xl p-2.5 bg-white flex items-center justify-between gap-2" :class="archivosAnexos[req.key] ? 'border-indigo-400 bg-indigo-50/20' : 'border-slate-200'">
                                                <div class="min-w-0 flex-1">
                                                    <p class="text-xs font-bold text-slate-700 truncate" x-text="req.label"></p>
                                                    <p class="text-[10px] truncate" :class="archivosAnexos[req.key] ? 'text-indigo-700 font-semibold' : 'text-slate-400'" x-text="archivosAnexos[req.key] ? '✓ Seleccionado: ' + archivosAnexos[req.key] : req.hint"></p>
                                                </div>
                                                <label :for="'anexo-' + req.key" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-[11px] rounded-xl cursor-pointer shrink-0">
                                                    <span x-text="archivosAnexos[req.key] ? 'Cambiar' : 'Seleccionar'"></span>
                                                </label>
                                                <input type="file" :id="'anexo-' + req.key" :name="'documentos[' + req.key + ']'" @change="handleAnexoFileChange(req.key, $el)" class="hidden">
                                            </div>
                                        </template>
                                    </div>

                                    <div class="flex justify-end pt-2">
                                        <button 
                                            type="submit" 
                                            :disabled="Object.keys(archivosAnexos).length === 0"
                                            :class="Object.keys(archivosAnexos).length > 0 ? 'bg-indigo-600 hover:bg-indigo-700 text-white shadow-md' : 'bg-slate-200 text-slate-400 cursor-not-allowed opacity-60'"
                                            class="px-5 py-2 text-xs font-bold rounded-xl transition-all border-0 cursor-pointer"
                                        >
                                            Anexar Documentos Seleccionados
                                        </button>
                                    </div>
                                </form>
                            </template>
                        </div>

                    </div>
                </template>

            </div>
        </div>

    </div>
</x-layouts.app>