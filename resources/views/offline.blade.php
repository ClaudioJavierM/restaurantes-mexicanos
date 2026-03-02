<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sin Conexión - FAMER</title>
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#dc2626">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .container {
            background: white;
            border-radius: 24px;
            padding: 48px 32px;
            text-align: center;
            max-width: 400px;
            width: 100%;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }
        .icon {
            font-size: 64px;
            margin-bottom: 24px;
        }
        h1 {
            color: #1f2937;
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 12px;
        }
        p {
            color: #6b7280;
            font-size: 16px;
            line-height: 1.6;
            margin-bottom: 32px;
        }
        .btn {
            display: inline-block;
            background: #dc2626;
            color: white;
            font-weight: 600;
            padding: 14px 28px;
            border-radius: 12px;
            text-decoration: none;
            transition: all 0.2s;
        }
        .btn:hover {
            background: #b91c1c;
            transform: translateY(-2px);
        }
        .tips {
            margin-top: 32px;
            padding-top: 24px;
            border-top: 1px solid #e5e7eb;
        }
        .tips h3 {
            color: #374151;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 12px;
        }
        .tips ul {
            list-style: none;
            text-align: left;
        }
        .tips li {
            color: #6b7280;
            font-size: 14px;
            padding: 8px 0;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .tips li::before {
            content: '✓';
            color: #10b981;
        }
        .logo {
            margin-top: 32px;
            opacity: 0.3;
            font-size: 14px;
            color: #9ca3af;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="icon">📡</div>
        <h1>Sin conexión a internet</h1>
        <p>Parece que no tienes conexión. Verifica tu WiFi o datos móviles e intenta de nuevo.</p>

        <a href="/" class="btn" onclick="window.location.reload(); return false;">
            Intentar de nuevo
        </a>

        <div class="tips">
            <h3>Mientras tanto puedes:</h3>
            <ul>
                <li>Revisar tu conexión WiFi</li>
                <li>Activar tus datos móviles</li>
                <li>Acercarte al router</li>
                <li>Reiniciar tu conexión</li>
            </ul>
        </div>

        <div class="logo">
            🌮 FAMER - Restaurantes Mexicanos Famosos
        </div>
    </div>

    <script>
        // Auto-reload when connection is restored
        window.addEventListener('online', () => {
            window.location.reload();
        });
    </script>
</body>
</html>
