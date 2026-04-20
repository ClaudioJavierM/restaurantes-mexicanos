<!DOCTYPE html>
<html lang="es" xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="format-detection" content="telephone=no">
    <title>Su restaurante ya está en el directorio — FAMER</title>
</head>
<body style="margin:0; padding:0; background-color:#0B0B0B; font-family:'Segoe UI',Arial,Helvetica,sans-serif; -webkit-text-size-adjust:100%; -ms-text-size-adjust:100%;">

<table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color:#0B0B0B;">
    <tr>
        <td align="center" style="padding:40px 16px;">

            <table role="presentation" width="100%" style="max-width:580px;" cellspacing="0" cellpadding="0" border="0">

                <!-- HEADER -->
                <tr>
                    <td style="background-color:#0B0B0B; border-radius:16px 16px 0 0; padding:28px 40px; text-align:center; border:1px solid #2A2A2A; border-bottom:none;">
                        <img src="https://restaurantesmexicanosfamosos.com.mx/images/branding/logo-horizontal.png"
                             alt="FAMER" width="160" style="max-width:160px; height:auto; display:block; margin:0 auto 12px;">
                        <p style="margin:0; color:#D4AF37; font-size:11px; font-weight:700; letter-spacing:3px; text-transform:uppercase;">RESTAURANTES MEXICANOS FAMOSOS</p>
                    </td>
                </tr>

                <!-- SEPARADOR DORADO -->
                <tr>
                    <td style="background:linear-gradient(90deg,#D4AF37,#F0D060,#D4AF37); height:3px; font-size:0; line-height:0;">&nbsp;</td>
                </tr>

                <!-- CUERPO -->
                <tr>
                    <td style="background-color:#1A1A1A; padding:48px 40px 40px; border:1px solid #2A2A2A; border-top:none; border-bottom:none;">

                        <!-- Saludo y nombre del restaurante -->
                        <h1 style="margin:0 0 6px; font-size:24px; font-weight:700; color:#F5F5F5; text-align:center; font-family:'Segoe UI',Arial,sans-serif;">
                            {{ $restaurant->name }}
                        </h1>
                        <p style="margin:0 0 28px; font-size:15px; color:#9CA3AF; text-align:center; line-height:1.6;">
                            Su restaurante ya aparece en el directorio más grande de cocina mexicana en Estados Unidos y México.
                        </p>

                        <!-- Box dorado con datos del restaurante -->
                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0"
                               style="background-color:#0B0B0B; border:1px solid #D4AF37; border-radius:10px; margin:0 0 28px;">
                            <tr>
                                <td style="padding:20px 24px; text-align:center;">
                                    @if($restaurant->average_rating)
                                    <p style="margin:0 0 6px; font-size:22px; font-weight:700; color:#F5F5F5;">
                                        {{ number_format($restaurant->average_rating, 1) }}
                                        <span style="font-size:14px; color:#D4AF37; font-weight:400;">/ 5.0 calificación</span>
                                    </p>
                                    @endif
                                    <p style="margin:0; font-size:14px; color:#9CA3AF;">
                                        {{ $restaurant->city }}@if($restaurant->state?->code), {{ $restaurant->state->code }}@endif — México
                                    </p>
                                </td>
                            </tr>
                        </table>

                        <!-- Mensaje principal -->
                        <p style="margin:0 0 10px; font-size:15px; color:#D1D5DB; line-height:1.7;">
                            Estimado propietario,
                        </p>
                        <p style="margin:0 0 24px; font-size:15px; color:#D1D5DB; line-height:1.7;">
                            Su establecimiento fue incluido en <strong style="color:#F5F5F5;">FAMER</strong>, el directorio de referencia para quienes buscan auténtica cocina mexicana desde Estados Unidos. Miles de viajeros y clientes potenciales ya pueden encontrar su restaurante en nuestro sitio.
                        </p>
                        <p style="margin:0 0 24px; font-size:15px; color:#D1D5DB; line-height:1.7;">
                            Reclamar su perfil es <strong style="color:#D4AF37;">completamente gratuito</strong> y le da control total sobre la información que los clientes ven.
                        </p>

                        <!-- Lista de beneficios -->
                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0"
                               style="background-color:#0B0B0B; border-radius:10px; margin:0 0 32px; padding:0;">
                            <tr>
                                <td style="padding:24px;">
                                    <p style="margin:0 0 16px; font-size:14px; font-weight:700; color:#D4AF37; text-transform:uppercase; letter-spacing:1px;">Al reclamar su perfil obtiene:</p>
                                    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                        <tr>
                                            <td style="padding:7px 0; font-size:14px; color:#D1D5DB; border-bottom:1px solid #2A2A2A;">
                                                <span style="color:#D4AF37; font-weight:700; margin-right:10px;">&#10003;</span>Panel de propietario: estadísticas y visitas en tiempo real
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="padding:7px 0; font-size:14px; color:#D1D5DB; border-bottom:1px solid #2A2A2A;">
                                                <span style="color:#D4AF37; font-weight:700; margin-right:10px;">&#10003;</span>Insignia de Restaurante Verificado en su perfil
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="padding:7px 0; font-size:14px; color:#D1D5DB; border-bottom:1px solid #2A2A2A;">
                                                <span style="color:#D4AF37; font-weight:700; margin-right:10px;">&#10003;</span>Responder reseñas de clientes
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="padding:7px 0; font-size:14px; color:#D1D5DB; border-bottom:1px solid #2A2A2A;">
                                                <span style="color:#D4AF37; font-weight:700; margin-right:10px;">&#10003;</span>Actualizar horarios, menú y datos de contacto
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="padding:7px 0; font-size:14px; color:#D1D5DB;">
                                                <span style="color:#D4AF37; font-weight:700; margin-right:10px;">&#10003;</span>Atraer clientes de Estados Unidos y de todo México
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>

                        <!-- Botón principal -->
                        <div style="text-align:center; margin-bottom:32px;">
                            <a href="{{ $claimUrl }}"
                               style="display:inline-block; background-color:#D4AF37; color:#0B0B0B; text-decoration:none; font-weight:700; font-size:16px; padding:18px 44px; border-radius:10px; font-family:'Segoe UI',Arial,sans-serif; letter-spacing:0.3px;">
                                Reclamar Mi Restaurante
                            </a>
                        </div>

                        <!-- Nota de confianza -->
                        <p style="margin:0; font-size:13px; color:#6B7280; text-align:center; line-height:1.6;">
                            El proceso toma menos de 5 minutos. No se requiere tarjeta de crédito.<br>
                            Si tiene preguntas, escríbanos a <a href="mailto:hola@restaurantesmexicanosfamosos.com" style="color:#D4AF37; text-decoration:none;">hola@restaurantesmexicanosfamosos.com</a>
                        </p>

                    </td>
                </tr>

                <!-- FOOTER -->
                <tr>
                    <td style="background-color:#0B0B0B; border-radius:0 0 16px 16px; padding:24px 40px; text-align:center; border:1px solid #2A2A2A; border-top:1px solid #2A2A2A;">
                        <p style="margin:0 0 8px; font-size:12px; color:#6B7280; text-align:center;">
                            © {{ date('Y') }} FAMER — Restaurantes Mexicanos Famosos<br>
                            El directorio de referencia para la cocina mexicana
                        </p>
                        <p style="margin:0; font-size:11px; color:#4B5563; text-align:center;">
                            Recibió este mensaje porque su restaurante aparece en nuestro directorio.<br>
                            <a href="{{ config('app.url') }}/unsubscribe?email={{ urlencode($restaurant->email ?? '') }}"
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
