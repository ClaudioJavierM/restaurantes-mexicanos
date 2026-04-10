<div style="background:#0B0B0B; min-height:100vh; font-family:'Poppins', sans-serif; color:#E5E5E5; padding:0 0 80px;">

    {{-- Google Fonts --}}
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&family=Playfair+Display:wght@700;800&display=swap" rel="stylesheet">

    {{-- ─── HERO ─── --}}
    <div style="background:linear-gradient(180deg,#111111 0%,#0B0B0B 100%); padding:60px 24px 40px; text-align:center; border-bottom:1px solid #2A2A2A;">
        <p style="font-family:'Poppins',sans-serif; font-size:12px; font-weight:600; letter-spacing:3px; color:#D4AF37; text-transform:uppercase; margin:0 0 16px;">FAMER — Restaurantes Famosos</p>
        <h1 style="font-family:'Playfair Display', serif; font-size:clamp(2rem,5vw,3.5rem); font-weight:800; color:#D4AF37; margin:0 0 16px; line-height:1.1;">
            Comparar Restaurantes
        </h1>
        <p style="color:#999; font-size:1rem; margin:0; max-width:500px; margin-left:auto; margin-right:auto; line-height:1.6;">
            Elige dos restaurantes y compáralos lado a lado: calificaciones, votos, horarios y más.
        </p>
    </div>

    {{-- ─── SEARCH SECTION ─── --}}
    <div style="max-width:900px; margin:0 auto; padding:40px 24px 0;">
        <div style="display:grid; grid-template-columns:1fr auto 1fr; gap:16px; align-items:start;">

            {{-- Slot 1 --}}
            <div>
                <p style="font-family:'Playfair Display',serif; color:#D4AF37; font-size:1rem; font-weight:700; margin:0 0 10px; letter-spacing:1px;">
                    🍽 Restaurante 1
                </p>
                <div style="position:relative;">
                    @if($restaurant1Id)
                        <div style="display:flex; align-items:center; gap:10px; background:#1A1A1A; border:1px solid #D4AF37; border-radius:12px; padding:12px 16px;">
                            <span style="flex:1; font-size:0.95rem; color:#F5F5F5; font-weight:500;">{{ $search1 }}</span>
                            <button wire:click="clearSlot(1)"
                                    style="background:none; border:none; color:#888; cursor:pointer; font-size:18px; line-height:1; padding:0; transition:color .2s;"
                                    onmouseover="this.style.color='#D4AF37'" onmouseout="this.style.color='#888'"
                                    title="Quitar selección">✕</button>
                        </div>
                    @else
                        <input
                            wire:model.live.debounce.300ms="search1"
                            type="text"
                            placeholder="Buscar restaurante…"
                            style="width:100%; box-sizing:border-box; background:#1A1A1A; border:1px solid #2A2A2A; border-radius:12px; padding:12px 16px; color:#F5F5F5; font-size:0.95rem; font-family:'Poppins',sans-serif; outline:none; transition:border-color .2s;"
                            onfocus="this.style.borderColor='#D4AF37'" onblur="this.style.borderColor='#2A2A2A'"
                        >

                        @if(!empty($results1))
                            <div style="position:absolute; z-index:50; top:100%; left:0; right:0; background:#1A1A1A; border:1px solid #2A2A2A; border-top:none; border-radius:0 0 12px 12px; overflow:hidden; box-shadow:0 8px 32px rgba(0,0,0,.6);">
                                @foreach($results1 as $r)
                                    <button
                                        wire:click="selectRestaurant(1, {{ $r['id'] }}, @js($r['name']))"
                                        style="display:block; width:100%; text-align:left; background:none; border:none; border-bottom:1px solid #2A2A2A; padding:10px 16px; color:#E5E5E5; font-family:'Poppins',sans-serif; font-size:0.88rem; cursor:pointer; transition:background .15s;"
                                        onmouseover="this.style.background='rgba(212,175,55,0.08)'" onmouseout="this.style.background='none'"
                                    >
                                        <span style="color:#F5F5F5; font-weight:500;">{{ $r['name'] }}</span>
                                        @if($r['city'])
                                            <span style="color:#666; margin-left:8px; font-size:0.8rem;">{{ $r['city'] }}</span>
                                        @endif
                                    </button>
                                @endforeach
                            </div>
                        @endif
                    @endif
                </div>
            </div>

            {{-- VS divider --}}
            <div style="display:flex; align-items:center; justify-content:center; padding-top:34px;">
                <div style="background:#1A1A1A; border:1px solid #2A2A2A; border-radius:50%; width:44px; height:44px; display:flex; align-items:center; justify-content:center;">
                    <span style="font-family:'Playfair Display',serif; color:#D4AF37; font-weight:800; font-size:0.9rem;">VS</span>
                </div>
            </div>

            {{-- Slot 2 --}}
            <div>
                <p style="font-family:'Playfair Display',serif; color:#D4AF37; font-size:1rem; font-weight:700; margin:0 0 10px; letter-spacing:1px;">
                    🍽 Restaurante 2
                </p>
                <div style="position:relative;">
                    @if($restaurant2Id)
                        <div style="display:flex; align-items:center; gap:10px; background:#1A1A1A; border:1px solid #D4AF37; border-radius:12px; padding:12px 16px;">
                            <span style="flex:1; font-size:0.95rem; color:#F5F5F5; font-weight:500;">{{ $search2 }}</span>
                            <button wire:click="clearSlot(2)"
                                    style="background:none; border:none; color:#888; cursor:pointer; font-size:18px; line-height:1; padding:0; transition:color .2s;"
                                    onmouseover="this.style.color='#D4AF37'" onmouseout="this.style.color='#888'"
                                    title="Quitar selección">✕</button>
                        </div>
                    @else
                        <input
                            wire:model.live.debounce.300ms="search2"
                            type="text"
                            placeholder="Buscar restaurante…"
                            style="width:100%; box-sizing:border-box; background:#1A1A1A; border:1px solid #2A2A2A; border-radius:12px; padding:12px 16px; color:#F5F5F5; font-size:0.95rem; font-family:'Poppins',sans-serif; outline:none; transition:border-color .2s;"
                            onfocus="this.style.borderColor='#D4AF37'" onblur="this.style.borderColor='#2A2A2A'"
                        >

                        @if(!empty($results2))
                            <div style="position:absolute; z-index:50; top:100%; left:0; right:0; background:#1A1A1A; border:1px solid #2A2A2A; border-top:none; border-radius:0 0 12px 12px; overflow:hidden; box-shadow:0 8px 32px rgba(0,0,0,.6);">
                                @foreach($results2 as $r)
                                    <button
                                        wire:click="selectRestaurant(2, {{ $r['id'] }}, @js($r['name']))"
                                        style="display:block; width:100%; text-align:left; background:none; border:none; border-bottom:1px solid #2A2A2A; padding:10px 16px; color:#E5E5E5; font-family:'Poppins',sans-serif; font-size:0.88rem; cursor:pointer; transition:background .15s;"
                                        onmouseover="this.style.background='rgba(212,175,55,0.08)'" onmouseout="this.style.background='none'"
                                    >
                                        <span style="color:#F5F5F5; font-weight:500;">{{ $r['name'] }}</span>
                                        @if($r['city'])
                                            <span style="color:#666; margin-left:8px; font-size:0.8rem;">{{ $r['city'] }}</span>
                                        @endif
                                    </button>
                                @endforeach
                            </div>
                        @endif
                    @endif
                </div>
            </div>

        </div>{{-- /grid --}}
    </div>{{-- /search section --}}

    {{-- ─── COMPARISON TABLE ─── --}}
    @if($r1 && $r2)
        @php
            $today = strtolower(now()->format('l')); // monday, tuesday…

            $hours1 = null;
            $hours2 = null;
            try { $hours1 = is_array($r1->hours) ? $r1->hours : json_decode($r1->hours, true); } catch(\Exception $e) {}
            try { $hours2 = is_array($r2->hours) ? $r2->hours : json_decode($r2->hours, true); } catch(\Exception $e) {}

            $todayHours1 = ($hours1 && isset($hours1[$today])) ? $hours1[$today] : 'No disponible';
            $todayHours2 = ($hours2 && isset($hours2[$today])) ? $hours2[$today] : 'No disponible';

            $ratingWinner = ($r1->average_rating ?? 0) >= ($r2->average_rating ?? 0) ? 1 : 2;
            $reviewWinner = ($r1->total_reviews ?? 0) >= ($r2->total_reviews ?? 0) ? 1 : 2;
            $voteWinner   = $votes1 >= $votes2 ? 1 : 2;

            $goldCell = 'background:rgba(212,175,55,0.1); border-left:3px solid #D4AF37;';
            $normalCell = '';

            function renderStars(float $rating): string {
                $full  = floor($rating);
                $half  = ($rating - $full) >= 0.5 ? 1 : 0;
                $empty = 5 - $full - $half;
                return str_repeat('★', $full) . str_repeat('½', $half) . str_repeat('☆', $empty);
            }
        @endphp

        <div style="max-width:900px; margin:40px auto 0; padding:0 24px;">

            {{-- Table header with restaurant names --}}
            <div style="display:grid; grid-template-columns:200px 1fr 1fr; background:#1A1A1A; border:1px solid #2A2A2A; border-radius:16px 16px 0 0; overflow:hidden;">
                <div style="padding:20px 16px; border-right:1px solid #2A2A2A; background:#111;"></div>
                <div style="padding:20px 16px; border-right:1px solid #2A2A2A; text-align:center;">
                    <p style="font-family:'Playfair Display',serif; color:#D4AF37; font-size:1.05rem; font-weight:700; margin:0;">{{ $r1->name }}</p>
                </div>
                <div style="padding:20px 16px; text-align:center;">
                    <p style="font-family:'Playfair Display',serif; color:#D4AF37; font-size:1.05rem; font-weight:700; margin:0;">{{ $r2->name }}</p>
                </div>
            </div>

            {{-- PHOTO ROW --}}
            <div style="display:grid; grid-template-columns:200px 1fr 1fr; border:1px solid #2A2A2A; border-top:none; background:#1A1A1A;">
                <div style="padding:16px; border-right:1px solid #2A2A2A; display:flex; align-items:center; background:#111;">
                    <span style="color:#999; font-size:0.85rem; font-weight:500; text-transform:uppercase; letter-spacing:1px;">Foto</span>
                </div>
                <div style="padding:16px; border-right:1px solid #2A2A2A; text-align:center;">
                    @if($r1->image)
                        <img src="{{ $r1->image }}" alt="{{ $r1->name }}"
                             style="width:100%; max-width:260px; height:160px; object-fit:cover; border-radius:10px; border:1px solid #2A2A2A;">
                    @else
                        <div style="width:100%; max-width:260px; height:160px; background:#222; border-radius:10px; display:flex; align-items:center; justify-content:center; margin:0 auto;">
                            <span style="color:#444; font-size:2rem;">🍽</span>
                        </div>
                    @endif
                </div>
                <div style="padding:16px; text-align:center;">
                    @if($r2->image)
                        <img src="{{ $r2->image }}" alt="{{ $r2->name }}"
                             style="width:100%; max-width:260px; height:160px; object-fit:cover; border-radius:10px; border:1px solid #2A2A2A;">
                    @else
                        <div style="width:100%; max-width:260px; height:160px; background:#222; border-radius:10px; display:flex; align-items:center; justify-content:center; margin:0 auto;">
                            <span style="color:#444; font-size:2rem;">🍽</span>
                        </div>
                    @endif
                </div>
            </div>

            {{-- LOCATION ROW --}}
            <div style="display:grid; grid-template-columns:200px 1fr 1fr; border:1px solid #2A2A2A; border-top:none; background:#1A1A1A;">
                <div style="padding:16px; border-right:1px solid #2A2A2A; display:flex; align-items:center; background:#111;">
                    <span style="color:#999; font-size:0.85rem; font-weight:500; text-transform:uppercase; letter-spacing:1px;">Ubicación</span>
                </div>
                <div style="padding:16px; border-right:1px solid #2A2A2A; text-align:center;">
                    <p style="color:#F5F5F5; margin:0; font-size:0.9rem;">
                        {{ $r1->city ?? '—' }}@if($r1->state), <span style="color:#D4AF37;">{{ $r1->state->code }}</span>@endif
                    </p>
                    @if($r1->address)
                        <p style="color:#666; margin:4px 0 0; font-size:0.78rem;">{{ Str::limit($r1->address, 50) }}</p>
                    @endif
                </div>
                <div style="padding:16px; text-align:center;">
                    <p style="color:#F5F5F5; margin:0; font-size:0.9rem;">
                        {{ $r2->city ?? '—' }}@if($r2->state), <span style="color:#D4AF37;">{{ $r2->state->code }}</span>@endif
                    </p>
                    @if($r2->address)
                        <p style="color:#666; margin:4px 0 0; font-size:0.78rem;">{{ Str::limit($r2->address, 50) }}</p>
                    @endif
                </div>
            </div>

            {{-- RATING ROW --}}
            <div style="display:grid; grid-template-columns:200px 1fr 1fr; border:1px solid #2A2A2A; border-top:none; background:#1A1A1A;">
                <div style="padding:16px; border-right:1px solid #2A2A2A; display:flex; align-items:center; background:#111;">
                    <span style="color:#999; font-size:0.85rem; font-weight:500; text-transform:uppercase; letter-spacing:1px;">Calificación</span>
                </div>
                <div style="padding:16px; border-right:1px solid #2A2A2A; text-align:center; {{ $ratingWinner === 1 ? $goldCell : $normalCell }}">
                    <p style="color:#D4AF37; font-size:1.1rem; margin:0; letter-spacing:2px;">{{ renderStars((float)($r1->average_rating ?? 0)) }}</p>
                    <p style="color:#F5F5F5; font-weight:600; margin:4px 0 0; font-size:1.1rem;">{{ number_format($r1->average_rating ?? 0, 1) }}</p>
                    @if($ratingWinner === 1)
                        <span style="background:#D4AF37; color:#0B0B0B; font-size:0.7rem; font-weight:700; padding:2px 8px; border-radius:20px; text-transform:uppercase; letter-spacing:1px; display:inline-block; margin-top:4px;">Ganador</span>
                    @endif
                </div>
                <div style="padding:16px; text-align:center; {{ $ratingWinner === 2 ? $goldCell : $normalCell }}">
                    <p style="color:#D4AF37; font-size:1.1rem; margin:0; letter-spacing:2px;">{{ renderStars((float)($r2->average_rating ?? 0)) }}</p>
                    <p style="color:#F5F5F5; font-weight:600; margin:4px 0 0; font-size:1.1rem;">{{ number_format($r2->average_rating ?? 0, 1) }}</p>
                    @if($ratingWinner === 2)
                        <span style="background:#D4AF37; color:#0B0B0B; font-size:0.7rem; font-weight:700; padding:2px 8px; border-radius:20px; text-transform:uppercase; letter-spacing:1px; display:inline-block; margin-top:4px;">Ganador</span>
                    @endif
                </div>
            </div>

            {{-- REVIEWS ROW --}}
            <div style="display:grid; grid-template-columns:200px 1fr 1fr; border:1px solid #2A2A2A; border-top:none; background:#1A1A1A;">
                <div style="padding:16px; border-right:1px solid #2A2A2A; display:flex; align-items:center; background:#111;">
                    <span style="color:#999; font-size:0.85rem; font-weight:500; text-transform:uppercase; letter-spacing:1px;">Reseñas</span>
                </div>
                <div style="padding:16px; border-right:1px solid #2A2A2A; text-align:center; {{ $reviewWinner === 1 ? $goldCell : $normalCell }}">
                    <p style="color:#F5F5F5; font-weight:600; margin:0; font-size:1.1rem;">{{ number_format($r1->total_reviews ?? 0) }}</p>
                    <p style="color:#666; margin:2px 0 0; font-size:0.8rem;">reseñas</p>
                    @if($reviewWinner === 1)
                        <span style="background:#D4AF37; color:#0B0B0B; font-size:0.7rem; font-weight:700; padding:2px 8px; border-radius:20px; text-transform:uppercase; letter-spacing:1px; display:inline-block; margin-top:4px;">Ganador</span>
                    @endif
                </div>
                <div style="padding:16px; text-align:center; {{ $reviewWinner === 2 ? $goldCell : $normalCell }}">
                    <p style="color:#F5F5F5; font-weight:600; margin:0; font-size:1.1rem;">{{ number_format($r2->total_reviews ?? 0) }}</p>
                    <p style="color:#666; margin:2px 0 0; font-size:0.8rem;">reseñas</p>
                    @if($reviewWinner === 2)
                        <span style="background:#D4AF37; color:#0B0B0B; font-size:0.7rem; font-weight:700; padding:2px 8px; border-radius:20px; text-transform:uppercase; letter-spacing:1px; display:inline-block; margin-top:4px;">Ganador</span>
                    @endif
                </div>
            </div>

            {{-- VOTOS ROW --}}
            <div style="display:grid; grid-template-columns:200px 1fr 1fr; border:1px solid #2A2A2A; border-top:none; background:#1A1A1A;">
                <div style="padding:16px; border-right:1px solid #2A2A2A; display:flex; align-items:center; background:#111;">
                    <span style="color:#999; font-size:0.85rem; font-weight:500; text-transform:uppercase; letter-spacing:1px;">Votos este mes</span>
                </div>
                <div style="padding:16px; border-right:1px solid #2A2A2A; text-align:center; {{ $voteWinner === 1 ? $goldCell : $normalCell }}">
                    <p style="color:#F5F5F5; font-weight:600; margin:0; font-size:1.1rem;">{{ number_format($votes1) }}</p>
                    <p style="color:#666; margin:2px 0 0; font-size:0.8rem;">votos</p>
                    @if($voteWinner === 1 && $votes1 > 0)
                        <span style="background:#D4AF37; color:#0B0B0B; font-size:0.7rem; font-weight:700; padding:2px 8px; border-radius:20px; text-transform:uppercase; letter-spacing:1px; display:inline-block; margin-top:4px;">Ganador</span>
                    @endif
                </div>
                <div style="padding:16px; text-align:center; {{ $voteWinner === 2 ? $goldCell : $normalCell }}">
                    <p style="color:#F5F5F5; font-weight:600; margin:0; font-size:1.1rem;">{{ number_format($votes2) }}</p>
                    <p style="color:#666; margin:2px 0 0; font-size:0.8rem;">votos</p>
                    @if($voteWinner === 2 && $votes2 > 0)
                        <span style="background:#D4AF37; color:#0B0B0B; font-size:0.7rem; font-weight:700; padding:2px 8px; border-radius:20px; text-transform:uppercase; letter-spacing:1px; display:inline-block; margin-top:4px;">Ganador</span>
                    @endif
                </div>
            </div>

            {{-- PRICE ROW --}}
            <div style="display:grid; grid-template-columns:200px 1fr 1fr; border:1px solid #2A2A2A; border-top:none; background:#1A1A1A;">
                <div style="padding:16px; border-right:1px solid #2A2A2A; display:flex; align-items:center; background:#111;">
                    <span style="color:#999; font-size:0.85rem; font-weight:500; text-transform:uppercase; letter-spacing:1px;">Precio</span>
                </div>
                <div style="padding:16px; border-right:1px solid #2A2A2A; text-align:center;">
                    <span style="color:#D4AF37; font-weight:700; font-size:1.1rem;">{{ $r1->price_range ?? '—' }}</span>
                </div>
                <div style="padding:16px; text-align:center;">
                    <span style="color:#D4AF37; font-weight:700; font-size:1.1rem;">{{ $r2->price_range ?? '—' }}</span>
                </div>
            </div>

            {{-- PHONE ROW --}}
            <div style="display:grid; grid-template-columns:200px 1fr 1fr; border:1px solid #2A2A2A; border-top:none; background:#1A1A1A;">
                <div style="padding:16px; border-right:1px solid #2A2A2A; display:flex; align-items:center; background:#111;">
                    <span style="color:#999; font-size:0.85rem; font-weight:500; text-transform:uppercase; letter-spacing:1px;">Teléfono</span>
                </div>
                <div style="padding:16px; border-right:1px solid #2A2A2A; text-align:center;">
                    @if($r1->phone)
                        <a href="tel:{{ $r1->phone }}" style="color:#D4AF37; text-decoration:none; font-size:0.9rem;">{{ $r1->phone }}</a>
                    @else
                        <span style="color:#444;">—</span>
                    @endif
                </div>
                <div style="padding:16px; text-align:center;">
                    @if($r2->phone)
                        <a href="tel:{{ $r2->phone }}" style="color:#D4AF37; text-decoration:none; font-size:0.9rem;">{{ $r2->phone }}</a>
                    @else
                        <span style="color:#444;">—</span>
                    @endif
                </div>
            </div>

            {{-- WEBSITE ROW --}}
            <div style="display:grid; grid-template-columns:200px 1fr 1fr; border:1px solid #2A2A2A; border-top:none; background:#1A1A1A;">
                <div style="padding:16px; border-right:1px solid #2A2A2A; display:flex; align-items:center; background:#111;">
                    <span style="color:#999; font-size:0.85rem; font-weight:500; text-transform:uppercase; letter-spacing:1px;">Sitio Web</span>
                </div>
                <div style="padding:16px; border-right:1px solid #2A2A2A; text-align:center;">
                    @if($r1->website)
                        <a href="{{ $r1->website }}" target="_blank" rel="noopener"
                           style="color:#D4AF37; text-decoration:none; font-size:0.85rem; word-break:break-all;">
                            {{ Str::limit(preg_replace('#^https?://(www\.)?#', '', $r1->website), 30) }} ↗
                        </a>
                    @else
                        <span style="color:#444;">—</span>
                    @endif
                </div>
                <div style="padding:16px; text-align:center;">
                    @if($r2->website)
                        <a href="{{ $r2->website }}" target="_blank" rel="noopener"
                           style="color:#D4AF37; text-decoration:none; font-size:0.85rem; word-break:break-all;">
                            {{ Str::limit(preg_replace('#^https?://(www\.)?#', '', $r2->website), 30) }} ↗
                        </a>
                    @else
                        <span style="color:#444;">—</span>
                    @endif
                </div>
            </div>

            {{-- HOURS TODAY ROW --}}
            <div style="display:grid; grid-template-columns:200px 1fr 1fr; border:1px solid #2A2A2A; border-top:none; border-radius:0 0 16px 16px; overflow:hidden; background:#1A1A1A;">
                <div style="padding:16px; border-right:1px solid #2A2A2A; display:flex; align-items:center; background:#111;">
                    <span style="color:#999; font-size:0.85rem; font-weight:500; text-transform:uppercase; letter-spacing:1px;">Horario hoy</span>
                </div>
                <div style="padding:16px; border-right:1px solid #2A2A2A; text-align:center;">
                    <span style="color:{{ $todayHours1 !== 'No disponible' ? '#4ADE80' : '#666' }}; font-size:0.9rem;">
                        {{ $todayHours1 }}
                    </span>
                </div>
                <div style="padding:16px; text-align:center;">
                    <span style="color:{{ $todayHours2 !== 'No disponible' ? '#4ADE80' : '#666' }}; font-size:0.9rem;">
                        {{ $todayHours2 }}
                    </span>
                </div>
            </div>

        </div>{{-- /table --}}

        {{-- ─── SHARE BUTTON ─── --}}
        <div style="max-width:900px; margin:32px auto 0; padding:0 24px; text-align:center;"
             x-data="{ copied: false }">
            <button
                @click="
                    const url = window.location.origin + window.location.pathname + '?r1={{ $r1->slug }}&r2={{ $r2->slug }}';
                    navigator.clipboard.writeText(url).then(() => { copied = true; setTimeout(() => copied = false, 2500); });
                "
                style="background:#1A1A1A; border:1px solid #D4AF37; color:#D4AF37; font-family:'Poppins',sans-serif; font-size:0.9rem; font-weight:600; padding:12px 28px; border-radius:50px; cursor:pointer; transition:all .2s; letter-spacing:0.5px;"
                onmouseover="this.style.background='rgba(212,175,55,0.1)'" onmouseout="this.style.background='#1A1A1A'"
            >
                <span x-show="!copied">🔗 Compartir esta comparación</span>
                <span x-show="copied" x-cloak>✅ ¡Enlace copiado!</span>
            </button>

            <div style="margin-top:16px; display:flex; justify-content:center; gap:16px; flex-wrap:wrap;">
                <a href="/restaurante/{{ $r1->slug }}"
                   style="color:#888; font-size:0.8rem; text-decoration:none; transition:color .2s;"
                   onmouseover="this.style.color='#D4AF37'" onmouseout="this.style.color='#888'">
                    Ver perfil de {{ $r1->name }} →
                </a>
                <a href="/restaurante/{{ $r2->slug }}"
                   style="color:#888; font-size:0.8rem; text-decoration:none; transition:color .2s;"
                   onmouseover="this.style.color='#D4AF37'" onmouseout="this.style.color='#888'">
                    Ver perfil de {{ $r2->name }} →
                </a>
            </div>
        </div>

    @elseif($r1 && !$r2)
        {{-- ─── PROMPT: select second restaurant ─── --}}
        <div style="max-width:900px; margin:40px auto 0; padding:0 24px; text-align:center;">
            <div style="background:#1A1A1A; border:1px solid #2A2A2A; border-radius:16px; padding:40px 24px;">
                <div style="font-size:3rem; margin-bottom:16px;">👈</div>
                <p style="color:#D4AF37; font-family:'Playfair Display',serif; font-size:1.25rem; font-weight:700; margin:0 0 8px;">
                    ¡Bien! Ya tienes "{{ $r1->name }}"
                </p>
                <p style="color:#999; margin:0; font-size:0.9rem;">
                    Ahora selecciona el segundo restaurante para comenzar la comparación.
                </p>
            </div>
        </div>

    @else
        {{-- ─── DEFAULT: placeholder examples ─── --}}
        <div style="max-width:900px; margin:40px auto 0; padding:0 24px;">
            <div style="background:#1A1A1A; border:1px dashed #2A2A2A; border-radius:16px; padding:40px 24px; text-align:center;">
                <div style="font-size:3rem; margin-bottom:16px;">⚖️</div>
                <p style="color:#D4AF37; font-family:'Playfair Display',serif; font-size:1.25rem; font-weight:700; margin:0 0 12px;">
                    Selecciona dos restaurantes para comparar
                </p>
                <p style="color:#666; margin:0 0 24px; font-size:0.9rem; max-width:420px; margin-left:auto; margin-right:auto; line-height:1.7;">
                    Compara calificaciones, votos del mes, horarios, precios y más entre cualquier par de restaurantes de nuestra base de datos.
                </p>
                <div style="border-top:1px solid #2A2A2A; padding-top:20px; margin-top:4px;">
                    <p style="color:#555; font-size:0.8rem; margin:0 0 12px; text-transform:uppercase; letter-spacing:1.5px;">Ejemplos populares</p>
                    <div style="display:flex; justify-content:center; flex-wrap:wrap; gap:10px;">
                        <span style="background:#111; border:1px solid #2A2A2A; border-radius:8px; padding:8px 14px; color:#777; font-size:0.82rem; font-style:italic;">
                            Los Tacos de Manuel vs La Casita Mexicana
                        </span>
                        <span style="background:#111; border:1px solid #2A2A2A; border-radius:8px; padding:8px 14px; color:#777; font-size:0.82rem; font-style:italic;">
                            El Ranchero vs Antojitos del Sur
                        </span>
                    </div>
                </div>
            </div>
        </div>
    @endif

</div>
