@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" class="flex items-center justify-between text-xs">
        <div class="flex justify-between flex-1 sm:hidden">
            {{-- Botón Anterior (Móvil) --}}
            @if ($paginator->onFirstPage())
                <span class="relative inline-flex items-center px-4 py-2.5 text-xs font-semibold text-slate-400 bg-white border border-slate-200 cursor-default rounded-xl">
                    Anterior
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="relative inline-flex items-center px-4 py-2.5 text-xs font-semibold text-slate-700 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 transition">
                    Anterior
                </a>
            @endif

            {{-- Botón Siguiente (Móvil) --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="relative inline-flex items-center px-4 py-2.5 ml-3 text-xs font-semibold text-slate-700 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 transition">
                    Siguiente
                </a>
            @else
                <span class="relative inline-flex items-center px-4 py-2.5 ml-3 text-xs font-semibold text-slate-400 bg-white border border-slate-200 cursor-default rounded-xl">
                    Siguiente
                </span>
            @endif
        </div>

        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
            <div>
                <p class="text-xs text-slate-500 font-medium">
                    Mostrando
                    <span class="font-bold text-slate-700">{{ $paginator->firstItem() }}</span>
                    a
                    <span class="font-bold text-slate-700">{{ $paginator->lastItem() }}</span>
                    de
                    <span class="font-bold text-slate-700">{{ $paginator->total() }}</span>
                    resultados
                </p>
            </div>

            <div>
                <span class="relative z-0 inline-flex shadow-2xs rounded-xl overflow-hidden gap-1">
                    {{-- Anterior --}}
                    @if ($paginator->onFirstPage())
                        <span aria-disabled="true" aria-label="Anterior" class="relative inline-flex items-center px-3 py-2 text-xs font-semibold text-slate-300 bg-white border border-slate-200 cursor-default rounded-lg">
                            &laquo; Anterior
                        </span>
                    @else
                        <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="relative inline-flex items-center px-3 py-2 text-xs font-semibold text-slate-600 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 transition">
                            &laquo; Anterior
                        </a>
                    @endif

                    {{-- Siguiente --}}
                    @if ($paginator->hasMorePages())
                        <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="relative inline-flex items-center px-3 py-2 text-xs font-semibold text-slate-600 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 transition ml-1">
                            Siguiente &raquo;
                        </a>
                    @else
                        <span aria-disabled="true" aria-label="Siguiente" class="relative inline-flex items-center px-3 py-2 text-xs font-semibold text-slate-300 bg-white border border-slate-200 cursor-default rounded-lg ml-1">
                            Siguiente &raquo;
                        </span>
                    @endif
                </span>
            </div>
        </div>
    </nav>
@endif