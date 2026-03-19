<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restablecer contraseña</title>
</head>
<body style="margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f5f5f5;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #f5f5f5;">
        <tr>
            <td align="center" style="padding: 40px 20px;">
                <table role="presentation" width="600" cellspacing="0" cellpadding="0" style="background-color: #ffffff; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">

                    <!-- Header -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%); padding: 30px 40px; border-radius: 12px 12px 0 0; text-align: center;">
                            <img src="https://restaurantesmexicanosfamosos.com/images/branding/logo.png?v=3" alt="Restaurantes Mexicanos Famosos" style="max-height: 50px; width: auto; margin-bottom: 12px;" />
                            <p style="color: #fcd34d; margin: 0 0 8px 0; font-size: 36px;">&#128274;</p>
                            <h1 style="color: #ffffff; margin: 0; font-size: 24px;">
                                Restablecer contraseña
                            </h1>
                            <p style="color: rgba(255,255,255,0.8); margin: 8px 0 0 0; font-size: 14px;">
                                Recibimos una solicitud para restablecer tu contraseña
                            </p>
                        </td>
                    </tr>

                    <!-- Main Content -->
                    <tr>
                        <td style="padding: 40px;">
                            <p style="color: #374151; font-size: 15px; line-height: 1.7; margin: 0 0 20px 0;">
                                Hola{{ $user->name ? ' ' . $user->name : '' }},
                            </p>

                            <p style="color: #4b5563; font-size: 15px; line-height: 1.7; margin: 0 0 25px 0;">
                                Hemos recibido una solicitud para restablecer la contraseña de tu cuenta en
                                <strong>Restaurantes Mexicanos Famosos</strong>. Haz clic en el siguiente boton para crear una nueva contraseña:
                            </p>

                            <!-- CTA Button -->
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td align="center" style="padding: 10px 0 30px 0;">
                                        <a href="{{ $url }}" style="display: inline-block; background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%); color: #ffffff; text-decoration: none; padding: 14px 40px; border-radius: 8px; font-size: 16px; font-weight: 600; box-shadow: 0 4px 12px rgba(220,38,38,0.3);">
                                            Restablecer contraseña
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <!-- Security Notice -->
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #fef3c7; border: 1px solid #fcd34d; border-radius: 8px; margin: 0 0 25px 0;">
                                <tr>
                                    <td style="padding: 16px 20px;">
                                        <p style="color: #92400e; font-size: 13px; line-height: 1.6; margin: 0;">
                                            <strong>&#9888; Nota de seguridad:</strong><br>
                                            Este enlace expira en <strong>60 minutos</strong>. Si no solicitaste restablecer tu contraseña, puedes ignorar este correo. Tu cuenta permanecera segura.
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <!-- Fallback URL -->
                            <p style="color: #9ca3af; font-size: 12px; line-height: 1.5; margin: 0; word-break: break-all;">
                                Si el boton no funciona, copia y pega este enlace en tu navegador:<br>
                                <a href="{{ $url }}" style="color: #dc2626;">{{ $url }}</a>
                            </p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f9fafb; padding: 24px 40px; border-radius: 0 0 12px 12px; border-top: 1px solid #e5e7eb; text-align: center;">
                            <p style="color: #6b7280; font-size: 12px; margin: 0 0 8px 0;">
                                Este correo fue enviado automaticamente por
                            </p>
                            <p style="color: #374151; font-size: 13px; font-weight: 600; margin: 0;">
                                Restaurantes Mexicanos Famosos
                            </p>
                            <p style="color: #9ca3af; font-size: 11px; margin: 8px 0 0 0;">
                                <a href="https://restaurantesmexicanosfamosos.com" style="color: #dc2626; text-decoration: none;">restaurantesmexicanosfamosos.com</a>
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
