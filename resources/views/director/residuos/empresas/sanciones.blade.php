<x-layouts.app title="Gestión de Sanciones - {{ $empresa->razon_social }}" active="residuos_empresas">
<div class="p-6 max-w-7xl mx-auto space-y-6" x-data="{ modalNuevaSancion: false, modalResolver: false, sancionSeleccionada: null }">

    <!-- ALERTAS -->
    @if(session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-xl flex items-center justify-between text-emerald-800 text-sm shadow-sm">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span>{{ session('success') }}</span>
            </div>
            <button type="button" @click="$el.parentElement.remove()" class="text-emerald-500 font-bold">&times;</button>
        </div>
    @endif

    <!-- NAVEGACIÓN Y CABECERA -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-500 mb-1">
                <a href="{{ route('director.residuos.empresas.index') }}" class="hover:underline">Empresas</a>
                <span>/</span>
                <a href="{{ route('director.residuos.empresas.show', $empresa->id) }}" class="hover:underline">{{ $empresa->razon_social }}</a>
                <span>/</span>
                <span class="text-slate-700 font-semibold">Sanciones y Procedimientos</span>
            </div>
            <h1 class="text-2xl font-bold text-slate-800">Expediente Sancionatorio</h1>
            <p class="text-sm text-slate-500">RIF: {{ $empresa->rif }} | Control fiscal y resoluciones administrativas</p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('director.residuos.empresas.show', $empresa->id) }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-xl transition">
                &larr; Volver a Empresa
            </a>
            <button type="button" @click="modalNuevaSancion = true" class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold rounded-xl transition shadow-sm flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                Aperturar Sanción / Citación
            </button>
        </div>
    </div>

    <!-- TARJETAS DE ESTADÍSTICAS SANCIONATORIAS -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase">Total Procedimientos</p>
                <p class="text-2xl font-bold text-slate-800 mt-1">{{ $sanciones->count() }}</p>
            </div>
            <div class="p-3 bg-slate-100 text-slate-600 rounded-xl">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            </div>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-rose-600 uppercase">Activas / Notificadas</p>
                <p class="text-2xl font-bold text-rose-600 mt-1">{{ $sanciones->whereIn('estatus', ['NOTIFICADA', 'PENDIENTE_NOTIFICACION'])->count() }}</p>
            </div>
            <div class="p-3 bg-rose-50 text-rose-600 rounded-xl">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-emerald-600 uppercase">Subsanadas / Retiradas</p>
                <p class="text-2xl font-bold text-emerald-600 mt-1">{{ $sanciones->where('estatus', 'SUBSANADA')->count() }}</p>
            </div>
            <div class="p-3 bg-emerald-50 text-emerald-600 rounded-xl">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            </div>
        </div>
    </div>

    <!-- TABLA DE EXPEDIENTES SANCIONATORIOS -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
            <h2 class="text-sm font-bold text-slate-800 uppercase tracking-wide">Histórico de Citaciones e Infracciones</h2>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left text-slate-600">
                <thead class="text-[11px] uppercase bg-slate-50 text-slate-500 font-bold border-b border-slate-200">
                    <tr>
                        <th class="px-4 py-3">Expediente</th>
                        <th class="px-4 py-3">Infracción</th>
                        <th class="px-4 py-3 text-center">Gravedad</th>
                        <th class="px-4 py-3">Fechas Clave</th>
                        <th class="px-4 py-3">Estatus</th>
                        <th class="px-4 py-3">Evidencia Fiscal</th>
                        <th class="px-4 py-3 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($sanciones as $s)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-4 py-3 font-bold text-slate-800 whitespace-nowrap">
                                {{ $s->codigo_expediente }}
                            </td>
                            <td class="px-4 py-3">
                                <span class="font-semibold text-slate-800 block">{{ str_replace('_', ' ', $s->tipo_infraccion) }}</span>
                                <span class="text-[11px] text-slate-400 line-clamp-1">{{ $s->motivo_descripcion }}</span>
                            </td>
                            <td class="px-4 py-3 text-center whitespace-nowrap">
                                @php
                                    $badgeG = match($s->gravedad) {
                                        'LEVE' => 'bg-amber-50 text-amber-700 border-amber-200',
                                        'GRAVE' => 'bg-orange-50 text-orange-700 border-orange-200 font-bold',
                                        'MUY_GRAVE' => 'bg-rose-100 text-rose-800 border-rose-300 font-bold',
                                        default => 'bg-slate-100 text-slate-600 border-slate-200'
                                    };
                                @endphp
                                <span class="px-2 py-0.5 rounded-full border text-[10px] uppercase font-semibold {{ $badgeG }}">
                                    {{ $s->gravedad }}
                                </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div>Incidencia: <strong class="text-slate-700">{{ $s->fecha_incidencia ? $s->fecha_incidencia->format('d/m/Y') : 'N/P' }}</strong></div>
                                @if($s->fecha_limite_subsanacion)
                                    <div class="text-[10px] text-rose-600">Límite: {{ $s->fecha_limite_subsanacion->format('d/m/Y') }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                @php
                                    $badgeE = match($s->estatus) {
                                        'NOTIFICADA' => 'bg-rose-50 text-rose-700 border-rose-200',
                                        'EN_REVISION' => 'bg-purple-50 text-purple-700 border-purple-200',
                                        'SUBSANADA' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                        'DESESTIMADA' => 'bg-slate-100 text-slate-600 border-slate-200',
                                        default => 'bg-slate-100 text-slate-600'
                                    };
                                @endphp
                                <span class="px-2.5 py-1 rounded-md border text-[10px] font-bold uppercase {{ $badgeE }}">
                                    {{ str_replace('_', ' ', $s->estatus) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                @if(!empty($s->evidencias_fiscalia))
                                    @foreach($s->evidencias_fiscalia as $file)
                                        <a href="{{ asset('storage/' . $file) }}" target="_blank" class="inline-flex items-center gap-1 text-indigo-600 hover:underline font-semibold text-[11px]">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                            Ver Acta/Prueba
                                        </a>
                                    @endforeach
                                @else
                                    <span class="text-slate-400 italic">Sin adjuntos</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right whitespace-nowrap">
                                @if(in_array($s->estatus, ['NOTIFICADA', 'EN_REVISION', 'PENDIENTE_NOTIFICACION']))
                                    <button type="button" 
                                            @click="sancionSeleccionada = {{ json_encode($s) }}; modalResolver = true"
                                            class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-lg text-[11px] transition">
                                        Evaluar / Retirar Sanción
                                    </button>
                                @else
                                    <span class="text-slate-400 text-[11px] italic">Resuelta el {{ $s->fecha_resolucion ? $s->fecha_resolucion->format('d/m/Y') : '' }}</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-slate-400">
                                La empresa no posee citaciones ni sanciones registradas en su historial.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- MODAL 1: APERTURAR NUEVA SANCIÓN / CITACIÓN -->
    <div x-show="modalNuevaSancion" x-cloak class="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-xl max-w-lg w-full overflow-hidden border border-slate-200">
            <div class="p-5 border-b border-slate-100 bg-slate-50/50 flex justify-between items-center">
                <h3 class="font-bold text-slate-800 text-base">Aperturar Citación / Sanción Administrativa</h3>
                <button type="button" @click="modalNuevaSancion = false" class="text-slate-400 hover:text-slate-600 font-bold">&times;</button>
            </div>

            <form action="{{ route('director.residuos.empresas.sanciones.store', $empresa->id) }}" method="POST" enctype="multipart/form-data" class="p-5 space-y-4">
                @csrf

                <div>
                    <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Concepto / Tipo Infracción *</label>
                    <select name="tipo_infraccion" required class="w-full bg-slate-50 border border-slate-300 rounded-xl p-2.5 text-xs text-slate-800 focus:ring-2 focus:ring-rose-500">
                        <option value="FALTA_DECLARACION_JURADA">Falta de Declaración Jurada de Residuos</option>
                        <option value="INCONGRUENCIA_DISPOSICION">Incongruencia / Falsedad en Manifiestos HDS</option>
                        <option value="VERTIDO_NO_AUTORIZADO">Vertido o Almacenamiento Clandestino</option>
                        <option value="OBSTACULIZACION_FISCALIA">Obstaculización de la Función Inspectora</option>
                        <option value="INCUMPLIMIENTO_MEDIDAS">Incumplimiento de Medidas Correctoras</option>
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Gravedad *</label>
                        <select name="gravedad" required class="w-full bg-slate-50 border border-slate-300 rounded-xl p-2.5 text-xs text-slate-800 focus:ring-2 focus:ring-rose-500">
                            <option value="LEVE">LEVE</option>
                            <option value="GRAVE">GRAVE</option>
                            <option value="MUY_GRAVE">MUY GRAVE</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Fecha Incidencia *</label>
                        <input type="date" name="fecha_incidencia" value="{{ date('Y-m-d') }}" required class="w-full bg-slate-50 border border-slate-300 rounded-xl p-2.5 text-xs text-slate-800">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Fecha Citación (Opcional)</label>
                        <input type="date" name="fecha_citacion" class="w-full bg-slate-50 border border-slate-300 rounded-xl p-2.5 text-xs text-slate-800">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Límite Subsanación</label>
                        <input type="date" name="fecha_limite_subsanacion" class="w-full bg-slate-50 border border-slate-300 rounded-xl p-2.5 text-xs text-slate-800">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Base Legal / Fundamentación</label>
                    <input type="text" name="base_legal" placeholder="Ej: Art. 45 de la Ordenanza Municipal de Gestión de Residuos" class="w-full bg-slate-50 border border-slate-300 rounded-xl p-2.5 text-xs text-slate-800">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Motivo / Descripción Detallada *</label>
                    <textarea name="motivo_descripcion" rows="3" required placeholder="Describe el hallazgo o incumplimiento evidenciado..." class="w-full bg-slate-50 border border-slate-300 rounded-xl p-2.5 text-xs text-slate-800"></textarea>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Adjuntar Acta / Evidencia en Fotos o PDF</label>
                    <input type="file" name="evidencia" accept=".pdf,.jpg,.png,.jpeg" class="w-full text-xs text-slate-500 file:mr-2 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-rose-50 file:text-rose-700 hover:file:bg-rose-100">
                </div>

                <div class="flex justify-end gap-2 pt-2 border-t border-slate-100">
                    <button type="button" @click="modalNuevaSancion = false" class="px-4 py-2 bg-slate-100 text-slate-700 text-xs font-semibold rounded-xl">Cancelar</button>
                    <button type="submit" class="px-4 py-2 bg-rose-600 text-white text-xs font-bold rounded-xl shadow-sm">Confirmar y Notificar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL 2: RESOLVER / RETIRAR SANCIÓN -->
    <div x-show="modalResolver" x-cloak class="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-xl max-w-lg w-full overflow-hidden border border-slate-200">
            <div class="p-5 border-b border-slate-100 bg-slate-50/50 flex justify-between items-center">
                <div>
                    <h3 class="font-bold text-slate-800 text-base">Evaluar y Resolver Sanción</h3>
                    <p class="text-xs text-slate-500" x-text="sancionSeleccionada?.codigo_expediente"></p>
                </div>
                <button type="button" @click="modalResolver = false" class="text-slate-400 hover:text-slate-600 font-bold">&times;</button>
            </div>

            <form :action="'/director/residuos/empresas/sanciones/' + sancionSeleccionada?.id + '/resolver'" method="POST" enctype="multipart/form-data" class="p-5 space-y-4">
                @csrf
                @method('PATCH')

                <div>
                    <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Dictamen Final del Director *</label>
                    <select name="estatus" required class="w-full bg-slate-50 border border-slate-300 rounded-xl p-2.5 text-xs text-slate-800 focus:ring-2 focus:ring-emerald-500">
                        <option value="SUBSANADA">SUBSANADA (Aprobada subsanación y retirada la sanción)</option>
                        <option value="DESESTIMADA">DESESTIMADA (Anulada por improcedencia)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Observaciones / Justificación de Cierre *</label>
                    <textarea name="observaciones_resolucion" rows="3" required placeholder="Ingresa los motivos o acuerdos alcanzados para el retiro del expediente..." class="w-full bg-slate-50 border border-slate-300 rounded-xl p-2.5 text-xs text-slate-800"></textarea>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Adjuntar Comprobante de Subsanación / Acta de Cierre</label>
                    <input type="file" name="evidencia_resolucion" accept=".pdf,.jpg,.png,.jpeg" class="w-full text-xs text-slate-500 file:mr-2 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100">
                </div>

                <div class="flex justify-end gap-2 pt-2 border-t border-slate-100">
                    <button type="button" @click="modalResolver = false" class="px-4 py-2 bg-slate-100 text-slate-700 text-xs font-semibold rounded-xl">Cancelar</button>
                    <button type="submit" class="px-4 py-2 bg-emerald-600 text-white text-xs font-bold rounded-xl shadow-sm">Registrar Resolución</button>
                </div>
            </form>
        </div>
    </div>

</div>
</x-layouts.app>