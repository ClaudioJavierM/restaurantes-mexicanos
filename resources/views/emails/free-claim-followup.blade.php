<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tu restaurante está activo en FAMER</title>
</head>
<body style="margin: 0; padding: 0; background-color: #0B0B0B; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; color: #F5F5F5;">

    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color: #0B0B0B;">
        <tr>
            <td align="center" style="padding: 40px 20px;">

                <!-- Container -->
                <table role="presentation" width="600" cellspacing="0" cellpadding="0" border="0" style="max-width: 600px; width: 100%;">

                    <!-- Header -->
                    <tr>
                        <td style="background-color: #111111; border-top: 3px solid #D4AF37; border-radius: 12px 12px 0 0; padding: 36px 40px; text-align: center;">
                            <div style="font-size: 28px; font-weight: 800; letter-spacing: 4px; color: #D4AF37; text-transform: uppercase; margin-bottom: 4px;">FAMER</div>
                            <div style="font-size: 12px; letter-spacing: 2px; color: #888888; text-transform: uppercase;">Restaurantes Mexicanos Famosos</div>
                        </td>
                    </tr>

                    <!-- Greeting -->
                    <tr>
                        <td style="background-color: #161616; padding: 40px 40px 24px 40px;">
                            <h1 style="margin: 0 0 12px 0; font-size: 24px; font-weight: 700; color: #F5F5F5; line-height: 1.3;">
                                Hola {{ $ownerName }},
                            </h1>
                            <p style="margin: 0; font-size: 16px; color: #AAAAAA; line-height: 1.6;">
                                Tu restaurante <strong style="color: #F5F5F5;">{{ $restaurant->name }}</strong> lleva 48 horas activo en FAMER.
                                Aquí está un resumen de lo que ha pasado.
                            </p>
                        </td>
                    </tr>

                    @if($views48h > 0)
                    <!-- Stats Card — Visits in 48h -->
                    <tr>
                        <td style="background-color: #161616; padding: 0 40px 24px 40px;">
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0"
                                   style="background: linear-gradient(135deg, #1A1500 0%, #221C00 100%); border: 1px solid #D4AF37; border-radius: 10px; padding: 28px 32px;">
                                <tr>
                                    <td>
                                        <div style="font-size: 13px; letter-spacing: 2px; text-transform: uppercase; color: #D4AF37; margin-bottom: 10px;">Visitas en 48 horas</div>
                                        <div style="font-size: 52px; font-weight: 800; color: #D4AF37; line-height: 1; margin-bottom: 8px;">{{ number_format($views48h) }}</div>
                                        <div style="font-size: 15px; color: #CCCCCC; line-height: 1.5;">
                                            personas visitaron el perfil de tu restaurante.<br>
                                            <span style="color: #888888;">Total acumulado: {{ number_format($totalViews) }} vistas</span>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    @else
                    <!-- Stats Card — No visits yet -->
                    <tr>
                        <td style="background-color: #161616; padding: 0 40px 24px 40px;">
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0"
                                   style="background-color: #1A1A1A; border: 1px solid #333333; border-radius: 10px; padding: 28px 32px;">
                                <tr>
                                    <td>
                                        <div style="font-size: 13px; letter-spacing: 2px; text-transform: uppercase; color: #888888; margin-bottom: 10px;">Visibilidad actual</div>
                                        <div style="font-size: 18px; font-weight: 600; color: #F5F5F5; margin-bottom: 8px;">Tu perfil ya está en línea</div>
                                        <div style="font-size: 15px; color: #AAAAAA; line-height: 1.6;">
                                            Tu restaurante ya aparece en FAMER. Actualiza tu perfil y activa Premium para destacar en los resultados de búsqueda.
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    @endif

                    <!-- Competitor Context -->
                    <tr>
                        <td style="background-color: #161616; padding: 0 40px 24px 40px;">
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0"
                                   style="background-color: #1A1A1A; border-left: 3px solid #8B1E1E; border-radius: 0 8px 8px 0; padding: 20px 24px;">
                                <tr>
                                    <td>
                                        <div style="font-size: 14px; color: #CCCCCC; line-height: 1.6;">
                                            <strong style="color: #F5F5F5;">{{ number_format($competitorCount) }} restaurantes</strong> compiten en tu área.
                                            Con el plan gratuito, tu perfil aparece al final. Con Premium, apareces primero.
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- What you get with Premium -->
                    <tr>
                        <td style="background-color: #161616; padding: 0 40px 24px 40px;">
                            <div style="font-size: 14px; font-weight: 700; letter-spacing: 1px; text-transform: uppercase; color: #D4AF37; margin-bottom: 16px;">Con Premium obtienes</div>
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
                                @foreach([
                                    ['icon' => '★', 'text' => 'Apareces antes que los restaurantes gratuitos'],
                                    ['icon' => '📊', 'text' => 'Analytics completo: quién te visita, de dónde, qué busca'],
                                    ['icon' => '🎟', 'text' => 'Cupones y promociones para atraer clientes nuevos'],
                                    ['icon' => '📧', 'text' => 'Campañas de email a clientes interesados en tu zona'],
                                    ['icon' => '🔍', 'text' => 'SEO premium: aparece en Google con datos estructurados'],
                                ] as $feature)
                                <tr>
                                    <td style="padding: 6px 0;">
                                        <table role="presentation" cellspacing="0" cellpadding="0" border="0">
                                            <tr>
                                                <td style="width: 28px; font-size: 16px; vertical-align: top; padding-top: 1px;">{{ $feature['icon'] }}</td>
                                                <td style="font-size: 14px; color: #CCCCCC; line-height: 1.5;">{{ $feature['text'] }}</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                @endforeach
                            </table>
                        </td>
                    </tr>

                    <!-- Primary CTA — Premium -->
                    <tr>
                        <td style="background-color: #161616; padding: 0 40px 16px 40px; text-align: center;">
                            <a href="https://restaurantesmexicanosfamosos.com/claim?restaurant={{ $restaurant->slug }}&upgrade=premium"
                               style="display: inline-block; background-color: #D4AF37; color: #0B0B0B; font-size: 16px; font-weight: 800;
                                      text-decoration: none; padding: 16px 40px; border-radius: 8px; letter-spacing: 0.5px; width: 100%; box-sizing: border-box;">
                                Actualizar a Premium — $9.99 primer mes
                            </a>
                        </td>
                    </tr>

                    <!-- Secondary CTA — Elite -->
                    <tr>
                        <td style="background-color: #161616; padding: 0 40px 16px 40px; text-align: center;">
                            <a href="https://restaurantesmexicanosfamosos.com/claim?restaurant={{ $restaurant->slug }}&upgrade=elite"
                               style="display: inline-block; background-color: transparent; color: #D4AF37; font-size: 14px; font-weight: 600;
                                      text-decoration: none; padding: 14px 40px; border-radius: 8px; border: 1px solid #D4AF37;
                                      width: 100%; box-sizing: border-box;">
                                O prueba Elite gratis 30 días — $29/mes después
                            </a>
                        </td>
                    </tr>

                    <!-- View profile link -->
                    <tr>
                        <td style="background-color: #161616; padding: 0 40px 40px 40px; text-align: center;">
                            <a href="https://restaurantesmexicanosfamosos.com/restaurante/{{ $restaurant->slug }}"
                               style="font-size: 13px; color: #888888; text-decoration: underline;">
                                Ver mi perfil en FAMER →
                            </a>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #0D0D0D; border-top: 1px solid #222222; border-radius: 0 0 12px 12px; padding: 28px 40px; text-align: center;">
                            <div style="font-size: 11px; color: #555555; line-height: 1.8;">
                                Recibiste este mensaje porque reclamaste <strong style="color: #666666;">{{ $restaurant->name }}</strong> en FAMER.<br>
                                <a href="mailto:unsubscribe@restaurantesmexicanosfamosos.com?subject=Unsubscribe&body=Restaurant+ID:+{{ $restaurant->id }}"
                                   style="color: #555555; text-decoration: underline;">Cancelar suscripción</a>
                                &nbsp;·&nbsp;
                                <a href="https://restaurantesmexicanosfamosos.com/owner"
                                   style="color: #555555; text-decoration: underline;">Panel de propietario</a>
                            </div>
                            <div style="margin-top: 16px; font-size: 10px; letter-spacing: 2px; color: #333333; text-transform: uppercase;">
                                © {{ date('Y') }} FAMER — restaurantesmexicanosfamosos.com
                            </div>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>

</body>
</html>
