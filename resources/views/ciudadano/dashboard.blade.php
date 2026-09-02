<x-layouts.app title="Inicio - Panel Ciudadano" active="inicio">
    <div class="max-w-7xl mx-auto space-y-8">
        
        <!-- Saludo -->
        <div>
            <h1 class="text-2xl font-bold text-slate-800">¡Hola, {{ auth()->user()->nombre }}!</h1>
            <p class="text-xs text-slate-500 mt-1">Selecciona el trámite o gestión ambiental que deseas realizar en el Municipio Carrizal.</p>
        </div>

        <!-- Opciones Principales (3 Tarjetas) -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            
            <!-- 1. Poda y Tala -->
            <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-xs flex flex-col justify-between space-y-4 hover:border-emerald-500/50 transition-all">
                <div class="space-y-3">
                    <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                        </svg>
                    </div>
                    <h3 class="text-base font-bold text-slate-800">Poda y Tala</h3>
                    <p class="text-xs text-slate-500 leading-relaxed">Solicita permisos fitosanitarios para poda de mantenimiento, seguridad o tala de árboles en propiedad privada o vía pública.</p>
                </div>
                <a href="{{ route('ciudadano.solicitudes') }}" class="inline-flex items-center justify-center gap-2 w-full py-2.5 px-4 bg-slate-900 hover:bg-emerald-600 text-white font-semibold text-xs rounded-xl transition-colors">
                    <span>Iniciar Solicitud</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </a>
            </div>

            <!-- 2. Trasplante -->
            <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-xs flex flex-col justify-between space-y-4 hover:border-emerald-500/50 transition-all">
                <div class="space-y-3">
                    <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                        </svg>
                    </div>
                    <h3 class="text-base font-bold text-slate-800">Trasplante Vegetal</h3>
                    <p class="text-xs text-slate-500 leading-relaxed">Tramita la reubicación de especies vegetales de intercepción o afectación por proyectos menores en el municipio.</p>
                </div>
                <a href="{{ route('ciudadano.solicitudes') }}" class="inline-flex items-center justify-center gap-2 w-full py-2.5 px-4 bg-slate-900 hover:bg-emerald-600 text-white font-semibold text-xs rounded-xl transition-colors">
                    <span>Solicitar Trasplante</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </a>
            </div>

            <!-- 3. Reportes y Denuncias -->
            <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-xs flex flex-col justify-between space-y-4 hover:border-emerald-500/50 transition-all">
                <div class="space-y-3">
                    <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <h3 class="text-base font-bold text-slate-800">Reportes y Denuncias</h3>
                    <p class="text-xs text-slate-500 leading-relaxed">Notifica incidentes de contaminación, vertederos no autorizados, quema de desechos o talas ilegales de forma anónima o pública.</p>
                </div>
                <a href="{{ route('ciudadano.reportes') }}" class="inline-flex items-center justify-center gap-2 w-full py-2.5 px-4 bg-amber-500 hover:bg-amber-600 text-white font-semibold text-xs rounded-xl transition-colors">
                    <span>Crear Reporte</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </a>
            </div>

        </div>
    </div>
</x-layouts.app>