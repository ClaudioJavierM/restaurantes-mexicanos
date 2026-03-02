<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Certificado FAMER Awards - {{ $restaurant->name }}</title>
    <style>
        @page {
            margin: 0;
            size: 11in 8.5in landscape;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Georgia', 'Times New Roman', serif;
            background: linear-gradient(135deg, #fef3c7 0%, #fcd34d 50%, #f59e0b 100%);
            width: 11in;
            height: 8.5in;
            position: relative;
            overflow: hidden;
        }
        
        .certificate-container {
            width: 100%;
            height: 100%;
            padding: 0.5in;
            position: relative;
        }
        
        .certificate-inner {
            width: 100%;
            height: 100%;
            background: linear-gradient(180deg, #fffbeb 0%, #fef3c7 50%, #fde68a 100%);
            border: 4px solid #b45309;
            border-radius: 15px;
            padding: 30px 40px;
            position: relative;
            box-shadow: inset 0 0 40px rgba(180, 83, 9, 0.1);
        }
        
        .corner-ornament {
            position: absolute;
            width: 80px;
            height: 80px;
            border: 3px solid #b45309;
        }
        
        .corner-ornament.top-left {
            top: 10px;
            left: 10px;
            border-right: none;
            border-bottom: none;
            border-radius: 10px 0 0 0;
        }
        
        .corner-ornament.top-right {
            top: 10px;
            right: 10px;
            border-left: none;
            border-bottom: none;
            border-radius: 0 10px 0 0;
        }
        
        .corner-ornament.bottom-left {
            bottom: 10px;
            left: 10px;
            border-right: none;
            border-top: none;
            border-radius: 0 0 0 10px;
        }
        
        .corner-ornament.bottom-right {
            bottom: 10px;
            right: 10px;
            border-left: none;
            border-top: none;
            border-radius: 0 0 10px 0;
        }
        
        .header {
            text-align: center;
            margin-bottom: 15px;
        }
        
        .logo-section {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 15px;
            margin-bottom: 10px;
        }
        
        .trophy {
            font-size: 48px;
        }
        
        .brand-name {
            font-size: 28px;
            font-weight: bold;
            color: #92400e;
            text-transform: uppercase;
            letter-spacing: 4px;
        }
        
        .awards-title {
            font-size: 42px;
            font-weight: bold;
            color: #78350f;
            text-transform: uppercase;
            letter-spacing: 6px;
            margin: 10px 0;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
        }
        
        .year-badge {
            display: inline-block;
            background: linear-gradient(135deg, #f59e0b, #d97706);
            color: white;
            padding: 8px 30px;
            border-radius: 25px;
            font-size: 22px;
            font-weight: bold;
            letter-spacing: 3px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.2);
        }
        
        .divider {
            width: 60%;
            height: 2px;
            background: linear-gradient(90deg, transparent, #b45309, transparent);
            margin: 15px auto;
        }
        
        .main-content {
            text-align: center;
            margin: 15px 0;
        }
        
        .certifies-text {
            font-size: 16px;
            color: #78350f;
            margin-bottom: 10px;
            font-style: italic;
        }
        
        .restaurant-name {
            font-size: 36px;
            font-weight: bold;
            color: #451a03;
            margin: 10px 0;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.1);
        }
        
        .ranking-position {
            font-size: 60px;
            font-weight: bold;
            margin: 10px 0;
        }
        
        .position-gold {
            background: linear-gradient(180deg, #ffd700, #b8860b);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .position-silver {
            background: linear-gradient(180deg, #c0c0c0, #808080);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .position-bronze {
            background: linear-gradient(180deg, #cd7f32, #8b4513);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .badge-text {
            font-size: 24px;
            color: #92400e;
            font-weight: bold;
            margin: 10px 0;
        }
        
        .location {
            font-size: 16px;
            color: #78350f;
            margin: 5px 0;
        }
        
        .score-section {
            background: linear-gradient(135deg, #fef3c7, #fde68a);
            border: 2px solid #d97706;
            border-radius: 10px;
            padding: 10px 30px;
            display: inline-block;
            margin: 10px 0;
        }
        
        .score-label {
            font-size: 12px;
            color: #92400e;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        
        .score-value {
            font-size: 28px;
            font-weight: bold;
            color: #78350f;
        }
        
        .footer {
            position: absolute;
            bottom: 40px;
            left: 40px;
            right: 40px;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
        }
        
        .footer-left, .footer-right {
            text-align: center;
        }
        
        .signature-line {
            width: 150px;
            border-top: 1px solid #78350f;
            margin-bottom: 5px;
        }
        
        .signature-text {
            font-size: 10px;
            color: #92400e;
        }
        
        .certificate-id {
            font-size: 10px;
            color: #92400e;
            font-family: 'Courier New', monospace;
        }
        
        .issue-date {
            font-size: 11px;
            color: #78350f;
            margin-top: 5px;
        }
        
        .stars {
            font-size: 24px;
            color: #f59e0b;
            letter-spacing: 5px;
            margin: 5px 0;
        }
    </style>
</head>
<body>
    <div class="certificate-container">
        <div class="certificate-inner">
            <div class="corner-ornament top-left"></div>
            <div class="corner-ornament top-right"></div>
            <div class="corner-ornament bottom-left"></div>
            <div class="corner-ornament bottom-right"></div>
            
            <div class="header">
                <div class="logo-section">
                    <span class="trophy">🏆</span>
                    <span class="brand-name">Restaurantes Mexicanos</span>
                    <span class="trophy">🏆</span>
                </div>
                <div class="awards-title">FAMER Awards</div>
                <div class="year-badge">{{ $year }}</div>
            </div>
            
            <div class="divider"></div>
            
            <div class="main-content">
                <div class="certifies-text">Se certifica que</div>
                
                <div class="restaurant-name">{{ $restaurant->name }}</div>
                
                <div class="stars">★ ★ ★ ★ ★</div>
                
                <div class="ranking-position {{ $ranking->position == 1 ? 'position-gold' : ($ranking->position <= 3 ? 'position-silver' : 'position-bronze') }}">
                    #{{ $ranking->position }}
                </div>
                
                <div class="badge-text">{{ $ranking->badge_name }}</div>
                
                <div class="location">
                    📍 {{ $ranking->city }}, {{ $restaurant->state->name ?? '' }}
                </div>
                
                <div class="score-section">
                    <div class="score-label">Puntuación FAMER</div>
                    <div class="score-value">{{ number_format($ranking->score, 1) }} / 100</div>
                </div>
            </div>
            
            <div class="footer">
                <div class="footer-left">
                    <div class="signature-line"></div>
                    <div class="signature-text">Director FAMER Awards</div>
                </div>
                
                <div style="text-align: center;">
                    <div class="certificate-id">{{ $certificate_id }}</div>
                    <div class="issue-date">Emitido: {{ $issue_date }}</div>
                </div>
                
                <div class="footer-right">
                    <div class="signature-line"></div>
                    <div class="signature-text">Comité de Evaluación</div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
