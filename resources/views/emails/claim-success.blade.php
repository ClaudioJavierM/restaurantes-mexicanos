<!DOCTYPE html>
<html lang="es" xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="format-detection" content="telephone=no">
    <title>Bienvenido a tu panel — FAMER</title>
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
                            Tu cuenta está lista
                        </h1>
                        <p style="margin:0 0 28px; font-size:15px; color:#6B7280; text-align:center; line-height:1.6;">
                            Hola @if(!empty($ownerName)){{ $ownerName }},@else,@endif tu perfil en FAMER está activo y verificado.
                        </p>

                        <!-- Restaurante -->
                        <h2 style="margin:0 0 6px; font-size:18px; font-weight:700; color:#111827; text-align:center;">
                            {{ $restaurant->name }}
                        </h2>
                        <p style="margin:0 0 28px; font-size:14px; color:#D4AF37; text-align:center; font-weight:700; letter-spacing:1px; text-transform:uppercase;">
                            Restaurante Verificado
                        </p>

                        <!-- Credenciales / datos de acceso -->
                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0"
                               style="background-color:#FBF6E9; border:1px solid #D4AF37; border-radius:10px; margin:0 0 28px;">
                            <tr>
                                <td style="padding:20px 24px;">
                                    <p style="margin:0 0 10px; font-size:14px; font-weight:700; color:#111827;">Datos de acceso a tu cuenta:</p>
                                    <p style="margin:0 0 6px; font-size:14px; color:#374151;">
                                        <strong>Email:</strong> {{ $user->email }}
                                    </p>
                                    <p style="margin:0 0 10px; font-size:14px; color:#374151;">
                                        <strong>Contraseña temporal:</strong> {{ $tempPassword }}
                                    </p>
                                    <p style="margin:0; font-size:12px; color:#9CA3AF; line-height:1.5;">
                                        Cambia tu contraseña al iniciar sesión por primera vez.
                                    </p>
                                </td>
                            </tr>
                        </table>

                        <!-- Botón principal -->
                        <div style="text-align:center; margin-bottom:16px;">
                            <a href="{{ $dashboardUrl }}"
                               style="display:inline-block; background-color:#D4AF37; color:#0B0B0B; text-decoration:none; font-weight:700; font-size:16px; padding:16px 40px; border-radius:10px; font-family:'Segoe UI',Arial,sans-serif; letter-spacing:0.3px;">
                                Explorar Mi Panel
                            </a>
                        </div>

                        <!-- Link cambiar contraseña -->
                        <div style="text-align:center; margin-bottom:32px;">
                            <a href="{{ $resetPasswordUrl }}"
                               style="color:#D4AF37; text-decoration:none; font-size:13px;">
                                Cambiar mi contraseña
                            </a>
                        </div>

                        <!-- Divider -->
                        <hr style="border:none; border-top:1px solid #F3F4F6; margin:0 0 24px;">

                        <!-- Qué puedes hacer -->
                        <p style="margin:0 0 16px; font-size:15px; font-weight:700; color:#111827;">Desde tu panel puedes:</p>

                        <table role="presentation" cellspacing="0" cellpadding="0" border="0" style="margin-bottom:24px;">
                            <tr>
                                <td style="padding:5px 0; font-size:14px; color:#374151;">
                                    <span style="color:#D4AF37; font-weight:700; margin-right:8px;">&#10003;</span>Actualizar descripción, horarios y contacto
                                </td>
                            </tr>
                            <tr>
                                <td style="padding:5px 0; font-size:14px; color:#374151;">
                                    <span style="color:#D4AF37; font-weight:700; margin-right:8px;">&#10003;</span>Subir fotos de tus platillos y local
                                </td>
                            </tr>
                            <tr>
                                <td style="padding:5px 0; font-size:14px; color:#374151;">
                                    <span style="color:#D4AF37; font-weight:700; margin-right:8px;">&#10003;</span>Agregar o actualizar tu menú
                                </td>
                            </tr>
                            <tr>
                                <td style="padding:5px 0; font-size:14px; color:#374151;">
                                    <span style="color:#D4AF37; font-weight:700; margin-right:8px;">&#10003;</span>Responder reseñas de clientes
                                </td>
                            </tr>
                            <tr>
                                <td style="padding:5px 0; font-size:14px; color:#374151;">
                                    <span style="color:#D4AF37; font-weight:700; margin-right:8px;">&#10003;</span>Ver estadísticas de visitas a tu perfil
                                </td>
                            </tr>
                        </table>

                        <!-- Plan section si es premium/elite -->
                        @if(isset($plan) && in_array($plan, ['premium', 'elite']))
                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0"
                               style="background-color:#F9FAFB; border-radius:10px; margin-bottom:24px;">
                            <tr>
                                <td style="padding:20px 24px;">
                                    <p style="margin:0 0 10px; font-size:14px; font-weight:700; color:#111827;">
                                        Tu Plan {{ ucfirst($plan) }} también incluye:
                                    </p>
                                    @if($plan === 'elite')
                                    <table role="presentation" cellspacing="0" cellpadding="0" border="0">
                                        <tr><td style="padding:4px 0; font-size:13px; color:#374151;"><span style="color:#D4AF37; font-weight:700; margin-right:8px;">&#10003;</span>Badge Elite — la máxima distinción</td></tr>
                                        <tr><td style="padding:4px 0; font-size:13px; color:#374151;"><span style="color:#D4AF37; font-weight:700; margin-right:8px;">&#10003;</span>Posición prioritaria en búsquedas</td></tr>
                                        <tr><td style="padding:4px 0; font-size:13px; color:#374151;"><span style="color:#D4AF37; font-weight:700; margin-right:8px;">&#10003;</span>Menú digital con código QR</td></tr>
                                        <tr><td style="padding:4px 0; font-size:13px; color:#374151;"><span style="color:#D4AF37; font-weight:700; margin-right:8px;">&#10003;</span>Analytics avanzados y reportes</td></tr>
                                        <tr><td style="padding:4px 0; font-size:13px; color:#374151;"><span style="color:#D4AF37; font-weight:700; margin-right:8px;">&#10003;</span>Account manager dedicado</td></tr>
                                    </table>
                                    @else
                                    <table role="presentation" cellspacing="0" cellpadding="0" border="0">
                                        <tr><td style="padding:4px 0; font-size:13px; color:#374151;"><span style="color:#D4AF37; font-weight:700; margin-right:8px;">&#10003;</span>Badge Premium Verificado</td></tr>
                                        <tr><td style="padding:4px 0; font-size:13px; color:#374151;"><span style="color:#D4AF37; font-weight:700; margin-right:8px;">&#10003;</span>Menú digital con código QR</td></tr>
                                        <tr><td style="padding:4px 0; font-size:13px; color:#374151;"><span style="color:#D4AF37; font-weight:700; margin-right:8px;">&#10003;</span>Sistema de reservaciones</td></tr>
                                        <tr><td style="padding:4px 0; font-size:13px; color:#374151;"><span style="color:#D4AF37; font-weight:700; margin-right:8px;">&#10003;</span>Analytics y estadísticas avanzadas</td></tr>
                                    </table>
                                    @endif
                                </td>
                            </tr>
                        </table>
                        @endif

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
