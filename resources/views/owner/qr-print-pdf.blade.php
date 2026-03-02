<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        @page {
            size: letter;
            margin: 0;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            background: white;
            color: #333;
        }
        .page {
            width: 100%;
            position: relative;
        }

        /* Ribbon - Mexican flag colors */
        .ribbon-table {
            width: 100%;
            border-collapse: collapse;
        }
        .ribbon-table td {
            height: 8px;
            width: 33.33%;
        }
        .ribbon-green { background-color: #166534; }
        .ribbon-white { background-color: #ffffff; }
        .ribbon-red { background-color: #DC2626; }

        /* Header */
        .header {
            background-color: #D4A54A;
            padding: 25px 40px 30px;
            text-align: center;
        }
        .famer-logo {
            font-size: 28px;
            font-weight: bold;
            color: #1f2937;
            letter-spacing: 4px;
            text-transform: uppercase;
            margin-bottom: 3px;
        }
        .famer-year {
            font-size: 13px;
            color: #5a4520;
            font-weight: 600;
            letter-spacing: 2px;
        }

        /* Body */
        .body-content {
            padding: 30px 60px 20px;
            text-align: center;
        }

        /* Highlight box */
        .highlight-box {
            background-color: #FFF8EC;
            border: 2px solid #D4A54A;
            border-radius: 10px;
            padding: 18px 25px;
            margin: 0 auto 22px;
            max-width: 440px;
        }
        .highlight-text {
            color: #78350f;
            font-size: 15px;
            font-weight: 500;
            line-height: 1.7;
        }
        .rest-name {
            font-size: 22px;
            font-weight: bold;
            color: #92400e;
            display: block;
            margin: 6px 0;
        }
        .highlight-bold {
            color: #b45309;
            font-weight: bold;
        }

        /* CTA */
        .cta-text {
            font-size: 15px;
            color: #4b5563;
            line-height: 1.7;
            margin-bottom: 22px;
        }
        .cta-bold {
            color: #b45309;
            font-weight: bold;
        }

        /* QR Section */
        .qr-section {
            text-align: center;
            margin-bottom: 18px;
        }
        .qr-frame {
            display: inline-block;
            padding: 10px;
            border: 4px solid #D4A54A;
            border-radius: 12px;
            margin-bottom: 10px;
            background-color: #ffffff;
        }
        .qr-frame img {
            width: 200px;
            height: 200px;
        }
        .scan-text {
            font-size: 14px;
            color: #6b7280;
            font-weight: 500;
            margin-top: 6px;
        }
        .scan-bold {
            color: #d97706;
            font-weight: bold;
            font-size: 16px;
            display: block;
            margin-top: 4px;
        }

        /* Vote URL */
        .vote-url-box {
            background-color: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 10px 25px;
            margin: 0 auto 20px;
            display: inline-block;
        }
        .vote-label {
            color: #9ca3af;
            font-size: 11px;
            margin-bottom: 2px;
        }
        .vote-link {
            color: #2563eb;
            font-size: 13px;
            text-decoration: none;
        }

        /* Footer */
        .footer-bar {
            background-color: #1f2937;
            padding: 14px 40px;
            text-align: center;
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
        }
        .footer-text {
            color: #d1d5db;
            font-size: 12px;
        }
        .footer-url {
            color: #9ca3af;
            font-size: 10px;
            margin-top: 2px;
        }
        .footer-logo-img {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            vertical-align: middle;
            margin-right: 6px;
        }

        /* Decorative borders */
        .deco-border {
            border: 2px solid #D4A54A;
            border-radius: 10px;
            margin: 0 20px;
            padding: 5px 0;
            min-height: 600px;
        }
    </style>
</head>
<body>
    <div class="page">
        <!-- Mexican Ribbon -->
        <table class="ribbon-table">
            <tr>
                <td class="ribbon-green"></td>
                <td class="ribbon-white"></td>
                <td class="ribbon-red"></td>
            </tr>
        </table>

        <!-- Header -->
        <div class="header">
            <div style="font-size: 18px; margin-bottom: 2px; color: #1f2937; letter-spacing: 8px;">* * *</div>
            <div class="famer-logo">FAMER Awards</div>
            <div class="famer-year">Los Mejores Restaurantes Mexicanos {{ date('Y') }}</div>
        </div>

        <!-- Decorative border wrapper -->
        <div class="deco-border">
            <!-- Body -->
            <div class="body-content">

                <!-- Logo -->
                @if(!empty($logoBase64))
                <div style="margin-bottom: 15px;">
                    <img src="{{ $logoBase64 }}" style="width: 60px; height: 60px; border-radius: 50%; border: 3px solid #D4A54A;" />
                </div>
                @endif

                <!-- Highlight Box -->
                <div class="highlight-box">
                    <div class="highlight-text">
                        &#161;Esta es tu oportunidad de que
                        <span class="rest-name">{{ $restaurant->name }}</span>
                        est&eacute; entre los <span class="highlight-bold">Top 10</span> restaurantes mexicanos este {{ date('Y') }}!
                    </div>
                </div>

                <!-- CTA -->
                <div class="cta-text">
                    Escanea el c&oacute;digo QR y <span class="cta-bold">vota por nosotros</span>.<br>
                    &#161;Tu voto cuenta para posicionarnos entre los mejores!
                </div>

                <!-- QR Code -->
                <div class="qr-section">
                    @if(!empty($qrBase64))
                    <div class="qr-frame">
                        <img src="{{ $qrBase64 }}" alt="QR Code" />
                    </div>
                    @else
                    <div style="width: 200px; height: 200px; border: 2px dashed #ccc; display: inline-block; line-height: 200px; color: #999;">QR Code</div>
                    @endif
                    <div class="scan-text">
                        Escanea con tu celular para votar
                        <span class="scan-bold">&#161;Cada voto cuenta!</span>
                    </div>
                </div>

                <!-- Vote URL -->
                <div class="vote-url-box">
                    <div class="vote-label">O visita:</div>
                    <div class="vote-link">{{ $voteUrl }}</div>
                </div>

            </div>
        </div>

        <!-- Footer -->
        <div class="footer-bar">
            <div class="footer-text">
                @if(!empty($logoBase64))
                <img src="{{ $logoBase64 }}" class="footer-logo-img" />
                @endif
                Restaurantes Mexicanos Famosos
            </div>
            <div class="footer-url">restaurantesmexicanosfamosos.com</div>
        </div>
    </div>
</body>
</html>
