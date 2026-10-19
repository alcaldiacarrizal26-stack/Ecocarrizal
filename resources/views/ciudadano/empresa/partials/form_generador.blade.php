<div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-4">
    
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 pb-3">
        <div>
            <h2 class="text-base font-semibold text-slate-800">1. Período Declarado y Esquema de Manejo</h2>
            <p class="text-xs text-slate-500">Define el período de declaración y el tipo de gestión o destino de los residuos.</p>
        </div>

        <!-- Selector Inteligente de Esquema de Manejo -->
        <div class="w-full sm:w-72">
            <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Esquema de Manejo</label>
            <select x-model="es_manejo_propio" 
                    @change="toggleManejoPropio()"
                    class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-xs font-bold text-slate-700 focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                <option value="propio">🏠 Autogestión Interna (Manejo Propio)</option>
                <option value="externo">🤝 Empresa Manejadora Externa (Tercero)</option>
            </select>
        </div>
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
                <span x-show="es_manejo_propio === 'propio'" class="text-[10px] text-emerald-600 font-bold lowercase">(autoasignado)</span>
            </label>
            <input type="text" name="manejadora_nombre" 
                   x-model="manejadora_nombre" 
                   :readonly="es_manejo_propio === 'propio'"
                   :class="es_manejo_propio === 'propio' ? 'bg-slate-100 text-slate-600 cursor-not-allowed font-medium' : 'bg-slate-50 text-slate-800'"
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
                <span x-show="es_manejo_propio === 'propio'" class="text-[10px] text-emerald-600 font-bold lowercase">(autoasignado)</span>
            </label>
            <input type="text" name="manejadora_rif" 
                   x-model="manejadora_rif" 
                   :readonly="es_manejo_propio === 'propio'"
                   :class="es_manejo_propio === 'propio' ? 'bg-slate-100 text-slate-600 cursor-not-allowed font-medium font-mono' : 'bg-slate-50 text-slate-800 font-mono'"
                   placeholder="J-12345678-9"
                   class="w-full border border-slate-300 rounded-xl px-3 py-2 text-sm placeholder-slate-400 focus:ring-2 focus:ring-emerald-500 focus:outline-none" required>
        </div>
    </div>
</div>