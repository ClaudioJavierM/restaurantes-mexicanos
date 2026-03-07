<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificacion en Proceso</title>
</head>
<body style="margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f5f5f5;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #f5f5f5;">
        <tr>
            <td align="center" style="padding: 40px 20px;">
                <table role="presentation" width="600" cellspacing="0" cellpadding="0" style="background-color: #ffffff; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">

                    <!-- Header -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%); padding: 30px 40px; border-radius: 12px 12px 0 0; text-align: center;">
                            <img src="https://restaurantesmexicanosfamosos.com/images/branding/logo.png?v=3" alt="Restaurantes Mexicanos Famosos" style="max-height: 50px; width: auto; margin-bottom: 12px;" />
                            <h1 style="color: #ffffff; margin: 0; font-size: 24px;">
                                @if($reminderNumber === 1)
                                    Tu verificacion esta en proceso
                                @else
                                    Pronto estara listo!
                                @endif
                            </h1>
                        </td>
                    </tr>

                    <!-- Main Content -->
                    <tr>
                        <td style="padding: 40px;">
                            <p style="color: #4b5563; font-size: 16px; line-height: 1.8; margin: 0 0 20px 0;">
                                Hola <strong>{{ $claim->owner_name }}</strong>,
                            </p>

                            <p style="color: #4b5563; font-size: 16px; line-height: 1.8; margin: 0 0 20px 0;">
                                Gracias por verificar tu identidad como dueno de <strong>{{ $claim->restaurant->name }}</strong>.
                            </p>

                            <!-- Status Box -->
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #eff6ff; border: 1px solid #bfdbfe; border-radius: 8px; margin: 20px 0;">
                                <tr>
                                    <td style="padding: 20px; text-align: center;">
                                        <p style="color: #1e40af; margin: 0 0 10px 0; font-size: 14px;">
                                            <strong>Estado de tu solicitud:</strong>
                                        </p>
                                        <p style="color: #1d4ed8; margin: 0; font-size: 18px; font-weight: bold;">
                                            En revision
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <p style="color: #4b5563; font-size: 16px; line-height: 1.8; margin: 20px 0;">
                                @if($reminderNumber === 1)
                                    Estamos revisando tu solicitud. Normalmente este proceso toma menos de 24 horas. Te notificaremos por email cuando tu restaurante este completamente activado.
                                @else
                                    Tu solicitud esta siendo procesada y sera aprobada muy pronto. En cuanto este lista, recibiras un email con las instrucciones para acceder a tu panel de control.
                                @endif
                            </p>

                            <!-- What's next -->
                            <h3 style="color: #1f2937; margin: 30px 0 15px 0; font-size: 16px;">
                                Que sigue despues de la aprobacion:
                            </h3>
                            <ul style="color: #4b5563; font-size: 15px; line-height: 2; padding-left: 20px; margin: 0;">
                                <li>Recibiras un email con acceso a tu panel</li>
                                <li>Podras actualizar toda la informacion de tu restaurante</li>
                                <li>Tu perfil mostrara la insignia de "Verificado"</li>
                                <li>Tendras acceso a estadisticas de visitas</li>
                            </ul>

                            <p style="color: #6b7280; font-size: 14px; line-height: 1.6; margin: 30px 0 0 0;">
                                Si tienes preguntas, responde a este email o contactanos en soporte@restaurantesmexicanosfamosos.com
                            </p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #1f2937; padding: 25px 40px; border-radius: 0 0 12px 12px; text-align: center;">
                            <p style="color: #6b7280; margin: 0; font-size: 12px;">
                                <a href="{{ config('app.url') }}" style="color: #10b981; text-decoration: none;">restaurantesmexicanosfamosos.com</a>
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
