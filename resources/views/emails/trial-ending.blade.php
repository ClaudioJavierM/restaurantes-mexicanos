<!DOCTYPE html>
<html lang="{{ $restaurant->country === 'US' ? 'en' : 'es' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    @if($restaurant->country === 'US')
        <title>Your Elite trial ends in {{ $daysLeft }} days — {{ $restaurant->name }}</title>
    @else
        <title>Tu prueba Elite termina en {{ $daysLeft }} días — {{ $restaurant->name }}</title>
    @endif
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

                    <!-- 2. DAYS LEFT BADGE -->
                    <tr>
                        <td style="padding:32px 24px 8px;text-align:center;">
                            <div style="display:inline-block;background-color:rgba(212,175,55,0.1);border:1px solid #D4AF37;border-radius:50px;padding:8px 24px;margin-bottom:20px;">
                                <span style="font-size:14px;color:#D4AF37;font-weight:bold;letter-spacing:1px;text-transform:uppercase;">
                                    @if($restaurant->country === 'US')
                                        Trial ending in
                                        <strong style="font-size:20px;">{{ $daysLeft }}</strong>
                                        {{ $daysLeft === 1 ? 'day' : 'days' }}
                                    @else
                                        Termina en
                                        <strong style="font-size:20px;">{{ $daysLeft }}</strong>
                                        {{ $daysLeft === 1 ? 'día' : 'días' }}
                                    @endif
                                </span>
                            </div>
                        </td>
                    </tr>

                    <!-- 3. HEADLINE -->
                    <tr>
                        <td style="padding:0 24px 20px;">
                            @if($restaurant->country === 'US')
                                <p style="margin:0 0 12px;font-size:16px;color:#9CA3AF;">Hello,</p>
                                <h1 style="margin:0 0 16px;font-size:24px;font-weight:bold;color:#F5F5F5;line-height:1.3;">
                                    Your Elite trial for<br>
                                    <span style="color:#D4AF37;">{{ $restaurant->name }}</span><br>
                                    is ending soon.
                                </h1>
                                <p style="margin:0;font-size:15px;color:#9CA3AF;line-height:1.6;">
                                    You have <strong style="color:#D4AF37;">{{ $daysLeft }} {{ $daysLeft === 1 ? 'day' : 'days' }}</strong> left on your Elite trial.
                                    Everything you've built — your analytics, your profile boost, your featured listing — will continue if you keep your plan active.
                                </p>
                            @else
                                <p style="margin:0 0 12px;font-size:16px;color:#9CA3AF;">Hola,</p>
                                <h1 style="margin:0 0 16px;font-size:24px;font-weight:bold;color:#F5F5F5;line-height:1.3;">
                                    Tu prueba Elite de<br>
                                    <span style="color:#D4AF37;">{{ $restaurant->name }}</span><br>
                                    está por terminar.
                                </h1>
                                <p style="margin:0;font-size:15px;color:#9CA3AF;line-height:1.6;">
                                    Te quedan <strong style="color:#D4AF37;">{{ $daysLeft }} {{ $daysLeft === 1 ? 'día' : 'días' }}</strong> de tu prueba Elite.
                                    Todo lo que has construido — tus analíticas, el impulso a tu perfil, tu destacado — seguirá activo si mantienes tu plan.
                                </p>
                            @endif
                        </td>
                    </tr>

                    <!-- 4. WHAT HAPPENS WHEN TRIAL ENDS -->
                    <tr>
                        <td style="padding:0 24px 24px;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td style="background-color:#111111;border:1px solid rgba(212,175,55,0.2);border-radius:8px;padding:24px;">
                                        <h2 style="margin:0 0 16px;font-size:17px;font-weight:bold;color:#F5F5F5;">
                                            @if($restaurant->country === 'US')
                                                What you keep with Elite ($79/month)
                                            @else
                                                Qué conservas con Elite ($79/mes)
                                            @endif
                                        </h2>

                                        <!-- Benefit rows -->
                                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                                            @if($restaurant->country === 'US')
                                                <tr><td style="padding:6px 0;">
                                                    <table role="presentation" cellpadding="0" cellspacing="0" border="0"><tr>
                                                        <td style="color:#D4AF37;font-size:15px;font-weight:bold;padding-right:10px;vertical-align:top;">&#10003;</td>
                                                        <td style="font-size:14px;color:#F5F5F5;line-height:1.5;">Advanced analytics dashboard with visitor trends</td>
                                                    </tr></table>
                                                </td></tr>
                                                <tr><td style="padding:6px 0;">
                                                    <table role="presentation" cellpadding="0" cellspacing="0" border="0"><tr>
                                                        <td style="color:#D4AF37;font-size:15px;font-weight:bold;padding-right:10px;vertical-align:top;">&#10003;</td>
                                                        <td style="font-size:14px;color:#F5F5F5;line-height:1.5;">Featured listing — appear at the top of search results</td>
                                                    </tr></table>
                                                </td></tr>
                                                <tr><td style="padding:6px 0;">
                                                    <table role="presentation" cellpadding="0" cellspacing="0" border="0"><tr>
                                                        <td style="color:#D4AF37;font-size:15px;font-weight:bold;padding-right:10px;vertical-align:top;">&#10003;</td>
                                                        <td style="font-size:14px;color:#F5F5F5;line-height:1.5;">Priority SEO indexing and Google Booster</td>
                                                    </tr></table>
                                                </td></tr>
                                                <tr><td style="padding:6px 0;">
                                                    <table role="presentation" cellpadding="0" cellspacing="0" border="0"><tr>
                                                        <td style="color:#D4AF37;font-size:15px;font-weight:bold;padding-right:10px;vertical-align:top;">&#10003;</td>
                                                        <td style="font-size:14px;color:#F5F5F5;line-height:1.5;">Email marketing tools to reach your customers</td>
                                                    </tr></table>
                                                </td></tr>
                                                <tr><td style="padding:6px 0;">
                                                    <table role="presentation" cellpadding="0" cellspacing="0" border="0"><tr>
                                                        <td style="color:#D4AF37;font-size:15px;font-weight:bold;padding-right:10px;vertical-align:top;">&#10003;</td>
                                                        <td style="font-size:14px;color:#F5F5F5;line-height:1.5;">Coupons and loyalty rewards program</td>
                                                    </tr></table>
                                                </td></tr>
                                            @else
                                                <tr><td style="padding:6px 0;">
                                                    <table role="presentation" cellpadding="0" cellspacing="0" border="0"><tr>
                                                        <td style="color:#D4AF37;font-size:15px;font-weight:bold;padding-right:10px;vertical-align:top;">&#10003;</td>
                                                        <td style="font-size:14px;color:#F5F5F5;line-height:1.5;">Dashboard de analíticas avanzado con tendencias de visitantes</td>
                                                    </tr></table>
                                                </td></tr>
                                                <tr><td style="padding:6px 0;">
                                                    <table role="presentation" cellpadding="0" cellspacing="0" border="0"><tr>
                                                        <td style="color:#D4AF37;font-size:15px;font-weight:bold;padding-right:10px;vertical-align:top;">&#10003;</td>
                                                        <td style="font-size:14px;color:#F5F5F5;line-height:1.5;">Perfil destacado — aparece primero en los resultados</td>
                                                    </tr></table>
                                                </td></tr>
                                                <tr><td style="padding:6px 0;">
                                                    <table role="presentation" cellpadding="0" cellspacing="0" border="0"><tr>
                                                        <td style="color:#D4AF37;font-size:15px;font-weight:bold;padding-right:10px;vertical-align:top;">&#10003;</td>
                                                        <td style="font-size:14px;color:#F5F5F5;line-height:1.5;">Indexación SEO prioritaria y Google Booster</td>
                                                    </tr></table>
                                                </td></tr>
                                                <tr><td style="padding:6px 0;">
                                                    <table role="presentation" cellpadding="0" cellspacing="0" border="0"><tr>
                                                        <td style="color:#D4AF37;font-size:15px;font-weight:bold;padding-right:10px;vertical-align:top;">&#10003;</td>
                                                        <td style="font-size:14px;color:#F5F5F5;line-height:1.5;">Email marketing para llegar a tus clientes</td>
                                                    </tr></table>
                                                </td></tr>
                                                <tr><td style="padding:6px 0;">
                                                    <table role="presentation" cellpadding="0" cellspacing="0" border="0"><tr>
                                                        <td style="color:#D4AF37;font-size:15px;font-weight:bold;padding-right:10px;vertical-align:top;">&#10003;</td>
                                                        <td style="font-size:14px;color:#F5F5F5;line-height:1.5;">Cupones y programa de lealtad para clientes frecuentes</td>
                                                    </tr></table>
                                                </td></tr>
                                            @endif
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- 5. WHAT HAPPENS NOTE -->
                    <tr>
                        <td style="padding:0 24px 24px;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td style="background-color:rgba(139,30,30,0.15);border:1px solid rgba(139,30,30,0.4);border-radius:8px;padding:16px;">
                                        <p style="margin:0;font-size:14px;color:#F5F5F5;line-height:1.6;text-align:center;">
                                            @if($restaurant->country === 'US')
                                                When your trial ends, your plan converts to
                                                <strong style="color:#D4AF37;">Elite at $79/month</strong>.
                                                If you prefer the free plan, you can downgrade below.
                                            @else
                                                Al terminar tu prueba, tu plan se activa como
                                                <strong style="color:#D4AF37;">Elite a $79/mes</strong>.
                                                Si prefieres el plan gratuito, puedes bajar de plan abajo.
                                            @endif
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- 6. PRIMARY CTA -->
                    <tr>
                        <td style="padding:0 24px 16px;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td align="center" style="background-color:#D4AF37;border-radius:8px;">
                                        <a href="{{ config('app.url') }}/owner/my-subscription"
                                           style="display:block;padding:18px 24px;font-size:17px;font-weight:bold;color:#0B0B0B;text-decoration:none;text-align:center;letter-spacing:0.5px;">
                                            @if($restaurant->country === 'US')
                                                Keep My Elite Benefits &rarr;
                                            @else
                                                Mantener mis beneficios Elite &rarr;
                                            @endif
                                        </a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- 7. SECONDARY CTA (downgrade) -->
                    <tr>
                        <td style="padding:0 24px 28px;text-align:center;">
                            <a href="{{ config('app.url') }}/owner/my-subscription?action=downgrade"
                               style="font-size:13px;color:#6B7280;text-decoration:underline;">
                                @if($restaurant->country === 'US')
                                    I prefer the free plan
                                @else
                                    Prefiero el plan gratuito
                                @endif
                            </a>
                        </td>
                    </tr>

                    <!-- 8. FOOTER -->
                    <tr>
                        <td style="border-top:1px solid #2A2A2A;padding:20px 24px;text-align:center;">
                            <p style="margin:0 0 8px;font-size:12px;color:#6B7280;">
                                FAMER &mdash; Famous Mexican Restaurants
                            </p>
                            <p style="margin:0 0 8px;font-size:12px;color:#6B7280;">
                                <a href="mailto:unsubscribe@restaurantesmexicanosfamosos.com?subject=Unsubscribe {{ $user->email }}"
                                   style="color:#6B7280;text-decoration:underline;">
                                    @if($restaurant->country === 'US')
                                        Unsubscribe
                                    @else
                                        Cancelar suscripci&oacute;n
                                    @endif
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
