<!DOCTYPE html>
<html lang="es" xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="format-detection" content="telephone=no">
    <title>{{ $subject ?? ($campaignName ?? 'FAMER') }}</title>
    @isset($previewText)
    <span style="display:none !important; visibility:hidden; mso-hide:all; font-size:1px; color:#F5F0E8; line-height:1px; max-height:0; max-width:0; opacity:0; overflow:hidden;">{{ $previewText }}</span>
    @endisset
</head>
<body style="margin:0; padding:0; background-color:#F5F0E8; font-family:'Segoe UI',Arial,Helvetica,sans-serif; -webkit-text-size-adjust:100%; -ms-text-size-adjust:100%;">

<table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color:#F5F0E8;">
    <tr>
        <td align="center" style="padding:40px 16px;">

            <table role="presentation" width="100%" style="max-width:580px;" cellspacing="0" cellpadding="0" border="0">

                @if($isTest ?? false)
                <!-- TEST BANNER -->
                <tr>
                    <td style="background-color:#FEF3C7; border:1px solid #F59E0B; border-radius:8px 8px 0 0; padding:12px 20px; text-align:center;">
                        <p style="margin:0; font-size:13px; color:#92400E; font-weight:600;">
                            Modo prueba — Este mensaje no fue enviado a destinatarios reales
                        </p>
                    </td>
                </tr>
                @endif

                <!-- HEADER -->
                <tr>
                    <td style="background-color:#0B0B0B; border-radius:16px 16px 0 0; padding:32px 40px; text-align:center;">
                        <img src="https://restaurantesmexicanosfamosos.com.mx/images/branding/logo-horizontal.png"
                             alt="FAMER" width="160" style="max-width:160px; height:auto; display:block; margin:0 auto 16px;">
                        <p style="margin:0; color:#D4AF37; font-size:11px; font-weight:700; letter-spacing:3px; text-transform:uppercase;">
                            Famous Mexican Restaurants
                        </p>
                    </td>
                </tr>

                <!-- GOLD DIVIDER -->
                <tr>
                    <td style="background:linear-gradient(90deg, #D4AF37, #F0D060, #D4AF37); height:3px; font-size:0; line-height:0;">&nbsp;</td>
                </tr>

                <!-- BODY -->
                <tr>
                    <td style="background-color:#FFFFFF; padding:48px 40px 40px;">

                        @isset($campaignName)
                        <h1 style="margin:0 0 24px; font-size:24px; font-weight:700; color:#111827; font-family:'Segoe UI',Arial,sans-serif; line-height:1.3;">
                            {{ $campaignName }}
                        </h1>
                        @endisset

                        @isset($restaurant)
                        <p style="margin:0 0 20px; font-size:15px; color:#6B7280; line-height:1.6;">
                            Hola <strong style="color:#111827;">{{ $restaurant->name }}</strong>,
                        </p>
                        @endisset

                        <!-- Dynamic content -->
                        <div style="font-size:15px; color:#374151; line-height:1.7;">
                            {!! $content ?? $htmlContent ?? '' !!}
                        </div>

                        @isset($ctaUrl)
                        <!-- CTA Button -->
                        <div style="text-align:center; margin:36px 0 8px;">
                            <a href="{{ $ctaUrl }}"
                               style="display:inline-block; background-color:#D4AF37; color:#0B0B0B; text-decoration:none; font-weight:700; font-size:16px; padding:16px 40px; border-radius:10px; font-family:'Segoe UI',Arial,sans-serif; letter-spacing:0.3px;">
                                {{ $ctaText ?? 'Ver más' }}
                            </a>
                        </div>
                        @endisset

                    </td>
                </tr>

                <!-- FOOTER -->
                <tr>
                    <td style="background-color:#F9FAFB; border-radius:0 0 16px 16px; padding:28px 40px; text-align:center; border-top:1px solid #F3F4F6;">
                        <p style="margin:0 0 8px; font-size:12px; color:#9CA3AF; text-align:center;">
                            © {{ date('Y') }} FAMER — Restaurantes Mexicanos Famosos<br>
                            El directorio líder de restaurantes mexicanos en Estados Unidos
                        </p>
                        <p style="margin:0; font-size:11px; color:#D1D5DB; text-align:center;">
                            Recibiste este mensaje como parte de nuestra lista de restaurantes.<br>
                            <a href="{{ $unsubscribeUrl ?? config('app.url').'/unsubscribe' }}"
                               style="color:#D4AF37; text-decoration:none;">Cancelar suscripción</a>
                        </p>
                    </td>
                </tr>

            </table>
        </td>
    </tr>
</table>

@isset($trackingPixelUrl)
<img src="{{ $trackingPixelUrl }}" width="1" height="1" style="display:none;" alt="">
@endisset

</body>
</html>
