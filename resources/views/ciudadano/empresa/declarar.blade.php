<x-layouts.app title="Nueva Declaración de Residuos" active="empresa_declaraciones">
    <div class="p-6 max-w-5xl mx-auto space-y-6 bg-slate-50 min-h-screen" x-data="declaracionResiduos()">
        
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
            <div>
                <h1 class="text-2xl font-bold text-slate-800 tracking-tight">Declaración de Residuos y Desechos</h1>
                <p class="text-sm text-slate-500 mt-1">
                    Empresa: <span class="text-slate-700 font-semibold">{{ $empresa->razon_social ?? $empresa->nombre_comercial }}</span> 
                    <span class="font-mono text-xs text-slate-400">({{ $empresa->rif }})</span>
                </p>
            </div>
            <a href="{{ route('ciudadano.declaraciones.index') }}" 
               class="inline-flex items-center px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-medium rounded-xl border border-slate-300 transition-colors">
                &larr; Volver al Historial
            </a>
        </div>

        <form action="{{ route('ciudadano.declaraciones.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <!-- Sección 1: Datos del Período y Frecuencia -->
            <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-4">
                <h2 class="text-base font-semibold text-slate-800 border-b border-slate-100 pb-2">1. Período Declarado</h2>
                
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
                        <label class="block text-xs font-semibold text-slate-500 uppercase mb-1">Empresa Manejadora / Destino (Opcional)</label>
                        <input type="text" name="manejadora_nombre" placeholder="Nombre de la empresa receptora"
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
                        <label class="block text-xs font-semibold text-slate-500 uppercase mb-1">RIF Manejadora (Opcional)</label>
                        <input type="text" name="manejadora_rif" placeholder="J-12345678-9"
                               class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-sm text-slate-800 placeholder-slate-400 focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                    </div>
                </div>
            </div>

            <!-- Sección 2: Desglose de Residuos (Tabla Dinámica Alpine.js) -->
            <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                    <div>
                        <h2 class="text-base font-semibold text-slate-800">2. Desglose por Tipo de Residuo</h2>
                        <p class="text-xs text-slate-500">Agrega los renglones correspondientes a los residuos declarados en este período.</p>
                    </div>
                    <button type="button" @click="agregarRenglon()" 
                            class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-lg transition-colors flex items-center gap-1 shadow-sm">
                        + Agregar Residuo
                    </button>
                </div>

                <div class="space-y-3">
                    <template x-for="(item, index) in detalles" :key="index">
                        <div class="bg-slate-50 p-4 rounded-xl border border-slate-200 relative space-y-3">
                            <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-end">
                                
                                <!-- Nombre del Residuo -->
                                <div class="md:col-span-4">
                                    <label class="block text-xs font-medium text-slate-500 mb-1">Tipo de Residuo</label>
                                    <input type="text" :name="`detalles[${index}][nombre_residuo]`" x-model="item.nombre_residuo" placeholder="Ej: Cartón / Aceites Usados" required
                                           class="w-full bg-white border border-slate-300 rounded-lg px-3 py-2 text-sm text-slate-800 focus:outline-none focus:border-emerald-500">
                                    <input type="hidden" :name="`detalles[${index}][codigo_residuo]`" :value="slugify(item.nombre_residuo)">
                                </div>

                                <!-- Categoría -->
                                <div class="md:col-span-3">
                                    <label class="block text-xs font-medium text-slate-500 mb-1">Categoría</label>
                                    <select :name="`detalles[${index}][categoria]`" x-model="item.categoria" class="w-full bg-white border border-slate-300 rounded-lg px-3 py-2 text-sm text-slate-800 focus:outline-none focus:border-emerald-500">
                                        <option value="COMUN">Común / Orgánico</option>
                                        <option value="RECICLABLE">Reciclable</option>
                                        <option value="PELIGROSO">Peligroso (RACDA)</option>
                                    </select>
                                </div>

                                <!-- Cantidad -->
                                <div class="md:col-span-2">
                                    <label class="block text-xs font-medium text-slate-500 mb-1">Cantidad</label>
                                    <input type="number" step="0.001" min="0.001" :name="`detalles[${index}][cantidad]`" x-model.number="item.cantidad" required
                                           class="w-full bg-white border border-slate-300 rounded-lg px-3 py-2 text-sm text-slate-800 focus:outline-none focus:border-emerald-500">
                                </div>

                                <!-- Unidad de Medida -->
                                <div class="md:col-span-2">
                                    <label class="block text-xs font-medium text-slate-500 mb-1">Unidad</label>
                                    <select :name="`detalles[${index}][unidad_medida]`" x-model="item.unidad_medida" class="w-full bg-white border border-slate-300 rounded-lg px-3 py-2 text-sm text-slate-800 focus:outline-none focus:border-emerald-500">
                                        <option value="TON">Toneladas (TON)</option>
                                        <option value="KG">Kilogramos (KG)</option>
                                        <option value="LITROS">Litros (L)</option>
                                        <option value="M3">Metros Cúbicos (m³)</option>
                                    </select>
                                </div>

                                <!-- Eliminar Renglón -->
                                <div class="md:col-span-1 text-right">
                                    <button type="button" @click="eliminarRenglon(index)" x-show="detalles.length > 1"
                                            class="p-2 text-red-500 hover:bg-red-50 rounded-lg transition-colors">
                                        &times;
                                    </button>
                                </div>
                            </div>

                            <div class="pt-2 border-t border-slate-200 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2">
                                <div class="text-xs text-slate-500">
                                    Toneladas equivalentes: <span class="text-emerald-700 font-mono font-bold" x-text="calcularToneladas(item).toFixed(3) + ' TON'"></span>
                                </div>
                                <div class="w-full sm:w-auto">
                                    <input type="file" :name="`detalles[${index}][manifiesto]`" accept=".pdf,.jpg,.png" class="block w-full text-xs text-slate-500 file:mr-2 file:py-1 file:px-2 file:rounded-lg file:border-0 file:text-xs file:bg-slate-200 file:text-slate-700 hover:file:bg-slate-300">
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Totales en Tiempo Real -->
                <div class="bg-slate-100/80 p-4 rounded-xl border border-slate-200 grid grid-cols-1 sm:grid-cols-3 gap-4 text-center">
                    <div>
                        <span class="block text-xs text-slate-500 uppercase font-semibold">Comunes</span>
                        <span class="text-lg font-bold text-slate-800 font-mono" x-text="totalComunes().toFixed(3) + ' TON'"></span>
                    </div>
                    <div>
                        <span class="block text-xs text-slate-500 uppercase font-semibold">Reciclables</span>
                        <span class="text-lg font-bold text-emerald-600 font-mono" x-text="totalReciclables().toFixed(3) + ' TON'"></span>
                    </div>
                    <div>
                        <span class="block text-xs text-slate-500 uppercase font-semibold">Peligrosos</span>
                        <span class="text-lg font-bold text-amber-600 font-mono" x-text="totalPeligrosos().toFixed(3) + ' TON'"></span>
                    </div>
                </div>
            </div>

            <!-- Sección 3: Anexos Generales -->
            <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-4">
                <h2 class="text-base font-semibold text-slate-800 border-b border-slate-100 pb-2">3. Anexos / Manifiestos Generales</h2>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 uppercase mb-1">Subir Manifiestos de Transporte o Certificados Generales (PDF / Imágenes)</label>
                    <input type="file" name="manifiestos[]" multiple accept=".pdf,.jpg,.jpeg,.png"
                           class="block w-full text-sm text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 cursor-pointer">
                </div>
            </div>

            <!-- Botones -->
            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('ciudadano.declaraciones.index') }}" 
                   class="px-5 py-2.5 bg-slate-200 hover:bg-slate-300 text-slate-700 text-sm font-medium rounded-xl transition-colors">
                    Cancelar
                </a>
                <button type="submit" 
                        class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold rounded-xl shadow-sm transition-colors">
                    Enviar Declaración
                </button>
            </div>
        </form>
    </div>

    <script>
    function declaracionResiduos() {
        return {
            frecuencia: 'MENSUAL',
            detalles: [
                { nombre_residuo: 'Desechos Comunes', categoria: 'COMUN', cantidad: 0, unidad_medida: 'KG' }
            ],
            agregarRenglon() {
                this.detalles.push({ nombre_residuo: '', categoria: 'COMUN', cantidad: 0, unidad_medida: 'KG' });
            },
            eliminarRenglon(index) {
                this.detalles.splice(index, 1);
            },
            slugify(text) {
                return text ? text.toString().toLowerCase().trim().replace(/[\s\W-]+/g, '_') : '';
            },
            calcularToneladas(item) {
                const cantidad = parseFloat(item.cantidad) || 0;
                switch(item.unidad_medida) {
                    case 'TON': return cantidad;
                    case 'KG':
                    case 'LITROS': return cantidad * 0.001;
                    case 'M3': return cantidad * 1.0;
                    default: return cantidad * 0.001;
                }
            },
            totalComunes() {
                return this.detalles.filter(i => i.categoria === 'COMUN').reduce((sum, i) => sum + this.calcularToneladas(i), 0);
            },
            totalReciclables() {
                return this.detalles.filter(i => i.categoria === 'RECICLABLE').reduce((sum, i) => sum + this.calcularToneladas(i), 0);
            },
            totalPeligrosos() {
                return this.detalles.filter(i => i.categoria === 'PELIGROSO').reduce((sum, i) => sum + this.calcularToneladas(i), 0);
            }
        }
    }
    </script>
</x-layouts.app>