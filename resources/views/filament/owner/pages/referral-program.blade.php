<x-filament-panels::page>
    @if(!$restaurant)
    <div style="text-align: center; padding: 3rem;">
        <p style="color: #9ca3af;">No tienes un restaurante asociado.</p>
    </div>
    @else
    <div style="display: flex; flex-direction: column; gap: 1.5rem;">

        {{-- Hero --}}
        <div style="background: linear-gradient(135deg, #7c3aed, #4f46e5); border-radius: 1rem; padding: 2rem; color: white; text-align: center;">
            <p style="font-size: 3rem; margin: 0 0 0.5rem;">🎁</p>
            <h2 style="font-size: 1.75rem; font-weight: bold; margin: 0 0 0.5rem;">Refiere y Gana</h2>
            <p style="opacity: 0.9; margin: 0 0 1.5rem; font-size: 1rem;">Invita a otros restaurantes a FAMER y gana 1 mes gratis cuando se suscriban</p>
            <div style="background: rgba(255,255,255,0.15); border-radius: 0.5rem; padding: 0.25rem; display: inline-flex; align-items: center; gap: 0.5rem; max-width: 100%;">
                <span style="font-size: 0.75rem; color: rgba(255,255,255,0.8); padding-left: 0.5rem; white-space: nowrap;">Tu codigo:</span>
                <code style="font-size: 1.25rem; font-weight: bold; letter-spacing: 0.1em; padding: 0.25rem 0.5rem;">{{ $restaurant->referral_code }}</code>
            </div>
        </div>

        {{-- Referral Link --}}
        <div style="background-color: #1f2937; border-radius: 0.75rem; padding: 1.5rem; border: 1px solid #374151;">
            <h3 style="font-size: 1rem; font-weight: 600; color: #ffffff; margin: 0 0 1rem;">Tu Enlace de Referido</h3>
            <div style="display: flex; gap: 0.75rem; flex-wrap: wrap; align-items: center;">
                <div style="flex: 1; min-width: 200px; background-color: #111827; border: 1px solid #374151; border-radius: 0.5rem; padding: 0.75rem 1rem;">
                    <code style="color: #818cf8; font-size: 0.8rem; word-break: break-all;">{{ $referralUrl }}</code>
                </div>
                <button onclick="navigator.clipboard.writeText('{{ $referralUrl }}').then(() => { this.textContent = '✓ Copiado!'; setTimeout(() => this.textContent = 'Copiar Enlace', 2000); })"
                    style="background: linear-gradient(135deg, #7c3aed, #4f46e5); color: white; padding: 0.75rem 1.5rem; border-radius: 0.5rem; border: none; font-size: 0.875rem; font-weight: 600; cursor: pointer; white-space: nowrap;">
                    Copiar Enlace
                </button>
            </div>

            {{-- Share buttons --}}
            <div style="margin-top: 1rem; display: flex; gap: 0.75rem; flex-wrap: wrap;">
                <a href="https://wa.me/?text={{ urlencode('¡Registra tu restaurante en FAMER, el directorio #1 de restaurantes mexicanos! Usa mi enlace: ' . $referralUrl) }}"
                    target="_blank"
                    style="display: inline-flex; align-items: center; gap: 0.5rem; background-color: #16a34a; color: white; padding: 0.5rem 1rem; border-radius: 0.5rem; text-decoration: none; font-size: 0.8rem; font-weight: 500;">
                    WhatsApp
                </a>
                <a href="mailto:?subject={{ urlencode('Te invito a FAMER') }}&body={{ urlencode('Registra tu restaurante en FAMER y obtén visibilidad en el directorio #1. Usa mi enlace: ' . $referralUrl) }}"
                    style="display: inline-flex; align-items: center; gap: 0.5rem; background-color: #1d4ed8; color: white; padding: 0.5rem 1rem; border-radius: 0.5rem; text-decoration: none; font-size: 0.8rem; font-weight: 500;">
                    Email
                </a>
            </div>
        </div>

        {{-- Stats --}}
        <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem;">
            @php
                $statsConfig = [
                    ['label' => 'Total Referidos', 'value' => $stats['total'], 'color' => '#818cf8', 'icon' => '👥'],
                    ['label' => 'Reclamaron', 'value' => $stats['claimed'], 'color' => '#60a5fa', 'icon' => '✅'],
                    ['label' => 'Se Suscribieron', 'value' => $stats['subscribed'], 'color' => '#34d399', 'icon' => '⭐'],
                    ['label' => 'Recompensas', 'value' => $stats['rewarded'], 'color' => '#fbbf24', 'icon' => '🎁'],
                ];
            @endphp
            @foreach($statsConfig as $stat)
            <div style="background-color: #1f2937; border-radius: 0.75rem; padding: 1.25rem; border: 1px solid #374151; text-align: center;">
                <p style="font-size: 1.5rem; margin: 0 0 0.25rem;">{{ $stat['icon'] }}</p>
                <p style="font-size: 1.75rem; font-weight: bold; color: {{ $stat['color'] }}; margin: 0;">{{ $stat['value'] }}</p>
                <p style="font-size: 0.75rem; color: #9ca3af; margin: 0.25rem 0 0; text-transform: uppercase;">{{ $stat['label'] }}</p>
            </div>
            @endforeach
        </div>

        {{-- Cómo funciona --}}
        <div style="background-color: #1f2937; border-radius: 0.75rem; padding: 1.5rem; border: 1px solid #374151;">
            <h3 style="font-size: 1rem; font-weight: 600; color: #ffffff; margin: 0 0 1.25rem;">¿Cómo Funciona?</h3>
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem;">
                @foreach([
                    ['step' => '1', 'title' => 'Comparte tu Enlace', 'desc' => 'Envía tu enlace único a otros dueños de restaurantes mexicanos'],
                    ['step' => '2', 'title' => 'Ellos Reclaman', 'desc' => 'El restaurante usa tu enlace para reclamar su perfil en FAMER'],
                    ['step' => '3', 'title' => 'Ganas 1 Mes Gratis', 'desc' => 'Cuando se suscriban a Premium o Elite, tú recibes 1 mes gratis'],
                ] as $item)
                <div style="text-align: center;">
                    <div style="width: 2.5rem; height: 2.5rem; background: linear-gradient(135deg, #7c3aed, #4f46e5); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 0.75rem; font-size: 1rem; font-weight: bold; color: white;">{{ $item['step'] }}</div>
                    <h4 style="font-size: 0.875rem; font-weight: 600; color: #ffffff; margin: 0 0 0.375rem;">{{ $item['title'] }}</h4>
                    <p style="font-size: 0.75rem; color: #9ca3af; margin: 0; line-height: 1.5;">{{ $item['desc'] }}</p>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Lista de referidos --}}
        @if(count($referrals) > 0)
        <div style="background-color: #1f2937; border-radius: 0.75rem; border: 1px solid #374151; overflow: hidden;">
            <div style="padding: 1.25rem; border-bottom: 1px solid #374151; background-color: #111827;">
                <h3 style="font-size: 1rem; font-weight: 600; color: #ffffff; margin: 0;">Mis Referidos</h3>
            </div>
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="border-bottom: 1px solid #374151;">
                            <th style="padding: 0.75rem 1.25rem; text-align: left; font-size: 0.75rem; color: #9ca3af; text-transform: uppercase;">Restaurante</th>
                            <th style="padding: 0.75rem 1.25rem; text-align: center; font-size: 0.75rem; color: #9ca3af; text-transform: uppercase;">Estado</th>
                            <th style="padding: 0.75rem 1.25rem; text-align: right; font-size: 0.75rem; color: #9ca3af; text-transform: uppercase;">Fecha</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($referrals as $referral)
                        <tr style="border-bottom: 1px solid #374151;">
                            <td style="padding: 1rem 1.25rem; color: #d1d5db; font-size: 0.875rem;">
                                {{ $referral['referred_email'] ?? ($referral['referred']['name'] ?? 'Pendiente') }}
                            </td>
                            <td style="padding: 1rem 1.25rem; text-align: center;">
                                @php
                                    $statusColors = ['pending' => '#f59e0b', 'claimed' => '#60a5fa', 'subscribed' => '#34d399', 'rewarded' => '#fbbf24'];
                                    $statusLabels = ['pending' => 'Pendiente', 'claimed' => 'Reclamado', 'subscribed' => 'Suscrito', 'rewarded' => 'Recompensado'];
                                    $color = $statusColors[$referral['status']] ?? '#9ca3af';
                                    $label = $statusLabels[$referral['status']] ?? ucfirst($referral['status']);
                                @endphp
                                <span style="display: inline-block; padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; background: {{ $color }}20; color: {{ $color }}; border: 1px solid {{ $color }}40;">
                                    {{ $label }}
                                </span>
                            </td>
                            <td style="padding: 1rem 1.25rem; color: #9ca3af; font-size: 0.875rem; text-align: right;">
                                {{ \Carbon\Carbon::parse($referral['created_at'])->format('d M, Y') }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @else
        <div style="background-color: #1f2937; border-radius: 0.75rem; padding: 2rem; text-align: center; border: 1px solid #374151;">
            <p style="font-size: 2rem; margin: 0 0 0.5rem;">📤</p>
            <p style="color: #9ca3af; font-size: 0.875rem; margin: 0;">Aún no tienes referidos. ¡Comparte tu enlace!</p>
        </div>
        @endif

    </div>
    @endif
</x-filament-panels::page>
