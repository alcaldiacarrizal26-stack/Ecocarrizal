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

        /* Estilo para el consolidado territorial */
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

    <!-- Encabezado Institucional -->
    <div class="header">
        <span class="sub-badge">Alcaldía del Municipio Carrizal • Despacho de la Alcaldesa</span>
        <h1>Informe Ejecutivo: Priorización del Poder Popular</h1>
        <p>Análisis territorial de incidencias y trámites comunitarios — Registro de Denuncias</p>
    </div>

    @php
        $totalReg = $registrosGenerales->count();
        $resueltos = $registrosGenerales->filter(function($item) {
            return in_array(strtolower($item->estatus), ['cerrado', 'aprobado', 'resuelto', 'completado']);
        })->count();
        $sinResolver = $totalReg - $resueltos;

        $pctResueltos = $totalReg > 0 ? round(($resueltos / $totalReg) * 100) : 0;
        $pctSinResolver = 100 - $pctResueltos;

        // Conteo por niveles de gravedad
        $gravedadAlta = $registrosGenerales->filter(fn($i) => strtolower($i->nivel_gravedad ?? '') === 'alta')->count();
        $gravedadMedia = $registrosGenerales->filter(fn($i) => strtolower($i->nivel_gravedad ?? '') === 'media')->count();
        $gravedadBaja = $registrosGenerales->filter(fn($i) => strtolower($i->nivel_gravedad ?? '') === 'baja')->count();
        
        $totalGravedad = ($gravedadAlta + $gravedadMedia + $gravedadBaja) ?: 1;
        $pctAlta = round(($gravedadAlta / $totalGravedad) * 100);
        $pctMedia = round(($gravedadMedia / $totalGravedad) * 100);
        $pctBaja = round(($gravedadBaja / $totalGravedad) * 100);

        $maxMotivo = $motivosPrincipales->max('total') ?: 1;
    @endphp

    <!-- BLOQUE METRICAS PRINCIPALES: Gráfico Circular + Gravedad en la misma fila -->
    <table style="width: 100%; border-collapse: collapse; margin-bottom: 14px;">
        <tr>
            <!-- 1. Gráfico Circular (Donut Chart SVG): Resueltos vs Sin Resolver -->
            <td style="width: 50%; vertical-align: top; padding-right: 8px;">
                <div style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px;">
                    <div style="font-size: 10px; font-weight: bold; color: #0f172a; text-transform: uppercase; margin-bottom: 8px;">
                        Estatus de Registros
                    </div>
                    
                    <div style="display: flex; align-items: center; justify-content: space-around;">
                        <!-- SVG Donut Chart Vectorial -->
                        <div style="position: relative; width: 85px; height: 85px;">
                            <svg viewBox="0 0 36 36" style="width: 85px; height: 85px; transform: rotate(-90deg); transform-origin: center;">
                                <!-- Círculo Fondo (Sin Resolver / Ámbar) -->
                                <path stroke="#d97706" stroke-width="4.5" fill="none"
                                      d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                                <!-- Círculo Progreso (Resueltos / Verde Ecosocialista) -->
                                <path stroke="#059669" stroke-width="4.5" stroke-dasharray="{{ $pctResueltos }}, 100" fill="none"
                                      d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                            </svg>
                            <!-- Texto en el centro del Donut (Total) -->
                            <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center;">
                                <span style="font-size: 16px; font-weight: 900; color: #0f172a; line-height: 1;">{{ $totalReg }}</span>
                                <span style="font-size: 7px; color: #64748b; font-weight: bold; text-transform: uppercase;">TOTAL</span>
                            </div>
                        </div>

                        <!-- Leyenda a la derecha del gráfico circular -->
                        <div style="font-size: 9px; space-y-2;">
                            <div style="margin-bottom: 6px;">
                                <span style="display: inline-block; width: 8px; height: 8px; background-color: #059669; border-radius: 50%; margin-right: 4px;"></span>
                                <strong>Resueltos:</strong> {{ $resueltos }} <span style="color: #059669; font-weight: bold;">({{ $pctResueltos }}%)</span>
                            </div>
                            <div>
                                <span style="display: inline-block; width: 8px; height: 8px; background-color: #d97706; border-radius: 50%; margin-right: 4px;"></span>
                                <strong>Sin Resolver:</strong> {{ $sinResolver }} <span style="color: #d97706; font-weight: bold;">({{ $pctSinResolver }}%)</span>
                            </div>
                        </div>
                    </div>
                </div>
            </td>

            <!-- 2. Gráfico de Gravedad en Barras Verticales -->
            <td style="width: 50%; vertical-align: top; padding-left: 8px;">
                <div style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px;">
                    <div style="font-size: 10px; font-weight: bold; color: #0f172a; text-transform: uppercase; margin-bottom: 8px;">
                        Niveles de Gravedad
                    </div>

                    <div style="display: flex; align-items: flex-end; justify-content: space-around; height: 60px; border-bottom: 1px solid #cbd5e1; padding-bottom: 4px; margin-bottom: 6px;">
                        <!-- Barra Alta -->
                        <div style="text-align: center; width: 25%;">
                            <span style="font-size: 8px; font-weight: bold; color: #dc2626;">{{ $gravedadAlta }}</span>
                            <div style="height: {{ max($pctAlta, 8) }}px; background-color: #dc2626; border-radius: 3px 3px 0 0; width: 100%; margin: 2px auto 0;"></div>
                        </div>
                        <!-- Barra Media -->
                        <div style="text-align: center; width: 25%;">
                            <span style="font-size: 8px; font-weight: bold; color: #d97706;">{{ $gravedadMedia }}</span>
                            <div style="height: {{ max($pctMedia, 8) }}px; background-color: #d97706; border-radius: 3px 3px 0 0; width: 100%; margin: 2px auto 0;"></div>
                        </div>
                        <!-- Barra Baja -->
                        <div style="text-align: center; width: 25%;">
                            <span style="font-size: 8px; font-weight: bold; color: #2563eb;">{{ $gravedadBaja }}</span>
                            <div style="height: {{ max($pctBaja, 8) }}px; background-color: #2563eb; border-radius: 3px 3px 0 0; width: 100%; margin: 2px auto 0;"></div>
                        </div>
                    </div>

                    <div style="display: flex; justify-content: space-around; font-size: 8px; font-weight: bold; text-transform: uppercase;">
                        <span style="color: #dc2626;">Alta ({{ $pctAlta }}%)</span>
                        <span style="color: #d97706;">Media ({{ $pctMedia }}%)</span>
                        <span style="color: #2563eb;">Baja ({{ $pctBaja }}%)</span>
                    </div>
                </div>
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

    <!-- Bloque 2: Distribución Gráfica por Principales Motivos (Barras Horizontales) -->
    <div class="section-title">2. Distribución Gráfica por Principales Motivos</div>
    <div style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 10px 12px; margin-bottom: 14px;">
        @foreach($motivosPrincipales as $motivo)
            @php
                $porcentajeMotivo = max(round(($motivo->total / $maxMotivo) * 100), 5);
            @endphp
            <div style="margin-bottom: 8px;">
                <div style="display: flex; justify-content: space-between; font-size: 10px; font-weight: bold; color: #334155; margin-bottom: 3px;">
                    <span>{{ ucfirst($motivo->motivo) }}</span>
                    <span style="color: #059669;">{{ $motivo->total }} casos</span>
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
                    $motivosEnSector = $registrosSector->countBy(function($item) {
                        return ucfirst($item->tipo_contaminacion);
                    });
                    
                    $resumenMotivosString = $motivosEnSector->map(function($count, $motivo) {
                        return "{$count} {$motivo}";
                    })->implode(' • ');

                    $gAlta = $registrosSector->filter(fn($i) => strtolower($i->nivel_gravedad ?? '') === 'alta')->count();
                    $gMedia = $registrosSector->filter(fn($i) => strtolower($i->nivel_gravedad ?? '') === 'media')->count();
                    $gBaja = $registrosSector->filter(fn($i) => strtolower($i->nivel_gravedad ?? '') === 'baja')->count();
                @endphp
                <div style="padding: 6px 10px; border-bottom: 1px solid #f1f5f9; font-size: 10px;">
                    <strong>Sector: {{ $sectorNombre }}</strong> 
                    <span style="color: #64748b; display: block; margin-top: 2px;">
                        Incidencias: [ {{ $resumenMotivosString }} ]
                    </span>
                    <span style="color: #475569; display: block; margin-top: 2px; font-size: 9px;">
                        Niveles de gravedad: 
                        @if($gAlta > 0) <span style="color: #dc2626; font-weight: bold;">Alta: {{ $gAlta }}</span> @endif
                        @if($gMedia > 0) <span style="color: #d97706; font-weight: bold; margin-left: 6px;">Media: {{ $gMedia }}</span> @endif
                        @if($gBaja > 0) <span style="color: #2563eb; font-weight: bold; margin-left: 6px;">Baja: {{ $gBaja }}</span> @endif
                    </span>
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