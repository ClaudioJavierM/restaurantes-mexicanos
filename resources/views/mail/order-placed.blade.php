<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Tu pedido fue recibido — FAMER</title>
    <!--[if mso]>
    <noscript>
        <xml>
            <o:OfficeDocumentSettings>
                <o:PixelsPerInch>96</o:PixelsPerInch>
            </o:OfficeDocumentSettings>
        </xml>
    </noscript>
    <![endif]-->
</head>
<body style="margin:0; padding:0; background-color:#0B0B0B; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; -webkit-text-size-adjust:100%; -ms-text-size-adjust:100%;">

<!-- Preheader (hidden) -->
<div style="display:none; font-size:1px; color:#0B0B0B; line-height:1px; max-height:0; max-width:0; opacity:0; overflow:hidden;">
    Hola {{ $order->customer_name }}, recibimos tu pedido #{{ $order->order_number }} en {{ $order->restaurant->name }}.
</div>

<!-- Wrapper -->
<table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background-color:#0B0B0B;">
    <tr>
        <td align="center" style="padding: 32px 16px;">

            <!-- Container -->
            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="600" style="max-width:600px; width:100%;">

                <!-- ===== HEADER ===== -->
                <tr>
                    <td align="center" style="background-color:#0B0B0B; padding: 40px 40px 32px 40px; border-radius: 12px 12px 0 0;">
                        <a href="https://restaurantesmexicanosfamosos.com" style="text-decoration:none;">
                            <img
                                src="https://restaurantesmexicanosfamosos.com/images/branding/famer55.png"
                                alt="FAMER"
                                width="120"
                                style="display:block; border:0; outline:none; text-decoration:none; width:120px; height:auto;"
                            >
                        </a>
                    </td>
                </tr>

                <!-- ===== HERO BAND ===== -->
                <tr>
                    <td style="background-color:#1A1A1A; padding: 32px 40px; border-left: 4px solid #D4AF37;">
                        <p style="margin:0 0 8px 0; font-size:13px; font-weight:600; color:#D4AF37; letter-spacing:2px; text-transform:uppercase;">
                            Pedido recibido
                        </p>
                        <h1 style="margin:0; font-size:26px; font-weight:700; color:#F5F5F5; line-height:1.3;">
                            ¡Hola, {{ $order->customer_name }}!
                        </h1>
                        <p style="margin:12px 0 0 0; font-size:16px; color:#AAAAAA; line-height:1.6;">
                            Recibimos tu pedido en <strong style="color:#F5F5F5;">{{ $order->restaurant->name }}</strong>.
                            Lo estamos procesando ahora mismo.
                        </p>
                    </td>
                </tr>

                <!-- ===== ORDER META ===== -->
                <tr>
                    <td style="background-color:#141414; padding: 24px 40px;">
                        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                            <tr>
                                <td style="padding: 0 0 8px 0;">
                                    <span style="font-size:12px; color:#888888; letter-spacing:1px; text-transform:uppercase;">Número de pedido</span><br>
                                    <span style="font-size:20px; font-weight:700; color:#D4AF37; letter-spacing:1px;">#{{ $order->order_number }}</span>
                                </td>
                                <td align="right" style="padding: 0 0 8px 0; vertical-align:bottom;">
                                    <!-- Order type badge -->
                                    @php
                                        $typeIcon  = match($order->order_type) { 'pickup' => '🏃', 'delivery' => '🚗', 'dine_in' => '🍽️', default => '📦' };
                                        $typeLabel = match($order->order_type) { 'pickup' => 'Para llevar', 'delivery' => 'Delivery', 'dine_in' => 'Comer aquí', default => $order->order_type };
                                        $typeBg    = match($order->order_type) { 'pickup' => '#1F3D2B', 'delivery' => '#1A2A3D', 'dine_in' => '#3D2A1A', default => '#2A2A2A' };
                                    @endphp
                                    <span style="display:inline-block; background-color:{{ $typeBg }}; color:#F5F5F5; font-size:13px; font-weight:600; padding: 6px 14px; border-radius:20px; white-space:nowrap;">
                                        {{ $typeIcon }} {{ $typeLabel }}
                                    </span>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <!-- ===== ITEMS TABLE ===== -->
                <tr>
                    <td style="background-color:#141414; padding: 0 40px 8px 40px;">
                        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="border-top: 1px solid #2A2A2A;">

                            <!-- Table header -->
                            <tr>
                                <td style="padding: 12px 0 8px 0; font-size:11px; font-weight:600; color:#888888; text-transform:uppercase; letter-spacing:1px;" width="50%">Producto</td>
                                <td style="padding: 12px 0 8px 0; font-size:11px; font-weight:600; color:#888888; text-transform:uppercase; letter-spacing:1px; text-align:center;" width="15%">Cant.</td>
                                <td style="padding: 12px 0 8px 0; font-size:11px; font-weight:600; color:#888888; text-transform:uppercase; letter-spacing:1px; text-align:right;" width="17%">Precio</td>
                                <td style="padding: 12px 0 8px 0; font-size:11px; font-weight:600; color:#888888; text-transform:uppercase; letter-spacing:1px; text-align:right;" width="18%">Total</td>
                            </tr>

                            <!-- Items -->
                            @foreach($order->items as $item)
                            <tr style="border-top: 1px solid #222222;">
                                <td style="padding: 12px 0; font-size:14px; color:#F5F5F5; vertical-align:top; line-height:1.4;">
                                    {{ $item->name }}
                                    @if($item->notes)
                                        <br><span style="font-size:12px; color:#888888;">{{ $item->notes }}</span>
                                    @endif
                                </td>
                                <td style="padding: 12px 0; font-size:14px; color:#CCCCCC; text-align:center; vertical-align:top;">{{ $item->quantity }}</td>
                                <td style="padding: 12px 0; font-size:14px; color:#CCCCCC; text-align:right; vertical-align:top; white-space:nowrap;">${{ number_format($item->unit_price, 2) }}</td>
                                <td style="padding: 12px 0; font-size:14px; color:#F5F5F5; text-align:right; vertical-align:top; white-space:nowrap;">${{ number_format($item->quantity * $item->unit_price, 2) }}</td>
                            </tr>
                            @endforeach

                        </table>
                    </td>
                </tr>

                <!-- ===== ORDER SUMMARY ===== -->
                <tr>
                    <td style="background-color:#141414; padding: 8px 40px 24px 40px;">
                        <table role="presentation" cellspacing="0" cellpadding="0" border="0" align="right" width="240" style="border-top: 1px solid #2A2A2A;">

                            <!-- Subtotal -->
                            <tr>
                                <td style="padding: 10px 0 4px 0; font-size:13px; color:#AAAAAA;">Subtotal</td>
                                <td style="padding: 10px 0 4px 0; font-size:13px; color:#CCCCCC; text-align:right;">${{ number_format($order->subtotal, 2) }}</td>
                            </tr>

                            <!-- Tax -->
                            @if($order->tax > 0)
                            <tr>
                                <td style="padding: 4px 0; font-size:13px; color:#AAAAAA;">Impuestos</td>
                                <td style="padding: 4px 0; font-size:13px; color:#CCCCCC; text-align:right;">${{ number_format($order->tax, 2) }}</td>
                            </tr>
                            @endif

                            <!-- Delivery fee -->
                            @if($order->delivery_fee > 0)
                            <tr>
                                <td style="padding: 4px 0; font-size:13px; color:#AAAAAA;">Envío</td>
                                <td style="padding: 4px 0; font-size:13px; color:#CCCCCC; text-align:right;">${{ number_format($order->delivery_fee, 2) }}</td>
                            </tr>
                            @endif

                            <!-- Tip -->
                            @if(isset($order->tip) && $order->tip > 0)
                            <tr>
                                <td style="padding: 4px 0; font-size:13px; color:#AAAAAA;">Propina</td>
                                <td style="padding: 4px 0; font-size:13px; color:#CCCCCC; text-align:right;">${{ number_format($order->tip, 2) }}</td>
                            </tr>
                            @endif

                            <!-- Discount -->
                            @if(isset($order->discount) && $order->discount > 0)
                            <tr>
                                <td style="padding: 4px 0; font-size:13px; color:#1F3D2B;">Descuento</td>
                                <td style="padding: 4px 0; font-size:13px; color:#4CAF50; text-align:right;">-${{ number_format($order->discount, 2) }}</td>
                            </tr>
                            @endif

                            <!-- Total -->
                            <tr style="border-top: 1px solid #2A2A2A;">
                                <td style="padding: 12px 0 0 0; font-size:16px; font-weight:700; color:#D4AF37;">Total</td>
                                <td style="padding: 12px 0 0 0; font-size:18px; font-weight:700; color:#D4AF37; text-align:right;">${{ number_format($order->total, 2) }}</td>
                            </tr>

                        </table>
                    </td>
                </tr>

                <!-- ===== PAYMENT METHOD ===== -->
                <tr>
                    <td style="background-color:#141414; padding: 0 40px 28px 40px;">
                        @php
                            $paymentIcon  = $order->payment_method === 'card' ? '💳' : '💵';
                            $paymentLabel = $order->payment_method === 'card' ? 'Pago con tarjeta' : 'Pago en efectivo';
                        @endphp
                        <p style="margin:0; font-size:13px; color:#888888;">
                            {{ $paymentIcon }} <span style="color:#CCCCCC;">{{ $paymentLabel }}</span>
                        </p>
                    </td>
                </tr>

                <!-- ===== CTA BUTTON ===== -->
                <tr>
                    <td align="center" style="background-color:#1A1A1A; padding: 32px 40px;">
                        <a
                            href="{{ url('/pedido/' . $order->order_number) }}"
                            style="display:inline-block; background-color:#D4AF37; color:#0B0B0B; font-size:15px; font-weight:700; text-decoration:none; padding: 14px 36px; border-radius:8px; letter-spacing:0.5px;"
                        >
                            Ver estado de tu pedido
                        </a>
                        <p style="margin:16px 0 0 0; font-size:12px; color:#666666;">
                            O copia y pega este enlace: <span style="color:#888888;">{{ url('/pedido/' . $order->order_number) }}</span>
                        </p>
                    </td>
                </tr>

                <!-- ===== RESTAURANT INFO ===== -->
                @if($order->restaurant->address)
                <tr>
                    <td style="background-color:#141414; padding: 20px 40px; border-top: 1px solid #222222;">
                        <p style="margin:0 0 4px 0; font-size:11px; color:#888888; text-transform:uppercase; letter-spacing:1px;">Restaurante</p>
                        <p style="margin:0; font-size:14px; color:#F5F5F5; font-weight:600;">{{ $order->restaurant->name }}</p>
                        <p style="margin:4px 0 0 0; font-size:13px; color:#888888;">{{ $order->restaurant->address }}</p>
                    </td>
                </tr>
                @endif

                <!-- ===== FOOTER ===== -->
                <tr>
                    <td align="center" style="background-color:#0B0B0B; padding: 32px 40px; border-top: 1px solid #1A1A1A; border-radius: 0 0 12px 12px;">
                        <p style="margin:0 0 8px 0; font-size:12px; color:#555555;">
                            Powered by <a href="https://restaurantesmexicanosfamosos.com" style="color:#D4AF37; text-decoration:none;">FAMER</a>
                            &nbsp;•&nbsp;
                            <a href="https://restaurantesmexicanosfamosos.com" style="color:#555555; text-decoration:none;">restaurantesmexicanosfamosos.com</a>
                        </p>
                        <p style="margin:0; font-size:11px; color:#444444;">
                            ¿No solicitaste este pedido? <a href="mailto:soporte@restaurantesmexicanosfamosos.com" style="color:#666666;">Contáctanos</a>
                        </p>
                    </td>
                </tr>

            </table>
            <!-- /Container -->

        </td>
    </tr>
</table>
<!-- /Wrapper -->

</body>
</html>
