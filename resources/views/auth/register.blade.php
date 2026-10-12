<x-layouts.auth title="Registro de Ciudadano" active="register">
    <div class="min-h-[85vh] flex items-center justify-center p-4">
        <div class="w-full max-w-xl bg-white border border-slate-200 rounded-3xl p-8 shadow-xl space-y-6">
            
            <div class="text-center">
                <div class="h-12 w-12 rounded-2xl bg-indigo-600 flex items-center justify-center text-white text-xl font-bold mx-auto mb-3 shadow-md">
                    <img src="{{ asset('images/carrizal_logo.png') }}" alt="Logo" class="h-8 w-8 object-contain">
                </div>
                <h3 class="text-xl font-bold text-slate-800">Registro de Nueva Cuenta</h3>
                <p class="text-xs text-slate-500 mt-1">Crea tu perfil ciudadano o empresarial para trámites ambientales</p>
            </div>

            @if ($errors->any())
                <div class="p-3 bg-rose-50 border border-rose-200 text-rose-700 text-xs rounded-xl space-y-1 font-semibold">
                    @foreach ($errors->all() as $error)
                        <p>• {{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}" class="space-y-4" x-data="{ showPass: false, showConf: false, submitting: false }" @submit="submitting = true">
                @csrf

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Cédula o RIF</label>
                    <div class="flex gap-2">
                        <select name="nacionalidad" class="px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 font-bold focus:bg-white transition">
                            <option value="V">V-</option>
                            <option value="E">E-</option>
                            <option value="J">J-</option>
                            <option value="G">G-</option>
                        </select>
                        <input type="text" name="cedula_num" value="{{ old('cedula_num') }}" maxlength="9" oninput="this.value = this.value.replace(/[^0-9]/g, '')" required placeholder="12345678" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:bg-white transition">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Nombre</label>
                        <input type="text" name="nombre" value="{{ old('nombre') }}" required placeholder="Juan" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:bg-white transition">
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Apellido</label>
                        <input type="text" name="apellido" value="{{ old('apellido') }}" required placeholder="Pérez" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:bg-white transition">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Correo Electrónico</label>
                    <input type="email" name="correo" value="{{ old('correo') }}" required placeholder="usuario@ejemplo.com" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:bg-white transition">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Teléfono Móvil</label>
                    <div class="flex gap-2">
                        <select name="prefijo_telefono" class="px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 font-bold focus:bg-white transition">
                            <option value="0412">0412</option>
                            <option value="0414">0414</option>
                            <option value="0424">0424</option>
                            <option value="0416">0416</option>
                            <option value="0426">0426</option>
                        </select>
                        <input type="text" name="telefono_num" value="{{ old('telefono_num') }}" maxlength="7" oninput="this.value = this.value.replace(/[^0-9]/g, '')" required placeholder="1234567" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:bg-white transition">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Contraseña</label>
                        <input :type="showPass ? 'text' : 'password'" name="contraseña" required placeholder="••••••••" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:bg-white transition">
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Confirmar Contraseña</label>
                        <input :type="showPass ? 'text' : 'password'" name="contraseña_confirmation" required placeholder="••••••••" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:bg-white transition">
                    </div>
                </div>

                <button type="submit" :disabled="submitting" class="w-full py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl transition shadow-sm cursor-pointer flex items-center justify-center gap-2 mt-2">
                    <span x-text="submitting ? 'Creando cuenta...' : 'Registrarse en el Sistema'"></span>
                </button>
            </form>

            <div class="text-center pt-2 border-t border-slate-100">
                <p class="text-xs text-slate-500">¿Ya tienes cuenta? <a href="{{ route('login') }}" class="font-bold text-indigo-600 hover:underline">Inicia sesión</a></p>
            </div>

        </div>
    </div>
</x-layouts.auth>