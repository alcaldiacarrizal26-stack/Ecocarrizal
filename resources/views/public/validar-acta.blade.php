<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Validación de Acta de Inspección — Alcaldía de Carrizal</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-100 font-sans antialiased text-slate-800">
    <div class="min-h-screen flex flex-col items-center justify-center p-4">
        <div class="max-w-xl w-full bg-white rounded-3xl shadow-xl border border-slate-200 p-8 space-y-6">
            
            <div class="text-center border-b border-slate-100 pb-6">
                <span class="px-3 py-1 bg-emerald-50 text-emerald-700 text-[10px] font-bold uppercase tracking-wider rounded-lg border border-emerald-200">
                    Alcaldía del Municipio Carrizal
                </span>
                <h1 class="text-xl font-black text-slate-900 mt-2">Certificado de Autenticidad de Acta</h1>
                <p class="text-xs text-slate-500 mt-1">Sistema Ecosocialista de Gestión Municipal</p>
            </div>

            @if($inspeccion)
                <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-2xl flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-emerald-500 text-white flex items-center justify-center font-black text-lg shrink-0">✓</div>
                    <div>
                        <p class="text-xs font-bold text-emerald-900 uppercase">Acta Oficial Válida y Legítima</p>
                        <p class="text-[11px] text-emerald-700">Esta acta de inspección se encuentra registrada oficialmente en los servidores de la Alcaldía.</p>
                    </div>
                </div>

                <div class="space-y-3 text-xs bg-slate-50 p-5 rounded-2xl border border-slate-100">
                    <div class="flex justify-between border-b border-slate-200/60 pb-2">
                        <span class="text-slate-400 font-semibold">Código de Acta:</span>
                        <span class="font-mono font-bold text-indigo-600">{{ $inspeccion->codigo_acta }}</span>
                    </div>
                    <div class="flex justify-between border-b border-slate-200/60 pb-2">
                        <span class="text-slate-400 font-semibold">Inspector Fiscalizador:</span>
                        <span class="font-bold text-slate-700">{{ $inspeccion->inspector->nombre ?? '' }} {{ $inspeccion->inspector->apellido ?? '' }}</span>
                    </div>
                    <div class="flex justify-between border-b border-slate-200/60 pb-2">
                        <span class="text-slate-400 font-semibold">Credencial / Perfil Oficial:</span>
                        <span class="font-mono font-bold text-emerald-700">
                            {{ $inspeccion->inspector->inspector->codigo_credencial ?? 'FUNCIONARIO DIRECTIVO / S.C.' }}
                        </span>
                    </div>
                    <div class="flex justify-between border-b border-slate-200/60 pb-2">
                        <span class="text-slate-400 font-semibold">Dictamen:</span>
                        <span class="font-bold text-slate-700 uppercase">{{ str_replace('_', ' ', $inspeccion->dictamen) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-400 font-semibold">Fecha de Visita:</span>
                        <span class="font-bold text-slate-700">{{ \Carbon\Carbon::parse($inspeccion->fecha_visita)->format('d/m/Y h:i A') }}</span>
                    </div>
                </div>
            @else
                <div class="p-4 bg-rose-50 border border-rose-200 rounded-2xl flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-rose-600 text-white flex items-center justify-center font-black text-lg shrink-0">✕</div>
                    <div>
                        <p class="text-xs font-bold text-rose-900 uppercase">Acta Inválida o Inexistente</p>
                        <p class="text-[11px] text-rose-700">El código escaneado (<strong class="font-mono">{{ $codigo }}</strong>) no figura en los registros oficiales de la municipalidad.</p>
                    </div>
                </div>
            @endif

            <div class="text-center pt-2">
                <p class="text-[10px] text-slate-400">Alcaldía del Municipio Carrizal • Dirección de Ambiente</p>
            </div>

        </div>
    </div>
</body>
</html>