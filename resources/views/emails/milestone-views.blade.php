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

        <!-- GOLD DIVIDER -->
        <tr>
          <td style="background:linear-gradient(90deg,#D4AF37,#F0D060,#D4AF37); height:3px; font-size:0; line-height:0;">&nbsp;</td>
        </tr>

        <!-- BODY -->
        <tr>
          <td style="background-color:#FFFFFF; padding:48px 40px 40px;">

            <!-- Congratulations badge -->
            <p style="margin:0 0 16px; text-align:center; font-size:40px;">🎉</p>

            <h1 style="margin:0 0 8px; font-size:26px; font-weight:700; color:#111827; text-align:center; font-family:'Segoe UI',Arial,sans-serif;">
              ¡Felicidades, {{ $restaurant->name }}!
            </h1>
            <p style="margin:0 0 32px; font-size:15px; color:#6B7280; text-align:center; line-height:1.6;">
              Tu restaurante está generando interés real en internet.
            </p>

            <!-- Big views milestone box -->
            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0"
                   style="background:linear-gradient(135deg,#0B0B0B 0%,#1A1A1A 100%); border-radius:12px; margin:0 0 28px;">
              <tr>
                <td style="padding:28px 24px; text-align:center;">
                  <p style="margin:0 0 4px; font-size:52px; font-weight:800; color:#D4AF37; line-height:1;">{{ number_format($viewCount) }}</p>
                  <p style="margin:0 0 12px; font-size:16px; color:#FFFFFF; font-weight:600;">visitas a tu perfil en FAMER</p>
                  <p style="margin:0; font-size:13px; color:#9CA3AF;">{{ $restaurant->city }}, {{ $restaurant->state->code ?? '' }}</p>
                </td>
              </tr>
            </table>

            <!-- Google Ads equivalence box -->
            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0"
                   style="background-color:#F0FDF4; border:1px solid #86EFAC; border-radius:10px; margin:0 0 28px;">
              <tr>
                <td style="padding:20px 24px;">
                  <p style="margin:0 0 8px; font-size:14px; font-weight:700; color:#15803D;">
                    💰 ¿Cuánto valdría esto en Google Ads?
                  </p>
                  <p style="margin:0 0 6px; font-size:15px; color:#374151; line-height:1.6;">
                    En Google Ads, cada visita de alguien buscando un restaurante mexicano cuesta entre <strong>$1.50 y $4.00 USD</strong>.
                    Tu restaurante ya recibió <strong>{{ number_format($viewCount) }} visitas orgánicas</strong> — un valor estimado de
                    <strong style="color:#15803D;">${{ number_format($viewCount * 2.5, 0) }} – ${{ number_format($viewCount * 4, 0) }} USD</strong> en publicidad.
                  </p>
                  <p style="margin:0; font-size:13px; color:#6B7280;">
                    Todo esto sin pagar un solo centavo en anuncios.
                  </p>
                </td>
              </tr>
            </table>

            <!-- Main message -->
            <p style="margin:0 0 24px; font-size:15px; color:#374151; line-height:1.7;">
              Imagina lo que pasaría si <strong>tu restaurante apareciera en los primeros lugares</strong> cuando alguien busca comida mexicana en {{ $restaurant->city }}. Eso es exactamente lo que ofrecemos — y tu competencia todavía no lo sabe.
            </p>

            <!-- Free claim CTA -->
            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0"
                   style="background-color:#FBF6E9; border:1px solid #D4AF37; border-radius:10px; margin:0 0 20px;">
              <tr>
                <td style="padding:24px;">
                  <p style="margin:0 0 6px; font-size:16px; font-weight:700; color:#111827;">
                    🏆 Opción 1 — Reclama tu perfil GRATIS
                  </p>
                  <p style="margin:0 0 16px; font-size:14px; color:#6B7280; line-height:1.6;">
                    Toma el control de tu perfil, agrega fotos, actualiza tu información y obtén tu insignia de restaurante verificado. Sin costo.
                  </p>
                  <table role="presentation" cellspacing="0" cellpadding="0" border="0">
                    <tr>
                      <td style="border-radius:8px; background-color:#D4AF37;">
                        <a href="{{ $claimUrl }}" target="_blank"
                           style="display:inline-block; padding:14px 32px; font-size:15px; font-weight:700; color:#0B0B0B; text-decoration:none;">
                          Reclamar mi restaurante gratis →
                        </a>
                      </td>
                    </tr>
                  </table>
                </td>
              </tr>
            </table>

            <!-- Premium CTA -->
            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0"
                   style="background-color:#0B0B0B; border-radius:10px; margin:0 0 28px;">
              <tr>
                <td style="padding:24px;">
                  <p style="margin:0 0 4px; font-size:11px; font-weight:700; letter-spacing:2px; text-transform:uppercase; color:#D4AF37;">
                    RECOMENDADO
                  </p>
                  <p style="margin:0 0 6px; font-size:16px; font-weight:700; color:#FFFFFF;">
                    ⚡ Opción 2 — Suscripción Premium · <span style="color:#D4AF37;">$9.99 el primer mes</span>, luego $29/mes
                  </p>
                  <p style="margin:0 0 16px; font-size:14px; color:#9CA3AF; line-height:1.6;">
                    Posiciona tu restaurante en los <strong style="color:#D4AF37;">primeros lugares de {{ $restaurant->city }}</strong> antes que tu competencia. Los restaurantes Premium aparecen destacados en búsquedas, Top 10 y la página principal.
                  </p>
                  <table role="presentation" cellspacing="0" cellpadding="0" border="0" style="margin-bottom:12px;">
                    <tr><td style="padding:4px 0; font-size:14px; color:#D4AF37;"><span style="margin-right:8px;">★</span><span style="color:#E5E7EB;">Perfil destacado en búsquedas y Top 10 de tu ciudad</span></td></tr>
                    <tr><td style="padding:4px 0; font-size:14px; color:#D4AF37;"><span style="margin-right:8px;">★</span><span style="color:#E5E7EB;">Badge "Restaurante Destacado" que genera confianza</span></td></tr>
                    <tr><td style="padding:4px 0; font-size:14px; color:#D4AF37;"><span style="margin-right:8px;">★</span><span style="color:#E5E7EB;">Analíticas detalladas: visitas, clicks y conversiones</span></td></tr>
                    <tr><td style="padding:4px 0; font-size:14px; color:#D4AF37;"><span style="margin-right:8px;">★</span><span style="color:#E5E7EB;">Posicionamiento prioritario antes que tu competencia</span></td></tr>
                  </table>
                  <table role="presentation" cellspacing="0" cellpadding="0" border="0">
                    <tr>
                      <td style="border-radius:8px; background-color:#D4AF37;">
                        <a href="{{ $premiumUrl }}" target="_blank"
                           style="display:inline-block; padding:14px 32px; font-size:15px; font-weight:700; color:#0B0B0B; text-decoration:none;">
                          Quiero ser el #1 de {{ $restaurant->city }} →
                        </a>
                      </td>
                    </tr>
                  </table>
                </td>
              </tr>
            </table>

            <!-- Urgency note -->
            <p style="margin:0 0 8px; font-size:13px; color:#9CA3AF; text-align:center; line-height:1.6;">
              Tu competencia también está en FAMER. El restaurante que reclame y optimice su perfil primero tendrá ventaja permanente en los rankings de {{ $restaurant->city }}.
            </p>

          </td>
        </tr>

        <!-- GOLD DIVIDER -->
        <tr>
          <td style="background:linear-gradient(90deg,#D4AF37,#F0D060,#D4AF37); height:2px; font-size:0; line-height:0;">&nbsp;</td>
        </tr>

        <!-- FOOTER -->
        <tr>
          <td style="background-color:#0B0B0B; border-radius:0 0 16px 16px; padding:24px 40px; text-align:center;">
            <p style="margin:0 0 8px; font-size:12px; color:#4B5563; line-height:1.6;">
              Recibes este email porque tu restaurante aparece en el directorio FAMER.<br>
              <a href="https://restaurantesmexicanosfamosos.com.mx/unsubscribe?email={{ urlencode($restaurant->email ?? '') }}"
                 style="color:#6B7280; text-decoration:underline;">Cancelar suscripción</a>
            </p>
            <p style="margin:0; font-size:11px; color:#374151;">
              © {{ date('Y') }} Famous Mexican Restaurants · restaurantesmexicanosfamosos.com.mx
            </p>
          </td>
        </tr>

      </table>
    </td>
  </tr>
</table>

</body>
</html>
