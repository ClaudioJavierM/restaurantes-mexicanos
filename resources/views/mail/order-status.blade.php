<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Actualización de tu pedido — FAMER</title>
</head>
<body style="margin:0; padding:0; background-color:#0B0B0B; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; -webkit-text-size-adjust:100%; -ms-text-size-adjust:100%;">

@php
    /*
     * Status-driven design tokens
     */
    $config = match($order->status) {
        'confirmed' => [
            'accent'   => '#D4AF37',   // gold
            'bg'       => '#1A1800',
            'icon'     => '✅',
            'heading'  => '¡Pedido confirmado!',
            'body'     => 'El restaurante ya tiene tu pedido y lo está preparando. Te avisaremos cuando esté listo.',
            'cta'      => 'Ver mi pedido',
        ],
        'preparing' => [
            'accent'   => '#4A9EFF',   // blue
            'bg'       => '#001A2A',
            'icon'     => '👨‍🍳',
            'heading'  => 'Tu pedido está en preparación',
            'body'     => 'El equipo de cocina está trabajando en tu pedido. Pronto te daremos más noticias.',
            'cta'      => 'Ver estado',
        ],
        'ready' => [
            'accent'   => '#4CAF50',   // green
            'bg'       => '#001A00',
            'icon'     => '🔔',
            'heading'  => '¡Tu pedido está listo!',
            'body'     => $order->order_type === 'delivery'
                ? 'Tu pedido está empacado y esperando al repartidor.'
                : 'Puedes pasar a recogerlo cuando quieras.',
            'cta'      => 'Ver detalles',
        ],
        'out_for_delivery' => [
            'accent'   => '#FF9800',   // orange
            'bg'       => '#1A0F00',
            'icon'     => '🚗',
            'heading'  => '¡Tu pedido está en camino!',
            'body'     => 'El repartidor ya salió con tu pedido. Estará en tu puerta muy pronto.',
            'cta'      => 'Rastrear pedido',
        ],
        'completed' => [
            'accent'   => '#D4AF37',   // gold
            'bg'       => '#1A1800',
            'icon'     => '⭐',
            'heading'  => '¡Gracias por tu pedido!',
            'body'     => '¿Qué tal estuvo tu experiencia? Tu opinión ayuda a otros comensales a descubrir grandes restaurantes.',
            'cta'      => 'Deja una reseña',
        ],
        'cancelled' => [
            'accent'   => '#E53E3E',   // red
            'bg'       => '#1A0000',
            'icon'     => '❌',
            'heading'  => 'Pedido cancelado',
            'body'     => 'Tu pedido fue cancelado.'
                . ($order->cancellation_reason ? ' Motivo: ' . $order->cancellation_reason . '.' : '')
                . ' Si tienes alguna duda, contáctanos.',
            'cta'      => 'Contactar soporte',
        ],
        default => [
            'accent'   => '#D4AF37',
            'bg'       => '#1A1A1A',
            'icon'     => '📋',
            'heading'  => 'Actualización de tu pedido',
            'body'     => 'El estado de tu pedido ha cambiado.',
            'cta'      => 'Ver pedido',
        ],
    };

    // CTA URL
    $ctaUrl = $order->status === 'completed'
        ? url('/restaurante/' . ($order->restaurant->slug ?? $order->restaurant_id))
        : url('/pedido/' . $order->order_number);

    if ($order->status === 'cancelled') {
        $ctaUrl = 'mailto:soporte@restaurantesmexicanosfamosos.com';
    }
@endphp

