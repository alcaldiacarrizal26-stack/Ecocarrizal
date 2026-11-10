<x-layouts.app title="Gestión de Usuarios" active="admin_usuarios">
    <div class="p-6 max-w-7xl mx-auto space-y-6">
        
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Gestión de Usuarios del Sistema</h1>
                <p class="text-slate-500 text-sm">Control centralizado de cuentas, perfiles institucionales, ciudadanos y permisos de acceso a la plataforma.</p>
            </div>
        </div>

        <!-- Alertas de éxito o error con cierre -->
        @if(session('success'))
            <div x-data="{ show: true }" x-show="show" x-transition class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-sm font-semibold shadow-sm flex items-center justify-between">
                <span>{{ session('success') }}</span>
                <button @click="show = false" class="text-emerald-600 font-bold text-lg px-2 cursor-pointer">&times;</button>
            </div>
        @endif

        @if($errors->any())
            <div x-data="{ show: true }" x-show="show" x-transition class="p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-xl text-sm font-semibold shadow-sm flex items-center justify-between">
                <span>{{ $errors->first() }}</span>
                <button @click="show = false" class="text-rose-600 font-bold text-lg px-2 cursor-pointer">&times;</button>
            </div>
        @endif

        <!-- Filtros de Búsqueda -->
        <form method="GET" action="{{ route('admin.usuarios.index') }}" class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm grid grid-cols-1 sm:grid-cols-4 gap-4 items-end">
            <div class="sm:col-span-2">
                <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Buscar Usuario</label>
                <input type="text" name="buscar" value="{{ request('buscar') }}" placeholder="Nombre, apellido, cédula o correo..." class="w-full bg-slate-50/50 border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-700 focus:bg-white transition">
            </div>

            <div>
                <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Rol</label>
                <select name="role_id" class="w-full bg-slate-50/50 border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-700 focus:bg-white transition">
                    <option value="">Todos los roles</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->id_roles }}" {{ request('role_id') == $role->id_roles ? 'selected' : '' }}>{{ strtoupper($role->nombre) }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Estatus</label>
                <select name="estatus" class="w-full bg-slate-50/50 border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-700 focus:bg-white transition">
                    <option value="">Todos los estatus</option>
                    <option value="activo" {{ request('estatus') === 'activo' ? 'selected' : '' }}>ACTIVO</option>
                    <option value="inactivo" {{ request('estatus') === 'inactivo' ? 'selected' : '' }}>SUSPENDIDO</option>
                </select>
            </div>

            <div class="sm:col-span-4 flex items-center gap-2 justify-end pt-2">
                <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl text-xs transition shadow-sm cursor-pointer">
                    Filtrar Resultados
                </button>
                @if(request()->filled('buscar') || request()->filled('role_id') || request()->filled('estatus'))
                    <a href="{{ route('admin.usuarios.index') }}" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold rounded-xl text-xs transition text-center">
                        Restablecer
                    </a>
                @endif
            </div>
        </form>

        <!-- Tabla de Usuarios -->
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600 min-w-[700px]">
                    <thead class="bg-slate-100 text-xs uppercase text-slate-600 border-b border-slate-200 font-bold tracking-wider">
                        <tr>
                            <th class="px-6 py-4">Usuario / Cédula</th>
                            <th class="px-6 py-4">Contacto</th>
                            <th class="px-6 py-4">Rol / Dirección</th>
                            <th class="px-6 py-4 text-center">Estatus</th>
                            <th class="px-6 py-4 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-xs">
                        @forelse($usuarios as $user)
                            <tr class="hover:bg-slate-50/85 transition-colors align-middle">
                                <td class="px-6 py-4">
                                    <div class="font-bold text-slate-800 text-sm">{{ $user->nombre }} {{ $user->apellido }}</div>
                                    <span class="font-mono text-slate-500 font-medium">{{ $user->cedula }}</span>
                                </td>
                                <td class="px-6 py-4 space-y-0.5">
                                    <div class="text-slate-800 font-medium">{{ $user->correo }}</div>
                                    <div class="text-slate-400 font-mono text-[11px]">{{ $user->telefono }}</div>
                                </td>
                                <td class="px-6 py-4 space-y-1">
                                    <span class="inline-block px-2.5 py-0.5 rounded-md font-mono font-bold text-[10px] bg-indigo-50 text-indigo-700 border border-indigo-200 uppercase">
                                        {{ $user->role->nombre ?? 'N/A' }}
                                    </span>
                                    @if($user->direccion)
                                        <div class="text-[11px] text-slate-500 font-medium">{{ $user->direccion->nombre_direc }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if($user->is_active)
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200 uppercase inline-block">Activo</span>
                                    @else
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800 border border-rose-200 uppercase inline-block">Suspendido</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    @if($user->id !== auth()->id())
                                        <form action="{{ route('admin.usuarios.toggle', $user->id) }}" method="POST" class="inline-block">
                                            @csrf
                                            @method('PATCH')
                                            @if($user->is_active)
                                                <button type="submit" onclick="return confirm('¿Estás seguro de suspender el acceso a este usuario?');" class="px-3 py-2 bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 rounded-xl text-xs font-bold transition shadow-sm cursor-pointer">
                                                    Suspender
                                                </button>
                                            @else
                                                <button type="submit" class="px-3 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold transition shadow-sm cursor-pointer">
                                                    Activar
                                                </button>
                                            @endif
                                        </form>
                                    @else
                                        <span class="text-slate-400 italic text-[11px]">Cuenta Actual</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-slate-400 text-sm">No se encontraron usuarios registrados con los filtros seleccionados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Paginación Adaptativa -->
        @if($usuarios->hasPages())
            <div class="p-4 bg-white border border-slate-200 rounded-2xl shadow-sm overflow-x-auto">
                {{ $usuarios->links() }}
            </div>
        @endif

        <!-- Paginación -->
        @if($usuarios->hasPages())
            <div class="p-4 bg-white border border-slate-200 rounded-2xl shadow-sm">
                {{ $usuarios->links() }}
            </div>
        @endif

    </div>
</x-layouts.app>