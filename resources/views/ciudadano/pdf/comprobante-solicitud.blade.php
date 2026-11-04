<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Comprobante Oficial de Solicitud — {{ $solicitud->codigo_expediente }}</title>
    <style>
        @page { size: letter; margin: 0; }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #1e293b;
            font-size: 11px;
            line-height: 1.5;
            margin: 0;
            padding: 15mm 20mm;
            background-color: #ffffff;
        }
        .header-container {
            display: table;
            width: 100%;
            border-bottom: 2px solid #059669;
            padding-bottom: 12px;
            margin-bottom: 16px;
        }
        .header-left {
            display: table-cell;
            vertical-align: middle;
        }
        .header-right {
            display: table-cell;
            vertical-align: middle;
            text-align: right;
        }
        .titulo-expediente {
            font-size: 16px;
            font-weight: 800;
            color: #0f172a;
            margin: 2px 0;
            text-transform: uppercase;
        }
        .badge-estatus {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            background-color: #ecfdf5;
            color: #047857;
            border: 1px solid #a7f3d0;
        }
        .seccion-titulo {
            font-size: 11px;
            font-weight: bold;
            color: #0f172a;
            margin-top: 16px;
            margin-bottom: 8px;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 4px;
            text-transform: uppercase;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
        }
        table.data-table th, table.data-table td {
            border: 1px solid #cbd5e1;
            padding: 6px 10px;
            text-align: left;
            font-size: 10px;
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
            padding: 10px;
            border-radius: 8px;
            text-align: center;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 8px;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
            padding-top: 8px;
        }
        @media print { .no-print { display: none !important; } }
    </style>
</head>
<body>

    <div class="no-print" style="margin-bottom: 15px; text-align: right;">
        <button onclick="window.print()" style="padding: 8px 16px; background-color: #0f172a; color: #ffffff; border: none; border-radius: 8px; font-weight: bold; cursor: pointer; font-size: 11px;">
            🖨️ Imprimir / Guardar como PDF
        </button>
    </div>

    <!-- CABECERA INSTITUCIONAL -->
    <div class="header-container">
        <div class="header-left">
            <span style="font-size: 9px; font-weight: bold; color: #047857; text-transform: uppercase;">Alcaldía del Municipio Carrizal • Dirección de Ambiente</span>
            <h1 class="titulo-expediente">Comprobante de Registro de Solicitud</h1>
            <p style="margin:0; font-size: 10px; color: #64748b;">Código de Expediente: <strong style="color: #0f172a;">{{ $solicitud->codigo_expediente }}</strong></p>
        </div>
        <div class="header-right">
            <span class="badge-estatus">Estatus: {{ strtoupper($solicitud->estado) }}</span>
            <p style="margin: 4px 0 0 0; font-size: 9px; color: #64748b;">Fecha Emisión: {{ $solicitud->created_at->format('d/m/Y h:i A') }}</p>
        </div>
    </div>

    <!-- DATOS DEL TRÁMITE Y SOLICITANTE -->
    <div class="seccion-titulo">1. Datos Principales del Trámite y Solicitante</div>
    <table class="data-table">
        <tr>
            <th>Tipo de Trámite</th>
            <td><strong>{{ strtoupper(str_replace('_', ' ', $solicitud->tipo_tramite)) }}</strong></td>
        </tr>
        <tr>
            <th>Solicitante</th>
            <td>
                @php
                    $u = $solicitud->usuario ?? null;
                    $nombreCompleto = trim(($u->nombre ?? $u->name ?? '') . ' ' . ($u->apellido ?? $u->last_name ?? ''));
                    $cedula = $u->cedula ?? $u->dni ?? 'N/D';
                @endphp
                <strong>{{ $nombreCompleto !== '' ? $nombreCompleto : 'Ciudadano (ID: ' . $solicitud->user_id . ')' }}</strong> — C.I.: {{ $cedula }}
            </td>
        </tr>
        <tr>
            <th>Ubicación Territorial</th>
            <td>Comuna: <strong>{{ $solicitud->comuna }}</strong> | Sector: <strong>{{ $solicitud->sector }}</strong></td>
        </tr>
        <tr>
            <th>Dirección Exacta</th>
            <td>{{ $solicitud->direccion_exacta }}</td>
        </tr>
        <tr>
            <th>Motivo de la Solicitud</th>
            <td>{{ $solicitud->motivo_descripcion }}</td>
        </tr>
    </table>

    <!-- RECAUDOS CONSIGNADOS -->
    <div class="seccion-titulo">2. Recaudos Validados en Expediente</div>
    <table class="data-table">
        <thead>
            <tr style="background-color: #f1f5f9;">
                <th style="width: 70%;">Tipo de Documento</th>
                <th style="width: 30%; text-align: center;">Estatus</th>
            </tr>
        </thead>
        <tbody>
            @forelse($solicitud->recaudos as $rec)
                <tr>
                    <td><strong>{{ strtoupper(str_replace('_', ' ', $rec->tipo_recaudo)) }}</strong></td>
                    <td style="text-align: center; color: #059669; font-weight: bold;">CONSIGNADO</td>
                </tr>
            @empty
                <tr>
                    <td colspan="2" style="text-align: center; color: #94a3b8;">Sin documentos adjuntos.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- SECCIÓN DE VERIFICACIÓN QR Y SELLO DIGITAL -->
    <div class="seccion-titulo">3. Validación y Autenticidad Digital</div>
    <table style="width: 100%; border: none;">
        <tr>
            <td style="width: 70%; vertical-align: top; padding-right: 15px; border: none;">
                <p style="font-size: 9px; color: #475569; margin-top: 0;">
                    El presente documento certifica que la solicitud identificada con el expediente <strong>{{ $solicitud->codigo_expediente }}</strong> se encuentra formalmente registrada en el <strong>Sistema Ecosocialista de la Alcaldía de Carrizal</strong> y ha cumplido con la entrega inicial de recaudos exigidos.
                </p>
                <p style="font-size: 8px; color: #94a3b8; font-style: italic;">
                    Nota: Este comprobante no constituye la aprobación final del permiso o inspección. Escanee el código QR adjunto para verificar la validez de este documento en el portal oficial municipal.
                </p>
            </td>
            <td style="width: 30%; vertical-align: top; border: none;">
                <div class="qr-box">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=110x110&data={{ urlencode($urlVerificacion) }}" alt="Código QR de Validación" style="width: 110px; height: 110px;">
                    <p style="margin: 4px 0 0 0; font-size: 8px; font-weight: bold; color: #047857;">VERIFICACIÓN QR</p>
                </div>
            </td>
        </tr>
    </table>

    <div class="footer">
        Alcaldía del Municipio Carrizal • Dirección de Ambiente y Ecosocialismo • Documento Generado Digitalmente
    </div>

</body>
</html>