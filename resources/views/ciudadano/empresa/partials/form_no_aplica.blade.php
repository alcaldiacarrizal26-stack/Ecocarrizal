<div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-4">
    <div class="border-b border-slate-100 pb-3">
        <h2 class="text-base font-semibold text-slate-800">1. Notificación Simplificada de Generación Residual</h2>
        <p class="text-xs text-slate-500 mt-1">
            Su empresa se encuentra registrada bajo la categoría de <strong class="text-slate-700">BAJO IMPACTO (NO APLICA RACDA)</strong>. Únicamente debe confirmar el período y declarar la no generación de residuos peligrosos o de manejo especial.
        </p>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
            <label class="block text-xs font-semibold text-slate-500 uppercase mb-1">Frecuencia</label>
            <select name="frecuencia" x-model="frecuencia" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-sm text-slate-800 focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                <option value="ANUAL">Anual</option>
                <option value="SEMESTRAL">Semestral</option>
            </select>
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-500 uppercase mb-1">Identificador del Período</label>
            <input type="text" name="periodo" placeholder="Ej: 2026-ANUAL" required
                   class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-sm text-slate-800 placeholder-slate-400 focus:ring-2 focus:ring-emerald-500 focus:outline-none">
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-500 uppercase mb-1">Fecha de Notificación</label>
            <input type="date" name="fecha_inicio_periodo" value="{{ date('Y-m-d') }}" required
                   class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-sm text-slate-800 focus:ring-2 focus:ring-emerald-500 focus:outline-none">
            <input type="hidden" name="fecha_fin_periodo" value="{{ date('Y-m-d') }}">
        </div>
    </div>

    <!-- Declaración Jurada Simplificada -->
    <div class="p-4 bg-emerald-50/60 border border-emerald-200 rounded-xl space-y-3 mt-4">
        <div class="flex items-start gap-3">
            <input type="checkbox" id="declaracion_jurada" name="declaracion_jurada" required
                   class="mt-1 rounded text-emerald-600 focus:ring-emerald-500 h-4 w-4 border-slate-300">
            <label for="declaracion_jurada" class="text-xs text-slate-700 leading-relaxed">
                <strong>Declaración Jurada:</strong> Declaro que durante este período el establecimiento ha generado exclusivamente <strong>desechos sólidos comunes / urbanos</strong> derivados del uso administrativo u operativo básico, los cuales son retirados por el servicio público de aseo urbano. Confirmo que no se han generado efluentes industriales, emisiones atmosféricas ni residuos peligrosos/regulados.
            </label>
        </div>
    </div>
</div>