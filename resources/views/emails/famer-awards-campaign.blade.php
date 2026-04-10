<!DOCTYPE html>
<html lang="es" xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="format-detection" content="telephone=no">
    <title>FAMER Awards {{ $awardYear ?? date('Y') }} — {{ $restaurant->name ?? 'Tu restaurante' }}</title>
</head>
<body style="margin:0; padding:0; background-color:#F5F0E8; font-family:'Segoe UI',Arial,Helvetica,sans-serif; -webkit-text-size-adjust:100%; -ms-text-size-adjust:100%;">

<table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color:#F5F0E8;">
    <tr>
        <td align="center" style="padding:40px 16px;">

            <table role="presentation" width="100%" style="max-width:580px;" cellspacing="0" cellpadding="0" border="0">

                <!-- HEADER -->
                <tr>
                    <td style="background-color:#0B0B0B; border-radius:16px 16px 0 0; padding:32px 40px; text-align:center;">
                        <img src="https://restaurantesmexicanosfamosos.com.mx/images/branding/logo-horizontal.png"
                             alt="FAMER" width="160" style="max-width:160px; height:auto; display:block; margin:0 auto 20px;">

                        <!-- Awards badge -->
                        <div style="display:inline-block; background:linear-gradient(135deg, #D4AF37, #F0D060, #D4AF37); border-radius:30px; padding:8px 28px; margin-bottom:12px;">
                            <span style="font-size:13px; font-weight:700; color:#0B0B0B; letter-spacing:3px; text-transform:uppercase;">FAMER Awards</span>
                        </div>
                        <p style="margin:0; color:#D4AF37; font-size:13px; font-weight:700; letter-spacing:4px;">
                            &#9733;&nbsp;{{ $awardYear ?? date('Y') }}&nbsp;&#9733;
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

                        <!-- Greeting -->
                        <p style="margin:0 0 6px; font-size:14px; color:#9CA3AF; text-align:center; letter-spacing:1px; text-transform:uppercase; font-weight:600;">
                            Una distinción especial para
                        </p>
                        <h1 style="margin:0 0 28px; font-size:26px; font-weight:700; color:#111827; text-align:center; line-height:1.3;">
                            {{ $restaurant->name ?? ($recipientName ?? 'tu restaurante') }}
                        </h1>

                        <!-- Award highlight box -->
                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="margin:0 0 32px;">
                            <tr>
                                <td style="background-color:#0B0B0B; border-radius:12px; padding:32px 28px; text-align:center;">

                                    <p style="margin:0 0 12px; font-size:11px; font-weight:700; letter-spacing:3px; text-transform:uppercase; color:#D4AF37;">
                                        Distinción FAMER Awards {{ $awardYear ?? date('Y') }}
                                    </p>

                                    <p style="margin:0 0 16px; font-size:24px; font-weight:700; color:#FFFFFF; line-height:1.3;">
                                        {{ $awardName ?? 'Restaurante Destacado' }}
                                    </p>

                                    <!-- Decorative line -->
                                    <div style="width:60px; height:2px; background:linear-gradient(90deg, #D4AF37, #F0D060); margin:0 auto 16px;"></div>

                                    @isset($badgeUrl)
                                    <img src="{{ $badgeUrl }}" alt="Insignia FAMER Awards" width="120"
                                         style="max-width:120px; height:auto; display:block; margin:0 auto;">
                                    @else
                                    <p style="margin:0; font-size:40px; line-height:1;">&#127942;</p>
                                    @endisset

                                </td>
                            </tr>
                        </table>

                        <!-- Body copy -->
                        <p style="margin:0 0 20px; font-size:15px; color:#374151; line-height:1.7; text-align:center;">
                            Los <strong style="color:#111827;">FAMER Awards {{ $awardYear ?? date('Y') }}</strong> reconocen a los restaurantes mexicanos que destacan por su autenticidad, calidad y compromiso con la experiencia gastronómica.
                        </p>

                        <p style="margin:0 0 32px; font-size:15px; color:#374151; line-height:1.7; text-align:center;">
                            Tu restaurante ha sido seleccionado entre miles de establecimientos en todo Estados Unidos. Esta distinción certifica la excelencia de
                            <strong style="color:#111827;">{{ $restaurant->name ?? 'tu negocio' }}</strong>.
                        </p>

                        <!-- What this means -->
                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color:#FBF6E9; border-radius:10px; margin:0 0 32px;">
                            <tr>
                                <td style="padding:24px 28px;">
                                    <p style="margin:0 0 16px; font-size:13px; font-weight:700; letter-spacing:2px; text-transform:uppercase; color:#92400E;">
                                        Qué incluye tu distinción
                                    </p>
                                    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
                                        <tr>
                                            <td style="padding:6px 0; font-size:14px; color:#374151; line-height:1.5;">
                                                <span style="color:#D4AF37; font-weight:700; margin-right:8px;">&#10003;</span>
                                                Insignia oficial FAMER Awards en tu perfil
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="padding:6px 0; font-size:14px; color:#374151; line-height:1.5;">
                                                <span style="color:#D4AF37; font-weight:700; margin-right:8px;">&#10003;</span>
                                                Posición destacada en búsquedas de FAMER
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="padding:6px 0; font-size:14px; color:#374151; line-height:1.5;">
                                                <span style="color:#D4AF37; font-weight:700; margin-right:8px;">&#10003;</span>
                                                Mención en la galería de premiados del año
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="padding:6px 0; font-size:14px; color:#374151; line-height:1.5;">
                                                <span style="color:#D4AF37; font-weight:700; margin-right:8px;">&#10003;</span>
                                                Material digital para compartir en redes sociales
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>

                        <!-- CTA -->
                        <div style="text-align:center; margin:0 0 8px;">
                            <a href="{{ isset($restaurant) ? route('restaurant.show', $restaurant->slug) : config('app.url') }}"
                               style="display:inline-block; background-color:#D4AF37; color:#0B0B0B; text-decoration:none; font-weight:700; font-size:16px; padding:16px 40px; border-radius:10px; font-family:'Segoe UI',Arial,sans-serif; letter-spacing:0.3px;">
                                Ver Mi Distinción
                            </a>
                        </div>

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
