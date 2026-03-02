<x-filament-panels::page>
    <div style="display: flex; flex-direction: column; gap: 1.5rem;">
        
        {{-- Header --}}
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h2 style="font-size: 1.25rem; font-weight: bold; color: #ffffff; margin: 0;">Centro de Notificaciones</h2>
                <p style="color: #9ca3af; font-size: 0.875rem; margin: 0.25rem 0 0 0;">
                    @if($unreadCount > 0)
                        Tienes <span style="color: #ef4444; font-weight: 600;">{{ $unreadCount }}</span> notificaciones sin leer
                    @else
                        Todas las notificaciones leidas
                    @endif
                </p>
            </div>
            @if($unreadCount > 0)
            <button wire:click="markAllAsRead" 
                    style="background-color: #374151; color: #e5e7eb; padding: 0.5rem 1rem; border-radius: 0.5rem; border: none; cursor: pointer; font-size: 0.875rem;">
                Marcar todas como leidas
            </button>
            @endif
        </div>

        {{-- Notifications List --}}
        <div style="background-color: #1f2937; border-radius: 0.75rem; border: 1px solid #374151; overflow: hidden;">
            @forelse($notifications as $notification)
            <div wire:click="markAsRead({{ $notification->id }})" 
                 style="padding: 1rem; border-bottom: 1px solid #374151; cursor: pointer; display: flex; align-items: start; gap: 1rem; {{ $notification->read_at ? '' : 'background-color: rgba(59, 130, 246, 0.1);' }}">
                
                {{-- Icon --}}
                <div style="flex-shrink: 0; width: 2.5rem; height: 2.5rem; border-radius: 9999px; display: flex; align-items: center; justify-content: center;
                    @if($notification->color === 'yellow') background-color: rgba(234, 179, 8, 0.2);
                    @elseif($notification->color === 'green') background-color: rgba(34, 197, 94, 0.2);
                    @elseif($notification->color === 'red') background-color: rgba(239, 68, 68, 0.2);
                    @elseif($notification->color === 'pink') background-color: rgba(236, 72, 153, 0.2);
                    @elseif($notification->color === 'orange') background-color: rgba(249, 115, 22, 0.2);
                    @else background-color: rgba(59, 130, 246, 0.2);
                    @endif
                ">
                    @if($notification->icon === 'star')
                        <svg style="width: 1.25rem; height: 1.25rem; color: #eab308;" fill="currentColor" viewBox="0 0 24 24"><path d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                    @elseif($notification->icon === 'eye')
                        <svg style="width: 1.25rem; height: 1.25rem; color: #22c55e;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    @elseif($notification->icon === 'heart')
                        <svg style="width: 1.25rem; height: 1.25rem; color: #ec4899;" fill="currentColor" viewBox="0 0 24 24"><path d="M11.645 20.91l-.007-.003-.022-.012a15.247 15.247 0 01-.383-.218 25.18 25.18 0 01-4.244-3.17C4.688 15.36 2.25 12.174 2.25 8.25 2.25 5.322 4.714 3 7.688 3A5.5 5.5 0 0112 5.052 5.5 5.5 0 0116.313 3c2.973 0 5.437 2.322 5.437 5.25 0 3.925-2.438 7.111-4.739 9.256a25.175 25.175 0 01-4.244 3.17 15.247 15.247 0 01-.383.219l-.022.012-.007.004-.003.001a.752.752 0 01-.704 0l-.003-.001z"/></svg>
                    @elseif($notification->icon === 'chat-bubble-left')
                        <svg style="width: 1.25rem; height: 1.25rem; color: #f97316;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                    @else
                        <svg style="width: 1.25rem; height: 1.25rem; color: #3b82f6;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                    @endif
                </div>

                {{-- Content --}}
                <div style="flex: 1; min-width: 0;">
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <h4 style="font-weight: 600; color: #ffffff; margin: 0; font-size: 0.9375rem;">{{ $notification->title }}</h4>
                        @if(!$notification->read_at)
                        <span style="width: 0.5rem; height: 0.5rem; background-color: #3b82f6; border-radius: 9999px;"></span>
                        @endif
                    </div>
                    <p style="color: #9ca3af; font-size: 0.875rem; margin: 0.25rem 0 0 0;">{{ $notification->message }}</p>
                    <div style="display: flex; align-items: center; gap: 1rem; margin-top: 0.5rem;">
                        <span style="font-size: 0.75rem; color: #6b7280;">{{ $notification->created_at->diffForHumans() }}</span>
                        @if($notification->action_url)
                        <a href="{{ url($notification->action_url) }}" style="font-size: 0.75rem; color: #818cf8; text-decoration: none;">Ver detalles →</a>
                        @endif
                    </div>
                </div>
            </div>
            @empty
            <div style="padding: 3rem; text-align: center;">
                <div style="width: 4rem; height: 4rem; background-color: #374151; border-radius: 9999px; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem;">
                    <svg style="width: 2rem; height: 2rem; color: #6b7280;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                </div>
                <h3 style="font-weight: 600; color: #ffffff; margin: 0 0 0.5rem 0;">No hay notificaciones</h3>
                <p style="color: #6b7280; font-size: 0.875rem; margin: 0;">Te notificaremos cuando haya nuevas resenas, favoritos o hitos de vistas.</p>
            </div>
            @endforelse
        </div>

        {{-- Notification Types Info --}}
        <div style="background-color: #1f2937; border-radius: 0.75rem; padding: 1.25rem; border: 1px solid #374151;">
            <h3 style="font-size: 1rem; font-weight: 600; color: #ffffff; margin: 0 0 1rem 0;">Tipos de Notificaciones</h3>
            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem;">
                <div style="display: flex; align-items: center; gap: 0.75rem;">
                    <div style="width: 2rem; height: 2rem; background-color: rgba(234, 179, 8, 0.2); border-radius: 0.5rem; display: flex; align-items: center; justify-content: center;">
                        <svg style="width: 1rem; height: 1rem; color: #eab308;" fill="currentColor" viewBox="0 0 24 24"><path d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                    </div>
                    <div>
                        <span style="font-size: 0.875rem; color: #ffffff;">Nuevas Resenas</span>
                        <p style="font-size: 0.75rem; color: #6b7280; margin: 0;">Cuando alguien deja una resena</p>
                    </div>
                </div>
                <div style="display: flex; align-items: center; gap: 0.75rem;">
                    <div style="width: 2rem; height: 2rem; background-color: rgba(34, 197, 94, 0.2); border-radius: 0.5rem; display: flex; align-items: center; justify-content: center;">
                        <svg style="width: 1rem; height: 1rem; color: #22c55e;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    </div>
                    <div>
                        <span style="font-size: 0.875rem; color: #ffffff;">Hitos de Vistas</span>
                        <p style="font-size: 0.75rem; color: #6b7280; margin: 0;">100, 500, 1000+ vistas</p>
                    </div>
                </div>
                <div style="display: flex; align-items: center; gap: 0.75rem;">
                    <div style="width: 2rem; height: 2rem; background-color: rgba(236, 72, 153, 0.2); border-radius: 0.5rem; display: flex; align-items: center; justify-content: center;">
                        <svg style="width: 1rem; height: 1rem; color: #ec4899;" fill="currentColor" viewBox="0 0 24 24"><path d="M11.645 20.91l-.007-.003-.022-.012a15.247 15.247 0 01-.383-.218 25.18 25.18 0 01-4.244-3.17C4.688 15.36 2.25 12.174 2.25 8.25 2.25 5.322 4.714 3 7.688 3A5.5 5.5 0 0112 5.052 5.5 5.5 0 0116.313 3c2.973 0 5.437 2.322 5.437 5.25 0 3.925-2.438 7.111-4.739 9.256a25.175 25.175 0 01-4.244 3.17 15.247 15.247 0 01-.383.219l-.022.012-.007.004-.003.001a.752.752 0 01-.704 0l-.003-.001z"/></svg>
                    </div>
                    <div>
                        <span style="font-size: 0.875rem; color: #ffffff;">Nuevos Favoritos</span>
                        <p style="font-size: 0.75rem; color: #6b7280; margin: 0;">Cuando agregan tu restaurante</p>
                    </div>
                </div>
                <div style="display: flex; align-items: center; gap: 0.75rem;">
                    <div style="width: 2rem; height: 2rem; background-color: rgba(249, 115, 22, 0.2); border-radius: 0.5rem; display: flex; align-items: center; justify-content: center;">
                        <svg style="width: 1rem; height: 1rem; color: #f97316;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                    </div>
                    <div>
                        <span style="font-size: 0.875rem; color: #ffffff;">Resenas Pendientes</span>
                        <p style="font-size: 0.75rem; color: #6b7280; margin: 0;">Resenas sin responder</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
