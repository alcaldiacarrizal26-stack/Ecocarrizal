<x-layouts.app title="Mi Perfil" active="perfil">
    <div class="p-6 max-w-4xl mx-auto space-y-6">
        
        <!-- Encabezado de la Sección -->
        <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-xs flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 border-l-4 border-l-emerald-600">
            <div class="flex items-center gap-4">
                <div class="h-16 w-16 rounded-2xl bg-emerald-600 text-white flex items-center justify-center font-bold text-2xl shadow-md">
                    {{ strtoupper(substr($user->nombre ?? 'U', 0, 1)) }}
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="px-2.5 py-0.5 bg-emerald-100 text-emerald-800 text-[10px] font-bold uppercase rounded-md border border-emerald-200">
                            {{ $user->role->nombre ?? 'Usuario' }}
                        </span>
                        @if($user->direccion)
                            <span class="text-xs text-slate-400 font-medium">• {{ $user->direccion->nombre_direc }}</span>
                        @endif
                    </div>
                    <h1 class="text-xl font-bold text-slate-900 mt-1">{{ $user->nombre }} {{ $user->apellido }}</h1>
                    <p class="text-xs text-slate-500">Información del expediente personal y credenciales de acceso al sistema.</p>
                </div>
            </div>

            <div class="shrink-0 bg-slate-100 text-slate-600 px-3 py-1.5 rounded-xl text-xs font-semibold border border-slate-200">
                Estado: {{ $user->is_active ? 'Activo' : 'Inactivo' }}
            </div>
        </div>

        <!-- Ficha Informativa (Solo Lectura) -->
        <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-xs space-y-6">
            <div class="border-b border-slate-100 pb-3 flex items-center justify-between">
                <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider">Datos de Identificación y Contacto</h3>
                <span class="text-[11px] font-medium text-amber-600 bg-amber-50 border border-amber-200 px-2.5 py-1 rounded-lg">
                    🔒 Edición temporalmente inhabilitada
                </span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 text-xs">
                
                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 space-y-1">
                    <span class="text-[10px] font-bold uppercase text-slate-400">Nombres y Apellidos</span>
                    <p class="font-bold text-slate-800 text-sm">{{ $user->nombre }} {{ $user->apellido }}</p>
                </div>

                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 space-y-1">
                    <span class="text-[10px] font-bold uppercase text-slate-400">Cédula de Identidad</span>
                    <p class="font-bold text-slate-800 text-sm font-mono">{{ $user->cedula ?? 'N/A' }}</p>
                </div>

                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 space-y-1">
                    <span class="text-[10px] font-bold uppercase text-slate-400">Teléfono Móvil / Contacto</span>
                    <p class="font-bold text-slate-800 text-sm font-mono">{{ $user->telefono ?? 'N/A' }}</p>
                </div>

                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 space-y-1 sm:col-span-2">
                    <span class="text-[10px] font-bold uppercase text-slate-400">Correo Electrónico Institucional / Usuario</span>
                    <p class="font-bold text-slate-800 text-sm font-mono">{{ $user->correo ?? $user->email }}</p>
                </div>

                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 space-y-1">
                    <span class="text-[10px] font-bold uppercase text-slate-400">Rol de Sistema</span>
                    <p class="font-bold text-indigo-600 text-sm uppercase">{{ $user->role->nombre ?? 'N/A' }}</p>
                </div>

                @if($user->inspector)
                    <div class="p-4 rounded-2xl bg-emerald-50/60 border border-emerald-200 space-y-1 sm:col-span-3">
                        <span class="text-[10px] font-bold uppercase text-emerald-800">Credencial de Fiscalización / Inspección</span>
                        <div class="flex items-center gap-4 mt-1">
                            <div>
                                <span class="text-slate-500 block text-[10px]">Código Credencial:</span>
                                <strong class="text-slate-900 font-mono text-xs">{{ $user->inspector->codigo_credencial }}</strong>
                            </div>
                            <div>
                                <span class="text-slate-500 block text-[10px]">Especialidad:</span>
                                <strong class="text-slate-900 text-xs">{{ $user->inspector->especialidad ?? 'General' }}</strong>
                            </div>
                        </div>
                    </div>
                @endif

            </div>
        </div>

    </div>
</x-layouts.app>