<x-layouts.app title="Catálogo de Requisitos Empresariales" active="residuos_catalogo">
<div class="p-6 max-w-5xl mx-auto space-y-4 bg-slate-50 min-h-screen"
     x-data="{
        expandido: null,
        filtroRol: 'TODOS',
        modalRequisito: {
            open: false,
            modo: 'crear',
            data: { activo: true, orden: 0, es_vitalicio_default: false, tipo_requisito: 'OBLIGATORIO', aplica_a_rol: 'TODOS', condicion_flag: '' }
        },

        abrirCrear() {
            this.modalRequisito = {
                open: true,
                modo: 'crear',
                data: { activo: true, orden: 0, es_vitalicio_default: false, tipo_requisito: 'OBLIGATORIO', aplica_a_rol: 'TODOS', condicion_flag: '', nombre: '', codigo: '', descripcion: '' }
            };
        },

        abrirEditar(req) {
            this.modalRequisito = {
                open: true,
                modo: 'editar',
                data: {
                    id: req.id,
                    nombre: req.nombre,
                    codigo: req.codigo,
                    descripcion: req.descripcion || '',
                    tipo_requisito: req.tipo_requisito,
                    condicion_flag: req.condicion_flag || '',
                    aplica_a_rol: req.aplica_a_rol,
                    es_vitalicio_default: !!req.es_vitalicio_default,
                    orden: req.orden,
                    activo: !!req.activo
                }
            };
        }
     }">

    <!-- HEADER -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 tracking-tight">Catálogo de Requisitos Empresariales</h1>
            <p class="text-sm text-slate-500 mt-1">
                Gestiona los documentos exigidos a empresas según su rol ambiental y condiciones operativas.
            </p>
        </div>
        <button type="button" @click="abrirCrear()" class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow-sm transition cursor-pointer shrink-0">
            + Nuevo Requisito
        </button>
    </div>

    <!-- ALERTAS FLASH -->
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

    <!-- FILTRO POR ROL -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm flex flex-wrap items-center gap-2">
        <span class="text-xs font-bold text-slate-500 uppercase mr-2">Filtrar por rol:</span>
        <button type="button" @click="filtroRol = 'TODOS'" :class="filtroRol === 'TODOS' ? 'bg-slate-900 text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'" class="px-3 py-1.5 rounded-lg text-xs font-bold transition cursor-pointer">
            Todos ({{ $requisitos->count() }})
        </button>
        <button type="button" @click="filtroRol = 'GENERADOR'" :class="filtroRol === 'GENERADOR' ? 'bg-emerald-600 text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'" class="px-3 py-1.5 rounded-lg text-xs font-bold transition cursor-pointer">
            Generadores ({{ $requisitos->where('aplica_a_rol', 'GENERADOR')->count() }})
        </button>
        <button type="button" @click="filtroRol = 'MANEJADOR'" :class="filtroRol === 'MANEJADOR' ? 'bg-amber-600 text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'" class="px-3 py-1.5 rounded-lg text-xs font-bold transition cursor-pointer">
            Manejadores ({{ $requisitos->where('aplica_a_rol', 'MANEJADOR')->count() }})
        </button>
        <button type="button" @click="filtroRol = 'NO_APLICA'" :class="filtroRol === 'NO_APLICA' ? 'bg-slate-600 text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'" class="px-3 py-1.5 rounded-lg text-xs font-bold transition cursor-pointer">
            No Aplica ({{ $requisitos->where('aplica_a_rol', 'NO_APLICA')->count() }})
        </button>
        <button type="button" @click="filtroRol = 'GLOBALES'" :class="filtroRol === 'GLOBALES' ? 'bg-indigo-600 text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'" class="px-3 py-1.5 rounded-lg text-xs font-bold transition cursor-pointer">
            Globales/Todos ({{ $requisitos->where('aplica_a_rol', 'TODOS')->count() }})
        </button>
    </div>

    <!-- ACORDEÓN AGRUPADO POR CÓDIGO -->
    <div class="space-y-3">
        @forelse($agrupados as $codigo => $variantes)
            @php
                $primera = $variantes->first();
                $rolesVariantes = $variantes->pluck('aplica_a_rol')->unique()->values();
            @endphp
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden transition-shadow hover:shadow-md">
                <!-- Cabecera del acordeón -->
                <div @click="expandido = expandido === '{{ $codigo }}' ? null : '{{ $codigo }}'" class="p-4 flex items-center justify-between gap-3 cursor-pointer hover:bg-slate-50 transition select-none">
                    <div class="flex items-center gap-3 flex-1 min-w-0">
                        <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-700 flex flex-col items-center justify-center font-bold text-xs shrink-0 border border-indigo-100">
                            <span class="text-sm leading-none">{{ $variantes->count() }}</span>
                            <span class="text-[8px] leading-none mt-0.5">{{ $variantes->count() === 1 ? 'VAR' : 'VARS' }}</span>
                        </div>
                        <div class="min-w-0">
                            <div class="flex items-center gap-2 flex-wrap">
                                <h3 class="font-bold text-slate-800 text-sm truncate">{{ $primera->nombre }}</h3>
                                <span class="px-1.5 py-0.5 text-[10px] font-mono rounded bg-slate-100 text-slate-600 border border-slate-200">{{ $codigo }}</span>
                                @foreach($rolesVariantes as $rolV)
                                    <span class="px-1.5 py-0.5 text-[9px] font-bold rounded
                                        {{ $rolV === 'GENERADOR' ? 'bg-emerald-100 text-emerald-800' : '' }}
                                        {{ $rolV === 'MANEJADOR' ? 'bg-amber-100 text-amber-800' : '' }}
                                        {{ $rolV === 'NO_APLICA' ? 'bg-slate-200 text-slate-700' : '' }}
                                        {{ $rolV === 'TODOS' ? 'bg-indigo-100 text-indigo-800' : '' }}">
                                        {{ $rolV }}
                                    </span>
                                @endforeach
                            </div>
                            <p class="text-[11px] text-slate-500 truncate mt-0.5">{{ Str::limit($primera->descripcion, 90) }}</p>
                        </div>
                    </div>
                    <svg class="w-5 h-5 text-slate-400 shrink-0 transition-transform duration-200" :class="expandido === '{{ $codigo }}' ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </div>

                <!-- Contenido: variantes del código -->
                <div x-show="expandido === '{{ $codigo }}'" x-cloak x-transition.opacity.duration.200ms class="border-t border-slate-100">
                    <div class="divide-y divide-slate-100">
                        @foreach($variantes as $req)
                            <div x-show="filtroRol === 'TODOS' || filtroRol === '{{ $req->aplica_a_rol }}' || (filtroRol === 'GLOBALES' && '{{ $req->aplica_a_rol }}' === 'TODOS')"
                                 class="p-4 flex items-center justify-between gap-3 hover:bg-slate-50/50 transition">
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span class="px-2 py-0.5 text-[10px] font-bold rounded
                                            {{ $req->aplica_a_rol === 'GENERADOR' ? 'bg-emerald-100 text-emerald-800' : '' }}
                                            {{ $req->aplica_a_rol === 'MANEJADOR' ? 'bg-amber-100 text-amber-800' : '' }}
                                            {{ $req->aplica_a_rol === 'NO_APLICA' ? 'bg-slate-200 text-slate-700' : '' }}
                                            {{ $req->aplica_a_rol === 'TODOS' ? 'bg-indigo-100 text-indigo-800' : '' }}">
                                            {{ $req->aplica_a_rol }}
                                        </span>
                                        <span class="px-2 py-0.5 text-[10px] font-bold rounded uppercase
                                            {{ $req->tipo_requisito === 'OBLIGATORIO' ? 'bg-rose-100 text-rose-800' : '' }}
                                            {{ $req->tipo_requisito === 'CONDICIONAL' ? 'bg-amber-100 text-amber-800' : '' }}
                                            {{ $req->tipo_requisito === 'OPCIONAL' ? 'bg-slate-100 text-slate-700' : '' }}">
                                            {{ $req->tipo_requisito }}
                                        </span>
                                        @if($req->condicion_flag)
                                            <span class="px-2 py-0.5 text-[10px] font-mono rounded bg-purple-100 text-purple-800 border border-purple-200">
                                                SI: {{ str_replace('_', ' ', $req->condicion_flag) }}
                                            </span>
                                        @endif
                                        @if($req->es_vitalicio_default)
                                            <span class="px-2 py-0.5 text-[10px] font-bold rounded bg-cyan-100 text-cyan-800">
                                                VITALICIO
                                            </span>
                                        @endif
                                        <span class="px-2 py-0.5 text-[10px] font-bold rounded-full
                                            {{ $req->activo ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-200 text-slate-600' }}">
                                            {{ $req->activo ? 'ACTIVO' : 'INACTIVO' }}
                                        </span>
                                    </div>
                                    <p class="text-[11px] text-slate-500 mt-1">{{ $req->descripcion ?? 'Sin descripción' }}</p>
                                </div>
                                <div class="flex items-center gap-1 shrink-0">
                                    <button @click.stop='abrirEditar(@js($req))' class="p-2 text-indigo-600 hover:bg-indigo-50 rounded-lg transition cursor-pointer" title="Editar">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </button>
                                    <form method="POST" action="{{ route('director.residuos.catalogo.toggle', $req) }}" class="inline-block">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="p-2 rounded-lg transition cursor-pointer {{ $req->activo ? 'text-amber-600 hover:bg-amber-50' : 'text-emerald-600 hover:bg-emerald-50' }}" title="{{ $req->activo ? 'Desactivar' : 'Activar' }}">
                                            @if($req->activo)
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                            @else
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                            @endif
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('director.residuos.catalogo.destroy', $req) }}" onsubmit="return confirm('¿Eliminar esta variante del requisito? No se puede deshacer.');" class="inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 text-rose-600 hover:bg-rose-50 rounded-lg transition cursor-pointer" title="Eliminar">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-2xl border-2 border-dashed border-slate-300 p-12 text-center space-y-2">
                <p class="text-sm font-bold text-slate-700">No hay requisitos en el catálogo</p>
                <p class="text-xs text-slate-500">Pulsa "Nuevo Requisito" para crear el primero.</p>
            </div>
        @endforelse
    </div>

    <!-- ===================================================== -->
    <!-- MODAL: CREAR / EDITAR REQUISITO                       -->
    <!-- ===================================================== -->
    <div x-show="modalRequisito.open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs" @click="modalRequisito.open = false"></div>
        <div class="relative bg-white rounded-2xl shadow-xl max-w-2xl w-full p-6 z-10 space-y-4 max-h-[90vh] overflow-y-auto">

            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div>
                    <h3 class="text-base font-bold text-slate-800" x-text="modalRequisito.modo === 'crear' ? 'Nuevo Requisito Empresarial' : 'Editar Requisito'"></h3>
                    <p class="text-[11px] text-slate-500">Configura el documento, rol aplicable y condicionalidad.</p>
                </div>
                <button type="button" @click="modalRequisito.open = false" class="text-slate-400 hover:text-slate-600 text-xl font-bold leading-none cursor-pointer">&times;</button>
            </div>

            <form :action="modalRequisito.modo === 'crear' ? '{{ route('director.residuos.catalogo.store') }}' : '/director/residuos/catalogo/' + modalRequisito.data.id"
                  method="POST" class="space-y-4 text-xs">
                @csrf
                <template x-if="modalRequisito.modo === 'editar'">
                    <input type="hidden" name="_method" value="PUT">
                </template>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Código *</label>
                        <input type="text" name="codigo" x-model="modalRequisito.data.codigo" :readonly="modalRequisito.modo === 'editar'" required placeholder="DOC_RENMA" class="w-full rounded-xl border-slate-300 bg-slate-50 px-3 py-2 font-mono uppercase" :class="modalRequisito.modo === 'editar' ? 'opacity-60 cursor-not-allowed' : ''">
                        <p class="text-[9px] text-slate-400 mt-0.5">Único por rol. Ej: DOC_POLIZA para MANEJADOR y otro DOC_POLIZA para GENERADOR.</p>
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Rol Aplicable *</label>
                        <select name="aplica_a_rol" x-model="modalRequisito.data.aplica_a_rol" :disabled="modalRequisito.modo === 'editar'" required class="w-full rounded-xl border-slate-300 bg-slate-50 px-3 py-2" :class="modalRequisito.modo === 'editar' ? 'opacity-60 cursor-not-allowed' : ''">
                            <option value="TODOS">TODOS (todos los roles)</option>
                            <option value="GENERADOR">Solo GENERADOR</option>
                            <option value="MANEJADOR">Solo MANEJADOR</option>
                            <option value="NO_APLICA">Solo NO_APLICA</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Nombre del Requisito *</label>
                    <input type="text" name="nombre" x-model="modalRequisito.data.nombre" required placeholder="Ej: Póliza de Seguro de Responsabilidad Ambiental" class="w-full rounded-xl border-slate-300 bg-slate-50 px-3 py-2">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Descripción / Indicaciones</label>
                    <textarea name="descripcion" x-model="modalRequisito.data.descripcion" rows="2" placeholder="Indica al ciudadano qué debe consignar..." class="w-full rounded-xl border-slate-300 bg-slate-50 px-3 py-2 resize-none"></textarea>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Tipo de Requisito *</label>
                        <select name="tipo_requisito" x-model="modalRequisito.data.tipo_requisito" required class="w-full rounded-xl border-slate-300 bg-slate-50 px-3 py-2">
                            <option value="OBLIGATORIO">OBLIGATORIO (debe subirse siempre)</option>
                            <option value="CONDICIONAL">CONDICIONAL (según flag)</option>
                            <option value="OPCIONAL">OPCIONAL (nunca obliga)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Condición Disparadora</label>
                        <select name="condicion_flag" x-model="modalRequisito.data.condicion_flag" class="w-full rounded-xl border-slate-300 bg-slate-50 px-3 py-2">
                            <option value="">Sin condición (siempre aplica según rol)</option>
                            <option value="genera_peligrosos">Si genera residuos peligrosos</option>
                            <option value="genera_efluentes">Si genera efluentes líquidos</option>
                            <option value="genera_emisiones">Si genera emisiones atmosféricas</option>
                            <option value="genera_riesgo_ambiental">Si genera riesgo ambiental (cualquiera de los 3)</option>
                            <option value="usa_pozo_agua">Si usa pozo de agua</option>
                            <option value="modalidad_externa">Si la modalidad de manejo es EXTERNA</option>
                            <option value="es_persona_juridica">Si el RIF es de persona jurídica (J o G)</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Orden de visualización</label>
                        <input type="number" name="orden" x-model="modalRequisito.data.orden" min="0" class="w-full rounded-xl border-slate-300 bg-slate-50 px-3 py-2">
                    </div>
                    <div class="flex items-center pt-5">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="es_vitalicio_default" value="1" x-model="modalRequisito.data.es_vitalicio_default" class="rounded text-emerald-600">
                            <span class="font-semibold text-slate-700">Marcar Vitalicio por defecto</span>
                        </label>
                    </div>
                    <div class="flex items-center pt-5">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="activo" value="1" x-model="modalRequisito.data.activo" class="rounded text-emerald-600">
                            <span class="font-semibold text-slate-700">Activo / Visible</span>
                        </label>
                    </div>
                </div>

                <div class="flex justify-end gap-2 pt-3 border-t border-slate-100">
                    <button type="button" @click="modalRequisito.open = false" class="px-4 py-2 bg-slate-100 text-slate-700 font-semibold rounded-xl cursor-pointer">Cancelar</button>
                    <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl cursor-pointer">
                        <span x-text="modalRequisito.modo === 'crear' ? 'Crear Requisito' : 'Guardar Cambios'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
</x-layouts.app>