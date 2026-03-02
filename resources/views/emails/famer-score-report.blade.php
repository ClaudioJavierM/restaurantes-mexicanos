<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Tu FAMER Score</title>
    <!--[if mso]>
    <noscript>
        <xml>
            <o:OfficeDocumentSettings>
                <o:PixelsPerInch>96</o:PixelsPerInch>
            </o:OfficeDocumentSettings>
        </xml>
    </noscript>
    <![endif]-->
    <style>
        /* Reset */
        body, table, td, p, a, li, blockquote {
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
        }
        table, td {
            mso-table-lspace: 0pt;
            mso-table-rspace: 0pt;
        }
        img {
            -ms-interpolation-mode: bicubic;
        }
        body {
            margin: 0;
            padding: 0;
            width: 100% !important;
            height: 100% !important;
        }
        img {
            border: 0;
            height: auto;
            line-height: 100%;
            outline: none;
            text-decoration: none;
        }
        table {
            border-collapse: collapse !important;
        }
        a {
            color: #10B981;
            text-decoration: none;
        }
        /* Mobile styles */
        @media screen and (max-width: 600px) {
            .container {
                width: 100% !important;
                padding: 10px !important;
            }
            .content-block {
                padding: 20px !important;
            }
            .score-circle {
                width: 120px !important;
                height: 120px !important;
            }
            .score-number {
                font-size: 36px !important;
            }
            .category-label {
                font-size: 12px !important;
            }
        }
    </style>
