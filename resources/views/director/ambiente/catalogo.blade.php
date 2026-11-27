<x-layouts.app title="Catálogo de Trámites y Recaudos" active="ambiente_catalogo">
<div class="p-6 max-w-5xl mx-auto space-y-4 bg-slate-50 min-h-screen" 
     x-data="{ 
        expandido: null,
        modalTramite: { open: {{ $errors->any() && old('nombre') ? 'true' : 'false' }}, modo: 'crear', data: { nombre: '{{ old('nombre') }}',descripcion: '{{ old('descripcion') }}',plazo_dias: '{{ old('plazo_dias', 0) }}', activo: true } },        
        modalRecaudo: { open: false, modo: 'crear', data: { tipo_requisito: 'OBLIGATORIO', tipo_documento: 'FORMAL' }, tramiteId: null, tramiteNombre: '' }
     }">

    <!-- ========================================================= -->
    <!-- HEADER -->
    <!-- ========================================================= -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 tracking-tight">Catálogo de Trámites y Recaudos</h1>
            <p class="text-sm text-slate-500 mt-1">
                Gestiona los trámites civiles y configura los requisitos exigidos para cada uno.
            </p>
        </div>
        <button type="button" 
                @click="modalTramite = { open: true, modo: 'crear', data: { activo: true, plazo_dias: 0 } }"
                class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow-sm transition cursor-pointer shrink-0">
            + Nuevo Trámite
        </button>
    </div>

    <!-- Alertas Flash -->
    @if(session('success'))
        <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center justify-between">
            <span>✅ {{ session('success') }}</span>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-600 font-bold">&times;</button>
        </div>
    @endif

    @if($errors->any())
        <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs">
            <strong class="block mb-1">Corrige los siguientes errores:</strong>
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- ========================================================= -->
    <!-- LISTA DE TRÁMITES (ACORDEÓN) -->
    <!-- ========================================================= -->
    <div class="space-y-3">
        @forelse($tramites as $tramite)
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden transition-shadow hover:shadow-md">
                
                <!-- Cabecera del Acordeón (Siempre visible) -->
                <div @click="expandido = expandido === {{ $tramite->id }} ? null : {{ $tramite->id }}" 
                     class="p-4 flex items-center justify-between gap-3 cursor-pointer hover:bg-slate-50 transition select-none">
                    <div class="flex items-center gap-3 flex-1 min-w-0">
                        <!-- Contador de Recaudos -->
                        <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-700 flex flex-col items-center justify-center font-bold text-xs shrink-0 border border-indigo-100">
                            <span class="text-sm leading-none">{{ $tramite->recaudos->count() }}</span>
                            <span class="text-[8px] leading-none mt-0.5">DOCS</span>
                        </div>
                        <div class="min-w-0">
                            <div class="flex items-center gap-2 flex-wrap">
                                <h3 class="font-bold text-slate-800 text-sm truncate">{{ $tramite->nombre }}</h3>
                                <span class="px-1.5 py-0.5 text-[10px] font-mono rounded bg-slate-100 text-slate-600 border border-slate-200">{{ $tramite->slug }}</span>
                                <span class="px-1.5 py-0.5 text-[10px] font-bold rounded {{ $tramite->activo ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-200 text-slate-600' }}">
                                    {{ $tramite->activo ? 'ACTIVO' : 'INACTIVO' }}
                                </span>
                            </div>
                            <p class="text-[11px] text-slate-500 truncate mt-0.5">
                                Plazo: {{ $tramite->plazo_dias }} días • {{ Str::limit($tramite->descripcion, 80) }}
                            </p>
                        </div>
                    </div>
                    <svg class="w-5 h-5 text-slate-400 shrink-0 transition-transform duration-200" 
                         :class="expandido === {{ $tramite->id }} ? 'rotate-180' : ''"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </div>

                <!-- Contenido Desplegable -->
                <div x-show="expandido === {{ $tramite->id }}" x-cloak x-transition.opacity.duration.200ms class="border-t border-slate-100">
                    
                    <!-- Panel de Acciones del Trámite -->
                    <div class="px-4 py-3 bg-slate-50/70 flex flex-wrap gap-2 items-center justify-between">
                        <p class="text-xs text-slate-600 flex-1 min-w-[200px]">{{ $tramite->descripcion }}</p>
                        <div class="flex items-center gap-1.5 shrink-0">
                            <button @click.stop="modalRecaudo = { open: true, modo: 'crear', data: { tipo_requisito: 'OBLIGATORIO', tipo_documento: 'FORMAL' }, tramiteId: {{ $tramite->id }}, tramiteNombre: @js($tramite->nombre) }"
                                    class="px-2.5 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-[11px] font-bold rounded-lg transition cursor-pointer flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                Añadir
                            </button>
                            <button @click.stop="modalTramite = { open: true, modo: 'editar', data: @js([
                                        'id' => $tramite->id, 
                                        'nombre' => $tramite->nombre, 
                                        'slug' => $tramite->slug, 
                                        'descripcion' => $tramite->descripcion, 
                                        'plazo_dias' => $tramite->plazo_dias, 
                                        'activo' => (bool) $tramite->activo
                                    ]) }"
                                    class="px-2.5 py-1.5 bg-white hover:bg-slate-100 text-slate-700 border border-slate-200 text-[11px] font-semibold rounded-lg transition cursor-pointer">
                                Editar
                            </button>
                            <form method="POST" action="{{ route('director.ambiente.catalogo.tramites.destroy', $tramite) }}" 
                                  onsubmit="return confirm('¿Eliminar este trámite y TODOS sus recaudos? Esta acción no se puede deshacer.');" class="inline-block">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-2.5 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 text-[11px] font-semibold rounded-lg transition cursor-pointer">
                                    Eliminar
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Lista de Recaudos del Trámite -->
                    <div class="divide-y divide-slate-100">
                        @forelse($tramite->recaudos as $recaudo)
                            <div class="px-4 py-3 flex items-center justify-between gap-3 hover:bg-slate-50/50 transition">
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span class="text-xs font-bold text-slate-800">{{ $recaudo->label }}</span>
                                        <span class="px-2 py-0.5 text-[9px] font-bold rounded-md {{ $recaudo->tipo_requisito === 'OBLIGATORIO' ? 'bg-rose-100 text-rose-800' : ($recaudo->tipo_requisito === 'CONDICIONAL' ? 'bg-amber-100 text-amber-800' : 'bg-slate-100 text-slate-700') }}">
                                            {{ $recaudo->tipo_requisito }}
                                        </span>
                                        <span class="px-2 py-0.5 text-[9px] font-mono rounded bg-indigo-50 text-indigo-700 border border-indigo-100">
                                            {{ $recaudo->tipo_documento }}
                                        </span>
                                        <span class="text-[10px] text-slate-400 font-mono">{{ $recaudo->key }}</span>
                                    </div>
                                    @if($recaudo->hint)
                                        <p class="text-[11px] text-slate-500 truncate mt-0.5">{{ $recaudo->hint }}</p>
                                    @endif
                                </div>
                                <div class="flex items-center gap-1 shrink-0">
                                    <button @click.stop="modalRecaudo = { open: true, modo: 'editar', data: @js([
                                                'id' => $recaudo->id, 
                                                'label' => $recaudo->label, 
                                                'tipo_requisito' => $recaudo->tipo_requisito, 
                                                'tipo_documento' => $recaudo->tipo_documento, 
                                                'hint' => $recaudo->hint
                                            ]), tramiteId: {{ $tramite->id }}, tramiteNombre: @js($tramite->nombre) }"
                                            class="p-1.5 text-indigo-600 hover:bg-indigo-50 rounded-lg transition cursor-pointer" title="Editar recaudo">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </button>
                                    <form method="POST" action="{{ route('director.ambiente.catalogo.recaudos.destroy', $recaudo) }}" 
                                          onsubmit="return confirm('¿Quitar este recaudo del trámite?');" class="inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 text-rose-600 hover:bg-rose-50 rounded-lg transition cursor-pointer" title="Quitar recaudo">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <div class="p-6 text-center text-xs text-slate-400 italic">
                                No hay requisitos configurados. Pulsa "Añadir" para crear el primero.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-2xl border-2 border-dashed border-slate-300 p-12 text-center space-y-2">
                <svg class="w-12 h-12 text-slate-300 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <p class="text-sm font-bold text-slate-700">No hay trámites en el catálogo</p>
                <p class="text-xs text-slate-500">Crea tu primer tipo de trámite para empezar a configurar requisitos.</p>
            </div>
        @endforelse
    </div>

    <!-- ========================================================= -->
    <!-- MODAL: CREAR / EDITAR TRÁMITE -->
    <!-- ========================================================= -->
    <div x-show="modalTramite.open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs" @click="modalTramite.open = false"></div>
        <div class="relative bg-white rounded-2xl shadow-xl max-w-md w-full p-6 z-10 space-y-4 max-h-[90vh] overflow-y-auto">
            
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div>
                    <h3 class="text-base font-bold text-slate-800" x-text="modalTramite.modo === 'crear' ? 'Nuevo Tipo de Trámite' : 'Editar Trámite'"></h3>
                    <p class="text-[11px] text-slate-500">Define los datos generales y el plazo del trámite.</p>
                </div>
                <button type="button" @click="modalTramite.open = false" class="text-slate-400 hover:text-slate-600 text-xl font-bold leading-none cursor-pointer">&times;</button>
            </div>

            <form :action="modalTramite.modo === 'crear' 
                    ? '{{ route('director.ambiente.catalogo.tramites.store') }}' 
                    : '/director/ambiente/catalogo/tramites/' + modalTramite.data.id" 
                  method="POST" class="space-y-4 text-xs">
                @csrf
                <template x-if="modalTramite.modo === 'editar'">
                    <input type="hidden" name="_method" value="PUT">
                </template>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Nombre del Trámite *</label>
                    <input type="text" name="nombre" x-model="modalTramite.data.nombre" required placeholder="Ej: Solicitud de Poda y Deshierbe" class="w-full rounded-xl border-slate-300 bg-slate-50 px-3 py-2">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Slug (opcional)</label>
                        <input type="text" name="slug" x-model="modalTramite.data.slug" :readonly="modalTramite.modo === 'editar'" placeholder="poda_deshierbe" class="w-full rounded-xl border-slate-300 bg-slate-50 px-3 py-2 font-mono text-[11px]" :class="modalTramite.modo === 'editar' ? 'opacity-60 cursor-not-allowed' : ''">
                        <p class="text-[9px] text-slate-400 mt-0.5">Se genera automático si lo dejas vacío.</p>
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Plazo (días) *</label>
                        <input type="number" name="plazo_dias" x-model="modalTramite.data.plazo_dias" min="0" required class="w-full rounded-xl border-slate-300 bg-slate-50 px-3 py-2">
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Descripción *</label>
                    <textarea name="descripcion" x-model="modalTramite.data.descripcion" rows="3" required placeholder="Descripción breve del trámite..." class="w-full rounded-xl border-slate-300 bg-slate-50 px-3 py-2 resize-none"></textarea>
                </div>

                <div class="flex items-center justify-between p-3 bg-slate-50 rounded-xl border border-slate-200">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" x-model="modalTramite.data.activo" class="rounded text-emerald-600">
                        <span class="font-semibold text-slate-700">Visible en el sistema (Activo)</span>
                    </label>
                    <input type="hidden" name="activo" :value="modalTramite.data.activo ? 1 : 0">
                </div>

                <div class="flex justify-end gap-2 pt-3 border-t border-slate-100">
                    <button type="button" @click="modalTramite.open = false" class="px-4 py-2 bg-slate-100 text-slate-700 font-semibold rounded-xl cursor-pointer">Cancelar</button>
                    <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl cursor-pointer">
                        <span x-text="modalTramite.modo === 'crear' ? 'Crear Trámite' : 'Guardar Cambios'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ========================================================= -->
    <!-- MODAL: CREAR / EDITAR RECAUDO -->
    <!-- ========================================================= -->
    <div x-show="modalRecaudo.open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs" @click="modalRecaudo.open = false"></div>
        <div class="relative bg-white rounded-2xl shadow-xl max-w-md w-full p-6 z-10 space-y-4 max-h-[90vh] overflow-y-auto">
            
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div>
                    <h3 class="text-base font-bold text-slate-800" x-text="modalRecaudo.modo === 'crear' ? 'Nuevo Recaudo' : 'Editar Recaudo'"></h3>
                    <p class="text-[11px] text-slate-500">
                        Trámite: <strong class="text-indigo-700" x-text="modalRecaudo.tramiteNombre"></strong>
                    </p>
                </div>
                <button type="button" @click="modalRecaudo.open = false" class="text-slate-400 hover:text-slate-600 text-xl font-bold leading-none cursor-pointer">&times;</button>
            </div>

            <form :action="modalRecaudo.modo === 'crear' 
                    ? '/director/ambiente/catalogo/tramites/' + modalRecaudo.tramiteId + '/recaudos' 
                    : '/director/ambiente/catalogo/recaudos/' + modalRecaudo.data.id" 
                  method="POST" class="space-y-4 text-xs">
                @csrf
                <template x-if="modalRecaudo.modo === 'editar'">
                    <input type="hidden" name="_method" value="PUT">
                </template>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Nombre del Recaudo *</label>
                    <input type="text" name="label" x-model="modalRecaudo.data.label" required placeholder="Ej: Copia de la Cédula de Identidad" class="w-full rounded-xl border-slate-300 bg-slate-50 px-3 py-2">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Tipo de Requisito *</label>
                        <select name="tipo_requisito" x-model="modalRecaudo.data.tipo_requisito" required class="w-full rounded-xl border-slate-300 bg-slate-50 px-3 py-2">
                            <option value="OBLIGATORIO">Obligatorio</option>
                            <option value="CONDICIONAL">Condicional</option>
                            <option value="OPCIONAL">Opcional</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Tipo de Documento *</label>
                        <select name="tipo_documento" x-model="modalRecaudo.data.tipo_documento" required class="w-full rounded-xl border-slate-300 bg-slate-50 px-3 py-2">
                            <option value="FORMAL">Formal (PDF/Imagen)</option>
                            <option value="FOTO">Fotografía</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Indicaciones / Hint</label>
                    <input type="text" name="hint" x-model="modalRecaudo.data.hint" placeholder="Ej: PDF legible, no mayor a 5MB" class="w-full rounded-xl border-slate-300 bg-slate-50 px-3 py-2">
                    <p class="text-[9px] text-slate-400 mt-0.5">Se le mostrará al ciudadano para guiarlo.</p>
                </div>

                <div class="flex justify-end gap-2 pt-3 border-t border-slate-100">
                    <button type="button" @click="modalRecaudo.open = false" class="px-4 py-2 bg-slate-100 text-slate-700 font-semibold rounded-xl cursor-pointer">Cancelar</button>
                    <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl cursor-pointer">
                        <span x-text="modalRecaudo.modo === 'crear' ? 'Agregar Recaudo' : 'Guardar Cambios'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
</x-layouts.app>