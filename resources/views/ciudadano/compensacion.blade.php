<x-layouts.app title="Compensación y Donación Ambiental - Panel Ciudadano" active="compensacion">
<div class="container mx-auto px-4 py-6 space-y-6" x-data="{ modalAbierto: false, esDonacion: false, itemSeleccionado: null, nombreArchivo: '' }">

    <!-- Encabezado -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 bg-white dark:bg-slate-900 p-6 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white flex items-center gap-2.5">
                <svg class="w-7 h-7 text-emerald-600 dark:text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                </svg>
                Compensación y Donación Ambiental
            </h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Gestión de compromisos de reforestación y donaciones voluntarias de plantas en viveros municipales.</p>
        </div>
        <div>
            <!-- Botón para Donar Ejemplares sin requerir resolución previa -->
            <button @click="esDonacion = true; itemSeleccionado = { id: null, resolucion: 'DONACION-VOLUNTARIA', especie: '', pendientes: 999 }; nombreArchivo = ''; modalAbierto = true;"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white font-semibold text-xs rounded-xl shadow-sm transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                <span>Donar Ejemplares (Voluntario)</span>
            </button>
        </div>
    </div>

    <!-- Alertas Flash -->
    @if(session('success'))
        <div class="p-4 bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 rounded-xl text-sm flex items-center justify-between">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>{{ session('success') }}</span>
            </div>
            <button @click="$el.parentElement.remove()" class="text-emerald-600 hover:text-emerald-800 dark:hover:text-emerald-200">&times;</button>
        </div>
    @endif

    @if($errors->any())
        <div class="p-4 bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-300 rounded-xl text-sm">
            <p class="font-bold mb-1">Por favor corrige los siguientes errores:</p>
            <ul class="list-disc list-inside space-y-0.5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Indicadores / Métricas -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm flex items-center space-x-4">
            <div class="p-3 bg-emerald-100 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 rounded-xl">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase text-slate-400">Ejemplares Asignados</p>
                <p class="text-2xl font-bold text-slate-900 dark:text-white">{{ $stats['arboles_comprometidos'] ?? 0 }}</p>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm flex items-center space-x-4">
            <div class="p-3 bg-sky-100 dark:bg-sky-950/60 text-sky-600 dark:text-sky-400 rounded-xl">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase text-slate-400">Entregados / Donados</p>
                <p class="text-2xl font-bold text-slate-900 dark:text-white">{{ $stats['arboles_entregados'] ?? 0 }}</p>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm flex items-center space-x-4">
            <div class="p-3 bg-amber-100 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 rounded-xl">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase text-slate-400">Pendientes por Entregar</p>
                <p class="text-2xl font-bold text-slate-900 dark:text-white">{{ $stats['pendientes_entrega'] ?? 0 }}</p>
            </div>
        </div>
    </div>

    <!-- Tabla de Registros -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-800 flex justify-between items-center">
            <h2 class="text-base font-semibold text-slate-900 dark:text-white">Compromisos de Compensación y Donaciones</h2>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600 dark:text-slate-300">
                <thead class="bg-slate-50 dark:bg-slate-800/50 text-xs uppercase text-slate-500 font-semibold border-b border-slate-200 dark:border-slate-800">
                    <tr>
                        <th class="px-6 py-3.5">Resolución / Tipo</th>
                        <th class="px-6 py-3.5">Especie Requerida / Donada</th>
                        <th class="px-6 py-3.5">Progreso / Cantidad</th>
                        <th class="px-6 py-3.5">Estatus</th>
                        <th class="px-6 py-3.5 text-right">Acción</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                    @forelse ($compensaciones as $item)
                        @php
                            $esVoluntario = ($item->tipo_tramite === 'DONACION' || str_contains($item->codigo_resolucion, 'DONACION'));
                            $pendientes = max(0, $item->cantidad_asignada - $item->cantidad_entregada);
                            $porcentaje = $item->cantidad_asignada > 0 ? min(100, round(($item->cantidad_entregada / $item->cantidad_asignada) * 100)) : 100;
                        @endphp
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30">
                            <td class="px-6 py-4 font-medium text-slate-900 dark:text-white">
                                @if($esVoluntario)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-purple-100 text-purple-800 dark:bg-purple-950/60 dark:text-purple-300">
                                        Donación Voluntaria
                                    </span>
                                @else
                                    <div class="font-bold text-emerald-600 dark:text-emerald-400 font-mono">{{ $item->codigo_resolucion }}</div>
                                    <div class="text-xs text-slate-400">Exp: {{ $item->solicitud->codigo_expediente ?? 'N/A' }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 font-medium text-slate-900 dark:text-white">{{ $item->especie_requerida }}</td>
                            <td class="px-6 py-4">
                                @if($esVoluntario)
                                    <span class="font-bold text-slate-800 dark:text-slate-200 font-mono">{{ $item->cantidad_entregada }} uds. donadas</span>
                                @else
                                    <div class="w-full max-w-xs">
                                        <div class="flex justify-between text-xs mb-1 font-semibold">
                                            <span class="text-slate-700 dark:text-slate-300">{{ $item->cantidad_entregada }} de {{ $item->cantidad_asignada }} uds.</span>
                                            <span class="text-emerald-600 dark:text-emerald-400">{{ $porcentaje }}%</span>
                                        </div>
                                        <div class="w-full bg-slate-200 dark:bg-slate-800 rounded-full h-2">
                                            <div class="bg-emerald-500 h-2 rounded-full transition-all duration-300" style="width: {{ $porcentaje }}%"></div>
                                        </div>
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if($item->estatus === 'COMPLETADO')
                                    <span class="px-2.5 py-1 text-xs font-semibold bg-emerald-100 text-emerald-800 dark:bg-emerald-950/50 dark:text-emerald-300 rounded-full">Aprobado / Completado</span>
                                @elseif($item->estatus === 'PARCIAL')
                                    <span class="px-2.5 py-1 text-xs font-semibold bg-sky-100 text-sky-800 dark:bg-sky-950/50 dark:text-sky-300 rounded-full">Parcial</span>
                                @else
                                    <span class="px-2.5 py-1 text-xs font-semibold bg-amber-100 text-amber-800 dark:bg-amber-950/50 dark:text-amber-300 rounded-full">Pendiente de Revisión</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right space-x-2">
                                @if(!$esVoluntario && $item->estatus !== 'COMPLETADO')
                                    <button 
                                        @click="esDonacion = false; itemSeleccionado = {
                                            id: '{{ $item->id }}',
                                            resolucion: '{{ $item->codigo_resolucion }}',
                                            especie: '{{ $item->especie_requerida }}',
                                            pendientes: {{ $pendientes }}
                                        }; nombreArchivo = ''; modalAbierto = true;" 
                                        class="inline-flex items-center px-3.5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-xl shadow-sm transition">
                                        Registrar Entrega
                                    </button>
                                @endif

                                @if($item->comprobante_path)
                                    <a href="{{ Storage::url($item->comprobante_path) }}" target="_blank" class="inline-flex items-center px-3 py-2 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 text-slate-700 dark:text-slate-300 text-xs font-medium rounded-xl transition">
                                        Ver Comprobante
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-slate-400">
                                <svg class="w-12 h-12 mx-auto text-slate-300 dark:text-slate-700 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
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
    <div x-show="modalAbierto" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/60 backdrop-blur-sm">
        <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-xl w-full max-w-md overflow-hidden border border-slate-200 dark:border-slate-800 space-y-4">
            
            <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-800 flex justify-between items-center bg-slate-50/50 dark:bg-slate-800/30">
                <h3 class="font-bold text-slate-900 dark:text-white text-base" x-text="esDonacion ? 'Registrar Donación Voluntaria' : 'Cargar Entrega de Compensación'"></h3>
                <button @click="modalAbierto = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 text-xl font-bold">&times;</button>
            </div>

            <form x-bind:action="esDonacion ? '{{ route('ciudadano.compensacion.donar') }}' : '{{ url('/ciudadano/compensacion') }}/' + itemSeleccionado?.id" method="POST" enctype="multipart/form-data" class="p-6 pt-2 space-y-4">
                @csrf
                <template x-if="!esDonacion">
                    <input type="hidden" name="_method" value="PUT">
                </template>

                <input type="hidden" name="compensacion_id" x-bind:value="itemSeleccionado?.id">

                <template x-if="esDonacion">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase mb-1">Especie / Tipo de Planta Donada</label>
                        <input type="text" name="tipo_especie" placeholder="Ej: Araguaney, Bucare, Plantas Ornamentales..." required class="w-full border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-white rounded-xl text-sm focus:ring-2 focus:ring-emerald-500 outline-none p-2.5">
                    </div>
                </template>

                <template x-if="!esDonacion">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase mb-1">Código de Resolución</label>
                        <input type="text" x-bind:value="itemSeleccionado?.resolucion" disabled class="w-full bg-slate-100 dark:bg-slate-800 border-slate-300 dark:border-slate-700 rounded-xl text-sm text-slate-700 dark:text-slate-300 font-mono font-bold p-2.5">
                        <input type="hidden" name="tipo_especie" x-bind:value="itemSeleccionado?.especie">
                    </div>
                </template>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase mb-1">Cantidad de Ejemplares</label>
                    <input type="number" name="cantidad" min="1" :max="esDonacion ? 999 : itemSeleccionado?.pendientes" value="1" required class="w-full border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-white rounded-xl text-sm focus:ring-2 focus:ring-emerald-500 outline-none p-2.5">
                    <span x-show="!esDonacion" class="text-xs text-slate-400 mt-1 block">Máximo pendiente por entregar: <strong class="text-emerald-600 dark:text-emerald-400" x-text="itemSeleccionado?.pendientes"></strong> uds.</span>
                </div>

                <!-- MEJORA DEL CUADRO PARA INSERTAR LA EVIDENCIA (COMPROBANTE) -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase mb-1">Comprobante de Vivero (Foto / PDF)</label>
                    <div class="relative border-2 border-dashed border-slate-300 dark:border-slate-700 hover:border-emerald-500 dark:hover:border-emerald-500 rounded-2xl p-5 text-center bg-slate-50 dark:bg-slate-800/50 transition-all cursor-pointer group">
                        <input type="file" name="comprobante" accept="image/*,.pdf" required 
                               @change="nombreArchivo = $event.target.files[0] ? $event.target.files[0].name : ''"
                               class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                        
                        <div class="space-y-2 pointer-events-none">
                            <div class="inline-flex p-3 bg-emerald-100 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 rounded-full group-hover:scale-110 transition-transform">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 0115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                </svg>
                            </div>
                            <p class="text-xs font-semibold text-slate-700 dark:text-slate-200">
                                <span x-text="nombreArchivo ? 'Archivo seleccionado:' : 'Arrastra tu archivo aquí o haz clic para buscar'"></span>
                            </p>
                            <p class="text-[11px] text-emerald-600 dark:text-emerald-400 font-mono font-medium truncate max-w-xs mx-auto" x-text="nombreArchivo"></p>
                            <p class="text-[10px] text-slate-400">Formatos permitidos: PDF, JPG, PNG (Máx. 4MB)</p>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase mb-1">Observaciones / Motivo</label>
                    <textarea name="observaciones" rows="3" class="w-full border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-white rounded-xl text-sm focus:ring-2 focus:ring-emerald-500 outline-none p-2.5" :placeholder="esDonacion ? 'Indica detalles de tu aporte o iniciativa comunitaria...' : 'Indica detalles de la entrega realizada...'"></textarea>
                </div>

                <div class="flex justify-end space-x-3 pt-3">
                    <button type="button" @click="modalAbierto = false" class="px-4 py-2 border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-300 text-xs font-semibold rounded-xl hover:bg-slate-50 dark:hover:bg-slate-800 transition">Cancelar</button>
                    <button type="submit" class="px-4 py-2 bg-emerald-600 text-white text-xs font-semibold rounded-xl hover:bg-emerald-700 transition shadow-sm" x-text="esDonacion ? 'Enviar Donación para Revisión' : 'Guardar Entrega'"></button>
                </div>
            </form>
        </div>
    </div>

</div>
</x-layouts.app>