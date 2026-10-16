<x-layouts.auth title="Iniciar Sesión" active="login">
    <div class="min-h-[80vh] flex items-center justify-center p-4">
        <div class="w-full max-w-md bg-white border border-slate-200 rounded-3xl p-8 shadow-xl space-y-6">
            
            <div class="text-center">
                <div class="h-12 w-12 rounded-2xl bg-emerald-600 flex items-center justify-center text-white text-xl font-bold mx-auto mb-3 shadow-md">
                    <img src="{{ asset('images/carrizal_logo.png') }}" alt="Logo" class="h-8 w-8 object-contain">
                </div>
                <h3 class="text-xl font-bold text-slate-800">Iniciar Sesión</h3>
                <p class="text-xs text-slate-500 mt-1">Plataforma Digital de Ecosocialismo - Carrizal</p>
            </div>

            @if (session('success'))
                <div class="p-3 bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs rounded-xl font-semibold">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="p-3 bg-rose-50 border border-rose-200 text-rose-700 text-xs rounded-xl space-y-1 font-semibold">
                    @foreach ($errors->all() as $error)
                        <p>• {{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-4" x-data="{ showPass: false, submitting: false }" @submit="if(submitting) { $event.preventDefault(); } else { submitting = true; }">
                @csrf
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Correo Electrónico</label>
                    <input type="email" name="correo" value="{{ old('correo') }}" required placeholder="usuario@ejemplo.com" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:bg-white focus:border-emerald-600 transition">
                </div>

                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-600">Contraseña</label>
                        <a href="{{ route('password.request') }}" class="text-[11px] font-bold text-emerald-600 hover:underline">¿Olvidaste tu contraseña?</a>
                    </div>
                    <div class="relative">
                        <input :type="showPass ? 'text' : 'password'" name="contraseña" required placeholder="••••••••" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:bg-white focus:border-emerald-600 transition pr-10">
                        <button type="button" @click="showPass = !showPass" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 cursor-pointer">
                            <svg x-show="!showPass" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            <svg x-show="showPass" x-cloak class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858-5.908a10.012 10.012 0 014.122-.963c4.478 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21M3 3l18 18"/></svg>
                        </button>
                    </div>
                </div>

                <button type="submit" 
                        :disabled="submitting" 
                        :class="submitting ? 'bg-emerald-800/60 text-emerald-100 cursor-not-allowed' : 'bg-emerald-600 hover:bg-emerald-700 text-white cursor-pointer'"
                        class="w-full py-3 font-bold text-xs rounded-xl transition shadow-sm flex items-center justify-center gap-2">
                    <span x-text="submitting ? 'Iniciando sesión...' : 'Entrar al Sistema'"></span>
                </button>
            </form>

            <div class="text-center pt-2 border-t border-slate-100">
                <p class="text-xs text-slate-500">¿No tienes cuenta? <a href="{{ route('register') }}" class="font-bold text-emerald-600 hover:underline">Regístrate aquí</a></p>
            </div>

        </div>
    </div>
</x-layouts.auth>