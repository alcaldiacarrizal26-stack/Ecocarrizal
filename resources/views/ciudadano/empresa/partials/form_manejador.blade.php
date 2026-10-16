<div class="bg-white p-6 rounded-2xl border border-amber-200 shadow-sm space-y-4"
     x-data="{ 
         tieneGeneradorFijo: {{ isset($perfil?->generador_exclusivo_nombre) && !empty($perfil->generador_exclusivo_nombre) ? 'true' : 'false' }},
         nombreGeneradorOriginal: '{{ $perfil?->generador_exclusivo_nombre ?? '' }}',
         rifGeneradorOriginal: '{{ $perfil?->generador_exclusivo_rif ?? '' }}',
         
         init() {
             if (this.tieneGeneradorFijo && !this.generador_origen_nombre) {
                 this.generador_origen_nombre = this.nombreGeneradorOriginal;
                 this.generador_origen_rif = this.rifGeneradorOriginal;
             }
         },

         toggleGeneradorFijo() {
             if (this.tieneGeneradorFijo) {
                 this.generador_origen_nombre = this.nombreGeneradorOriginal;
                 this.generador_origen_rif = this.rifGeneradorOriginal;
             } else {
                 this.generador_origen_nombre = '';
                 this.generador_origen_rif = '';
             }
         }
     }">
     
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-amber-100 pb-3">
        <div>
            <h2 class="text-base font-semibold text-slate-800">1. Trazabilidad de Recepción y Operación</h2>
            <p class="text-xs text-slate-500">Registro de origen semanal del cliente generador, Hoja de Seguimiento (HDS) y destino final.</p>
        </div>

        <!-- Check / Toggle de Generador Fijo -->
        <template x-if="nombreGeneradorOriginal">
            <label class="inline-flex items-center gap-2 cursor-pointer bg-amber-50/70 hover:bg-amber-100/70 px-3.5 py-2 rounded-xl border border-amber-200 transition-colors">
                <input type="checkbox" 
                       x-model="tieneGeneradorFijo" 
                       @change="toggleGeneradorFijo()"
                       class="w-4 h-4 text-amber-600 rounded border-amber-300 focus:ring-amber-500">
                <span class="text-xs font-bold text-amber-900">Usar Generador Fijo Registrado</span>
            </label>
        </template>
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
                   class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-sm text-slate-800 placeholder-slate-400 focus:ring-2 focus:ring-amber-500 focus:outline-none">
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-500 uppercase mb-1">Nº Hoja de Seguimiento / HDS</label>
            <input type="text" name="hoja_seguimiento_numero" 
                   x-model="hoja_seguimiento_numero" 
                   placeholder="Ej: HDS-2026-0045" required
                   class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-sm text-slate-800 placeholder-slate-400 focus:ring-2 focus:ring-amber-500 focus:outline-none">
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-500 uppercase mb-1">
                Cliente Generador Origen
                <span x-show="tieneGeneradorFijo" class="text-[10px] text-amber-700 font-bold lowercase">(fijo)</span>
            </label>
            <input type="text" name="generador_origen_nombre" 
                   x-model="generador_origen_nombre" 
                   :readonly="tieneGeneradorFijo"
                   :class="tieneGeneradorFijo ? 'bg-slate-100 text-slate-600 cursor-not-allowed font-medium' : 'bg-slate-50 text-slate-800'"
                   placeholder="Ej: Industrias Químicas S.A." required
                   class="w-full border border-slate-300 rounded-xl px-3 py-2 text-sm placeholder-slate-400 focus:ring-2 focus:ring-amber-500 focus:outline-none">
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-500 uppercase mb-1">
                RIF Cliente Generador
                <span x-show="tieneGeneradorFijo" class="text-[10px] text-amber-700 font-bold lowercase">(fijo)</span>
            </label>
            <input type="text" name="generador_origen_rif" 
                   x-model="generador_origen_rif" 
                   :readonly="tieneGeneradorFijo"
                   :class="tieneGeneradorFijo ? 'bg-slate-100 text-slate-600 cursor-not-allowed font-medium font-mono' : 'bg-slate-50 text-slate-800 font-mono'"
                   placeholder="J-98765432-1" required
                   class="w-full border border-slate-300 rounded-xl px-3 py-2 text-sm placeholder-slate-400 focus:ring-2 focus:ring-amber-500 focus:outline-none">
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-500 uppercase mb-1">Placa del Vehículo de Transportista</label>
            <input type="text" name="vehiculo_placa" 
                   x-model="vehiculo_placa" 
                   placeholder="Ej: A12BC3D"
                   class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-sm text-slate-800 placeholder-slate-400 focus:ring-2 focus:ring-amber-500 focus:outline-none">
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-500 uppercase mb-1">Fecha Inicio (Lunes)</label>
            <input type="date" name="fecha_inicio_periodo" required
                   class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-sm text-slate-800 focus:ring-2 focus:ring-amber-500 focus:outline-none">
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-500 uppercase mb-1">Fecha Fin (Domingo)</label>
            <input type="date" name="fecha_fin_periodo" required
                   class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-sm text-slate-800 focus:ring-2 focus:ring-amber-500 focus:outline-none">
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-500 uppercase mb-1">Destino Final o Planta de Disposición</label>
            <input type="text" name="destino_final_nombre" 
                   x-model="destino_final_nombre" 
                   placeholder="Ej: Relleno Sanitario Limoncito"
                   class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-sm text-slate-800 placeholder-slate-400 focus:ring-2 focus:ring-amber-500 focus:outline-none">
        </div>
    </div>
</div>