<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Acta Oficial de Inspección — {{ $inspeccion->codigo_acta }}</title>
    <style>
        @page { size: letter; margin: 0; }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #1e293b;
            font-size: 10.5px;
            line-height: 1.4;
            margin: 0;
            padding: 12mm 18mm;
            background-color: #ffffff;
        }
        .header-container {
            display: table;
            width: 100%;
            border-bottom: 2px solid #059669;
            padding-bottom: 10px;
            margin-bottom: 14px;
        }
        .header-left { display: table-cell; vertical-align: middle; }
        .header-right { display: table-cell; vertical-align: middle; text-align: right; }
        .titulo-acta {
            font-size: 15px;
            font-weight: 800;
            color: #0f172a;
            margin: 2px 0;
            text-transform: uppercase;
        }
        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .badge-success { background-color: #ecfdf5; color: #047857; border: 1px solid #a7f3d0; }
        .badge-danger { background-color: #fff1f2; color: #be123c; border: 1px solid #fecdd3; }
        .seccion-titulo {
            font-size: 10.5px;
            font-weight: bold;
            color: #0f172a;
            margin-top: 14px;
            margin-bottom: 6px;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 3px;
            text-transform: uppercase;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        table.data-table th, table.data-table td {
            border: 1px solid #cbd5e1;
            padding: 5px 8px;
            text-align: left;
            font-size: 9.5px;
        }
        table.data-table th {
            background-color: #f8fafc;
            color: #475569;
            font-weight: bold;
            text-transform: uppercase;
            width: 30%;
        }
        .qr-box {
            border: 1px dashed #059669;
            background-color: #f0fdf4;
            padding: 8px;
            border-radius: 6px;
            text-align: center;
        }
        .footer {
            margin-top: 25px;
            text-align: center;
            font-size: 8px;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
            padding-top: 6px;
        }
        @media print { .no-print { display: none !important; } }
    </style>
</head>
<body>

    <div class="no-print" style="margin-bottom: 15px; text-align: right;">
        <button onclick="window.print()" style="padding: 8px 16px; background-color: #0f172a; color: #ffffff; border: none; border-radius: 8px; font-weight: bold; cursor: pointer; font-size: 11px;">
            🖨️ Imprimir / Guardar Acta PDF
        </button>
    </div>

    <!-- CABECERA INSTITUCIONAL -->
    <div class="header-container">
        <div class="header-left">
            <span style="font-size: 8.5px; font-weight: bold; color: #047857; text-transform: uppercase;">Alcaldía del Municipio Carrizal • Dirección de Ambiente</span>
            <h1 class="titulo-acta">Acta Oficial de Inspección Ambiental</h1>
            <p style="margin:0; font-size: 9.5px; color: #64748b;">Código de Acta: <strong style="color: #0f172a;">{{ $inspeccion->codigo_acta }}</strong></p>
        </div>
        <div class="header-right">
            <span class="badge badge-success">Dictamen: {{ str_replace('_', ' ', $inspeccion->dictamen) }}</span>
            <p style="margin: 4px 0 0 0; font-size: 8.5px; color: #64748b;">Fecha Visita: {{ \Carbon\Carbon::parse($inspeccion->fecha_visita)->format('d/m/Y h:i A') }}</p>
        </div>
    </div>

    @php
        $empresa = $inspeccion->ordenInspeccion->empresa ?? $inspeccion->empresa ?? null;
        $orden = $inspeccion->ordenInspeccion ?? null;
    @endphp

    @if($empresa)
    <div class="seccion-titulo">1. Datos de la Entidad o Empresa Fiscalizada</div>
    <table class="data-table">
        <tr>
            <th>Razón Social</th>
            <td><strong>{{ $empresa->razon_social }}</strong> (RIF: {{ $empresa->rif }})</td>
        </tr>
        <tr>
            <th>Tipo de Inspección</th>
            <td>{{ $orden?->tipo_inspeccion ?? $inspeccion->tipo_inspeccion ?? 'ORDINARIA' }}</td>
        </tr>
    </table>
    @endif

    <!-- DATOS DEL INSPECTOR / FUNCIONARIO -->
    <div class="seccion-titulo">2. Identificación del Funcionario Fiscalizador</div>
    <table class="data-table">
        <tr>
            <th>Inspector Responsable</th>
            <td><strong>{{ $inspeccion->inspector->nombre ?? '' }} {{ $inspeccion->inspector->apellido ?? '' }}</strong></td>
        </tr>
        <tr>
            <th>Credencial / Perfil Oficial</th>
            <td>
                <span style="font-family: monospace; font-weight: bold; color: #047857;">
                    {{ $inspeccion->inspector->inspector->codigo_credencial ?? 'FUNCIONARIO DIRECTIVO / S.C.' }}
                </span>
            </td>
        </tr>
    </table>

    <!-- OBSERVACIONES Y RECOMENDACIONES -->
    <div class="seccion-titulo">3. Hallazgos y Observaciones de Campo</div>
    <div style="background-color: #f8fafc; border: 1px solid #cbd5e1; padding: 8px 10px; border-radius: 6px; margin-bottom: 10px; font-size: 9.5px;">
        {{ $inspeccion->observaciones_campo }}
    </div>

    @if($inspeccion->recomendaciones)
    <div class="seccion-titulo">4. Recomendaciones y Medidas Correctivas</div>
    <div style="background-color: #fffbeb; border: 1px solid #fde68a; padding: 8px 10px; border-radius: 6px; margin-bottom: 10px; font-size: 9.5px;">
        {{ $inspeccion->recomendaciones }}
    </div>
    @endif

    <!-- SECCIÓN DE VERIFICACIÓN QR -->
    <div class="seccion-titulo">5. Autenticidad y Verificación Digital</div>
    <table style="width: 100%; border: none; margin-top: 5px;">
        <tr>
            <td style="width: 70%; vertical-align: top; padding-right: 15px; border: none;">
                <p style="font-size: 8.5px; color: #475569; margin-top: 0;">
                    Este documento legal certifica la visita de fiscalización ambiental realizada por la Dirección de Ambiente de la Alcaldía del Municipio Carrizal. Escanee el código QR para corroborar la integridad y validez oficial de esta acta en el portal municipal.
                </p>
            </td>
            <td style="width: 30%; vertical-align: top; border: none;">
                <div class="qr-box">
                    <img src="data:image/svg+xml;base64,{{ $qrCode }}" alt="QR Validación" style="width: 95px; height: 95px;">
                    <p style="margin: 3px 0 0 0; font-size: 7.5px; font-weight: bold; color: #047857;">VALIDAR ACTA</p>
                </div>
            </td>
        </tr>
    </table>

    <!-- ÁREA DE FIRMAS -->
    <table style="width: 100%; border: none; margin-top: 35px; text-align: center; font-size: 9px; color: #475569;">
        <tr>
            <td style="width: 50%; border: none; border-top: 1px solid #94a3b8; padding-top: 4px;">
                <strong>{{ $inspeccion->inspector->nombre ?? '' }} {{ $inspeccion->inspector->apellido ?? '' }}</strong><br>
                Inspector Ambiental / Fiscalizador
            </td>
            <td style="width: 50%; border: none; border-top: 1px solid #94a3b8; padding-top: 4px;">
                <strong>Firma del Representante / Receptor</strong><br>
                C.I.: ________________________
            </td>
        </tr>
    </table>

    <div class="footer">
        Alcaldía del Municipio Carrizal • Dirección de Ambiente y Ecosocialismo • Acta Certificada Digitalmente
    </div>

</body>
</html>