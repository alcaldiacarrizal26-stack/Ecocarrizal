<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Informe Ejecutivo - Poder Popular</title>
    <style>
        @page {
            size: auto;
            margin: 0mm;
        }

        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #1e293b;
            line-height: 1.4;
            font-size: 11px;
            margin: 0;
            padding: 12mm 15mm;
            background-color: #ffffff;
        }

        /* Encabezado Institucional Carrizal */
        .header {
            border-left: 6px solid #059669;
            padding-left: 14px;
            margin-bottom: 16px;
        }
        .header .sub-badge {
            font-size: 9px;
            font-weight: bold;
            color: #047857;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            background-color: #ecfdf5;
            padding: 3px 8px;
            border-radius: 4px;
            display: inline-block;
            margin-bottom: 4px;
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
            letter-spacing: 0.5px;
        }

        .comuna-group {
            margin-bottom: 10px;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            overflow: hidden;
        }
        .comuna-header {
            background-color: #f1f5f9;
            padding: 6px 10px;
            font-weight: bold;
            color: #0f172a;
            font-size: 10px;
            text-transform: uppercase;
            border-bottom: 1px solid #e2e8f0;
        }

        .footer {
            margin-top: 15px;
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

    <!-- Botón de respaldo manual -->
    <div class="no-print" style="margin-bottom: 12px; text-align: right;">
        <button onclick="window.print()" style="padding: 6px 12px; background-color: #0f172a; color: #ffffff; border: none; border-radius: 6px; font-weight: bold; cursor: pointer; font-size: 10px;">
            🖨️ Imprimir / Guardar como PDF
        </button>
    </div>

    <!-- Encabezado Institucional Dinámico -->
    <div class="header">
        <span class="sub-badge">Alcaldía del Municipio Carrizal • Despacho de la Alcaldesa</span>
        <h1>Informe Ejecutivo: Priorización del Poder Popular</h1>
        <p>Análisis territorial de incidencias y trámites comunitarios — Registro de {{ $tipoRegistro === 'solicitud' ? 'Solicitudes Ambientales y Permisología' : 'Denuncias' }}</p>
    </div>

    @php
        $totalReg = $registrosGenerales->count();
        $resueltos = $registrosGenerales->filter(function($item) use ($tipoRegistro) {
            $estatusCampo = $tipoRegistro === 'solicitud' ? ($item->estado ?? '') : ($item->estatus ?? '');
            return in_array(strtolower($estatusCampo), ['cerrado', 'aprobado', 'resuelto', 'completado']);
        })->count();
        $sinResolver = $totalReg - $resueltos;

        $pctResueltos = $totalReg > 0 ? round(($resueltos / $totalReg) * 100) : 0;
        $pctSinResolver = 100 - $pctResueltos;

        if ($tipoRegistro === 'denuncia') {
            $totalGravedad = (($gravedadAlta ?? 0) + ($gravedadMedia ?? 0) + ($gravedadBaja ?? 0)) ?: 1;
            $pctAlta = round((($gravedadAlta ?? 0) / $totalGravedad) * 100);
            $pctMedia = round((($gravedadMedia ?? 0) / $totalGravedad) * 100);
            $pctBaja = round((($gravedadBaja ?? 0) / $totalGravedad) * 100);
        } else {
            $totalDictamen = (($val1 ?? 0) + ($val2 ?? 0) + ($val3 ?? 0) + ($val4 ?? 0)) ?: 1;
            $pctVal1 = round((($val1 ?? 0) / $totalDictamen) * 100);
            $pctVal2 = round((($val2 ?? 0) / $totalDictamen) * 100);
            $pctVal3 = round((($val3 ?? 0) / $totalDictamen) * 100);
            $pctVal4 = round((($val4 ?? 0) / $totalDictamen) * 100);
        }

        $maxMotivo = $motivosPrincipales->max('total') ?: 1;
    @endphp

    <!-- BLOQUE METRICAS PRINCIPALES: Dos Gráficas Circulares Idénticas y Simétricas -->
    <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom: 14px;">
        <tr>
            <!-- 1. TARJETA ESTATUS DE REGISTROS -->
            <td width="49%" valign="top" style="border: 1px solid #cbd5e1; border-radius: 8px; padding: 10px; background-color: #f8fafc;">
                <div style="font-size: 10px; font-weight: bold; color: #0f172a; text-transform: uppercase; margin-bottom: 8px;">
                    ESTATUS DE REGISTROS
                </div>
                
                <table width="100%" cellpadding="0" cellspacing="0">
                    <tr>
                        <td width="40%" align="center" valign="middle">
                            <img src="{{ $chartEstatusUrl }}" width="70" height="70" style="display: block; margin: 0 auto;" alt="Estatus" />
                            <div style="font-size: 11px; font-weight: 800; color: #0f172a; margin-top: 3px;">
                                TOTAL: {{ $totalReg }}
                            </div>
                        </td>
                        <td width="60%" valign="middle" style="padding-left: 8px; font-size: 9px; line-height: 1.6;">
                            <div style="margin-bottom: 6px;">
                                <span style="color: #059669; font-size: 12px;">■</span> 
                                <strong>{{ $tipoRegistro === 'solicitud' ? 'Aprobados:' : 'Resueltos:' }}</strong> {{ $resueltos }} <span style="color: #059669; font-weight: bold;">({{ $pctResueltos }}%)</span>
                            </div>
                            <div>
                                <span style="color: #d97706; font-size: 12px;">■</span> 
                                <strong>{{ $tipoRegistro === 'solicitud' ? 'Pendientes:' : 'Sin Resolver:' }}</strong> {{ $sinResolver }} <span style="color: #d97706; font-weight: bold;">({{ $pctSinResolver }}%)</span>
                            </div>
                        </td>
                    </tr>
                </table>
            </td>

            <td width="2%"></td> <!-- Espaciador -->

            <!-- 2. TARJETA GRAVEDAD O VEREDICTOS -->
            <td width="49%" valign="top" style="border: 1px solid #cbd5e1; border-radius: 8px; padding: 10px; background-color: #f8fafc;">
                <div style="font-size: 10px; font-weight: bold; color: #0f172a; text-transform: uppercase; margin-bottom: 8px;">
                    {{ $tipoRegistro === 'solicitud' ? 'Veredictos de Inspección' : 'Niveles de Gravedad' }}
                </div>

                <table width="100%" cellpadding="0" cellspacing="0">
                    <tr>
                        <td width="40%" align="center" valign="middle">
                            <img src="{{ $tipoRegistro === 'solicitud' ? $chartDictamenUrl : $chartGravedadUrl }}" width="70" height="70" style="display: block; margin: 0 auto;" alt="Métrica 2" />
                            <div style="font-size: 11px; font-weight: 800; color: #0f172a; margin-top: 3px;">
                                TOTAL: {{ $totalReg }}
                            </div>
                        </td>
                        
                        <td width="60%" valign="middle" style="padding-left: 8px; font-size: 8px; line-height: 1.4;">
                            @if($tipoRegistro === 'denuncia')
                                <div style="margin-bottom: 4px;"><span style="color: #dc2626; font-size: 11px;">■</span> <strong>Alta:</strong> {{ $gravedadAlta }} ({{ $pctAlta }}%)</div>
                                <div style="margin-bottom: 4px;"><span style="color: #d97706; font-size: 11px;">■</span> <strong>Media:</strong> {{ $gravedadMedia }} ({{ $pctMedia }}%)</div>
                                <div><span style="color: #2563eb; font-size: 11px;">■</span> <strong>Baja:</strong> {{ $gravedadBaja }} ({{ $pctBaja }}%)</div>
                            @else
                                <div style="margin-bottom: 3px;"><span style="color: #059669; font-size: 10px;">■</span> <strong>Aprobado:</strong> {{ $val1 }} ({{ $pctVal1 }}%)</div>
                                <div style="margin-bottom: 3px;"><span style="color: #d97706; font-size: 10px;">■</span> <strong>Observaciones:</strong> {{ $val2 }} ({{ $pctVal2 }}%)</div>
                                <div style="margin-bottom: 3px;"><span style="color: #dc2626; font-size: 10px;">■</span> <strong>Rechazado:</strong> {{ $val3 }} ({{ $pctVal3 }}%)</div>
                                <div><span style="color: #94a3b8; font-size: 10px;">■</span> <strong>Sin Insp.:</strong> {{ $val4 }} ({{ $pctVal4 }}%)</div>
                            @endif
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- Bloque 1: Priorización por Comunas y Sectores -->
    <div class="section-title">1. Priorización de Comunas y Sectores con Mayor Incidencia</div>
    <table style="width: 100%; border-collapse: collapse; margin-bottom: 12px;">
        <thead>
            <tr style="background-color: #f1f5f9; color: #475569; font-size: 9px; text-transform: uppercase; font-weight: 700;">
                <th style="padding: 6px 8px; text-align: left; border-bottom: 1px solid #e2e8f0;">Comuna</th>
                <th style="padding: 6px 8px; text-align: left; border-bottom: 1px solid #e2e8f0;">Sector</th>
                <th style="padding: 6px 8px; text-align: right; border-bottom: 1px solid #e2e8f0;">Total Registros</th>
            </tr>
        </thead>
        <tbody>
            @foreach($rankingComunas as $row)
            <tr>
                <td style="padding: 6px 8px; border-bottom: 1px solid #e2e8f0;"><strong>{{ $row->comuna ?? 'No especificada' }}</strong></td>
                <td style="padding: 6px 8px; border-bottom: 1px solid #e2e8f0;">{{ $row->sector ?? 'General' }}</td>
                <td style="padding: 6px 8px; text-align: right; border-bottom: 1px solid #e2e8f0;">
                    <span style="background-color: #ecfdf5; color: #047857; border: 1px solid #a7f3d0; padding: 2px 6px; border-radius: 4px; font-weight: bold; font-size: 9px;">{{ $row->total }}</span>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Bloque 2: Distribución Gráfica por Principales Motivos o Tipos de Trámite -->
    <div class="section-title">2. Distribución Gráfica por Principales {{ $tipoRegistro === 'solicitud' ? 'Tipos de Trámite' : 'Motivos' }}</div>
    <div style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 10px 12px; margin-bottom: 14px;">
        @foreach($motivosPrincipales as $motivo)
            @php
                $porcentajeMotivo = max(round(($motivo->total / $maxMotivo) * 100), 5);
                $nombreMotivo = $motivo->motivo;
                if($tipoRegistro === 'solicitud') {
                    $nombresTramites = [
                        'inspeccion' => 'Inspección Técnica',
                        'poda_deshierbe' => 'Poda o Deshierbe',
                        'tala' => 'Tala de Árboles',
                        'desmalezamiento_conformacion' => 'Desmalezamiento y Conformación'
                    ];
                    $nombreMotivo = $nombresTramites[$motivo->motivo] ?? ucfirst($motivo->motivo);
                } else {
                    $nombreMotivo = ucfirst($motivo->motivo);
                }
            @endphp
            <div style="margin-bottom: 8px;">
                <div style="display: flex; justify-content: space-between; font-size: 10px; font-weight: bold; color: #334155; margin-bottom: 3px;">
                    <span>{{ $nombreMotivo }}</span>
                    <span style="color: #059669;">{{ $motivo->total }} {{ $tipoRegistro === 'solicitud' ? 'trámites' : 'casos' }}</span>
                </div>
                <div style="width: 100%; background-color: #e2e8f0; height: 8px; border-radius: 4px; overflow: hidden;">
                    <div style="width: {{ $porcentajeMotivo }}%; background-color: #059669; height: 8px; border-radius: 4px;"></div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Bloque 3: Consolidado Territorial Agrupado -->
    <div class="section-title">3. Consolidado Territorial por Comunas y Sectores</div>
    
    @php
        $agrupadoPorComuna = $registrosGenerales->groupBy(function($item) {
            return $item->comuna ?? 'Sin Comuna Asignada';
        });
    @endphp

    @forelse($agrupadoPorComuna as $comunaNombre => $registrosComuna)
        <div class="comuna-group">
            <div class="comuna-header">
                Comuna: {{ $comunaNombre }} <span style="float: right; font-weight: normal;">(Total Comunal: {{ $registrosComuna->count() }})</span>
            </div>
            
            @php
                $agrupadoPorSector = $registrosComuna->groupBy(function($item) {
                    return $item->sector ?? 'General / Sin especificar';
                });
            @endphp

            @foreach($agrupadoPorSector as $sectorNombre => $registrosSector)
                @php
                    $motivosEnSector = $registrosSector->countBy(function($item) use ($tipoRegistro) {
                        $campoMotivo = $tipoRegistro === 'solicitud' ? $item->tipo_tramite : $item->tipo_contaminacion;
                        return ucfirst($campoMotivo);
                    });
                    
                    $resumenMotivosString = $motivosEnSector->map(function($count, $motivo) use ($tipoRegistro) {
                        if($tipoRegistro === 'solicitud') {
                            $mapa = ['Inspeccion' => 'Inspección', 'Poda_deshierbe' => 'Poda', 'Tala' => 'Tala', 'Desmalezamiento_conformacion' => 'Desmalezamiento'];
                            $motivo = $mapa[$motivo] ?? $motivo;
                        }
                        return "{$count} {$motivo}";
                    })->implode(' • ');

                    $gAlta = $registrosSector->filter(fn($i) => strtolower($i->nivel_gravedad ?? '') === 'alta')->count();
                    $gMedia = $registrosSector->filter(fn($i) => strtolower($i->nivel_gravedad ?? '') === 'media')->count();
                    $gBaja = $registrosSector->filter(fn($i) => strtolower($i->nivel_gravedad ?? '') === 'baja')->count();
                @endphp
                <div style="padding: 6px 10px; border-bottom: 1px solid #f1f5f9; font-size: 10px;">
                    <strong>Sector: {{ $sectorNombre }}</strong> 
                    <span style="color: #64748b; display: block; margin-top: 2px;">
                        {{ $tipoRegistro === 'solicitud' ? 'Trámites' : 'Incidencias' }}: [ {{ $resumenMotivosString }} ]
                    </span>
                    
                    @if($tipoRegistro === 'denuncia')
                    <span style="color: #475569; display: block; margin-top: 2px; font-size: 9px;">
                        Niveles de gravedad: 
                        @if($gAlta > 0) <span style="color: #dc2626; font-weight: bold;">Alta: {{ $gAlta }}</span> @endif
                        @if($gMedia > 0) <span style="color: #d97706; font-weight: bold; margin-left: 6px;">Media: {{ $gMedia }}</span> @endif
                        @if($gBaja > 0) <span style="color: #2563eb; font-weight: bold; margin-left: 6px;">Baja: {{ $gBaja }}</span> @endif
                    </span>
                    @else
                    @php
                        $inspeccionSector = $registrosSector->map(fn($s) => $s->inspecciones()->latest()->first()?->dictamen)->filter();
                        $aprobadosSec = $inspeccionSector->filter(fn($d) => strtoupper($d) === 'APROBADO')->count();
                        $observadosSec = $inspeccionSector->filter(fn($d) => strtoupper($d) === 'CON_OBSERVACIONES')->count();
                        $rechazadosSec = $inspeccionSector->filter(fn($d) => in_array(strtoupper($d), ['RECHAZADO', 'REINSPECCION_REQUERIDA']))->count();
                        $sinInspeccionSec = $registrosSector->count() - $inspeccionSector->count();
                    @endphp
                    <span style="color: #475569; display: block; margin-top: 2px; font-size: 9px;">
                        Veredictos: 
                        @if($aprobadosSec > 0) <span style="color: #059669; font-weight: bold;">Aprobados: {{ $aprobadosSec }}</span> @endif
                        @if($observadosSec > 0) <span style="color: #d97706; font-weight: bold; margin-left: 4px;">Obs: {{ $observadosSec }}</span> @endif
                        @if($rechazadosSec > 0) <span style="color: #dc2626; font-weight: bold; margin-left: 4px;">Rechazados: {{ $rechazadosSec }}</span> @endif
                        @if($sinInspeccionSec > 0) <span style="color: #94a3b8; font-weight: bold; margin-left: 4px;">Sin Insp.: {{ $sinInspeccionSec }}</span> @endif
                    </span>
                    @endif

                    <div style="text-align: right; font-weight: bold; color: #0f172a; margin-top: -14px;">
                        TOTAL: {{ $registrosSector->count() }}
                    </div>
                </div>
            @endforeach
        </div>
    @empty
        <p style="font-size: 10px; color: #94a3b8; text-align: center; padding: 10px;">No hay registros territoriales disponibles para consolidar.</p>
    @endforelse

    <div class="footer">
        Dirección de Digitalización y Despacho Municipal — Sistema de Gestión Ecosocialista de la Alcaldía de Carrizal
    </div>

</body>
</html>