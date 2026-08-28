<x-layouts.app active="inicio" title="Inicio - Panel Ciudadano">

    <!-- Encabezado de bienvenida -->
    <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-800 tracking-tight sm:text-3xl">
                ¡Hola, {{ strtok(auth()->user()->nombre ?? 'Ciudadano', ' ') }}! 👋
            </h1>
            <p class="text-slate-500 text-sm mt-1">
                Bienvenido al sistema de autogestión ambiental del Municipio Carrizal.
            </p>
        </div>
        <div class="flex gap-3">
            <a href="/solicitudes/nueva" class="inline-flex items-center justify-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-sm px-4 py-2.5 rounded-xl shadow-lg shadow-emerald-600/20 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                <span>Nuevo Trámite</span>
            </a>
        </div>
    </div>

    <!-- Accesos Rápidos a Servicios -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        
        <!-- Poda y Tala -->
        <a href="/solicitudes/crear?tipo=poda" class="group bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-md hover:border-emerald-500/50 transition-all">
            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4m-6 7h18M5 19v-4m-2 2h4m11-12v4m-2-2h4m-2 14v-4m-2 2h4"/>
                </svg>
            </div>
            <h3 class="font-bold text-slate-800 text-sm group-hover:text-emerald-600 transition-colors">Poda y Tala</h3>
            <p class="text-xs text-slate-500 mt-1">Permisos de intervención vegetal urbana.</p>
        </a>

        <!-- Trasplante -->
        <a href="/solicitudes/crear?tipo=trasplante" class="group bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-md hover:border-emerald-500/50 transition-all">
            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                </svg>
            </div>
            <h3 class="font-bold text-slate-800 text-sm group-hover:text-emerald-600 transition-colors">Trasplante</h3>
            <p class="text-xs text-slate-500 mt-1">Reubicación de ejemplares botánicos.</p>
        </a>

        <!-- Conformidad Ambiental -->
        <a href="/solicitudes/crear?tipo=conformidad" class="group bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-md hover:border-emerald-500/50 transition-all">
            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <h3 class="font-bold text-slate-800 text-sm group-hover:text-emerald-600 transition-colors">Conformidad</h3>
            <p class="text-xs text-slate-500 mt-1">Certificación para locales y proyectos.</p>
        </a>

        <!-- RACDA -->
        <a href="/solicitudes/crear?tipo=racda" class="group bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-md hover:border-emerald-500/50 transition-all">
            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                </svg>
            </div>
            <h3 class="font-bold text-slate-800 text-sm group-hover:text-emerald-600 transition-colors">RACDA</h3>
            <p class="text-xs text-slate-500 mt-1">Registro de Actividades Degradantes.</p>
        </a>

    </div>

    <!-- Sección Estado de Trámites Recientes -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h2 class="font-bold text-slate-800 text-base">Trámites Recientes</h2>
                <p class="text-xs text-slate-500 mt-0.5">Estado de tus solicitudes en curso e inspecciones.</p>
            </div>
            <a href="/solicitudes" class="text-xs font-semibold text-emerald-600 hover:text-emerald-700">
                Ver todos (0)
            </a>
        </div>

        <!-- Estado vacío o Lista de registros -->
        <div class="p-8 text-center">
            <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <h4 class="text-sm font-semibold text-slate-700">No tienes trámites registrados</h4>
            <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">Inicia un nuevo trámite ambiental para hacerle seguimiento en tiempo real desde este panel.</p>
        </div>
    </div>

</x-layouts.app>