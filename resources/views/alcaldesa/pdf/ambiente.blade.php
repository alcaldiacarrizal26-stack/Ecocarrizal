<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Informe Ejecutivo - Gestión Ambiental y Permisología</title>
    <style>
        @page { size: auto; margin: 0mm; }
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; color: #1e293b; font-size: 11px; line-height: 1.4; margin: 0; padding: 12mm 15mm; background-color: #ffffff; }
        .header { border-left: 6px solid #059669; padding-left: 14px; margin-bottom: 16px; }
        .header h1 { font-size: 16px; color: #0f172a; margin: 2px 0; text-transform: uppercase; font-weight: 800; }
        .header p { font-size: 10px; color: #64748b; margin: 0; }
        .section-title { font-size: 11px; font-weight: bold; color: #0f172a; margin-top: 14px; margin-bottom: 8px; border-bottom: 2px solid #059669; padding-bottom: 3px; text-transform: uppercase; }
        table.data-table { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
        table.data-table th, table.data-table td { border: 1px solid #cbd5e1; padding: 6px 8px; text-align: left; }
        table.data-table th { background-color: #f1f5f9; color: #334155; font-weight: bold; font-size: 10px; text-transform: uppercase; }
        table.data-table td { font-size: 10px; }
        .text-center { text-align: center; }
        .font-mono { font-family: monospace; }
        .grid-3 { width: 100%; margin-bottom: 12px; }
        .grid-3 td { width: 33.33%; vertical-align: top; border: none; padding: 0 4px 0 0; }
        .card { background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 10px; }
        .footer { margin-top: 20px; text-align: center; font-size: 8px; color: #94a3b8; border-top: 1px solid #e2e8f0; padding-top: 6px; }
        @media print { .no-print { display: none !important; } }
    </style>
</head>
<body>

    <div class="no-print" style="margin-bottom: 12px; text-align: right;">
        <button onclick="window.print()" style="padding: 6px 12px; background-color: #0f172a; color: #ffffff; border: none; border-radius: 6px; font-weight: bold; cursor: pointer; font-size: 10px;">
            🖨️ Imprimir / Guardar como PDF
        </button>
    </div>

    <div class="header">
        <span style="font-size: 9px; font-weight: bold; color: #047857; text-transform: uppercase; background-color: #ecfdf5; padding: 3px 8px; border-radius: 4px; display: inline-block; margin-bottom: 4px;">Alcaldía del Municipio Carrizal • Despacho de la Alcaldesa</span>
        <h1>Informe Ejecutivo — Gestión Ambiental y Permisología</h1>
        <p>Período analizado: Del <strong>{{ \Carbon\Carbon::parse($fechaInicio)->format('d/m/Y') }}</strong> al <strong>{{ \Carbon\Carbon::parse($fechaFin)->format('d/m/Y') }}</strong></p>
    </div>

    <!-- TARJETAS DE INDICADORES GENERALES -->
    <table class="grid-3">
        <tr>
            <td>
                <div class="card">
                    <p style="margin:0; font-size:9px; font-weight:bold; color:#64748b; text-transform:uppercase;">Trámites sin Cierre</p>
                    <p style="margin:4px 0 0 0; font-size:16px; font-weight:900; color:#d97706;" class="font-mono">{{ $tramitesSinCierre }}</p>
                </div>
            </td>
            <td>
                <div class="card">
                    <p style="margin:0; font-size:9px; font-weight:bold; color:#64748b; text-transform:uppercase;">Sin Inspector</p>
                    <p style="margin:4px 0 0 0; font-size:16px; font-weight:900; color:#dc2626;" class="font-mono">{{ $tramitesSinInspector }}</p>
                </div>
            </td>
            <td>
                <div class="card">
                    <p style="margin:0; font-size:9px; font-weight:bold; color:#64748b; text-transform:uppercase;">Denuncias Rechazadas</p>
                    <p style="margin:4px 0 0 0; font-size:16px; font-weight:900; color:#e11d48;" class="font-mono">{{ $denunciasRechazadas }} / {{ $totalDenuncias }}</p>
                </div>
            </td>
        </tr>
    </table>

    <!-- 1. TIPOS DE TRÁMITES Y ESTATUS -->
    <div class="section-title">1. Tipos de Trámites, Estatus y Volumetría Total</div>
    <table class="data-table">
        <thead>
            <tr>
                <th>Tipo de Trámite</th>
                <th>Desglose por Estatus (Cantidad)</th>
                <th class="text-center">Total Trámites</th>
            </tr>
        </thead>
        <tbody>
            @forelse($resumenPorTipo as $tipo => $datos)
                <tr>
                    <td><strong>{{ strtoupper(str_replace('_', ' ', $tipo)) }}</strong></td>
                    <td>
                        @foreach($datos['estatus'] as $est => $cant)
                            <span style="display:inline-block; margin-right:8px; background:#f1f5f9; padding:2px 6px; border-radius:4px; font-size:9px;">
                                <strong>{{ strtoupper($est) }}:</strong> {{ $cant }}
                            </span>
                        @endforeach
                    </td>
                    <td class="text-center font-mono"><strong>{{ $datos['total'] }}</strong></td>
                </tr>
            @empty
                <tr><td colspan="3" class="text-center" style="color:#64748b; padding:15px;">No hay registros en este período.</td></tr>
            @endforelse
        </tbody>
    </table>

    <!-- 2. DISTRIBUCIÓN POR COMUNA -->
    <div class="section-title">2. Consolidado por Comuna y Trámites</div>
    <table class="data-table">
        <thead>
            <tr>
                <th>Comuna / Territorio</th>
                <th>Desglose por Tipo de Trámite</th>
                <th class="text-center">Total Comuna</th>
            </tr>
        </thead>
        <tbody>
            @forelse($resumenPorComuna as $comuna => $datos)
                <tr>
                    <td><strong>{{ $comuna }}</strong></td>
                    <td>
                        @foreach($datos['tipos'] as $t => $c)
                            <span style="display:inline-block; margin-right:8px; background:#f8fafc; padding:2px 6px; border-radius:4px; font-size:9px; border:1px solid #e2e8f0;">
                                {{ strtoupper(str_replace('_', ' ', $t)) }}: <strong>{{ $c }}</strong>
                            </span>
                        @endforeach
                    </td>
                    <td class="text-center font-mono"><strong>{{ $datos['total'] }}</strong></td>
                </tr>
            @empty
                <tr><td colspan="3" class="text-center" style="color:#64748b; padding:15px;">No hay registros por comuna.</td></tr>
            @endforelse
        </tbody>
    </table>

    <!-- 3. COMPENSACIONES AMBIENTALES -->
    <div class="section-title">3. Estado de Compensaciones Ambientales</div>
    <table class="data-table">
        <thead>
            <tr>
                <th>Compensaciones Concretadas (Completadas)</th>
                <th>Compensaciones a Deber (Pendientes)</th>
                <th class="text-center">Total Compensaciones</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="font-mono" style="color: #059669; font-weight: bold;">{{ $compensacionesConcretadas }}</td>
                <td class="font-mono" style="color: #d97706; font-weight: bold;">{{ $compensacionesADeber }}</td>
                <td class="text-center font-mono"><strong>{{ $totalCompensaciones }}</strong></td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        Dirección de Digitalización y Despacho Municipal — Sistema de Gestión Ecosocialista de la Alcaldía de Carrizal
    </div>

</body>
</html>