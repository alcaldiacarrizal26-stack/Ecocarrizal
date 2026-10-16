<div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-4" 
     x-data="{ 
         esPropio: {{ isset($esManejoPropio) && $esManejoPropio ? 'true' : 'false' }},
         nombreEmpresaOriginal: '{{ $empresa->razon_social ?? $empresa->nombre_comercial ?? '' }}',
         rifEmpresaOriginal: '{{ $empresa->rif ?? '' }}',
         
         init() {
             // Si viene marcado por defecto, asignamos los datos de la empresa inmediatamente al iniciar
             if (this.esPropio && !this.manejadora_nombre) {
                 this.manejadora_nombre = this.nombreEmpresaOriginal;
                 this.manejadora_rif = this.rifEmpresaOriginal;
             }
         },

         toggleManejoPropio() {
             if (this.esPropio) {
                 this.manejadora_nombre = this.nombreEmpresaOriginal;
                 this.manejadora_rif = this.rifEmpresaOriginal;
             } else {
                 this.manejadora_nombre = '';
                 this.manejadora_rif = '';
             }
         }
     }">
    
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 pb-3">
        <div>
            <h2 class="text-base font-semibold text-slate-800">1. Período Declarado y Esquema de Manejo</h2>
            <p class="text-xs text-slate-500">Selecciona el período semanal e indica si el manejo es propio o con un tercero.</p>
        </div>

        <!-- Check / Toggle de Manejo Propio -->
        <label class="inline-flex items-center gap-2 cursor-pointer bg-slate-50 hover:bg-slate-100 px-3.5 py-2 rounded-xl border border-slate-200 transition-colors">
            <input type="checkbox" 
                   x-model="esPropio" 
                   @change="toggleManejoPropio()"
                   class="w-4 h-4 text-emerald-600 rounded border-slate-300 focus:ring-emerald-500">
            <span class="text-xs font-bold text-slate-700">Manejo Propio (Autogestión Interna)</span>
        </label>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
            <label class="block text-xs font-semibold text-slate-500 uppercase mb-1">Frecuencia</label>
            <input type="text" value="Semanal" disabled
                   class="w-full bg-slate-100 border border-slate-300 rounded-xl px-3 py-2 text-sm font-semibold text-slate-700 cursor-not-allowed">
            <input type="hidden" name="frecuencia" value="SEMANAL">
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-500 uppercase mb-1">Identificador de la Semana</label>
            <input type="text" name="periodo" placeholder="Ej: 2026-W12" required
                   class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-sm text-slate-800 placeholder-slate-400 focus:ring-2 focus:ring-emerald-500 focus:outline-none">
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-500 uppercase mb-1">
                Empresa Manejadora Destino
                <span x-show="esPropio" class="text-[10px] text-emerald-600 font-bold lowercase">(autoasignado)</span>
            </label>
            <input type="text" name="manejadora_nombre" 
                   x-model="manejadora_nombre" 
                   :readonly="esPropio"
                   :class="esPropio ? 'bg-slate-100 text-slate-600 cursor-not-allowed font-medium' : 'bg-slate-50 text-slate-800'"
                   placeholder="Ej: Reciclajes Carrizal C.A"
                   class="w-full border border-slate-300 rounded-xl px-3 py-2 text-sm placeholder-slate-400 focus:ring-2 focus:ring-emerald-500 focus:outline-none" required>
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-500 uppercase mb-1">Fecha Inicio (Lunes)</label>
            <input type="date" name="fecha_inicio_periodo" required
                   class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-sm text-slate-800 focus:ring-2 focus:ring-emerald-500 focus:outline-none">
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-500 uppercase mb-1">Fecha Fin (Domingo)</label>
            <input type="date" name="fecha_fin_periodo" required
                   class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-sm text-slate-800 focus:ring-2 focus:ring-emerald-500 focus:outline-none">
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-500 uppercase mb-1">
                RIF Manejadora
                <span x-show="esPropio" class="text-[10px] text-emerald-600 font-bold lowercase">(autoasignado)</span>
            </label>
            <input type="text" name="manejadora_rif" 
                   x-model="manejadora_rif" 
                   :readonly="esPropio"
                   :class="esPropio ? 'bg-slate-100 text-slate-600 cursor-not-allowed font-medium font-mono' : 'bg-slate-50 text-slate-800 font-mono'"
                   placeholder="J-12345678-9"
                   class="w-full border border-slate-300 rounded-xl px-3 py-2 text-sm placeholder-slate-400 focus:ring-2 focus:ring-emerald-500 focus:outline-none" required>
        </div>
    </div>
</div>