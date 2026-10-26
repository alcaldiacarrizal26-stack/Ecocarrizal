<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $titulo ?? 'Alcaldía de Carrizal' }}</title>
</head>
<body style="font-family: 'Segoe UI', Helvetica, Arial, sans-serif; background-color: #f1f5f9; margin: 0; padding: 0; -webkit-text-size-adjust: none; text-size-adjust: none;">
    <table width="100%" border="0" cellspacing="0" cellpadding="0" style="background-color: #f1f5f9; padding: 40px 10px;">
        <tr>
            <td align="center">
                <table width="100%" max-width="600" border="0" cellspacing="0" cellpadding="0" style="max-width: 600px; background-color: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08); border: 1px solid #e2e8f0;">
                    
                    <!-- 1. HEADER INSTITUCIONAL NORMALIZADO -->
                    <tr>
                        <td style="background-color: #0f172a; padding: 28px 32px; text-align: center; border-bottom: 4px solid #10b981;">
                            <h1 style="color: #ffffff; font-size: 18px; margin: 0; font-weight: 800; letter-spacing: 1px; text-transform: uppercase;">
                                ALCALDÍA DEL MUNICIPIO CARRIZAL
                            </h1>
                            <p style="color: #34d399; font-size: 11px; margin: 5px 0 0 0; font-weight: 700; letter-spacing: 1.5px; text-transform: uppercase;">
                                Dirección de Ecosocialismo — Carrizal Digital
                            </p>
                        </td>
                    </tr>

                    <!-- 2. CUERPO CONTENIDO DINÁMICO -->
                    <tr>
                        <td style="padding: 36px 32px; color: #334155;">
                            @if(isset($subtitulo))
                                <h2 style="font-size: 18px; font-weight: 700; color: #1e293b; margin-top: 0; margin-bottom: 16px;">
                                    {{ $subtitulo }}
                                </h2>
                            @endif

                            <div style="font-size: 14px; line-height: 1.6; color: #475569;">
                                {!! $contenido !!}
                            </div>

                            @if(isset($urlAccion) && isset($textoAccion))
                                <table border="0" cellspacing="0" cellpadding="0" style="margin: 28px 0;">
                                    <tr>
                                        <td align="center" style="border-radius: 10px;" bgcolor="#10b981">
                                            <a href="{{ $urlAccion }}" target="_blank" style="font-size: 14px; font-weight: bold; color: #ffffff; text-decoration: none; padding: 12px 28px; border-radius: 10px; border: 1px solid #10b981; display: inline-block; background-color: #10b981;">
                                                {{ $textoAccion }}
                                            </a>
                                        </td>
                                    </tr>
                                </table>
                            @endif

                            <p style="font-size: 13px; color: #64748b; margin-top: 28px; margin-bottom: 0;">
                                Atentamente,<br>
                                <strong style="color: #334155;">Soporte Tecnológico — Carrizal Digital</strong>
                            </p>
                        </td>
                    </tr>

                    <!-- 3. FOOTER INSTITUCIONAL NORMALIZADO -->
                    <tr>
                        <td style="background-color: #f8fafc; padding: 20px 32px; text-align: center; border-top: 1px solid #e2e8f0;">
                            <p style="font-size: 11px; color: #94a3b8; margin: 0 0 6px 0; line-height: 1.4;">
                                Este es un mensaje automático generado por el Sistema de Gestión Ambiental Ecosocialismo.
                            </p>
                            <p style="font-size: 11px; color: #94a3b8; margin: 0; line-height: 1.4;">
                                &copy; {{ date('Y') }} Alcaldía del Municipio Carrizal, Estado Miranda. Todos los derechos reservados.
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>