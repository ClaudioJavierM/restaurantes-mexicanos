<!DOCTYPE html>
<html lang="es" xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="format-detection" content="telephone=no">
    <title>Tu restaurante ya está en el directorio — FAMER</title>
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

                        <!-- Nombre del restaurante -->
                        <h1 style="margin:0 0 6px; font-size:24px; font-weight:700; color:#111827; text-align:center; font-family:'Segoe UI',Arial,sans-serif;">
                            {{ $restaurant->name }}
                        </h1>
                        <p style="margin:0 0 28px; font-size:15px; color:#6B7280; text-align:center; line-height:1.6;">
                            Tu restaurante ya aparece en el directorio más grande de comida mexicana en Estados Unidos.
                        </p>

                        <!-- Box dorado con datos del restaurante -->
                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0"
                               style="background-color:#FBF6E9; border:1px solid #D4AF37; border-radius:10px; margin:0 0 28px;">
                            <tr>
                                <td style="padding:20px 24px; text-align:center;">
                                    @if($restaurant->average_rating)
                                    <p style="margin:0 0 6px; font-size:22px; font-weight:700; color:#0B0B0B;">
                                        {{ number_format($restaurant->average_rating, 1) }}
                                        <span style="font-size:14px; color:#D4AF37; font-weight:400;">/ 5.0 rating</span>
                                    </p>
                                    @endif
                                    <p style="margin:0; font-size:14px; color:#6B7280;">
                                        {{ $restaurant->city }}, {{ $restaurant->state->code ?? '' }}
                                    </p>
                                </td>
                            </tr>
                        </table>

                        <!-- Mensaje principal -->
                        <p style="margin:0 0 24px; font-size:15px; color:#374151; line-height:1.7;">
                            Tu perfil fue creado con información de Yelp y Google. Al reclamar tu restaurante, tomas el control total de cómo apareces ante los 26,000+ visitantes mensuales que buscan comida mexicana en nuestro directorio.
                        </p>

                        <!-- Lista de beneficios -->
                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="margin:0 0 28px;">
                            <tr>
                                <td>
                                    <p style="margin:0 0 12px; font-size:15px; font-weight:700; color:#111827;">Al reclamar tu perfil obtienes:</p>
                                    <table role="presentation" cellspacing="0" cellpadding="0" border="0">
                                        <tr>
                                            <td style="padding:5px 0; font-size:14px; color:#374151;">
                                                <span style="color:#D4AF37; font-weight:700; margin-right:8px;">&#10003;</span>Insignia de Restaurante Verificado
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="padding:5px 0; font-size:14px; color:#374151;">
                                                <span style="color:#D4AF37; font-weight:700; margin-right:8px;">&#10003;</span>Control total sobre la información de tu perfil
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="padding:5px 0; font-size:14px; color:#374151;">
                                                <span style="color:#D4AF37; font-weight:700; margin-right:8px;">&#10003;</span>Responder reseñas de clientes
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="padding:5px 0; font-size:14px; color:#374151;">
                                                <span style="color:#D4AF37; font-weight:700; margin-right:8px;">&#10003;</span>Actualizar horarios y datos de contacto
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="padding:5px 0; font-size:14px; color:#374151;">
                                                <span style="color:#D4AF37; font-weight:700; margin-right:8px;">&#10003;</span>Subir fotos de tu negocio y platillos
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>

                        <!-- Botón principal -->
                        <div style="text-align:center; margin-bottom:32px;">
                            <a href="{{ $claimUrl }}"
                               style="display:inline-block; background-color:#D4AF37; color:#0B0B0B; text-decoration:none; font-weight:700; font-size:16px; padding:16px 40px; border-radius:10px; font-family:'Segoe UI',Arial,sans-serif; letter-spacing:0.3px;">
                                Reclamar Mi Restaurante
                            </a>
                        </div>

                        <!-- Sección Premium -->
                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0"
                               style="background-color:#F9FAFB; border-radius:10px;">
                            <tr>
                                <td style="padding:20px 24px;">
                                    <p style="margin:0 0 8px; font-size:14px; font-weight:700; color:#111827;">¿Quieres destacar sobre la competencia?</p>
                                    <p style="margin:0 0 10px; font-size:13px; color:#6B7280; line-height:1.6;">
                                        Con el plan Premium ($29/mes) obtienes Analytics avanzados, Menú digital con código QR y sistema de Reservas online para tu restaurante.
                                    </p>
                                    <p style="margin:0; font-size:12px; color:#9CA3AF;">
                                        Puedes activarlo desde tu panel después de reclamar tu perfil.
                                    </p>
                                </td>
                            </tr>
                        </table>

                    </td>
                </tr>

                <!-- FOOTER -->
                <tr>
                    <td style="background-color:#F9FAFB; border-radius:0 0 16px 16px; padding:24px 40px; text-align:center; border-top:1px solid #F3F4F6;">
                        <p style="margin:0 0 8px; font-size:12px; color:#9CA3AF; text-align:center;">
                            © {{ date('Y') }} FAMER — Restaurantes Mexicanos Famosos<br>
                            El directorio líder de restaurantes mexicanos en Estados Unidos
                        </p>
                        <p style="margin:0; font-size:11px; color:#D1D5DB; text-align:center;">
                            Recibiste este mensaje porque tu restaurante aparece en nuestro directorio.<br>
                            <a href="{{ config('app.url') }}/unsubscribe?email={{ urlencode($restaurant->owner_email ?? '') }}"
                               style="color:#D4AF37; text-decoration:none;">Cancelar notificaciones</a>
                        </p>
                    </td>
                </tr>

            </table>
        </td>
    </tr>
</table>

</body>
</html>
