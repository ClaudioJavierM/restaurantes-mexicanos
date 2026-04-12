<!DOCTYPE html>
<html lang="es" xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="format-detection" content="telephone=no">
    <title>Tu reporte semanal — FAMER</title>
</head>
<body style="margin:0; padding:0; background-color:#0B0B0B; font-family:Arial,Helvetica,sans-serif; -webkit-text-size-adjust:100%; -ms-text-size-adjust:100%;">

<table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color:#0B0B0B;">
    <tr>
        <td align="center" style="padding:32px 16px;">

            <table role="presentation" width="100%" style="max-width:600px;" cellspacing="0" cellpadding="0" border="0">

                <!-- HEADER -->
                <tr>
                    <td style="background-color:#1A1A1A; border-radius:16px 16px 0 0; padding:28px 40px; text-align:center;">
                        <img src="https://restaurantesmexicanosfamosos.com.mx/images/branding/logo-horizontal.png"
                             alt="FAMER" width="160"
                             style="max-width:160px; height:auto; display:block; margin:0 auto 12px;">
                        <!-- Subtitle + Tier Badge row -->
                        <table role="presentation" align="center" cellspacing="0" cellpadding="0" border="0">
                            <tr>
                                <td style="vertical-align:middle; padding-right:10px;">
                                    <p style="margin:0; color:#D4AF37; font-size:11px; font-weight:700; letter-spacing:3px; text-transform:uppercase; font-family:Arial,Helvetica,sans-serif;">REPORTE SEMANAL</p>
                                </td>
                                <td style="vertical-align:middle;">
                                    @if($tier === 'elite')
                                        <span style="display:inline-block; background-color:#D4AF37; color:#0B0B0B; font-size:10px; font-weight:700; letter-spacing:2px; text-transform:uppercase; padding:3px 10px; border-radius:20px; font-family:Arial,Helvetica,sans-serif;">ELITE</span>
                                    @else
                                        <span style="display:inline-block; background-color:#D4AF37; color:#0B0B0B; font-size:10px; font-weight:700; letter-spacing:2px; text-transform:uppercase; padding:3px 10px; border-radius:20px; font-family:Arial,Helvetica,sans-serif;">PREMIUM</span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <!-- GOLD BAR -->
                <tr>
                    <td style="background:linear-gradient(90deg,#D4AF37,#F0D060,#D4AF37); height:3px; font-size:0; line-height:0;">&nbsp;</td>
                </tr>

                <!-- BODY -->
                <tr>
                    <td style="background-color:#1A1A1A; padding:36px 40px;">

                        <!-- GREETING -->
                        <p style="margin:0 0 28px 0; color:#F5F5F5; font-size:16px; line-height:1.5; font-family:Arial,Helvetica,sans-serif;">
                            Hola {{ $ownerName }}, aquí está tu reporte de esta semana:
                        </p>

                        <!-- HERO STAT CARD -->
                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="margin-bottom:24px;">
                            <tr>
                                <td style="background-color:#0B0B0B; border:2px solid #D4AF37; border-radius:12px; padding:28px 24px; text-align:center;">
                                    <p style="margin:0 0 8px 0; color:#9CA3AF; font-size:13px; font-family:Arial,Helvetica,sans-serif; text-transform:uppercase; letter-spacing:1px;">Esta semana</p>
                                    <p style="margin:0 0 6px 0; color:#D4AF37; font-size:48px; font-weight:700; line-height:1; font-family:Arial,Helvetica,sans-serif;">{{ number_format($thisWeekViews) }}</p>
                                    <p style="margin:0 0 12px 0; color:#F5F5F5; font-size:16px; font-family:Arial,Helvetica,sans-serif;">visitas esta semana</p>
                                    @if($viewsChange > 0)
                                        <p style="margin:0; color:#4ADE80; font-size:14px; font-weight:700; font-family:Arial,Helvetica,sans-serif;">&#8593;{{ $viewsChange }}% vs semana anterior</p>
                                    @elseif($viewsChange < 0)
                                        <p style="margin:0; color:#EF4444; font-size:14px; font-weight:700; font-family:Arial,Helvetica,sans-serif;">&#8595;{{ abs($viewsChange) }}% vs semana anterior</p>
                                    @else
                                        <p style="margin:0; color:#9CA3AF; font-size:14px; font-family:Arial,Helvetica,sans-serif;">Sin cambio vs semana anterior</p>
                                    @endif
                                </td>
                            </tr>
                        </table>

                        <!-- STATS ROW (3 cards) -->
                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="margin-bottom:32px;">
                            <tr>
                                <td width="32%" style="background-color:#0B0B0B; border:1px solid #2A2A2A; border-radius:10px; padding:16px 12px; text-align:center;">
                                    <p style="margin:0 0 4px 0; color:#9CA3AF; font-size:11px; text-transform:uppercase; letter-spacing:1px; font-family:Arial,Helvetica,sans-serif;">Visitas este mes</p>
                                    <p style="margin:0; color:#F5F5F5; font-size:22px; font-weight:700; font-family:Arial,Helvetica,sans-serif;">{{ number_format($monthlyViews) }}</p>
                                </td>
                                <td width="4%" style="font-size:0;">&nbsp;</td>
                                <td width="32%" style="background-color:#0B0B0B; border:1px solid #2A2A2A; border-radius:10px; padding:16px 12px; text-align:center;">
                                    <p style="margin:0 0 4px 0; color:#9CA3AF; font-size:11px; text-transform:uppercase; letter-spacing:1px; font-family:Arial,Helvetica,sans-serif;">Votos este mes</p>
                                    <p style="margin:0; color:#F5F5F5; font-size:22px; font-weight:700; font-family:Arial,Helvetica,sans-serif;">{{ number_format($monthlyVotes) }}</p>
                                </td>
                                <td width="4%" style="font-size:0;">&nbsp;</td>
                                <td width="32%" style="background-color:#0B0B0B; border:1px solid #2A2A2A; border-radius:10px; padding:16px 12px; text-align:center;">
                                    <p style="margin:0 0 4px 0; color:#9CA3AF; font-size:11px; text-transform:uppercase; letter-spacing:1px; font-family:Arial,Helvetica,sans-serif;">Competidores en área</p>
                                    <p style="margin:0; color:#F5F5F5; font-size:22px; font-weight:700; font-family:Arial,Helvetica,sans-serif;">{{ number_format($competitorCount) }}</p>
                                </td>
                            </tr>
                        </table>

                        <!-- SMART TIP BLOCK (only for premium/elite) -->
                        @if($tip !== null)
                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="margin-bottom:32px;">
                            <tr>
                                <td style="background-color:#0B0B0B; border:1px solid #2A2A2A; border-radius:12px; padding:24px;">
                                    <p style="margin:0 0 14px 0; color:#D4AF37; font-size:13px; font-weight:700; text-transform:uppercase; letter-spacing:1px; font-family:Arial,Helvetica,sans-serif;">&#128161; Tip de la semana</p>
                                    <p style="margin:0 0 8px 0; color:#F5F5F5; font-size:16px; font-weight:700; font-family:Arial,Helvetica,sans-serif;">
                                        {{ $tip['icon'] }} {{ $tip['title'] }}
                                    </p>
                                    <p style="margin:0 0 20px 0; color:#9CA3AF; font-size:14px; line-height:1.6; font-family:Arial,Helvetica,sans-serif;">
                                        {{ $tip['body'] }}
                                    </p>
                                    <!-- Outlined CTA Button -->
                                    <table role="presentation" cellspacing="0" cellpadding="0" border="0">
                                        <tr>
                                            <td style="border:2px solid #D4AF37; border-radius:8px;">
                                                <a href="{{ $tip['url'] }}"
                                                   style="display:inline-block; color:#D4AF37; text-decoration:none; font-size:14px; font-weight:700; padding:10px 24px; font-family:Arial,Helvetica,sans-serif;">
                                                    {{ $tip['cta'] }} &rarr;
                                                </a>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                        @endif

                        <!-- DASHBOARD CTA -->
                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
                            <tr>
                                <td align="center">
                                    <a href="{{ url('/owner/dashboard') }}"
                                       style="display:inline-block; background-color:#D4AF37; color:#0B0B0B; text-decoration:none; font-size:15px; font-weight:700; padding:14px 32px; border-radius:8px; font-family:Arial,Helvetica,sans-serif;">
                                        Ver mi dashboard completo &rarr;
                                    </a>
                                </td>
                            </tr>
                        </table>

                    </td>
                </tr>

                <!-- GOLD BAR BOTTOM -->
                <tr>
                    <td style="background:linear-gradient(90deg,#D4AF37,#F0D060,#D4AF37); height:3px; font-size:0; line-height:0;">&nbsp;</td>
                </tr>

                <!-- FOOTER -->
                <tr>
                    <td style="background-color:#1A1A1A; border-radius:0 0 16px 16px; padding:24px 40px; text-align:center;">
                        <p style="margin:0 0 6px 0; color:#D4AF37; font-size:13px; font-weight:700; letter-spacing:2px; text-transform:uppercase; font-family:Arial,Helvetica,sans-serif;">FAMER — Famous Mexican Restaurants</p>
                        <p style="margin:0 0 12px 0; color:#9CA3AF; font-size:12px; font-family:Arial,Helvetica,sans-serif;">&copy; {{ date('Y') }} FAMER. Todos los derechos reservados.</p>
                        <p style="margin:0; font-family:Arial,Helvetica,sans-serif;">
                            <a href="{{ url('/email/unsubscribe?email=' . urlencode($restaurant->email ?? '')) }}"
                               style="color:#9CA3AF; font-size:11px; text-decoration:underline;">Cancelar suscripción a reportes</a>
                        </p>
                    </td>
                </tr>

            </table>
        </td>
    </tr>
</table>

</body>
</html>
