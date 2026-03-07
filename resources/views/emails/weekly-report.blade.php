<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte Semanal - {{ $restaurant->name }}</title>
</head>
<body style="margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f3f4f6;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f3f4f6; padding: 20px 0;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background-color: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
                    
                    <!-- Header -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #dc2626 0%, #ea580c 100%); padding: 30px; text-align: center;">
                            <img src="https://restaurantesmexicanosfamosos.com/images/branding/logo.png?v=3" alt="Restaurantes Mexicanos Famosos" style="max-height: 50px; width: auto; margin-bottom: 12px;" />
                            <h1 style="color: white; margin: 0; font-size: 24px;">📊 Reporte Semanal</h1>
                            <p style="color: rgba(255,255,255,0.9); margin: 10px 0 0 0; font-size: 14px;">{{ $weekRange }}</p>
                        </td>
                    </tr>
                    
                    <!-- Restaurant Name -->
                    <tr>
                        <td style="padding: 30px 30px 20px 30px; text-align: center; border-bottom: 1px solid #e5e7eb;">
                            <h2 style="margin: 0; color: #111827; font-size: 22px;">{{ $restaurant->name }}</h2>
                            <p style="margin: 5px 0 0 0; color: #6b7280; font-size: 14px;">{{ $restaurant->city }}, {{ $restaurant->state }}</p>
                        </td>
                    </tr>
                    
                    <!-- Stats Grid -->
                    <tr>
                        <td style="padding: 30px;">
                            <table width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <!-- Views This Week -->
                                    <td width="50%" style="padding: 10px;">
                                        <div style="background-color: #eff6ff; border-radius: 12px; padding: 20px; text-align: center;">
                                            <p style="margin: 0; color: #3b82f6; font-size: 36px; font-weight: bold;">{{ number_format($stats['this_week_views']) }}</p>
                                            <p style="margin: 5px 0 0 0; color: #1e40af; font-size: 12px; font-weight: 600;">VISTAS ESTA SEMANA</p>
                                            @if($stats['growth'] != 0)
                                            <p style="margin: 8px 0 0 0; font-size: 12px; color: {{ $stats['growth'] > 0 ? '#16a34a' : '#dc2626' }};">
                                                {{ $stats['growth'] > 0 ? '↑' : '↓' }} {{ abs($stats['growth']) }}% vs semana anterior
                                            </p>
                                            @endif
                                        </div>
                                    </td>
                                    <!-- Total Views -->
                                    <td width="50%" style="padding: 10px;">
                                        <div style="background-color: #f0fdf4; border-radius: 12px; padding: 20px; text-align: center;">
                                            <p style="margin: 0; color: #22c55e; font-size: 36px; font-weight: bold;">{{ number_format($stats['total_views']) }}</p>
                                            <p style="margin: 5px 0 0 0; color: #166534; font-size: 12px; font-weight: 600;">VISTAS TOTALES</p>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <!-- New Reviews -->
                                    <td width="50%" style="padding: 10px;">
                                        <div style="background-color: #fefce8; border-radius: 12px; padding: 20px; text-align: center;">
                                            <p style="margin: 0; color: #eab308; font-size: 36px; font-weight: bold;">{{ $stats['new_reviews'] }}</p>
                                            <p style="margin: 5px 0 0 0; color: #a16207; font-size: 12px; font-weight: 600;">NUEVAS RESENAS</p>
                                        </div>
                                    </td>
                                    <!-- Rating -->
                                    <td width="50%" style="padding: 10px;">
                                        <div style="background-color: #fdf4ff; border-radius: 12px; padding: 20px; text-align: center;">
                                            <p style="margin: 0; color: #a855f7; font-size: 36px; font-weight: bold;">{{ $stats['avg_rating'] }} ⭐</p>
                                            <p style="margin: 5px 0 0 0; color: #7e22ce; font-size: 12px; font-weight: 600;">CALIFICACION</p>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    
                    <!-- Pending Actions -->
                    @if($stats['pending_responses'] > 0)
                    <tr>
                        <td style="padding: 0 30px 30px 30px;">
                            <div style="background-color: #fef2f2; border: 1px solid #fecaca; border-radius: 12px; padding: 20px;">
                                <p style="margin: 0; color: #dc2626; font-weight: 600;">⚠️ Tienes {{ $stats['pending_responses'] }} resena(s) sin responder</p>
                                <p style="margin: 10px 0 0 0; color: #7f1d1d; font-size: 14px;">Responder a las resenas mejora tu reputacion y visibilidad.</p>
                            </div>
                        </td>
                    </tr>
                    @endif
                    
                    <!-- CTA Button -->
                    <tr>
                        <td style="padding: 0 30px 30px 30px; text-align: center;">
                            <a href="{{ url('/owner') }}" style="display: inline-block; background: linear-gradient(135deg, #dc2626 0%, #ea580c 100%); color: white; text-decoration: none; padding: 15px 40px; border-radius: 8px; font-weight: bold; font-size: 16px;">
                                Ver Panel Completo →
                            </a>
                        </td>
                    </tr>
                    
                    <!-- Tips Section -->
                    <tr>
                        <td style="background-color: #f9fafb; padding: 30px; border-top: 1px solid #e5e7eb;">
                            <h3 style="margin: 0 0 15px 0; color: #111827; font-size: 16px;">💡 Tips de la Semana</h3>
                            <ul style="margin: 0; padding: 0 0 0 20px; color: #4b5563; font-size: 14px; line-height: 1.8;">
                                <li>Agrega fotos de tus platillos mas populares para atraer mas clientes</li>
                                <li>Responde a todas las resenas, incluso las negativas, de forma profesional</li>
                                <li>Manten tu menu actualizado con precios correctos</li>
                            </ul>
                        </td>
                    </tr>
                    
                    <!-- Benefits Reminder -->
                    <tr>
                        <td style="padding: 30px; border-top: 1px solid #e5e7eb;">
                            <p style="margin: 0 0 15px 0; color: #111827; font-weight: 600; text-align: center;">🎁 Recuerda tus beneficios exclusivos FAMER</p>
                            <p style="margin: 0; color: #6b7280; font-size: 13px; text-align: center;">
                                Descuentos en: MF Imports • Tormex Pro • MF Trailers • Refrimex • y mas...
                            </p>
                            <p style="margin: 15px 0 0 0; text-align: center;">
                                <a href="{{ url('/dashboard') }}" style="color: #dc2626; text-decoration: none; font-size: 14px; font-weight: 600;">Ver todos mis beneficios →</a>
                            </p>
                        </td>
                    </tr>
                    
                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #1f2937; padding: 25px; text-align: center;">
                            <p style="margin: 0; color: #9ca3af; font-size: 12px;">
                                Restaurantes Mexicanos Famosos (FAMER)<br>
                                Este reporte se envia automaticamente cada lunes.
                            </p>
                            <p style="margin: 15px 0 0 0;">
                                <a href="{{ url('/owner/my-subscription') }}" style="color: #60a5fa; text-decoration: none; font-size: 12px;">Administrar preferencias de email</a>
                            </p>
                        </td>
                    </tr>
                    
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
