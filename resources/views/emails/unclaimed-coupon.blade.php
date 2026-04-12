<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Oferta especial — Premium por $1 el primer mes</title>
</head>
<body style="margin:0;padding:0;background-color:#0B0B0B;font-family:Arial,Helvetica,sans-serif;">

    <!-- Wrapper -->
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#0B0B0B;">
        <tr>
            <td align="center" style="padding:32px 16px;">

                <!-- Main container -->
                <table role="presentation" width="600" cellpadding="0" cellspacing="0" border="0" style="max-width:600px;width:100%;background-color:#1A1A1A;border-radius:12px;overflow:hidden;">

                    <!-- 1. HEADER -->
                    <tr>
                        <td style="background-color:#1A1A1A;padding:24px;text-align:center;border-bottom:2px solid #D4AF37;">
                            <div style="font-family:Georgia,'Times New Roman',serif;font-size:32px;font-weight:bold;color:#D4AF37;letter-spacing:4px;margin:0 0 6px;">FAMER</div>
                            <div style="font-size:13px;color:#9CA3AF;letter-spacing:2px;text-transform:uppercase;">Famous Mexican Restaurants</div>
                            <!-- OFERTA ESPECIAL badge -->
                            <div style="margin-top:14px;">
                                <span style="display:inline-block;background-color:#8B1E1E;color:#F5F5F5;font-size:12px;font-weight:bold;letter-spacing:2px;text-transform:uppercase;padding:5px 16px;border-radius:20px;">
                                    OFERTA ESPECIAL
                                </span>
                            </div>
                        </td>
                    </tr>

                    <!-- 2. HERO — $1 first month -->
                    <tr>
                        <td style="padding:32px 24px 20px;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td style="background-color:#111111;border:2px solid #D4AF37;border-radius:10px;padding:28px 24px;text-align:center;">
                                        <p style="margin:0 0 6px;font-size:14px;color:#9CA3AF;text-transform:uppercase;letter-spacing:2px;">Oferta exclusiva para</p>
                                        <h1 style="margin:0 0 16px;font-size:22px;font-weight:bold;color:#F5F5F5;line-height:1.3;">{{ $restaurant->name }}</h1>
                                        <div style="font-size:48px;font-weight:bold;color:#D4AF37;line-height:1;margin-bottom:6px;">Premium por <span style="font-size:64px;">$1</span></div>
                                        <div style="font-size:15px;color:#9CA3AF;margin-bottom:16px;">el primer mes</div>
                                        <div style="font-size:13px;color:#6B7280;text-decoration:line-through;margin-bottom:12px;">Normalmente $39/mes</div>
                                        <!-- Urgency -->
                                        <div style="display:inline-block;background-color:rgba(139,30,30,0.15);border:1px solid rgba(139,30,30,0.4);border-radius:6px;padding:8px 16px;">
                                            <span style="font-size:13px;color:#F87171;font-weight:bold;">Oferta v&aacute;lida por 7 d&iacute;as</span>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- 3. COUPON BLOCK -->
                    <tr>
                        <td style="padding:0 24px 24px;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td style="border:2px dashed #D4AF37;border-radius:10px;padding:24px;text-align:center;background-color:#111111;">
                                        <p style="margin:0 0 8px;font-size:13px;color:#9CA3AF;text-transform:uppercase;letter-spacing:1px;">Usa el c&oacute;digo:</p>
                                        <div style="font-family:'Courier New',Courier,monospace;font-size:36px;font-weight:bold;color:#D4AF37;letter-spacing:6px;margin-bottom:8px;">{{ $couponCode }}</div>
                                        <p style="margin:0;font-size:12px;color:#6B7280;">Al reclamar tu restaurante</p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- 4. STATS (conditional) -->
                    @if($monthlyViews > 0)
                    <tr>
                        <td style="padding:0 24px 20px;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td style="background-color:#1F3D2B;border:1px solid rgba(74,222,128,0.2);border-radius:8px;padding:16px 20px;text-align:center;">
                                        <p style="margin:0;font-size:14px;color:#4ADE80;line-height:1.6;">
                                            Tu restaurante tuvo <strong style="color:#F5F5F5;">{{ $monthlyViews }} {{ $monthlyViews === 1 ? 'visita' : 'visitas' }}</strong> este mes &mdash; imagina cu&aacute;ntas m&aacute;s con Premium
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    @endif

                    <!-- 5. WHAT YOU GET -->
                    <tr>
                        <td style="padding:0 24px 20px;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td style="background-color:#111111;border:1px solid #2A2A2A;border-radius:8px;padding:24px;">
                                        <h2 style="margin:0 0 16px;font-size:16px;font-weight:bold;color:#F5F5F5;">Lo que obtienes con Premium:</h2>

                                        <!-- Bullet 1 -->
                                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom:12px;">
                                            <tr>
                                                <td width="28" valign="top" style="padding-top:2px;">
                                                    <div style="width:22px;height:22px;background-color:rgba(212,175,55,0.15);border-radius:50%;text-align:center;line-height:22px;font-size:13px;">&#11088;</div>
                                                </td>
                                                <td style="padding-left:8px;">
                                                    <strong style="font-size:14px;color:#F5F5F5;">Badge Destacado</strong>
                                                    <div style="font-size:13px;color:#9CA3AF;margin-top:2px;">Sello dorado Premium visible en tu perfil</div>
                                                </td>
                                            </tr>
                                        </table>

                                        <!-- Bullet 2 -->
                                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom:12px;">
                                            <tr>
                                                <td width="28" valign="top" style="padding-top:2px;">
                                                    <div style="width:22px;height:22px;background-color:rgba(212,175,55,0.15);border-radius:50%;text-align:center;line-height:22px;font-size:13px;">&#128269;</div>
                                                </td>
                                                <td style="padding-left:8px;">
                                                    <strong style="font-size:14px;color:#F5F5F5;">Top 3 en B&uacute;squedas</strong>
                                                    <div style="font-size:13px;color:#9CA3AF;margin-top:2px;">Aparece antes que los {{ $competitorCount }} competidores en tu &aacute;rea</div>
                                                </td>
                                            </tr>
                                        </table>

                                        <!-- Bullet 3 -->
                                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom:12px;">
                                            <tr>
                                                <td width="28" valign="top" style="padding-top:2px;">
                                                    <div style="width:22px;height:22px;background-color:rgba(212,175,55,0.15);border-radius:50%;text-align:center;line-height:22px;font-size:13px;">&#127860;</div>
                                                </td>
                                                <td style="padding-left:8px;">
                                                    <strong style="font-size:14px;color:#F5F5F5;">Men&uacute; Digital + QR</strong>
                                                    <div style="font-size:13px;color:#9CA3AF;margin-top:2px;">Publica tu men&uacute; completo con fotos y precios</div>
                                                </td>
                                            </tr>
                                        </table>

                                        <!-- Bullet 4 -->
                                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                                            <tr>
                                                <td width="28" valign="top" style="padding-top:2px;">
                                                    <div style="width:22px;height:22px;background-color:rgba(212,175,55,0.15);border-radius:50%;text-align:center;line-height:22px;font-size:13px;">&#128202;</div>
                                                </td>
                                                <td style="padding-left:8px;">
                                                    <strong style="font-size:14px;color:#F5F5F5;">Dashboard de Anal&iacute;ticas</strong>
                                                    <div style="font-size:13px;color:#9CA3AF;margin-top:2px;">Visitas, b&uacute;squedas, clicks y comportamiento de clientes</div>
                                                </td>
                                            </tr>
                                        </table>

                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- 6. CTA BUTTON -->
                    <tr>
                        <td style="padding:0 24px 28px;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td align="center" style="background-color:#D4AF37;border-radius:8px;">
                                        <a href="{{ url('/claim?restaurant=' . $restaurant->slug . '&plan=premium&coupon=' . $couponCode) }}"
                                           style="display:block;padding:18px 24px;font-size:17px;font-weight:bold;color:#0B0B0B;text-decoration:none;text-align:center;letter-spacing:0.5px;">
                                            Reclamar con descuento &rarr;
                                        </a>
                                    </td>
                                </tr>
                            </table>
                            <p style="margin:12px 0 0;text-align:center;font-size:12px;color:#6B7280;">
                                C&oacute;digo <strong style="color:#D4AF37;">{{ $couponCode }}</strong> se aplica autom&aacute;ticamente
                            </p>
                        </td>
                    </tr>

                    <!-- 7. FOOTER -->
                    <tr>
                        <td style="border-top:1px solid #2A2A2A;padding:20px 24px;text-align:center;">
                            <p style="margin:0 0 8px;font-size:12px;color:#6B7280;">
                                FAMER &mdash; Famous Mexican Restaurants
                            </p>
                            <p style="margin:0 0 8px;font-size:12px;color:#6B7280;">
                                <a href="mailto:unsubscribe@restaurantesmexicanosfamosos.com?subject=Unsubscribe {{ $restaurant->owner_email ?? $restaurant->email ?? '' }}"
                                   style="color:#6B7280;text-decoration:underline;">
                                    Cancelar suscripci&oacute;n
                                </a>
                            </p>
                            <p style="margin:0;font-size:12px;color:#6B7280;">
                                &copy; {{ date('Y') }} FAMER. Todos los derechos reservados.
                            </p>
                        </td>
                    </tr>

                </table>
                <!-- /Main container -->

            </td>
        </tr>
    </table>
    <!-- /Wrapper -->

</body>
</html>
