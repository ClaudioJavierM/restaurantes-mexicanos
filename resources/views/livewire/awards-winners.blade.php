<div style="min-height:100vh; background:#0B0B0B; color:#F5F5F5; font-family:'Poppins',sans-serif;">

    @push('head')
    <meta property="og:title" content="FAMER Awards — {{ $monthName }} {{ $year }}">
    <meta property="og:description" content="Los restaurantes mexicanos más votados por la comunidad en {{ $monthName }} {{ $year }}. Descubre quién ganó.">
    <meta property="og:image" content="{{ asset('images/branding/famer55.png') }}">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="FAMER Awards — {{ $monthName }} {{ $year }}">
    <meta name="twitter:description" content="Los restaurantes mexicanos más votados en {{ $monthName }} {{ $year }}.">
    <meta name="twitter:image" content="{{ asset('images/branding/famer55.png') }}">
    @endpush

    {{-- ══════════════════════════════════════════════════ --}}
    {{-- HERO                                              --}}
    {{-- ══════════════════════════════════════════════════ --}}
    <section style="background:linear-gradient(180deg,#111 0%,#0B0B0B 100%); border-bottom:1px solid rgba(212,175,55,0.2); padding:4rem 1.5rem 3rem; text-align:center; position:relative; overflow:hidden;">
        {{-- Radial gold glow --}}
        <div style="position:absolute; inset:0; background:radial-gradient(ellipse 60% 50% at 50% 30%, rgba(212,175,55,0.06) 0%, transparent 70%); pointer-events:none;"></div>

        <div style="position:relative; z-index:1; max-width:700px; margin:0 auto;">
            <div style="font-size:3rem; margin-bottom:1rem; line-height:1;">🏆</div>

            <h1 style="font-family:'Playfair Display',serif; font-size:clamp(2rem,6vw,2.5rem); font-weight:800; color:#D4AF37; margin:0 0 0.5rem; letter-spacing:-0.01em;">
                FAMER Awards
            </h1>
            <p style="color:#AAAAAA; font-size:1rem; margin:0 0 0.25rem;">
                Los restaurantes más votados por la comunidad
            </p>
            <p style="color:#666; font-size:0.875rem;">
                <a href="{{ route('awards.hall-of-fame') }}" style="color:#D4AF37; text-decoration:none;" wire:navigate>
                    Ver Hall of Fame →
                </a>
            </p>
        </div>
    </section>

    {{-- ══════════════════════════════════════════════════ --}}
    {{-- MONTH SELECTOR                                    --}}
    {{-- ══════════════════════════════════════════════════ --}}
    @php
        $shortMonths = ['','Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'];
    @endphp

    <div style="padding:1.5rem 1.5rem 0; overflow-x:auto;">
        <div style="display:flex; gap:0.5rem; width:max-content; margin:0 auto;">
            @forelse($availableMonths as $am)
                @php
                    $isActive = ($am->year == $year && $am->month == $month);
                @endphp
                <button
                    wire:click="setMonth({{ $am->year }}, {{ $am->month }})"
                    style="
                        padding:0.45rem 1rem;
                        border-radius:999px;
                        border:1px solid {{ $isActive ? '#D4AF37' : 'rgba(212,175,55,0.25)' }};
                        background:{{ $isActive ? '#D4AF37' : 'transparent' }};
                        color:{{ $isActive ? '#0B0B0B' : '#AAAAAA' }};
                        font-size:0.8rem;
                        font-weight:{{ $isActive ? '700' : '400' }};
                        font-family:'Poppins',sans-serif;
                        cursor:pointer;
                        white-space:nowrap;
                        transition:all 0.15s;
                    "
                    onmouseover="if(!{{ $isActive ? 'true' : 'false' }})this.style.borderColor='#D4AF37';this.style.color='#D4AF37';"
                    onmouseout="if(!{{ $isActive ? 'true' : 'false' }})this.style.borderColor='rgba(212,175,55,0.25)';this.style.color='#AAAAAA';"
                >
                    {{ $shortMonths[$am->month] }} {{ $am->year }}
                </button>
            @empty
                <span style="color:#666; font-size:0.875rem; padding:0.5rem;">Sin datos disponibles aún</span>
            @endforelse
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════ --}}
    {{-- CONTENT                                           --}}
    {{-- ══════════════════════════════════════════════════ --}}
    <div style="max-width:900px; margin:0 auto; padding:2.5rem 1.5rem 4rem;">

        {{-- Month title --}}
        <h2 style="font-family:'Playfair Display',serif; font-size:1.5rem; font-weight:700; text-align:center; color:#F5F5F5; margin:0 0 2.5rem;">
            Ganadores — {{ $monthName }} {{ $year }}
        </h2>

        @if($winners->isEmpty())
            {{-- Empty state --}}
            <div style="text-align:center; padding:3rem 1.5rem; background:#1A1A1A; border-radius:1rem; border:1px solid rgba(212,175,55,0.15);">
                <div style="font-size:2.5rem; margin-bottom:1rem;">🗳️</div>
                <p style="color:#CCCCCC; font-size:1.05rem; margin:0 0 0.5rem;">
                    Los votos de <strong style="color:#D4AF37;">{{ $monthName }}</strong> están en proceso.
                </p>
                <p style="color:#888; font-size:0.9rem; margin:0 0 1.5rem;">
                    ¡Vota por tu restaurante favorito y ayúdalo a ganar!
                </p>
                <a href="{{ route('votar') }}"
                   style="display:inline-block; padding:0.75rem 2rem; background:#D4AF37; color:#0B0B0B; font-weight:700; border-radius:0.75rem; text-decoration:none; font-size:0.95rem;">
                    Votar ahora →
                </a>
            </div>

        @else

            {{-- ─── PODIUM (TOP 3) ─── --}}
            @php
                $top3 = $winners->take(3);
                $first  = $top3->get(0);
                $second = $top3->get(1);
                $third  = $top3->get(2);
            @endphp

            <div style="display:flex; align-items:flex-end; justify-content:center; gap:1rem; margin-bottom:3rem; flex-wrap:wrap;">

                {{-- 2nd place --}}
                @if($second)
                <div style="flex:1; min-width:160px; max-width:220px; text-align:center;">
                    <a href="{{ route('restaurants.show', $second->slug) }}" style="text-decoration:none; display:block;">
                        <div style="background:#1A1A1A; border:1px solid rgba(212,175,55,0.2); border-radius:1rem; padding:1.5rem 1rem 1.25rem; transition:border-color 0.2s;"
                             onmouseover="this.style.borderColor='rgba(212,175,55,0.5)'"
                             onmouseout="this.style.borderColor='rgba(212,175,55,0.2)'">
                            <div style="font-size:2rem; margin-bottom:0.75rem;">🥈</div>
                            <div style="width:70px; height:70px; border-radius:50%; overflow:hidden; margin:0 auto 0.75rem; border:2px solid rgba(192,192,192,0.4);">
                                @if($second->image)
                                    <img src="{{ $second->image }}" alt="{{ $second->name }}" style="width:100%; height:100%; object-fit:cover;">
                                @else
                                    <div style="width:100%; height:100%; background:#2A2A2A; display:flex; align-items:center; justify-content:center; font-size:1.75rem;">🍽️</div>
                                @endif
                            </div>
                            <div style="font-weight:700; color:#F5F5F5; font-size:0.9rem; margin-bottom:0.25rem; line-height:1.3;">{{ $second->name }}</div>
                            <div style="color:#888; font-size:0.75rem; margin-bottom:0.5rem;">{{ $second->city }}{{ $second->state ? ', '.$second->state->code : '' }}</div>
                            <div style="color:#C0C0C0; font-size:0.8rem; font-weight:600;">{{ number_format($second->vote_count) }} votos</div>
                        </div>
                    </a>
                </div>
                @endif

                {{-- 1st place (elevated) --}}
                @if($first)
                <div style="flex:1; min-width:180px; max-width:260px; text-align:center; margin-bottom:-1rem;">
                    <a href="{{ route('restaurants.show', $first->slug) }}" style="text-decoration:none; display:block;">
                        <div style="background:#1A1A1A; border:2px solid #D4AF37; border-radius:1rem; padding:2rem 1.25rem 1.5rem; box-shadow:0 0 30px rgba(212,175,55,0.2); transition:box-shadow 0.2s;"
                             onmouseover="this.style.boxShadow='0 0 50px rgba(212,175,55,0.35)'"
                             onmouseout="this.style.boxShadow='0 0 30px rgba(212,175,55,0.2)'">
                            <div style="font-size:2.5rem; margin-bottom:0.75rem;">🥇</div>
                            <div style="width:90px; height:90px; border-radius:50%; overflow:hidden; margin:0 auto 0.75rem; border:3px solid #D4AF37;">
                                @if($first->image)
                                    <img src="{{ $first->image }}" alt="{{ $first->name }}" style="width:100%; height:100%; object-fit:cover;">
                                @else
                                    <div style="width:100%; height:100%; background:#2A2A2A; display:flex; align-items:center; justify-content:center; font-size:2rem;">🍽️</div>
                                @endif
                            </div>
                            <div style="font-weight:700; color:#F5F5F5; font-size:1rem; margin-bottom:0.25rem; line-height:1.3;">{{ $first->name }}</div>
                            <div style="color:#888; font-size:0.8rem; margin-bottom:0.5rem;">{{ $first->city }}{{ $first->state ? ', '.$first->state->code : '' }}</div>
                            <div style="color:#D4AF37; font-size:0.9rem; font-weight:700;">{{ number_format($first->vote_count) }} votos</div>
                        </div>
                    </a>
                </div>
                @endif

                {{-- 3rd place --}}
                @if($third)
                <div style="flex:1; min-width:160px; max-width:220px; text-align:center;">
                    <a href="{{ route('restaurants.show', $third->slug) }}" style="text-decoration:none; display:block;">
                        <div style="background:#1A1A1A; border:1px solid rgba(212,175,55,0.2); border-radius:1rem; padding:1.5rem 1rem 1.25rem; transition:border-color 0.2s;"
                             onmouseover="this.style.borderColor='rgba(212,175,55,0.5)'"
                             onmouseout="this.style.borderColor='rgba(212,175,55,0.2)'">
                            <div style="font-size:2rem; margin-bottom:0.75rem;">🥉</div>
                            <div style="width:70px; height:70px; border-radius:50%; overflow:hidden; margin:0 auto 0.75rem; border:2px solid rgba(205,127,50,0.4);">
                                @if($third->image)
                                    <img src="{{ $third->image }}" alt="{{ $third->name }}" style="width:100%; height:100%; object-fit:cover;">
                                @else
                                    <div style="width:100%; height:100%; background:#2A2A2A; display:flex; align-items:center; justify-content:center; font-size:1.75rem;">🍽️</div>
                                @endif
                            </div>
                            <div style="font-weight:700; color:#F5F5F5; font-size:0.9rem; margin-bottom:0.25rem; line-height:1.3;">{{ $third->name }}</div>
                            <div style="color:#888; font-size:0.75rem; margin-bottom:0.5rem;">{{ $third->city }}{{ $third->state ? ', '.$third->state->code : '' }}</div>
                            <div style="color:#CD7F32; font-size:0.8rem; font-weight:600;">{{ number_format($third->vote_count) }} votos</div>
                        </div>
                    </a>
                </div>
                @endif

            </div>

            {{-- ─── TOP 4-10 LIST ─── --}}
            @php $rest = $winners->slice(3); @endphp
            @if($rest->isNotEmpty())
            <div style="margin-bottom:2.5rem;">
                <h3 style="font-size:0.85rem; font-weight:600; color:#666; letter-spacing:0.08em; text-transform:uppercase; margin:0 0 1rem;">
                    También destacados
                </h3>
                <div style="display:flex; flex-direction:column; gap:0.5rem;">
                    @foreach($rest as $i => $r)
                    <a href="{{ route('restaurants.show', $r->slug) }}"
                       style="display:flex; align-items:center; gap:1rem; background:#1A1A1A; border:1px solid rgba(255,255,255,0.06); border-radius:0.75rem; padding:0.875rem 1.25rem; text-decoration:none; transition:border-color 0.2s;"
                       onmouseover="this.style.borderColor='rgba(212,175,55,0.3)'"
                       onmouseout="this.style.borderColor='rgba(255,255,255,0.06)'">
                        <span style="font-size:1.1rem; font-weight:800; color:#555; min-width:1.75rem; text-align:center;">
                            {{ $i + 4 }}
                        </span>
                        <div style="width:40px; height:40px; border-radius:50%; overflow:hidden; flex-shrink:0; border:1px solid rgba(255,255,255,0.1);">
                            @if($r->image)
                                <img src="{{ $r->image }}" alt="{{ $r->name }}" style="width:100%; height:100%; object-fit:cover;">
                            @else
                                <div style="width:100%; height:100%; background:#2A2A2A; display:flex; align-items:center; justify-content:center; font-size:1.1rem;">🍽️</div>
                            @endif
                        </div>
                        <div style="flex:1; min-width:0;">
                            <div style="font-weight:600; color:#F5F5F5; font-size:0.9rem; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">{{ $r->name }}</div>
                            <div style="color:#666; font-size:0.78rem;">{{ $r->city }}{{ $r->state ? ', '.$r->state->code : '' }}</div>
                        </div>
                        <div style="color:#888; font-size:0.85rem; font-weight:600; white-space:nowrap; flex-shrink:0;">
                            {{ number_format($r->vote_count) }} votos
                        </div>
                        <span style="color:#444; font-size:0.9rem; flex-shrink:0;">›</span>
                    </a>
                    @endforeach
                </div>
            </div>
            @endif

        @endif

        {{-- ─── CTA VOTE ─── --}}
        <div style="border:1px solid rgba(212,175,55,0.35); border-radius:1rem; padding:2rem 1.5rem; text-align:center; margin-bottom:2rem;">
            <div style="font-size:1.75rem; margin-bottom:0.75rem;">🗳️</div>
            <h3 style="font-family:'Playfair Display',serif; font-size:1.25rem; font-weight:700; color:#F5F5F5; margin:0 0 0.5rem;">
                ¿Aún no has votado este mes?
            </h3>
            <p style="color:#888; font-size:0.9rem; margin:0 0 1.25rem;">
                Tu voto decide quién gana los FAMER Awards de {{ $monthName }}.
            </p>
            <a href="{{ route('votar') }}"
               style="display:inline-block; padding:0.75rem 2rem; background:#D4AF37; color:#0B0B0B; font-weight:700; border-radius:0.75rem; text-decoration:none; font-size:0.95rem; transition:background 0.2s;"
               onmouseover="this.style.background='#B8962E'"
               onmouseout="this.style.background='#D4AF37'">
                Votar ahora →
            </a>
        </div>

        {{-- ─── HALL OF FAME LINK ─── --}}
        <div style="text-align:center;">
            <a href="{{ route('awards.hall-of-fame') }}"
               style="color:#D4AF37; font-size:0.9rem; text-decoration:none; display:inline-flex; align-items:center; gap:0.4rem; opacity:0.8; transition:opacity 0.2s;"
               onmouseover="this.style.opacity='1'"
               onmouseout="this.style.opacity='0.8'">
                👑 Ver el Hall of Fame de todos los tiempos
            </a>
        </div>

    </div>
</div>
