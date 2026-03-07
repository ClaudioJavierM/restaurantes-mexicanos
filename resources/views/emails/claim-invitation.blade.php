<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reclama tu Restaurante</title>
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
                                El directorio #1 de comida mexicana en USA
                            </p>
                        </td>
                    </tr>

                    <!-- Main Content -->
                    <tr>
                        <td style="padding: 40px;">
                            <!-- Restaurant Name -->
                            <h2 style="color: #1f2937; margin: 0 0 20px 0; font-size: 24px; text-align: center;">
                                {{ $restaurant->name }}
                            </h2>

                            <!-- Exclamation -->
                            <p style="color: #4b5563; font-size: 18px; line-height: 1.6; text-align: center; margin: 0 0 20px 0;">
                                ¡Tu restaurante ya aparece en nuestro directorio con datos de
                                <span style="color: #dc2626; font-weight: bold;">Yelp</span> y
                                <span style="color: #4285f4; font-weight: bold;">Google</span>!
                            </p>

                            <!-- Stats Box -->
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #fef3c7; border-radius: 8px; margin: 20px 0;">
                                <tr>
                                    <td style="padding: 20px; text-align: center;">
                                        <p style="color: #92400e; margin: 0; font-size: 14px;">
                                            <strong>Tu perfil incluye:</strong>
                                        </p>
                                        <p style="color: #78350f; margin: 10px 0 0 0; font-size: 16px;">
                                            @if($restaurant->getMedia('photos')->count() > 0)
                                                {{ $restaurant->getMedia('photos')->count() }} fotos |
                                            @endif
                                            @if($restaurant->average_rating)
                                                {{ number_format($restaurant->average_rating, 1) }} rating |
                                            @endif
                                            {{ $restaurant->city }}, {{ $restaurant->state->code ?? '' }}
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <!-- Benefits -->
                            <h3 style="color: #059669; margin: 30px 0 15px 0; font-size: 18px;">
                                Al reclamar tu perfil GRATIS obtienes:
                            </h3>
                            <ul style="color: #4b5563; font-size: 15px; line-height: 2; padding-left: 20px; margin: 0;">
                                <li>Insignia de <strong>Restaurante Verificado</strong></li>
                                <li>Control total sobre tu informacion</li>
                                <li>Responder a resenas de clientes</li>
                                <li>Actualizar horarios y contacto</li>
                                <li>Subir hasta 5 fotos de tu negocio</li>
                            </ul>

                            <!-- CTA Button -->
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="margin: 30px 0;">
                                <tr>
                                    <td align="center">
                                        <a href="{{ $claimUrl }}" style="display: inline-block; background: linear-gradient(135deg, #059669 0%, #10b981 100%); color: #ffffff; text-decoration: none; padding: 16px 40px; border-radius: 8px; font-size: 18px; font-weight: bold; box-shadow: 0 4px 6px rgba(5, 150, 105, 0.3);">
                                            RECLAMAR MI RESTAURANTE GRATIS
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <!-- Premium Upgrade Section -->
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background: linear-gradient(135deg, #7c3aed 0%, #a855f7 100%); border-radius: 8px; margin: 30px 0;">
                                <tr>
                                    <td style="padding: 25px; text-align: center;">
                                        <h3 style="color: #ffffff; margin: 0 0 10px 0; font-size: 18px;">
                                            Quieres destacar sobre la competencia?
                                        </h3>
                                        <p style="color: rgba(255,255,255,0.9); margin: 0 0 15px 0; font-size: 14px;">
                                            Actualiza a <strong>Premium</strong> por solo $29/mes y obtiene:
                                        </p>
                                        <p style="color: #fcd34d; margin: 0; font-size: 13px;">
                                            Insignia Premium | Analytics | Menu Digital + QR | Chatbot IA | Reservas Online
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <!-- Why Us -->
                            <div style="border-top: 1px solid #e5e7eb; padding-top: 25px; margin-top: 25px;">
                                <h4 style="color: #6b7280; margin: 0 0 10px 0; font-size: 14px; text-transform: uppercase;">
                                    Por que elegirnos?
                                </h4>
                                <p style="color: #4b5563; font-size: 14px; line-height: 1.6; margin: 0;">
                                    Somos el unico directorio especializado 100% en restaurantes mexicanos.
                                    Combinamos datos de Yelp + Google para ofrecer perfiles mas completos que cualquier competidor.
                                    Ya tenemos mas de <strong>6,000 restaurantes</strong> y creciendo diariamente.
                                </p>
                            </div>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #1f2937; padding: 25px 40px; border-radius: 0 0 12px 12px; text-align: center;">
                            <p style="color: #9ca3af; margin: 0 0 10px 0; font-size: 13px;">
                                Este email fue enviado a restaurantes listados en nuestro directorio.
                            </p>
                            <p style="color: #6b7280; margin: 0; font-size: 12px;">
                                <a href="{{ config('app.url') }}" style="color: #10b981; text-decoration: none;">restaurantesmexicanosfamosos.com</a>
                                |
                                <a href="{{ config('app.url') }}/unsubscribe?email={{ urlencode($restaurant->owner_email ?? '') }}" style="color: #6b7280; text-decoration: none;">Cancelar suscripcion</a>
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
