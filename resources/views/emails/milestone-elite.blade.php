<!DOCTYPE html>
<html lang="es" xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Sigue siendo el #1 — FAMER</title>
</head>
<body style="margin:0;padding:0;background-color:#F5F0E8;font-family:'Segoe UI',Arial,Helvetica,sans-serif;">
<table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color:#F5F0E8;">
  <tr><td align="center" style="padding:40px 16px;">
    <table role="presentation" width="100%" style="max-width:580px;" cellspacing="0" cellpadding="0" border="0">

      <!-- HEADER -->
      <tr>
        <td style="background-color:#0B0B0B;border-radius:16px 16px 0 0;padding:28px 40px;text-align:center;">
          <img src="https://restaurantesmexicanosfamosos.com.mx/images/branding/logo-horizontal.png" alt="FAMER" width="160" style="max-width:160px;height:auto;display:block;margin:0 auto 12px;">
          <p style="margin:0;color:#D4AF37;font-size:11px;font-weight:700;letter-spacing:3px;text-transform:uppercase;">FAMOUS MEXICAN RESTAURANTS</p>
        </td>
      </tr>
      <tr><td style="background:linear-gradient(90deg,#D4AF37,#F0D060,#D4AF37);height:3px;font-size:0;line-height:0;">&nbsp;</td></tr>

      <!-- BODY -->
      <tr>
        <td style="background-color:#FFFFFF;padding:48px 40px 40px;">

          <p style="margin:0 0 16px;text-align:center;font-size:40px;">👑</p>
          <h1 style="margin:0 0 8px;font-size:26px;font-weight:700;color:#111827;text-align:center;">
            ¡{{ $restaurant->name }} sigue brillando!
          </h1>
          <p style="margin:0 0 32px;font-size:15px;color:#6B7280;text-align:center;line-height:1.6;">
            Tu plan Elite está generando resultados reales en {{ $restaurant->city }}.
          </p>

          <!-- Views milestone box -->
          <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0"
                 style="background:linear-gradient(135deg,#0B0B0B 0%,#1A1A1A 100%);border-radius:12px;margin:0 0 28px;">
            <tr>
              <td style="padding:28px 24px;text-align:center;">
                <p style="margin:0 0 4px;font-size:52px;font-weight:800;color:#D4AF37;line-height:1;">{{ number_format($viewCount) }}</p>
                <p style="margin:0 0 12px;font-size:16px;color:#FFFFFF;font-weight:600;">personas vieron tu restaurante en FAMER</p>
                <p style="margin:0;font-size:13px;color:#9CA3AF;">{{ $restaurant->city }}, {{ $restaurant->state->code ?? '' }}</p>
              </td>
            </tr>
          </table>

          <p style="margin:0 0 24px;font-size:15px;color:#374151;line-height:1.7;">
            Mantener el #1 requiere consistencia. Aquí te recordamos las herramientas Elite que más impacto tienen en tu posicionamiento:
          </p>

          <!-- Tools checklist -->
          <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0"
                 style="border:1px solid #E5E7EB;border-radius:10px;margin:0 0 28px;overflow:hidden;">
            <tr>
              <td style="background-color:#FBF6E9;padding:16px 20px;border-bottom:1px solid #E5E7EB;">
                <p style="margin:0;font-size:14px;font-weight:700;color:#111827;">
                  ⚡ Acciones recomendadas esta semana
                </p>
              </td>
            </tr>
            <tr>
              <td style="padding:0;">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
                  <tr style="border-bottom:1px solid #F3F4F6;">
                    <td style="padding:14px 20px;">
                      <p style="margin:0 0 2px;font-size:14px;font-weight:600;color:#111827;">📸 Actualiza tus fotos</p>
                      <p style="margin:0;font-size:13px;color:#6B7280;">Los perfiles con fotos recientes reciben 3x más clics</p>
                    </td>
                  </tr>
                  <tr style="border-bottom:1px solid #F3F4F6;">
                    <td style="padding:14px 20px;">
                      <p style="margin:0 0 2px;font-size:14px;font-weight:600;color:#111827;">💬 Responde tus reseñas</p>
                      <p style="margin:0;font-size:13px;color:#6B7280;">Responder reseñas mejora tu score FAMER y tu posición</p>
                    </td>
                  </tr>
                  <tr style="border-bottom:1px solid #F3F4F6;">
                    <td style="padding:14px 20px;">
                      <p style="margin:0 0 2px;font-size:14px;font-weight:600;color:#111827;">🕐 Verifica tu horario</p>
                      <p style="margin:0;font-size:13px;color:#6B7280;">Horarios actualizados evitan visitas fallidas y reseñas negativas</p>
                    </td>
                  </tr>
                  <tr>
                    <td style="padding:14px 20px;">
                      <p style="margin:0 0 2px;font-size:14px;font-weight:600;color:#111827;">📊 Revisa tus analíticas</p>
                      <p style="margin:0;font-size:13px;color:#6B7280;">Identifica de dónde vienen tus visitantes y qué buscan</p>
                    </td>
                  </tr>
                </table>
              </td>
            </tr>
          </table>

          <!-- Dashboard CTA -->
          <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0"
                 style="background-color:#0B0B0B;border-radius:10px;margin:0 0 20px;text-align:center;">
            <tr>
              <td style="padding:28px;">
                <p style="margin:0 0 20px;font-size:16px;color:#FFFFFF;line-height:1.5;">
                  Accede a tu dashboard para gestionar<br><strong style="color:#D4AF37;">{{ $restaurant->name }}</strong>
                </p>
                <table role="presentation" cellspacing="0" cellpadding="0" border="0" style="margin:0 auto;">
                  <tr>
                    <td style="border-radius:8px;background-color:#D4AF37;">
                      <a href="{{ $dashboardUrl }}" target="_blank"
                         style="display:inline-block;padding:14px 32px;font-size:15px;font-weight:700;color:#0B0B0B;text-decoration:none;">
                        Ir a mi dashboard →
                      </a>
                    </td>
                  </tr>
                </table>
              </td>
            </tr>
          </table>

          <p style="margin:0;font-size:13px;color:#9CA3AF;text-align:center;line-height:1.6;">
            Gracias por ser parte de FAMER Elite. Tu éxito es nuestro éxito.
          </p>

        </td>
      </tr>

      <tr><td style="background:linear-gradient(90deg,#D4AF37,#F0D060,#D4AF37);height:2px;font-size:0;line-height:0;">&nbsp;</td></tr>

      <!-- FOOTER -->
      <tr>
        <td style="background-color:#0B0B0B;border-radius:0 0 16px 16px;padding:24px 40px;text-align:center;">
          <p style="margin:0 0 8px;font-size:12px;color:#4B5563;line-height:1.6;">
            Recibes este email porque tienes plan Elite en FAMER para {{ $restaurant->name }}.<br>
            <a href="https://restaurantesmexicanosfamosos.com.mx/unsubscribe?email={{ urlencode($restaurant->email ?? '') }}" style="color:#6B7280;text-decoration:underline;">Cancelar suscripción</a>
          </p>
          <p style="margin:0;font-size:11px;color:#374151;">© {{ date('Y') }} Famous Mexican Restaurants · restaurantesmexicanosfamosos.com.mx</p>
        </td>
      </tr>

    </table>
  </td></tr>
</table>
</body>
</html>
