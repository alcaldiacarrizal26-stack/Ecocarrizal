<x-layouts.app title="Compensación - Panel Ciudadano" active="compensacion">
<div class="container mx-auto px-4 py-6 space-y-6">

    <!-- Encabezado -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 bg-white p-6 rounded-xl shadow-sm border border-gray-100">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Módulo de Compensación Ambiental</h1>
            <p class="text-sm text-gray-500 mt-1">Gestión y seguimiento de compromisos de reforestación y entrega de ejemplares de árboles.</p>
        </div>
    </div>

    <!-- Alertas Flash -->
    @if(session('success'))
        <div class="p-4 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-800 rounded-r-lg text-sm flex items-center justify-between">
            <span>{{ session('success') }}</span>
            <button onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-800">&times;</button>
        </div>
    @endif

    @if($errors->any())
        <div class="p-4 bg-red-50 border-l-4 border-red-500 text-red-800 rounded-r-lg text-sm">
            <p class="font-bold mb-1">Por favor corrige los siguientes errores:</p>
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Indicadores / Métricas -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm flex items-center space-x-4">
            <div class="p-3 bg-emerald-100 text-emerald-600 rounded-lg">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase text-gray-400">Árboles Asignados</p>
                <p class="text-2xl font-bold text-gray-800">{{ $stats['arboles_comprometidos'] ?? 0 }}</p>
            </div>
        </div>

        <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm flex items-center space-x-4">
            <div class="p-3 bg-blue-100 text-blue-600 rounded-lg">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase text-gray-400">Entregados</p>
                <p class="text-2xl font-bold text-gray-800">{{ $stats['arboles_entregados'] ?? 0 }}</p>
            </div>
        </div>

        <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm flex items-center space-x-4">
            <div class="p-3 bg-amber-100 text-amber-600 rounded-lg">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase text-gray-400">Pendientes por Entregar</p>
                <p class="text-2xl font-bold text-gray-800">{{ $stats['pendientes_entrega'] ?? 0 }}</p>
            </div>
        </div>
    </div>

    <!-- Tabla de Registros -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
            <h2 class="text-base font-semibold text-gray-800">Compromisos de Compensación Ambientales</h2>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-600">
                <thead class="bg-gray-50 text-xs uppercase text-gray-500 font-semibold border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-3">Resolución / Expediente</th>
                        <th class="px-6 py-3">Especie Requerida</th>
                        <th class="px-6 py-3">Progreso de Entrega</th>
                        <th class="px-6 py-3">Estatus</th>
                        <th class="px-6 py-3 text-right">Acción</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($compensaciones as $item)
                        <tr class="hover:bg-gray-50/50">
                            <td class="px-6 py-4 font-medium text-gray-800">
                                <div class="font-bold text-emerald-700">{{ $item->codigo_resolucion }}</div>
                                <div class="text-xs text-gray-400">Exp: {{ $item->solicitud->codigo_expediente ?? 'N/A' }}</div>
                            </td>
                            <td class="px-6 py-4 font-medium text-gray-800">{{ $item->especie_requerida }}</td>
                            <td class="px-6 py-4">
                                <span class="font-bold text-gray-800">{{ $item->cantidad_entregada }}</span> / <span>{{$item->cantidad_asignada }}</span> uds.
                                <div class="w-full bg-gray-200 rounded-full h-1.5 mt-1">
                                    @php
                                        $porcentaje =$item->cantidad_asignada > 0 ? min(100, round(($item->cantidad_entregada / $item->cantidad_asignada) * 100)) : 0;
                                    @endphp
                                    <div class="bg-emerald-600 h-1.5 rounded-full" style="width: {{ $porcentaje }}%"></div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @if($item->estatus === 'COMPLETADO')
                                    <span class="px-2.5 py-1 text-xs font-semibold bg-emerald-100 text-emerald-800 rounded-full">Completado</span>
                                @elseif($item->estatus === 'PARCIAL')
                                    <span class="px-2.5 py-1 text-xs font-semibold bg-blue-100 text-blue-800 rounded-full">Parcial</span>
                                @else
                                    <span class="px-2.5 py-1 text-xs font-semibold bg-amber-100 text-amber-800 rounded-full">Pendiente</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right space-x-2">
                                @if($item->estatus !== 'COMPLETADO')
                                    <button onclick="abrirModalCargarSoporte('{{ $item->id }}', '{{$item->codigo_resolucion }}', {{ $item->cantidad_asignada -$item->cantidad_entregada }})" 
                                            class="inline-flex items-center px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-medium rounded-md shadow-sm transition">
                                        Registrar Entrega
                                    </button>
                                @endif

                                @if($item->comprobante_path)
                                    <a href="{{ Storage::url($item->comprobante_path) }}" target="_blank" class="inline-flex items-center px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-medium rounded-md transition">
                                        Ver Comprobante
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center text-gray-400">
                                <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                </svg>
                                No posees resoluciones ni compromisos de compensación pendientes.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Formulario Registrar Entrega de Compensación -->
<div id="modalCargarSoporte" class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm hidden flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-md overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
            <h3 class="font-bold text-gray-800">Cargar Entrega de Compensación</h3>
            <button onclick="document.getElementById('modalCargarSoporte').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">&times;</button>
        </div>
        <form id="formCompensacion" action="" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Resolución</label>
                <input type="text" id="modal_codigo_resolucion" disabled class="w-full bg-gray-100 border-gray-300 rounded-lg text-sm text-gray-600 font-bold">
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Cantidad Entregada (Arboles)</label>
                <input type="number" name="cantidad_entregada" id="modal_cantidad_entregada" min="1" required class="w-full border-gray-300 rounded-lg text-sm focus:ring-emerald-500 focus:border-emerald-500" placeholder="Ej. 5">
                <span class="text-xs text-gray-400 mt-1 block">Pendientes max: <span id="modal_max_pendientes" class="font-semibold text-emerald-600">0</span></span>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Comprobante de Entrega / Recibo del Vivero (PDF / Imagen)</label>
                <input type="file" name="comprobante" accept="image/*,.pdf" required class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100">
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Observaciones</label>
                <textarea name="observaciones" rows="3" class="w-full border-gray-300 rounded-lg text-sm focus:ring-emerald-500 focus:border-emerald-500" placeholder="Notas o detalles de la entrega en el vivero municipal..."></textarea>
            </div>

            <div class="flex justify-end space-x-3 pt-2">
                <button type="button" onclick="document.getElementById('modalCargarSoporte').classList.add('hidden')" class="px-4 py-2 border border-gray-300 text-gray-600 text-sm font-medium rounded-lg hover:bg-gray-50">Cancelar</button>
                <button type="submit" class="px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700">Guardar Entrega</button>
            </div>
        </form>
    </div>
</div>

<script>
    function abrirModalCargarSoporte(id, resolucion, pendientes) {
        document.getElementById('formCompensacion').action = `/ciudadano/compensaciones/${id}`;
        document.getElementById('modal_codigo_resolucion').value = resolucion;
        document.getElementById('modal_cantidad_entregada').max = pendientes;
        document.getElementById('modal_cantidad_entregada').value = pendientes;
        document.getElementById('modal_max_pendientes').innerText = pendientes;
        document.getElementById('modalCargarSoporte').classList.remove('hidden');
    }
</script>
</x-layouts.app>