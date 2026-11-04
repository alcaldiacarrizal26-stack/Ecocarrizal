<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Validación de Expediente — Alcaldía de Carrizal</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-100 font-sans antialiased text-slate-800">
    <div class="min-h-screen flex flex-col items-center justify-center p-4">
        <div class="max-w-xl w-full bg-white rounded-3xl shadow-xl border border-slate-200 p-8 space-y-6">
            
            <!-- Cabecera Oficial -->
            <div class="text-center border-b border-slate-100 pb-6">
                <span class="px-3 py-1 bg-emerald-50 text-emerald-700 text-[10px] font-bold uppercase tracking-wider rounded-lg border border-emerald-200">
                    Alcaldía del Municipio Carrizal
                </span>
                <h1 class="text-xl font-black text-slate-900 mt-2">Certificado de Autenticidad</h1>
                <p class="text-xs text-slate-500 mt-1">Sistema Ecosocialista de Gestión Municipal</p>
            </div>

            @if($solicitud)
                <!-- CASO 1: EL EXPEDIENTE SÍ EXISTE Y ES VÁLIDO -->
                <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-2xl flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-emerald-500 text-white flex items-center justify-center font-black text-lg shrink-0">
                        ✓
                    </div>
                    <div>
                        <p class="text-xs font-bold text-emerald-900 uppercase">Documento Válido y Oficial</p>
                        <p class="text-[11px] text-emerald-700">Este expediente se encuentra registrado legítimamente en la base de datos municipal.</p>
                    </div>
                </div>

                <!-- Detalles del Trámite -->
                <div class="space-y-3 text-xs bg-slate-50 p-5 rounded-2xl border border-slate-100">
                    <div class="flex justify-between border-b border-slate-200/60 pb-2">
                        <span class="text-slate-400 font-semibold">Código de Expediente:</span>
                        <span class="font-mono font-bold text-indigo-600">{{ $solicitud->codigo_expediente }}</span>
                    </div>
                    <div class="flex justify-between border-b border-slate-200/60 pb-2">
                        <span class="text-slate-400 font-semibold">Tipo de Trámite:</span>
                        <span class="font-bold text-slate-700 uppercase">{{ str_replace('_', ' ', $solicitud->tipo_tramite) }}</span>
                    </div>
                    <div class="flex justify-between border-b border-slate-200/60 pb-2">
                        <span class="text-slate-400 font-semibold">Solicitante:</span>
                        <span class="font-bold text-slate-700">
                            @php
                                $u = $solicitud->usuario ?? null;
                                $nombreCompleto = trim(($u->nombre ?? $u->name ?? '') . ' ' . ($u->apellido ?? $u->last_name ?? ''));
                            @endphp
                            {{ $nombreCompleto !== '' ? $nombreCompleto : 'Ciudadano Registrado (ID: ' . $solicitud->user_id . ')' }}
                        </span>
                    </div>
                    <div class="flex justify-between border-b border-slate-200/60 pb-2">
                        <span class="text-slate-400 font-semibold">Ubicación Territorial:</span>
                        <span class="font-bold text-slate-700">{{ $solicitud->comuna }} — {{ $solicitud->sector }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-400 font-semibold">Estatus del Trámite:</span>
                        <span class="font-bold uppercase text-emerald-600">{{ $solicitud->estado }}</span>
                    </div>
                </div>
            @else
                <!-- CASO 2: EL EXPEDIENTE NO EXISTE O EL CÓDIGO ES FALSO/INVÁLIDO -->
                <div class="p-4 bg-rose-50 border border-rose-200 rounded-2xl flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-rose-600 text-white flex items-center justify-center font-black text-lg shrink-0">
                        ✕
                    </div>
                    <div>
                        <p class="text-xs font-bold text-rose-900 uppercase">Documento Inválido o Inexistente</p>
                        <p class="text-[11px] text-rose-700">El código de expediente escaneado (<strong class="font-mono">{{ $codigo }}</strong>) no figura en los registros oficiales de la Alcaldía.</p>
                    </div>
                </div>

                <div class="p-5 bg-slate-50 rounded-2xl border border-slate-100 text-center space-y-2">
                    <p class="text-xs text-slate-600 font-medium">
                        Este comprobante podría haber sido alterado o no pertenece a un trámite oficial emitido por el Sistema Ecosocialista de Carrizal.
                    </p>
                </div>
            @endif

            <div class="text-center pt-2">
                <p class="text-[10px] text-slate-400">
                    Este documento digital cuenta con respaldo institucional en el servidor de la Alcaldía del Municipio Carrizal.
                </p>
            </div>

        </div>
    </div>
</body>
</html>