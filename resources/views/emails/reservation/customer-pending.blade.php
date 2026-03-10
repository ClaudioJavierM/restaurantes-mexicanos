<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; background: #f3f4f6; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: linear-gradient(135deg, #dc2626, #991b1b); color: white; padding: 30px 20px; text-align: center; border-radius: 12px 12px 0 0; }
        .logo { width: 80px; height: 80px; border-radius: 50%; object-fit: cover; border: 3px solid rgba(255,255,255,0.3); margin-bottom: 12px; }
        .restaurant-name { font-size: 14px; opacity: 0.9; margin-bottom: 8px; }
        .content { background: #ffffff; padding: 24px; border: 1px solid #e5e7eb; }
        .details { background: #f9fafb; padding: 18px; border-radius: 10px; margin: 18px 0; }
        .detail-row { margin-bottom: 12px; }
        .detail-row:last-child { margin-bottom: 0; }
        .label { color: #6b7280; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 2px; }
        .value { font-size: 16px; font-weight: 600; color: #111827; }
        .status-box { background: #fef3c7; border: 1px solid #fde68a; padding: 18px; text-align: center; border-radius: 10px; margin: 18px 0; }
        .status-icon { font-size: 40px; margin-bottom: 8px; }
        .status-text { font-size: 16px; font-weight: 600; color: #92400e; }
        .code { background: #f0f9ff; padding: 18px; text-align: center; border-radius: 10px; margin: 18px 0; }
        .code-value { font-size: 28px; font-weight: bold; color: #0369a1; letter-spacing: 3px; }
        .footer { text-align: center; padding: 20px; color: #9ca3af; font-size: 12px; border-radius: 0 0 12px 12px; background: #ffffff; border: 1px solid #e5e7eb; border-top: none; }
    </style>
</head>
<body>
    <div style="text-align: center; padding: 20px 0 0 0;"><img src="https://restaurantesmexicanosfamosos.com/images/branding/logo.png?v=3" alt="Restaurantes Mexicanos Famosos" style="max-height: 50px; width: auto; margin-bottom: 12px;" /></div>
    <div class="container">
        <div class="header">
            @if($restaurant->logo)
                <img src="{{ asset('storage/' . $restaurant->logo) }}" alt="{{ $restaurant->name }}" class="logo">
            @elseif($restaurant->image)
                <img src="{{ asset('storage/' . $restaurant->image) }}" alt="{{ $restaurant->name }}" class="logo">
            @endif
            <div class="restaurant-name">{{ $restaurant->name }}</div>
            <h1 style="margin: 0; font-size: 24px;">Solicitud de Reservación</h1>
        </div>
        <div class="content">
            <p style="margin-top: 0;">Hola {{ $reservation->getContactName() }},</p>
            <p>Tu solicitud de reservación ha sido enviada exitosamente.</p>

            <div class="status-box">
                <div class="status-icon">&#9203;</div>
                <div class="status-text">Esperando confirmación del restaurante</div>
                <p style="margin: 8px 0 0 0; font-size: 13px; color: #78350f;">
                    El restaurante revisará tu solicitud y te notificará cuando sea confirmada.
                </p>
            </div>

            <div class="details">
                <div class="detail-row">
                    <div class="label">Restaurante</div>
                    <div class="value" style="color: #dc2626;">{{ $restaurant->name }}</div>
                </div>

                @if($restaurant->address)
                <div class="detail-row">
                    <div class="label">Dirección</div>
                    <div class="value">{{ $restaurant->address }}@if($restaurant->city), {{ $restaurant->city }}@endif</div>
                </div>
                @endif

                <div class="detail-row">
                    <div class="label">Fecha y Hora</div>
                    <div class="value">{{ $reservation->reservation_date->format('d/m/Y') }} a las {{ $reservation->reservation_time->format('g:i A') }}</div>
                </div>

                <div class="detail-row">
                    <div class="label">Número de Personas</div>
                    <div class="value">{{ $reservation->party_size }} personas</div>
                </div>

                @if($reservation->occasion && $reservation->occasion !== 'none')
                <div class="detail-row">
                    <div class="label">Ocasión</div>
                    <div class="value">{{ $reservation->getOccasionLabel() }}</div>
                </div>
                @endif

                @if($reservation->special_requests)
                <div class="detail-row">
                    <div class="label">Peticiones Especiales</div>
                    <div class="value">{{ $reservation->special_requests }}</div>
                </div>
                @endif
            </div>

            <div class="code">
                <div class="label">Tu Código de Reservación</div>
                <div class="code-value">{{ $reservation->confirmation_code }}</div>
                <p style="margin: 8px 0 0 0; font-size: 12px; color: #6b7280;">
                    Guarda este código para referencia
                </p>
            </div>

            @if($restaurant->phone)
            <p style="text-align: center; margin-top: 20px; font-size: 14px;">
                <strong>¿Necesitas contactar al restaurante?</strong><br>
                Teléfono: {{ $restaurant->phone }}
            </p>
            @endif
        </div>
        <div class="footer">
            <p>Gracias por elegir {{ $restaurant->name }}</p>
            <p>Powered by Restaurantes Mexicanos Famosos</p>
        </div>
    </div>
</body>
</html>
