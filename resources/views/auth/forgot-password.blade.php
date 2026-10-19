<x-layouts.auth title="Recuperar Contraseña">
    <div class="bg-white border border-slate-200 rounded-3xl p-8 shadow-xl space-y-6">
        
        <div class="text-center">
            <!-- Contenedor horizontal limpio -->
            <div class="flex items-center justify-center mx-auto mb-3">
                <img src="{{ asset('images/carrizal_logo.png') }}" alt="Logo Carrizal" class="h-10 w-auto object-contain">
            </div>
            <h3 class="text-xl font-bold text-slate-800">Recuperar Contraseña</h3>
            <p class="text-xs text-slate-500 mt-1">Ingresa tu correo electrónico registrado y te enviaremos las instrucciones.</p>
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

        <form method="POST" action="{{ route('password.email') }}" class="space-y-4" x-data="{ submitting: false }" @submit="submitting = true">
            @csrf
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Correo Electrónico</label>
                <input type="email" name="email" value="{{ old('email') }}" required placeholder="usuario@ejemplo.com" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:bg-white focus:border-emerald-600 transition">
            </div>

            <!-- Botón actualizado a verde esmeralda -->
            <button type="submit" :disabled="submitting" class="w-full py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl transition shadow-sm cursor-pointer flex items-center justify-center gap-2">
                <span x-text="submitting ? 'Enviando enlace...' : 'Enviar Enlace de Recuperación'"></span>
            </button>
        </form>

        <div class="text-center pt-2 border-t border-slate-100">
            <p class="text-xs text-slate-500"><a href="{{ route('login') }}" class="font-bold text-emerald-600 hover:underline">← Volver al Inicio de Sesión</a></p>
        </div>

    </div>
</x-layouts.auth>