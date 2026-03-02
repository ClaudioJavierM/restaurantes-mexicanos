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
        .code { background: #fef3c7; padding: 18px; text-align: center; border-radius: 10px; margin: 18px 0; }
        .code-value { font-size: 28px; font-weight: bold; color: #d97706; letter-spacing: 3px; }
        .btn { display: inline-block; background: #dc2626; color: #ffffff !important; padding: 14px 32px; text-decoration: none; border-radius: 8px; margin-top: 18px; font-weight: 600; font-size: 15px; }
        .footer { text-align: center; padding: 20px; color: #9ca3af; font-size: 12px; border-radius: 0 0 12px 12px; background: #ffffff; border: 1px solid #e5e7eb; border-top: none; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            @if($restaurant->logo)
                <img src="{{ asset('storage/' . $restaurant->logo) }}" alt="{{ $restaurant->name }}" class="logo">
            @elseif($restaurant->image)
                <img src="{{ asset('storage/' . $restaurant->image) }}" alt="{{ $restaurant->name }}" class="logo">
            @endif
            <div class="restaurant-name">{{ $restaurant->name }}</div>
            <h1 style="margin: 0; font-size: 24px;">Nueva Reservación</h1>
        </div>
        <div class="content">
            <p style="margin-top: 0;">Has recibido una nueva solicitud de reservación:</p>

            <div class="details">
                <div class="detail-row">
                    <div class="label">Cliente</div>
                    <div class="value">{{ $reservation->getContactName() }}</div>
                </div>

                <div class="detail-row">
                    <div class="label">Teléfono</div>
                    <div class="value">{{ $reservation->guest_phone }}</div>
                </div>

                @if($reservation->getContactEmail())
                <div class="detail-row">
                    <div class="label">Email</div>
                    <div class="value">{{ $reservation->getContactEmail() }}</div>
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
                <div class="label">Código de Confirmación</div>
                <div class="code-value">{{ $reservation->confirmation_code }}</div>
            </div>

            <p style="text-align: center;">
                <a href="{{ config('app.url') }}/owner/my-reservations/{{ $reservation->id }}/edit" class="btn" style="color: #ffffff !important;">
                    Ver en Panel de Administración
                </a>
            </p>
        </div>
        <div class="footer">
            <p>Este es un mensaje automático de Restaurantes Mexicanos Famosos</p>
        </div>
    </div>
</body>
</html>
