<!DOCTYPE html>
<html lang="es" xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="format-detection" content="telephone=no">
    <title>¡Bienvenido a FAMER! Tu restaurante está reclamado</title>
</head>
<body style="margin:0; padding:0; background-color:#F5F0E8; font-family:'Segoe UI',Arial,Helvetica,sans-serif; -webkit-text-size-adjust:100%; -ms-text-size-adjust:100%;">

<table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color:#F5F0E8;">
    <tr>
        <td align="center" style="padding:40px 16px;">

            <table role="presentation" width="100%" style="max-width:600px;" cellspacing="0" cellpadding="0" border="0">

                <!-- HEADER -->
                <tr>
                    <td style="background-color:#0B0B0B; border-radius:16px 16px 0 0; padding:28px 40px; text-align:center;">
                        <img src="https://restaurantesmexicanosfamosos.com.mx/images/branding/logo-horizontal.png"
                             alt="FAMER" width="160"
                             style="max-width:160px; height:auto; display:block; margin:0 auto 12px;">
                        <p style="margin:0; color:#D4AF37; font-size:11px; font-weight:700; letter-spacing:3px; text-transform:uppercase;">FAMOUS MEXICAN RESTAURANTS</p>
                    </td>
                </tr>

                <!-- BARRA DORADA -->
                <tr>
                    <td style="background:linear-gradient(90deg,#D4AF37,#F0D060,#D4AF37); height:3px; font-size:0; line-height:0;">&nbsp;</td>
                </tr>

                <!-- CUERPO -->
                <tr>
                    <td style="background-color:#FFFFFF; padding:48px 40px 40px;">

                        <!-- Saludo -->
                        <h1 style="margin:0 0 8px; font-size:26px; font-weight:700; color:#111827; text-align:center; font-family:'Segoe UI',Arial,sans-serif;">
                            ¡Felicidades, {{ $ownerName }}!
                        </h1>
                        <p style="margin:0 0 24px; font-size:15px; color:#6B7280; text-align:center; line-height:1.6;">
                            Tu restaurante <strong style="color:#111827;">{{ $restaurant->name }}</strong> ha sido reclamado exitosamente en FAMER.
                        </p>

                        <!-- Mensaje de bienvenida -->
                        <p style="margin:0 0 28px; font-size:15px; color:#374151; line-height:1.7;">
                            Ya eres parte del directorio más completo de restaurantes mexicanos auténticos. Desde tu panel podrás gestionar tu perfil, responder reseñas, subir fotos y llegar a más comensales.
                        </p>

                        <!-- CUPÓN -->
                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0"
                               style="margin-bottom:32px; border:2px solid #D4AF37; border-radius:12px; background-color:#FFFBF0;">
                            <tr>
                                <td style="padding:24px 28px; text-align:center;">
                                    <p style="margin:0 0 6px; font-size:12px; font-weight:700; color:#D4AF37; letter-spacing:2px; text-transform:uppercase;">Oferta especial de bienvenida</p>
                                    <p style="margin:0 0 10px; font-size:36px; font-weight:700; color:#111827; letter-spacing:6px; font-family:'Courier New',Courier,monospace;">{{ $couponCode }}</p>
                                    <p style="margin:0 0 4px; font-size:16px; font-weight:600; color:#1F3D2B;">30% OFF tu primer mes Premium</p>
                                    <p style="margin:0; font-size:13px; color:#9CA3AF;">
                                        ⏰ &nbsp;Válido por <strong style="color:#8B1E1E;">24 horas</strong> — úsalo al actualizar tu plan
                                    </p>
                                </td>
                            </tr>
                        </table>

                        <!-- CTA PRINCIPAL -->
                        <div style="text-align:center; margin-bottom:36px;">
                            <a href="https://restaurantesmexicanosfamosos.com/owner"
                               style="display:inline-block; background-color:#22C55E; color:#FFFFFF; text-decoration:none; font-weight:700; font-size:16px; padding:16px 44px; border-radius:10px; font-family:'Segoe UI',Arial,sans-serif; letter-spacing:0.3px;">
                                Ir a mi Dashboard &nbsp;→
                            </a>
                        </div>

                        <!-- Divider -->
                        <hr style="border:none; border-top:1px solid #F3F4F6; margin:0 0 28px;">

                        <!-- Próximos pasos -->
                        <p style="margin:0 0 16px; font-size:15px; font-weight:700; color:#111827;">Tus primeros 3 pasos:</p>

                        <!-- Paso 1 -->
                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="margin-bottom:14px;">
                            <tr>
                                <td width="36" valign="top" style="padding-top:1px;">
                                    <div style="width:30px; height:30px; background-color:#D4AF37; border-radius:50%; text-align:center; line-height:30px; font-size:15px; color:#0B0B0B;">📸</div>
                                </td>
                                <td style="padding-left:14px;">
                                    <p style="margin:0; font-size:14px; color:#374151; line-height:1.6;">
                                        <strong style="color:#111827;">Sube fotos de tu restaurante</strong> — los perfiles con imágenes reciben 3× más visitas y generan mayor confianza.
                                    </p>
                                </td>
                            </tr>
                        </table>

                        <!-- Paso 2 -->
                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="margin-bottom:14px;">
                            <tr>
                                <td width="36" valign="top" style="padding-top:1px;">
                                    <div style="width:30px; height:30px; background-color:#D4AF37; border-radius:50%; text-align:center; line-height:30px; font-size:15px; color:#0B0B0B;">✍️</div>
                                </td>
                                <td style="padding-left:14px;">
                                    <p style="margin:0; font-size:14px; color:#374151; line-height:1.6;">
                                        <strong style="color:#111827;">Completa tu perfil</strong> — agrega descripción, horarios, menú y datos de contacto para aparecer en más búsquedas.
                                    </p>
                                </td>
                            </tr>
                        </table>

                        <!-- Paso 3 -->
                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="margin-bottom:28px;">
                            <tr>
                                <td width="36" valign="top" style="padding-top:1px;">
                                    <div style="width:30px; height:30px; background-color:#D4AF37; border-radius:50%; text-align:center; line-height:30px; font-size:15px; color:#0B0B0B;">⭐</div>
                                </td>
                                <td style="padding-left:14px;">
                                    <p style="margin:0; font-size:14px; color:#374151; line-height:1.6;">
                                        <strong style="color:#111827;">Responde tus reseñas</strong> — demuestra que hay personas reales detrás de tu negocio y mejora tu FAMER Score.
                                    </p>
                                </td>
                            </tr>
                        </table>

                        <!-- Soporte -->
                        <p style="margin:0; font-size:13px; color:#9CA3AF; line-height:1.7; text-align:center;">
                            ¿Tienes preguntas? Escríbenos a
                            <a href="mailto:soporte@restaurantesmexicanosfamosos.com"
                               style="color:#D4AF37; text-decoration:none;">soporte@restaurantesmexicanosfamosos.com</a>
                        </p>

                    </td>
                </tr>

                <!-- FOOTER -->
                <tr>
                    <td style="background-color:#F9FAFB; border-radius:0 0 16px 16px; padding:24px 40px; text-align:center; border-top:1px solid #F3F4F6;">
                        <p style="margin:0 0 8px; font-size:12px; color:#9CA3AF;">
                            © {{ date('Y') }} FAMER — Restaurantes Mexicanos Famosos. Todos los derechos reservados.
                        </p>
                        <p style="margin:0; font-size:11px; color:#D1D5DB;">
                            Recibiste este correo porque reclamaste tu restaurante en FAMER.
                            <a href="https://restaurantesmexicanosfamosos.com/email/unsubscribe?email={{ urlencode($ownerEmail) }}"
                               style="color:#9CA3AF; text-decoration:underline;">Cancelar suscripción</a>
                        </p>
                    </td>
                </tr>

            </table>
        </td>
    </tr>
</table>

</body>
</html>
