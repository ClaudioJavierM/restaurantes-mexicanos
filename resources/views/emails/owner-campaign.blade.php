<!DOCTYPE html>
<html lang="es" xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="format-detection" content="telephone=no">
    <title>{{ $campaignName ?? $restaurant->name ?? 'Mensaje de tu restaurante favorito' }}</title>
</head>
<body style="margin:0; padding:0; background-color:#F5F0E8; font-family:'Segoe UI',Arial,Helvetica,sans-serif; -webkit-text-size-adjust:100%; -ms-text-size-adjust:100%;">

<table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color:#F5F0E8;">
    <tr>
        <td align="center" style="padding:40px 16px;">

            <table role="presentation" width="100%" style="max-width:580px;" cellspacing="0" cellpadding="0" border="0">

                <!-- HEADER — Restaurant branding (dark with gold name) -->
                <tr>
                    <td style="background-color:#0B0B0B; border-radius:16px 16px 0 0; padding:32px 40px; text-align:center;">

                        @if(isset($restaurant) && $restaurant->logo_url)
                        <img src="{{ $restaurant->logo_url }}" alt="{{ $restaurant->name }}"
                             style="max-height:64px; max-width:200px; height:auto; display:block; margin:0 auto 16px;">
                        @endif

                        <p style="margin:0 0 4px; font-size:22px; font-weight:700; color:#D4AF37; line-height:1.2; letter-spacing:0.3px;">
                            {{ $restaurant->name ?? 'Tu restaurante favorito' }}
                        </p>

                        @if(isset($restaurant) && ($restaurant->city || $restaurant->state))
                        <p style="margin:0 0 12px; font-size:12px; color:#9CA3AF; letter-spacing:1px;">
                            {{ trim(($restaurant->city ?? '') . ($restaurant->city && $restaurant->state ? ', ' : '') . ($restaurant->state ?? '')) }}
                        </p>
                        @else
                        <p style="margin:0 0 12px; font-size:0; line-height:0;">&nbsp;</p>
                        @endif

                        <!-- Powered by FAMER -->
                        <p style="margin:0; font-size:10px; color:#4B5563; letter-spacing:2px; text-transform:uppercase;">
                            Enviado a través de
                            <a href="{{ config('app.url') }}" style="color:#D4AF37; text-decoration:none; font-weight:600;">FAMER</a>
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
                        @isset($recipientName)
                        <p style="margin:0 0 20px; font-size:17px; font-weight:600; color:#111827; line-height:1.4;">
                            Hola, {{ $recipientName }}
                        </p>
                        @else
                        <p style="margin:0 0 20px; font-size:17px; font-weight:600; color:#111827; line-height:1.4;">
                            Hola,
                        </p>
                        @endisset

                        @isset($campaignName)
                        <h1 style="margin:0 0 20px; font-size:22px; font-weight:700; color:#111827; line-height:1.3;">
                            {{ $campaignName }}
                        </h1>
                        @endisset

                        <!-- Campaign content -->
                        <div style="font-size:15px; color:#374151; line-height:1.75;">
                            {!! $content !!}
                        </div>

                        @isset($coupon)
                        <!-- Inline coupon (optional) -->
                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="margin:28px 0;">
                            <tr>
                                <td style="background-color:#FBF6E9; border:2px dashed #D4AF37; border-radius:10px; padding:24px; text-align:center;">
                                    <p style="margin:0 0 10px; font-size:11px; font-weight:700; letter-spacing:3px; text-transform:uppercase; color:#92400E;">
                                        Tu código de descuento
                                    </p>
                                    <div style="display:inline-block; background-color:#FFFFFF; border:2px solid #D4AF37; border-radius:8px; padding:12px 28px; margin-bottom:12px;">
                                        <span style="font-size:26px; font-weight:700; color:#D4AF37; letter-spacing:4px; font-family:'Courier New', monospace;">{{ $coupon['code'] }}</span>
                                    </div>
                                    @if(!empty($coupon['discount']))
                                    <p style="margin:0 0 8px; font-size:18px; font-weight:700; color:#111827;">
                                        {{ $coupon['discount'] }} de descuento
                                    </p>
                                    @endif
                                    @if(!empty($coupon['expiry']))
                                    <p style="margin:0; font-size:12px; color:#92400E; font-weight:600;">
                                        Válido hasta: {{ $coupon['expiry'] }}
                                    </p>
                                    @endif
                                </td>
                            </tr>
                        </table>
                        @endisset

                        <!-- CTA Button -->
                        @isset($ctaUrl)
                        <div style="text-align:center; margin:32px 0 8px;">
                            <a href="{{ $ctaUrl }}"
                               style="display:inline-block; background-color:#D4AF37; color:#0B0B0B; text-decoration:none; font-weight:700; font-size:16px; padding:16px 40px; border-radius:10px; font-family:'Segoe UI',Arial,sans-serif; letter-spacing:0.3px;">
                                {{ $ctaText ?? 'Ver más' }}
                            </a>
                        </div>
                        @endisset

                        <!-- Restaurant contact info -->
                        @isset($restaurant)
                        <hr style="border:none; border-top:1px solid #F3F4F6; margin:36px 0 24px;">
                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
                            <tr>
                                <td style="font-size:13px; color:#6B7280; line-height:1.7;">
                                    <strong style="color:#111827; font-size:14px;">{{ $restaurant->name }}</strong><br>
                                    @if($restaurant->address)
                                    {{ $restaurant->address }}@if($restaurant->city || $restaurant->state), {{ trim(($restaurant->city ?? '') . ($restaurant->city && $restaurant->state ? ', ' : '') . ($restaurant->state ?? '')) }}@endif<br>
                                    @endif
                                    @if($restaurant->phone)
                                    Tel: {{ $restaurant->phone }}<br>
                                    @endif
                                </td>
                                <td align="right" valign="top">
                                    <a href="{{ route('restaurant.show', $restaurant->slug) }}"
                                       style="display:inline-block; background-color:#D4AF37; color:#0B0B0B; text-decoration:none; font-weight:700; font-size:13px; padding:10px 20px; border-radius:8px;">
                                        Ver Menú
                                    </a>
                                </td>
                            </tr>
                        </table>
                        @endisset

                    </td>
                </tr>

                <!-- FOOTER -->
                <tr>
                    <td style="background-color:#F9FAFB; border-radius:0 0 16px 16px; padding:28px 40px; text-align:center; border-top:1px solid #F3F4F6;">
                        <p style="margin:0 0 6px; font-size:12px; color:#9CA3AF; text-align:center;">
                            Mensaje enviado por <strong style="color:#6B7280;">{{ $restaurant->name ?? 'tu restaurante' }}</strong><br>
                            a través de FAMER — Restaurantes Mexicanos Famosos
                        </p>
                        <p style="margin:8px 0 0; font-size:11px; color:#D1D5DB; text-align:center;">
                            Recibiste este mensaje porque te suscribiste a novedades de este restaurante.<br>
                            <a href="{{ $unsubscribeUrl ?? config('app.url').'/unsubscribe' }}"
                               style="color:#D4AF37; text-decoration:none;">Cancelar suscripción</a>
                        </p>
                    </td>
                </tr>

            </table>
        </td>
    </tr>
</table>

@isset($trackingPixel)
<img src="{{ $trackingPixel }}" width="1" height="1" style="display:none;" alt="">
@endisset

</body>
</html>
