<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Tu prueba Elite termina pronto</title>
</head>
<body style="margin: 0; padding: 0; background-color: #0B0B0B; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; -webkit-font-smoothing: antialiased;">

    <!-- Wrapper -->
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background-color: #0B0B0B;">
        <tr>
            <td style="padding: 32px 16px;">

                <!-- Container -->
                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="600" align="center" style="max-width: 600px; width: 100%; margin: 0 auto;">

                    <!-- Header -->
                    <tr>
                        <td style="padding-bottom: 32px; text-align: center;">
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                <tr>
                                    <td style="border-bottom: 2px solid #D4AF37; padding-bottom: 24px; text-align: center;">
                                        <span style="font-size: 28px; font-weight: 800; letter-spacing: 3px; color: #D4AF37; text-transform: uppercase;">FAMER</span>
                                        <br>
                                        <span style="font-size: 11px; color: #9CA3AF; letter-spacing: 2px; text-transform: uppercase; font-weight: 400;">Famous Mexican Restaurants</span>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Main Card -->
                    <tr>
                        <td>
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background-color: #1A1A1A; border-radius: 12px; border: 1px solid #2A2A2A;">
                                <tr>
                                    <td style="padding: 40px 36px;">

                                        <!-- Gold accent bar -->
                                        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                            <tr>
                                                <td style="padding-bottom: 28px;">
                                                    <div style="width: 48px; height: 3px; background-color: #D4AF37; border-radius: 2px;"></div>
                                                </td>
                                            </tr>
                                        </table>

                                        <!-- Greeting -->
                                        <p style="margin: 0 0 8px 0; font-size: 15px; color: #9CA3AF; font-weight: 400;">
                                            Hola {{ $ownerName }},
                                        </p>

                                        <!-- Main headline -->
                                        <h1 style="margin: 0 0 24px 0; font-size: 26px; font-weight: 700; color: #F5F5F5; line-height: 1.3;">
                                            Tu prueba Elite de
                                            <span style="color: #D4AF37;">{{ $restaurant->name }}</span>
                                            termina en
                                            <span style="color: #D4AF37;">{{ $daysLeft }} {{ $daysLeft === 1 ? 'día' : 'días' }}</span>
                                        </h1>

                                        @if ($trialViews > 0)
                                        <!-- Stats Card -->
                                        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background-color: #0B0B0B; border-radius: 8px; border: 1px solid #D4AF37; margin-bottom: 28px;">
                                            <tr>
                                                <td style="padding: 24px;">
                                                    <p style="margin: 0 0 6px 0; font-size: 11px; font-weight: 600; letter-spacing: 2px; color: #D4AF37; text-transform: uppercase;">Tu impacto durante la prueba</p>

                                                    <!-- Stat row 1 -->
                                                    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin-top: 16px;">
                                                        <tr>
                                                            <td style="width: 28px; vertical-align: top; padding-top: 2px;">
                                                                <span style="font-size: 18px;">👁</span>
                                                            </td>
                                                            <td style="padding-left: 10px; vertical-align: top;">
                                                                <span style="font-size: 22px; font-weight: 800; color: #4ADE80; display: block; line-height: 1.1;">{{ number_format($trialViews) }}</span>
                                                                <span style="font-size: 13px; color: #9CA3AF;">personas visitaron tu perfil durante tu prueba</span>
                                                            </td>
                                                        </tr>
                                                    </table>

                                                    <!-- Stat row 2 -->
                                                    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin-top: 16px;">
                                                        <tr>
                                                            <td style="width: 28px; vertical-align: top; padding-top: 2px;">
                                                                <span style="font-size: 18px;">🏆</span>
                                                            </td>
                                                            <td style="padding-left: 10px; vertical-align: top;">
                                                                <span style="font-size: 22px; font-weight: 800; color: #D4AF37; display: block; line-height: 1.1;">{{ number_format($competitorCount) }}</span>
                                                                <span style="font-size: 13px; color: #9CA3AF;">restaurantes compiten por las mismas búsquedas</span>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                        </table>
                                        @endif

                                        <!-- Key message -->
                                        <p style="margin: 0 0 28px 0; font-size: 17px; color: #F5F5F5; line-height: 1.6;">
                                            No pierdas tu posición — continúa con Elite y mantén todo lo que construiste durante tu prueba.
                                            <strong style="color: #D4AF37;">Solo $79/mes.</strong>
                                        </p>

                                        <!-- Features list -->
                                        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background-color: #111111; border-radius: 8px; margin-bottom: 32px;">
                                            <tr>
                                                <td style="padding: 20px 24px;">
                                                    <p style="margin: 0 0 14px 0; font-size: 12px; font-weight: 600; letter-spacing: 1.5px; color: #9CA3AF; text-transform: uppercase;">Incluido en Elite</p>

                                                    <!-- Feature 1 -->
                                                    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin-bottom: 10px;">
                                                        <tr>
                                                            <td style="width: 20px; vertical-align: top; padding-top: 1px;">
                                                                <span style="color: #D4AF37; font-size: 14px; font-weight: 700;">★</span>
                                                            </td>
                                                            <td style="padding-left: 10px;">
                                                                <span style="font-size: 14px; color: #F5F5F5;">Posición <strong style="color: #D4AF37;">#1 Garantizada</strong> en búsquedas de tu ciudad</span>
                                                            </td>
                                                        </tr>
                                                    </table>

                                                    <!-- Feature 2 -->
                                                    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin-bottom: 10px;">
                                                        <tr>
                                                            <td style="width: 20px; vertical-align: top; padding-top: 1px;">
                                                                <span style="color: #D4AF37; font-size: 14px; font-weight: 700;">★</span>
                                                            </td>
                                                            <td style="padding-left: 10px;">
                                                                <span style="font-size: 14px; color: #F5F5F5;"><strong style="color: #D4AF37;">App Móvil White Label</strong> con tu marca</span>
                                                            </td>
                                                        </tr>
                                                    </table>

                                                    <!-- Feature 3 -->
                                                    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin-bottom: 10px;">
                                                        <tr>
                                                            <td style="width: 20px; vertical-align: top; padding-top: 1px;">
                                                                <span style="color: #D4AF37; font-size: 14px; font-weight: 700;">★</span>
                                                            </td>
                                                            <td style="padding-left: 10px;">
                                                                <span style="font-size: 14px; color: #F5F5F5;"><strong style="color: #D4AF37;">Account Manager Dedicado</strong> para tu negocio</span>
                                                            </td>
                                                        </tr>
                                                    </table>

                                                    <!-- Feature 4 -->
                                                    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin-bottom: 0;">
                                                        <tr>
                                                            <td style="width: 20px; vertical-align: top; padding-top: 1px;">
                                                                <span style="color: #D4AF37; font-size: 14px; font-weight: 700;">★</span>
                                                            </td>
                                                            <td style="padding-left: 10px;">
                                                                <span style="font-size: 14px; color: #F5F5F5;"><strong style="color: #D4AF37;">Fotografía Profesional</strong> trimestral incluida</span>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                        </table>

                                        <!-- Primary CTA -->
                                        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin-bottom: 16px;">
                                            <tr>
                                                <td align="center">
                                                    <a href="{{ url('/owner/upgrade-subscription') }}"
                                                       style="display: inline-block; background-color: #D4AF37; color: #0B0B0B; font-size: 16px; font-weight: 800; text-decoration: none; padding: 16px 40px; border-radius: 8px; letter-spacing: 0.5px;">
                                                        Continuar con Elite &rarr;
                                                    </a>
                                                </td>
                                            </tr>
                                        </table>

                                        <!-- Secondary CTA -->
                                        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin-bottom: 28px;">
                                            <tr>
                                                <td align="center">
                                                    <a href="{{ url('/owner/upgrade-subscription?plan=premium') }}"
                                                       style="font-size: 13px; color: #9CA3AF; text-decoration: underline;">
                                                        ¿Prefieres Premium? $39/mes
                                                    </a>
                                                </td>
                                            </tr>
                                        </table>

                                        <!-- Urgency note -->
                                        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background-color: #1F1500; border-left: 3px solid #D4AF37; border-radius: 0 6px 6px 0;">
                                            <tr>
                                                <td style="padding: 14px 18px;">
                                                    <p style="margin: 0; font-size: 13px; color: #9CA3AF; line-height: 1.5;">
                                                        ⏰ Si no confirmas tu plan, tu perfil regresará al listado básico el
                                                        <strong style="color: #F5F5F5;">{{ \Carbon\Carbon::parse($restaurant->trial_ends_at)->locale('es')->isoFormat('D [de] MMMM') }}</strong>.
                                                    </p>
                                                </td>
                                            </tr>
                                        </table>

                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="padding-top: 32px; text-align: center;">
                            <p style="margin: 0 0 8px 0; font-size: 12px; color: #9CA3AF;">
                                FAMER — Famous Mexican Restaurants
                            </p>
                            <p style="margin: 0; font-size: 12px; color: #9CA3AF;">
                                ¿No quieres recibir estos correos?
                                <a href="mailto:unsubscribe@restaurantesmexicanosfamosos.com?subject=unsubscribe"
                                   style="color: #9CA3AF; text-decoration: underline;">
                                    Cancelar suscripción
                                </a>
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>

</body>
</html>
