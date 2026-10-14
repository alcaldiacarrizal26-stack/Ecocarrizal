<section id="contacto" class="py-20 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
    <div class="bg-primary/5 border border-primary/20 rounded-3xl p-8 sm:p-12">
        <div class="grid lg:grid-cols-2 gap-12 items-center">
            <div class="space-y-6">
                <span class="inline-block px-3 py-1 text-xs font-semibold uppercase tracking-wider bg-primary/10 text-primary rounded-full">Atención Ciudadana</span>
                <h2 class="text-3xl font-extrabold text-slate-800">¿Necesitas asistencia o información institucional?</h2>
                <p class="text-slate-600 text-sm leading-relaxed">
                    Comunícate directamente con la Dirección de Ecología y Residuos de la Alcaldía del Municipio Carrizal o visita nuestras oficinas.
                </p>

                <div class="space-y-4 text-sm text-slate-700">
                    <div class="flex items-center gap-3">
                        <span class="h-9 w-9 rounded-xl bg-primary/10 text-primary flex items-center justify-center font-bold">📍</span>
                        <span>Sede de la Alcaldía de Carrizal, Estado Miranda.</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="h-9 w-9 rounded-xl bg-primary/10 text-primary flex items-center justify-center font-bold">📞</span>
                        <span>+58 (0212) 000-0000 / Lunes a Viernes (8:00 AM - 4:00 PM)</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="h-9 w-9 rounded-xl bg-primary/10 text-primary flex items-center justify-center font-bold">✉️</span>
                        <span>contacto@carrizal.gob.ve / ecosocialismo@carrizal.gob.ve</span>
                    </div>
                </div>
            </div>

            <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm space-y-4 text-center">
                <h3 class="text-lg font-bold text-slate-800">Acceso Rápido al Sistema</h3>
                <p class="text-xs text-slate-500">Inicia sesión o regístrate para gestionar tu expediente comercial ambiental.</p>
                <div class="pt-2 flex flex-col gap-3">
                    <a href="{{ route('login') }}" class="w-full py-3 bg-primary hover:bg-secondary text-white font-bold text-xs rounded-xl shadow-md transition-all">
                        Iniciar Sesión
                    </a>
                    <a href="{{ route('register') }}" class="w-full py-3 bg-white border border-slate-300 hover:bg-slate-50 text-slate-700 font-semibold text-xs rounded-xl transition-all">
                        Registrar Nueva Cuenta
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>