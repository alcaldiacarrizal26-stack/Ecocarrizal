@props([
    'name' => 'user_id',
    'id' => 'select_inspector_id',
    'required' => false,
])

<div class="w-full relative" x-data="selectInspectorSearch()">
    <label class="block text-xs font-semibold text-slate-600 uppercase mb-1">
        Asignar Funcionario / Inspector {{ $required ? '*' : '' }}
    </label>

    <!-- Campo oculto que enviará el ID seleccionado al formulario -->
    <input type="hidden" name="{{ $name }}" :value="selectedId" {{ $required ? 'required' : '' }}>

    <!-- Input Buscador -->
    <div class="relative">
        <input type="text" 
               x-model="search" 
               @focus="open = true" 
               @click.away="open = false"
               placeholder="🔍 Buscar por nombre o cédula..."
               class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white transition-all">
        
        <button type="button" 
                x-show="selectedId" 
                @click="limpiar()" 
                class="absolute right-3 top-2 text-slate-400 hover:text-red-500 font-bold text-xs">
            &times;
        </button>
    </div>

    <!-- Lista desplegable filtrada -->
    <div x-show="open" 
         x-cloak 
         class="absolute z-50 left-0 right-0 mt-1 max-h-48 overflow-y-auto bg-white border border-slate-200 rounded-xl shadow-lg divide-y divide-slate-100 text-xs">
        
        <template x-if="filtrados.length === 0">
            <div class="p-3 text-slate-400 text-center italic">
                No se encontraron coincidencias
            </div>
        </template>

        <template x-for="item in filtrados" :key="item.id">
            <div @click="seleccionar(item)" 
                 class="p-2.5 hover:bg-emerald-50 cursor-pointer transition-colors flex items-center justify-between">
                <div>
                    <div class="font-bold text-slate-800" x-text="item.nombre"></div>
                    <div class="text-[11px] text-slate-400">
                        C.I: <span x-text="item.cedula || 'N/P'"></span> | Credencial: <span x-text="item.credencial || 'S/C'"></span>
                    </div>
                </div>
                <span class="text-[10px] font-semibold px-2 py-0.5 rounded bg-slate-100 text-slate-600 uppercase" x-text="item.rol || 'Inspector'"></span>
            </div>
        </template>
    </div>
</div>

<script>
function selectInspectorSearch() {
    return {
        open: false,
        search: '',
        selectedId: '',
        items: [],

        init() {
            window.addEventListener('cargar-inspectores-select', (e) => {
                this.items = e.detail || [];
                // Resetear búsqueda e ID al abrir la modal para una nueva asignación
                this.selectedId = '';
                this.search = '';
            });
        },

        get filtrados() {
            if (!this.search || this.selectedId) {
                return this.items;
            }
            const q = this.search.toLowerCase().trim();
            return this.items.filter(i => {
                const nombre = (i.nombre || '').toLowerCase();
                const cedula = (i.cedula || '').toString().toLowerCase();
                const credencial = (i.credencial || '').toLowerCase();
                return nombre.includes(q) || cedula.includes(q) || credencial.includes(q);
            });
        },

        seleccionar(item) {
            this.selectedId = item.id;
            this.search = `${item.nombre} (C.I: ${item.cedula || 'N/P'})`;
            this.open = false;
        },

        limpiar() {
            this.selectedId = '';
            this.search = '';
            this.open = false;
        }
    }
}
</script>