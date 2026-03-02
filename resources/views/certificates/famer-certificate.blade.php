<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Certificado FAMER - {{ $restaurant->name }}</title>
    <style>
        @page {
            margin: 0;
            size: 11in 8.5in;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: Georgia, 'Times New Roman', serif;
            background: #f8f4e8;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .certificate {
            width: 11in;
            height: 8.5in;
            background: linear-gradient(145deg, #fffdf5 0%, #fef9e7 40%, #fdf2d0 100%);
            position: relative;
            overflow: hidden;
        }
        /* Gold border */
        .certificate::before {
            content: '';
            position: absolute;
            top: 20px; left: 20px; right: 20px; bottom: 20px;
            border: 3px solid #b8860b;
            border-radius: 8px;
        }
        .certificate::after {
            content: '';
            position: absolute;
            top: 28px; left: 28px; right: 28px; bottom: 28px;
            border: 1px solid #d4a54a;
            border-radius: 6px;
        }
        /* Corner ornaments */
        .corner { position: absolute; width: 60px; height: 60px; }
        .corner svg { width: 100%; height: 100%; }
        .corner-tl { top: 32px; left: 32px; }
        .corner-tr { top: 32px; right: 32px; transform: scaleX(-1); }
        .corner-bl { bottom: 32px; left: 32px; transform: scaleY(-1); }
        .corner-br { bottom: 32px; right: 32px; transform: scale(-1, -1); }

        .content {
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            padding: 60px 80px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
        }

        /* Mexican flag ribbon */
        .flag-ribbon {
            width: 280px;
            height: 6px;
            display: flex;
            margin: 0 auto;
            border-radius: 3px;
            overflow: hidden;
        }
        .flag-green { background: #006847; flex: 1; }
        .flag-white { background: #ffffff; flex: 1; border-top: 1px solid #ddd; border-bottom: 1px solid #ddd; }
        .flag-red { background: #CE1126; flex: 1; }

        .org-name {
            font-size: 14px;
            color: #8b6914;
            text-transform: uppercase;
            letter-spacing: 6px;
            margin-top: 15px;
        }
        .cert-title {
            font-size: 40px;
            font-weight: bold;
            color: #5c3d0e;
            margin: 8px 0;
            letter-spacing: 3px;
        }
        .subtitle {
            font-size: 16px;
            color: #8b6914;
            font-style: italic;
            letter-spacing: 2px;
        }
        .divider {
            width: 50%;
            height: 2px;
            background: linear-gradient(90deg, transparent, #b8860b, transparent);
            margin: 18px auto;
        }
        .certifies {
            font-size: 15px;
            color: #6b5a2e;
            font-style: italic;
        }
        .restaurant-name {
            font-size: 38px;
            font-weight: bold;
            color: #2d1f05;
            margin: 12px 0 6px;
        }
        .location {
            font-size: 14px;
            color: #6b5a2e;
            margin-bottom: 12px;
        }
        .stars {
            font-size: 22px;
            color: #d4a54a;
            letter-spacing: 8px;
            margin: 6px 0;
        }
        .verification-box {
            background: linear-gradient(135deg, #fef9e7, #fdf2d0);
            border: 2px solid #d4a54a;
            border-radius: 8px;
            padding: 10px 35px;
            margin: 14px 0;
        }
        .verification-label {
            font-size: 10px;
            color: #8b6914;
            text-transform: uppercase;
            letter-spacing: 3px;
        }
        .verification-status {
            font-size: 22px;
            font-weight: bold;
            color: #5c3d0e;
            margin-top: 2px;
        }
        .description {
            font-size: 12px;
            color: #6b5a2e;
            max-width: 500px;
            line-height: 1.5;
            margin: 10px 0;
        }
        .footer {
            position: absolute;
            bottom: 50px;
            left: 100px;
            right: 100px;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
        }
        .footer-section { text-align: center; }
        .signature-line {
            width: 140px;
            border-top: 1px solid #8b6914;
            margin-bottom: 4px;
        }
        .signature-text {
            font-size: 9px;
            color: #8b6914;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .cert-id {
            font-size: 9px;
            color: #999;
            font-family: 'Courier New', monospace;
        }
        .issue-date {
            font-size: 10px;
            color: #6b5a2e;
            margin-top: 3px;
        }
        .year-badge {
            display: inline-block;
            background: linear-gradient(135deg, #d4a54a, #b8860b);
            color: white;
            padding: 5px 25px;
            border-radius: 20px;
            font-size: 18px;
            font-weight: bold;
            letter-spacing: 2px;
            margin-top: 5px;
        }

        /* Print styles */
        @media print {
            body { background: white; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>
    @if(!isset($isPdf))
    <div class="no-print" style="position: fixed; top: 20px; right: 20px; z-index: 100; display: flex; gap: 10px;">
        <a href="{{ url('/owner/certificate-pdf/' . $restaurant->id) }}"
           style="display: inline-flex; align-items: center; gap: 8px; background: linear-gradient(135deg, #d4a54a, #b8860b); color: white; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-family: Arial, sans-serif; font-size: 14px; font-weight: 600; box-shadow: 0 2px 8px rgba(0,0,0,0.2);">
            <svg style="width:16px;height:16px" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
            Descargar PDF
        </a>
        <button onclick="window.print()"
                style="display: inline-flex; align-items: center; gap: 8px; background: #374151; color: white; padding: 10px 20px; border-radius: 8px; border: none; cursor: pointer; font-family: Arial, sans-serif; font-size: 14px; font-weight: 600; box-shadow: 0 2px 8px rgba(0,0,0,0.2);">
            <svg style="width:16px;height:16px" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
            Imprimir
        </button>
    </div>
    @endif

    <div class="certificate">
        <!-- Corner ornaments using simple L-shapes -->
        <div class="corner corner-tl">
            <svg viewBox="0 0 60 60"><path d="M5 55 L5 5 L55 5" fill="none" stroke="#b8860b" stroke-width="2"/><path d="M12 48 L12 12 L48 12" fill="none" stroke="#d4a54a" stroke-width="1"/></svg>
        </div>
        <div class="corner corner-tr">
            <svg viewBox="0 0 60 60"><path d="M5 55 L5 5 L55 5" fill="none" stroke="#b8860b" stroke-width="2"/><path d="M12 48 L12 12 L48 12" fill="none" stroke="#d4a54a" stroke-width="1"/></svg>
        </div>
        <div class="corner corner-bl">
            <svg viewBox="0 0 60 60"><path d="M5 55 L5 5 L55 5" fill="none" stroke="#b8860b" stroke-width="2"/><path d="M12 48 L12 12 L48 12" fill="none" stroke="#d4a54a" stroke-width="1"/></svg>
        </div>
        <div class="corner corner-br">
            <svg viewBox="0 0 60 60"><path d="M5 55 L5 5 L55 5" fill="none" stroke="#b8860b" stroke-width="2"/><path d="M12 48 L12 12 L48 12" fill="none" stroke="#d4a54a" stroke-width="1"/></svg>
        </div>

        <div class="content">
            <div class="flag-ribbon">
                <div class="flag-green"></div>
                <div class="flag-white"></div>
                <div class="flag-red"></div>
            </div>

            <div class="org-name">Restaurantes Mexicanos Famosos</div>
            <div class="cert-title">CERTIFICADO</div>
            <div class="subtitle">Restaurante Mexicano Verificado</div>
            <div class="year-badge">{{ $year }}</div>

            <div class="divider"></div>

            <div class="certifies">Se certifica que el establecimiento</div>
            <div class="restaurant-name">{{ $restaurant->name }}</div>
            <div class="location">
                @if($restaurant->city || $restaurant->state)
                    {{ $restaurant->city }}{{ $restaurant->city && $restaurant->state ? ', ' : '' }}{{ $restaurant->state->name ?? $restaurant->state_id ?? '' }}
                @endif
            </div>

            <div class="stars">* * * * *</div>

            <div class="verification-box">
                <div class="verification-label">Estatus de Verificacion</div>
                <div class="verification-status">Restaurante Verificado</div>
            </div>

            <div class="description">
                Ha sido verificado y reconocido como un autentico restaurante de comida mexicana,
                cumpliendo con los estandares de calidad, autenticidad y servicio establecidos
                por el programa FAMER (Famous Mexican Restaurants).
            </div>
        </div>

        <div class="footer">
            <div class="footer-section">
                <div class="signature-line"></div>
                <div class="signature-text">Director FAMER</div>
            </div>

            <div class="footer-section">
                <div class="cert-id">{{ $certificate_id }}</div>
                <div class="issue-date">Emitido: {{ $issue_date }}</div>
            </div>

            <div class="footer-section">
                <div class="signature-line"></div>
                <div class="signature-text">Comite de Verificacion</div>
            </div>
        </div>
    </div>
</body>
</html>
