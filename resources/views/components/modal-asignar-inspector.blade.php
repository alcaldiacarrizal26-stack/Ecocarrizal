@props([
    'modalId' => 'modalAsignarInspector',
])

<div id="{{ $modalId }}" class="fixed inset-0 z-50 hidden bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xl max-w-md w-full overflow-hidden">
        
        <!-- Header -->
        <div class="p-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
            <div>
                <h3 id="modal_titulo" class="text-base font-bold text-slate-800">Asignar Inspector en Campo</h3>
                <p id="modal_subtitulo" class="text-xs text-slate-500 mt-0.5">Selecciona el funcionario responsable de la actuación.</p>
            </div>
            <button type="button" onclick="cerrarModalInspector('{{ $modalId }}')" class="text-slate-400 hover:text-slate-600 font-bold text-lg">&times;</button>
        </div>

        <!-- Formulario -->
        <form id="form_asignar_inspector" method="POST" action="" class="p-5 space-y-4">
            @csrf
            @method('PUT') <!-- Se sobrescribe dinámicamente según la ruta -->

            <!-- Componente Select Normalizado -->
            <x-select-inspector id="inspector_select_modal" name="inspector_id" required />

            <div>
                <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">Instrucciones u Observaciones</label>
                <textarea name="observaciones" rows="3" placeholder="Ej: Realizar fiscalización de emergencia / verificar manifiestos de transporte..."
                          class="w-full bg-slate-50 border border-slate-300 rounded-xl p-3 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500"></textarea>
            </div>

            <!-- Footer con Botones -->
            <div class="flex items-center justify-end gap-2 pt-2">
                <button type="button" onclick="cerrarModalInspector('{{ $modalId }}')" 
                        class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-xl transition-colors">
                    Cancelar
                </button>
                <button type="submit" 
                        class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow-sm transition-colors">
                    Confirmar Asignación
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function abrirModalInspector(config) {
        const modal = document.getElementById('{{ $modalId }}');
        const form = document.getElementById('form_asignar_inspector');
        const select = document.getElementById('inspector_select_modal');
        
        if (!modal || !form || !select) {
            console.error('No se encontraron los elementos necesarios del modal en el DOM.');
            return;
        }

        // Personalizar Acción del Formulario
        form.action = config.actionUrl;
        
        if (config.method) {
            let inputMethod = form.querySelector('input[name="_method"]');
            if (inputMethod) inputMethod.value = config.method;
        }

        if (config.titulo) document.getElementById('modal_titulo').innerText = config.titulo;
        if (config.subtitulo) document.getElementById('modal_subtitulo').innerText = config.subtitulo;

        // Limpiar y mostrar estado de carga en el select
        select.innerHTML = '<option value="">Cargando inspectores disponibles...</option>';
        select.disabled = true;

        // Abrir el modal inmediatamente para retroalimentación visual
        modal.classList.remove('hidden');

        // Cargar los inspectores vía API dinámicamente
        fetch(`/api/inspectores/disponibles?${config.params || ''}`, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(async res => {
            const data = await res.json();
            if (!res.ok || !Array.isArray(data)) {
                throw new Error(data.detalle || data.message || data.error || 'Error desconocido al cargar inspectores');
            }
            return data;
        })
        .then(data => {
            select.disabled = false;
            if (data.length === 0) {
                select.innerHTML = '<option value="">No hay inspectores disponibles</option>';
                return;
            }

            select.innerHTML = '<option value="">-- Seleccionar Fiscal / Inspector --</option>';
            data.forEach(item => {
                select.innerHTML += `<option value="${item.id}">${item.nombre} (${item.credencial || 'S/C'}) - [${item.especialidad || 'General'}]</option>`;
            });
        })
        .catch(err => {
            console.error('Error al cargar inspectores:', err);
            select.disabled = false;
            select.innerHTML = '<option value="">Error al obtener inspectores</option>';
            alert('No se pudieron cargar los inspectores: ' + err.message);
        });
    }

    function cerrarModalInspector(modalId) {
        const targetId = modalId || '{{ $modalId }}';
        const modal = document.getElementById(targetId);
        if (modal) {
            modal.classList.add('hidden');
        }
    }
</script>