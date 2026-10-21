<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Directorio y Perfil Ambiental - Establecimientos RACDA</title>
    <style>
        @page { size: auto; margin: 0mm; }
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; color: #1e293b; font-size: 11px; line-height: 1.4; margin: 0; padding: 12mm 15mm; background-color: #ffffff; }
        .header { border-left: 6px solid #4f46e5; padding-left: 14px; margin-bottom: 16px; }
        .header h1 { font-size: 16px; color: #0f172a; margin: 2px 0; text-transform: uppercase; font-weight: 800; }
        .header p { font-size: 10px; color: #64748b; margin: 0; }
        .section-title { font-size: 11px; font-weight: bold; color: #0f172a; margin-top: 14px; margin-bottom: 8px; border-bottom: 2px solid #4f46e5; padding-bottom: 3px; text-transform: uppercase; }
        table.data-table { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
        table.data-table th, table.data-table td { border: 1px solid #cbd5e1; padding: 6px 8px; text-align: left; }
        table.data-table th { background-color: #f1f5f9; color: #334155; font-weight: bold; font-size: 10px; text-transform: uppercase; }
        table.data-table td { font-size: 10px; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-mono { font-family: monospace; }
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
        <span style="font-size: 9px; font-weight: bold; color: #4338ca; text-transform: uppercase; background-color: #eef2ff; padding: 3px 8px; border-radius: 4px; display: inline-block; margin-bottom: 4px;">Alcaldía del Municipio Carrizal • Despacho de la Alcaldesa</span>
        <h1>Directorio y Clasificación Ambiental RACDA</h1>
        <p>Resumen general de establecimientos y distribución por categorías de perfiles ambientales y comunas.</p>
    </div>

    <!-- 1. RESUMEN DE TIPOS DE PERFIL -->
    <div class="section-title">1. Resumen Global por Rol Ambiental</div>
    <table class="data-table">
        <thead>
            <tr>
                <th>Categoría de Perfil Ambiental</th>
                <th class="text-center">Total de Establecimientos</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>Generadores</strong></td>
                <td class="text-center font-mono">{{ $totalGeneradores }}</td>
            </tr>
            <tr>
                <td><strong>Manejadores</strong></td>
                <td class="text-center font-mono">{{ $totalManejadores }}</td>
            </tr>
            <tr>
                <td><strong>No Aplica</strong></td>
                <td class="text-center font-mono">{{ $totalNoAplica }}</td>
            </tr>
            <tr style="background-color: #f8fafc;">
                <td><strong>Total General Registrado</strong></td>
                <td class="text-center font-mono"><strong>{{ $totalEmpresas }}</strong></td>
            </tr>
        </tbody>
    </table>

    <!-- 2. DISTRIBUCIÓN POR COMUNA -->
    <div class="section-title">2. Distribución de Establecimientos por Comuna y Rol</div>
    <table class="data-table">
        <thead>
            <tr>
                <th>Comuna / Territorio</th>
                <th class="text-center">Generadores</th>
                <th class="text-center">Manejadores</th>
                <th class="text-center">No Aplica</th>
                <th class="text-center">Total Comuna</th>
            </tr>
        </thead>
        <tbody>
            @forelse($resumenComunasEmpresas as $comuna => $datos)
                <tr>
                    <td><strong>{{ $comuna }}</strong></td>
                    <td class="text-center font-mono">{{ $datos['generador'] }}</td>
                    <td class="text-center font-mono">{{ $datos['manejador'] }}</td>
                    <td class="text-center font-mono">{{ $datos['no_aplica'] }}</td>
                    <td class="text-center font-mono"><strong>{{ $datos['total'] }}</strong></td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center" style="color: #64748b; padding: 15px;">No hay comunas registradas.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Dirección de Digitalización y Despacho Municipal — Sistema de Gestión Ecosocialista de la Alcaldía de Carrizal
    </div>

</body>
</html>