<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Código de Verificación</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%); padding: 30px; text-align: center; border-radius: 10px 10px 0 0;">
        <img src="https://restaurantesmexicanosfamosos.com/images/branding/icon.png" alt="Restaurantes Mexicanos Famosos" style="width: 70px; height: 70px; border-radius: 50%; border: 3px solid rgba(255,255,255,0.3); margin-bottom: 12px;" />
        <h1 style="color: white; margin: 0; font-size: 24px;">🌮 Restaurantes Mexicanos</h1>
        <p style="color: rgba(255,255,255,0.9); margin: 10px 0 0 0;">Verificación de Propietario</p>
    </div>
    
    <div style="background: #f9fafb; padding: 30px; border: 1px solid #e5e7eb; border-top: none;">
        <p style="margin-top: 0;">Hola <strong>{{ $ownerName }}</strong>,</p>
        
        <p>Estás reclamando el restaurante <strong>{{ $restaurantName }}</strong> en nuestra plataforma.</p>
        
        <p>Tu código de verificación es:</p>
        
        <div style="background: #ffffff; border: 2px dashed #dc2626; border-radius: 10px; padding: 20px; text-align: center; margin: 20px 0;">
            <span style="font-size: 36px; font-weight: bold; letter-spacing: 8px; color: #dc2626;">{{ $code }}</span>
        </div>
        
        <p style="color: #6b7280; font-size: 14px;">
            <strong>⏱️ Este código expira en 15 minutos.</strong>
        </p>
        
        <p style="color: #6b7280; font-size: 14px;">
            Si no solicitaste este código, puedes ignorar este mensaje.
        </p>
    </div>
    
    <div style="background: #1f2937; padding: 20px; text-align: center; border-radius: 0 0 10px 10px;">
        <p style="color: #9ca3af; margin: 0; font-size: 12px;">
            © {{ date('Y') }} Restaurantes Mexicanos Famosos. Todos los derechos reservados.
        </p>
    </div>
</body>
</html>
