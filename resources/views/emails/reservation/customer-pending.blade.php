@php
    $contactName = $reservation->getContactName();
    $date = $reservation->reservation_date->format('d \d\e F \d\e Y');
    $time = $reservation->reservation_time->format('g:i A');
@endphp
<!DOCTYPE html>
<html lang="es" xmlns="http://www.w3.org/1999/xhtml">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="format-detection" content="telephone=no">
  <title>Solicitud de Reservación Recibida — FAMER</title>
</head>
<body style="margin:0; padding:0; background-color:#F5F0E8; font-family:'Segoe UI',Arial,Helvetica,sans-serif; -webkit-text-size-adjust:100%; -ms-text-size-adjust:100%;">

<table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color:#F5F0E8;">
  <tr><td align="center" style="padding:40px 16px;">
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

          <!-- Status badge -->
          <div style="text-align:center; margin-bottom:28px;">
            <span style="display:inline-block; background:#FFFBEB; border:1.5px solid #FDE68A; color:#92400E; font-size:12px; font-weight:700; letter-spacing:2px; text-transform:uppercase; padding:8px 20px; border-radius:20px;">
              Pendiente de Confirmación
            </span>
          </div>

          <!-- Heading -->
          <h1 style="margin:0 0 8px; font-size:26px; font-weight:700; color:#111827; text-align:center; font-family:'Segoe UI',Arial,sans-serif;">
            Solicitud Recibida, {{ $contactName }}
          </h1>
          <p style="margin:0 0 32px; font-size:15px; color:#6B7280; text-align:center; line-height:1.6;">
            Tu solicitud de reservación en <strong style="color:#111827;">{{ $reservation->restaurant->name }}</strong> fue enviada. El restaurante la revisará y te confirmaremos pronto.
          </p>

          <!-- Details box -->
          <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#FBF6E9; border:1px solid #D4AF37; border-radius:10px; margin:0 0 28px;">
            <tr><td style="padding:24px;">
              <p style="margin:0 0 16px; font-size:13px; font-weight:700; color:#92400E; text-transform:uppercase; letter-spacing:1px;">Detalles de tu Solicitud</p>
              <table role="presentation" width="100%" cellspacing="0" cellpadding="0">
                <tr>
                  <td style="padding:7px 0; font-size:14px; color:#6B7280; width:45%; vertical-align:top;">Restaurante</td>
                  <td style="padding:7px 0; font-size:14px; color:#111827; font-weight:600;">{{ $reservation->restaurant->name }}</td>
                </tr>
                @if($reservation->restaurant->address)
                <tr>
                  <td style="padding:7px 0; font-size:14px; color:#6B7280; vertical-align:top;">Dirección</td>
                  <td style="padding:7px 0; font-size:14px; color:#111827; font-weight:600;">{{ $reservation->restaurant->address }}@if($reservation->restaurant->city), {{ $reservation->restaurant->city }}@endif</td>
                </tr>
                @endif
                <tr>
                  <td style="padding:7px 0; font-size:14px; color:#6B7280; vertical-align:top;">Fecha</td>
                  <td style="padding:7px 0; font-size:14px; color:#111827; font-weight:600;">{{ $date }}</td>
                </tr>
                <tr>
                  <td style="padding:7px 0; font-size:14px; color:#6B7280; vertical-align:top;">Hora</td>
                  <td style="padding:7px 0; font-size:14px; color:#111827; font-weight:600;">{{ $time }}</td>
                </tr>
                <tr>
                  <td style="padding:7px 0; font-size:14px; color:#6B7280; vertical-align:top;">Personas</td>
                  <td style="padding:7px 0; font-size:14px; color:#111827; font-weight:600;">{{ $reservation->party_size }} {{ $reservation->party_size === 1 ? 'persona' : 'personas' }}</td>
                </tr>
                @if($reservation->occasion && $reservation->occasion !== 'none')
                <tr>
                  <td style="padding:7px 0; font-size:14px; color:#6B7280; vertical-align:top;">Ocasión</td>
                  <td style="padding:7px 0; font-size:14px; color:#111827; font-weight:600;">{{ $reservation->getOccasionLabel() }}</td>
                </tr>
                @endif
                @if($reservation->special_requests)
                <tr>
                  <td style="padding:7px 0; font-size:14px; color:#6B7280; vertical-align:top;">Solicitudes especiales</td>
                  <td style="padding:7px 0; font-size:14px; color:#111827; font-weight:600;">{{ $reservation->special_requests }}</td>
                </tr>
                @endif
              </table>
            </td></tr>
          </table>

          <!-- Reference code -->
          <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#F9FAFB; border:1px solid #E5E7EB; border-radius:10px; margin:0 0 28px;">
            <tr><td style="padding:18px; text-align:center;">
              <p style="margin:0 0 4px; font-size:11px; font-weight:700; color:#6B7280; letter-spacing:2px; text-transform:uppercase;">Código de Referencia</p>
              <p style="margin:0; font-size:22px; font-weight:700; color:#9CA3AF; letter-spacing:4px;">{{ $reservation->confirmation_code }}</p>
            </td></tr>
          </table>

          <hr style="border:none; border-top:1px solid #F3F4F6; margin:0 0 20px;">

          <p style="margin:0; font-size:13px; color:#9CA3AF; line-height:1.7; text-align:center;">
            Recibirás un correo de confirmación en cuanto el restaurante apruebe tu solicitud.
            @if($reservation->restaurant->phone)
            <br>Si tienes dudas, puedes contactar al restaurante al <a href="tel:{{ $reservation->restaurant->phone }}" style="color:#D4AF37; text-decoration:none;">{{ $reservation->restaurant->phone }}</a>.
            @endif
          </p>

        </td>
      </tr>

      <!-- FOOTER -->
      <tr>
        <td style="background-color:#F9FAFB; border-radius:0 0 16px 16px; padding:24px 40px; border-top:1px solid #F3F4F6;">
          <p style="margin:0 0 8px; font-size:12px; color:#9CA3AF; text-align:center;">
            © {{ date('Y') }} FAMER — Restaurantes Mexicanos Famosos
          </p>
          <p style="margin:0; font-size:11px; color:#D1D5DB; text-align:center;">
            Este es un mensaje automático, por favor no respondas a este correo.
          </p>
        </td>
      </tr>

    </table>
  </td></tr>
</table>

</body>
</html>
