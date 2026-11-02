<section id="marco-legal" class="py-20 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
    <div class="text-center max-w-2xl mx-auto mb-16 space-y-3">
        <span class="inline-block px-3 py-1 text-xs font-semibold uppercase tracking-wider bg-secondary/10 text-secondary rounded-full">Normativa Vigente</span>
        <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-800">Marco Legal Ambiental y de Residuos</h2>
        <p class="text-slate-600 text-base">Fundamentos jurídicos nacionales, leyes orgánicas y ordenanzas municipales que rigen la materia en el Municipio Carrizal.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <!-- 1. Legislación Ambiental y Penal -->
        <div class="bg-white border border-slate-200 rounded-3xl p-8 shadow-sm space-y-6 flex flex-col justify-between">
            <div class="space-y-4">
                <div class="flex items-center gap-3">
                    <div class="h-10 w-10 rounded-xl bg-primary/10 text-primary flex items-center justify-center font-bold">🌿</div>
                    <h3 class="text-lg font-bold text-slate-800">Legislación Ambiental y Penal</h3>
                </div>
                <ul class="space-y-4 text-sm text-slate-600">
                    <li class="flex items-start justify-between gap-3 pb-3 border-b border-slate-100">
                        <div>
                            <strong class="text-slate-700 block">Constitución de la RBV (Art. 127 y 129):</strong> 
                            <span class="text-xs text-slate-500">Deberes, derechos ciudadanos y estudios de impacto ambiental.</span>
                        </div>
                        <a href="{{ route('marco.legal.pdf', ['documento' => 'constitucion']) }}" target="_blank" class="shrink-0 p-2 bg-slate-50 hover:bg-slate-900 hover:text-white text-slate-600 rounded-xl text-xs font-bold transition flex items-center gap-1">
                            <span>PDF</span> 📥
                        </a>
                    </li>
                    <li class="flex items-start justify-between gap-3 pb-3 border-b border-slate-100">
                        <div>
                            <strong class="text-slate-700 block">Ley Orgánica del Ambiente:</strong> 
                            <span class="text-xs text-slate-500">Directrices para la gestión ambiental y defensa de los ecosistemas.</span>
                        </div>
                        <a href="{{ route('marco.legal.pdf', ['documento' => 'ley_ambiente']) }}" target="_blank" class="shrink-0 p-2 bg-slate-50 hover:bg-slate-900 hover:text-white text-slate-600 rounded-xl text-xs font-bold transition flex items-center gap-1">
                            <span>PDF</span> 📥
                        </a>
                    </li>
                    <li class="flex items-start justify-between gap-3 pb-3 border-b border-slate-100">
                        <div>
                            <strong class="text-slate-700 block">Ley Penal del Ambiente:</strong> 
                            <span class="text-xs text-slate-500">Sanciones y tipificación de delitos contra los recursos naturales y el entorno.</span>
                        </div>
                        <a href="{{ route('marco.legal.pdf', ['documento' => 'ley_penal_ambiente']) }}" target="_blank" class="shrink-0 p-2 bg-slate-50 hover:bg-slate-900 hover:text-white text-slate-600 rounded-xl text-xs font-bold transition flex items-center gap-1">
                            <span>PDF</span> 📥
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <!-- 2. Manejo de Residuos y Ordenanzas Municipales -->
        <div class="bg-white border border-slate-200 rounded-3xl p-8 shadow-sm space-y-6 flex flex-col justify-between">
            <div class="space-y-4">
                <div class="flex items-center gap-3">
                    <div class="h-10 w-10 rounded-xl bg-secondary/10 text-secondary flex items-center justify-center font-bold">🗑️</div>
                    <h3 class="text-lg font-bold text-slate-800">Manejo de Residuos y Ordenanzas</h3>
                </div>
                <ul class="space-y-4 text-sm text-slate-600">
                    <li class="flex items-start justify-between gap-3 pb-3 border-b border-slate-100">
                        <div>
                            <strong class="text-slate-700 block">Ley Integral de Gestión de Basura:</strong> 
                            <span class="text-xs text-slate-500">Regula la generación, valorización y disposición final de residuos sólidos.</span>
                        </div>
                        <a href="{{ route('marco.legal.pdf', ['documento' => 'ley_gestion_basura']) }}" target="_blank" class="shrink-0 p-2 bg-slate-50 hover:bg-slate-900 hover:text-white text-slate-600 rounded-xl text-xs font-bold transition flex items-center gap-1">
                            <span>PDF</span> 📥
                        </a>
                    </li>
                    <li class="flex items-start justify-between gap-3 pb-3 border-b border-slate-100">
                        <div>
                            <strong class="text-slate-700 block">Ordenanza Municipal de Aseo Urbano y Domiciliario:</strong> 
                            <span class="text-xs text-slate-500">Competencias, tasas y obligaciones de recolección en Carrizal.</span>
                        </div>
                        <a href="{{ route('marco.legal.pdf', ['documento' => 'ordenanza_aseo']) }}" target="_blank" class="shrink-0 p-2 bg-slate-50 hover:bg-slate-900 hover:text-white text-slate-600 rounded-xl text-xs font-bold transition flex items-center gap-1">
                            <span>PDF</span> 📥
                        </a>
                    </li>
                    <li class="flex items-start justify-between gap-3 pb-3 border-b border-slate-100">
                        <div>
                            <strong class="text-slate-700 block">Ordenanza de Convivencia Ciudadana y Sanciones:</strong> 
                            <span class="text-xs text-slate-500">Marco de multas y faltas ante vertidos ilegchos o contravenciones ambientales.</span>
                        </div>
                        <a href="{{ route('marco.legal.pdf', ['documento' => 'ordenanza_convivencia']) }}" target="_blank" class="shrink-0 p-2 bg-slate-50 hover:bg-slate-900 hover:text-white text-slate-600 rounded-xl text-xs font-bold transition flex items-center gap-1">
                            <span>PDF</span> 📥
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</section>