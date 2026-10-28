<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificación de Correo — Alcaldía de Carrizal</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 font-sans antialiased">
    <div class="min-h-screen flex items-center justify-center p-4">
        <div class="bg-white border border-slate-200/80 rounded-3xl w-full max-w-md p-8 shadow-2xl space-y-6">
            
            <!-- Icono Institucional -->
            <div class="w-16 h-16 bg-emerald-50 text-emerald-600 rounded-2xl mx-auto flex items-center justify-center shadow-inner">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
            </div>

            <div class="text-center space-y-2">
                <h1 class="text-xl font-bold text-slate-900 tracking-tight">Verifica tu Correo Electrónico</h1>
                <p class="text-xs text-slate-500 leading-relaxed">
                    Hemos enviado un enlace de activación a tu bandeja de entrada. Por favor, haz clic en él para completar tu registro.
                </p>
            </div>

            @if(session('success'))
                <div class="p-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-medium text-center">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="p-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs font-medium">
                    {{ $errors->first() }}
                </div>
            @endif

            <!-- Formulario para corregir el correo -->
            <div class="p-5 bg-slate-50/80 rounded-2xl border border-slate-200/80 space-y-4 text-left shadow-sm">
                <p class="text-xs font-bold text-slate-800 flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    ¿El correo registrado tiene un error?
                </p>

                <form action="{{ route('verification.update-email') }}" method="POST" class="space-y-3">
                    @csrf
                    
                    <!-- Input oculto que envía de forma segura el correo actual a PHP -->
                    <input type="hidden" name="correo_actual" value="{{ $correoPendiente }}">

                    <!-- Visualización clara y elegante del correo actual -->
                    <div>
                        <label class="block text-[10px] uppercase tracking-wider font-bold text-slate-500 mb-1">Correo registrado actualmente</label>
                        <div class="w-full bg-white text-slate-900 font-bold border-2 border-slate-200 rounded-xl px-3.5 py-2.5 text-xs shadow-inner flex items-center justify-between">
                            <span class="truncate font-mono">{{ $correoPendiente ?? 'No especificado' }}</span>
                            <span class="text-[10px] font-semibold text-slate-400 bg-slate-100 px-2 py-0.5 rounded-md border border-slate-200 shrink-0">
                                Actual
                            </span>
                        </div>
                    </div>

                    <!-- Campo libre para el correo correcto -->
                    <div>
                        <label class="block text-[10px] uppercase tracking-wider font-bold text-slate-600 mb-1">Escribe el nuevo correo correcto</label>
                        <input 
                            type="email" 
                            name="nuevo_correo" 
                            required 
                            placeholder="correo_correcto@ejemplo.com" 
                            class="w-full bg-white text-slate-900 border border-slate-300 rounded-xl px-3 py-2.5 text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all"
                        >
                    </div>

                    <button type="submit" class="w-full py-2.5 bg-slate-800 hover:bg-slate-900 text-white text-xs font-semibold rounded-xl transition-all shadow-md cursor-pointer flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        Actualizar Correo y Reenviar Enlace
                    </button>
                </form>
            </div>

            <div class="pt-2">
                <a 
                    href="{{ route('login') }}" 
                    class="w-full py-2.5 block bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-xl shadow-md transition-all text-center cursor-pointer"
                >
                    Ir al Inicio de Sesión
                </a>
            </div>

        </div>
    </div>
</body>
</html>