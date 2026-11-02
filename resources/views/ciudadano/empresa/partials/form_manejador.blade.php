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
                 this.rifGeneradorInput = { tipo: 'J', numero: '', digito: '' };
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

        <!-- RIF Cliente Generador Normalizado -->
        <div>
            <label class="block text-xs font-semibold text-slate-500 uppercase mb-1">
                RIF Cliente Generador
                <span x-show="tieneGeneradorFijo" class="text-[10px] text-amber-700 font-bold lowercase">(fijo)</span>
            </label>
            
            <!-- Si usa generador fijo se muestra el input de texto bloqueado -->
            <template x-if="tieneGeneradorFijo">
                <input type="text" name="generador_origen_rif" 
                       x-model="generador_origen_rif" 
                       readonly
                       class="w-full bg-slate-100 text-slate-600 cursor-not-allowed font-medium font-mono border border-slate-300 rounded-xl px-3 py-2 text-sm">
            </template>

            <!-- Si es manual, se despliega el RIF normalizado (Select tipo + Inputs numéricos) -->
            <template x-if="!tieneGeneradorFijo">
                <div>
                    <div class="flex items-center gap-1.5">
                        <select x-model="rifGeneradorInput.tipo" @change="formatGeneradorRif()"
                                class="px-2 py-2.5 rounded-xl border border-slate-300 text-xs font-mono font-bold text-slate-800 bg-slate-50 focus:border-amber-500 focus:ring-1 focus:ring-amber-500">
                            <option value="J">J</option>
                            <option value="G">G</option>
                            <option value="V">V</option>
                            <option value="E">E</option>
                            <option value="P">P</option>
                        </select>
                        <span class="text-slate-400 font-bold">-</span>
                        <input type="text" x-model="rifGeneradorInput.numero" @input="formatGeneradorRif()" maxlength="8" placeholder="12345678"
                               class="w-full px-3 py-2.5 rounded-xl border border-slate-300 text-xs font-mono font-bold tracking-wider text-slate-900 bg-slate-50 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 text-center">
                        <span class="text-slate-400 font-bold">-</span>
                        <input type="text" x-model="rifGeneradorInput.digito" @input="formatGeneradorRif()" maxlength="1" placeholder="0"
                               class="w-12 px-2 py-2.5 rounded-xl border border-slate-300 text-xs font-mono font-bold tracking-wider text-slate-900 bg-slate-50 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 text-center">
                    </div>
                    <input type="hidden" name="generador_origen_rif" x-model="generador_origen_rif">
                </div>
            </template>
        </div>

        <!-- Placa del Vehículo Normalizada (Máximo 7 caracteres, Mayúsculas Automáticas) -->
        <div>
            <label class="block text-xs font-semibold text-slate-500 uppercase mb-1">Placa del Vehículo de Transportista</label>
            <input type="text" name="vehiculo_placa" 
                   x-model="vehiculo_placa" 
                   @input="formatPlaca()"
                   maxlength="7"
                   placeholder="Ej: AB123CD"
                   class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-sm font-mono uppercase text-slate-800 placeholder-slate-400 focus:ring-2 focus:ring-amber-500 focus:outline-none">
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