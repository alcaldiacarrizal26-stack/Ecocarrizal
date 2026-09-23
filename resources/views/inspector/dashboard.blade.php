<x-layouts.app title="Panel del Inspector">
    <div class="p-6 max-w-7xl mx-auto space-y-6">
        
        <!-- Saludo -->
        <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">¡Hola, {{ $user->nombre }}!</h1>
                <p class="text-slate-500 text-sm">Bienvenido al panel técnico de inspecciones y fiscalización.</p>
            </div>
            <a href="{{ route('inspecciones.index') }}" 
               class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-medium text-sm rounded-xl transition shadow-sm flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002 2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                Ir al Módulo de Inspecciones
            </a>
        </div>

        <!-- Tarjetas de Acceso Rápido -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
                <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center mb-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <h2 class="text-lg font-bold text-slate-800 mb-1">Evaluación de Solicitudes y Denuncias</h2>
                <p class="text-slate-500 text-sm mb-4">Consulta las asignaciones pendientes por visitar en campo y levanta las actas de inspección correspondientes.</p>
                <a href="{{ route('inspecciones.index') }}" class="text-emerald-600 font-semibold text-sm hover:underline">Gestionar Inspecciones →</a>
            </div>

            <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
                <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center mb-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                </div>
                <h2 class="text-lg font-bold text-slate-800 mb-1">Soporte Técnico</h2>
                <p class="text-slate-500 text-sm mb-4">Reporta inconvenientes o dudas en el uso de la plataforma institucional.</p>
                <a href="{{ route('inspector.soporte') }}" class="text-blue-600 font-semibold text-sm hover:underline">Ir a Soporte →</a>
            </div>
        </div>

    </div>
</x-layouts.app>