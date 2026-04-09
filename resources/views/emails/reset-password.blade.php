<!DOCTYPE html>
<html lang="es" xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Recupera tu contraseña — FAMER</title>
</head>
<body style="margin:0; padding:0; background-color:#F5F0E8; font-family:'Segoe UI',Arial,Helvetica,sans-serif; -webkit-text-size-adjust:100%; -ms-text-size-adjust:100%;">

<table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color:#F5F0E8;">
    <tr>
        <td align="center" style="padding:40px 16px;">

            <!-- Email container -->
            <table role="presentation" width="100%" style="max-width:580px;" cellspacing="0" cellpadding="0" border="0">

                <!-- ── HEADER ── -->
                <tr>
                    <td style="background-color:#0B0B0B; border-radius:16px 16px 0 0; padding:32px 40px; text-align:center;">
                        <img src="https://restaurantesmexicanosfamosos.com/images/branding/logo-horizontal.png"
                             alt="FAMER" width="140" style="max-width:140px; height:auto; display:block; margin:0 auto 16px;">
                        <p style="margin:0; color:#D4AF37; font-size:11px; font-weight:700; letter-spacing:3px; text-transform:uppercase;">
                            FAMOUS MEXICAN RESTAURANTS
                        </p>
                    </td>
                </tr>

                <!-- ── GOLD DIVIDER ── -->
                <tr>
                    <td style="background:linear-gradient(90deg, #D4AF37, #F0D060, #D4AF37); height:3px; font-size:0; line-height:0;">&nbsp;</td>
                </tr>

                <!-- ── BODY ── -->
                <tr>
                    <td style="background-color:#FFFFFF; padding:48px 40px 40px;">

                        <!-- Icon -->
                        <div style="text-align:center; margin-bottom:28px;">
                            <div style="display:inline-block; background:#FBF6E9; border:2px solid #D4AF37; border-radius:50%; width:72px; height:72px; line-height:72px; text-align:center; font-size:32px;">
                                🔐
                            </div>
                        </div>

                        <!-- Greeting -->
                        <h1 style="margin:0 0 8px; font-size:26px; font-weight:700; color:#111827; text-align:center; font-family:'Segoe UI',Arial,sans-serif;">
                            Recupera tu contraseña
                        </h1>
                        <p style="margin:0 0 28px; font-size:15px; color:#6B7280; text-align:center; line-height:1.6;">
                            Hola {{ $name }}, recibimos una solicitud para restablecer la contraseña de tu cuenta en FAMER.
                        </p>

                        <!-- CTA Button -->
                        <div style="text-align:center; margin-bottom:32px;">
                            <a href="{{ $url }}"
                               style="display:inline-block; background-color:#D4AF37; color:#0B0B0B; text-decoration:none; font-weight:700; font-size:16px; padding:16px 40px; border-radius:10px; font-family:'Segoe UI',Arial,sans-serif; letter-spacing:0.3px;">
                                Restablecer contraseña
                            </a>
                        </div>

                        <!-- Expiry notice -->
                        <p style="margin:0 0 24px; font-size:13px; color:#9CA3AF; text-align:center; line-height:1.6;">
                            Este enlace expira en <strong style="color:#6B7280;">{{ $expireIn }} minutos</strong>.
                        </p>

                        <!-- Divider -->
                        <hr style="border:none; border-top:1px solid #F3F4F6; margin:0 0 24px;">

                        <!-- Security notice -->
                        <p style="margin:0 0 16px; font-size:13px; color:#9CA3AF; line-height:1.7;">
                            Si no solicitaste restablecer tu contraseña, puedes ignorar este correo — tu cuenta está segura y no se realizará ningún cambio.
                        </p>

                        <!-- Fallback URL -->
                        <p style="margin:0; font-size:12px; color:#D1D5DB; line-height:1.6;">
                            Si el botón no funciona, copia y pega este enlace en tu navegador:<br>
                            <a href="{{ $url }}" style="color:#D4AF37; word-break:break-all; font-size:11px; text-decoration:none;">{{ $url }}</a>
                        </p>

                    </td>
                </tr>

                <!-- ── FOOTER ── -->
                <tr>
                    <td style="background-color:#F9FAFB; border-radius:0 0 16px 16px; padding:24px 40px; text-align:center; border-top:1px solid #F3F4F6;">
                        <p style="margin:0 0 8px; font-size:12px; color:#9CA3AF;">
                            © {{ date('Y') }} FAMER — Restaurantes Mexicanos Famosos. Todos los derechos reservados.
                        </p>
                        <p style="margin:0; font-size:11px; color:#D1D5DB;">
                            Este es un correo automático, por favor no respondas a este mensaje.
                        </p>
                    </td>
                </tr>

            </table>
        </td>
    </tr>
</table>

</body>
</html>
