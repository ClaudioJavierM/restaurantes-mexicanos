<!DOCTYPE html>
<html lang="es" xmlns="http://www.w3.org/1999/xhtml">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="format-detection" content="telephone=no">
  <title>Tu restaurante sigue recibiendo visitas — FAMER</title>
</head>
<body style="margin:0; padding:0; background-color:#F5F0E8; font-family:'Segoe UI',Arial,Helvetica,sans-serif; -webkit-text-size-adjust:100%; -ms-text-size-adjust:100%;">

<table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color:#F5F0E8;">
  <tr><td align="center" style="padding:40px 16px;">

    <table role="presentation" width="100%" style="max-width:580px;" cellspacing="0" cellpadding="0" border="0">

      <!-- HEADER -->
      <tr>
        <td style="background-color:#0B0B0B; border-radius:16px 16px 0 0; padding:28px 40px; text-align:center;">
          <img src="https://restaurantesmexicanosfamosos.com.mx/images/branding/logo-horizontal.png"
               alt="FAMER" width="160" style="max-width:160px; height:auto; display:block; margin:0 auto 12px;">
          <p style="margin:0; color:#D4AF37; font-size:11px; font-weight:700; letter-spacing:3px; text-transform:uppercase;">FAMOUS MEXICAN RESTAURANTS</p>
        </td>
      </tr>

      <!-- SEPARADOR -->
      <tr>
        <td style="background:linear-gradient(90deg,#D4AF37,#F0D060,#D4AF37); height:3px; font-size:0; line-height:0;">&nbsp;</td>
      </tr>

      <!-- CUERPO -->
      <tr>
        <td style="background-color:#FFFFFF; padding:48px 40px 40px;">

          <!-- Saludo -->
          <h1 style="margin:0 0 12px; font-size:24px; font-weight:700; color:#111827; font-family:'Segoe UI',Arial,sans-serif;">
            Te hemos extrañado por aquí
          </h1>
          <p style="margin:0 0 6px; font-size:16px; color:#374151; line-height:1.7;">
            @if(isset($ownerName) && $ownerName)
            Hola {{ $ownerName }},
            @else
            Hola,
            @endif
          </p>
          <p style="margin:0 0 8px; font-size:15px; color:#6B7280; line-height:1.7;">
            Hace
            @isset($daysSinceLogin)
            {{ $daysSinceLogin }} días
            @else
            un tiempo
            @endisset
            que no visitas tu panel de dueño en FAMER. No pasa nada — queremos que sepas que tu restaurante <strong style="color:#111827;">{{ $restaurant->name ?? $restaurantName ?? '' }}</strong> ha seguido activo durante tu ausencia.
          </p>

          <hr style="border:none; border-top:1px solid #F3F4F6; margin:24px 0;">

          <!-- Lo que pasó mientras estabas ausente -->
          <p style="margin:0 0 16px; font-size:13px; font-weight:700; color:#D4AF37; letter-spacing:2px; text-transform:uppercase;">Mientras estuviste ausente</p>

          <!-- Stats de ausencia -->
          <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="margin-bottom:24px;">
            <tr>
              <!-- Vistas -->
              <td width="50%" style="padding:0 6px 0 0;">
                <div style="background-color:#FBF6E9; border-radius:12px; padding:20px; text-align:center;">
                  @isset($stats['views_since_login'])
                  <p style="margin:0 0 4px; font-size:32px; font-weight:800; color:#D4AF37; line-height:1;">{{ number_format($stats['views_since_login']) }}</p>
                  @else
                  <p style="margin:0 0 4px; font-size:32px; font-weight:800; color:#D4AF37; line-height:1;">—</p>
                  @endisset
                  <p style="margin:0; font-size:12px; color:#78540A; font-weight:600; text-transform:uppercase; letter-spacing:0.5px;">Visitas al perfil</p>
                </div>
              </td>
              <!-- Reseñas -->
              <td width="50%" style="padding:0 0 0 6px;">
                <div style="background-color:#FBF6E9; border-radius:12px; padding:20px; text-align:center;">
                  @isset($stats['new_reviews'])
                  <p style="margin:0 0 4px; font-size:32px; font-weight:800; color:#D4AF37; line-height:1;">{{ $stats['new_reviews'] }}</p>
                  @else
                  <p style="margin:0 0 4px; font-size:32px; font-weight:800; color:#D4AF37; line-height:1;">—</p>
                  @endisset
                  <p style="margin:0; font-size:12px; color:#78540A; font-weight:600; text-transform:uppercase; letter-spacing:0.5px;">Reseñas nuevas</p>
                </div>
              </td>
            </tr>
          </table>

          <!-- Mensaje empático -->
          <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="margin-bottom:28px;">
            <tr>
              <td style="background-color:#F9FAFB; border-radius:10px; padding:20px 22px;">
                <p style="margin:0 0 8px; font-size:15px; font-weight:700; color:#111827;">Tu presencia hace la diferencia</p>
                <p style="margin:0; font-size:14px; color:#6B7280; line-height:1.6;">
                  Responder reseñas, actualizar tu menú y revisar tus analytics son acciones que mejoran tu posición en el directorio y generan más visitas de clientes potenciales. Solo toma unos minutos.
                </p>
              </td>
            </tr>
          </table>

          <!-- 3 acciones rápidas -->
          <p style="margin:0 0 14px; font-size:13px; font-weight:700; color:#D4AF37; letter-spacing:2px; text-transform:uppercase;">3 cosas que puedes hacer hoy</p>

          <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="margin-bottom:32px;">
            @foreach([['Revisar las reseñas recientes','Responder muestra que te importan tus clientes'],['Actualizar fotos o información','Un perfil fresco atrae más atención'],['Ver tus métricas de la semana','Descubre cómo te está yendo en el directorio']] as $i => $tip)
            <tr>
              <td style="padding-bottom:10px;">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
                  <tr>
                    <td width="32" valign="top">
                      <div style="width:26px; height:26px; background-color:#D4AF37; border-radius:6px; text-align:center; line-height:26px; font-weight:700; font-size:13px; color:#0B0B0B;">{{ $i + 1 }}</div>
                    </td>
                    <td valign="top" style="padding-left:12px;">
                      <p style="margin:0 0 2px; font-size:14px; font-weight:700; color:#111827;">{{ $tip[0] }}</p>
                      <p style="margin:0; font-size:13px; color:#6B7280;">{{ $tip[1] }}</p>
                    </td>
                  </tr>
                </table>
              </td>
            </tr>
            @endforeach
          </table>

          <!-- CTA -->
          <div style="text-align:center; margin-bottom:32px;">
            <a href="{{ $dashboardUrl ?? $loginUrl ?? config('app.url') . '/owner/dashboard' }}"
               style="display:inline-block; background-color:#D4AF37; color:#0B0B0B; text-decoration:none; font-weight:700; font-size:16px; padding:16px 40px; border-radius:10px; font-family:'Segoe UI',Arial,sans-serif; letter-spacing:0.3px;">
              Volver al Panel
            </a>
          </div>

          <p style="margin:0; font-size:13px; color:#9CA3AF; text-align:center; line-height:1.6;">
            Si tienes algún problema para acceder a tu cuenta, responde este correo y te ayudamos.
          </p>

        </td>
      </tr>

      <!-- FOOTER -->
      <tr>
        <td style="background-color:#F9FAFB; border-radius:0 0 16px 16px; padding:24px 40px; text-align:center; border-top:1px solid #F3F4F6;">
          <p style="margin:0 0 8px; font-size:12px; color:#9CA3AF; text-align:center;">
            &copy; {{ date('Y') }} FAMER — Restaurantes Mexicanos Famosos<br>
            El directorio líder de restaurantes mexicanos en Estados Unidos
          </p>
          <p style="margin:0; font-size:11px; color:#D1D5DB; text-align:center;">
            Recibiste este mensaje porque eres dueño registrado en FAMER.<br>
            <a href="{{ config('app.url') }}/unsubscribe?email={{ urlencode($email ?? $ownerEmail ?? ($restaurant->owner_email ?? '')) }}"
               style="color:#D4AF37; text-decoration:none;">Cancelar notificaciones</a>
          </p>
        </td>
      </tr>

    </table>
  </td></tr>
</table>

</body>
</html>
