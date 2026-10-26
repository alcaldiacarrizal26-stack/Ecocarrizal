<x-layouts.auth title="Enlace Expirado - Alcaldía de Carrizal">
    <div class="sm:mx-auto sm:w-full sm:max-w-md text-center space-y-6">
        
        <!-- Icono de advertencia -->
        <div class="w-16 h-16 bg-rose-100 text-rose-600 rounded-2xl flex items-center justify-center mx-auto shadow-xs">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>

        <div class="space-y-2">
            <h2 class="text-xl font-bold text-slate-800">Enlace No Válido o Expirado</h2>
            <p class="text-xs text-slate-500 leading-relaxed max-w-sm mx-auto">
                {{ $motivo ?? 'Por razones de seguridad, los enlaces de acceso y recuperación caducan a los 60 minutos o pierden validez tras su primer uso.' }}
            </p>
        </div>

        <!-- Formulario directo para solicitar un nuevo enlace sin salir de la vista -->
        <form action="{{ route('password.email') }}" method="POST" class="space-y-3 pt-2 text-left bg-slate-50 p-4 rounded-2xl border border-slate-200">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Ingresa tu correo institucional:</label>
                <input 
                    type="email" 
                    name="email" 
                    required 
                    placeholder="ejemplo@carrizal.gob.ve"
                    class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-800 focus:border-emerald-500 focus:outline-none"
                >
            </div>

            <button 
                type="submit" 
                class="w-full py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-md transition-all cursor-pointer flex items-center justify-center gap-2"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                <span>Generar y Enviar Nuevo Enlace</span>
            </button>
        </form>

        <div class="pt-2 border-t border-slate-100">
            <a href="{{ route('login') }}" class="text-xs font-semibold text-slate-400 hover:text-slate-600">
                &larr; Volver al inicio de sesión
            </a>
        </div>
    </div>
</x-layouts.auth>