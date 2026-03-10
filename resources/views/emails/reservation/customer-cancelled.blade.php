<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #6b7280; color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0; }
        .content { background: #f9fafb; padding: 20px; border: 1px solid #e5e7eb; }
        .details { background: white; padding: 15px; border-radius: 8px; margin: 15px 0; }
        .label { color: #6b7280; font-size: 12px; text-transform: uppercase; }
        .value { font-size: 16px; font-weight: bold; margin-bottom: 10px; }
        .footer { text-align: center; padding: 20px; color: #6b7280; font-size: 12px; }
        .restaurant-name { font-size: 18px; font-weight: bold; color: #dc2626; }
        .reason-box { background: #fef2f2; border: 1px solid #fecaca; padding: 15px; border-radius: 8px; margin: 15px 0; }
    </style>
</head>
<body>
    <div style="text-align: center; padding: 20px 0 0 0;"><img src="https://restaurantesmexicanosfamosos.com/images/branding/logo.png?v=3" alt="Restaurantes Mexicanos Famosos" style="max-height: 50px; width: auto; margin-bottom: 12px;" /></div>
    <div class="container">
        <div class="header">
            <h1>Reservación Cancelada</h1>
        </div>
        <div class="content">
            <p>Hola {{ $reservation->getContactName() }},</p>
            <p>Lamentamos informarte que tu reservación ha sido cancelada.</p>

            <div class="details">
                <div class="label">Restaurante</div>
                <div class="value restaurant-name">{{ $restaurant->name }}</div>

                <div class="label">Fecha y Hora Original</div>
                <div class="value">{{ $reservation->reservation_date->format('d/m/Y') }} a las {{ $reservation->reservation_time->format('g:i A') }}</div>

                <div class="label">Número de Personas</div>
                <div class="value">{{ $reservation->party_size }} personas</div>
            </div>

            @if(isset($reason) && $reason)
            <div class="reason-box">
                <div class="label">Motivo de la Cancelación</div>
                <p style="margin: 5px 0 0 0;">{{ $reason }}</p>
            </div>
            @endif

            <p style="text-align: center; margin-top: 20px;">
                Te invitamos a hacer una nueva reservación cuando gustes.<br>
                @if($restaurant->phone)
                Contacto: {{ $restaurant->phone }}
                @endif
            </p>
        </div>
        <div class="footer">
            <p>Esperamos verte pronto en {{ $restaurant->name }}</p>
            <p>Powered by Restaurantes Mexicanos Famosos</p>
        </div>
    </div>
</body>
</html>
