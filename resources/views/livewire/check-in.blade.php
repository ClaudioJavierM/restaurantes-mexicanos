<div style="background:#111111; border:1px solid #2A2A2A; border-radius:12px; padding:1.25rem; margin-top:1rem;">

    {{-- Header --}}
    <div style="display:flex; align-items:center; gap:0.625rem; margin-bottom:1rem;">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
             stroke="#D4AF37" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/>
            <circle cx="12" cy="10" r="3"/>
        </svg>
        <span style="font-size:0.875rem; font-weight:600; color:#D4AF37; letter-spacing:0.5px;">
            Registro de Visitas
        </span>
    </div>

    {{-- Total check-ins --}}
    <div style="display:flex; align-items:center; gap:0.5rem; margin-bottom:1rem;">
        <span style="font-size:1.375rem; font-weight:700; color:#F5F5F5;">
            {{ number_format($this->checkinCount) }}
        </span>
        <span style="font-size:0.8rem; color:#888; line-height:1.3;">
            {{ $this->checkinCount === 1 ? 'persona ha visitado' : 'personas han visitado' }}<br>
            este restaurante
        </span>
    </div>

    {{-- Flash messages --}}
    @if(session('checkin_success'))
        <div style="background:#0D2818; border:1px solid #1F5A34; border-radius:8px;
                    padding:0.625rem 0.875rem; margin-bottom:0.875rem;
                    display:flex; align-items:center; gap:0.5rem;">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none"
                 stroke="#34D399" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="20 6 9 17 4 12"/>
            </svg>
            <span style="font-size:0.8125rem; color:#34D399; font-weight:600;">
                {{ session('checkin_success') }}
            </span>
        </div>
    @endif

    @if(session('checkin_error'))
        <div style="background:#1F0D0D; border:1px solid #5A1F1F; border-radius:8px;
                    padding:0.625rem 0.875rem; margin-bottom:0.875rem;
                    display:flex; align-items:center; gap:0.5rem;">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none"
                 stroke="#F87171" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/>
                <line x1="12" y1="16" x2="12.01" y2="16"/>
            </svg>
            <span style="font-size:0.8125rem; color:#F87171; font-weight:600;">
                {{ session('checkin_error') }}
            </span>
        </div>
    @endif

    @guest
        {{-- Not authenticated --}}
        <div style="background:#0D0D0D; border:1px solid #1E1E1E; border-radius:8px;
                    padding:0.875rem; text-align:center;">
            <p style="font-size:0.8125rem; color:#888; margin:0 0 0.625rem;">
                Inicia sesión para registrar tu visita
            </p>
            <a href="{{ route('login') }}"
               style="display:inline-block; padding:0.5rem 1.25rem;
                      background:linear-gradient(135deg,#D4AF37,#F0C040); color:#0B0B0B;
                      border-radius:6px; font-size:0.8125rem; font-weight:700;
                      text-decoration:none;">
                Iniciar sesión
            </a>
        </div>
    @endguest

    @auth
        {{-- Authenticated: check-in area --}}
        @if($this->hasCheckedInToday)
            {{-- Already checked in today --}}
            <div style="background:#0B1A10; border:1px solid #1F3D2B; border-radius:8px;
                        padding:0.75rem 1rem; display:flex; align-items:center; gap:0.625rem;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                     stroke="#34D399" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="20 6 9 17 4 12"/>
                </svg>
                <span style="font-size:0.875rem; font-weight:600; color:#34D399;">
                    ✓ Visitado hoy
                </span>
            </div>
        @else
            {{-- Check-in form --}}
            <div style="display:flex; flex-direction:column; gap:0.625rem;">
                <textarea
                    wire:model="note"
                    placeholder="Nota opcional (ej. 'Fui con familia')"
                    rows="2"
                    style="width:100%; padding:0.5rem 0.75rem; background:#0D0D0D;
                           border:1px solid #2A2A2A; border-radius:6px; color:#E5E5E5;
                           font-size:0.8rem; resize:none; outline:none;
                           font-family:inherit; box-sizing:border-box;"
                ></textarea>
                <button
                    wire:click="checkIn"
                    wire:loading.attr="disabled"
                    style="width:100%; padding:0.625rem; display:flex; align-items:center;
                           justify-content:center; gap:0.5rem;
                           background:linear-gradient(135deg,#D4AF37,#F0C040); color:#0B0B0B;
                           border:none; border-radius:8px; font-size:0.875rem; font-weight:700;
                           cursor:pointer; transition:opacity 0.15s;">
                    <span wire:loading.remove wire:target="checkIn">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"
                             style="display:inline; vertical-align:middle; margin-right:4px;">
                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/>
                            <circle cx="12" cy="10" r="3"/>
                        </svg>
                        Marcar mi visita hoy
                    </span>
                    <span wire:loading wire:target="checkIn"
                          style="font-size:0.8rem; opacity:0.7;">
                        Registrando...
                    </span>
                </button>
            </div>
        @endif

        {{-- User visit stats --}}
        @if($this->userCheckinCount > 0)
            <div style="margin-top:1rem; padding-top:1rem; border-top:1px solid #1E1E1E;">
                <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:0.625rem;">
                    <span style="font-size:0.8rem; color:#888;">Tus visitas</span>
                    <span style="font-size:1rem; font-weight:700; color:#F5F5F5;">
                        {{ $this->userCheckinCount }}
                    </span>
                </div>

                {{-- Achievement badge --}}
                @php $achievement = $this->achievementBadge; @endphp
                @if(!empty($achievement))
                    <div style="display:inline-flex; align-items:center; gap:0.375rem;
                                padding:0.3rem 0.75rem;
                                background:{{ $achievement['color'] }}1A;
                                border:1px solid {{ $achievement['color'] }}55;
                                border-radius:999px;">
                        <svg width="11" height="11" viewBox="0 0 24 24"
                             fill="{{ $achievement['color'] }}" stroke="none">
                            <polygon points="12,2 15.09,8.26 22,9.27 17,14.14 18.18,21.02 12,17.77 5.82,21.02 7,14.14 2,9.27 8.91,8.26"/>
                        </svg>
                        <span style="font-size:0.75rem; font-weight:600; color:{{ $achievement['color'] }};">
                            {{ $achievement['label'] }}
                        </span>
                    </div>
                @endif
            </div>
        @endif
    @endauth
</div>
