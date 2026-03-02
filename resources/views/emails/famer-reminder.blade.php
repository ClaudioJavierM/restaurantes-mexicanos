<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ultima Oportunidad - FAMER Awards</title>
</head>
<body style="margin: 0; padding: 0; font-family: Georgia, Times, serif; background-color: #000000;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #000000; padding: 20px 0;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.3);">
                    <!-- Header con urgencia -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #8B0000 0%, #DC143C 50%, #FF4500 100%); padding: 30px; text-align: center;">
                            <div style="font-size: 14px; color: #FFD700; letter-spacing: 3px; margin-bottom: 10px; font-weight: bold;">⏰ ULTIMA OPORTUNIDAD</div>
                            <h1 style="color: #ffffff; margin: 0; font-size: 28px; font-weight: bold; text-shadow: 2px 2px 4px rgba(0,0,0,0.3);">FAMER Awards 2026</h1>
                            <p style="color: #FFD700; margin: 10px 0 0 0; font-size: 14px;">Famous Mexican Restaurants</p>
                        </td>
                    </tr>

                    <!-- Mensaje urgente -->
                    <tr>
                        <td style="padding: 30px;">
                            <div style="background-color: #FFF3CD; border-left: 4px solid #FFD700; padding: 15px; margin-bottom: 25px;">
                                <p style="color: #856404; margin: 0; font-size: 16px;">
                                    <strong>⚠️ Aun no has reclamado tu restaurante.</strong><br>
                                    Esta es tu ultima oportunidad para participar en FAMER Awards 2026.
                                </p>
                            </div>

                            <h2 style="color: #000000; font-size: 22px; margin: 0 0 20px 0;">
                                {{ $restaurant->name }}
                            </h2>
                            
                            <p style="color: #000000; font-size: 16px; line-height: 1.8; margin: 0 0 20px 0;">
                                Tu restaurante ya tiene un perfil en nuestra plataforma y esta siendo visto por 
                                miles de personas que buscan los mejores restaurantes mexicanos.
                            </p>

                            <p style="color: #000000; font-size: 16px; line-height: 1.8; margin: 0 0 25px 0;">
                                <strong>Sin embargo, sin reclamar tu perfil:</strong>
                            </p>

                            <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom: 25px;">
                                <tr>
                                    <td style="padding: 12px; background-color: #FFEBEE; border-radius: 8px; margin-bottom: 10px;">
                                        <span style="color: #C62828; font-size: 18px;">✗</span>
                                        <span style="color: #000000; font-size: 15px; margin-left: 10px;">No puedes actualizar tu informacion</span>
                                    </td>
                                </tr>
                                <tr><td style="height: 10px;"></td></tr>
                                <tr>
                                    <td style="padding: 12px; background-color: #FFEBEE; border-radius: 8px;">
                                        <span style="color: #C62828; font-size: 18px;">✗</span>
                                        <span style="color: #000000; font-size: 15px; margin-left: 10px;">No participas en el ranking FAMER Awards</span>
                                    </td>
                                </tr>
                                <tr><td style="height: 10px;"></td></tr>
                                <tr>
                                    <td style="padding: 12px; background-color: #FFEBEE; border-radius: 8px;">
                                        <span style="color: #C62828; font-size: 18px;">✗</span>
                                        <span style="color: #000000; font-size: 15px; margin-left: 10px;">Pierdes clientes potenciales cada dia</span>
                                    </td>
                                </tr>
                                <tr><td style="height: 10px;"></td></tr>
                                <tr>
                                    <td style="padding: 12px; background-color: #FFEBEE; border-radius: 8px;">
                                        <span style="color: #C62828; font-size: 18px;">✗</span>
                                        <span style="color: #000000; font-size: 15px; margin-left: 10px;">No recibes el reconocimiento que mereces</span>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- CTA Principal -->
                    <tr>
                        <td style="padding: 0 30px 30px 30px; text-align: center;">
                            <div style="background: linear-gradient(135deg, #006847 0%, #004d35 100%); padding: 25px; border-radius: 12px;">
                                <p style="color: #ffffff; font-size: 18px; margin: 0 0 20px 0; font-weight: bold;">
                                    🎯 Reclama tu restaurante AHORA
                                </p>
                                <p style="color: #90EE90; font-size: 14px; margin: 0 0 20px 0;">
                                    Es GRATIS y solo toma 2 minutos
                                </p>
                                <a href="{{ $claimUrl }}" 
                                   style="display: inline-block; background: linear-gradient(135deg, #FFD700 0%, #FFA500 100%); color: #000000; padding: 18px 50px; text-decoration: none; border-radius: 30px; font-weight: bold; font-size: 18px; text-transform: uppercase; letter-spacing: 1px; box-shadow: 0 4px 15px rgba(255,215,0,0.4);">
                                    RECLAMAR MI RESTAURANTE
                                </a>
                            </div>
                        </td>
                    </tr>

                    <!-- Lo que obtienen -->
                    <tr>
                        <td style="padding: 0 30px 30px 30px;">
                            <h3 style="color: #000000; font-size: 18px; margin: 0 0 15px 0; text-align: center;">
                                Al reclamar tu perfil obtendras:
                            </h3>
                            <table width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td style="padding: 10px; background-color: #E8F5E9; border-radius: 8px;">
                                        <span style="color: #2E7D32; font-size: 18px;">✓</span>
                                        <span style="color: #000000; font-size: 15px; margin-left: 10px;">Participacion automatica en FAMER Awards 2026</span>
                                    </td>
                                </tr>
                                <tr><td style="height: 8px;"></td></tr>
                                <tr>
                                    <td style="padding: 10px; background-color: #E8F5E9; border-radius: 8px;">
                                        <span style="color: #2E7D32; font-size: 18px;">✓</span>
                                        <span style="color: #000000; font-size: 15px; margin-left: 10px;">Panel de control con estadisticas</span>
                                    </td>
                                </tr>
                                <tr><td style="height: 8px;"></td></tr>
                                <tr>
                                    <td style="padding: 10px; background-color: #E8F5E9; border-radius: 8px;">
                                        <span style="color: #2E7D32; font-size: 18px;">✓</span>
                                        <span style="color: #000000; font-size: 15px; margin-left: 10px;">Fotos ilimitadas y menu digital</span>
                                    </td>
                                </tr>
                                <tr><td style="height: 8px;"></td></tr>
                                <tr>
                                    <td style="padding: 10px; background-color: #E8F5E9; border-radius: 8px;">
                                        <span style="color: #2E7D32; font-size: 18px;">✓</span>
                                        <span style="color: #000000; font-size: 15px; margin-left: 10px;">Distintivo de restaurante verificado</span>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Footer con urgencia -->
                    <tr>
                        <td style="background-color: #000000; padding: 25px; text-align: center;">
                            <p style="color: #FFD700; font-size: 14px; margin: 0 0 15px 0; font-weight: bold;">
                                No dejes pasar esta oportunidad
                            </p>
                            <p style="color: #888; font-size: 12px; margin: 0 0 10px 0;">
                                Famous Mexican Restaurants - FAMER Awards 2026
                            </p>
                            <p style="color: #666; font-size: 11px; margin: 0;">
                                <a href="https://famousmexicanrestaurants.com/unsubscribe?email={{ urlencode($restaurant->email ?? $restaurant->owner_email) }}" style="color: #666;">Cancelar suscripcion</a>
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
