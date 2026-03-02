<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Certificado FAMER - {{ $restaurant->name }}</title>
    <style>
        @page {
            size: 792pt 612pt;
            margin: 0;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        html, body {
            margin: 0;
            padding: 0;
            width: 792pt;
            height: 612pt;
            overflow: hidden;
            background-color: #0f172a;
        }
        
        body {
            font-family: 'DejaVu Sans', sans-serif;
            color: #ffffff;
        }
        
        .certificate {
            width: 752pt;
            height: 572pt;
            margin: 20pt;
            padding: 12pt;
            background-color: #1a1a2e;
            border: 3pt solid #d4af37;
            overflow: hidden;
            page-break-inside: avoid;
        }
        
        .inner-border {
            border: 1.5pt solid #d4af37;
            width: 100%;
            height: 100%;
            text-align: center;
            padding: 30pt 25pt 20pt;
            display: table;
        }
        
        .content-wrapper {
            display: table-cell;
            vertical-align: middle;
        }
        
        .logo-text {
            font-size: 52pt;
            font-weight: bold;
            color: #d4af37;
            letter-spacing: 8pt;
        }
        
        .logo-subtitle {
            font-size: 11pt;
            color: #94a3b8;
            letter-spacing: 4pt;
            text-transform: uppercase;
            margin-top: 5pt;
            margin-bottom: 25pt;
        }
        
        .certificate-title {
            font-size: 38pt;
            color: #ffffff;
            text-transform: uppercase;
            letter-spacing: 10pt;
            margin-bottom: 10pt;
            font-weight: normal;
        }
        
        .certificate-subtitle {
            font-size: 13pt;
            color: #d4af37;
            letter-spacing: 3pt;
            text-transform: uppercase;
            margin-bottom: 18pt;
        }
        
        .divider {
            width: 140pt;
            height: 2pt;
            background-color: #d4af37;
            margin: 0 auto 18pt;
        }
        
        .certify-text {
            font-size: 11pt;
            color: #94a3b8;
            margin-bottom: 8pt;
        }
        
        .restaurant-name {
            font-size: 36pt;
            color: #d4af37;
            font-weight: bold;
            margin-bottom: 8pt;
        }
        
        .location {
            font-size: 14pt;
            color: #e2e8f0;
            margin-bottom: 18pt;
        }
        
        .description {
            font-size: 10pt;
            color: #94a3b8;
            line-height: 1.5;
            max-width: 450pt;
            margin: 0 auto 20pt;
        }
        
        .verified-badge {
            display: inline-block;
            background-color: #d4af37;
            color: #1a1a2e;
            padding: 10pt 35pt;
            font-size: 12pt;
            font-weight: bold;
            letter-spacing: 2pt;
            text-transform: uppercase;
            margin-bottom: 25pt;
        }
        
        .footer {
            margin-top: 15pt;
        }
        
        .footer-table {
            width: 100%;
            max-width: 600pt;
            margin: 0 auto;
            border-collapse: collapse;
        }
        
        .footer-table td {
            padding: 3pt;
            vertical-align: bottom;
        }
        
        .footer-left {
            text-align: left;
            width: 30%;
        }
        
        .footer-center {
            text-align: center;
            width: 40%;
        }
        
        .footer-right {
            text-align: right;
            width: 30%;
        }
        
        .certificate-id {
            font-size: 8pt;
            color: #64748b;
        }
        
        .website {
            font-size: 8pt;
            color: #64748b;
        }
        
        .signature-line {
            width: 100pt;
            border-top: 1pt solid #d4af37;
            margin: 0 auto 3pt;
        }
        
        .signature-text {
            font-size: 10pt;
            color: #e2e8f0;
        }
        
        .signature-title {
            font-size: 7pt;
            color: #64748b;
        }
        
        .issue-label {
            font-size: 7pt;
            color: #64748b;
            text-transform: uppercase;
        }
        
        .issue-date {
            font-size: 10pt;
            color: #e2e8f0;
        }
    </style>
</head>
<body>
    <div class="certificate">
        <div class="inner-border">
            <div class="content-wrapper">
                <div class="logo-text">FAMER</div>
                <div class="logo-subtitle">Famous Mexican Restaurants</div>
                
                <div class="certificate-title">Certificado</div>
                <div class="certificate-subtitle">Restaurante Mexicano Verificado</div>
                
                <div class="divider"></div>
                
                <div class="certify-text">Se certifica que el establecimiento</div>
                
                <div class="restaurant-name">{{ $restaurant->name }}</div>
                
                <div class="location">{{ $restaurant->city }}, {{ $restaurant->state->code ?? $restaurant->state }}</div>
                
                <div class="description">
                    Ha sido verificado y reconocido por FAMER (Famous Mexican Restaurants) como un autentico 
                    restaurante mexicano que cumple con los estandares de calidad, autenticidad y servicio 
                    que distinguen a la gastronomia mexicana.
                </div>
                
                <div class="verified-badge">VERIFICADO {{ $year }}</div>
                
                <div class="footer">
                    <table class="footer-table">
                        <tr>
                            <td class="footer-left">
                                <div class="certificate-id">ID: {{ $certificate_id }}</div>
                                <div class="website">restaurantesmexicanosfamosos.com</div>
                            </td>
                            <td class="footer-center">
                                <div class="signature-line"></div>
                                <div class="signature-text">FAMER Directory</div>
                                <div class="signature-title">Autoridad Certificadora</div>
                            </td>
                            <td class="footer-right">
                                <div class="issue-label">Fecha de Emision</div>
                                <div class="issue-date">{{ $issue_date }}</div>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
