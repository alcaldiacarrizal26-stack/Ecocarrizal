@props([
    'tramites' => collect(),
    'requisitosEmpresas' => collect(),
])

<section id="recaudos"
         class="py-16 bg-slate-50 border-t border-slate-200"
         x-data="{
             sector: 'ciudadanos',
             tramiteActivo: {{ $tramites->first()?->id ?? 'null' }},
             filtroEmpresa: 'todos',
         }">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

        <!-- Encabezado -->
        <div class="text-center max-w-2xl mx-auto space-y-2">
            <span class="px-3 py-1 text-xs font-bold uppercase tracking-wider rounded-full bg-emerald-100 text-emerald-800 border border-emerald-200">
                Catálogo de Recaudos
            </span>
            <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900">
                Requisitos por Sector
            </h2>
            <p class="text-sm text-slate-500">
                Consulta los recaudos separados para trámites ciudadanos y para el sector empresarial.
            </p>
        </div>

        <!-- Selector de Sector -->
        <div class="flex justify-center">
            <div class="inline-flex p-1.5 bg-white rounded-2xl border border-slate-200 shadow-xs gap-1">
                <button type="button"
                        @click="sector = 'ciudadanos'"
                        :class="sector === 'ciudadanos' ? 'bg-slate-900 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900'"
                        class="px-5 py-2.5 rounded-xl text-xs font-bold transition cursor-pointer">
                    Sector Ciudadano (Trámites)
                </button>
                <button type="button"
                        @click="sector = 'empresas'"
                        :class="sector === 'empresas' ? 'bg-emerald-600 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900'"
                        class="px-5 py-2.5 rounded-xl text-xs font-bold transition cursor-pointer">
                    Sector Empresarial ({{ $requisitosEmpresas->count() }})
                </button>
            </div>
        </div>

        <!-- ======================================================= -->
        <!-- SECTOR 1: CIUDADANOS                                    -->
        <!-- ======================================================= -->
        <div x-show="sector === 'ciudadanos'" class="grid grid-cols-1 md:grid-cols-12 gap-6 items-start">
            <div class="md:col-span-4 space-y-2">
                @forelse($tramites as $tramite)
                    <button type="button"
                            @click="tramiteActivo = {{ $tramite->id }}"
                            :class="tramiteActivo === {{ $tramite->id }} ? 'bg-slate-900 text-white border-slate-900 shadow-sm' : 'bg-white text-slate-700 border-slate-200 hover:border-emerald-400'"
                            class="w-full text-left p-4 rounded-2xl border transition flex items-center justify-between gap-3 cursor-pointer">
                        <div>
                            <p class="text-xs font-bold">{{ $tramite->nombre }}</p>
                            <span class="text-[10px] opacity-75">
                                {{ $tramite->recaudos->count() }} requisito(s)
                                @if($tramite->plazo_dias > 0)
                                    • Plazo: {{ $tramite->plazo_dias }} días
                                @endif
                            </span>
                        </div>
                        <span>&rarr;</span>
                    </button>
                @empty
                    <div class="p-4 bg-white rounded-2xl border border-slate-200 text-xs text-slate-400">
                        No hay trámites ciudadanos disponibles actualmente.
                    </div>
                @endforelse
            </div>

            <div class="md:col-span-8">
                @foreach($tramites as $tramite)
                    @php
                        $porTipo = $tramite->recaudos->groupBy('tipo_requisito');
                    @endphp
                    <div x-show="tramiteActivo === {{ $tramite->id }}"
                         class="bg-white rounded-3xl border border-slate-200 p-6 shadow-xs space-y-6">
                        <div class="border-b border-slate-100 pb-4">
                            <h3 class="text-lg font-bold text-slate-900">{{ $tramite->nombre }}</h3>
                            <p class="text-xs text-slate-500 mt-1">{{ $tramite->descripcion }}</p>
                        </div>

                        @foreach(['OBLIGATORIO' => 'Obligatorios', 'CONDICIONAL' => 'Condicionales', 'DIFERIDO' => 'Diferidos'] as $claveTipo => $tituloTipo)
                            @if(!empty($porTipo[$claveTipo]) && $porTipo[$claveTipo]->count() > 0)
                                <div class="space-y-2.5">
                                    <h4 class="text-xs font-bold uppercase tracking-wider {{ $claveTipo === 'OBLIGATORIO' ? 'text-rose-700' : ($claveTipo === 'CONDICIONAL' ? 'text-amber-700' : 'text-slate-600') }}">
                                        {{ $tituloTipo }}
                                    </h4>
                                    <div class="space-y-2">
                                        @foreach($porTipo[$claveTipo] as $rec)
                                            <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100 flex items-start justify-between gap-3">
                                                <div>
                                                    <p class="text-xs font-bold text-slate-800">{{ $rec->label }}</p>
                                                    @if($rec->hint)
                                                        <p class="text-[11px] text-slate-500 mt-0.5">{{ $rec->hint }}</p>
                                                    @endif
                                                </div>
                                                <span class="px-2 py-0.5 text-[10px] font-mono font-bold rounded-md bg-white border border-slate-200 text-slate-600 shrink-0">
                                                    {{ $rec->tipo_documento }}
                                                </span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        @endforeach

                        <div class="pt-2 flex justify-end">
                            <a href="{{ route('login') }}"
                               class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl transition">
                                Iniciar Solicitud Ciudadana &rarr;
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- ======================================================= -->
        <!-- SECTOR 2: EMPRESARIAL                                   -->
        <!-- ======================================================= -->
        <div x-show="sector === 'empresas'" x-cloak class="bg-white rounded-3xl border border-slate-200 p-6 shadow-xs space-y-6">

            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-slate-100 pb-4">
                <div>
                    <h3 class="text-lg font-bold text-slate-900">Recaudos para el Sector Empresarial</h3>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Consulta los documentos exigidos según la clasificación ambiental de tu empresa.
                    </p>
                </div>

                <!-- Filtros por tipo -->
                <div class="inline-flex p-1 bg-slate-100 rounded-xl gap-1 shrink-0">
                    <button type="button"
                            @click="filtroEmpresa = 'todos'"
                            :class="filtroEmpresa === 'todos' ? 'bg-white text-slate-900 shadow-2xs' : 'text-slate-600'"
                            class="px-3 py-1.5 rounded-lg text-xs font-bold transition cursor-pointer">
                        Todos
                    </button>
                    <button type="button"
                            @click="filtroEmpresa = 'obligatorios'"
                            :class="filtroEmpresa === 'obligatorios' ? 'bg-white text-rose-700 shadow-2xs' : 'text-slate-600'"
                            class="px-3 py-1.5 rounded-lg text-xs font-bold transition cursor-pointer">
                        Obligatorios
                    </button>
                    <button type="button"
                            @click="filtroEmpresa = 'condicionales'"
                            :class="filtroEmpresa === 'condicionales' ? 'bg-white text-amber-700 shadow-2xs' : 'text-slate-600'"
                            class="px-3 py-1.5 rounded-lg text-xs font-bold transition cursor-pointer">
                        Condicionales
                    </button>
                </div>
            </div>

            @php
                // Agrupar por código para no duplicar Póliza / Plan Emergencia
                $requisitosAgrupados = $requisitosEmpresas->groupBy('codigo');
            @endphp

            <!-- Listado de requisitos agrupados -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                @forelse($requisitosAgrupados as $codigo => $variantes)
                    @php
                        $primera = $variantes->first();
                        $roles = $variantes->pluck('aplica_a_rol')->unique()->values();
                        $tipos = $variantes->pluck('tipo_requisito')->unique()->values();
                        $esObligatorio = $tipos->contains('OBLIGATORIO');
                        $esCondicional = $tipos->contains('CONDICIONAL');
                    @endphp

                    <div 
                        x-show="filtroEmpresa === 'todos' 
                            || (filtroEmpresa === 'obligatorios' && {{ $esObligatorio ? 'true' : 'false' }}) 
                            || (filtroEmpresa === 'condicionales' && {{ $esCondicional ? 'true' : 'false' }})"
                        class="p-4 rounded-2xl border bg-slate-50 border-slate-200/80 space-y-1.5">
                        
                        <div class="flex items-start justify-between gap-2">
                            <span class="text-xs font-bold text-slate-900">{{ $primera->nombre }}</span>
                            <div class="flex flex-col gap-1 shrink-0">
                                @foreach($tipos as $tipo)
                                    <span class="px-2 py-0.5 text-[10px] font-bold rounded-md text-center
                                        {{ $tipo === 'OBLIGATORIO' ? 'bg-rose-100 text-rose-800' : '' }}
                                        {{ $tipo === 'CONDICIONAL' ? 'bg-amber-100 text-amber-800' : '' }}
                                        {{ $tipo === 'OPCIONAL' ? 'bg-slate-200 text-slate-700' : '' }}">
                                        {{ $tipo }}
                                    </span>
                                @endforeach
                            </div>
                        </div>

                        @if($primera->descripcion)
                            <p class="text-[11px] text-slate-500">{{ $primera->descripcion }}</p>
                        @endif

                        <div class="flex flex-wrap items-center gap-1.5 pt-1">
                            <span class="inline-block text-[10px] font-mono text-slate-400">
                                {{ $codigo }}
                            </span>
                            <span class="text-slate-300">•</span>
                            <span class="text-[10px] font-semibold text-indigo-700">
                                Aplica a: {{ $roles->implode(', ') }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="md:col-span-2 p-8 text-center text-xs text-slate-400">
                        No hay requisitos empresariales registrados actualmente.
                    </div>
                @endforelse
            </div>

            <div class="pt-2 flex justify-end">
                <a href="{{ route('login') }}"
                class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl transition">
                    Gestionar Registro Empresarial &rarr;
                </a>
            </div>
        </div>

    </div>
</section>