{{-- resources/views/components/auth-modal.blade.php --}}
<div 
    x-show="loginModalOpen" 
    x-cloak
    class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    @keydown.escape.window="loginModalOpen = false"
>
    <!-- Overlay click closure -->
    <div class="fixed inset-0" @click="loginModalOpen = false"></div>

    <!-- Modal Content -->
    <div 
        x-data="{ tab: 'login' }"
        class="relative w-full max-w-md bg-white rounded-2xl shadow-2xl border border-slate-100 p-6 z-10 overflow-hidden transform transition-all"
        @click.stop
    >
        <!-- Close Button -->
        <button 
            @click="loginModalOpen = false"
            class="absolute top-4 right-4 text-slate-400 hover:text-slate-600 transition-colors p-1 rounded-lg hover:bg-slate-100"
        >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>

        <!-- Header -->
        <div class="text-center mb-6">
            <h3 class="text-xl font-bold text-slate-900">Plataforma Digital de Ecosocialismo</h3>
            <p class="text-xs text-slate-500 mt-1">Alcaldía del Municipio Carrizal</p>
        </div>

        <!-- Dynamic Category Notice (if redirected from hero options) -->
        <template x-if="selectedCategory">
            <div class="mb-4 p-3 bg-emerald-50 border border-emerald-200 rounded-lg text-xs text-emerald-800 flex items-center justify-between">
                <div>
                    <span class="font-semibold block">Tratamiento seleccionado:</span>
                    <span x-text="selectedCategory + (selectedSubType ? ' — ' + selectedSubType : '')"></span>
                </div>
            </div>
        </template>

        <!-- Tab Selector -->
        <div class="flex border-b border-slate-200 mb-6">
            <button 
                @click="tab = 'login'"
                :class="tab === 'login' ? 'border-emerald-600 text-emerald-700 font-semibold' : 'border-transparent text-slate-500 hover:text-slate-700'"
                class="flex-1 py-2 text-center text-sm border-b-2 transition-all"
            >
                Iniciar Sesión
            </button>
            <button 
                @click="tab = 'register'"
                :class="tab === 'register' ? 'border-emerald-600 text-emerald-700 font-semibold' : 'border-transparent text-slate-500 hover:text-slate-700'"
                class="flex-1 py-2 text-center text-sm border-b-2 transition-all"
            >
                Registrarse
            </button>
        </div>

        <!-- Login Form -->
        <form x-show="tab === 'login'" method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Correo Electrónico</label>
                <input 
                    type="email" 
                    name="email" 
                    required 
                    placeholder="usuario@ejemplo.com"
                    class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition-all"
                />
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Contraseña</label>
                <input 
                    type="password" 
                    name="password" 
                    required 
                    placeholder="••••••••"
                    class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition-all"
                />
            </div>
            <button 
                type="submit" 
                class="w-full py-2.5 bg-emerald-700 hover:bg-emerald-800 text-white font-semibold text-sm rounded-lg shadow-sm transition-colors"
            >
                Ingresar
            </button>
        </form>

        <!-- Register Form -->
        <form x-show="tab === 'register'" method="POST" action="{{ route('register') }}" class="space-y-4" style="display: none;">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Nombre Completo</label>
                <input 
                    type="text" 
                    name="name" 
                    required 
                    placeholder="Juan Pérez"
                    class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition-all"
                />
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Correo Electrónico</label>
                <input 
                    type="email" 
                    name="email" 
                    required 
                    placeholder="usuario@ejemplo.com"
                    class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition-all"
                />
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Contraseña</label>
                <input 
                    type="password" 
                    name="password" 
                    required 
                    placeholder="••••••••"
                    class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition-all"
                />
            </div>
            <button 
                type="submit" 
                class="w-full py-2.5 bg-emerald-700 hover:bg-emerald-800 text-white font-semibold text-sm rounded-lg shadow-sm transition-colors"
            >
                Crear Cuenta
            </button>
        </form>
    </div>
</div>