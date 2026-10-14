<header class="bg-white/95 backdrop-blur-sm border-b border-slate-200/60 sticky top-0 z-40 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
        <!-- Logo -->
        <a href="#" @click.prevent="activeTab = 'inicio'" class="flex items-center gap-3 group cursor-pointer">
            <div class="h-11 w-11 rounded-2xl bg-primary flex items-center justify-center font-black text-white text-2xl shadow-md shadow-primary/10 group-hover:scale-105 transition-transform">
                <img src="{{ asset('images/carrizal_logo.png') }}" alt="Logo Carrizal" class="h-10 w-10 object-contain">
            </div>
            <div class="flex flex-col">
                <span class="font-bold text-xl tracking-tight text-slate-800 group-hover:text-primary transition-colors">Ecosocialismo</span>
                <span class="text-[11px] text-primary font-semibold tracking-wider uppercase">Alcaldía de Carrizal</span>
            </div>
        </a>

        <!-- Navegación desktop dinámica -->
        <nav class="hidden lg:flex items-center gap-6 text-sm font-medium text-slate-600">
            <a href="#" @click.prevent="activeTab = 'inicio'" :class="activeTab === 'inicio' ? 'text-primary font-bold' : 'hover:text-primary'" class="transition-colors">Inicio</a>
            <a href="#" @click.prevent="activeTab = 'servicios'" :class="activeTab === 'servicios' ? 'text-primary font-bold' : 'hover:text-primary'" class="transition-colors">Servicios</a>
            <a href="#" @click.prevent="activeTab = 'marco-legal'" :class="activeTab === 'marco-legal' ? 'text-primary font-bold' : 'hover:text-primary'" class="transition-colors">Marco Legal</a>
            <a href="#" @click.prevent="activeTab = 'especies'" :class="activeTab === 'especies' ? 'text-primary font-bold' : 'hover:text-primary'" class="transition-colors">Especies Protegidas</a>
            <a href="#" @click.prevent="activeTab = 'contacto'" :class="activeTab === 'contacto' ? 'text-primary font-bold' : 'hover:text-primary'" class="transition-colors">Contacto</a>
        </nav>

        <!-- Botones de acción -->
        <div class="hidden md:flex items-center gap-4">
            <a href="{{ route('login') }}" class="px-4 py-2.5 text-sm font-medium text-slate-600 hover:text-primary transition-colors cursor-pointer">
                Iniciar Sesión
            </a>
            <a href="{{ route('register') }}" class="px-4 py-2.5 text-sm font-semibold bg-primary hover:bg-secondary text-white rounded-xl transition-all shadow-md shadow-primary/20 hover:shadow-primary/30 transform active:scale-95 cursor-pointer">
                Registrarse
            </a>
        </div>

        <!-- Botón menú móvil -->
        <button @click="mobileMenu = !mobileMenu" class="lg:hidden p-2 text-slate-500 hover:text-primary rounded-lg border border-slate-200">
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>
    </div>

    <!-- Menú móvil desplegable -->
    <div x-show="mobileMenu" x-cloak class="lg:hidden border-t border-slate-200 bg-white px-4 pt-3 pb-6 space-y-3 shadow-lg">
        <a href="#" @click.prevent="activeTab = 'inicio'; mobileMenu = false" class="block text-slate-700 hover:text-primary text-sm font-medium">Inicio</a>
        <a href="#" @click.prevent="activeTab = 'servicios'; mobileMenu = false" class="block text-slate-700 hover:text-primary text-sm font-medium">Servicios</a>
        <a href="#" @click.prevent="activeTab = 'marco-legal'; mobileMenu = false" class="block text-slate-700 hover:text-primary text-sm font-medium">Marco Legal</a>
        <a href="#" @click.prevent="activeTab = 'especies'; mobileMenu = false" class="block text-slate-700 hover:text-primary text-sm font-medium">Especies Protegidas</a>
        <a href="#" @click.prevent="activeTab = 'contacto'; mobileMenu = false" class="block text-slate-700 hover:text-primary text-sm font-medium">Contacto</a>
        <div class="pt-3 border-t border-slate-200 flex flex-col gap-2">
            <a href="{{ route('login') }}" class="w-full text-center py-2 text-sm text-slate-600 border border-slate-300 rounded-xl font-medium hover:bg-slate-50 cursor-pointer">
                Iniciar Sesión
            </a>
            <a href="{{ route('register') }}" class="w-full text-center py-2 text-sm font-semibold bg-primary text-white rounded-xl hover:bg-secondary cursor-pointer">
                Registrarse
            </a>
        </div>
    </div>
</header>