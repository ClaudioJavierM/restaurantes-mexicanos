<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f5f5f5;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #f5f5f5;">
        <tr>
            <td align="center" style="padding: 40px 20px;">
                <table role="presentation" width="600" cellspacing="0" cellpadding="0" style="background-color: #ffffff; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">

                    <!-- Header -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #059669 0%, #10b981 100%); padding: 25px 40px; border-radius: 12px 12px 0 0; text-align: center;">
                            <img src="https://restaurantesmexicanosfamosos.com/images/branding/logo.png?v=3" alt="Restaurantes Mexicanos Famosos" style="max-height: 50px; width: auto; margin-bottom: 12px;" />
                            <h1 style="color: #ffffff; margin: 0; font-size: 24px;">
                                Hola {{ $owner->name }}!
                            </h1>
                        </td>
                    </tr>

                    <!-- Main Content -->
                    <tr>
                        <td style="padding: 40px;">
                            <h2 style="color: #1f2937; margin: 0 0 20px 0; font-size: 22px; text-align: center;">
                                {{ $restaurant->name }} ha tenido actividad
                            </h2>

                            <!-- Stats Box -->
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="margin: 25px 0;">
                                <tr>
                                    <td width="33%" style="text-align: center; padding: 20px; background-color: #ecfdf5; border-radius: 8px 0 0 8px;">
                                        <p style="color: #059669; font-size: 28px; font-weight: bold; margin: 0;">{{ $stats['profile_views'] }}</p>
                                        <p style="color: #6b7280; font-size: 12px; margin: 5px 0 0 0;">Visitas al perfil</p>
                                    </td>
                                    <td width="33%" style="text-align: center; padding: 20px; background-color: #fef3c7;">
                                        <p style="color: #d97706; font-size: 28px; font-weight: bold; margin: 0;">{{ $stats['new_reviews'] }}</p>
                                        <p style="color: #6b7280; font-size: 12px; margin: 5px 0 0 0;">Resenas</p>
                                    </td>
                                    <td width="33%" style="text-align: center; padding: 20px; background-color: #ede9fe; border-radius: 0 8px 8px 0;">
                                        <p style="color: #7c3aed; font-size: 28px; font-weight: bold; margin: 0;">{{ number_format($stats['average_rating'], 1) }}</p>
                                        <p style="color: #6b7280; font-size: 12px; margin: 5px 0 0 0;">Rating promedio</p>
                                    </td>
                                </tr>
                            </table>

                            <p style="color: #4b5563; font-size: 16px; line-height: 1.6; text-align: center;">
                                Clientes potenciales estan viendo tu restaurante.
                                <strong>Inicia sesion</strong> para ver analytics completos y responder a resenas.
                            </p>

                            <!-- CTA Button -->
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="margin: 30px 0;">
                                <tr>
                                    <td align="center">
                                        <a href="{{ $loginUrl }}" style="display: inline-block; background: linear-gradient(135deg, #059669 0%, #10b981 100%); color: #ffffff; text-decoration: none; padding: 16px 40px; border-radius: 8px; font-size: 18px; font-weight: bold;">
                                            VER MI PANEL DE CONTROL
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <!-- Tips -->
                            <div style="background-color: #f9fafb; border-radius: 8px; padding: 20px; margin-top: 25px;">
                                <h4 style="color: #374151; margin: 0 0 10px 0; font-size: 14px;">
                                    Consejo para aumentar visitas:
                                </h4>
                                <ul style="color: #6b7280; font-size: 14px; margin: 0; padding-left: 20px; line-height: 1.8;">
                                    <li>Agrega mas fotos de tus platillos</li>
                                    <li>Responde a las resenas de clientes</li>
                                    <li>Actualiza tu menu y horarios</li>
                                    <li>Considera upgrade a <strong style="color: #7c3aed;">Premium</strong> para destacar</li>
                                </ul>
                            </div>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #1f2937; padding: 20px 40px; border-radius: 0 0 12px 12px; text-align: center;">
                            <p style="color: #9ca3af; margin: 0; font-size: 12px;">
                                Recibes este email porque eres dueno verificado de {{ $restaurant->name }}.
                                <br>
                                <a href="{{ config('app.url') }}/unsubscribe?email={{ urlencode($owner->email) }}" style="color: #6b7280;">Cancelar recordatorios</a>
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
