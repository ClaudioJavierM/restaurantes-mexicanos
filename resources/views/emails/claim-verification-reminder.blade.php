<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Completa tu Verificacion</title>
</head>
<body style="margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f5f5f5;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #f5f5f5;">
        <tr>
            <td align="center" style="padding: 40px 20px;">
                <table role="presentation" width="600" cellspacing="0" cellpadding="0" style="background-color: #ffffff; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">

                    <!-- Header -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); padding: 30px 40px; border-radius: 12px 12px 0 0; text-align: center;">
                            <h1 style="color: #ffffff; margin: 0; font-size: 24px;">
                                @if($reminderNumber === 1)
                                    Casi terminas!
                                @else
                                    Ultimo recordatorio!
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
                                Iniciaste el proceso para reclamar <strong>{{ $claim->restaurant->name }}</strong> pero aun no has verificado tu identidad.
                            </p>

                            @if($reminderNumber === 2)
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #fef3c7; border: 1px solid #fcd34d; border-radius: 8px; margin: 20px 0;">
                                <tr>
                                    <td style="padding: 15px;">
                                        <p style="color: #92400e; margin: 0; font-size: 14px;">
                                            <strong>Importante:</strong> Tu codigo de verificacion expirara pronto. Completa el proceso ahora para no perder tu progreso.
                                        </p>
                                    </td>
                                </tr>
                            </table>
                            @endif

                            <p style="color: #4b5563; font-size: 16px; line-height: 1.8; margin: 0 0 25px 0;">
                                Te enviamos un codigo de 6 digitos a tu {{ $claim->verification_method === 'email' ? 'correo electronico' : 'telefono' }}. Ingresalo para completar la verificacion.
                            </p>

                            <!-- CTA Button -->
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="margin: 25px 0;">
                                <tr>
                                    <td align="center">
                                        <a href="{{ $verifyUrl }}" style="display: inline-block; background: linear-gradient(135deg, #059669 0%, #10b981 100%); color: #ffffff; text-decoration: none; padding: 16px 40px; border-radius: 8px; font-size: 18px; font-weight: bold;">
                                            COMPLETAR VERIFICACION
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <p style="color: #6b7280; font-size: 14px; line-height: 1.6; margin: 25px 0 0 0;">
                                Si no solicitaste esto, puedes ignorar este mensaje.
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
