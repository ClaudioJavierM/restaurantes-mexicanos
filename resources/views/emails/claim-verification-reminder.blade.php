<!DOCTYPE html>
<html lang="es" xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="format-detection" content="telephone=no">
    <title>Completa la verificación de tu restaurante — FAMER</title>
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

                        <!-- Headline según número de reminder -->
                        <h1 style="margin:0 0 8px; font-size:24px; font-weight:700; color:#111827; text-align:center; font-family:'Segoe UI',Arial,sans-serif;">
                            @if(isset($reminderNumber) && $reminderNumber >= 2)
                                Un paso más para completar tu registro
                            @else
                                Falta completar la verificación
                            @endif
                        </h1>
                        <p style="margin:0 0 28px; font-size:15px; color:#6B7280; text-align:center; line-height:1.6;">
                            Hola {{ $claim->owner_name }},
                        </p>

                        <p style="margin:0 0 20px; font-size:15px; color:#374151; line-height:1.7;">
                            Iniciaste el proceso para reclamar <strong style="color:#111827;">{{ $claim->restaurant->name }}</strong>, pero aún no completaste la verificación de identidad.
                        </p>

                        <!-- Box de código de verificación -->
                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0"
                               style="background-color:#FBF6E9; border:1px solid #D4AF37; border-radius:10px; margin:0 0 24px;">
                            <tr>
                                <td style="padding:20px 24px;">
                                    <p style="margin:0 0 8px; font-size:14px; color:#374151; line-height:1.6;">
                                        Te enviamos un código de 6 dígitos a tu
                                        <strong>{{ $claim->verification_method === 'email' ? 'correo electrónico' : 'teléfono' }}</strong>.
                                        Ingrésalo para activar tu perfil de propietario.
                                    </p>
                                    @if(isset($reminderNumber) && $reminderNumber >= 2)
                                    <p style="margin:8px 0 0; font-size:13px; color:#9CA3AF; line-height:1.5;">
                                        Tu código de verificación vence pronto. Completa el proceso antes de que expire.
                                    </p>
                                    @endif
                                </td>
                            </tr>
                        </table>

                        <!-- Botón principal -->
                        <div style="text-align:center; margin-bottom:32px;">
                            <a href="{{ $verificationUrl ?? $claimUrl }}"
                               style="display:inline-block; background-color:#D4AF37; color:#0B0B0B; text-decoration:none; font-weight:700; font-size:16px; padding:16px 40px; border-radius:10px; font-family:'Segoe UI',Arial,sans-serif; letter-spacing:0.3px;">
                                Completar Verificación
                            </a>
                        </div>

                        <!-- Qué pasa al verificar -->
                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0"
                               style="background-color:#F9FAFB; border-radius:10px; margin-bottom:24px;">
                            <tr>
                                <td style="padding:20px 24px;">
                                    <p style="margin:0 0 12px; font-size:14px; font-weight:700; color:#111827;">Al verificar obtienes acceso a:</p>
                                    <table role="presentation" cellspacing="0" cellpadding="0" border="0">
                                        <tr>
                                            <td style="padding:4px 0; font-size:14px; color:#374151;">
                                                <span style="color:#D4AF37; font-weight:700; margin-right:8px;">&#10003;</span>Panel de propietario de {{ $claim->restaurant->name }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="padding:4px 0; font-size:14px; color:#374151;">
                                                <span style="color:#D4AF37; font-weight:700; margin-right:8px;">&#10003;</span>Insignia de Restaurante Verificado
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="padding:4px 0; font-size:14px; color:#374151;">
                                                <span style="color:#D4AF37; font-weight:700; margin-right:8px;">&#10003;</span>Control total de tu información y fotos
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="padding:4px 0; font-size:14px; color:#374151;">
                                                <span style="color:#D4AF37; font-weight:700; margin-right:8px;">&#10003;</span>Responder reseñas de clientes
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>

                        <!-- Divider -->
                        <hr style="border:none; border-top:1px solid #F3F4F6; margin:0 0 20px;">

                        <p style="margin:0; font-size:13px; color:#9CA3AF; line-height:1.7; text-align:center;">
                            Si no iniciaste este proceso, puedes ignorar este mensaje — tu restaurante no se verá afectado.
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
                            Este es un correo automático relacionado con tu proceso de verificación.
                        </p>
                    </td>
                </tr>

            </table>
        </td>
    </tr>
</table>

</body>
</html>
