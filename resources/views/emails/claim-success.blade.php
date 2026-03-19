<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenido a tu Dashboard</title>
</head>
<body style="margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f5f5f5;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #f5f5f5;">
        <tr>
            <td align="center" style="padding: 40px 20px;">
                <table role="presentation" width="600" cellspacing="0" cellpadding="0" style="background-color: #ffffff; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">

                    <!-- Header -->
                    <tr>
                        <td style="background: linear-gradient(135deg, {{ $plan === 'elite' ? '#b45309' : '#dc2626' }} 0%, {{ $plan === 'elite' ? '#d97706' : '#ef4444' }} 100%); padding: 30px 40px; border-radius: 12px 12px 0 0; text-align: center;">
                            <img src="https://restaurantesmexicanosfamosos.com/images/branding/logo.png?v=3" alt="Restaurantes Mexicanos Famosos" style="max-height: 50px; width: auto; margin-bottom: 12px;" />
                            @if($plan === 'elite')
                                <p style="color: #fcd34d; margin: 0 0 8px 0; font-size: 40px;">&#127942;</p>
                            @elseif($plan === 'premium')
                                <p style="color: #fcd34d; margin: 0 0 8px 0; font-size: 40px;">&#11088;</p>
                            @else
                                <p style="color: #ffffff; margin: 0 0 8px 0; font-size: 40px;">&#127881;</p>
                            @endif
                            <h1 style="color: #ffffff; margin: 0; font-size: 26px;">
                                Bienvenido{{ $plan === 'elite' ? ' al Club Elite' : ($plan === 'premium' ? ' a Premium' : '') }}, {{ $user->name }}!
                            </h1>
                            <p style="color: rgba(255,255,255,0.9); margin: 10px 0 0 0; font-size: 16px;">
                                Tu suscripcion <strong>{{ match($plan) { 'claimed', 'free' => 'Gratuito', 'premium' => 'Premium', 'elite' => 'Elite', default => ucfirst($plan) } }}</strong> esta activa
                            </p>
                        </td>
                    </tr>

                    <!-- Main Content -->
                    <tr>
                        <td style="padding: 40px;">
                            <!-- Restaurant Name -->
                            <h2 style="color: #1f2937; margin: 0 0 20px 0; font-size: 22px; text-align: center;">
                                {{ $restaurant->name }}
                            </h2>

                            <p style="color: #4b5563; font-size: 15px; line-height: 1.7; text-align: center; margin: 0 0 25px 0;">
                                Tu restaurante ha sido verificado y tu plan <strong>{{ match($plan) { 'claimed', 'free' => 'Gratuito', 'premium' => 'Premium', 'elite' => 'Elite', default => ucfirst($plan) } }}</strong> esta completamente activo.
                                Ya puedes acceder a tu panel de propietario para gestionar tu restaurante.
                            </p>

                            <!-- Account Credentials Box -->
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #fef3c7; border: 1px solid #fcd34d; border-radius: 8px; margin: 25px 0;">
                                <tr>
                                    <td style="padding: 20px;">
                                        <h3 style="color: #92400e; margin: 0 0 12px 0; font-size: 16px;">
                                            &#128273; Datos de acceso a tu cuenta:
                                        </h3>
                                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0">
                                            <tr>
                                                <td style="padding: 4px 0;">
                                                    <p style="color: #78350f; margin: 0; font-size: 14px;">
                                                        <strong>Email:</strong> {{ $user->email }}
                                                    </p>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 4px 0;">
                                                    <p style="color: #78350f; margin: 0; font-size: 14px;">
                                                        <strong>Contrasena temporal:</strong> {{ $tempPassword }}
                                                    </p>
                                                </td>
                                            </tr>
                                        </table>
                                        <p style="color: #92400e; margin: 12px 0 0 0; font-size: 12px;">
                                            &#9888; Te recomendamos cambiar tu contrasena al iniciar sesion.
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <!-- CTA Button - Dashboard -->
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="margin: 25px 0;">
                                <tr>
                                    <td align="center">
                                        <a href="{{ $dashboardUrl }}" style="display: inline-block; background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%); color: #ffffff; text-decoration: none; padding: 16px 40px; border-radius: 8px; font-size: 16px; font-weight: bold; box-shadow: 0 4px 6px rgba(220, 38, 38, 0.3); letter-spacing: 0.5px;">
                                            ACCEDER A MI DASHBOARD
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <!-- Reset Password Link -->
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="margin: 10px 0 25px 0;">
                                <tr>
                                    <td align="center">
                                        <a href="{{ $resetPasswordUrl }}" style="color: #dc2626; text-decoration: underline; font-size: 13px;">
                                            Cambiar mi contrasena
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <!-- Plan Features -->
                            @if($plan === 'elite')
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #fffbeb; border: 1px solid #fcd34d; border-radius: 8px; margin: 25px 0;">
                                <tr>
                                    <td style="padding: 20px;">
                                        <h3 style="color: #92400e; margin: 0 0 12px 0; font-size: 16px;">
                                            &#127942; Tu Plan Elite incluye:
                                        </h3>
                                        <ul style="color: #78350f; font-size: 14px; line-height: 2; padding-left: 20px; margin: 0;">
                                            <li>Badge Elite Dorado - La maxima distincion</li>
                                            <li>Posicion #1 en busquedas de tu area</li>
                                            <li>Menu Digital + Codigo QR</li>
                                            <li>Dashboard de Analiticas Avanzadas</li>
                                            <li>Marketing Automatizado (email y SMS)</li>
                                            <li>Chatbot AI 24/7</li>
                                            <li>Account Manager Dedicado</li>
                                            <li>6 Cupones con 15% Extra de descuento</li>
                                        </ul>
                                    </td>
                                </tr>
                            </table>
                            @elseif($plan === 'premium')
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #fef2f2; border: 1px solid #fecaca; border-radius: 8px; margin: 25px 0;">
                                <tr>
                                    <td style="padding: 20px;">
                                        <h3 style="color: #991b1b; margin: 0 0 12px 0; font-size: 16px;">
                                            &#11088; Tu Plan Premium incluye:
                                        </h3>
                                        <ul style="color: #7f1d1d; font-size: 14px; line-height: 2; padding-left: 20px; margin: 0;">
                                            <li>Badge Premium Verificado</li>
                                            <li>Top 3 en busquedas locales</li>
                                            <li>Menu Digital + Codigo QR</li>
                                            <li>Sistema de Reservaciones</li>
                                            <li>Dashboard de Analiticas</li>
                                            <li>Fotos y Videos Ilimitados</li>
                                            <li>Chatbot AI 24/7</li>
                                            <li>4 Cupones Trimestrales de descuento</li>
                                        </ul>
                                    </td>
                                </tr>
                            </table>
                            @endif

                            <!-- Next Steps -->
                            <h3 style="color: #1f2937; margin: 25px 0 15px 0; font-size: 17px;">
                                &#128161; Primeros pasos recomendados:
                            </h3>
                            <ol style="color: #4b5563; font-size: 14px; line-height: 2; padding-left: 20px; margin: 0;">
                                <li>Inicia sesion en tu dashboard</li>
                                <li>Actualiza la informacion de tu restaurante</li>
                                <li>Sube fotos de tus platillos y local</li>
                                <li>Configura tu menu digital</li>
                                <li>Responde a las resenas de tus clientes</li>
                            </ol>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #1f2937; padding: 25px 40px; border-radius: 0 0 12px 12px; text-align: center;">
                            <p style="color: #d1d5db; margin: 0 0 8px 0; font-size: 14px; font-weight: bold;">
                                Restaurantes Mexicanos Famosos
                            </p>
                            <p style="color: #9ca3af; margin: 0 0 10px 0; font-size: 13px;">
                                Gracias por confiar en nosotros para hacer crecer tu negocio
                            </p>
                            <p style="color: #6b7280; margin: 0; font-size: 12px;">
                                <a href="https://restaurantesmexicanosfamosos.com" style="color: #ef4444; text-decoration: none;">restaurantesmexicanosfamosos.com</a>
                                &nbsp;|&nbsp;
                                <a href="mailto:support@restaurantesmexicanosfamosos.com" style="color: #ef4444; text-decoration: none;">Soporte</a>
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
