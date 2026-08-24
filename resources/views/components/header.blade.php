{{-- resources/views/components/header.blade.php --}}
<header 
    x-data="{ 
        scrolled: false, 
        mobileMenuOpen: false 
    }" 
    x-init="
        scrolled = window.scrollY > 20;
        window.addEventListener('scroll', () => { scrolled = window.scrollY > 20 });
    "
    :class="scrolled ? 'bg-white/95 backdrop-blur-md shadow-sm py-2.5' : 'bg-white py-4'"
    class="sticky top-0 z-40 w-full transition-all duration-200 border-b border-gray-100"
>
    <div class="w-full max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-12">
        <div class="flex items-center justify-between">
            
            {{-- Logo & Municipal Branding --}}
            <a href="/" class="flex items-center gap-3 group">
                <div class="relative shrink-0 overflow-hidden p-0.5 flex items-center justify-center">
                    <img 
                        src="{{ asset('images/carrizal_logo.png') }}" 
                        alt="Alcaldía de Carrizal Logo" 
                        class="w-[134px] h-auto object-contain"
                    />
                </div>
                <div class="flex flex-col">
                    <span class="text-sm sm:text-base font-bold text-gray-900 tracking-tight leading-tight group-hover:text-[#0d5238] transition-colors">
                        Alcaldía de Carrizal
                    </span>
                    <span class="text-[11px] font-medium text-[#0d5238] leading-tight">
                        Gestión de Residuos y
                    </span>
                    <span class="text-[10px] font-semibold text-[#0d5238] leading-tight">
                        Ecosocialismo y Ambiente
                    </span>
                </div>
            </a>

            {{-- Desktop Navigation Links --}}
            <nav class="hidden md:flex items-center space-x-6 text-sm font-semibold text-gray-700">
                <a href="#inicio" class="hover:text-[#0d5238] transition-colors py-1">Inicio</a>
                <a href="#servicios" class="hover:text-[#0d5238] transition-colors py-1">Servicios y Trámites</a>
                <a href="#transparencia" class="hover:text-[#0d5238] transition-colors py-1">Transparencia</a>
            </nav>

            {{-- Right Action Controls --}}
            <div class="hidden md:flex items-center gap-3">
                <button 
                    type="button"
                    @click="$dispatch('open-login-modal')"
                    class="inline-flex items-center justify-center px-6 py-2 rounded-full text-sm font-bold text-white bg-[#0d5238] hover:bg-[#073825] active:scale-95 transition-all shadow-xs cursor-pointer"
                >
                    Iniciar Sesión / Registro
                </button>
            </div>

            {{-- Mobile Menu Button --}}
            <div class="flex items-center gap-2 md:hidden">
                <button
                    @click="mobileMenuOpen = !mobileMenuOpen"
                    class="p-2 rounded-lg text-slate-700 hover:bg-slate-100 focus:outline-none"
                    aria-label="Abrir menú"
                >
                    <svg x-show="!mobileMenuOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                    <svg x-show="mobileMenuOpen" x-cloak class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    {{-- Mobile Dropdown Menu --}}
    <div 
        x-show="mobileMenuOpen" 
        x-cloak 
        class="md:hidden bg-white border-b border-slate-200 px-4 pt-3 pb-6 space-y-3 shadow-lg"
    >
        <a href="#inicio" @click="mobileMenuOpen = false" class="block px-3 py-2 rounded-md text-base font-medium text-slate-700 hover:bg-slate-100 hover:text-slate-900">Inicio</a>
        <a href="#servicios" @click="mobileMenuOpen = false" class="block px-3 py-2 rounded-md text-base font-medium text-slate-700 hover:bg-slate-100 hover:text-slate-900">Servicios</a>
        <a href="#tramites" @click="mobileMenuOpen = false" class="block px-3 py-2 rounded-md text-base font-medium text-slate-700 hover:bg-slate-100 hover:text-slate-900">Trámites</a>
        <a href="#transparencia" @click="mobileMenuOpen = false" class="block px-3 py-2 rounded-md text-base font-medium text-slate-700 hover:bg-slate-100 hover:text-slate-900">Transparencia</a>
        <a href="#contacto" @click="mobileMenuOpen = false" class="block px-3 py-2 rounded-md text-base font-medium text-slate-700 hover:bg-slate-100 hover:text-slate-900">Contacto</a>

        <div class="pt-3 border-t border-slate-100 flex flex-col gap-2">
            <button
                type="button"
                @click="mobileMenuOpen = false; $dispatch('open-login-modal')"
                class="w-full text-center py-2.5 px-4 rounded-full text-sm font-bold text-white bg-[#0d5238] hover:bg-[#073825] cursor-pointer"
            >
                Iniciar Sesión
            </button>
        </div>
    </div>
</header>