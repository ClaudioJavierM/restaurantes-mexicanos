<!DOCTYPE html>
<html lang="es" xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="format-detection" content="telephone=no">
    <title>Oferta exclusiva de {{ $restaurant->name ?? ($restaurantName ?? 'tu restaurante favorito') }}</title>
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

                        <!-- Greeting -->
                        <p style="margin:0 0 6px; font-size:22px; font-weight:700; color:#111827; line-height:1.3;">
                            Hola{{ isset($recipientName) ? ', '.$recipientName : '' }},
                        </p>
                        <p style="margin:0 0 28px; font-size:15px; color:#6B7280; line-height:1.7;">
                            <strong style="color:#111827;">{{ $restaurant->name ?? $restaurantName ?? 'Tu restaurante favorito' }}</strong>
                            tiene una oferta especial preparada para ti.
                        </p>

                        <!-- Coupon Box -->
                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="margin:0 0 32px;">
                            <tr>
                                <td style="background-color:#FBF6E9; border:2px dashed #D4AF37; border-radius:12px; padding:32px 28px; text-align:center;">

                                    <p style="margin:0 0 12px; font-size:11px; font-weight:700; letter-spacing:3px; text-transform:uppercase; color:#92400E;">
                                        Tu código de descuento exclusivo
                                    </p>

                                    <!-- Coupon code -->
                                    <div style="display:inline-block; background-color:#FFFFFF; border:2px solid #D4AF37; border-radius:8px; padding:14px 32px; margin:0 0 16px;">
                                        <span style="font-size:30px; font-weight:700; color:#D4AF37; letter-spacing:4px; font-family:'Courier New', monospace;">{{ $couponCode }}</span>
                                    </div>

                                    @isset($couponDiscount)
                                    <p style="margin:0 0 12px; font-size:26px; font-weight:700; color:#111827;">
                                        {{ $couponDiscount }} de descuento
                                    </p>
                                    @endisset

                                    @isset($couponExpiry)
                                    <p style="margin:0; font-size:13px; color:#92400E; font-weight:600;">
                                        Válido hasta: {{ $couponExpiry }}
                                    </p>
                                    @endisset

                                </td>
                            </tr>
                        </table>

                        <!-- How to use -->
                        <h2 style="margin:0 0 16px; font-size:17px; font-weight:700; color:#111827;">
                            Cómo usar tu descuento
                        </h2>

                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="margin:0 0 32px;">
                            <tr>
                                <td style="padding:10px 0; border-bottom:1px solid #F3F4F6;">
                                    <table role="presentation" cellspacing="0" cellpadding="0" border="0">
                                        <tr>
                                            <td width="32" valign="top" style="padding-top:2px;">
                                                <div style="width:22px; height:22px; background-color:#D4AF37; border-radius:50%; text-align:center; line-height:22px; font-size:12px; font-weight:700; color:#0B0B0B;">1</div>
                                            </td>
                                            <td style="font-size:14px; color:#374151; padding-left:8px; line-height:1.6;">
                                                Visita el perfil del restaurante en FAMER o acércate directamente.
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <td style="padding:10px 0; border-bottom:1px solid #F3F4F6;">
                                    <table role="presentation" cellspacing="0" cellpadding="0" border="0">
                                        <tr>
                                            <td width="32" valign="top" style="padding-top:2px;">
                                                <div style="width:22px; height:22px; background-color:#D4AF37; border-radius:50%; text-align:center; line-height:22px; font-size:12px; font-weight:700; color:#0B0B0B;">2</div>
                                            </td>
                                            <td style="font-size:14px; color:#374151; padding-left:8px; line-height:1.6;">
                                                Presenta el código <strong style="color:#D4AF37;">{{ $couponCode }}</strong> al momento de pagar o al hacer tu reservación.
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <td style="padding:10px 0;">
                                    <table role="presentation" cellspacing="0" cellpadding="0" border="0">
                                        <tr>
                                            <td width="32" valign="top" style="padding-top:2px;">
                                                <div style="width:22px; height:22px; background-color:#D4AF37; border-radius:50%; text-align:center; line-height:22px; font-size:12px; font-weight:700; color:#0B0B0B;">3</div>
                                            </td>
                                            <td style="font-size:14px; color:#374151; padding-left:8px; line-height:1.6;">
                                                Disfruta tu descuento. Una sola aplicación por visita.
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>

                        <!-- CTA -->
                        <div style="text-align:center; margin:0 0 32px;">
                            <a href="{{ isset($restaurant) ? route('restaurant.show', $restaurant->slug) : config('app.url') }}"
                               style="display:inline-block; background-color:#D4AF37; color:#0B0B0B; text-decoration:none; font-weight:700; font-size:16px; padding:16px 40px; border-radius:10px; font-family:'Segoe UI',Arial,sans-serif; letter-spacing:0.3px;">
                                Ver Menú y Reservar
                            </a>
                        </div>

                        <!-- Restaurant info -->
                        @isset($restaurant)
                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color:#F9FAFB; border-radius:10px; padding:20px;">
                            <tr>
                                <td style="padding:20px;">
                                    <p style="margin:0 0 4px; font-size:14px; font-weight:700; color:#111827;">{{ $restaurant->name }}</p>
                                    @if($restaurant->address)
                                    <p style="margin:0 0 2px; font-size:13px; color:#6B7280;">{{ $restaurant->address }}, {{ $restaurant->city }}, {{ $restaurant->state }}</p>
                                    @endif
                                    @if($restaurant->phone)
                                    <p style="margin:0; font-size:13px; color:#6B7280;">Tel: {{ $restaurant->phone }}</p>
                                    @endif
                                </td>
                            </tr>
                        </table>
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

</body>
</html>