</head>
<body style="background-color: #f3f4f6; margin: 0; padding: 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;">

    <!-- Preview Text -->
    <div style="display: none; max-height: 0; overflow: hidden;">
        Tu restaurante {{ $restaurant['name'] ?? '' }} obtuvo un score de {{ $scoreData['overall_score'] ?? 0 }} ({{ $scoreData['letter_grade'] ?? 'N/A' }}). Ve el reporte completo con recomendaciones para mejorar.
    </div>

    <!-- Main Container -->
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color: #f3f4f6;">
        <tr>
            <td align="center" style="padding: 40px 20px;">

                <!-- Email Container -->
                <table role="presentation" class="container" width="600" cellspacing="0" cellpadding="0" border="0" style="background-color: #ffffff; border-radius: 12px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">

                    <!-- Header -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #10B981 0%, #059669 100%); border-radius: 12px 12px 0 0; padding: 30px; text-align: center;">
                            <img src="{{ asset('images/logo-white.png') }}" alt="FAMER" width="120" style="max-width: 120px; height: auto;">
                            <p style="color: rgba(255,255,255,0.9); font-size: 14px; margin: 10px 0 0 0;">
                                Famous Mexican Restaurants
                            </p>
                        </td>
                    </tr>

                    <!-- Greeting -->
                    <tr>
                        <td class="content-block" style="padding: 30px 40px 20px 40px;">
                            <p style="color: #374151; font-size: 16px; line-height: 1.6; margin: 0;">
                                Hola {{ $recipientName }},
                            </p>
                            <p style="color: #6B7280; font-size: 15px; line-height: 1.6; margin: 15px 0 0 0;">
                                Aquí está el reporte completo del FAMER Score para:
                            </p>
                            <h2 style="color: #111827; font-size: 22px; font-weight: 700; margin: 10px 0;">
                                {{ $restaurant['name'] ?? 'Tu Restaurante' }}
                            </h2>
                            <p style="color: #6B7280; font-size: 14px; margin: 0;">
                                📍 {{ $restaurant['city'] ?? '' }}{{ isset($restaurant['state']) ? ', ' . $restaurant['state'] : '' }}
                            </p>
                        </td>
                    </tr>

                    <!-- Score Display -->
                    <tr>
                        <td style="padding: 20px 40px; text-align: center;">
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
                                <tr>
                                    <td align="center">
                                        <!-- Score Circle -->
                                        <div class="score-circle" style="width: 160px; height: 160px; border-radius: 50%; background: {{ $this->getGradeBgColorHex() }}; border: 8px solid {{ $this->getGradeColorHex() }}; display: inline-block; text-align: center; line-height: 144px;">
                                            <span class="score-number" style="font-size: 48px; font-weight: 800; color: {{ $this->getGradeColorHex() }};">
                                                {{ $scoreData['overall_score'] ?? 0 }}
                                            </span>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="center" style="padding-top: 15px;">
                                        <span style="display: inline-block; background: {{ $this->getGradeBgColorHex() }}; color: {{ $this->getGradeColorHex() }}; font-size: 24px; font-weight: 700; padding: 8px 24px; border-radius: 20px;">
                                            {{ $scoreData['letter_grade'] ?? 'N/A' }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="center" style="padding-top: 10px;">
                                        <p style="color: #6B7280; font-size: 14px; margin: 0;">
                                            {{ $scoreData['score_description'] ?? '' }}
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    @if(!empty($scoreData['percentile']) && !empty($scoreData['area_rank']))
                    <!-- Comparison Stats -->
                    <tr>
                        <td style="padding: 10px 40px 30px 40px;">
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color: #F0FDF4; border-radius: 8px; padding: 20px;">
                                <tr>
                                    <td style="padding: 20px; text-align: center;">
                                        <p style="color: #059669; font-size: 14px; font-weight: 600; margin: 0;">
                                            🏆 Estás en el <strong style="font-size: 18px;">top {{ $scoreData['percentile'] ?? 0 }}%</strong> de restaurantes mexicanos en tu área
                                        </p>
                                        <p style="color: #6B7280; font-size: 13px; margin: 8px 0 0 0;">
                                            Posición #{{ $scoreData['area_rank'] }} de {{ $scoreData['area_total'] }} restaurantes
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    @endif

                    @if(!empty($scoreData['categories']))
                    <!-- Category Breakdown -->
                    <tr>
                        <td class="content-block" style="padding: 20px 40px;">
                            <h3 style="color: #111827; font-size: 18px; font-weight: 700; margin: 0 0 20px 0; border-bottom: 2px solid #E5E7EB; padding-bottom: 10px;">
                                📊 Desglose por Categoría
                            </h3>

                            @foreach($scoreData['categories'] as $category)
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="margin-bottom: 15px;">
                                <tr>
                                    <td style="padding-bottom: 5px;">
                                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
                                            <tr>
                                                <td>
                                                    <span class="category-label" style="color: #374151; font-size: 14px; font-weight: 500;">
                                                        {{ $category['name'] }}
                                                    </span>
                                                    <span style="color: #9CA3AF; font-size: 12px;">
                                                        ({{ $category['weight'] }}%)
                                                    </span>
                                                </td>
                                                <td align="right">
                                                    <span style="color: #111827; font-size: 14px; font-weight: 700;">
                                                        {{ $category['score'] }}/100
                                                    </span>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div style="background-color: #E5E7EB; border-radius: 4px; height: 8px; overflow: hidden;">
                                            <div style="background-color: {{ $category['score'] >= 70 ? '#10B981' : ($category['score'] >= 50 ? '#F59E0B' : '#EF4444') }}; width: {{ $category['score'] }}%; height: 8px; border-radius: 4px;"></div>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                            @endforeach
                        </td>
                    </tr>
                    @endif

                    <!-- Recommendations Section -->
                    <tr>
                        <td class="content-block" style="padding: 20px 40px 30px 40px;">
                            <h3 style="color: #111827; font-size: 18px; font-weight: 700; margin: 0 0 20px 0; border-bottom: 2px solid #E5E7EB; padding-bottom: 10px;">
                                💡 Recomendaciones para Mejorar
                            </h3>

                            @php
                                $recommendations = $scoreData['all_recommendations'] ?? $scoreData['top_recommendations'] ?? [];
                                if (empty($recommendations)) {
                                    $recommendations = $scoreData['top_recommendations'] ?? [];
                                }
                            @endphp

                            @forelse($recommendations as $index => $rec)
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color: #F9FAFB; border-radius: 8px; margin-bottom: 12px; border-left: 4px solid {{ $this->getPriorityColor($rec['priority'] ?? 'medium') }};">
                                <tr>
                                    <td style="padding: 16px;">
                                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
                                            <tr>
                                                <td>
                                                    <span style="display: inline-block; background: {{ $this->getPriorityBgColor($rec['priority'] ?? 'medium') }}; color: {{ $this->getPriorityColor($rec['priority'] ?? 'medium') }}; font-size: 10px; font-weight: 600; text-transform: uppercase; padding: 3px 8px; border-radius: 4px; margin-right: 8px;">
                                                        {{ $rec['priority'] ?? 'medium' }}
                                                    </span>
                                                    <span style="color: #9CA3AF; font-size: 11px;">
                                                        {{ $this->getCategoryIcon($rec['category'] ?? 'general') }} {{ ucfirst($rec['category'] ?? 'General') }}
                                                    </span>
                                                </td>
                                                @if(!empty($rec['impact']))
                                                <td align="right">
                                                    <span style="color: #10B981; font-size: 12px; font-weight: 600;">
                                                        {{ $rec['impact'] }}
                                                    </span>
                                                </td>
                                                @endif
                                            </tr>
                                            <tr>
                                                <td colspan="2" style="padding-top: 8px;">
                                                    <p style="color: #111827; font-size: 15px; font-weight: 600; margin: 0;">
                                                        {{ $rec['title'] }}
                                                    </p>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="2" style="padding-top: 5px;">
                                                    <p style="color: #6B7280; font-size: 13px; line-height: 1.5; margin: 0;">
                                                        {{ $rec['description'] }}
                                                    </p>
                                                </td>
                                            </tr>
                                            @if(!empty($rec['action_url']) && !empty($rec['action_label']))
                                            <tr>
                                                <td colspan="2" style="padding-top: 10px;">
                                                    <a href="{{ url($rec['action_url']) }}" style="display: inline-block; background-color: #10B981; color: #ffffff; font-size: 12px; font-weight: 600; padding: 8px 16px; border-radius: 6px; text-decoration: none;">
                                                        {{ $rec['action_label'] }} →
                                                    </a>
                                                </td>
                                            </tr>
                                            @endif
                                        </table>
                                    </td>
                                </tr>
                            </table>
                            @empty
                            <p style="color: #6B7280; font-size: 14px; text-align: center; padding: 20px;">
                                ¡Excelente! Tu restaurante está bien optimizado.
                            </p>
                            @endforelse
                        </td>
                    </tr>

                    <!-- CTA Section -->
                    @if(empty($restaurant['is_claimed']))
                    <tr>
                        <td style="padding: 20px 40px 30px 40px;">
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background: linear-gradient(135deg, #065F46 0%, #047857 100%); border-radius: 12px;">
                                <tr>
                                    <td style="padding: 30px; text-align: center;">
                                        <h3 style="color: #ffffff; font-size: 20px; font-weight: 700; margin: 0 0 10px 0;">
                                            🚀 ¿Listo para mejorar tu Score?
                                        </h3>
                                        <p style="color: rgba(255,255,255,0.9); font-size: 14px; line-height: 1.5; margin: 0 0 20px 0;">
                                            Reclama tu restaurante en FAMER para acceder a herramientas de marketing, gestionar tu perfil y atraer más clientes.
                                        </p>
                                        <a href="{{ $claimUrl }}" style="display: inline-block; background-color: #ffffff; color: #047857; font-size: 16px; font-weight: 700; padding: 14px 32px; border-radius: 8px; text-decoration: none;">
                                            Reclamar Mi Restaurante Gratis
                                        </a>
                                        <p style="color: rgba(255,255,255,0.7); font-size: 12px; margin: 15px 0 0 0;">
                                            ✓ Sin costo  ✓ Sin compromisos  ✓ Resultado en minutos
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    @endif

                    <!-- What's Next Section -->
                    <tr>
                        <td class="content-block" style="padding: 20px 40px 30px 40px;">
                            <h3 style="color: #111827; font-size: 16px; font-weight: 700; margin: 0 0 15px 0;">
                                📋 Próximos Pasos
                            </h3>
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
                                <tr>
                                    <td style="padding: 8px 0;">
                                        <span style="color: #10B981; font-weight: 700;">1.</span>
                                        <span style="color: #374151; font-size: 14px; margin-left: 8px;">
                                            Revisa las recomendaciones críticas primero
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 8px 0;">
                                        <span style="color: #10B981; font-weight: 700;">2.</span>
                                        <span style="color: #374151; font-size: 14px; margin-left: 8px;">
                                            @if(empty($restaurant['is_claimed']))
                                            <a href="{{ $claimUrl }}" style="color: #10B981; text-decoration: underline;">Reclama tu restaurante</a> para desbloquear todas las funciones
                                            @else
                                            Actualiza tu perfil con fotos y menú
                                            @endif
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 8px 0;">
                                        <span style="color: #10B981; font-weight: 700;">3.</span>
                                        <span style="color: #374151; font-size: 14px; margin-left: 8px;">
                                            Vuelve en 30 días para ver cómo mejora tu score
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Help Section -->
                    <tr>
                        <td style="padding: 20px 40px;">
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color: #F3F4F6; border-radius: 8px;">
                                <tr>
                                    <td style="padding: 20px; text-align: center;">
                                        <p style="color: #374151; font-size: 14px; margin: 0 0 10px 0;">
                                            ¿Tienes preguntas sobre tu score?
                                        </p>
                                        <a href="mailto:support@restaurantesfamosos.com" style="color: #10B981; font-size: 14px; font-weight: 600; text-decoration: none;">
                                            Escríbenos: support@restaurantesfamosos.com
                                        </a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #F9FAFB; border-radius: 0 0 12px 12px; padding: 30px 40px; text-align: center;">
                            <p style="color: #9CA3AF; font-size: 12px; margin: 0 0 10px 0;">
                                Este reporte fue generado el {{ now()->format('d/m/Y') }} por FAMER - Famous Mexican Restaurants
                            </p>
                            <p style="color: #9CA3AF; font-size: 12px; margin: 0 0 10px 0;">
                                El directorio #1 de restaurantes mexicanos auténticos en USA
                            </p>
                            <p style="color: #9CA3AF; font-size: 12px; margin: 0;">
                                <a href="{{ url('/') }}" style="color: #10B981; text-decoration: none;">Visitar FAMER</a>
                                &nbsp;•&nbsp;
                                <a href="{{ url('/for-owners') }}" style="color: #10B981; text-decoration: none;">Para Dueños</a>
                                &nbsp;•&nbsp;
                                <a href="{{ $unsubscribeUrl }}" style="color: #9CA3AF; text-decoration: underline;">Cancelar suscripción</a>
                            </p>
                            <p style="color: #D1D5DB; font-size: 11px; margin: 15px 0 0 0;">
                                © {{ date('Y') }} Famous Mexican Restaurants. Todos los derechos reservados.
                            </p>
                        </td>
                    </tr>

                </table>

            </td>
        </tr>
    </table>

    <!-- Tracking pixel -->
    <img src="{{ url('/api/email/track/' . $request->id) }}" width="1" height="1" alt="" style="display: block; width: 1px; height: 1px;">

</body>
</html>
