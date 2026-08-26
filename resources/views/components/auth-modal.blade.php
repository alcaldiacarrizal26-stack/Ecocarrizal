{{-- resources/views/components/auth-modal.blade.php --}}
<div 
    x-show="openAuth" 
    x-cloak
    class="fixed inset-0 z-50 overflow-y-auto"
    @keydown.escape.window="openAuth = false"
    x-effect="document.body.classList.toggle('overflow-hidden', openAuth)"
>
    <!-- Backdrop / Overlay -->
    <div 
        x-show="openAuth" 
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @click="openAuth = false" 
        class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm"
    ></div>

    <!-- Container Centrado -->
    <div class="flex min-h-full items-center justify-center p-4">
        <!-- Card / Modal Content -->
        <div 
            x-show="openAuth"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            x-data="{ 
                showLoginPass: false, 
                showRegPass: false, 
                showConfirmPass: false 
            }"
            class="relative w-full max-w-lg bg-slate-900 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-2xl z-10 overflow-hidden my-8"
            @click.stop
        >
            <!-- Close Button -->
            <button 
                @click="openAuth = false"
                class="absolute top-5 right-5 text-slate-400 hover:text-white transition-colors p-1 rounded-lg hover:bg-slate-800 cursor-pointer"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>

            <!-- Header -->
            <div class="text-center mb-6">
                <div class="h-10 w-10 rounded-xl bg-gradient-to-br from-emerald-400 via-teal-500 to-emerald-600 flex items-center justify-center font-black text-slate-950 text-xl shadow-md shadow-emerald-500/20 mx-auto mb-3">
                    E
                </div>
                <h3 class="text-xl font-bold text-white">Plataforma Digital de Ecosocialismo</h3>
                <p class="text-xs text-emerald-400 font-semibold tracking-wider uppercase mt-1">Alcaldía del Municipio Carrizal</p>
            </div>

            <!-- Flash & Validation Messages -->
            @if (session('success'))
                <div class="mb-4 p-3 bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-xs rounded-xl">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-4 p-3 bg-red-950/50 border border-red-800/80 text-red-400 text-xs rounded-xl space-y-1">
                    @foreach ($errors->all() as $error)
                        <p>• {{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <!-- Tab Selector -->
            <div class="flex border-b border-slate-800 mb-6">
                <button 
                    @click="authMode = 'login'"
                    :class="authMode === 'login' ? 'border-emerald-500 text-emerald-400 font-bold' : 'border-transparent text-slate-400 hover:text-slate-200'"
                    class="flex-1 pb-3 text-center text-sm font-medium border-b-2 transition-all cursor-pointer"
                >
                    Iniciar Sesión
                </button>
                <button 
                    @click="authMode = 'register'"
                    :class="authMode === 'register' ? 'border-emerald-500 text-emerald-400 font-bold' : 'border-transparent text-slate-400 hover:text-slate-200'"
                    class="flex-1 pb-3 text-center text-sm font-medium border-b-2 transition-all cursor-pointer"
                >
                    Registrarse
                </button>
            </div>

            <!-- Formulario de Login -->
            <form x-show="authMode === 'login'" method="POST" action="/login" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1">Correo Electrónico</label>
                    <input 
                        type="email" 
                        name="correo" 
                        value="{{ old('correo') }}"
                        required 
                        placeholder="usuario@ejemplo.com"
                        class="w-full px-4 py-3 bg-slate-950 border border-slate-800 rounded-xl text-sm text-white placeholder-slate-500 focus:outline-none focus:border-emerald-500 transition-all"
                    />
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1">Contraseña</label>
                    <div class="relative">
                        <input 
                            :type="showLoginPass ? 'text' : 'password'" 
                            name="contraseña" 
                            required 
                            placeholder="••••••••"
                            class="w-full px-4 py-3 bg-slate-950 border border-slate-800 rounded-xl text-sm text-white placeholder-slate-500 focus:outline-none focus:border-emerald-500 transition-all pr-10"
                        />
                        <button 
                            type="button" 
                            @click="showLoginPass = !showLoginPass" 
                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-500 hover:text-slate-300 transition-colors"
                        >
                            <svg x-show="!showLoginPass" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <svg x-show="showLoginPass" x-cloak class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858-5.908a10.012 10.012 0 014.122-.963c4.478 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21M3 3l18 18"/>
                            </svg>
                        </button>
                    </div>
                </div>
                <button 
                    type="submit" 
                    class="w-full py-3.5 bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-bold text-sm rounded-xl transition shadow-md shadow-emerald-500/20 mt-2 cursor-pointer"
                >
                    Entrar
                </button>
            </form>

            <!-- Formulario de Registro -->
            <form x-show="authMode === 'register'" method="POST" action="/register" class="space-y-3" style="display: none;">
                @csrf
                
                <!-- Cédula -->
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1">Cédula o RIF</label>
                    <div class="flex gap-2">
                        <select 
                            name="nacionalidad" 
                            class="px-3 py-3 bg-slate-950 border border-slate-800 rounded-xl text-sm text-slate-200 font-medium focus:outline-none focus:border-emerald-500 transition-all"
                        >
                            <option value="V" {{ old('nacionalidad') == 'V' ? 'selected' : '' }}>V-</option>
                            <option value="E" {{ old('nacionalidad') == 'E' ? 'selected' : '' }}>E-</option>
                            <option value="J" {{ old('nacionalidad') == 'J' ? 'selected' : '' }}>J-</option>
                            <option value="G" {{ old('nacionalidad') == 'G' ? 'selected' : '' }}>G-</option>
                        </select>
                        <input 
                            type="text" 
                            name="cedula_num" 
                            value="{{ old('cedula_num') }}"
                            maxlength="9" 
                            oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                            required 
                            placeholder="12345678"
                            class="w-full px-4 py-3 bg-slate-950 border border-slate-800 rounded-xl text-sm text-white placeholder-slate-500 focus:outline-none focus:border-emerald-500 transition-all"
                        />
                    </div>
                </div>

                <!-- Nombre y Apellido -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1">Nombre</label>
                        <input 
                            type="text" 
                            name="nombre" 
                            value="{{ old('nombre') }}"
                            required 
                            placeholder="Juan"
                            class="w-full px-4 py-3 bg-slate-950 border border-slate-800 rounded-xl text-sm text-white placeholder-slate-500 focus:outline-none focus:border-emerald-500 transition-all"
                        />
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1">Apellido</label>
                        <input 
                            type="text" 
                            name="apellido" 
                            value="{{ old('apellido') }}"
                            required 
                            placeholder="Pérez"
                            class="w-full px-4 py-3 bg-slate-950 border border-slate-800 rounded-xl text-sm text-white placeholder-slate-500 focus:outline-none focus:border-emerald-500 transition-all"
                        />
                    </div>
                </div>

                <!-- Correo Electrónico -->
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1">Correo Electrónico</label>
                    <input 
                        type="email" 
                        name="correo" 
                        value="{{ old('correo') }}"
                        required 
                        placeholder="usuario@ejemplo.com"
                        class="w-full px-4 py-3 bg-slate-950 border border-slate-800 rounded-xl text-sm text-white placeholder-slate-500 focus:outline-none focus:border-emerald-500 transition-all"
                    />
                </div>

                <!-- Teléfono -->
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1">Teléfono Móvil</label>
                    <div class="flex gap-2">
                        <select 
                            name="prefijo_telefono" 
                            class="px-3 py-3 bg-slate-950 border border-slate-800 rounded-xl text-sm text-slate-200 font-medium focus:outline-none focus:border-emerald-500 transition-all"
                        >
                            <option value="0412" {{ old('prefijo_telefono') == '0412' ? 'selected' : '' }}>0412</option>
                            <option value="0414" {{ old('prefijo_telefono') == '0414' ? 'selected' : '' }}>0414</option>
                            <option value="0424" {{ old('prefijo_telefono') == '0424' ? 'selected' : '' }}>0424</option>
                            <option value="0416" {{ old('prefijo_telefono') == '0416' ? 'selected' : '' }}>0416</option>
                            <option value="0426" {{ old('prefijo_telefono') == '0426' ? 'selected' : '' }}>0426</option>
                        </select>
                        <input 
                            type="text" 
                            name="telefono_num" 
                            value="{{ old('telefono_num') }}"
                            maxlength="7" 
                            oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                            required 
                            placeholder="1234567"
                            class="w-full px-4 py-3 bg-slate-950 border border-slate-800 rounded-xl text-sm text-white placeholder-slate-500 focus:outline-none focus:border-emerald-500 transition-all"
                        />
                    </div>
                </div>

                <!-- Contraseñas -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1">Contraseña</label>
                        <div class="relative">
                            <input 
                                :type="showRegPass ? 'text' : 'password'" 
                                name="contraseña" 
                                required 
                                placeholder="••••••••"
                                class="w-full px-4 py-3 bg-slate-950 border border-slate-800 rounded-xl text-sm text-white placeholder-slate-500 focus:outline-none focus:border-emerald-500 transition-all pr-10"
                            />
                            <button 
                                type="button" 
                                @click="showRegPass = !showRegPass" 
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-500 hover:text-slate-300 transition-colors"
                            >
                                <svg x-show="!showRegPass" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                <svg x-show="showRegPass" x-cloak class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858-5.908a10.012 10.012 0 014.122-.963c4.478 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21M3 3l18 18"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1">Confirmar</label>
                        <div class="relative">
                            <input 
                                :type="showConfirmPass ? 'text' : 'password'" 
                                name="contraseña_confirmation" 
                                required 
                                placeholder="••••••••"
                                class="w-full px-4 py-3 bg-slate-950 border border-slate-800 rounded-xl text-sm text-white placeholder-slate-500 focus:outline-none focus:border-emerald-500 transition-all pr-10"
                            />
                            <button 
                                type="button" 
                                @click="showConfirmPass = !showConfirmPass" 
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-500 hover:text-slate-300 transition-colors"
                            >
                                <svg x-show="!showConfirmPass" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                <svg x-show="showConfirmPass" x-cloak class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858-5.908a10.012 10.012 0 014.122-.963c4.478 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21M3 3l18 18"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <button 
                    type="submit" 
                    class="w-full py-3.5 bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-bold text-sm rounded-xl transition shadow-md shadow-emerald-500/20 mt-3 cursor-pointer"
                >
                    Crear Cuenta
                </button>
            </form>
        </div>
    </div>
</div>