<!DOCTYPE html>
<html lang="es" xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Upgrade a Elite — FAMER</title>
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

          <p style="margin:0 0 16px;text-align:center;font-size:40px;">🚀</p>
          <h1 style="margin:0 0 8px;font-size:26px;font-weight:700;color:#111827;text-align:center;">
            {{ $restaurant->name }} está despegando
          </h1>
          <p style="margin:0 0 32px;font-size:15px;color:#6B7280;text-align:center;line-height:1.6;">
            Tu plan Premium está funcionando. Es el momento de dominar {{ $restaurant->city }} completamente.
          </p>

          <!-- Views milestone box -->
          <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0"
                 style="background:linear-gradient(135deg,#0B0B0B 0%,#1A1A1A 100%);border-radius:12px;margin:0 0 28px;">
            <tr>
              <td style="padding:28px 24px;text-align:center;">
                <p style="margin:0 0 4px;font-size:52px;font-weight:800;color:#D4AF37;line-height:1;">{{ number_format($viewCount) }}</p>
                <p style="margin:0 0 12px;font-size:16px;color:#FFFFFF;font-weight:600;">visitas a tu perfil en FAMER</p>
                <p style="margin:0;font-size:13px;color:#9CA3AF;">{{ $restaurant->city }}, {{ $restaurant->state->code ?? '' }}</p>
              </td>
            </tr>
          </table>

          <!-- Premium vs Elite comparison -->
          <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="margin:0 0 28px;">
            <tr>
              <!-- Premium (current) -->
              <td width="48%" style="background-color:#F9FAFB;border:1px solid #E5E7EB;border-radius:10px;padding:16px 20px;vertical-align:top;">
                <p style="margin:0 0 8px;font-size:12px;font-weight:700;color:#6B7280;text-transform:uppercase;letter-spacing:1px;">Tu plan actual</p>
                <p style="margin:0 0 12px;font-size:18px;font-weight:700;color:#111827;">Premium</p>
                <p style="margin:0 0 6px;font-size:13px;color:#6B7280;">✓ Perfil destacado</p>
                <p style="margin:0 0 6px;font-size:13px;color:#6B7280;">✓ Analíticas básicas</p>
                <p style="margin:0 0 6px;font-size:13px;color:#6B7280;">✓ Badge verificado</p>
                <p style="margin:0;font-size:13px;color:#D4AF37;">$29/mes</p>
              </td>
              <td width="4%" style="text-align:center;vertical-align:middle;font-size:20px;color:#D4AF37;font-weight:700;">→</td>
              <!-- Elite -->
              <td width="48%" style="background-color:#0B0B0B;border:2px solid #D4AF37;border-radius:10px;padding:16px 20px;vertical-align:top;">
                <p style="margin:0 0 8px;font-size:12px;font-weight:700;color:#D4AF37;text-transform:uppercase;letter-spacing:1px;">Upgrade</p>
                <p style="margin:0 0 12px;font-size:18px;font-weight:700;color:#FFFFFF;">Elite</p>
                <p style="margin:0 0 6px;font-size:13px;color:#E5E7EB;">★ Todo lo de Premium</p>
                <p style="margin:0 0 6px;font-size:13px;color:#E5E7EB;">★ #1 garantizado en city</p>
                <p style="margin:0 0 6px;font-size:13px;color:#E5E7EB;">★ Analíticas avanzadas</p>
                <p style="margin:0 0 6px;font-size:13px;color:#E5E7EB;">★ Respuestas AI a reseñas</p>
                <p style="margin:0;font-size:13px;color:#D4AF37;font-weight:700;">$79/mes</p>
              </td>
            </tr>
          </table>

          <p style="margin:0 0 28px;font-size:15px;color:#374151;line-height:1.7;">
            Con Elite, <strong>{{ $restaurant->name }} aparece en el #1 de {{ $restaurant->city }}</strong> con máxima visibilidad. Solo un restaurante por ciudad puede tener el top spot Elite — y tu competencia también lo sabe.
          </p>

          <!-- Elite CTA -->
          <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0"
                 style="background-color:#0B0B0B;border:1px solid #D4AF37;border-radius:10px;margin:0 0 20px;">
            <tr>
              <td style="padding:24px;text-align:center;">
                <p style="margin:0 0 4px;font-size:11px;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:#D4AF37;">EXCLUSIVO</p>
                <p style="margin:0 0 20px;font-size:16px;color:#FFFFFF;line-height:1.6;">
                  Domina {{ $restaurant->city }} con <strong style="color:#D4AF37;">Elite por $79/mes</strong>
                </p>
                <table role="presentation" cellspacing="0" cellpadding="0" border="0" style="margin:0 auto;">
                  <tr>
                    <td style="border-radius:8px;background-color:#D4AF37;">
                      <a href="{{ $eliteUrl }}" target="_blank"
                         style="display:inline-block;padding:14px 32px;font-size:15px;font-weight:700;color:#0B0B0B;text-decoration:none;">
                        Hacer upgrade a Elite →
                      </a>
                    </td>
                  </tr>
                </table>
              </td>
            </tr>
          </table>

          <p style="margin:0;font-size:13px;color:#9CA3AF;text-align:center;">
            Mantén tu plan Premium si lo prefieres — siempre puedes hacer upgrade más adelante.
          </p>

        </td>
      </tr>

      <tr><td style="background:linear-gradient(90deg,#D4AF37,#F0D060,#D4AF37);height:2px;font-size:0;line-height:0;">&nbsp;</td></tr>

      <!-- FOOTER -->
      <tr>
        <td style="background-color:#0B0B0B;border-radius:0 0 16px 16px;padding:24px 40px;text-align:center;">
          <p style="margin:0 0 8px;font-size:12px;color:#4B5563;line-height:1.6;">
            Recibes este email porque tienes plan Premium en FAMER para {{ $restaurant->name }}.<br>
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
