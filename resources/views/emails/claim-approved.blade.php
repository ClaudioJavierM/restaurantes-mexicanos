<!DOCTYPE html>
<html lang="es" xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="format-detection" content="telephone=no">
    <title>Tu restaurante fue aprobado — FAMER</title>
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

                        <!-- Headline -->
                        <h1 style="margin:0 0 8px; font-size:26px; font-weight:700; color:#111827; text-align:center; font-family:'Segoe UI',Arial,sans-serif;">
                            Tu restaurante fue aprobado
                        </h1>
                        <p style="margin:0 0 28px; font-size:15px; color:#6B7280; text-align:center; line-height:1.6;">
                            Hola {{ $ownerName }},
                        </p>

                        <p style="margin:0 0 20px; font-size:15px; color:#374151; line-height:1.7;">
                            El claim de <strong style="color:#111827;">{{ $restaurant->name }}</strong> fue verificado y aprobado. Tu perfil ahora cuenta con la insignia de <strong style="color:#D4AF37;">Restaurante Verificado</strong> y tiene mayor visibilidad en el directorio.
                        </p>

                        <!-- Botón principal -->
                        <div style="text-align:center; margin-bottom:32px;">
                            <a href="{{ $dashboardUrl ?? (config('app.url') . '/owner') }}"
                               style="display:inline-block; background-color:#D4AF37; color:#0B0B0B; text-decoration:none; font-weight:700; font-size:16px; padding:16px 40px; border-radius:10px; font-family:'Segoe UI',Arial,sans-serif; letter-spacing:0.3px;">
                                Ir a Mi Panel
                            </a>
                        </div>

                        <!-- Divider -->
                        <hr style="border:none; border-top:1px solid #F3F4F6; margin:0 0 24px;">

                        <!-- Próximos pasos -->
                        <p style="margin:0 0 16px; font-size:15px; font-weight:700; color:#111827;">Primeros pasos recomendados:</p>

                        <!-- Paso 1 -->
                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="margin-bottom:12px;">
                            <tr>
                                <td width="36" valign="top" style="padding-top:1px;">
                                    <div style="width:28px; height:28px; background-color:#D4AF37; border-radius:50%; text-align:center; line-height:28px; font-size:13px; font-weight:700; color:#0B0B0B;">1</div>
                                </td>
                                <td style="padding-left:12px;">
                                    <p style="margin:0; font-size:14px; color:#374151; line-height:1.6;">
                                        <strong style="color:#111827;">Completa tu perfil</strong> — agrega descripción, horarios y datos de contacto actualizados.
                                    </p>
                                </td>
                            </tr>
                        </table>

                        <!-- Paso 2 -->
                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="margin-bottom:12px;">
                            <tr>
                                <td width="36" valign="top" style="padding-top:1px;">
                                    <div style="width:28px; height:28px; background-color:#D4AF37; border-radius:50%; text-align:center; line-height:28px; font-size:13px; font-weight:700; color:#0B0B0B;">2</div>
                                </td>
                                <td style="padding-left:12px;">
                                    <p style="margin:0; font-size:14px; color:#374151; line-height:1.6;">
                                        <strong style="color:#111827;">Sube fotos</strong> — los perfiles con imágenes reciben hasta 3 veces más visitas.
                                    </p>
                                </td>
                            </tr>
                        </table>

                        <!-- Paso 3 -->
                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="margin-bottom:24px;">
                            <tr>
                                <td width="36" valign="top" style="padding-top:1px;">
                                    <div style="width:28px; height:28px; background-color:#D4AF37; border-radius:50%; text-align:center; line-height:28px; font-size:13px; font-weight:700; color:#0B0B0B;">3</div>
                                </td>
                                <td style="padding-left:12px;">
                                    <p style="margin:0; font-size:14px; color:#374151; line-height:1.6;">
                                        <strong style="color:#111827;">Responde reseñas</strong> — muestra a los clientes que hay alguien detrás del restaurante.
                                    </p>
                                </td>
                            </tr>
                        </table>

                        <p style="margin:0; font-size:13px; color:#9CA3AF; line-height:1.7; text-align:center;">
                            ¿Necesitas ayuda? Escríbenos a
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
                            Este es un correo automático relacionado con tu cuenta de propietario.
                        </p>
                    </td>
                </tr>

            </table>
        </td>
    </tr>
</table>

</body>
</html>
