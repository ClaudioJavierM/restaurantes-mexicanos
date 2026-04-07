<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restaurante Verificado</title>
</head>
<body style="margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f5f5f5;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #f5f5f5;">
        <tr>
            <td align="center" style="padding: 40px 20px;">
                <table role="presentation" width="600" cellspacing="0" cellpadding="0" style="background-color: #ffffff; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">

                    <!-- Header -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #059669 0%, #10b981 100%); padding: 30px 40px; border-radius: 12px 12px 0 0; text-align: center;">
                            <img src="https://restaurantesmexicanosfamosos.com/images/branding/logo.png?v=3" alt="Restaurantes Mexicanos Famosos" style="max-height: 50px; width: auto; margin-bottom: 12px;" />
                            <h1 style="color: #ffffff; margin: 0; font-size: 28px;">
                                Felicidades {{ $user->name }}!
                            </h1>
                            <p style="color: rgba(255,255,255,0.9); margin: 10px 0 0 0; font-size: 18px;">
                                Tu restaurante ha sido verificado
                            </p>
                        </td>
                    </tr>

                    <!-- Main Content -->
                    <tr>
                        <td style="padding: 40px;">
                            <!-- Success Icon -->
                            <div style="text-align: center; margin-bottom: 25px;">
                                <span style="font-size: 60px;">&#9989;</span>
                            </div>

                            <!-- Restaurant Name -->
                            <h2 style="color: #1f2937; margin: 0 0 20px 0; font-size: 24px; text-align: center;">
                                {{ $restaurant->name }}
                            </h2>

                            <p style="color: #4b5563; font-size: 16px; line-height: 1.8; text-align: center; margin: 0 0 25px 0;">
                                Tu restaurante ahora tiene la <strong style="color: #059669;">insignia de verificado</strong> 
                                y aparece con prioridad en los resultados de busqueda.
                            </p>

                            <!-- Account Info Box -->
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px; margin: 25px 0;">
                                <tr>
                                    <td style="padding: 20px;">
                                        <h3 style="color: #166534; margin: 0 0 15px 0; font-size: 16px;">
                                            Tu cuenta ha sido creada:
                                        </h3>
                                        <p style="color: #15803d; margin: 0 0 8px 0; font-size: 15px;">
                                            <strong>Email:</strong> {{ $user->email }}
                                        </p>
                                        <p style="color: #15803d; margin: 0; font-size: 15px;">
                                            <strong>Contrasena:</strong> <em>Necesitas establecerla</em>
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <!-- CTA Button - Reset Password -->
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="margin: 25px 0;">
                                <tr>
                                    <td align="center">
                                        <a href="{{ $resetPasswordUrl }}" style="display: inline-block; background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%); color: #ffffff; text-decoration: none; padding: 14px 35px; border-radius: 8px; font-size: 16px; font-weight: bold; box-shadow: 0 4px 6px rgba(220, 38, 38, 0.3);">
                                            ESTABLECER MI CONTRASENA
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <!-- Dashboard CTA -->
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="margin: 20px 0;">
                                <tr>
                                    <td align="center">
                                        <a href="{{ $dashboardUrl }}" style="display: inline-block; background: linear-gradient(135deg, #059669 0%, #10b981 100%); color: #ffffff; text-decoration: none; padding: 14px 35px; border-radius: 8px; font-size: 16px; font-weight: bold; box-shadow: 0 4px 6px rgba(5, 150, 105, 0.3);">
                                            IR A MI PANEL DE CONTROL
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <!-- What you can do now -->
                            <h3 style="color: #1f2937; margin: 30px 0 15px 0; font-size: 18px;">
                                Que puedes hacer ahora:
                            </h3>
                            <ul style="color: #4b5563; font-size: 15px; line-height: 2; padding-left: 20px; margin: 0;">
                                <li>Actualizar la informacion de tu restaurante</li>
                                <li>Subir fotos de tus platillos y local</li>
                                <li>Agregar o actualizar tu menu</li>
                                <li>Responder a resenas de clientes</li>
                                <li>Ver estadisticas de visitas a tu perfil</li>
                            </ul>

                            <!-- Premium Upgrade Section -->
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background: linear-gradient(135deg, #7c3aed 0%, #a855f7 100%); border-radius: 8px; margin: 30px 0;">
                                <tr>
                                    <td style="padding: 25px; text-align: center;">
                                        <h3 style="color: #ffffff; margin: 0 0 10px 0; font-size: 18px;">
                                            Quieres destacar aun mas?
                                        </h3>
                                        <p style="color: rgba(255,255,255,0.9); margin: 0 0 15px 0; font-size: 14px;">
                                            Actualiza a <strong>Premium</strong> por solo $39/mes y obtiene:
                                        </p>
                                        <p style="color: #fcd34d; margin: 0; font-size: 13px;">
                                            Insignia Premium | Analytics Avanzados | Menu Digital + QR | Chatbot IA
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #1f2937; padding: 25px 40px; border-radius: 0 0 12px 12px; text-align: center;">
                            <p style="color: #9ca3af; margin: 0 0 10px 0; font-size: 13px;">
                                Gracias por ser parte de Restaurantes Mexicanos Famosos
                            </p>
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
