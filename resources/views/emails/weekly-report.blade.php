<!DOCTYPE html>
<html lang="es" xmlns="http://www.w3.org/1999/xhtml">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="format-detection" content="telephone=no">
  <title>Reporte Semanal — {{ $restaurant->name ?? 'Tu restaurante' }}</title>
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

          <!-- Encabezado del reporte -->
          <h1 style="margin:0 0 8px; font-size:24px; font-weight:700; color:#111827; font-family:'Segoe UI',Arial,sans-serif;">
            Reporte semanal
          </h1>
          <p style="margin:0 0 4px; font-size:18px; font-weight:700; color:#0B0B0B;">{{ $restaurant->name ?? '' }}</p>
          @if(isset($restaurant->city) || isset($restaurant->state))
          <p style="margin:0 0 4px; font-size:14px; color:#6B7280;">{{ $restaurant->city ?? '' }}{{ isset($restaurant->state) && isset($restaurant->city) ? ', ' : '' }}{{ $restaurant->state ?? '' }}</p>
          @endif
          <p style="margin:0 0 28px; font-size:13px; color:#9CA3AF;">
            @isset($weekStart)
            {{ $weekStart }}
            @endisset
            @if(isset($weekStart) && isset($weekEnd)) — @endif
            @isset($weekEnd)
            {{ $weekEnd }}
            @endisset
            @if(!isset($weekStart) && !isset($weekEnd))
            {{ isset($weekRange) ? $weekRange : '' }}
            @endif
          </p>

          <hr style="border:none; border-top:1px solid #F3F4F6; margin:0 0 28px;">

          <!-- Grid de métricas 2x2 -->
          <p style="margin:0 0 16px; font-size:13px; font-weight:700; color:#D4AF37; letter-spacing:2px; text-transform:uppercase;">Tu actividad esta semana</p>

          <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="margin-bottom:16px;">
            <tr>
              <!-- Vistas esta semana -->
              <td width="50%" style="padding:0 6px 12px 0;">
                <div style="background-color:#FBF6E9; border-radius:12px; padding:22px 16px; text-align:center;">
                  <p style="margin:0 0 4px; font-size:36px; font-weight:800; color:#D4AF37; line-height:1;">
                    {{ isset($stats['this_week_views']) ? number_format($stats['this_week_views']) : (isset($stats['views']) ? number_format($stats['views']) : '—') }}
                  </p>
                  <p style="margin:0 0 6px; font-size:11px; font-weight:700; color:#78540A; text-transform:uppercase; letter-spacing:0.5px;">Vistas esta semana</p>
                  @if(isset($stats['growth']) && $stats['growth'] != 0)
                  <p style="margin:0; font-size:12px; color:{{ $stats['growth'] > 0 ? '#166534' : '#991B1B' }}; font-weight:600;">
                    {{ $stats['growth'] > 0 ? '+' : '' }}{{ $stats['growth'] }}% vs semana anterior
                  </p>
                  @endif
                </div>
              </td>
              <!-- Vistas totales / Clicks -->
              <td width="50%" style="padding:0 0 12px 6px;">
                <div style="background-color:#F9FAFB; border-radius:12px; padding:22px 16px; text-align:center;">
                  <p style="margin:0 0 4px; font-size:36px; font-weight:800; color:#374151; line-height:1;">
                    {{ isset($stats['total_views']) ? number_format($stats['total_views']) : (isset($stats['clicks']) ? number_format($stats['clicks']) : '—') }}
                  </p>
                  <p style="margin:0; font-size:11px; font-weight:700; color:#6B7280; text-transform:uppercase; letter-spacing:0.5px;">
                    {{ isset($stats['total_views']) ? 'Vistas totales' : 'Clicks al perfil' }}
                  </p>
                </div>
              </td>
            </tr>
            <tr>
              <!-- Reseñas nuevas -->
              <td width="50%" style="padding:0 6px 0 0;">
                <div style="background-color:#F9FAFB; border-radius:12px; padding:22px 16px; text-align:center;">
                  <p style="margin:0 0 4px; font-size:36px; font-weight:800; color:#374151; line-height:1;">
                    {{ $stats['new_reviews'] ?? $stats['reviews'] ?? '—' }}
                  </p>
                  <p style="margin:0; font-size:11px; font-weight:700; color:#6B7280; text-transform:uppercase; letter-spacing:0.5px;">Reseñas nuevas</p>
                </div>
              </td>
              <!-- Calificación -->
              <td width="50%" style="padding:0 0 0 6px;">
                <div style="background-color:#FBF6E9; border-radius:12px; padding:22px 16px; text-align:center;">
                  <p style="margin:0 0 4px; font-size:36px; font-weight:800; color:#D4AF37; line-height:1;">
                    {{ isset($stats['avg_rating']) ? number_format($stats['avg_rating'], 1) : (isset($stats['average_rating']) ? number_format($stats['average_rating'], 1) : '—') }}
                  </p>
                  <p style="margin:0; font-size:11px; font-weight:700; color:#78540A; text-transform:uppercase; letter-spacing:0.5px;">Calificación</p>
                </div>
              </td>
            </tr>
          </table>

          <!-- Llamadas si existen -->
          @if(isset($stats['calls']) && $stats['calls'] > 0)
          <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="margin-bottom:16px;">
            <tr>
              <td style="background-color:#F9FAFB; border-radius:12px; padding:16px 20px; text-align:center;">
                <p style="margin:0 0 4px; font-size:28px; font-weight:800; color:#374151; line-height:1;">{{ $stats['calls'] }}</p>
                <p style="margin:0; font-size:11px; font-weight:700; color:#6B7280; text-transform:uppercase; letter-spacing:0.5px;">Llamadas recibidas</p>
              </td>
            </tr>
          </table>
          @endif

          <!-- Reseñas pendientes de respuesta -->
          @if(isset($stats['pending_responses']) && $stats['pending_responses'] > 0)
          <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="margin:16px 0 24px;">
            <tr>
              <td style="background-color:#FEF3C7; border-left:3px solid #D4AF37; border-radius:0 10px 10px 0; padding:14px 18px;">
                <p style="margin:0 0 4px; font-size:14px; font-weight:700; color:#92400E;">
                  {{ $stats['pending_responses'] }} reseña{{ $stats['pending_responses'] > 1 ? 's' : '' }} sin responder
                </p>
                <p style="margin:0; font-size:13px; color:#78540A; line-height:1.5;">
                  Responder a tus clientes mejora tu reputación y tu score en el directorio.
                </p>
              </td>
            </tr>
          </table>
          @endif

          <div style="margin-top:8px; margin-bottom:28px;"></div>

          <!-- Tips de la semana -->
          <hr style="border:none; border-top:1px solid #F3F4F6; margin:0 0 24px;">
          <p style="margin:0 0 14px; font-size:13px; font-weight:700; color:#D4AF37; letter-spacing:2px; text-transform:uppercase;">Consejos para esta semana</p>

          <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="margin-bottom:28px;">
            @foreach([['Agrega fotos nuevas de tus platillos más populares','Los perfiles con fotos actualizadas generan más clicks'],['Responde a todas las reseñas, incluso las críticas','Una respuesta profesional habla bien de tu negocio'],['Mantén horarios y teléfono actualizados','Evita que los clientes lleguen cuando estás cerrado']] as $tip)
            <tr>
              <td style="padding-bottom:12px;">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
                  <tr>
                    <td width="16" valign="top" style="padding-top:3px;">
                      <div style="width:8px; height:8px; background-color:#D4AF37; border-radius:50%;"></div>
                    </td>
                    <td valign="top" style="padding-left:10px;">
                      <p style="margin:0 0 2px; font-size:14px; font-weight:600; color:#111827;">{{ $tip[0] }}</p>
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
            <a href="{{ url('/owner/dashboard') }}"
               style="display:inline-block; background-color:#D4AF37; color:#0B0B0B; text-decoration:none; font-weight:700; font-size:16px; padding:16px 40px; border-radius:10px; font-family:'Segoe UI',Arial,sans-serif; letter-spacing:0.3px;">
              Ver Reporte Completo
            </a>
          </div>

          <!-- Beneficios FAMER -->
          <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
            <tr>
              <td style="background-color:#F9FAFB; border-radius:10px; padding:16px 20px; text-align:center;">
                <p style="margin:0 0 6px; font-size:13px; font-weight:700; color:#374151;">Beneficios exclusivos para dueños FAMER</p>
                <p style="margin:0; font-size:12px; color:#9CA3AF; line-height:1.6;">
                  Descuentos en MF Imports, Tormex Pro, MF Trailers, Refrimex y más socios de la red MF Group.
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
