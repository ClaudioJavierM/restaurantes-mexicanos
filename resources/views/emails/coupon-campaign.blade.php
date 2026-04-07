<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Oferta Exclusiva para {{ $restaurantName }}</title>
</head>
<body style="margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f5f5f5;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #f5f5f5;">
        <tr>
            <td align="center" style="padding: 40px 20px;">
                <table role="presentation" width="600" cellspacing="0" cellpadding="0" style="background-color: #ffffff; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">

                    <!-- Header -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #059669 0%, #C9A84C 100%); padding: 30px 40px; border-radius: 12px 12px 0 0; text-align: center;">
                            <img src="https://restaurantesmexicanosfamosos.com/images/branding/logo.png?v=3" alt="Restaurantes Mexicanos Famosos" style="max-height: 50px; width: auto; margin-bottom: 12px;" />
                            <h1 style="color: #ffffff; margin: 0; font-size: 28px;">
                                Restaurantes Mexicanos Famosos
                            </h1>
                            <p style="color: rgba(255,255,255,0.9); margin: 10px 0 0 0; font-size: 16px;">
                                Oferta EXCLUSIVA para clientes de MF Imports
                            </p>
                        </td>
                    </tr>

                    <!-- Main Content -->
                    <tr>
                        <td style="padding: 40px;">
                            <!-- Greeting -->
                            <p style="color: #4b5563; font-size: 18px; margin: 0 0 20px 0;">
                                Hola <strong>{{ $contactName }}</strong>,
                            </p>

                            <p style="color: #4b5563; font-size: 16px; line-height: 1.6; margin: 0 0 20px 0;">
                                Como cliente especial de <strong>MF Imports</strong>, te ofrecemos una oportunidad unica para hacer crecer tu negocio: <strong>{{ $restaurantName }}</strong>.
                            </p>

                            <!-- Coupon Box -->
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); border-radius: 12px; margin: 30px 0; border: 2px dashed #f59e0b;">
                                <tr>
                                    <td style="padding: 30px; text-align: center;">
                                        <p style="color: #92400e; margin: 0 0 10px 0; font-size: 14px; text-transform: uppercase; letter-spacing: 1px;">
                                            Tu codigo de descuento exclusivo
                                        </p>
                                        <p style="background-color: #ffffff; color: #dc2626; margin: 0; font-size: 32px; font-weight: bold; padding: 15px 30px; border-radius: 8px; display: inline-block; letter-spacing: 3px; border: 2px solid #dc2626;">
                                            {{ $couponCode }}
                                        </p>
                                        <p style="color: #78350f; margin: 15px 0 0 0; font-size: 24px; font-weight: bold;">
                                            {{ $discountPercent }}% DE DESCUENTO
                                        </p>
                                        <p style="color: #92400e; margin: 10px 0 0 0; font-size: 14px;">
                                            en tu primer mes de suscripcion Premium
                                        </p>
                                        <p style="color: #b45309; margin: 10px 0 0 0; font-size: 12px;">
                                            Valido hasta: {{ $expirationDate }}
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <!-- What You Get -->
                            <h3 style="color: #1f2937; margin: 30px 0 15px 0; font-size: 20px;">
                                Que incluye Premium?
                            </h3>

                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td style="padding: 10px 0; border-bottom: 1px solid #e5e7eb;">
                                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0">
                                            <tr>
                                                <td width="40" style="color: #10b981; font-size: 20px;">&#10003;</td>
                                                <td style="color: #4b5563; font-size: 15px;"><strong>Insignia de Restaurante Verificado</strong> - Genera mas confianza</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 10px 0; border-bottom: 1px solid #e5e7eb;">
                                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0">
                                            <tr>
                                                <td width="40" style="color: #10b981; font-size: 20px;">&#10003;</td>
                                                <td style="color: #4b5563; font-size: 15px;"><strong>Posicion destacada</strong> - Aparece primero en busquedas de {{ $city }}</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 10px 0; border-bottom: 1px solid #e5e7eb;">
                                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0">
                                            <tr>
                                                <td width="40" style="color: #10b981; font-size: 20px;">&#10003;</td>
                                                <td style="color: #4b5563; font-size: 15px;"><strong>Menu Digital + Codigo QR</strong> - Actualizable en tiempo real</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 10px 0; border-bottom: 1px solid #e5e7eb;">
                                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0">
                                            <tr>
                                                <td width="40" style="color: #10b981; font-size: 20px;">&#10003;</td>
                                                <td style="color: #4b5563; font-size: 15px;"><strong>Chatbot con IA</strong> - Responde preguntas 24/7</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 10px 0; border-bottom: 1px solid #e5e7eb;">
                                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0">
                                            <tr>
                                                <td width="40" style="color: #10b981; font-size: 20px;">&#10003;</td>
                                                <td style="color: #4b5563; font-size: 15px;"><strong>Analytics detallado</strong> - Ve cuantas personas visitan tu perfil</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 10px 0;">
                                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0">
                                            <tr>
                                                <td width="40" style="color: #10b981; font-size: 20px;">&#10003;</td>
                                                <td style="color: #4b5563; font-size: 15px;"><strong>Fotos ilimitadas</strong> - Muestra lo mejor de tu negocio</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <!-- Pricing -->
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background: linear-gradient(135deg, #7c3aed 0%, #a855f7 100%); border-radius: 12px; margin: 30px 0;">
                                <tr>
                                    <td style="padding: 25px; text-align: center;">
                                        <p style="color: rgba(255,255,255,0.9); margin: 0 0 5px 0; font-size: 14px;">
                                            Precio normal: <span style="text-decoration: line-through;">$39/mes</span>
                                        </p>
                                        <p style="color: #ffffff; margin: 0; font-size: 28px; font-weight: bold;">
                                            Con tu cupon: <span style="color: #fcd34d;">${{ number_format(39 * (100 - $discountPercent) / 100, 2) }}/mes</span>
                                        </p>
                                        <p style="color: rgba(255,255,255,0.8); margin: 10px 0 0 0; font-size: 13px;">
                                            *Descuento aplicable al primer mes
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <!-- CTA Button -->
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="margin: 30px 0;">
                                <tr>
                                    <td align="center">
                                        <a href="{{ config('app.url') }}/claim?coupon={{ $couponCode }}" style="display: inline-block; background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%); color: #ffffff; text-decoration: none; padding: 18px 50px; border-radius: 8px; font-size: 18px; font-weight: bold; box-shadow: 0 4px 6px rgba(220, 38, 38, 0.3);">
                                            RECLAMAR MI DESCUENTO AHORA
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <!-- Trust badges -->
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="margin: 30px 0; text-align: center;">
                                <tr>
                                    <td style="padding: 10px; border-right: 1px solid #e5e7eb;">
                                        <p style="color: #059669; font-size: 24px; font-weight: bold; margin: 0;">6,000+</p>
                                        <p style="color: #6b7280; font-size: 12px; margin: 5px 0 0 0;">Restaurantes</p>
                                    </td>
                                    <td style="padding: 10px; border-right: 1px solid #e5e7eb;">
                                        <p style="color: #059669; font-size: 24px; font-weight: bold; margin: 0;">50</p>
                                        <p style="color: #6b7280; font-size: 12px; margin: 5px 0 0 0;">Estados</p>
                                    </td>
                                    <td style="padding: 10px;">
                                        <p style="color: #059669; font-size: 24px; font-weight: bold; margin: 0;">100%</p>
                                        <p style="color: #6b7280; font-size: 12px; margin: 5px 0 0 0;">Mexicano</p>
                                    </td>
                                </tr>
                            </table>

                            <!-- MF Imports mention -->
                            <div style="border-top: 1px solid #e5e7eb; padding-top: 25px; margin-top: 25px;">
                                <p style="color: #6b7280; font-size: 14px; line-height: 1.6; margin: 0; text-align: center;">
                                    <strong>Gracias por ser cliente de MF Imports!</strong><br>
                                    Esta oferta es nuestra manera de agradecer tu preferencia y ayudarte a hacer crecer tu restaurante.
                                </p>
                            </div>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #1f2937; padding: 25px 40px; border-radius: 0 0 12px 12px; text-align: center;">
                            <p style="color: #9ca3af; margin: 0 0 10px 0; font-size: 13px;">
                                Este email fue enviado a clientes especiales de MF Imports.
                            </p>
                            <p style="color: #6b7280; margin: 0; font-size: 12px;">
                                <a href="{{ config('app.url') }}" style="color: #10b981; text-decoration: none;">restaurantesmexicanosfamosos.com</a>
                                |
                                <a href="{{ config('app.url') }}/privacy" style="color: #6b7280; text-decoration: none;">Privacidad</a>
                                |
                                <a href="mailto:unsubscribe@restaurantesmexicanosfamosos.com?subject=Cancelar%20suscripcion" style="color: #6b7280; text-decoration: none;">Cancelar suscripcion</a>
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
