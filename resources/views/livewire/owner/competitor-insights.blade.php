<div style="background:#0B0B0B; min-height:100vh; padding:1.5rem; font-family:'Poppins',sans-serif; color:#F5F5F5;">

    @php
        $insights     = $this->insights;
        $restaurant   = $this->restaurant;
        $competitors  = $insights['competitors'];
        $rank         = $insights['rank'];
        $totalInCity  = $insights['total_in_city'];
        $percentile   = $insights['percentile'];
        $ratingGap    = $insights['rating_gap'];
        $reviewGap    = $insights['review_gap'];
        $leader       = $insights['leader'];
        $city         = $insights['city'] ?? $restaurant->city;
        $isLeader     = $rank === 1 && $totalInCity > 1;
        $alone        = $totalInCity <= 1;
    @endphp

    {{-- ─── PAGE HEADER ─── --}}
    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:1.5rem; flex-wrap:wrap; gap:0.75rem;">
        <div>
            <h1 style="font-family:'Playfair Display',serif; font-size:1.6rem; font-weight:700; color:#D4AF37; margin:0 0 0.2rem;">
                Análisis de Competidores
            </h1>
            <p style="color:#9CA3AF; font-size:0.875rem; margin:0;">{{ $restaurant->name }} · {{ $city }}</p>
        </div>
        <button
            wire:click="refreshData"
            wire:loading.attr="disabled"
            style="background:#1A1A1A; border:1px solid #2A2A2A; color:#D4AF37; font-size:0.8rem; padding:0.5rem 1rem; border-radius:0.5rem; cursor:pointer; display:flex; align-items:center; gap:0.4rem; transition:border-color .2s;"
            onmouseover="this.style.borderColor='#D4AF37'" onmouseout="this.style.borderColor='#2A2A2A'">
            <span wire:loading.remove>&#8635; Actualizar</span>
            <span wire:loading>Actualizando…</span>
        </button>
    </div>

    @if($alone)
        {{-- ─── EMPTY STATE ─── --}}
        <div style="background:#1A1A1A; border:1px solid #2A2A2A; border-radius:1rem; padding:3rem; text-align:center;">
            <div style="font-size:3rem; margin-bottom:1rem;">🏅</div>
            <h2 style="font-size:1.25rem; font-weight:600; color:#D4AF37; margin:0 0 0.5rem;">¡Eres el único disponible en FAMER!</h2>
            <p style="color:#9CA3AF; max-width:28rem; margin:0 auto;">No hay suficientes restaurantes en tu ciudad para comparar. Tienes ventaja de pionero — ¡aprovéchala!</p>
        </div>
    @else

        {{-- ─── RANK CARD ─── --}}
        <div style="background:linear-gradient(135deg,#1A1A1A 0%,#111 100%); border:1px solid #D4AF37; border-radius:1rem; padding:1.75rem; margin-bottom:1.25rem; position:relative; overflow:hidden;">
            {{-- Decorative glow --}}
            <div style="position:absolute; top:-40px; right:-40px; width:180px; height:180px; background:radial-gradient(circle,rgba(212,175,55,.15) 0%,transparent 70%); pointer-events:none;"></div>

            <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:1rem;">
                <div>
                    <p style="color:#9CA3AF; font-size:0.8rem; text-transform:uppercase; letter-spacing:.08em; margin:0 0 .3rem;">Tu posición en {{ $city }}</p>
                    <div style="display:flex; align-items:baseline; gap:.5rem;">
                        <span style="font-family:'Playfair Display',serif; font-size:4rem; font-weight:700; color:#D4AF37; line-height:1;">#{{ $rank }}</span>
                        <span style="color:#9CA3AF; font-size:1rem;">de {{ $totalInCity }}</span>
                    </div>
                    @if($isLeader)
                        <span style="display:inline-flex; align-items:center; gap:.3rem; background:rgba(212,175,55,.15); border:1px solid rgba(212,175,55,.4); color:#D4AF37; font-size:0.75rem; font-weight:600; padding:.25rem .75rem; border-radius:999px; margin-top:.5rem;">
                            &#127942; Líder de la ciudad
                        </span>
                    @endif
                </div>

                <div style="flex:1; min-width:200px; max-width:320px;">
                    <div style="display:flex; justify-content:space-between; margin-bottom:.4rem;">
                        <span style="color:#9CA3AF; font-size:0.75rem;">Superas al</span>
                        <span style="color:#D4AF37; font-size:0.75rem; font-weight:600;">{{ $percentile }}% de restaurantes</span>
                    </div>
                    {{-- Percentile bar --}}
                    <div style="background:#2A2A2A; border-radius:999px; height:10px; overflow:hidden;">
                        <div style="height:100%; width:{{ $percentile }}%; background:linear-gradient(90deg,#B8922F,#D4AF37); border-radius:999px; transition:width .6s ease;"></div>
                    </div>
                    <p style="color:#6B7280; font-size:0.7rem; margin:.4rem 0 0;">En comparación con {{ $totalInCity - 1 }} restaurantes de {{ $city }}</p>
                </div>
            </div>
        </div>

        {{-- ─── COMPARISON TABLE ─── --}}
        <div style="background:#1A1A1A; border:1px solid #2A2A2A; border-radius:1rem; margin-bottom:1.25rem; overflow:hidden;">
            <div style="padding:1rem 1.5rem; border-bottom:1px solid #2A2A2A;">
                <h2 style="font-size:0.95rem; font-weight:600; color:#F5F5F5; margin:0;">Top Competidores en {{ $city }}</h2>
            </div>

            <div style="overflow-x:auto;">
                <table style="width:100%; border-collapse:collapse; font-size:0.85rem;">
                    <thead>
                        <tr style="background:#111;">
                            <th style="text-align:left; padding:.75rem 1.5rem; color:#6B7280; font-weight:500; font-size:.75rem; text-transform:uppercase; letter-spacing:.06em;">#</th>
                            <th style="text-align:left; padding:.75rem 1rem; color:#6B7280; font-weight:500; font-size:.75rem; text-transform:uppercase; letter-spacing:.06em;">Restaurante</th>
                            <th style="text-align:center; padding:.75rem 1rem; color:#6B7280; font-weight:500; font-size:.75rem; text-transform:uppercase; letter-spacing:.06em;">Calificación</th>
                            <th style="text-align:center; padding:.75rem 1rem; color:#6B7280; font-weight:500; font-size:.75rem; text-transform:uppercase; letter-spacing:.06em;">Reseñas</th>
                            <th style="text-align:center; padding:.75rem 1.5rem; color:#6B7280; font-weight:500; font-size:.75rem; text-transform:uppercase; letter-spacing:.06em;">Votos FAMER</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- Your restaurant row --}}
                        @php
                            $myTotalVotes = \App\Models\RestaurantVote::where('restaurant_id', $restaurant->id)->count();
                        @endphp
                        <tr style="border-left:3px solid #D4AF37; background:rgba(212,175,55,.06);">
                            <td style="padding:.85rem 1.5rem; color:#D4AF37; font-weight:700;">{{ $rank }}</td>
                            <td style="padding:.85rem 1rem;">
                                <div style="font-weight:600; color:#F5F5F5;">{{ $restaurant->name }}</div>
                                <div style="color:#D4AF37; font-size:.7rem; font-weight:500;">TÚ</div>
                            </td>
                            <td style="padding:.85rem 1rem; text-align:center;">
                                <span style="color:#D4AF37; font-weight:700;">
                                    {{ number_format((float)($restaurant->average_rating ?? 0), 1) }}
                                </span>
                                <span style="color:#9CA3AF; font-size:.75rem;"> / 5</span>
                            </td>
                            <td style="padding:.85rem 1rem; text-align:center; color:#F5F5F5;">
                                {{ number_format($restaurant->total_reviews ?? 0) }}
                            </td>
                            <td style="padding:.85rem 1.5rem; text-align:center; color:#F5F5F5;">
                                {{ number_format($myTotalVotes) }}
                            </td>
                        </tr>

                        {{-- Competitor rows --}}
                        @foreach($competitors as $index => $comp)
                            @php
                                $compRank = $comp->average_rating > ($restaurant->average_rating ?? 0) ? $index + 1 : $index + ($rank <= $index + 1 ? 2 : 1);
                            @endphp
                            <tr style="border-left:3px solid transparent; border-bottom:1px solid #222; background:{{ $loop->even ? '#111' : '#1A1A1A' }};">
                                <td style="padding:.8rem 1.5rem; color:#6B7280;">
                                    {{ $comp->average_rating > ($restaurant->average_rating ?? 0) ? $loop->iteration : $loop->iteration + 1 }}
                                </td>
                                <td style="padding:.8rem 1rem;">
                                    <div style="color:#D1D5DB; font-weight:500;">{{ $comp->name }}</div>
                                    @if($comp->address)
                                        <div style="color:#6B7280; font-size:.72rem;">{{ Str::limit($comp->address, 40) }}</div>
                                    @endif
                                </td>
                                <td style="padding:.8rem 1rem; text-align:center;">
                                    <span style="color:{{ $comp->average_rating >= ($restaurant->average_rating ?? 0) ? '#EF4444' : '#22C55E' }}; font-weight:600;">
                                        {{ number_format((float)($comp->average_rating ?? 0), 1) }}
                                    </span>
                                    <span style="color:#6B7280; font-size:.75rem;"> / 5</span>
                                </td>
                                <td style="padding:.8rem 1rem; text-align:center; color:#9CA3AF;">
                                    {{ number_format($comp->total_reviews ?? 0) }}
                                </td>
                                <td style="padding:.8rem 1.5rem; text-align:center; color:#9CA3AF;">
                                    {{ number_format($comp->total_votes_count ?? 0) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- ─── GAPS SECTION ─── --}}
        <div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(260px,1fr)); gap:1rem; margin-bottom:1.25rem;">

            {{-- Rating gap --}}
            <div style="background:#1A1A1A; border:1px solid #2A2A2A; border-radius:.875rem; padding:1.25rem;">
                <div style="display:flex; align-items:center; gap:.5rem; margin-bottom:.75rem;">
                    <span style="font-size:1.25rem;">⭐</span>
                    <span style="color:#9CA3AF; font-size:.8rem; text-transform:uppercase; letter-spacing:.06em;">Brecha de Calificación</span>
                </div>
                @if($isLeader)
                    <p style="font-size:1.5rem; font-weight:700; color:#22C55E; margin:0 0 .25rem;">¡Eres el líder!</p>
                    <p style="color:#6B7280; font-size:.8rem; margin:0;">Tienes la calificación más alta en {{ $city }}. &#127942;</p>
                @elseif($ratingGap < 0)
                    <p style="font-size:1.5rem; font-weight:700; color:#F59E0B; margin:0 0 .25rem;">
                        {{ number_format(abs($ratingGap), 1) }} pts abajo
                    </p>
                    <p style="color:#9CA3AF; font-size:.8rem; margin:0;">
                        Estás {{ number_format(abs($ratingGap), 1) }} puntos por debajo del líder
                        @if($leader) ({{ $leader->name }}, {{ number_format($leader->average_rating ?? 0, 1) }}★) @endif
                    </p>
                @else
                    <p style="font-size:1.5rem; font-weight:700; color:#22C55E; margin:0 0 .25rem;">
                        +{{ number_format($ratingGap, 1) }} pts arriba
                    </p>
                    <p style="color:#9CA3AF; font-size:.8rem; margin:0;">Superas al líder de la competencia</p>
                @endif
            </div>

            {{-- Review gap --}}
            <div style="background:#1A1A1A; border:1px solid #2A2A2A; border-radius:.875rem; padding:1.25rem;">
                <div style="display:flex; align-items:center; gap:.5rem; margin-bottom:.75rem;">
                    <span style="font-size:1.25rem;">📝</span>
                    <span style="color:#9CA3AF; font-size:.8rem; text-transform:uppercase; letter-spacing:.06em;">Brecha de Reseñas</span>
                </div>
                @if($reviewGap >= 0 && $rank === 1)
                    <p style="font-size:1.5rem; font-weight:700; color:#22C55E; margin:0 0 .25rem;">
                        +{{ number_format($reviewGap) }} reseñas
                    </p>
                    <p style="color:#9CA3AF; font-size:.8rem; margin:0;">Tienes más reseñas que el resto en tu ciudad</p>
                @elseif($reviewGap < 0)
                    @php $absGap = abs($reviewGap); $leaderRevs = $leader?->total_reviews ?? 0; @endphp
                    <p style="font-size:1.5rem; font-weight:700; color:#F59E0B; margin:0 0 .25rem;">
                        {{ number_format($absGap) }} reseñas menos
                    </p>
                    <p style="color:#9CA3AF; font-size:.8rem; margin:0;">
                        El líder tiene {{ number_format($absGap) }} reseñas más — activa recordatorios SMS para cerrar la brecha
                    </p>
                @else
                    <p style="font-size:1.5rem; font-weight:700; color:#9CA3AF; margin:0 0 .25rem;">Sin datos</p>
                    <p style="color:#6B7280; font-size:.8rem; margin:0;">Empieza a recopilar reseñas para comparar</p>
                @endif
            </div>

        </div>

        {{-- ─── ACTIONABLE RECOMMENDATIONS ─── --}}
        @if(count($insights['recommendations']) > 0)
        <div style="background:#1A1A1A; border:1px solid #2A2A2A; border-radius:1rem; padding:1.5rem; margin-bottom:1.25rem;">
            <h2 style="font-size:.95rem; font-weight:600; color:#F5F5F5; margin:0 0 1.25rem; display:flex; align-items:center; gap:.5rem;">
                <span style="color:#D4AF37;">&#128161;</span> Recomendaciones de Acción
            </h2>

            <div style="display:flex; flex-direction:column; gap:.875rem;">
                @foreach($insights['recommendations'] as $i => $rec)
                <div style="display:flex; align-items:flex-start; gap:1rem; background:#111; border:1px solid #2A2A2A; border-radius:.75rem; padding:1rem;">
                    <div style="flex-shrink:0; width:2rem; height:2rem; background:rgba(212,175,55,.15); border:1px solid rgba(212,175,55,.3); border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:.8rem; font-weight:700; color:#D4AF37;">
                        {{ $i + 1 }}
                    </div>
                    <div style="flex:1; min-width:0;">
                        <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:.75rem; flex-wrap:wrap;">
                            <p style="color:#F5F5F5; font-size:.875rem; margin:0; line-height:1.5;">
                                {{ $rec['icon'] ?? '' }} {{ $rec['text'] }}
                            </p>
                            @if(isset($rec['cta']) && isset($rec['cta_url']))
                            <a href="{{ $rec['cta_url'] }}"
                               style="flex-shrink:0; background:#D4AF37; color:#0B0B0B; font-size:.75rem; font-weight:700; padding:.35rem .875rem; border-radius:.375rem; text-decoration:none; white-space:nowrap; display:inline-block; transition:opacity .2s;"
                               onmouseover="this.style.opacity='.85'" onmouseout="this.style.opacity='1'">
                                {{ $rec['cta'] }}
                            </a>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

    @endif

    {{-- ─── FOOTER NOTE ─── --}}
    <div style="text-align:center; padding-top:.75rem; border-top:1px solid #1F1F1F; margin-top:.5rem;">
        <p style="color:#4B5563; font-size:.72rem; margin:0;">
            &#128338; Actualizado en tiempo real &nbsp;·&nbsp; Datos basados en restaurantes aprobados en FAMER &nbsp;·&nbsp;
            <button wire:click="refreshData" style="background:none; border:none; color:#D4AF37; font-size:.72rem; cursor:pointer; padding:0; text-decoration:underline;">Forzar actualización</button>
        </p>
    </div>

</div>
