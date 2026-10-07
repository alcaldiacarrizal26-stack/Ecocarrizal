<div class="bg-white p-6 rounded-2xl border border-amber-200 shadow-sm space-y-4">
    <div class="flex flex-wrap items-center justify-between gap-2 border-b border-amber-100 pb-2">
        <div>
            <h2 class="text-base font-semibold text-slate-800">1. Trazabilidad de Recepción y Operación</h2>
            <p class="text-xs text-slate-500">Registro de origen del cliente generador, Hoja de Seguimiento (HDS) y destino final.</p>
        </div>
        <button type="button" 
                @click="seleccionarGeneradorPredefinido()" 
                class="inline-flex items-center gap-1.5 px-3 py-1 bg-amber-50 hover:bg-amber-100 text-amber-800 text-xs font-semibold rounded-lg border border-amber-200 transition-colors">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            Cargar Generador Habitual
        </button>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
            <label class="block text-xs font-semibold text-slate-500 uppercase mb-1">Frecuencia</label>
            <select name="frecuencia" x-model="frecuencia" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-sm text-slate-800 focus:ring-2 focus:ring-amber-500 focus:outline-none">
                <option value="MENSUAL">Mensual</option>
                <option value="TRIMESTRAL">Trimestral</option>
                <option value="SEMESTRAL">Semestral</option>
                <option value="ANUAL">Anual</option>
            </select>
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-500 uppercase mb-1">Identificador del Período</label>
            <input type="text" name="periodo" placeholder="Ej: 2026-03 o 2026-Q1" required
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
            <label class="block text-xs font-semibold text-slate-500 uppercase mb-1">Cliente Generador Origen</label>
            <input type="text" name="generador_origen_nombre" 
                   x-model="generador_origen_nombre" 
                   placeholder="Ej: Industrias Químicas S.A." required
                   class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-sm text-slate-800 placeholder-slate-400 focus:ring-2 focus:ring-amber-500 focus:outline-none">
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-500 uppercase mb-1">RIF Cliente Generador</label>
            <input type="text" name="generador_origen_rif" 
                   x-model="generador_origen_rif" 
                   placeholder="J-98765432-1" required
                   class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-sm text-slate-800 placeholder-slate-400 focus:ring-2 focus:ring-amber-500 focus:outline-none">
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-500 uppercase mb-1">Placa del Vehículo de Transportista</label>
            <input type="text" name="vehiculo_placa" 
                   x-model="vehiculo_placa" 
                   placeholder="Ej: A12BC3D"
                   class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-sm text-slate-800 placeholder-slate-400 focus:ring-2 focus:ring-amber-500 focus:outline-none">
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-500 uppercase mb-1">Fecha Inicio</label>
            <input type="date" name="fecha_inicio_periodo" required
                   class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-sm text-slate-800 focus:ring-2 focus:ring-amber-500 focus:outline-none">
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-500 uppercase mb-1">Fecha Fin</label>
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