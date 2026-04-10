<!DOCTYPE html>
<html lang="es" xmlns="http://www.w3.org/1999/xhtml">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="format-detection" content="telephone=no">
  <title>Tu FAMER Score — {{ $restaurant['name'] ?? $restaurant->name ?? 'Reporte' }}</title>
</head>
<body style="margin:0; padding:0; background-color:#F5F0E8; font-family:'Segoe UI',Arial,Helvetica,sans-serif; -webkit-text-size-adjust:100%; -ms-text-size-adjust:100%;">

<!-- Preview text -->
<div style="display:none; max-height:0; overflow:hidden;">
  Tu FAMER Score {{ isset($score) ? 'es ' . $score : (isset($scoreData['overall_score']) ? 'es ' . $scoreData['overall_score'] : '') }} — revisa tu reporte y recomendaciones para mejorar tu posición.
</div>

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
            Tu reporte de FAMER Score
          </h1>
          <p style="margin:0 0 6px; font-size:16px; color:#374151; line-height:1.7;">
            @isset($recipientName)
            Hola {{ $recipientName }},
            @else
            Hola,
            @endisset
          </p>
          <p style="margin:0 0 4px; font-size:15px; color:#6B7280; line-height:1.7;">
            Aquí está el reporte de
            @if(is_array($restaurant))
              <strong style="color:#111827;">{{ $restaurant['name'] ?? 'tu restaurante' }}</strong>
              @if(isset($restaurant['city']))
              — {{ $restaurant['city'] }}{{ isset($restaurant['state']) ? ', ' . $restaurant['state'] : '' }}
              @endif
            @else
              <strong style="color:#111827;">{{ $restaurant->name ?? 'tu restaurante' }}</strong>
              @if(isset($restaurant->city))
              — {{ $restaurant->city }}{{ isset($restaurant->state) ? ', ' . $restaurant->state : '' }}
              @endif
            @endif
          </p>
          <p style="margin:0 0 28px; font-size:12px; color:#9CA3AF;">Generado el {{ now()->format('d \d\e F \d\e Y') }}</p>

          <!-- Score principal -->
          <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="margin-bottom:24px;">
            <tr>
              <td style="background-color:#0B0B0B; border-radius:14px; padding:32px 24px; text-align:center;">
                @php
                  $scoreVal = $score ?? $scoreData['overall_score'] ?? 0;
                  $scoreGrade = $scoreData['letter_grade'] ?? ($scoreVal >= 90 ? 'A' : ($scoreVal >= 80 ? 'B' : ($scoreVal >= 70 ? 'C' : ($scoreVal >= 60 ? 'D' : 'F'))));
                  $scoreDesc = $scoreData['score_description'] ?? ($scoreVal >= 80 ? 'Excelente' : ($scoreVal >= 60 ? 'Bueno' : 'Necesita mejoras'));
                @endphp
                <p style="margin:0 0 4px; font-size:68px; font-weight:800; color:#D4AF37; line-height:1;">{{ $scoreVal }}</p>
                <p style="margin:0 0 8px; font-size:13px; color:#9CA3AF; letter-spacing:1px; text-transform:uppercase;">de 100 puntos</p>
                <span style="display:inline-block; background-color:#D4AF37; color:#0B0B0B; font-size:22px; font-weight:800; padding:6px 24px; border-radius:30px; letter-spacing:1px;">{{ $scoreGrade }}</span>
                <p style="margin:12px 0 0; font-size:14px; color:#9CA3AF;">{{ $scoreDesc }}</p>
              </td>
            </tr>
          </table>

          <!-- Cambio vs mes anterior -->
          @if(isset($scoreChange))
          <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="margin-bottom:24px;">
            <tr>
              <td style="background-color:{{ $scoreChange >= 0 ? '#F0FDF4' : '#FEF2F2' }}; border-radius:10px; padding:14px 20px; text-align:center;">
                <p style="margin:0; font-size:15px; font-weight:700; color:{{ $scoreChange >= 0 ? '#166534' : '#991B1B' }};">
                  {{ $scoreChange >= 0 ? '+' : '' }}{{ $scoreChange }} puntos vs. el mes anterior
                  {{ $scoreChange >= 0 ? '— Tu score está mejorando' : '— Hay oportunidad de mejora' }}
                </p>
              </td>
            </tr>
          </table>
          @endif

          <!-- Posición en ranking -->
          @if(isset($rankPosition))
          <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="margin-bottom:24px;">
            <tr>
              <td style="background-color:#FBF6E9; border-radius:10px; padding:14px 20px; text-align:center;">
                <p style="margin:0; font-size:14px; color:#78540A; line-height:1.6;">
                  Posición actual en el directorio:
                  <strong style="font-size:18px; color:#D4AF37;">#{{ $rankPosition }}</strong>
                  @isset($rankTotal)
                  de {{ number_format($rankTotal) }} restaurantes
                  @endisset
                </p>
              </td>
            </tr>
          </table>
          @endif

          @if(!isset($rankPosition) && !isset($scoreChange))
          <div style="margin-bottom:24px;"></div>
          @endif

          <!-- Desglose por categorías -->
          @if(!empty($scoreData['categories']))
          <hr style="border:none; border-top:1px solid #F3F4F6; margin:0 0 24px;">
          <p style="margin:0 0 16px; font-size:13px; font-weight:700; color:#D4AF37; letter-spacing:2px; text-transform:uppercase;">Desglose por categoría</p>

          @foreach($scoreData['categories'] as $category)
          @php $catScore = $category['score'] ?? 0; @endphp
          <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="margin-bottom:14px;">
            <tr>
              <td>
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="margin-bottom:5px;">
                  <tr>
                    <td>
                      <span style="font-size:14px; font-weight:600; color:#374151;">{{ $category['name'] }}</span>
                      @isset($category['weight'])
                      <span style="font-size:12px; color:#9CA3AF;"> ({{ $category['weight'] }}%)</span>
                      @endisset
                    </td>
                    <td align="right">
                      <span style="font-size:14px; font-weight:700; color:#111827;">{{ $catScore }}/100</span>
                    </td>
                  </tr>
                </table>
                <div style="background-color:#F3F4F6; border-radius:4px; height:8px; overflow:hidden;">
                  <div style="background-color:{{ $catScore >= 70 ? '#D4AF37' : ($catScore >= 50 ? '#F59E0B' : '#EF4444') }}; width:{{ $catScore }}%; height:8px; border-radius:4px;"></div>
                </div>
              </td>
            </tr>
          </table>
          @endforeach
          @endif

          <!-- Insights / Recomendaciones -->
          @php
            $recs = $insights ?? $scoreData['all_recommendations'] ?? $scoreData['top_recommendations'] ?? [];
          @endphp
          @if(!empty($recs))
          <hr style="border:none; border-top:1px solid #F3F4F6; margin:24px 0;">
          <p style="margin:0 0 16px; font-size:13px; font-weight:700; color:#D4AF37; letter-spacing:2px; text-transform:uppercase;">Recomendaciones para mejorar</p>

          @foreach($recs as $rec)
          <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="margin-bottom:12px;">
            <tr>
              <td style="background-color:#FAFAFA; border-left:3px solid #D4AF37; border-radius:0 8px 8px 0; padding:14px 18px;">
                @if(is_array($rec))
                  <p style="margin:0 0 4px; font-size:14px; font-weight:700; color:#111827;">{{ $rec['title'] ?? '' }}</p>
                  @isset($rec['description'])
                  <p style="margin:0; font-size:13px; color:#6B7280; line-height:1.5;">{{ $rec['description'] }}</p>
                  @endisset
                @else
                  <p style="margin:0; font-size:14px; color:#374151;">{{ $rec }}</p>
                @endif
              </td>
            </tr>
          </table>
          @endforeach
          @endif

          <div style="margin-top:32px;"></div>

          <!-- CTA -->
          <div style="text-align:center; margin-bottom:32px;">
            <a href="{{ $analyticsUrl ?? config('app.url') . '/owner/analytics' }}"
               style="display:inline-block; background-color:#D4AF37; color:#0B0B0B; text-decoration:none; font-weight:700; font-size:16px; padding:16px 40px; border-radius:10px; font-family:'Segoe UI',Arial,sans-serif; letter-spacing:0.3px;">
              Ver Mi Analytics
            </a>
          </div>

          <p style="margin:0; font-size:13px; color:#9CA3AF; text-align:center; line-height:1.6;">
            Este reporte se actualiza mensualmente. Las mejoras en tu perfil se reflejan en el próximo ciclo.
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
            <a href="{{ config('app.url') }}/unsubscribe?email={{ urlencode($email ?? $ownerEmail ?? $unsubscribeUrl ?? '') }}"
               style="color:#D4AF37; text-decoration:none;">Cancelar notificaciones</a>
          </p>
        </td>
      </tr>

    </table>
  </td></tr>
</table>

</body>
</html>
