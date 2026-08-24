{{-- resources/views/components/footer.blade.php --}}
<footer class="bg-[#06291c] text-white pt-12 pb-8 border-t border-emerald-950 w-full">
    <div class="w-full max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-12">
        
        <div class="grid grid-cols-1 md:grid-cols-12 gap-8 pb-10 border-b border-emerald-900/60">
            
            {{-- Municipal Branding Column --}}
            <div class="md:col-span-5 space-y-3 flex flex-col items-start">
                <div class="p-1 flex items-center justify-start">
                    <img
                        src="{{ asset('images/carrizal_logo.png') }}"
                        alt="Alcaldía de Carrizal"
                        class="w-[134px] h-auto object-contain"
                    />
                </div>
                <div>
                    <h4 class="text-xl font-bold text-white leading-tight">
                        Alcaldía de Carrizal
                    </h4>
                    <p class="text-sm font-semibold text-emerald-300 leading-snug mt-1">
                        Gestión de Residuos y Ecosocialismo y Ambiente
                    </p>
                </div>

                <p class="text-xs text-emerald-100/70 max-w-md leading-relaxed pt-1">
                    Trabajamos continuamente por la conservación de los ecosistemas, la ordenación ecológica y el bienestar ambiental del municipio Carrizal.
                </p>
            </div>

            {{-- Dirección y Contacto Column --}}
            <div class="md:col-span-4 space-y-3 text-xs">
                <h5 class="font-bold text-white uppercase tracking-wider mb-3 text-sm">
                    Dirección y Contacto
                </h5>
                <div class="space-y-2.5 text-emerald-100/80">
                    <div class="flex items-start gap-2.5">
                        <svg class="w-4 h-4 text-emerald-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <span>Casco Central, Av. Sucre, Sede de la Alcaldía de Carrizal, Estado Miranda, Venezuela.</span>
                    </div>
                    <div class="flex items-center gap-2.5">
                        <svg class="w-4 h-4 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                        <span>0212-383-1022 / 0800-CARRIZAL</span>
                    </div>
                    <div class="flex items-center gap-2.5">
                        <svg class="w-4 h-4 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 002-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        <span>ecosocialismo@carrizal-miranda.gob.ve</span>
                    </div>
                    <div class="flex items-center gap-2.5">
                        <svg class="w-4 h-4 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Lunes a Viernes: 8:00 AM - 4:00 PM</span>
                    </div>
                </div>
            </div>

            {{-- Navigation Links Column --}}
            <div class="md:col-span-3 space-y-2 text-xs">
                <h5 class="font-bold text-white uppercase tracking-wider mb-3 text-sm">
                    Navegación Rápida
                </h5>
                <ul class="space-y-2 text-emerald-100/70">
                    <li><a href="#inicio" class="hover:text-white transition-colors">Página Principal</a></li>
                    <li><a href="#servicios" class="hover:text-white transition-colors">Servicios y Requisitos</a></li>
                    <li><a href="#transparencia" class="hover:text-white transition-colors">Transparencia y Gaceta</a></li>
                </ul>
            </div>

        </div>

        {{-- Copyright --}}
        <div class="pt-6 flex flex-col sm:flex-row items-center justify-between text-[11px] text-emerald-200/60 gap-4">
            <p>© {{ date('Y') }} Alcaldía de Carrizal. Todos los derechos reservados.</p>
            <p class="flex items-center gap-2">
                <span>Plataforma Digital de Ecosocialismo v2.5</span>
            </p>
        </div>

    </div>
</footer>