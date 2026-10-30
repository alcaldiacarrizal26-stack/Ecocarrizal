<x-layouts.app title="Compensación y Donación Ambiental - Panel Ciudadano" active="compensacion">
<div class="max-w-7xl mx-auto space-y-8" x-data="{ modalAbierto: false, esDonacion: false, itemSeleccionado: null, nombreArchivo: '' }">

    <!-- Encabezado -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-800">Compensación y Donación Ambiental</h1>
            <p class="text-xs text-slate-500 mt-0.5">Gestión de compromisos de reforestación y donaciones voluntarias de plantas en viveros municipales.</p>
        </div>
        <div>
            <!-- Botón para Donar Ejemplares sin requerir resolución previa -->
            <button @click="esDonacion = true; itemSeleccionado = { id: null, resolucion: 'DONACION-VOLUNTARIA', especie: '', pendientes: 999 }; nombreArchivo = ''; modalAbierto = true;"
                    class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow-md transition-all cursor-pointer border-0">
                <svg class="w-4 h-4 text-white shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                <span>Donar Ejemplares (Voluntario)</span>
            </button>
        </div>
    </div>

    <!-- Alertas Flash -->
    @if(session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold rounded-2xl flex items-center justify-between gap-2 shadow-xs">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span>¡Éxito! {{ session('success') }}</span>
            </div>
            <button @click="$el.parentElement.remove()" class="text-emerald-500 hover:text-emerald-800 p-1 rounded-lg cursor-pointer">&times;</button>
        </div>
    @endif

    @if($errors->any())
        <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold rounded-2xl space-y-1 shadow-xs">
            <p class="font-bold">Por favor corrige los siguientes errores:</p>
            <ul class="list-disc list-inside space-y-0.5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Indicadores / Métricas -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-xs flex items-center space-x-4">
            <div class="p-3 bg-indigo-50 text-indigo-600 rounded-2xl">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                </svg>
            </div>
            <div>
                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Ejemplares Asignados</p>
                <p class="text-xl font-bold text-slate-800 mt-0.5">{{ $stats['arboles_comprometidos'] ?? 0 }}</p>
            </div>
        </div>

        <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-xs flex items-center space-x-4">
            <div class="p-3 bg-emerald-50 text-emerald-600 rounded-2xl">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Entregados / Donados</p>
                <p class="text-xl font-bold text-slate-800 mt-0.5">{{ $stats['arboles_entregados'] ?? 0 }}</p>
            </div>
        </div>

        <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-xs flex items-center space-x-4">
            <div class="p-3 bg-amber-50 text-amber-600 rounded-2xl">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Pendientes por Entregar</p>
                <p class="text-xl font-bold text-slate-800 mt-0.5">{{ $stats['pendientes_entrega'] ?? 0 }}</p>
            </div>
        </div>
    </div>

    <!-- Tabla de Registros -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex justify-between items-center">
            <h3 class="text-sm font-bold text-slate-800">Compromisos de Compensación y Donaciones</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50 text-slate-400 uppercase font-bold text-[10px] tracking-wider border-b border-slate-100">
                    <tr>
                        <th class="p-4">Resolución / Tipo</th>
                        <th class="p-4">Especie Requerida / Donada</th>
                        <th class="p-4">Progreso / Cantidad</th>
                        <th class="p-4">Estatus</th>
                        <th class="p-4 text-center">Acción</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse ($compensaciones as $item)
                        @php
                            $esVoluntario = ($item->tipo_tramite === 'DONACION' || str_contains($item->codigo_resolucion, 'DONACION'));
                            $pendientes = max(0, $item->cantidad_asignada - $item->cantidad_entregada);
                            $porcentaje = $item->cantidad_asignada > 0 ? min(100, round(($item->cantidad_entregada / $item->cantidad_asignada) * 100)) : 100;
                        @endphp
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="p-4 font-bold text-slate-800">
                                @if($esVoluntario)
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase inline-block border bg-purple-50 text-purple-700 border-purple-200">
                                        Donación Voluntaria
                                    </span>
                                @else
                                    <div class="font-bold text-indigo-600 font-mono">{{ $item->codigo_resolucion }}</div>
                                    <div class="text-[10px] text-slate-400 mt-0.5">Exp: {{ $item->solicitud->codigo_expediente ?? 'N/A' }}</div>
                                @endif
                            </td>
                            <td class="p-4 text-slate-700 font-semibold">{{ $item->especie_requerida }}</td>
                            <td class="p-4">
                                @if($esVoluntario)
                                    <span class="font-bold text-slate-800 font-mono">{{ $item->cantidad_entregada }} uds. donadas</span>
                                @else
                                    <div class="w-full max-w-xs">
                                        <div class="flex justify-between text-[11px] mb-1 font-bold">
                                            <span class="text-slate-700">{{ $item->cantidad_entregada }} de {{ $item->cantidad_asignada }} uds.</span>
                                            <span class="text-emerald-600">{{ $porcentaje }}%</span>
                                        </div>
                                        <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                                            <div class="bg-emerald-500 h-2 rounded-full transition-all duration-300" style="width: {{ $porcentaje }}%"></div>
                                        </div>
                                    </div>
                                @endif
                            </td>
                            <td class="p-4">
                                @if($item->estatus === 'COMPLETADO')
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase inline-block border bg-emerald-50 text-emerald-700 border-emerald-200">Aprobado / Completado</span>
                                @elseif($item->estatus === 'PARCIAL')
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase inline-block border bg-blue-50 text-blue-700 border-blue-200">Parcial</span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase inline-block border bg-amber-50 text-amber-700 border-amber-200">Pendiente de Revisión</span>
                                @endif
                            </td>
                            <td class="p-4 text-center space-x-2">
                                @if(!$esVoluntario && $item->estatus !== 'COMPLETADO')
                                    <button 
                                        @click="esDonacion = false; itemSeleccionado = {
                                            id: '{{ $item->id }}',
                                            resolucion: '{{ $item->codigo_resolucion }}',
                                            especie: '{{ $item->especie_requerida }}',
                                            pendientes: {{ $pendientes }}
                                        }; nombreArchivo = ''; modalAbierto = true;" 
                                        class="px-3 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-bold text-[11px] rounded-xl transition-colors cursor-pointer">
                                        Registrar Entrega
                                    </button>
                                @endif

                                @if($item->comprobante_path)
                                    <a href="{{ Storage::url($item->comprobante_path) }}" target="_blank" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-[11px] rounded-xl transition-colors inline-block">
                                        Ver Comprobante
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-8 text-center text-slate-400 text-xs">
                                <svg class="mx-auto h-10 w-10 text-slate-300 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                </svg>
                                No posees resoluciones ni donaciones registradas aún.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Formulario (Registrar Entrega de Compensación o Donación Voluntaria) -->
    <div x-show="modalAbierto" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/60 backdrop-blur-xs">
        <div @click.outside="modalAbierto = false" class="bg-white w-full max-w-lg rounded-3xl p-6 shadow-2xl space-y-4 max-h-[90vh] overflow-y-auto">
            
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div>
                    <h3 class="text-sm font-bold text-slate-800" x-text="esDonacion ? 'Registrar Donación Voluntaria' : 'Cargar Entrega de Compensación'"></h3>
                    <p class="text-[11px] text-slate-400">Completa los datos del aporte ambiental.</p>
                </div>
                <button @click="modalAbierto = false" type="button" class="text-slate-400 hover:text-slate-600 text-lg font-bold cursor-pointer leading-none">&times;</button>
            </div>

            <form x-bind:action="esDonacion ? '{{ route('ciudadano.compensacion.donar') }}' : '{{ url('/ciudadano/compensacion') }}/' + itemSeleccionado?.id" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <template x-if="!esDonacion">
                    <input type="hidden" name="_method" value="PUT">
                </template>

                <input type="hidden" name="compensacion_id" x-bind:value="itemSeleccionado?.id">

                <template x-if="esDonacion">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Especie / Tipo de Planta Donada <span class="text-rose-500">*</span></label>
                        <input type="text" name="tipo_especie" placeholder="Ej: Araguaney, Bucare, Plantas Ornamentales..." required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:bg-white focus:border-indigo-600 transition">
                    </div>
                </template>

                <template x-if="!esDonacion">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Código de Resolución</label>
                        <input type="text" x-bind:value="itemSeleccionado?.resolucion" disabled class="w-full px-4 py-2.5 bg-slate-100 border border-slate-200 rounded-xl text-xs text-slate-700 font-mono font-bold">
                        <input type="hidden" name="tipo_especie" x-bind:value="itemSeleccionado?.especie">
                    </div>
                </template>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Cantidad de Ejemplares <span class="text-rose-500">*</span></label>
                    <input type="number" name="cantidad" min="1" :max="esDonacion ? 999 : itemSeleccionado?.pendientes" value="1" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:bg-white focus:border-indigo-600 transition">
                    <span x-show="!esDonacion" class="text-[10px] text-slate-400 mt-1 block">Máximo pendiente por entregar: <strong class="text-indigo-600" x-text="itemSeleccionado?.pendientes"></strong> uds.</span>
                </div>

                <!-- Cuadro para insertar el Comprobante -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Comprobante de Vivero (Foto / PDF) <span class="text-rose-500">*</span></label>
                    <div class="relative border-2 border-dashed border-slate-200 hover:border-indigo-500 rounded-2xl p-4 text-center bg-slate-50/50 transition-all cursor-pointer group">
                        <input type="file" name="comprobante" accept="image/*,.pdf" required 
                               @change="nombreArchivo = $event.target.files[0] ? $event.target.files[0].name : ''"
                               class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                        
                        <div class="space-y-1.5 pointer-events-none">
                            <div class="inline-flex p-2.5 bg-indigo-50 text-indigo-600 rounded-full group-hover:scale-105 transition-transform">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 0115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                </svg>
                            </div>
                            <p class="text-xs font-bold text-slate-700">
                                <span x-text="nombreArchivo ? 'Archivo seleccionado:' : 'Arrastra tu archivo aquí o haz clic para buscar'"></span>
                            </p>
                            <p class="text-[11px] text-indigo-600 font-mono font-bold truncate max-w-xs mx-auto" x-text="nombreArchivo"></p>
                            <p class="text-[10px] text-slate-400">PDF, JPG, PNG (Máx. 4MB)</p>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Observaciones / Motivo</label>
                    <textarea name="observaciones" rows="3" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:bg-white focus:border-indigo-600 transition resize-none" :placeholder="esDonacion ? 'Indica detalles de tu aporte o iniciativa comunitaria...' : 'Indica detalles de la entrega realizada...'"></textarea>
                </div>

                <div class="flex justify-end gap-2 pt-3 border-t border-slate-100">
                    <button type="button" @click="modalAbierto = false" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-xl cursor-pointer">Cancelar</button>
                    <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow-xs transition cursor-pointer border-0" x-text="esDonacion ? 'Enviar Donación para Revisión' : 'Guardar Entrega'"></button>
                </div>
            </form>
        </div>
    </div>

</div>
</x-layouts.app>