<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #16a34a; color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0; }
        .content { background: #f9fafb; padding: 20px; border: 1px solid #e5e7eb; }
        .details { background: white; padding: 15px; border-radius: 8px; margin: 15px 0; }
        .label { color: #6b7280; font-size: 12px; text-transform: uppercase; }
        .value { font-size: 16px; font-weight: bold; margin-bottom: 10px; }
        .code { background: #dcfce7; padding: 15px; text-align: center; border-radius: 8px; }
        .code-value { font-size: 24px; font-weight: bold; color: #16a34a; letter-spacing: 2px; }
        .footer { text-align: center; padding: 20px; color: #6b7280; font-size: 12px; }
        .restaurant-name { font-size: 18px; font-weight: bold; color: #dc2626; }
        .check-icon { font-size: 48px; }
    </style>
</head>
<body>
    <div style="text-align: center; padding: 20px 0 0 0;"><img src="https://restaurantesmexicanosfamosos.com/images/branding/logo.png?v=3" alt="Restaurantes Mexicanos Famosos" style="max-height: 50px; width: auto; margin-bottom: 12px;" /></div>
    <div class="container">
        <div class="header">
            <div class="check-icon">&#10004;</div>
            <h1>Reservación Confirmada</h1>
        </div>
        <div class="content">
            <p>Hola {{ $reservation->getContactName() }},</p>
            <p>Tu reservación ha sido <strong>confirmada</strong>. ¡Te esperamos!</p>

            <div class="details">
                <div class="label">Restaurante</div>
                <div class="value restaurant-name">{{ $restaurant->name }}</div>

                <div class="label">Dirección</div>
                <div class="value">{{ $restaurant->address }}, {{ $restaurant->city }}</div>

                <div class="label">Fecha y Hora</div>
                <div class="value">{{ $reservation->reservation_date->format('d/m/Y') }} a las {{ $reservation->reservation_time->format('g:i A') }}</div>

                <div class="label">Número de Personas</div>
                <div class="value">{{ $reservation->party_size }} personas</div>

                @if($reservation->table_assigned)
                <div class="label">Mesa Asignada</div>
                <div class="value">{{ $reservation->table_assigned }}</div>
                @endif
            </div>

            <div class="code">
                <div class="label">Tu Código de Confirmación</div>
                <div class="code-value">{{ $reservation->confirmation_code }}</div>
                <p style="margin: 10px 0 0 0; font-size: 12px; color: #6b7280;">
                    Presenta este código al llegar al restaurante
                </p>
            </div>

            @if($restaurant->phone)
            <p style="text-align: center; margin-top: 20px;">
                <strong>Teléfono del restaurante:</strong> {{ $restaurant->phone }}
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
