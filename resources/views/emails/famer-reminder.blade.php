<!DOCTYPE html>
<html lang="es" xmlns="http://www.w3.org/1999/xhtml">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="format-detection" content="telephone=no">
  <title>Tu perfil tiene pasos pendientes — FAMER</title>
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
            Tu perfil está casi listo
          </h1>
          <p style="margin:0 0 6px; font-size:16px; color:#374151; line-height:1.7;">
            @isset($ownerName)
            Hola {{ $ownerName }},
            @else
            Hola,
            @endisset
          </p>
          <p style="margin:0 0 28px; font-size:15px; color:#6B7280; line-height:1.7;">
            Notamos que el perfil de <strong style="color:#111827;">{{ $restaurant->name ?? $restaurantName ?? 'tu restaurante' }}</strong> en FAMER todavía tiene algunos pasos por completar. Te tomará solo unos minutos y marcará una diferencia real en tu visibilidad.
          </p>

          <!-- Acciones pendientes -->
          @isset($pendingActions)
          @if(count($pendingActions) > 0)
          <p style="margin:0 0 16px; font-size:13px; font-weight:700; color:#D4AF37; letter-spacing:2px; text-transform:uppercase;">Acciones pendientes</p>

          @foreach($pendingActions as $action)
          <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="margin-bottom:10px;">
            <tr>
              <td style="background-color:#FAFAFA; border-left:3px solid #D4AF37; border-radius:0 8px 8px 0; padding:14px 18px;">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
                  <tr>
                    <td width="20" valign="top" style="padding-top:2px;">
                      <div style="width:16px; height:16px; border:2px solid #D4AF37; border-radius:50%;"></div>
                    </td>
                    <td valign="top" style="padding-left:10px;">
                      <p style="margin:0; font-size:14px; color:#111827; font-weight:600;">{{ $action['title'] ?? $action }}</p>
                      @isset($action['description'])
                      <p style="margin:4px 0 0; font-size:13px; color:#6B7280;">{{ $action['description'] }}</p>
                      @endisset
                    </td>
                  </tr>
                </table>
              </td>
            </tr>
          </table>
          @endforeach

          <div style="margin-top:8px; margin-bottom:28px;"></div>
          @endif
          @else
          <!-- Acciones genéricas si no se pasan -->
          <p style="margin:0 0 16px; font-size:13px; font-weight:700; color:#D4AF37; letter-spacing:2px; text-transform:uppercase;">Qué falta completar</p>

          @foreach([['Agregar fotos del restaurante','Los perfiles con fotos reciben 3 veces más visitas'],['Completar descripción y horarios','Ayuda a los clientes a encontrarte y visitarte'],['Verificar información de contacto','Teléfono, dirección y sitio web actualizados']] as $item)
          <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="margin-bottom:10px;">
            <tr>
              <td style="background-color:#FAFAFA; border-left:3px solid #D4AF37; border-radius:0 8px 8px 0; padding:14px 18px;">
                <p style="margin:0; font-size:14px; color:#111827; font-weight:600;">{{ $item[0] }}</p>
                <p style="margin:4px 0 0; font-size:13px; color:#6B7280;">{{ $item[1] }}</p>
              </td>
            </tr>
          </table>
          @endforeach

          <div style="margin-top:8px; margin-bottom:28px;"></div>
          @endisset

          <!-- Nota informativa -->
          <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="margin-bottom:32px;">
            <tr>
              <td style="background-color:#FBF6E9; border-radius:10px; padding:18px 20px;">
                <p style="margin:0; font-size:14px; color:#78540A; line-height:1.6;">
                  Los restaurantes con perfil completo en FAMER aparecen mejor posicionados en el directorio y en las búsquedas de nuestros 26,000+ listados.
                </p>
              </td>
            </tr>
          </table>

          <!-- CTA -->
          <div style="text-align:center; margin-bottom:32px;">
            <a href="{{ $dashboardUrl ?? config('app.url') . '/owner/profile' }}"
               style="display:inline-block; background-color:#D4AF37; color:#0B0B0B; text-decoration:none; font-weight:700; font-size:16px; padding:16px 40px; border-radius:10px; font-family:'Segoe UI',Arial,sans-serif; letter-spacing:0.3px;">
              Completar Mi Perfil
            </a>
          </div>

          <p style="margin:0; font-size:13px; color:#9CA3AF; text-align:center; line-height:1.6;">
            Si ya completaste tu perfil, ignora este mensaje — no necesitas hacer nada más.
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
