@php
    $restaurantName = $restaurant->name;
    $contactName = $reservation->getContactName();
    $date = $reservation->reservation_date->format('d/m/Y');
    $time = $reservation->reservation_time->format('g:i A');
@endphp
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservacion Confirmada</title>
</head>
<body style="margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f5f5f5;">
    <div style="display:none; max-height:0; overflow:hidden;">Tu reservacion en {{ $restaurantName }} el {{ $date }} a las {{ $time }} esta confirmada</div>
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #f5f5f5;">
        <tr>
            <td align="center" style="padding: 40px 20px;">
                <table role="presentation" width="600" cellspacing="0" cellpadding="0" style="background-color: #ffffff; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); max-width: 600px; width: 100%;">
                    <tr>
                        <td style="background: linear-gradient(135deg, #16A34A 0%, #15803D 100%); padding: 32px 40px; border-radius: 12px 12px 0 0; text-align: center;">
                            <img src="https://restaurantesmexicanosfamosos.com/images/branding/icon.png" alt="FAMER" style="width: 64px; height: 64px; border-radius: 50%; border: 3px solid rgba(255,255,255,0.3); margin-bottom: 12px;" width="64" height="64" />
                            <div style="color: #ffffff; font-size: 40px; margin-bottom: 8px;">&#10004;</div>
                            <h1 style="color: #ffffff; margin: 0; font-size: 24px; font-weight: 700;">Reservacion Confirmada</h1>
                            <p style="color: rgba(255,255,255,0.9); margin: 8px 0 0 0; font-size: 15px;">Te esperamos</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 40px;">
                            <p style="color: #1f2937; font-size: 16px; margin: 0 0 16px 0;">Hola <strong>{{ $contactName }}</strong>,</p>
                            <p style="color: #4b5563; font-size: 15px; line-height: 1.6; margin: 0 0 24px 0;">
                                Tu reservacion ha sido <strong style="color: #16a34a;">confirmada</strong>. Te esperamos!
                            </p>

                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #f9fafb; border: 1px solid #e5e7eb; border-radius: 8px; margin: 0 0 24px 0;">
                                <tr>
                                    <td style="padding: 20px;">
                                        <p style="color: #6b7280; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; margin: 0 0 4px 0;">Restaurante</p>
                                        <p style="color: #DC2626; font-size: 18px; font-weight: 700; margin: 0 0 16px 0;">{{ $restaurantName }}</p>

                                        <p style="color: #6b7280; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; margin: 0 0 4px 0;">Direccion</p>
                                        <p style="color: #1f2937; font-size: 14px; margin: 0 0 16px 0;">{{ $restaurant->address }}, {{ $restaurant->city }}</p>

                                        <p style="color: #6b7280; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; margin: 0 0 4px 0;">Fecha y Hora</p>
                                        <p style="color: #1f2937; font-size: 16px; font-weight: 700; margin: 0 0 16px 0;">{{ $date }} a las {{ $time }}</p>

                                        <p style="color: #6b7280; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; margin: 0 0 4px 0;">Numero de Personas</p>
                                        <p style="color: #1f2937; font-size: 16px; font-weight: 700; margin: 0 {{ $reservation->table_assigned ? '0 16px' : '0' }} 0;">{{ $reservation->party_size }} {{ $reservation->party_size === 1 ? 'persona' : 'personas' }}</p>

                                        @if($reservation->table_assigned)
                                            <p style="color: #6b7280; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; margin: 0 0 4px 0;">Mesa Asignada</p>
                                            <p style="color: #1f2937; font-size: 16px; font-weight: 700; margin: 0;">{{ $reservation->table_assigned }}</p>
                                        @endif
                                    </td>
                                </tr>
                            </table>

                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #dcfce7; border: 1px solid #86efac; border-radius: 8px; margin: 0 0 24px 0;">
                                <tr>
                                    <td style="padding: 20px; text-align: center;">
                                        <p style="color: #166534; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; margin: 0 0 8px 0;">Tu Codigo de Confirmacion</p>
                                        <p style="color: #16a34a; font-size: 28px; font-weight: 700; letter-spacing: 3px; margin: 0 0 8px 0;">{{ $reservation->confirmation_code }}</p>
                                        <p style="color: #15803d; font-size: 12px; margin: 0;">Presenta este codigo al llegar al restaurante</p>
                                    </td>
                                </tr>
                            </table>

                            @if($restaurant->phone)
                            <p style="text-align: center; color: #4b5563; font-size: 14px; margin: 0;">
                                <strong>Telefono del restaurante:</strong> <a href="tel:{{ $restaurant->phone }}" style="color: #DC2626; text-decoration: none;">{{ $restaurant->phone }}</a>
                            </p>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td style="background-color: #f9fafb; padding: 24px 40px; border-radius: 0 0 12px 12px; border-top: 1px solid #e5e7eb; text-align: center;">
                            <a href="https://restaurantesmexicanosfamosos.com" style="color: #DC2626; text-decoration: none; font-weight: 600; font-size: 14px;">restaurantesmexicanosfamosos.com</a>
                            <p style="color: #9ca3af; font-size: 11px; margin: 8px 0 0 0;">
                                &copy; {{ date('Y') }} FAMER &mdash; Restaurantes Mexicanos Famosos
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
