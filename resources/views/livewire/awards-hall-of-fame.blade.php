<div style="min-height:100vh; background:#0B0B0B; color:#F5F5F5; font-family:'Poppins',sans-serif;">

    {{-- ══════════════════════════════════════════════════ --}}
    {{-- HERO                                              --}}
    {{-- ══════════════════════════════════════════════════ --}}
    <section style="background:linear-gradient(180deg,#111 0%,#0B0B0B 100%); border-bottom:1px solid rgba(212,175,55,0.2); padding:4rem 1.5rem 3rem; text-align:center; position:relative; overflow:hidden;">
        <div style="position:absolute; inset:0; background:radial-gradient(ellipse 60% 50% at 50% 30%, rgba(212,175,55,0.07) 0%, transparent 70%); pointer-events:none;"></div>

        <div style="position:relative; z-index:1; max-width:700px; margin:0 auto;">
            <div style="font-size:3rem; margin-bottom:1rem; line-height:1;">👑</div>

            <h1 style="font-family:'Playfair Display',serif; font-size:clamp(2rem,6vw,2.8rem); font-weight:800; color:#D4AF37; margin:0 0 0.5rem; letter-spacing:-0.01em;">
                Hall of Fame
            </h1>
            <p style="color:#AAAAAA; font-size:1rem; margin:0 0 0.5rem; max-width:520px; margin-left:auto; margin-right:auto; line-height:1.6;">
                Los restaurantes que han marcado la historia de FAMER
            </p>
            <p style="margin-top:1rem;">
                <a href="{{ route('awards.winners') }}" style="color:#D4AF37; font-size:0.85rem; text-decoration:none; opacity:0.8;"
                   onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0.8'">
                    ← Ver ganadores del mes
                </a>
            </p>
        </div>
    </section>

    <div style="max-width:860px; margin:0 auto; padding:3rem 1.5rem 5rem;">

        @if($leaders->isEmpty())
            <div style="text-align:center; padding:4rem 1.5rem;">
                <div style="font-size:2.5rem; margin-bottom:1rem;">🏆</div>
                <p style="color:#888; font-size:1rem;">
                    El Hall of Fame se construye con los votos de la comunidad.<br>
                    ¡Sé el primero en votar!
                </p>
                <a href="{{ route('votar') }}"
                   style="display:inline-block; margin-top:1.5rem; padding:0.75rem 2rem; background:#D4AF37; color:#0B0B0B; font-weight:700; border-radius:0.75rem; text-decoration:none; font-size:0.95rem;">
                    Votar ahora →
                </a>
            </div>
        @else

            {{-- ─── TOP 3 SPECIAL CARDS ─── --}}
            @php
                $topThree = $leaders->take(3);
                $medals   = ['🥇', '🥈', '🥉'];
                $borders  = ['#D4AF37', '#C0C0C0', '#CD7F32'];
                $shadows  = [
                    '0 0 40px rgba(212,175,55,0.25)',
                    '0 0 20px rgba(192,192,192,0.1)',
                    '0 0 20px rgba(205,127,50,0.1)',
                ];
            @endphp

            <h2 style="font-size:0.8rem; font-weight:600; color:#555; letter-spacing:0.1em; text-transform:uppercase; margin:0 0 1.5rem; text-align:center;">
                Leyendas FAMER
            </h2>

            <div style="display:flex; flex-wrap:wrap; gap:1rem; justify-content:center; margin-bottom:3rem;">
                @foreach($topThree as $idx => $leader)
                <div style="flex:1; min-width:200px; max-width:260px;">
                    <a href="{{ route('restaurant.show', $leader->slug) }}" style="text-decoration:none; display:block;">
                        <div style="
                            background:#1A1A1A;
                            border:2px solid {{ $borders[$idx] }};
                            border-radius:1rem;
                            padding:2rem 1.25rem 1.5rem;
                            text-align:center;
                            box-shadow:{{ $shadows[$idx] }};
                            transition:box-shadow 0.2s;
                        "
                             onmouseover="this.style.boxShadow='{{ str_replace('0.25','0.4',$shadows[$idx]) }}'"
                             onmouseout="this.style.boxShadow='{{ $shadows[$idx] }}'">

                            <div style="font-size:2.25rem; margin-bottom:0.75rem;">{{ $medals[$idx] }}</div>

                            <div style="width:80px; height:80px; border-radius:50%; overflow:hidden; margin:0 auto 1rem; border:3px solid {{ $borders[$idx] }};">
                                @if($leader->image)
                                    <img src="{{ $leader->image }}" alt="{{ $leader->name }}" style="width:100%; height:100%; object-fit:cover;">
                                @else
                                    <div style="width:100%; height:100%; background:#2A2A2A; display:flex; align-items:center; justify-content:center; font-size:2rem;">🍽️</div>
                                @endif
                            </div>

                            <div style="font-family:'Playfair Display',serif; font-size:1rem; font-weight:700; color:#F5F5F5; margin-bottom:0.3rem; line-height:1.3;">
                                {{ $leader->name }}
                            </div>
                            <div style="color:#777; font-size:0.78rem; margin-bottom:1rem;">
                                {{ $leader->city }}{{ $leader->state ? ', '.$leader->state->code : '' }}
                            </div>

                            <div style="display:flex; justify-content:center; gap:1.5rem; border-top:1px solid rgba(255,255,255,0.07); padding-top:0.875rem;">
                                <div style="text-align:center;">
                                    <div style="color:{{ $borders[$idx] }}; font-size:1.1rem; font-weight:700;">{{ number_format($leader->total_votes) }}</div>
                                    <div style="color:#666; font-size:0.7rem; text-transform:uppercase; letter-spacing:0.05em;">votos totales</div>
                                </div>
                                <div style="text-align:center;">
                                    <div style="color:{{ $borders[$idx] }}; font-size:1.1rem; font-weight:700;">{{ $leader->months_participated }}</div>
                                    <div style="color:#666; font-size:0.7rem; text-transform:uppercase; letter-spacing:0.05em;">meses activo</div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                @endforeach
            </div>

            {{-- ─── FULL LEADERBOARD #1-25 ─── --}}
            <h2 style="font-size:0.8rem; font-weight:600; color:#555; letter-spacing:0.1em; text-transform:uppercase; margin:0 0 1rem; text-align:center;">
                Tabla completa — Top {{ $leaders->count() }}
            </h2>

            <div style="background:#111; border:1px solid rgba(255,255,255,0.06); border-radius:1rem; overflow:hidden;">
                {{-- Header --}}
                <div style="display:grid; grid-template-columns:3rem 1fr 7rem; gap:0.75rem; padding:0.75rem 1.25rem; border-bottom:1px solid rgba(255,255,255,0.06);">
                    <span style="color:#555; font-size:0.75rem; font-weight:600; text-transform:uppercase; letter-spacing:0.07em;">#</span>
                    <span style="color:#555; font-size:0.75rem; font-weight:600; text-transform:uppercase; letter-spacing:0.07em;">Restaurante</span>
                    <span style="color:#555; font-size:0.75rem; font-weight:600; text-transform:uppercase; letter-spacing:0.07em; text-align:right;">Votos</span>
                </div>

                @foreach($leaders as $rank => $leader)
                <a href="{{ route('restaurant.show', $leader->slug) }}"
                   style="
                       display:grid;
                       grid-template-columns:3rem 1fr 7rem;
                       gap:0.75rem;
                       align-items:center;
                       padding:0.875rem 1.25rem;
                       text-decoration:none;
                       border-bottom:{{ !$loop->last ? '1px solid rgba(255,255,255,0.04)' : 'none' }};
                       background:{{ $rank < 3 ? 'rgba(212,175,55,0.03)' : 'transparent' }};
                       transition:background 0.15s;
                   "
                   onmouseover="this.style.background='rgba(212,175,55,0.06)'"
                   onmouseout="this.style.background='{{ $rank < 3 ? 'rgba(212,175,55,0.03)' : 'transparent' }}'">

                    {{-- Rank --}}
                    <span style="
                        font-size:{{ $rank < 3 ? '1.25rem' : '0.95rem' }};
                        font-weight:{{ $rank < 3 ? '700' : '500' }};
                        color:{{ $rank === 0 ? '#D4AF37' : ($rank === 1 ? '#C0C0C0' : ($rank === 2 ? '#CD7F32' : '#444')) }};
                        text-align:center;
                    ">
                        {{ $rank < 3 ? ['🥇','🥈','🥉'][$rank] : $rank + 1 }}
                    </span>

                    {{-- Restaurant info --}}
                    <div style="display:flex; align-items:center; gap:0.75rem; min-width:0;">
                        <div style="width:36px; height:36px; border-radius:50%; overflow:hidden; flex-shrink:0; border:1px solid rgba(255,255,255,0.08);">
                            @if($leader->image)
                                <img src="{{ $leader->image }}" alt="{{ $leader->name }}" style="width:100%; height:100%; object-fit:cover;">
                            @else
                                <div style="width:100%; height:100%; background:#2A2A2A; display:flex; align-items:center; justify-content:center; font-size:1rem;">🍽️</div>
                            @endif
                        </div>
                        <div style="min-width:0;">
                            <div style="font-weight:600; color:#F5F5F5; font-size:0.88rem; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                                {{ $leader->name }}
                            </div>
                            <div style="color:#666; font-size:0.75rem; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                                {{ $leader->city }}{{ $leader->state ? ', '.$leader->state->code : '' }}
                            </div>
                        </div>
                    </div>

                    {{-- Votes --}}
                    <div style="text-align:right;">
                        <span style="color:{{ $rank < 3 ? '#D4AF37' : '#888' }}; font-size:0.88rem; font-weight:{{ $rank < 3 ? '700' : '500' }};">
                            {{ number_format($leader->total_votes) }}
                        </span>
                    </div>
                </a>
                @endforeach
            </div>

        @endif

        {{-- ─── CLAIM CTA ─── --}}
        <div style="margin-top:3rem; border:1px solid rgba(212,175,55,0.25); border-radius:1rem; padding:2rem 1.5rem; text-align:center;">
            <div style="font-size:1.75rem; margin-bottom:0.75rem;">🍽️</div>
            <h3 style="font-family:'Playfair Display',serif; font-size:1.2rem; font-weight:700; color:#F5F5F5; margin:0 0 0.5rem;">
                ¿Tu restaurante no está aquí?
            </h3>
            <p style="color:#888; font-size:0.9rem; margin:0 0 1.25rem;">
                Reclama tu perfil y empieza a recibir votos de la comunidad.
            </p>
            <a href="{{ route('claim.restaurant') }}"
               style="display:inline-block; padding:0.75rem 2rem; background:transparent; color:#D4AF37; font-weight:700; border-radius:0.75rem; text-decoration:none; font-size:0.95rem; border:2px solid #D4AF37; transition:all 0.2s;"
               onmouseover="this.style.background='#D4AF37';this.style.color='#0B0B0B'"
               onmouseout="this.style.background='transparent';this.style.color='#D4AF37'">
                Reclamar mi restaurante →
            </a>
        </div>

    </div>
</div>
