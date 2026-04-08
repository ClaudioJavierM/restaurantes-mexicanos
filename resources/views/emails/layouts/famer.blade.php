@props([
    'title' => 'Restaurantes Mexicanos Famosos',
    'headerColor' => '#DC2626',
    'headerGradient' => 'linear-gradient(135deg, #DC2626 0%, #991B1B 100%)',
    'preheader' => null,
])
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
</head>
<body style="margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f5f5f5;">
    @if($preheader)
        <div style="display:none; max-height:0; overflow:hidden;">{{ $preheader }}</div>
    @endif
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #f5f5f5;">
        <tr>
            <td align="center" style="padding: 40px 20px;">
                <table role="presentation" width="600" cellspacing="0" cellpadding="0" style="background-color: #ffffff; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); max-width: 600px; width: 100%;">

                    {{-- Header with FAMER branding --}}
                    <tr>
                        <td style="background: {{ $headerGradient }}; padding: 32px 40px; border-radius: 12px 12px 0 0; text-align: center;">
                            <img src="https://restaurantesmexicanosfamosos.com/images/branding/icon.png"
                                 alt="FAMER"
                                 style="width: 64px; height: 64px; border-radius: 50%; border: 3px solid rgba(255,255,255,0.3); margin-bottom: 12px;"
                                 width="64" height="64" />
                            <h1 style="color: #ffffff; margin: 0; font-size: 24px; font-weight: 700; letter-spacing: -0.3px;">
                                {{ $header ?? 'Restaurantes Mexicanos Famosos' }}
                            </h1>
                            @isset($subheader)
                                <p style="color: rgba(255,255,255,0.9); margin: 8px 0 0 0; font-size: 15px;">
                                    {{ $subheader }}
                                </p>
                            @endisset
                        </td>
                    </tr>

                    {{-- Main Content --}}
                    <tr>
                        <td style="padding: 40px;">
                            {{ $slot }}
                        </td>
                    </tr>

                    {{-- Footer --}}
                    <tr>
                        <td style="background-color: #f9fafb; padding: 24px 40px; border-radius: 0 0 12px 12px; border-top: 1px solid #e5e7eb;">
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td align="center" style="padding-bottom: 12px;">
                                        <a href="https://restaurantesmexicanosfamosos.com" style="color: #DC2626; text-decoration: none; font-weight: 600; font-size: 14px;">restaurantesmexicanosfamosos.com</a>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="center" style="padding-bottom: 8px;">
                                        <a href="https://restaurantesmexicanosfamosos.com" style="color: #6b7280; text-decoration: none; font-size: 12px; margin: 0 8px;">Directorio</a>
                                        <span style="color: #d1d5db;">&middot;</span>
                                        <a href="https://restaurantesmexicanosfamosos.com/for-owners" style="color: #6b7280; text-decoration: none; font-size: 12px; margin: 0 8px;">Para Restaurantes</a>
                                        <span style="color: #d1d5db;">&middot;</span>
                                        <a href="https://restaurantesmexicanosfamosos.com/preguntas-frecuentes" style="color: #6b7280; text-decoration: none; font-size: 12px; margin: 0 8px;">FAQ</a>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="center">
                                        <p style="color: #9ca3af; font-size: 11px; margin: 0; line-height: 1.5;">
                                            &copy; {{ date('Y') }} FAMER &mdash; Restaurantes Mexicanos Famosos<br>
                                            El directorio mas completo de restaurantes mexicanos en Estados Unidos
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
