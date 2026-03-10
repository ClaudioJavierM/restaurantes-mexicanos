<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FAMER - Tu restaurante ya esta en la lista</title>
</head>
<body style="margin: 0; padding: 0; font-family: Arial, Helvetica, sans-serif; background-color: #f5f5f5;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f5f5f5; padding: 40px 20px;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                    
                    <!-- Header con bandera mexicana -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #006847 0%, #1a1a1a 50%, #CE1126 100%); padding: 40px 30px; text-align: center;">
                            <img src="https://restaurantesmexicanosfamosos.com/images/branding/logo.png?v=3" alt="Restaurantes Mexicanos Famosos" style="max-height: 50px; width: auto; margin-bottom: 12px;" />
                            <h1 style="color: #FFD700; margin: 0; font-size: 32px; font-weight: bold; text-shadow: 2px 2px 4px rgba(0,0,0,0.3);">FAMER</h1>
                            <p style="color: #ffffff; margin: 10px 0 0 0; font-size: 14px; letter-spacing: 2px;">RESTAURANTES MEXICANOS FAMOSOS</p>
                        </td>
                    </tr>
                    
                    <!-- Contenido principal -->
                    <tr>
                        <td style="padding: 40px 30px;">
                            <h2 style="color: #000000; font-size: 24px; margin: 0 0 20px 0;">Hola {{ $restaurantName }},</h2>
                            
                            <p style="color: #000000; font-size: 16px; line-height: 1.8; margin: 0 0 20px 0;">
                                Queremos compartirte algo importante.
                            </p>
                            
                            <p style="color: #000000; font-size: 16px; line-height: 1.8; margin: 0 0 20px 0;">
                                <strong>Tu restaurante ya forma parte de la lista de FAMER</strong> – Restaurantes Mexicanos Famosos, una iniciativa creada para reconocer, promover y celebrar la gastronomia mexicana en Estados Unidos.
                            </p>
                            
                            <p style="color: #000000; font-size: 16px; line-height: 1.8; margin: 0 0 30px 0;">
                                Durante anos, miles de restaurantes han representado a Mexico con orgullo, tradicion y sabor... pero nunca habian tenido una plataforma creada exclusivamente para ellos.
                                <br><br>
                                <strong style="color: #006847;">Eso es lo que hoy nace con FAMER.</strong>
                            </p>
                            
                            <!-- Seccion Que es FAMER -->
                            <div style="background-color: #ffffff; border-left: 4px solid #006847; padding: 20px; margin: 0 0 30px 0; border-radius: 0 8px 8px 0;">
                                <h3 style="color: #006847; margin: 0 0 15px 0; font-size: 18px;">🌮 Que es FAMER?</h3>
                                <p style="color: #000000; font-size: 15px; line-height: 1.7; margin: 0;">
                                    FAMER (Famous Mexican Restaurant) es una plataforma especializada en:
                                </p>
                                <ul style="color: #000000; font-size: 15px; line-height: 1.8; margin: 15px 0 0 0; padding-left: 20px;">
                                    <li>Promover restaurantes mexicanos por ciudad, estado, region y especialidad</li>
                                    <li>Mostrar la diversidad real de la gastronomia mexicana</li>
                                    <li>Dar visibilidad y reconocimiento a los restaurantes que representan autenticamente a Mexico</li>
                                </ul>
                            </div>
                            
                            <!-- Seccion FAMER Awards -->
                            <div style="background: linear-gradient(135deg, #FFD700 0%, #FFA500 100%); padding: 25px; margin: 0 0 30px 0; border-radius: 8px;">
                                <h3 style="color: #000000; margin: 0 0 15px 0; font-size: 18px;">🏆 FAMER Awards 2026</h3>
                                <p style="color: #000000; font-size: 15px; line-height: 1.7; margin: 0 0 15px 0;">
                                    En 2026 se celebrara la primera edicion de los FAMER Awards, donde se reconocera a:
                                </p>
                                <ul style="color: #000000; font-size: 15px; line-height: 1.8; margin: 0; padding-left: 20px;">
                                    <li><strong>Los Top 10 Restaurantes Mexicanos por Ciudad</strong></li>
                                    <li><strong>Los Top 10 por Estado</strong></li>
                                    <li><strong>Los 100 Restaurantes Mexicanos que debes visitar en USA</strong></li>
                                </ul>
                                <p style="color: #CE1126; font-size: 14px; font-weight: bold; margin: 15px 0 0 0;">
                                    👉 Solo los restaurantes que reclamen su perfil podran participar en estos rankings.
                                </p>
                            </div>
                            
                            <!-- Seccion Beneficios -->
                            <div style="background-color: #ffffff; border: 1px solid #006847; padding: 20px; margin: 0 0 30px 0; border-radius: 8px;">
                                <h3 style="color: #006847; margin: 0 0 15px 0; font-size: 18px;">🎁 Reclama tu restaurante (sin costo en esta etapa)</h3>
                                <p style="color: #000000; font-size: 15px; line-height: 1.7; margin: 0 0 15px 0;">
                                    Durante estos primeros meses puedes:
                                </p>
                                <ul style="color: #000000; font-size: 15px; line-height: 1.8; margin: 0; padding-left: 20px;">
                                    <li>Reclamar tu restaurante</li>
                                    <li>Activar tu perfil oficial en FAMER</li>
                                    <li>Aparecer en busquedas por ciudad, estado y tipo de platillo</li>
                                    <li>Invitar a tus clientes a votar por tu restaurante</li>
                                    <li>Tener la oportunidad de estar en el Top 10 de tu ciudad y estado</li>
                                </ul>
                            </div>
                            
                            <!-- Boton CTA -->
                            <div style="text-align: center; margin: 40px 0;">
                                <a href="{{ $claimUrl }}" style="display: inline-block; background: linear-gradient(135deg, #CE1126 0%, #a00d1e 100%); color: #ffffff; text-decoration: none; padding: 18px 40px; font-size: 18px; font-weight: bold; border-radius: 8px; box-shadow: 0 4px 15px rgba(206, 17, 38, 0.4);">
                                    RECLAMAR MI RESTAURANTE
                                </a>
                            </div>
                            
                            <!-- Firma -->
                            <p style="color: #000000; font-size: 16px; line-height: 1.6; margin: 30px 0 0 0;">
                                Con orgullo mexicano,<br>
                                <strong style="color: #006847;">Equipo FAMER</strong><br>
                                <span style="color: #555555; font-size: 14px;">Restaurantes Mexicanos Famosos</span>
                            </p>
                        </td>
                    </tr>
                    
                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #000000; padding: 25px 30px; text-align: center;">
                            <p style="color: #555555; font-size: 12px; line-height: 1.6; margin: 0 0 10px 0;">
                                Este perfil fue creado a partir de informacion publica disponible.<br>
                                Al reclamarlo, puedes administrar y actualizar la informacion de tu restaurante directamente.
                            </p>
                            <p style="color: #FFD700; font-size: 12px; margin: 15px 0 0 0;">
                                <strong>P.D.</strong> Los rankings del FAMER Awards 2026 ya se estan formando.<br>
                                Reclamar tu restaurante es el primer paso para ser considerado.
                            </p>
                            <p style="color: #444444; font-size: 11px; margin: 20px 0 0 0;">
                                <a href="https://famousmexicanrestaurants.com/unsubscribe?email={{ urlencode($restaurant->email ?? $restaurant->owner_email) }}" style="color: #555555;">Cancelar suscripcion</a>
                            </p>
                        </td>
                    </tr>
                    
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
