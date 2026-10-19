<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Informe Ejecutivo - Control de Residuos y Desechos</title>
    <style>
        @page {
            size: auto;
            margin: 0mm;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #1e293b;
            font-size: 11px;
            line-height: 1.4;
            margin: 0;
            padding: 12mm 15mm;
            background-color: #ffffff;
        }
        .header {
            border-left: 6px solid #059669;
            padding-left: 14px;
            margin-bottom: 16px;
        }
        .header h1 {
            font-size: 16px;
            color: #0f172a;
            margin: 2px 0;
            text-transform: uppercase;
            font-weight: 800;
        }
        .header p {
            font-size: 10px;
            color: #64748b;
            margin: 0;
        }
        .section-title {
            font-size: 11px;
            font-weight: bold;
            color: #0f172a;
            margin-top: 14px;
            margin-bottom: 8px;
            border-bottom: 2px solid #059669;
            padding-bottom: 3px;
            text-transform: uppercase;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }
        table.data-table th, table.data-table td {
            border: 1px solid #cbd5e1;
            padding: 6px 8px;
            text-align: left;
        }
        table.data-table th {
            background-color: #f1f5f9;
            color: #334155;
            font-weight: bold;
            font-size: 10px;
            text-transform: uppercase;
        }
        table.data-table td {
            font-size: 10px;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-mono { font-family: monospace; }
        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 8px;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
            padding-top: 6px;
        }
        @media print {
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>

    <!-- Botón de impresión idéntico a Poder Popular -->
    <div class="no-print" style="margin-bottom: 12px; text-align: right;">
        <button onclick="window.print()" style="padding: 6px 12px; background-color: #0f172a; color: #ffffff; border: none; border-radius: 6px; font-weight: bold; cursor: pointer; font-size: 10px;">
            🖨️ Imprimir / Guardar como PDF
        </button>
    </div>

    <div class="header">
        <span style="font-size: 9px; font-weight: bold; color: #047857; text-transform: uppercase; background-color: #ecfdf5; padding: 3px 8px; border-radius: 4px; display: inline-block; margin-bottom: 4px;">Alcaldía del Municipio Carrizal • Despacho de la Alcaldesa</span>
        <h1>Control Comercial RACDA y Declaraciones de Residuos</h1>
        <p>Reporte ejecutivo consolidado de establecimientos comerciales y balance de toneladas de desechos.</p>
    </div>

    <!-- 1. RESUMEN GLOBAL -->
    <div class="section-title">1. Resumen Ejecutivo Global</div>
    <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom: 12px;">
        <tr>
            <td width="49%" valign="top" style="border: 1px solid #cbd5e1; border-radius: 6px; padding: 10px; background-color: #f8fafc;">
                <strong>Parque Comercial e Industrial RACDA</strong>
                <table class="data-table" style="margin-top: 6px; margin-bottom: 0;">
                    <tr><td>Total Registradas:</td><td class="text-right font-mono"><strong>{{ $totalEmpresas ?? 0 }}</strong></td></tr>
                    <tr><td>Generadoras:</td><td class="text-right font-mono">{{ $totalGeneradoras ?? 0 }}</td></tr>
                    <tr><td>Manejadoras:</td><td class="text-right font-mono">{{ $totalManejadoras ?? 0 }}</td></tr>
                    <tr><td>No Aplica:</td><td class="text-right font-mono">{{ $totalNoAplica ?? 0 }}</td></tr>
                </table>
            </td>
            <td width="2%"></td>
            <td width="49%" valign="top" style="border: 1px solid #cbd5e1; border-radius: 6px; padding: 10px; background-color: #f8fafc;">
                <strong>Balance General de Residuos</strong>
                <table class="data-table" style="margin-top: 6px; margin-bottom: 0;">
                    <tr><td>Toneladas Comunes:</td><td class="text-right font-mono"><strong>{{ number_format($totalesPorCategoria['comunes'] ?? 0, 3) }} Ton</strong></td></tr>
                    <tr><td>Toneladas Reciclables:</td><td class="text-right font-mono"><strong>{{ number_format($totalesPorCategoria['reciclables'] ?? 0, 3) }} Ton</strong></td></tr>
                    <tr><td>Toneladas Peligrosas:</td><td class="text-right font-mono"><strong>{{ number_format($totalesPorCategoria['peligrosas'] ?? 0, 3) }} Ton</strong></td></tr>
                    <tr><td><strong>Total Acumulado:</strong></td><td class="text-right font-mono"><strong>{{ number_format(($totalesPorCategoria['comunes'] ?? 0) + ($totalesPorCategoria['reciclables'] ?? 0) + ($totalesPorCategoria['peligrosas'] ?? 0), 3) }} Ton</strong></td></tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- 2. BALANCE POR COMUNA -->
    <div class="section-title">2. Consolidado por Comuna y Categoría</div>
    <table class="data-table">
        <thead>
            <tr>
                <th>Circuito / Comuna</th>
                <th class="text-center">Empresas</th>
                <th class="text-right">Comunes (Ton)</th>
                <th class="text-right">Reciclables (Ton)</th>
                <th class="text-right">Peligrosas (Ton)</th>
                <th class="text-right">Total Declarado</th>
            </tr>
        </thead>
        <tbody>
            @forelse($resumenPorComuna as $comuna => $data)
                <tr>
                    <td><strong>{{ $comuna }}</strong></td>
                    <td class="text-center">{{ $data['total_empresas'] }}</td>
                    <td class="text-right font-mono">{{ number_format($data['comunes'], 3) }}</td>
                    <td class="text-right font-mono">{{ number_format($data['reciclables'], 3) }}</td>
                    <td class="text-right font-mono">{{ number_format($data['peligrosas'], 3) }}</td>
                    <td class="text-right font-mono"><strong>{{ number_format($data['total_toneladas'], 3) }} Ton</strong></td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center" style="color: #64748b; padding: 15px;">No se registran datos de declaraciones por comuna.</td></tr>
            @endforelse
        </tbody>
    </table>

    <!-- 3. LIDERES POR COMUNA -->
    <div class="section-title">3. Establecimientos Líderes en Generación por Comuna</div>
    <table class="data-table">
        <thead>
            <tr>
                <th>Comuna / Territorio</th>
                <th>Mayor Generador - Comunes</th>
                <th>Mayor Generador - Reciclables</th>
                <th>Mayor Generador - Peligrosos</th>
            </tr>
        </thead>
        <tbody>
            @forelse($lideresPorComuna as $comuna => $lideres)
                <tr>
                    <td><strong>{{ $comuna }}</strong></td>
                    <td>
                        @if($lideres['comun'])
                            {{ $lideres['comun']->razon_social }}<br><span style="font-size: 9px; color: #64748b;">({{ number_format($lideres['comun']->total, 3) }} Ton)</span>
                        @else
                            <span style="color: #94a3b8;">Sin registro</span>
                        @endif
                    </td>
                    <td>
                        @if($lideres['reciclable'])
                            {{ $lideres['reciclable']->razon_social }}<br><span style="font-size: 9px; color: #64748b;">({{ number_format($lideres['reciclable']->total, 3) }} Ton)</span>
                        @else
                            <span style="color: #94a3b8;">Sin registro</span>
                        @endif
                    </td>
                    <td>
                        @if($lideres['peligroso'])
                            {{ $lideres['peligroso']->razon_social }}<br><span style="font-size: 9px; color: #64748b;">({{ number_format($lideres['peligroso']->total, 3) }} Ton)</span>
                        @else
                            <span style="color: #94a3b8;">Sin registro</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" class="text-center" style="color: #64748b; padding: 15px;">Sin información sectorial disponible.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Dirección de Digitalización y Despacho Municipal — Sistema de Gestión Ecosocialista de la Alcaldía de Carrizal
    </div>

</body>
</html>