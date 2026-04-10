@props([
    'title' => 'Restaurantes Mexicanos Famosos',
    'preheader' => null,
    'header' => 'Restaurantes Mexicanos Famosos',
    'subheader' => null,
    'showUnsubscribe' => false,
    'unsubscribeUrl' => null,
])
<!DOCTYPE html>
<html lang="es" xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="format-detection" content="telephone=no">
    <title>{{ $title }}</title>
</head>
<body style="margin:0; padding:0; background-color:#F5F0E8; font-family:'Segoe UI',Arial,Helvetica,sans-serif;">

    @if($preheader)
        <div style="display:none; max-height:0; overflow:hidden; mso-hide:all;">{{ $preheader }}</div>
    @endif

    {{-- Wrapper --}}
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color:#F5F0E8;">
        <tr>
            <td align="center" style="padding:40px 20px;">

                {{-- Container 580px --}}
                <table role="presentation" width="580" cellspacing="0" cellpadding="0" border="0" style="max-width:580px; width:100%; border-radius:12px; overflow:hidden; box-shadow:0 4px 16px rgba(0,0,0,0.12);">

                    {{-- Header: negro + logo --}}
                    <tr>
                        <td style="background-color:#0B0B0B; padding:32px 40px; text-align:center; border-radius:12px 12px 0 0;">
                            <img src="https://restaurantesmexicanosfamosos.com.mx/images/branding/logo-horizontal.png"
                                 alt="FAMER"
                                 width="160"
                                 style="display:block; margin:0 auto 16px auto; max-width:160px; height:auto;" />
                            <h1 style="color:#FFFFFF; margin:0; font-size:22px; font-weight:700; letter-spacing:-0.3px; line-height:1.3;">
                                {{ $header }}
                            </h1>
                            @if($subheader)
                                <p style="color:#D4AF37; margin:8px 0 0 0; font-size:14px; font-weight:500; letter-spacing:0.2px;">
                                    {{ $subheader }}
                                </p>
                            @endif
                        </td>
                    </tr>

                    {{-- Separador dorado --}}
                    <tr>
                        <td style="padding:0; line-height:0; font-size:0;">
                            <div style="height:3px; background:linear-gradient(90deg,#D4AF37,#F0D060,#D4AF37); font-size:0; line-height:0;">&nbsp;</div>
                        </td>
                    </tr>

                    {{-- Cuerpo blanco --}}
                    <tr>
                        <td style="background-color:#FFFFFF; padding:48px 40px; color:#111827; font-size:15px; line-height:1.7;">
                            {{ $slot }}
                        </td>
                    </tr>

                    {{-- Footer --}}
                    <tr>
                        <td style="background-color:#F9FAFB; padding:28px 40px; border-top:1px solid #E5E7EB; border-radius:0 0 12px 12px; text-align:center;">
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
                                <tr>
                                    <td align="center" style="padding-bottom:10px;">
                                        <a href="https://restaurantesmexicanosfamosos.com.mx" style="color:#D4AF37; text-decoration:none; font-weight:600; font-size:13px;">restaurantesmexicanosfamosos.com.mx</a>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="center" style="padding-bottom:10px;">
                                        <a href="https://restaurantesmexicanosfamosos.com.mx" style="color:#9CA3AF; text-decoration:none; font-size:12px; margin:0 6px;">Directorio</a>
                                        <span style="color:#D1D5DB;">&middot;</span>
                                        <a href="https://restaurantesmexicanosfamosos.com.mx/for-owners" style="color:#9CA3AF; text-decoration:none; font-size:12px; margin:0 6px;">Para Restaurantes</a>
                                        <span style="color:#D1D5DB;">&middot;</span>
                                        <a href="https://restaurantesmexicanosfamosos.com.mx/preguntas-frecuentes" style="color:#9CA3AF; text-decoration:none; font-size:12px; margin:0 6px;">FAQ</a>
                                    </td>
                                </tr>
                                @if($showUnsubscribe && $unsubscribeUrl)
                                    <tr>
                                        <td align="center" style="padding-bottom:10px;">
                                            <a href="{{ $unsubscribeUrl }}" style="color:#9CA3AF; text-decoration:underline; font-size:11px;">Cancelar suscripcion</a>
                                        </td>
                                    </tr>
                                @endif
                                <tr>
                                    <td align="center">
                                        <p style="color:#9CA3AF; font-size:11px; margin:0; line-height:1.6;">
                                            &copy; {{ date('Y') }} FAMER &mdash; Restaurantes Mexicanos Famosos<br>
                                            123 Main St, Suite 100, Los Angeles, CA 90001<br>
                                            El directorio mas completo de restaurantes mexicanos en Estados Unidos
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                </table>
                {{-- End container --}}

            </td>
        </tr>
    </table>

</body>
</html>
