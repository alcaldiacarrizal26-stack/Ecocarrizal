<x-layouts.auth title="Restablecer Contraseña">
    <div class="bg-white border border-slate-200 rounded-3xl p-8 shadow-xl space-y-6">
        
        <div class="text-center">
            <div class="h-12 w-12 rounded-2xl bg-indigo-600 flex items-center justify-center text-white text-xl font-bold mx-auto mb-3 shadow-md">
                <img src="{{ asset('images/carrizal_logo.png') }}" alt="Logo" class="h-8 w-8 object-contain">
            </div>
            <h3 class="text-xl font-bold text-slate-800">Nueva Contraseña</h3>
            <p class="text-xs text-slate-500 mt-1">Ingresa tu nueva contraseña para acceder a la plataforma.</p>
        </div>

        @if ($errors->any())
            <div class="p-3 bg-rose-50 border border-rose-200 text-rose-700 text-xs rounded-xl space-y-1 font-semibold">
                @foreach ($errors->all() as $error)
                    <p>• {{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('password.update') }}" class="space-y-4" x-data="{ showPass: false, submitting: false }" @submit="submitting = true">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Correo Electrónico</label>
                <input type="email" name="correo" value="{{ $correo ?? old('correo') }}" required class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:bg-white transition">
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Nueva Contraseña</label>
                <input type="password" name="contraseña" required placeholder="••••••••" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:bg-white transition">
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Confirmar Contraseña</label>
                <input type="password" name="contraseña_confirmation" required placeholder="••••••••" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:bg-white transition">
            </div>

            <button type="submit" :disabled="submitting" class="w-full py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl transition shadow-sm cursor-pointer flex items-center justify-center gap-2">
                <span x-text="submitting ? 'Actualizando...' : 'Restablecer Contraseña'"></span>
            </button>
        </form>

    </div>
</x-layouts.auth>