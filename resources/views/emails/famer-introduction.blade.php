<!DOCTYPE html>
<html lang="es" xmlns="http://www.w3.org/1999/xhtml">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="format-detection" content="telephone=no">
  <title>Bienvenido a tu panel FAMER</title>
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
          <h1 style="margin:0 0 12px; font-size:26px; font-weight:700; color:#111827; font-family:'Segoe UI',Arial,sans-serif;">
            Bienvenido a tu panel de dueño
          </h1>
          <p style="margin:0 0 6px; font-size:16px; color:#374151; line-height:1.7;">
            @isset($ownerName)
            Hola {{ $ownerName }},
            @else
            Hola,
            @endisset
          </p>
          <p style="margin:0 0 28px; font-size:15px; color:#6B7280; line-height:1.7;">
            Tu restaurante <strong style="color:#111827;">{{ $restaurantName }}</strong> ya está activo en FAMER — el directorio líder de restaurantes mexicanos en Estados Unidos con más de 31,000 establecimientos.
            Aquí te mostramos cómo sacarle el máximo provecho a tu perfil.
          </p>

          <!-- Separador interno -->
          <hr style="border:none; border-top:1px solid #F3F4F6; margin:0 0 28px;">

          <!-- 3 pasos -->
          <p style="margin:0 0 20px; font-size:13px; font-weight:700; color:#D4AF37; letter-spacing:2px; text-transform:uppercase;">Por donde empezar</p>

          <!-- Paso 1 -->
          <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="margin-bottom:16px;">
            <tr>
              <td style="border:1.5px solid #D4AF37; border-radius:12px; padding:20px 24px;">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
                  <tr>
                    <td width="44" valign="top">
                      <div style="width:36px; height:36px; background-color:#D4AF37; border-radius:50%; text-align:center; line-height:36px; font-weight:700; font-size:16px; color:#0B0B0B;">1</div>
                    </td>
                    <td valign="top" style="padding-left:12px;">
                      <p style="margin:0 0 4px; font-size:15px; font-weight:700; color:#111827;">Completa tu perfil</p>
                      <p style="margin:0; font-size:13px; color:#6B7280; line-height:1.6;">Agrega descripción, horarios, teléfono y dirección exacta. Un perfil completo recibe hasta 3 veces más visitas.</p>
                    </td>
                  </tr>
                </table>
              </td>
            </tr>
          </table>

          <!-- Paso 2 -->
          <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="margin-bottom:16px;">
            <tr>
              <td style="border:1.5px solid #E5E7EB; border-radius:12px; padding:20px 24px;">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
                  <tr>
                    <td width="44" valign="top">
                      <div style="width:36px; height:36px; background-color:#F5F0E8; border:1.5px solid #D4AF37; border-radius:50%; text-align:center; line-height:34px; font-weight:700; font-size:16px; color:#D4AF37;">2</div>
                    </td>
                    <td valign="top" style="padding-left:12px;">
                      <p style="margin:0 0 4px; font-size:15px; font-weight:700; color:#111827;">Sube fotos de calidad</p>
                      <p style="margin:0; font-size:13px; color:#6B7280; line-height:1.6;">Restaurantes con 5 o más fotos generan 68% más clicks. Muestra tus platillos, el ambiente y la experiencia.</p>
                    </td>
                  </tr>
                </table>
              </td>
            </tr>
          </table>

          <!-- Paso 3 -->
          <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="margin-bottom:32px;">
            <tr>
              <td style="border:1.5px solid #E5E7EB; border-radius:12px; padding:20px 24px;">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
                  <tr>
                    <td width="44" valign="top">
                      <div style="width:36px; height:36px; background-color:#F5F0E8; border:1.5px solid #D4AF37; border-radius:50%; text-align:center; line-height:34px; font-weight:700; font-size:16px; color:#D4AF37;">3</div>
                    </td>
                    <td valign="top" style="padding-left:12px;">
                      <p style="margin:0 0 4px; font-size:15px; font-weight:700; color:#111827;">Revisa tus analytics</p>
                      <p style="margin:0; font-size:13px; color:#6B7280; line-height:1.6;">Desde tu panel puedes ver cuántas personas visitaron tu perfil, de dónde vienen y qué buscan.</p>
                    </td>
                  </tr>
                </table>
              </td>
            </tr>
          </table>

          <!-- CTA -->
          <div style="text-align:center; margin-bottom:32px;">
            <a href="{{ $dashboardUrl ?? config('app.url') . '/owner/dashboard' }}"
               style="display:inline-block; background-color:#D4AF37; color:#0B0B0B; text-decoration:none; font-weight:700; font-size:16px; padding:16px 40px; border-radius:10px; font-family:'Segoe UI',Arial,sans-serif; letter-spacing:0.3px;">
              Explorar Mi Panel
            </a>
          </div>

          <!-- Nota -->
          <p style="margin:0; font-size:13px; color:#9CA3AF; text-align:center; line-height:1.6;">
            Si tienes dudas, responde directamente a este correo y un miembro de nuestro equipo te ayudará.
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
            <a href="{{ config('app.url') }}/unsubscribe?email={{ urlencode($email ?? $ownerEmail ?? '') }}"
               style="color:#D4AF37; text-decoration:none;">Cancelar notificaciones</a>
          </p>
        </td>
      </tr>

    </table>
  </td></tr>
</table>

</body>
</html>
