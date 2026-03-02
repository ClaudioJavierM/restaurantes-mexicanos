<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{{ $campaignName ?? 'FAMER' }}</title>
    @if(!empty($previewText))
    <meta name="description" content="{{ $previewText }}">
    <!--[if !mso]><!-->
    <span style="display:none !important;visibility:hidden;mso-hide:all;font-size:1px;color:#ffffff;line-height:1px;max-height:0px;max-width:0px;opacity:0;overflow:hidden;">
        {{ $previewText }}
    </span>
    <!--<![endif]-->
    @endif
    <style>
        /* Reset styles */
        body, table, td, p, a, li { -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; }
        table, td { mso-table-lspace: 0pt; mso-table-rspace: 0pt; }
        img { -ms-interpolation-mode: bicubic; border: 0; height: auto; line-height: 100%; outline: none; text-decoration: none; }

        body {
            margin: 0;
            padding: 0;
            width: 100% !important;
            height: 100% !important;
            background-color: #f5f5f5;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
        }

        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
        }

        .email-header {
            background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
            padding: 30px 40px;
            text-align: center;
        }

        .email-header img {
            max-height: 50px;
        }

        .email-header h1 {
            color: #ffffff;
            font-size: 24px;
            margin: 10px 0 0 0;
            font-weight: 600;
        }

        .email-body {
            padding: 40px;
            color: #374151;
            font-size: 16px;
            line-height: 1.6;
        }

        .email-body h1, .email-body h2, .email-body h3 {
            color: #111827;
            margin-top: 0;
        }

        .email-body a {
            color: #dc2626;
            text-decoration: underline;
        }

        .email-body a:hover {
            color: #b91c1c;
        }

        .cta-button {
            display: inline-block;
            background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
            color: #ffffff !important;
            text-decoration: none !important;
            padding: 14px 32px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 16px;
            margin: 20px 0;
        }

        .cta-button:hover {
            background: linear-gradient(135deg, #b91c1c 0%, #991b1b 100%);
        }

        .email-footer {
            background-color: #f9fafb;
            padding: 30px 40px;
            text-align: center;
            border-top: 1px solid #e5e7eb;
        }

        .email-footer p {
            color: #6b7280;
            font-size: 13px;
            margin: 5px 0;
        }

        .email-footer a {
            color: #6b7280;
            text-decoration: underline;
        }

        .social-links {
            margin: 15px 0;
        }

        .social-links a {
            display: inline-block;
            margin: 0 8px;
        }

        .test-banner {
            background-color: #fef3c7;
            border: 1px solid #f59e0b;
            color: #92400e;
            padding: 12px;
            text-align: center;
            font-weight: 600;
        }

        /* Responsive */
        @media screen and (max-width: 600px) {
            .email-container {
                width: 100% !important;
            }
            .email-header, .email-body, .email-footer {
                padding: 20px !important;
            }
        }
    </style>
</head>
<body>
    <center style="width: 100%; background-color: #f5f5f5; padding: 40px 0;">
        <!--[if mso]>
        <table role="presentation" width="600" cellpadding="0" cellspacing="0" border="0" align="center">
        <tr>
        <td>
        <![endif]-->

        <table role="presentation" class="email-container" cellpadding="0" cellspacing="0" border="0" align="center" width="600" style="margin: auto;">
            @if($isTest ?? false)
            <tr>
                <td class="test-banner">
                    ⚠️ ESTE ES UN EMAIL DE PRUEBA - No será enviado a destinatarios reales
                </td>
            </tr>
            @endif

            <!-- Header -->
            <tr>
                <td class="email-header">
                    <img src="{{ asset('images/famer-logo-white.png') }}" alt="FAMER" style="max-height: 40px;">
                    <p style="color: rgba(255,255,255,0.9); margin: 10px 0 0 0; font-size: 14px;">
                        Find Authentic Mexican Eats & Restaurants
                    </p>
                </td>
            </tr>

            <!-- Body Content -->
            <tr>
                <td class="email-body">
                    {!! $htmlContent !!}
                </td>
            </tr>

            <!-- Footer -->
            <tr>
                <td class="email-footer">
                    <div class="social-links">
                        <a href="https://facebook.com/famerapp" target="_blank">
                            <img src="{{ asset('images/email/icon-facebook.png') }}" alt="Facebook" width="24" height="24">
                        </a>
                        <a href="https://instagram.com/famerapp" target="_blank">
                            <img src="{{ asset('images/email/icon-instagram.png') }}" alt="Instagram" width="24" height="24">
                        </a>
                        <a href="https://twitter.com/famerapp" target="_blank">
                            <img src="{{ asset('images/email/icon-twitter.png') }}" alt="Twitter" width="24" height="24">
                        </a>
                    </div>

                    <p>
                        <strong>FAMER - Restaurantes Mexicanos</strong><br>
                        El directorio más grande de restaurantes mexicanos auténticos
                    </p>

                    <p>
                        <a href="{{ url('/') }}">Visitar FAMER</a> |
                        <a href="{{ url('/contact') }}">Contacto</a> |
                        <a href="{!! $unsubscribeUrl ?? url('/unsubscribe') !!}">Cancelar suscripción</a>
                    </p>

                    <p style="font-size: 11px; color: #9ca3af; margin-top: 20px;">
                        © {{ date('Y') }} FAMER. Todos los derechos reservados.<br>
                        Este email fue enviado porque tu restaurante está listado en FAMER.
                    </p>
                </td>
            </tr>
        </table>

        <!--[if mso]>
        </td>
        </tr>
        </table>
        <![endif]-->
    </center>
</body>
</html>
