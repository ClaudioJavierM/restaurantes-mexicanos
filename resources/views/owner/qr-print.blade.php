<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Votación - {{ $restaurant->name }} | FAMER Awards {{ date('Y') }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700;900&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        @page {
            size: letter;
            margin: 0;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Poppins', sans-serif;
            background: #111827;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
            color-adjust: exact !important;
        }

        .print-page {
            width: 8.5in;
            min-height: 11in;
            background: white;
            display: flex;
            flex-direction: column;
            align-items: center;
            overflow: hidden;
            position: relative;
        }

        /* Mexican ribbon stripe */
        .ribbon {
            width: 100%;
            height: 8px;
            background: linear-gradient(90deg, #166534 33.33%, #ffffff 33.33%, #ffffff 66.66%, #DC2626 66.66%) !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        /* Header */
        .header {
            width: 100%;
            background: linear-gradient(135deg, #B8892E 0%, #D4A54A 30%, #E8C67A 50%, #D4A54A 70%, #B8892E 100%) !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
            padding: 1.75rem 2rem 2.5rem;
            text-align: center;
            position: relative;
        }
        .header::after {
            content: '';
            position: absolute;
            bottom: -1px;
            left: 0; right: 0;
            height: 30px;
            background: white !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
            clip-path: ellipse(55% 100% at 50% 100%);
        }
        .famer-logo {
            font-family: 'Playfair Display', serif;
            font-size: 2rem;
            font-weight: 900;
            color: #1f2937;
            letter-spacing: 4px;
            text-transform: uppercase;
            margin-bottom: 0.15rem;
        }
        .famer-year {
            font-size: 0.875rem;
            color: rgba(31,41,55,0.65);
            font-weight: 600;
            letter-spacing: 2px;
        }
        .trophy { font-size: 2.5rem; margin-bottom: 0.25rem; display: block; }

        /* Body */
        .body {
            padding: 1.5rem 3rem 1rem;
            text-align: center;
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .highlight-box {
            background: #FFF8EC !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
            border: 2px solid #D4A54A;
            border-radius: 0.75rem;
            padding: 1.25rem 2rem;
            margin-bottom: 1.25rem;
            max-width: 480px;
        }
        .highlight-box p {
            color: #78350f;
            font-size: 1.1rem;
            font-weight: 500;
            line-height: 1.6;
        }
        .highlight-box strong {
            color: #b45309;
            font-weight: 700;
        }
        .highlight-box .rest-name {
            font-family: 'Playfair Display', serif;
            font-size: 1.5rem;
            font-weight: 700;
            color: #92400e;
            display: block;
            margin: 0.25rem 0;
        }

        .cta-text {
            font-size: 1.05rem;
            color: #4b5563;
            line-height: 1.6;
            margin-bottom: 1.5rem;
            max-width: 420px;
        }
        .cta-text strong {
            color: #b45309;
        }

        /* QR Section */
        .qr-section {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 1.25rem;
        }
        .qr-frame {
            background: white;
            padding: 0.75rem;
            border-radius: 1rem;
            border: 4px solid #D4A54A;
            box-shadow: 0 4px 20px rgba(212,165,74,0.25);
            margin-bottom: 0.75rem;
        }
        .qr-frame img {
            width: 200px;
            height: 200px;
            display: block;
        }
        .scan-text {
            font-size: 0.95rem;
            color: #6b7280;
            font-weight: 500;
        }
        .scan-text strong {
            color: #d97706;
            display: block;
            font-size: 1.1rem;
            margin-top: 0.25rem;
        }

        /* Vote URL */
        .vote-url {
            background: #f9fafb !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
            border: 1px solid #e5e7eb;
            border-radius: 0.5rem;
            padding: 0.625rem 1.5rem;
            margin-bottom: 1rem;
        }
        .vote-url span {
            color: #9ca3af;
            font-size: 0.75rem;
            display: block;
        }
        .vote-url a {
            color: #2563eb;
            font-size: 0.85rem;
            text-decoration: none;
            word-break: break-all;
        }

        /* Footer */
        .footer {
            width: 100%;
            background: #1f2937 !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
            padding: 1rem 2rem;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }
        .footer-logo {
            font-size: 0.8rem;
            color: #d1d5db;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .footer-logo img {
            height: 28px;
            border-radius: 50%;
            border: 2px solid rgba(212,165,74,0.5);
        }
        .footer-url {
            font-size: 0.7rem;
            color: #9ca3af;
            margin-left: 0.5rem;
            padding-left: 0.5rem;
            border-left: 1px solid #4b5563;
        }

        /* Decorative corner accents */
        .corner-decor {
            position: absolute;
            width: 60px;
            height: 60px;
            border-color: #D4A54A;
            border-style: solid;
            border-width: 0;
        }
        .corner-tl { top: 60px; left: 20px; border-top-width: 3px; border-left-width: 3px; border-top-left-radius: 12px; }
        .corner-tr { top: 60px; right: 20px; border-top-width: 3px; border-right-width: 3px; border-top-right-radius: 12px; }
        .corner-bl { bottom: 55px; left: 20px; border-bottom-width: 3px; border-left-width: 3px; border-bottom-left-radius: 12px; }
        .corner-br { bottom: 55px; right: 20px; border-bottom-width: 3px; border-right-width: 3px; border-bottom-right-radius: 12px; }

        /* Screen-only elements */
        .print-actions {
            text-align: center;
            margin-top: 1.5rem;
        }
        .btn-print {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: linear-gradient(135deg, #D4A54A, #B8892E);
            color: #1f2937;
            padding: 0.75rem 2rem;
            border-radius: 0.5rem;
            border: none;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            font-family: 'Poppins', sans-serif;
            transition: transform 0.2s;
        }
        .btn-print:hover { transform: scale(1.05); }
        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: transparent;
            color: #9ca3af;
            padding: 0.75rem 1.5rem;
            border-radius: 0.5rem;
            border: 1px solid #374151;
            font-size: 0.875rem;
            cursor: pointer;
            font-family: 'Poppins', sans-serif;
            text-decoration: none;
            margin-left: 0.75rem;
        }
        .btn-back:hover { border-color: #6b7280; color: #e5e7eb; }

        @media print {
            body {
                background: white !important;
                padding: 0 !important;
                min-height: auto;
            }
            .print-page {
                width: 100%;
                min-height: 100vh;
                box-shadow: none;
                border-radius: 0;
            }
            .print-actions {
                display: none !important;
            }
            .corner-decor {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
        }

        @media screen {
            .print-page {
                border-radius: 0.75rem;
                box-shadow: 0 25px 60px rgba(0,0,0,0.5);
                max-width: 650px;
            }
        }
    </style>
</head>
<body>
    <div class="print-page">
        <div class="ribbon"></div>

        <!-- Decorative corners -->
        <div class="corner-decor corner-tl"></div>
        <div class="corner-decor corner-tr"></div>
        <div class="corner-decor corner-bl"></div>
        <div class="corner-decor corner-br"></div>

        <div class="header">
            <span class="trophy">&#127942;</span>
            <div class="famer-logo">FAMER Awards</div>
            <div class="famer-year">Los Mejores Restaurantes Mexicanos {{ date('Y') }}</div>
        </div>

        <div class="body">
            <div class="highlight-box">
                <p>
                    &#161;Esta es tu oportunidad de que
                    <span class="rest-name">{{ $restaurant->name }}</span>
                    est&eacute; entre los <strong>Top 10</strong> restaurantes mexicanos este {{ date('Y') }}!
                </p>
            </div>

            <p class="cta-text">
                Escanea el c&oacute;digo QR y <strong>vota por nosotros</strong>.<br>
                &#161;Tu voto cuenta para posicionarnos entre los mejores!
            </p>

            <div class="qr-section">
                <div class="qr-frame">
                    <img src="{{ $qrUrl }}" alt="QR Code - Vota por {{ $restaurant->name }}">
                </div>
                <div class="scan-text">
                    Escanea con tu celular para votar
                    <strong>&#161;Cada voto cuenta!</strong>
                </div>
            </div>

            <div class="vote-url">
                <span>O visita:</span>
                <a href="{{ $voteUrl }}">{{ $voteUrl }}</a>
            </div>
        </div>

        <div class="footer">
            <div class="footer-logo">
                <img src="https://restaurantesmexicanosfamosos.com/images/branding/icon.png" alt="Logo">
                Restaurantes Mexicanos Famosos
            </div>
            <div class="footer-url">restaurantesmexicanosfamosos.com</div>
        </div>
    </div>

    <div class="print-actions">
        <a href="{{ url('/owner/qr-pdf/' . $restaurant->id) }}" class="btn-print" style="text-decoration:none;">
            Descargar PDF
        </a>
        <button class="btn-print" onclick="window.print()" style="background:linear-gradient(135deg,#6b7280,#4b5563); margin-left:0.75rem;">
            Imprimir
        </button>
        <a href="{{ url('/owner') }}" class="btn-back">
            Volver al Dashboard
        </a>
    </div>
</body>
</html>
