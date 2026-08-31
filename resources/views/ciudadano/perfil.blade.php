<x-layouts.app title="Mi Perfil - Panel Ciudadano" active="perfil">
    <div class="max-w-4xl mx-auto space-y-6">
        
        <div>
            <h1 class="text-xl font-bold text-slate-800">Perfil de Ciudadano</h1>
            <p class="text-xs text-slate-500 mt-1">Gestiona tu información personal y credenciales de acceso.</p>
        </div>

        @if(session('success'))
            <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-semibold rounded-2xl">
                {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            
            <!-- Tarjeta Lateral -->
            <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-xs text-center space-y-4 h-fit">
                <div class="w-20 h-20 bg-emerald-600 text-white font-bold text-2xl rounded-2xl mx-auto flex items-center justify-center shadow-md shadow-emerald-600/20">
                    {{ strtoupper(substr($user->nombre, 0, 1)) }}
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-800">{{ $user->nombre }} {{ $user->apellido }}</h3>
                    <p class="text-xs text-slate-500 mt-0.5">{{ $user->cedula ?? 'V-00000000' }}</p>
                </div>
                <div class="pt-3 border-t border-slate-100">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                        Ciudadano Verificado
                    </span>
                </div>
            </div>

            <!-- Formulario Principal -->
            <div class="md:col-span-2 bg-white p-6 rounded-3xl border border-slate-200/80 shadow-xs space-y-6">
                <form action="{{ route('ciudadano.perfil.update') }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <h2 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Datos Personales</h2>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Nombre</label>
                            <input type="text" name="nombre" value="{{ old('nombre', $user->nombre) }}" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Apellido</label>
                            <input type="text" name="apellido" value="{{ old('apellido', $user->apellido) }}" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Correo Electrónico</label>
                            <input type="email" name="email" value="{{ old('email', $user->email) }}" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Teléfono</label>
                            <input type="text" name="telefono" value="{{ old('telefono', $user->telefono) }}" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                        </div>
                    </div>

                    <hr class="border-slate-100 my-4">

                    <h2 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Seguridad</h2>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Nueva Contraseña (Opcional)</label>
                        <input type="password" name="password" placeholder="Dejar en blanco para no modificar" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                    </div>

                    <div class="flex justify-end pt-2">
                        <button type="submit" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-xl transition-colors cursor-pointer shadow-md shadow-emerald-600/20">
                            Guardar Cambios
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</x-layouts.app>