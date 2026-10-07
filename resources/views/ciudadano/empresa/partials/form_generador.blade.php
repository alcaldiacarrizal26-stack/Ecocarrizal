<div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-4">
    <div class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-100 pb-2">
        <div>
            <h2 class="text-base font-semibold text-slate-800">1. Período Declarado y Empresa Manejadora</h2>
            <p class="text-xs text-slate-500">Información del período de generación y la empresa contratada para el manejo.</p>
        </div>
        <button type="button" 
                @click="seleccionarManejadoraPredefinida()" 
                class="inline-flex items-center gap-1.5 px-3 py-1 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 text-xs font-semibold rounded-lg border border-emerald-200 transition-colors">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            Cargar Manejadora Habitual
        </button>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
            <label class="block text-xs font-semibold text-slate-500 uppercase mb-1">Frecuencia</label>
            <select name="frecuencia" x-model="frecuencia" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-sm text-slate-800 focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                <option value="MENSUAL">Mensual</option>
                <option value="TRIMESTRAL">Trimestral</option>
                <option value="SEMESTRAL">Semestral</option>
                <option value="ANUAL">Anual</option>
            </select>
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-500 uppercase mb-1">Identificador del Período</label>
            <input type="text" name="periodo" placeholder="Ej: 2026-03 o 2026-Q1" required
                   class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-sm text-slate-800 placeholder-slate-400 focus:ring-2 focus:ring-emerald-500 focus:outline-none">
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-500 uppercase mb-1">Empresa Manejadora Destino</label>
            <input type="text" name="manejadora_nombre" 
                   x-model="manejadora_nombre" 
                   placeholder="Ej: Reciclajes Carrizal C.A"
                   class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-sm text-slate-800 placeholder-slate-400 focus:ring-2 focus:ring-emerald-500 focus:outline-none">
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-500 uppercase mb-1">Fecha Inicio</label>
            <input type="date" name="fecha_inicio_periodo" required
                   class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-sm text-slate-800 focus:ring-2 focus:ring-emerald-500 focus:outline-none">
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-500 uppercase mb-1">Fecha Fin</label>
            <input type="date" name="fecha_fin_periodo" required
                   class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-sm text-slate-800 focus:ring-2 focus:ring-emerald-500 focus:outline-none">
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-500 uppercase mb-1">RIF Manejadora</label>
            <input type="text" name="manejadora_rif" 
                   x-model="manejadora_rif" 
                   placeholder="J-12345678-9"
                   class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-sm text-slate-800 placeholder-slate-400 focus:ring-2 focus:ring-emerald-500 focus:outline-none">
        </div>
    </div>
</div>