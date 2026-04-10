<div x-data="{ open: @entangle('showDropdown') }" style="position: relative; display: inline-block;">

    {{-- Bell Button --}}
    <button
        wire:click="toggleDropdown"
        style="position: relative; background: none; border: none; cursor: pointer; padding: 0.5rem; border-radius: 0.5rem; display: flex; align-items: center; justify-content: center; transition: background 0.2s;"
        onmouseover="this.style.backgroundColor='#1A1A1A'"
        onmouseout="this.style.backgroundColor='transparent'"
        title="Notificaciones"
    >
        {{-- Bell Icon --}}
        <svg style="width: 1.5rem; height: 1.5rem; color: #9ca3af;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                  d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
        </svg>

        {{-- Unread Badge --}}
        @if($this->unreadCount > 0)
        <span style="
            position: absolute;
            top: 0.125rem;
            right: 0.125rem;
            min-width: 1.125rem;
            height: 1.125rem;
            background-color: #ef4444;
            color: #ffffff;
            font-size: 0.625rem;
            font-weight: 700;
            border-radius: 9999px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0 0.25rem;
            line-height: 1;
            border: 1.5px solid #0B0B0B;
        ">{{ $this->unreadCount > 99 ? '99+' : $this->unreadCount }}</span>
        @endif
    </button>

    {{-- Dropdown --}}
    <div
        x-show="open"
        x-cloak
        @click.outside="open = false; $wire.closeDropdown()"
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        style="
            position: absolute;
            right: 0;
            top: calc(100% + 0.5rem);
            width: 22rem;
            background-color: #1A1A1A;
            border: 1px solid #2A2A2A;
            border-radius: 0.75rem;
            box-shadow: 0 20px 40px rgba(0,0,0,0.6);
            z-index: 9999;
            overflow: hidden;
        "
    >
        {{-- Dropdown Header --}}
        <div style="
            padding: 0.875rem 1rem;
            border-bottom: 1px solid #2A2A2A;
            display: flex;
            align-items: center;
            justify-content: space-between;
        ">
            <div style="display: flex; align-items: center; gap: 0.5rem;">
                <span style="font-size: 0.9375rem; font-weight: 600; color: #ffffff;">Notificaciones</span>
                @if($this->unreadCount > 0)
                <span style="
                    background-color: rgba(212, 175, 55, 0.15);
                    color: #D4AF37;
                    font-size: 0.6875rem;
                    font-weight: 600;
                    padding: 0.125rem 0.5rem;
                    border-radius: 9999px;
                    border: 1px solid rgba(212, 175, 55, 0.3);
                ">{{ $this->unreadCount }} nuevas</span>
                @endif
            </div>

            @if($this->unreadCount > 0)
            <button
                wire:click="markAllRead"
                style="
                    background: none;
                    border: none;
                    cursor: pointer;
                    font-size: 0.75rem;
                    color: #D4AF37;
                    padding: 0.25rem 0.5rem;
                    border-radius: 0.375rem;
                    transition: background 0.15s;
                "
                onmouseover="this.style.backgroundColor='rgba(212,175,55,0.1)'"
                onmouseout="this.style.backgroundColor='transparent'"
            >Marcar todo como leído</button>
            @endif
        </div>

        {{-- Notification List --}}
        <div style="max-height: 26rem; overflow-y: auto; scrollbar-width: thin; scrollbar-color: #2A2A2A transparent;">

            @forelse($notifications as $notification)
            @php
                $isUnread = is_null($notification['read_at']);
                $type     = $notification['type'] ?? 'system';
            @endphp
            <div
                wire:click="markRead({{ $notification['id'] }})"
                style="
                    padding: 0.875rem 1rem;
                    border-bottom: 1px solid #2A2A2A;
                    display: flex;
                    align-items: flex-start;
                    gap: 0.75rem;
                    cursor: pointer;
                    transition: background 0.15s;
                    {{ $isUnread ? 'background-color: rgba(212,175,55,0.04); border-left: 3px solid #D4AF37;' : 'border-left: 3px solid transparent;' }}
                "
                onmouseover="this.style.backgroundColor='rgba(255,255,255,0.04)'"
                onmouseout="this.style.backgroundColor='{{ $isUnread ? 'rgba(212,175,55,0.04)' : 'transparent' }}'"
            >
                {{-- Type Icon --}}
                <div style="
                    flex-shrink: 0;
                    width: 2.125rem;
                    height: 2.125rem;
                    border-radius: 9999px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    font-size: 1.0625rem;
                    {{ $type === 'new_order'  ? 'background-color: rgba(59,130,246,0.15);'  :
                      ($type === 'new_review' ? 'background-color: rgba(212,175,55,0.15);' :
                      ($type === 'new_vote'   ? 'background-color: rgba(34,197,94,0.15);'  :
                                               'background-color: rgba(107,114,128,0.15);')) }}
                ">
                    @if($type === 'new_order')  📦
                    @elseif($type === 'new_review') ⭐
                    @elseif($type === 'new_vote') 🗳️
                    @elseif($notification['icon'] === 'star') ⭐
                    @elseif($notification['icon'] === 'heart') ❤️
                    @elseif($notification['icon'] === 'eye') 👁️
                    @else 🔔
                    @endif
                </div>

                {{-- Content --}}
                <div style="flex: 1; min-width: 0;">
                    <div style="display: flex; align-items: center; gap: 0.375rem; margin-bottom: 0.125rem;">
                        <span style="
                            font-size: 0.875rem;
                            font-weight: {{ $isUnread ? '600' : '500' }};
                            color: {{ $isUnread ? '#ffffff' : '#d1d5db' }};
                            white-space: nowrap;
                            overflow: hidden;
                            text-overflow: ellipsis;
                            max-width: 13rem;
                        ">{{ $notification['title'] }}</span>
                        @if($isUnread)
                        <span style="
                            width: 0.4375rem;
                            height: 0.4375rem;
                            background-color: #D4AF37;
                            border-radius: 9999px;
                            flex-shrink: 0;
                        "></span>
                        @endif
                    </div>
                    <p style="
                        font-size: 0.8125rem;
                        color: #9ca3af;
                        margin: 0;
                        line-height: 1.4;
                        overflow: hidden;
                        display: -webkit-box;
                        -webkit-line-clamp: 2;
                        -webkit-box-orient: vertical;
                    ">{{ $notification['message'] }}</p>
                    <span style="font-size: 0.6875rem; color: #6b7280; display: block; margin-top: 0.25rem;">
                        {{ \Carbon\Carbon::parse($notification['created_at'])->diffForHumans() }}
                    </span>
                </div>
            </div>
            @empty
            <div style="padding: 2.5rem 1rem; text-align: center;">
                <div style="
                    width: 3.5rem;
                    height: 3.5rem;
                    background-color: #2A2A2A;
                    border-radius: 9999px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    margin: 0 auto 0.875rem;
                    font-size: 1.5rem;
                ">🔔</div>
                <p style="color: #9ca3af; font-size: 0.875rem; margin: 0; font-weight: 500;">No tienes notificaciones</p>
                <p style="color: #6b7280; font-size: 0.75rem; margin: 0.25rem 0 0 0;">Te avisaremos cuando haya actividad</p>
            </div>
            @endforelse
        </div>

        {{-- Footer --}}
        @if(count($notifications) > 0)
        <div style="padding: 0.625rem 1rem; border-top: 1px solid #2A2A2A; text-align: center;">
            <a href="/owner/notifications" style="
                font-size: 0.8125rem;
                color: #D4AF37;
                text-decoration: none;
                font-weight: 500;
            ">Ver todas las notificaciones →</a>
        </div>
        @endif
    </div>
</div>