<!-- Preheader (hidden) -->
<div style="display:none; font-size:1px; color:#0B0B0B; line-height:1px; max-height:0; max-width:0; opacity:0; overflow:hidden;">
    {{ $config['heading'] }} — Pedido #{{ $order->order_number }} en {{ $order->restaurant->name }}.
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

                <!-- ===== STATUS HERO ===== -->
                <tr>
                    <td style="background-color:{{ $config['bg'] }}; padding: 36px 40px; border-left: 4px solid {{ $config['accent'] }};">
                        <p style="margin:0 0 12px 0; font-size:40px; line-height:1;">{{ $config['icon'] }}</p>
                        <h1 style="margin:0 0 12px 0; font-size:24px; font-weight:700; color:#F5F5F5; line-height:1.3;">
                            {{ $config['heading'] }}
                        </h1>
                        <p style="margin:0; font-size:15px; color:#AAAAAA; line-height:1.6;">
                            {{ $config['body'] }}
                        </p>
                    </td>
                </tr>

                <!-- ===== ORDER SUMMARY ===== -->
                <tr>
                    <td style="background-color:#141414; padding: 24px 40px;">
                        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">

                            <!-- Order number -->
                            <tr>
                                <td style="padding: 0 0 16px 0; border-bottom: 1px solid #222222;">
                                    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                        <tr>
                                            <td>
                                                <span style="font-size:11px; color:#888888; text-transform:uppercase; letter-spacing:1px;">Pedido</span><br>
                                                <span style="font-size:18px; font-weight:700; color:{{ $config['accent'] }};">#{{ $order->order_number }}</span>
                                            </td>
                                            <td align="right">
                                                <span style="font-size:11px; color:#888888; text-transform:uppercase; letter-spacing:1px;">Estado</span><br>
                                                <span style="font-size:14px; font-weight:600; color:#F5F5F5;">{{ $order->status_label }}</span>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>

                            <!-- Restaurant -->
                            <tr>
                                <td style="padding: 16px 0 0 0;">
                                    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                        <tr>
                                            <td>
                                                <span style="font-size:11px; color:#888888; text-transform:uppercase; letter-spacing:1px;">Restaurante</span><br>
                                                <span style="font-size:14px; color:#F5F5F5; font-weight:600;">{{ $order->restaurant->name }}</span>
                                            </td>
                                            <td align="right">
                                                <span style="font-size:11px; color:#888888; text-transform:uppercase; letter-spacing:1px;">Total</span><br>
                                                <span style="font-size:16px; font-weight:700; color:#D4AF37;">${{ number_format($order->total, 2) }}</span>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>

                        </table>
                    </td>
                </tr>

                <!-- ===== REVIEW BLOCK (completed only) ===== -->
                @if($order->status === 'completed')
                <tr>
                    <td style="background-color:#1A1800; padding: 24px 40px; border-top: 1px solid #2A2200;">
                        <p style="margin:0 0 8px 0; font-size:13px; color:#D4AF37; font-weight:600; text-transform:uppercase; letter-spacing:1px;">
                            ¿Qué tal estuvo {{ $order->restaurant->name }}?
                        </p>
                        <p style="margin:0 0 16px 0; font-size:14px; color:#AAAAAA; line-height:1.6;">
                            Tu reseña ayuda a otros comensales a descubrir los mejores restaurantes mexicanos.
                        </p>
                        <!-- Star rating visual (static decorative) -->
                        <p style="margin:0 0 4px 0; font-size:28px; letter-spacing:4px;">⭐⭐⭐⭐⭐</p>
                    </td>
                </tr>
                @endif

                <!-- ===== CTA BUTTON ===== -->
                <tr>
                    <td align="center" style="background-color:#1A1A1A; padding: 32px 40px;">
                        <a
                            href="{{ $ctaUrl }}"
                            style="display:inline-block; background-color:{{ $config['accent'] }}; color:#0B0B0B; font-size:15px; font-weight:700; text-decoration:none; padding: 14px 36px; border-radius:8px; letter-spacing:0.5px;"
                        >
                            {{ $config['cta'] }}
                        </a>

                        @if($order->status === 'completed')
                        <br><br>
                        <a
                            href="{{ url('/pedido/' . $order->order_number) }}"
                            style="display:inline-block; border: 1px solid #2A2A2A; color:#AAAAAA; font-size:13px; font-weight:500; text-decoration:none; padding: 10px 24px; border-radius:8px;"
                        >
                            Ver mi pedido
                        </a>
                        @endif
                    </td>
                </tr>

                <!-- ===== CANCELLATION REASON (cancelled only) ===== -->
                @if($order->status === 'cancelled' && $order->cancellation_reason)
                <tr>
                    <td style="background-color:#1A0000; padding: 20px 40px; border-top: 1px solid #2A0000;">
                        <p style="margin:0 0 4px 0; font-size:11px; color:#888888; text-transform:uppercase; letter-spacing:1px;">Motivo de cancelación</p>
                        <p style="margin:0; font-size:14px; color:#CCCCCC; line-height:1.6;">{{ $order->cancellation_reason }}</p>
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
                            ¿Tienes alguna duda? <a href="mailto:soporte@restaurantesmexicanosfamosos.com" style="color:#666666;">soporte@restaurantesmexicanosfamosos.com</a>
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
