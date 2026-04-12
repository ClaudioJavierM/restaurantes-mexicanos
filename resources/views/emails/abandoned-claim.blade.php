<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{{ $restaurant->name }} — ¿terminaste de reclamar tu perfil?</title>
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
                        </td>
                    </tr>

                    <!-- 2. HEADLINE -->
                    <tr>
                        <td style="padding:32px 24px 20px;">
                            <p style="margin:0 0 12px;font-size:16px;color:#9CA3AF;">Hola,</p>
                            <h1 style="margin:0 0 16px;font-size:24px;font-weight:bold;color:#F5F5F5;line-height:1.3;">
                                Quedaste a un paso de reclamar<br>
                                <span style="color:#D4AF37;">{{ $restaurant->name }}</span>
                            </h1>
                            <p style="margin:0;font-size:15px;color:#9CA3AF;line-height:1.6;">
                                Tu restaurante ya aparece en FAMER y las personas lo están buscando. Solo te falta completar la verificación para tomar el control de tu perfil.
                            </p>
                        </td>
                    </tr>

                    @php
                        $monthlyViews = \App\Models\AnalyticsEvent::where('restaurant_id', $restaurant->id)
                            ->where('event_type', \App\Models\AnalyticsEvent::EVENT_PAGE_VIEW)
                            ->where('created_at', '>=', now()->startOfMonth())
                            ->count();
                    @endphp

                    <!-- 3. STAT (si hay vistas este mes) -->
                    @if($monthlyViews > 0)
                    <tr>
                        <td style="padding:4px 24px 24px;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td style="background-color:#111111;border:1px solid rgba(212,175,55,0.2);border-radius:8px;padding:20px;text-align:center;">
                                        <div style="font-size:42px;font-weight:bold;color:#D4AF37;line-height:1;margin-bottom:8px;">{{ $monthlyViews }}</div>
                                        <div style="font-size:13px;color:#9CA3AF;text-transform:uppercase;letter-spacing:1px;">personas buscaron tu restaurante este mes</div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    @endif

                    <!-- 4. BENEFITS BOX -->
                    <tr>
                        <td style="padding:0 24px 24px;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td style="background-color:#111111;border:1px solid rgba(212,175,55,0.2);border-radius:8px;padding:24px;">
                                        <h2 style="margin:0 0 16px;font-size:17px;font-weight:bold;color:#F5F5F5;">
                                            ¿Qué obtienes al completar tu reclamación?
                                        </h2>

                                        <!-- Benefit rows -->
                                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                                            <tr>
                                                <td style="padding:6px 0;">
                                                    <table role="presentation" cellpadding="0" cellspacing="0" border="0">
                                                        <tr>
                                                            <td style="color:#D4AF37;font-size:15px;font-weight:bold;padding-right:10px;vertical-align:top;">&#10003;</td>
                                                            <td style="font-size:14px;color:#F5F5F5;line-height:1.5;">Dashboard de analíticas con visitas y tendencias</td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding:6px 0;">
                                                    <table role="presentation" cellpadding="0" cellspacing="0" border="0">
                                                        <tr>
                                                            <td style="color:#D4AF37;font-size:15px;font-weight:bold;padding-right:10px;vertical-align:top;">&#10003;</td>
                                                            <td style="font-size:14px;color:#F5F5F5;line-height:1.5;">Responde reseñas y construye tu reputación</td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding:6px 0;">
                                                    <table role="presentation" cellpadding="0" cellspacing="0" border="0">
                                                        <tr>
                                                            <td style="color:#D4AF37;font-size:15px;font-weight:bold;padding-right:10px;vertical-align:top;">&#10003;</td>
                                                            <td style="font-size:14px;color:#F5F5F5;line-height:1.5;">Actualiza fotos, horarios y menú cuando quieras</td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding:6px 0;">
                                                    <table role="presentation" cellpadding="0" cellspacing="0" border="0">
                                                        <tr>
                                                            <td style="color:#D4AF37;font-size:15px;font-weight:bold;padding-right:10px;vertical-align:top;">&#10003;</td>
                                                            <td style="font-size:14px;color:#F5F5F5;line-height:1.5;">Es completamente <strong style="color:#4ADE80;">GRATIS</strong> para empezar</td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- 5. PRIMARY CTA -->
                    <tr>
                        <td style="padding:0 24px 28px;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td align="center" style="background-color:#D4AF37;border-radius:8px;">
                                        <a href="{{ url('/claim?restaurant=' . $restaurant->slug) }}"
                                           style="display:block;padding:18px 24px;font-size:17px;font-weight:bold;color:#0B0B0B;text-decoration:none;text-align:center;letter-spacing:0.5px;">
                                            Completar mi Reclamaci&oacute;n &rarr;
                                        </a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- 6. URGENCY NOTE -->
                    <tr>
                        <td style="padding:0 24px 28px;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td style="background-color:rgba(212,175,55,0.05);border:1px solid rgba(212,175,55,0.1);border-radius:8px;padding:16px;text-align:center;">
                                        <p style="margin:0;font-size:13px;color:#9CA3AF;line-height:1.5;">
                                            Tu perfil es público y los clientes ya lo están viendo.<br>
                                            <strong style="color:#F5F5F5;">Sé el primero en responder y destacar en tu área.</strong>
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- 7. FOOTER -->
                    <tr>
                        <td style="border-top:1px solid #2A2A2A;padding:20px 24px;text-align:center;">
                            <p style="margin:0 0 8px;font-size:12px;color:#6B7280;">
                                FAMER &mdash; Famous Mexican Restaurants
                            </p>
                            <p style="margin:0 0 8px;font-size:12px;color:#6B7280;">
                                <a href="mailto:unsubscribe@restaurantesmexicanosfamosos.com?subject=Unsubscribe {{ $restaurant->email ?? '' }}"
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
