<x-filament-panels::page>
    <div style="display: flex; flex-direction: column; gap: 1.5rem;">

        {{-- Embed NotificationCenter bell + full list --}}
        @if($restaurantId)
            @livewire('owner.notification-center', ['restaurantId' => $restaurantId])
        @endif

        {{-- Header --}}
        <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 0.5rem;">
            <div>
                <h2 style="font-size: 1.25rem; font-weight: bold; color: #ffffff; margin: 0;">Centro de Notificaciones</h2>
                <p style="color: #9ca3af; font-size: 0.875rem; margin: 0.25rem 0 0 0;">
                    @if($unreadCount > 0)
                        Tienes <span style="color: #D4AF37; font-weight: 600;">{{ $unreadCount }}</span> notificaci{{ $unreadCount === 1 ? 'ón' : 'ones' }} sin leer
                    @else
                        Todas las notificaciones leídas
                    @endif
                </p>
            </div>
            @if($unreadCount > 0)
            <button wire:click="markAllAsRead"
                    style="background-color: #1A1A1A; color: #D4AF37; padding: 0.5rem 1rem; border-radius: 0.5rem; border: 1px solid rgba(212,175,55,0.3); cursor: pointer; font-size: 0.875rem; transition: background 0.15s;"
                    onmouseover="this.style.backgroundColor='rgba(212,175,55,0.1)'"
                    onmouseout="this.style.backgroundColor='#1A1A1A'">
                Marcar todas como leídas
            </button>
            @endif
        </div>

        {{-- Notifications List --}}
        <div style="background-color: #1A1A1A; border-radius: 0.75rem; border: 1px solid #2A2A2A; overflow: hidden;">
            @forelse($notifications as $notification)
            @php $isUnread = is_null($notification->read_at); @endphp
            <div wire:click="markAsRead({{ $notification->id }})"
                 style="
                     padding: 1rem;
                     border-bottom: 1px solid #2A2A2A;
                     cursor: pointer;
                     display: flex;
                     align-items: flex-start;
                     gap: 1rem;
                     transition: background 0.15s;
                     {{ $isUnread ? 'background-color: rgba(212,175,55,0.04); border-left: 3px solid #D4AF37;' : 'border-left: 3px solid transparent;' }}
                 "
                 onmouseover="this.style.backgroundColor='rgba(255,255,255,0.03)'"
                 onmouseout="this.style.backgroundColor='{{ $isUnread ? 'rgba(212,175,55,0.04)' : 'transparent' }}'">

                {{-- Icon --}}
                <div style="
                    flex-shrink: 0; width: 2.5rem; height: 2.5rem; border-radius: 9999px;
                    display: flex; align-items: center; justify-content: center; font-size: 1.125rem;
                    @if($notification->color === 'yellow') background-color: rgba(212,175,55,0.15);
                    @elseif($notification->color === 'green') background-color: rgba(34,197,94,0.15);
                    @elseif($notification->color === 'red') background-color: rgba(239,68,68,0.15);
                    @elseif($notification->color === 'pink') background-color: rgba(236,72,153,0.15);
                    @elseif($notification->color === 'orange') background-color: rgba(249,115,22,0.15);
                    @elseif($notification->color === 'blue') background-color: rgba(59,130,246,0.15);
                    @else background-color: rgba(107,114,128,0.15);
                    @endif
                ">
                    @if($notification->type === 'new_order')  📦
                    @elseif($notification->type === 'new_review' || $notification->icon === 'star') ⭐
                    @elseif($notification->type === 'new_vote') 🗳️
                    @elseif($notification->icon === 'eye') 👁️
                    @elseif($notification->icon === 'heart') ❤️
                    @elseif($notification->icon === 'chat-bubble-left') 💬
                    @else 🔔
                    @endif
                </div>

                {{-- Content --}}
                <div style="flex: 1; min-width: 0;">
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <h4 style="font-weight: {{ $isUnread ? '600' : '500' }}; color: {{ $isUnread ? '#ffffff' : '#d1d5db' }}; margin: 0; font-size: 0.9375rem;">
                            {{ $notification->title }}
                        </h4>
                        @if($isUnread)
                        <span style="width: 0.5rem; height: 0.5rem; background-color: #D4AF37; border-radius: 9999px; flex-shrink: 0;"></span>
                        @endif
                    </div>
                    <p style="color: #9ca3af; font-size: 0.875rem; margin: 0.25rem 0 0 0; line-height: 1.5;">{{ $notification->message }}</p>
                    <div style="display: flex; align-items: center; gap: 1rem; margin-top: 0.5rem;">
                        <span style="font-size: 0.75rem; color: #6b7280;">{{ $notification->created_at->diffForHumans() }}</span>
                        @if($notification->action_url)
                        <a href="{{ url($notification->action_url) }}"
                           style="font-size: 0.75rem; color: #D4AF37; text-decoration: none;"
                           wire:navigate>Ver detalles →</a>
                        @endif
                    </div>
                </div>
            </div>
            @empty
            <div style="padding: 3rem; text-align: center;">
                <div style="width: 4rem; height: 4rem; background-color: #2A2A2A; border-radius: 9999px; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem; font-size: 1.75rem;">
                    🔔
                </div>
                <h3 style="font-weight: 600; color: #ffffff; margin: 0 0 0.5rem 0;">No hay notificaciones</h3>
                <p style="color: #6b7280; font-size: 0.875rem; margin: 0;">Te avisaremos cuando haya nuevas reseñas, votos o pedidos.</p>
            </div>
            @endforelse
        </div>

        {{-- Notification Types Info --}}
        <div style="background-color: #1A1A1A; border-radius: 0.75rem; padding: 1.25rem; border: 1px solid #2A2A2A;">
            <h3 style="font-size: 1rem; font-weight: 600; color: #ffffff; margin: 0 0 1rem 0;">Tipos de Notificaciones</h3>
            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem;">
                @foreach([
                    ['icon' => '📦', 'bg' => 'rgba(59,130,246,0.12)', 'label' => 'Nuevos Pedidos',        'desc' => 'Al recibir un pedido en tiempo real'],
                    ['icon' => '⭐', 'bg' => 'rgba(212,175,55,0.12)', 'label' => 'Nuevas Reseñas',        'desc' => 'Cuando alguien deja una reseña'],
                    ['icon' => '🗳️', 'bg' => 'rgba(34,197,94,0.12)',  'label' => 'Nuevos Votos',          'desc' => 'Votos en el ranking mensual'],
                    ['icon' => '🔔', 'bg' => 'rgba(107,114,128,0.12)','label' => 'Notificaciones Sistema','desc' => 'Hitos, recordatorios y alertas'],
                ] as $nt)
                <div style="display: flex; align-items: center; gap: 0.75rem;">
                    <div style="width: 2.25rem; height: 2.25rem; background-color: {{ $nt['bg'] }}; border-radius: 0.5rem; display: flex; align-items: center; justify-content: center; font-size: 1rem; flex-shrink: 0;">
                        {{ $nt['icon'] }}
                    </div>
                    <div>
                        <span style="font-size: 0.875rem; color: #ffffff; font-weight: 500;">{{ $nt['label'] }}</span>
                        <p style="font-size: 0.75rem; color: #6b7280; margin: 0;">{{ $nt['desc'] }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

    </div>
</x-filament-panels::page>
