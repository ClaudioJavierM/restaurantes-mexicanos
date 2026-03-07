<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $restaurant->name }}</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f4f4f4; font-family: Arial, sans-serif;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #f4f4f4;">
        <tr>
            <td align="center" style="padding: 40px 20px;">
                <table role="presentation" width="600" cellspacing="0" cellpadding="0" style="background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                    {{-- Header --}}
                    <tr>
                        <td style="background: linear-gradient(135deg, #dc2626, #ea580c); padding: 30px; text-align: center;">
                            <img src="https://restaurantesmexicanosfamosos.com/images/branding/logo.png?v=3" alt="Restaurantes Mexicanos Famosos" style="max-height: 50px; width: auto; margin-bottom: 12px;" />
                            @if($restaurant->logo_url)
                                <img src="{{ $restaurant->logo_url }}" alt="{{ $restaurant->name }}" style="max-height: 60px; margin-bottom: 15px;">
                            @endif
                            <h1 style="color: #ffffff; margin: 0; font-size: 24px;">{{ $restaurant->name }}</h1>
                        </td>
                    </tr>
                    
                    {{-- Content --}}
                    <tr>
                        <td style="padding: 40px 30px;">
                            <div style="color: #333333; font-size: 16px; line-height: 1.6;">
                                {!! nl2br(e($content)) !!}
                            </div>
                            
                            @if($coupon)
                                <div style="margin: 30px 0; padding: 20px; background-color: #fef3c7; border: 2px dashed #f59e0b; border-radius: 8px; text-align: center;">
                                    <p style="margin: 0 0 10px; font-size: 14px; color: #92400e;">Tu codigo de descuento:</p>
                                    <p style="margin: 0; font-size: 28px; font-weight: bold; color: #d97706; letter-spacing: 2px;">{{ $coupon['code'] }}</p>
                                    @if($coupon['discount'])
                                        <p style="margin: 10px 0 0; font-size: 18px; color: #b45309;">{{ $coupon['discount'] }} de descuento</p>
                                    @endif
                                    @if($coupon['expiry'])
                                        <p style="margin: 10px 0 0; font-size: 12px; color: #92400e;">Valido hasta: {{ $coupon['expiry'] }}</p>
                                    @endif
                                </div>
                            @endif
                        </td>
                    </tr>
                    
                    {{-- Restaurant Info --}}
                    <tr>
                        <td style="padding: 20px 30px; background-color: #f9fafb; border-top: 1px solid #e5e7eb;">
                            <table width="100%" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td style="color: #6b7280; font-size: 14px;">
                                        <p style="margin: 0 0 5px;"><strong>{{ $restaurant->name }}</strong></p>
                                        @if($restaurant->address)
                                            <p style="margin: 0 0 5px;">{{ $restaurant->address }}, {{ $restaurant->city }}, {{ $restaurant->state }}</p>
                                        @endif
                                        @if($restaurant->phone)
                                            <p style="margin: 0;">Tel: {{ $restaurant->phone }}</p>
                                        @endif
                                    </td>
                                    <td align="right" style="vertical-align: top;">
                                        <a href="{{ route('restaurant.show', $restaurant->slug) }}" style="display: inline-block; padding: 10px 20px; background-color: #dc2626; color: #ffffff; text-decoration: none; border-radius: 5px; font-size: 14px;">Ver Menu</a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    
                    {{-- Footer --}}
                    <tr>
                        <td style="padding: 20px 30px; text-align: center; border-top: 1px solid #e5e7eb;">
                            <p style="margin: 0 0 10px; font-size: 12px; color: #9ca3af;">
                                Recibiste este email porque estas suscrito a {{ $restaurant->name }}.
                            </p>
                            <p style="margin: 0; font-size: 12px;">
                                <a href="{{ $unsubscribeUrl }}" style="color: #6b7280;">Cancelar suscripcion</a>
                            </p>
                        </td>
                    </tr>
                </table>
                
                {{-- FAMER Branding --}}
                <p style="margin: 20px 0 0; font-size: 11px; color: #9ca3af;">
                    Enviado via <a href="{{ url('/') }}" style="color: #dc2626;">FAMER</a> - Restaurantes Mexicanos Famosos
                </p>
            </td>
        </tr>
    </table>
    
    {{-- Tracking Pixel --}}
    <img src="{{ $trackingPixel }}" width="1" height="1" style="display: none;" alt="">
</body>
</html>
