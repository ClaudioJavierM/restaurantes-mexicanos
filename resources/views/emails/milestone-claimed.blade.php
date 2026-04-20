<!DOCTYPE html>
<html lang="es" xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{{ $viewCount }} visitas — FAMER</title>
</head>
<body style="margin:0; padding:0; background-color:#F5F0E8; font-family:'Segoe UI',Arial,Helvetica,sans-serif;">
<table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color:#F5F0E8;">
  <tr><td align="center" style="padding:40px 16px;">
    <table role="presentation" width="100%" style="max-width:580px;" cellspacing="0" cellpadding="0" border="0">

      <!-- HEADER -->
      <tr>
        <td style="background-color:#0B0B0B; border-radius:16px 16px 0 0; padding:28px 40px; text-align:center;">
          <img src="https://restaurantesmexicanosfamosos.com.mx/images/branding/logo-horizontal.png" alt="FAMER" width="160" style="max-width:160px;height:auto;display:block;margin:0 auto 12px;">
          <p style="margin:0;color:#D4AF37;font-size:11px;font-weight:700;letter-spacing:3px;text-transform:uppercase;">FAMOUS MEXICAN RESTAURANTS</p>
        </td>
      </tr>
      <tr><td style="background:linear-gradient(90deg,#D4AF37,#F0D060,#D4AF37);height:3px;font-size:0;line-height:0;">&nbsp;</td></tr>

      <!-- BODY -->
      <tr>
        <td style="background-color:#FFFFFF;padding:48px 40px 40px;">

          <!-- Verified badge -->
          <p style="margin:0 0 16px;text-align:center;font-size:40px;">📈</p>
          <h1 style="margin:0 0 8px;font-size:26px;font-weight:700;color:#111827;text-align:center;">
            ¡Tu perfil ya está activo, {{ $restaurant->name }}!
          </h1>
          <p style="margin:0 0 32px;font-size:15px;color:#6B7280;text-align:center;line-height:1.6;">
            Reclamaste tu restaurante — ahora es momento de llevarlo al siguiente nivel.
          </p>

          <!-- Views milestone box -->
          <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0"
                 style="background:linear-gradient(135deg,#0B0B0B 0%,#1A1A1A 100%);border-radius:12px;margin:0 0 28px;">
            <tr>
              <td style="padding:28px 24px;text-align:center;">
                <p style="margin:0 0 4px;font-size:52px;font-weight:800;color:#D4AF37;line-height:1;">{{ number_format($viewCount) }}</p>
                <p style="margin:0 0 12px;font-size:16px;color:#FFFFFF;font-weight:600;">visitas orgánicas en FAMER</p>
                <p style="margin:0;font-size:13px;color:#9CA3AF;">{{ $restaurant->city }}, {{ $restaurant->state->code ?? '' }}</p>
              </td>
            </tr>
          </table>

          <!-- What's missing box -->
          <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0"
                 style="background-color:#FEF3C7;border:1px solid #F59E0B;border-radius:10px;margin:0 0 28px;">
            <tr>
              <td style="padding:20px 24px;">
                <p style="margin:0 0 8px;font-size:14px;font-weight:700;color:#92400E;">⚠️ Tu perfil aún no aparece destacado</p>
                <p style="margin:0;font-size:14px;color:#374151;line-height:1.6;">
                  Con el perfil gratuito, tu restaurante aparece en el directorio pero <strong>no en posiciones prioritarias</strong>.
                  Restaurantes Premium en {{ $restaurant->city }} están captando clientes que deberían ser tuyos.
                </p>
              </td>
            </tr>
          </table>

          <p style="margin:0 0 24px;font-size:15px;color:#374151;line-height:1.7;">
            Ya diste el primer paso al reclamar tu perfil. Con Premium, <strong>tu restaurante aparece primero</strong> en búsquedas de comida mexicana en {{ $restaurant->city }} — antes que tu competencia que aún no ha actuado.
          </p>

          <!-- Premium CTA -->
          <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0"
                 style="background-color:#0B0B0B;border-radius:10px;margin:0 0 20px;">
            <tr>
              <td style="padding:28px;">
                <p style="margin:0 0 4px;font-size:11px;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:#D4AF37;">OFERTA ESPECIAL</p>
                <p style="margin:0 0 6px;font-size:22px;font-weight:800;color:#FFFFFF;">
                  Premium · <span style="color:#D4AF37;">$9.99 el primer mes</span>
                </p>
                <p style="margin:0 0 4px;font-size:13px;color:#6B7280;">Luego $29/mes · Cancela cuando quieras</p>
                <p style="margin:12px 0 20px;font-size:14px;color:#9CA3AF;line-height:1.6;">
                  Posiciona {{ $restaurant->name }} en los primeros resultados de {{ $restaurant->city }} y empieza a recibir más clientes esta semana.
                </p>
                <table role="presentation" cellspacing="0" cellpadding="0" border="0" style="margin-bottom:16px;">
                  <tr><td style="padding:4px 0;font-size:14px;color:#D4AF37;"><span style="margin-right:8px;">★</span><span style="color:#E5E7EB;">Posición destacada en búsquedas y Top 10</span></td></tr>
                  <tr><td style="padding:4px 0;font-size:14px;color:#D4AF37;"><span style="margin-right:8px;">★</span><span style="color:#E5E7EB;">Badge "Restaurante Destacado"</span></td></tr>
                  <tr><td style="padding:4px 0;font-size:14px;color:#D4AF37;"><span style="margin-right:8px;">★</span><span style="color:#E5E7EB;">Analíticas: visitas, clicks y tendencias</span></td></tr>
                  <tr><td style="padding:4px 0;font-size:14px;color:#D4AF37;"><span style="margin-right:8px;">★</span><span style="color:#E5E7EB;">Galería de fotos ilimitada</span></td></tr>
                </table>
                <table role="presentation" cellspacing="0" cellpadding="0" border="0">
                  <tr>
                    <td style="border-radius:8px;background-color:#D4AF37;">
                      <a href="{{ $premiumUrl }}" target="_blank"
                         style="display:inline-block;padding:14px 32px;font-size:15px;font-weight:700;color:#0B0B0B;text-decoration:none;">
                        Activar Premium por $9.99 →
                      </a>
                    </td>
                  </tr>
                </table>
              </td>
            </tr>
          </table>

          <p style="margin:0 0 8px;font-size:13px;color:#9CA3AF;text-align:center;line-height:1.6;">
            Solo para restaurantes que ya reclamaron su perfil. Oferta disponible por tiempo limitado.
          </p>

        </td>
      </tr>

      <tr><td style="background:linear-gradient(90deg,#D4AF37,#F0D060,#D4AF37);height:2px;font-size:0;line-height:0;">&nbsp;</td></tr>

      <!-- FOOTER -->
      <tr>
        <td style="background-color:#0B0B0B;border-radius:0 0 16px 16px;padding:24px 40px;text-align:center;">
          <p style="margin:0 0 8px;font-size:12px;color:#4B5563;line-height:1.6;">
            Recibes este email porque gestionas {{ $restaurant->name }} en FAMER.<br>
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
