<!DOCTYPE html>
<html lang="es" xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="format-detection" content="telephone=no">
    <title>Solicitud recibida — FAMER</title>
</head>
<body style="margin:0; padding:0; background-color:#F5F0E8; font-family:'Segoe UI',Arial,Helvetica,sans-serif; -webkit-text-size-adjust:100%; -ms-text-size-adjust:100%;">

<table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color:#F5F0E8;">
    <tr>
        <td align="center" style="padding:40px 16px;">

            <table role="presentation" width="100%" style="max-width:580px;" cellspacing="0" cellpadding="0" border="0">

                <!-- HEADER -->
                <tr>
                    <td style="background-color:#0B0B0B; border-radius:16px 16px 0 0; padding:28px 40px; text-align:center;">
                        <img src="https://restaurantesmexicanosfamosos.com.mx/images/branding/logo-horizontal.png"
                             alt="FAMER" width="160" style="max-width:160px; height:auto; display:block; margin:0 auto 12px;">
                        <p style="margin:0; color:#D4AF37; font-size:11px; font-weight:700; letter-spacing:3px; text-transform:uppercase;">FAMOUS MEXICAN RESTAURANTS</p>
                    </td>
                </tr>

                <!-- SEPARADOR DORADO -->
                <tr>
                    <td style="background:linear-gradient(90deg,#D4AF37,#F0D060,#D4AF37); height:3px; font-size:0; line-height:0;">&nbsp;</td>
                </tr>

                <!-- CUERPO -->
                <tr>
                    <td style="background-color:#FFFFFF; padding:48px 40px 40px;">

                        <!-- Saludo -->
                        <h1 style="margin:0 0 8px; font-size:24px; font-weight:700; color:#111827; text-align:center; font-family:'Segoe UI',Arial,sans-serif;">
                            Solicitud recibida
                        </h1>
                        <p style="margin:0 0 28px; font-size:15px; color:#6B7280; text-align:center; line-height:1.6;">
                            Hola {{ $ownerName }},
                        </p>

                        <p style="margin:0 0 20px; font-size:15px; color:#374151; line-height:1.7;">
                            Recibimos tu solicitud para reclamar <strong style="color:#111827;">{{ $restaurant->name }}</strong>. Nuestro equipo está verificando la información para completar el proceso.
                        </p>

                        <!-- Status box -->
                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0"
                               style="background-color:#FBF6E9; border:1px solid #D4AF37; border-radius:10px; margin:0 0 24px;">
                            <tr>
                                <td style="padding:20px 24px; text-align:center;">
                                    <p style="margin:0 0 6px; font-size:12px; font-weight:700; color:#9CA3AF; letter-spacing:2px; text-transform:uppercase;">Estado de tu solicitud</p>
                                    <p style="margin:0; font-size:18px; font-weight:700; color:#D4AF37;">
                                        En revisión
                                    </p>
                                </td>
                            </tr>
                        </table>

                        <p style="margin:0 0 20px; font-size:15px; color:#374151; line-height:1.7;">
                            Este proceso normalmente toma entre 24 y 48 horas. Recibirás un correo en cuanto tu perfil esté listo con las instrucciones para acceder a tu panel de propietario.
                        </p>

                        <!-- Qué sigue -->
                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0"
                               style="background-color:#F9FAFB; border-radius:10px; margin:0 0 24px;">
                            <tr>
                                <td style="padding:20px 24px;">
                                    <p style="margin:0 0 12px; font-size:14px; font-weight:700; color:#111827;">Después de la aprobación podrás:</p>
                                    <table role="presentation" cellspacing="0" cellpadding="0" border="0">
                                        <tr>
                                            <td style="padding:4px 0; font-size:14px; color:#374151;">
                                                <span style="color:#D4AF37; font-weight:700; margin-right:8px;">&#10003;</span>Actualizar toda la información de tu restaurante
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="padding:4px 0; font-size:14px; color:#374151;">
                                                <span style="color:#D4AF37; font-weight:700; margin-right:8px;">&#10003;</span>Subir fotos y gestionar tu galería
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="padding:4px 0; font-size:14px; color:#374151;">
                                                <span style="color:#D4AF37; font-weight:700; margin-right:8px;">&#10003;</span>Responder reseñas de clientes
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="padding:4px 0; font-size:14px; color:#374151;">
                                                <span style="color:#D4AF37; font-weight:700; margin-right:8px;">&#10003;</span>Mostrar la insignia de Restaurante Verificado
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>

                        <!-- Divider -->
                        <hr style="border:none; border-top:1px solid #F3F4F6; margin:0 0 20px;">

                        <p style="margin:0; font-size:13px; color:#9CA3AF; line-height:1.7; text-align:center;">
                            Si tienes preguntas sobre tu solicitud, escríbenos a
                            <a href="mailto:soporte@restaurantesmexicanosfamosos.com.mx"
                               style="color:#D4AF37; text-decoration:none;">soporte@restaurantesmexicanosfamosos.com.mx</a>
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
                            Este es un correo automático relacionado con tu solicitud de claim.
                        </p>
                    </td>
                </tr>

            </table>
        </td>
    </tr>
</table>

</body>
</html>
