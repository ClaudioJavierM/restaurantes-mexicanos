<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Acción requerida — pago rechazado</title>
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

                                        <!-- Red badge — ACCIÓN REQUERIDA -->
                                        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin-bottom: 28px;">
                                            <tr>
                                                <td>
                                                    <span style="display: inline-block; background-color: #8B1E1E; color: #F5F5F5; font-size: 11px; font-weight: 700; letter-spacing: 2px; text-transform: uppercase; padding: 6px 14px; border-radius: 4px;">
                                                        &#9888; ACCIÓN REQUERIDA
                                                    </span>
                                                </td>
                                            </tr>
                                        </table>

                                        <!-- Greeting -->
                                        <p style="margin: 0 0 8px 0; font-size: 15px; color: #9CA3AF; font-weight: 400;">
                                            Hola {{ $user->name }},
                                        </p>

                                        <!-- Main headline -->
                                        <h1 style="margin: 0 0 24px 0; font-size: 26px; font-weight: 700; color: #F5F5F5; line-height: 1.3;">
                                            Tu suscripción <span style="color: #D4AF37;">{{ ucfirst($restaurant->subscription_tier) }}</span> está en pausa
                                        </h1>

                                        <!-- Main message -->
                                        <p style="margin: 0 0 28px 0; font-size: 16px; color: #D1D5DB; line-height: 1.7;">
                                            No pudimos procesar tu pago para <strong style="color: #F5F5F5;">{{ $restaurant->name }}</strong>. Tu perfil premium seguirá activo por 3 días mientras actualizas tu método de pago.
                                        </p>

                                        <!-- Info list -->
                                        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background-color: #111111; border-radius: 8px; margin-bottom: 32px;">
                                            <tr>
                                                <td style="padding: 20px 24px;">
                                                    <p style="margin: 0 0 14px 0; font-size: 12px; font-weight: 600; letter-spacing: 1.5px; color: #9CA3AF; text-transform: uppercase;">Lo que debes saber</p>

                                                    <!-- Item 1 -->
                                                    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin-bottom: 12px;">
                                                        <tr>
                                                            <td style="width: 20px; vertical-align: top; padding-top: 2px;">
                                                                <span style="color: #D4AF37; font-size: 14px; font-weight: 700;">&#10003;</span>
                                                            </td>
                                                            <td style="padding-left: 10px;">
                                                                <span style="font-size: 14px; color: #F5F5F5;">Tu posición en búsquedas <strong style="color: #D4AF37;">se mantiene por ahora</strong></span>
                                                            </td>
                                                        </tr>
                                                    </table>

                                                    <!-- Item 2 -->
                                                    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin-bottom: 12px;">
                                                        <tr>
                                                            <td style="width: 20px; vertical-align: top; padding-top: 2px;">
                                                                <span style="color: #8B1E1E; font-size: 14px; font-weight: 700;">&#9888;</span>
                                                            </td>
                                                            <td style="padding-left: 10px;">
                                                                <span style="font-size: 14px; color: #F5F5F5;">Debes actualizar tu tarjeta antes del <strong style="color: #F5F5F5;">{{ \Carbon\Carbon::now()->addDays(3)->locale('es')->isoFormat('D [de] MMMM [de] YYYY') }}</strong></span>
                                                            </td>
                                                        </tr>
                                                    </table>

                                                    <!-- Item 3 -->
                                                    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin-bottom: 0;">
                                                        <tr>
                                                            <td style="width: 20px; vertical-align: top; padding-top: 2px;">
                                                                <span style="color: #D4AF37; font-size: 14px; font-weight: 700;">&#9679;</span>
                                                            </td>
                                                            <td style="padding-left: 10px;">
                                                                <span style="font-size: 14px; color: #F5F5F5;">El proceso tarda <strong style="color: #D4AF37;">menos de 1 minuto</strong></span>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                        </table>

                                        <!-- Primary CTA — Red -->
                                        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin-bottom: 24px;">
                                            <tr>
                                                <td align="center">
                                                    <a href="{{ $updateUrl }}"
                                                       style="display: inline-block; background-color: #8B1E1E; color: #F5F5F5; font-size: 16px; font-weight: 800; text-decoration: none; padding: 18px 44px; border-radius: 8px; letter-spacing: 0.5px;">
                                                        Actualizar método de pago &rarr;
                                                    </a>
                                                </td>
                                            </tr>
                                        </table>

                                        <!-- Small note -->
                                        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                            <tr>
                                                <td align="center">
                                                    <p style="margin: 0; font-size: 13px; color: #6B7280; line-height: 1.5;">
                                                        Si crees que esto es un error, contáctanos en
                                                        <a href="mailto:soporte@restaurantesmexicanosfamosos.com"
                                                           style="color: #9CA3AF; text-decoration: underline;">soporte@restaurantesmexicanosfamosos.com</a>
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
