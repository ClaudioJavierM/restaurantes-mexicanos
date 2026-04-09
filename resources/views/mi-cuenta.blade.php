@extends('layouts.app')
@section('title', 'Mi Cuenta — FAMER')

@php
    $user = auth()->user();
    $favoritesCount = $user->favorites()->count();
    try { $reviewsCount = $user->reviews()->count(); } catch(\Exception $e) { $reviewsCount = 0; }
    try { $checkInsCount = $user->checkIns()->count(); } catch(\Exception $e) { $checkInsCount = 0; }
    $recentFavorites = $user->favoriteRestaurants()->with('state')->latest('favorites.created_at')->take(4)->get();
    try { $recentReviews = $user->reviews()->with('restaurant')->latest()->take(3)->get(); } catch(\Exception $e) { $recentReviews = collect(); }
    try { $ordersCount = \App\Models\Order::where('user_id', $user->id)->count(); $recentOrders = \App\Models\Order::with('restaurant')->where('user_id', $user->id)->latest()->take(3)->get(); } catch(\Exception $e) { $ordersCount = 0; $recentOrders = collect(); }
    try {
        $votesThisMonth = \App\Models\RestaurantVote::where('user_id', $user->id)->where('year', now()->year)->where('month', now()->month)->with('restaurant')->get();
        $recentVotes = \App\Models\RestaurantVote::where('user_id', $user->id)->with('restaurant')->orderByDesc('year')->orderByDesc('month')->orderByDesc('created_at')->take(6)->get();
        $totalVotes = \App\Models\RestaurantVote::where('user_id', $user->id)->count();
    } catch(\Exception $e) { $votesThisMonth = collect(); $recentVotes = collect(); $totalVotes = 0; }
@endphp

@section('content')
<div style="background:#0B0B0B; min-height:100vh; padding-bottom:4rem;">

    {{-- ============================================================
         1. HERO / WELCOME HEADER
    ============================================================ --}}
    <div style="background:#1A1A1A; border-bottom:1px solid #2A2A2A; padding:2rem 1.5rem;">
        <div style="max-width:1100px; margin:0 auto; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:1rem;">
            <div>
                <h1 style="font-family:'Playfair Display',serif; font-size:2rem; font-weight:700; color:#D4AF37; margin:0 0 0.25rem 0; line-height:1.2;">
                    ¡Hola, {{ $user->name }}!
                </h1>
                <p style="font-family:'Poppins',sans-serif; font-size:0.95rem; color:#9CA3AF; margin:0;">
                    Tu cuenta en FAMER
                </p>
            </div>
            <div style="display:flex; align-items:center; gap:1.25rem; flex-wrap:wrap;">
                <a href="{{ route('mi-cuenta.perfil') }}"
                   style="font-family:'Poppins',sans-serif; font-size:0.85rem; font-weight:500; color:#D4AF37; text-decoration:none; border:1px solid rgba(212,175,55,0.35); border-radius:8px; padding:0.4rem 1rem; transition:background 0.2s;"
                   onmouseover="this.style.background='rgba(212,175,55,0.08)'" onmouseout="this.style.background='transparent'">
                    Editar perfil
                </a>
                <a href="{{ route('logout') }}"
                   onclick="event.preventDefault(); document.getElementById('logout-form-cuenta').submit();"
                   style="font-family:'Poppins',sans-serif; font-size:0.8rem; color:#6B7280; text-decoration:none; transition:color 0.2s;"
                   onmouseover="this.style.color='#9CA3AF'" onmouseout="this.style.color='#6B7280'">
                    Cerrar sesión
                </a>
            </div>
            <form id="logout-form-cuenta" action="{{ route('logout') }}" method="POST" style="display:none;">
                @csrf
            </form>
        </div>
    </div>

    <div style="max-width:1100px; margin:0 auto; padding:2rem 1.5rem;">

        {{-- ============================================================
             2. QUICK STATS ROW — 3 CARDS
        ============================================================ --}}
        <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(220px, 1fr)); gap:1rem; margin-bottom:2.5rem;">

            {{-- Favoritos --}}
            <div style="background:#1A1A1A; border:1px solid #2A2A2A; border-radius:16px; padding:1.5rem; text-align:center;">
                <div style="font-size:2.25rem; font-weight:700; font-family:'Playfair Display',serif; color:#D4AF37; line-height:1;">
                    {{ $favoritesCount }}
                </div>
                <div style="font-family:'Poppins',sans-serif; font-size:0.85rem; color:#9CA3AF; margin-top:0.5rem;">
                    Favoritos guardados
                </div>
            </div>

            {{-- Reseñas --}}
            <div style="background:#1A1A1A; border:1px solid #2A2A2A; border-radius:16px; padding:1.5rem; text-align:center;">
                <div style="font-size:2.25rem; font-weight:700; font-family:'Playfair Display',serif; color:#D4AF37; line-height:1;">
                    {{ $reviewsCount }}
                </div>
                <div style="font-family:'Poppins',sans-serif; font-size:0.85rem; color:#9CA3AF; margin-top:0.5rem;">
                    Reseñas escritas
                </div>
            </div>

            {{-- Check-ins --}}
            <div style="background:#1A1A1A; border:1px solid #2A2A2A; border-radius:16px; padding:1.5rem; text-align:center;">
                <div style="font-size:2.25rem; font-weight:700; font-family:'Playfair Display',serif; color:#D4AF37; line-height:1;">
                    {{ $checkInsCount }}
                </div>
                <div style="font-family:'Poppins',sans-serif; font-size:0.85rem; color:#9CA3AF; margin-top:0.5rem;">
                    Restaurantes visitados
                </div>
            </div>

            {{-- Votos este mes --}}
            <a href="#mis-votos"
               style="display:block; background:#1A1A1A; border:1px solid #2A2A2A; border-radius:16px; padding:1.5rem; text-align:center; text-decoration:none; transition:border-color 0.2s, background 0.2s;"
               onmouseover="this.style.borderColor='rgba(212,175,55,0.4)'; this.style.background='#1E1E1E';"
               onmouseout="this.style.borderColor='#2A2A2A'; this.style.background='#1A1A1A';">
                <div style="font-size:2.25rem; font-weight:700; font-family:'Playfair Display',serif; color:#D4AF37; line-height:1;">
                    {{ $votesThisMonth->count() }}
                </div>
                <div style="font-family:'Poppins',sans-serif; font-size:0.85rem; color:#9CA3AF; margin-top:0.5rem;">
                    {{ $votesThisMonth->count() === 1 ? 'Voto este mes →' : 'Votos este mes →' }}
                </div>
            </a>

            {{-- Pedidos --}}
            <a href="{{ route('mi-cuenta.pedidos') }}"
               style="display:block; background:#1A1A1A; border:1px solid #2A2A2A; border-radius:16px; padding:1.5rem; text-align:center; text-decoration:none; transition:border-color 0.2s, background 0.2s;"
               onmouseover="this.style.borderColor='rgba(212,175,55,0.4)'; this.style.background='#1E1E1E';"
               onmouseout="this.style.borderColor='#2A2A2A'; this.style.background='#1A1A1A';">
                <div style="font-size:2.25rem; font-weight:700; font-family:'Playfair Display',serif; color:#D4AF37; line-height:1;">
                    {{ $ordersCount }}
                </div>
                <div style="font-family:'Poppins',sans-serif; font-size:0.85rem; color:#9CA3AF; margin-top:0.5rem;">
                    Pedidos realizados →
                </div>
            </a>
        </div>

        {{-- ============================================================
             3. MIS PEDIDOS RECIENTES
        ============================================================ --}}
        @if($ordersCount > 0)
        <div style="margin-bottom:2.5rem;">
            <div style="display:flex; align-items:baseline; justify-content:space-between; margin-bottom:1.25rem; flex-wrap:wrap; gap:0.5rem;">
                <h2 style="font-family:'Playfair Display',serif; font-size:1.5rem; font-weight:700; color:#D4AF37; margin:0;">
                    Mis Pedidos Recientes
                </h2>
                <a href="{{ route('mi-cuenta.pedidos') }}"
                   style="font-family:'Poppins',sans-serif; font-size:0.85rem; color:#D4AF37; text-decoration:none;"
                   onmouseover="this.style.opacity='0.75'" onmouseout="this.style.opacity='1'">
                    Ver historial completo →
                </a>
            </div>
            <div style="display:flex; flex-direction:column; gap:0.75rem;">
                @foreach($recentOrders as $order)
                @php
                    $statusStyles = [
                        'pending'          => 'background:rgba(212,175,55,0.15); color:#D4AF37;',
                        'confirmed'        => 'background:rgba(59,130,246,0.15); color:#93C5FD;',
                        'preparing'        => 'background:rgba(249,115,22,0.15); color:#FDBA74;',
                        'ready'            => 'background:rgba(74,222,128,0.15); color:#4ADE80;',
                        'out_for_delivery' => 'background:rgba(167,139,250,0.15); color:#C4B5FD;',
                        'completed'        => 'background:rgba(74,222,128,0.1); color:#86EFAC;',
                        'cancelled'        => 'background:rgba(239,68,68,0.15); color:#FCA5A5;',
                    ];
                    $statusLabels = [
                        'pending' => 'Pendiente', 'confirmed' => 'Confirmado', 'preparing' => 'Preparando',
                        'ready' => 'Listo', 'out_for_delivery' => 'En camino',
                        'completed' => 'Completado', 'cancelled' => 'Cancelado',
                    ];
                    $badgeStyle = $statusStyles[$order->status] ?? 'background:#2A2A2A; color:#9CA3AF;';
                @endphp
                <div style="background:#1A1A1A; border:1px solid #2A2A2A; border-radius:16px; padding:1.25rem 1.5rem; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:0.75rem;">
                    <div>
                        <div style="font-family:'Playfair Display',serif; font-size:1rem; font-weight:600; color:#F5F5F5; margin-bottom:0.2rem;">
                            {{ $order->restaurant?->name ?? 'Restaurante' }}
                        </div>
                        <div style="font-family:'Poppins',sans-serif; font-size:0.78rem; color:#6B7280;">
                            #{{ $order->order_number }} · {{ $order->created_at->format('d M Y') }}
                        </div>
                    </div>
                    <div style="display:flex; align-items:center; gap:1rem; flex-wrap:wrap;">
                        <span style="font-family:'Poppins',sans-serif; font-size:0.75rem; font-weight:600; padding:0.3rem 0.75rem; border-radius:20px; {{ $badgeStyle }}">
                            {{ $statusLabels[$order->status] ?? $order->status }}
                        </span>
                        <div style="font-family:'Playfair Display',serif; font-size:1.1rem; font-weight:700; color:#D4AF37;">
                            ${{ number_format($order->total, 2) }}
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- ============================================================
             4. MIS VOTOS
        ============================================================ --}}
        <div id="mis-votos" style="margin-bottom:2.5rem;">
            <div style="display:flex; align-items:baseline; justify-content:space-between; margin-bottom:1.25rem; flex-wrap:wrap; gap:0.5rem;">
                <h2 style="font-family:'Playfair Display',serif; font-size:1.5rem; font-weight:700; color:#D4AF37; margin:0;">
                    Mis Votos FAMER
                </h2>
                @if($totalVotes > 0)
                    <span style="font-family:'Poppins',sans-serif; font-size:0.8rem; color:#6B7280;">
                        {{ $totalVotes }} {{ $totalVotes === 1 ? 'voto total' : 'votos en total' }}
                    </span>
                @endif
            </div>

            @if($recentVotes->isEmpty())
                <div style="background:#1A1A1A; border:1px solid #2A2A2A; border-radius:16px; padding:2rem; text-align:center;">
                    <div style="font-size:2rem; margin-bottom:0.75rem;">🏆</div>
                    <p style="font-family:'Poppins',sans-serif; color:#6B7280; margin:0 0 1rem 0;">
                        Aún no has votado este mes.
                    </p>
                    <a href="{{ route('votar') }}"
                       style="display:inline-block; font-family:'Poppins',sans-serif; font-size:0.875rem; font-weight:600; background:#D4AF37; color:#0B0B0B; padding:0.6rem 1.5rem; border-radius:8px; text-decoration:none;">
                        Votar por mi restaurante favorito
                    </a>
                </div>
            @else
                {{-- Este mes --}}
                @if($votesThisMonth->isNotEmpty())
                <div style="background:linear-gradient(135deg, #1A1500 0%, #0F0D00 100%); border:1px solid rgba(212,175,55,0.25); border-radius:16px; padding:1.25rem 1.5rem; margin-bottom:1rem;">
                    <div style="font-family:'Poppins',sans-serif; font-size:0.7rem; font-weight:700; color:#D4AF37; text-transform:uppercase; letter-spacing:0.1em; margin-bottom:1rem;">
                        🗓️ {{ now()->translatedFormat('F Y') }} — Este mes
                    </div>
                    <div style="display:flex; flex-direction:column; gap:0.75rem;">
                        @foreach($votesThisMonth as $vote)
                        <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:0.5rem;">
                            <div>
                                @if($vote->restaurant)
                                    <a href="/restaurante/{{ $vote->restaurant->slug }}"
                                       style="font-family:'Poppins',sans-serif; font-size:0.9rem; font-weight:600; color:#F5F5F5; text-decoration:none;"
                                       onmouseover="this.style.color='#D4AF37'" onmouseout="this.style.color='#F5F5F5'">
                                        {{ $vote->restaurant->name }}
                                    </a>
                                    <div style="font-family:'Poppins',sans-serif; font-size:0.75rem; color:#6B7280; margin-top:0.1rem;">
                                        {{ $vote->restaurant->city }}@if($vote->restaurant->state), {{ $vote->restaurant->state->code ?? $vote->restaurant->state->name }}@endif
                                    </div>
                                @else
                                    <span style="font-family:'Poppins',sans-serif; font-size:0.9rem; color:#6B7280;">Restaurante eliminado</span>
                                @endif
                            </div>
                            <div style="display:flex; align-items:center; gap:0.5rem;">
                                <span style="font-family:'Poppins',sans-serif; font-size:0.72rem; font-weight:600; padding:0.25rem 0.65rem; border-radius:20px; background:rgba(212,175,55,0.12); color:#D4AF37; border:1px solid rgba(212,175,55,0.25);">
                                    ⭐ {{ match($vote->vote_type) { 'up' => 'Voto', 'favorite' => 'Favorito', 'must_visit' => 'Must Visit', 'qr_scan' => 'QR', 'qr_email' => 'QR verificado', default => $vote->vote_type } }}
                                </span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <div style="margin-top:1rem; padding-top:1rem; border-top:1px solid rgba(212,175,55,0.1); text-align:center;">
                        <a href="{{ route('votar') }}"
                           style="font-family:'Poppins',sans-serif; font-size:0.8rem; color:#D4AF37; text-decoration:none; opacity:0.8;"
                           onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0.8'">
                            Votar por otro restaurante este mes →
                        </a>
                    </div>
                </div>
                @else
                <div style="background:#1A1A1A; border:1px dashed rgba(212,175,55,0.25); border-radius:16px; padding:1rem 1.5rem; margin-bottom:1rem; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:0.75rem;">
                    <span style="font-family:'Poppins',sans-serif; font-size:0.875rem; color:#9CA3AF;">
                        🗓️ Aún no has votado en {{ now()->translatedFormat('F') }}
                    </span>
                    <a href="{{ route('votar') }}"
                       style="font-family:'Poppins',sans-serif; font-size:0.8rem; font-weight:600; color:#D4AF37; text-decoration:none; background:rgba(212,175,55,0.1); border:1px solid rgba(212,175,55,0.25); padding:0.4rem 1rem; border-radius:8px;">
                        Votar ahora →
                    </a>
                </div>
                @endif

                {{-- Historial de votos anteriores --}}
                @php $pastVotes = $recentVotes->filter(fn($v) => !($v->year == now()->year && $v->month == now()->month)); @endphp
                @if($pastVotes->isNotEmpty())
                <div style="background:#1A1A1A; border:1px solid #2A2A2A; border-radius:16px; padding:1.25rem 1.5rem;">
                    <div style="font-family:'Poppins',sans-serif; font-size:0.7rem; font-weight:700; color:#6B7280; text-transform:uppercase; letter-spacing:0.1em; margin-bottom:1rem;">
                        Votos anteriores
                    </div>
                    <div style="display:flex; flex-direction:column; gap:0.65rem;">
                        @foreach($pastVotes as $vote)
                        <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:0.5rem;">
                            <div>
                                @if($vote->restaurant)
                                    <a href="/restaurante/{{ $vote->restaurant->slug }}"
                                       style="font-family:'Poppins',sans-serif; font-size:0.875rem; color:#9CA3AF; text-decoration:none;"
                                       onmouseover="this.style.color='#F5F5F5'" onmouseout="this.style.color='#9CA3AF'">
                                        {{ $vote->restaurant->name }}
                                    </a>
                                @else
                                    <span style="font-family:'Poppins',sans-serif; font-size:0.875rem; color:#4B5563;">Restaurante eliminado</span>
                                @endif
                            </div>
                            <span style="font-family:'Poppins',sans-serif; font-size:0.72rem; color:#4B5563;">
                                @php $months = ['','Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic']; @endphp
                                {{ $months[$vote->month] ?? $vote->month }} {{ $vote->year }}
                            </span>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            @endif
        </div>

        {{-- ============================================================
             5. MIS FAVORITOS — LAST 4
        ============================================================ --}}
        <div style="margin-bottom:2.5rem;">
            <div style="display:flex; align-items:baseline; justify-content:space-between; margin-bottom:1.25rem; flex-wrap:wrap; gap:0.5rem;">
                <h2 style="font-family:'Playfair Display',serif; font-size:1.5rem; font-weight:700; color:#D4AF37; margin:0;">
                    Mis Favoritos
                </h2>
                @if($favoritesCount > 0)
                    <a href="/my-favorites"
                       style="font-family:'Poppins',sans-serif; font-size:0.85rem; color:#D4AF37; text-decoration:none;"
                       onmouseover="this.style.opacity='0.75'" onmouseout="this.style.opacity='1'">
                        Ver todos mis favoritos →
                    </a>
                @endif
            </div>

            @if($recentFavorites->isEmpty())
                <div style="background:#1A1A1A; border:1px solid #2A2A2A; border-radius:16px; padding:2rem; text-align:center;">
                    <p style="font-family:'Poppins',sans-serif; color:#6B7280; margin:0;">
                        Aún no tienes favoritos.
                        <a href="/" style="color:#D4AF37; text-decoration:none;">Explora restaurantes →</a>
                    </p>
                </div>
            @else
                <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(240px, 1fr)); gap:1rem;">
                    @foreach($recentFavorites as $restaurant)
                        <a href="/restaurante/{{ $restaurant->slug }}"
                           style="display:block; text-decoration:none; background:#1A1A1A; border:1px solid #2A2A2A; border-radius:16px; padding:1.25rem 1.5rem; transition:background 0.2s, border-color 0.2s;"
                           onmouseover="this.style.background='#252525'; this.style.borderColor='#3A3A3A';"
                           onmouseout="this.style.background='#1A1A1A'; this.style.borderColor='#2A2A2A';">
                            <div style="font-family:'Playfair Display',serif; font-size:1rem; font-weight:600; color:#F5F5F5; margin-bottom:0.35rem; line-height:1.3;">
                                {{ $restaurant->name }}
                            </div>
                            <div style="font-family:'Poppins',sans-serif; font-size:0.8rem; color:#6B7280;">
                                {{ $restaurant->city }}@if($restaurant->state), {{ $restaurant->state->code ?? $restaurant->state->name }}@endif
                            </div>
                            @if(!empty($restaurant->rating) && $restaurant->rating > 0)
                                <div style="margin-top:0.5rem; font-size:0.8rem; color:#D4AF37;">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= round($restaurant->rating))★@else☆@endif
                                    @endfor
                                    <span style="color:#6B7280; margin-left:0.25rem;">{{ number_format($restaurant->rating, 1) }}</span>
                                </div>
                            @endif
                        </a>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- ============================================================
             5. MIS RESEÑAS — LAST 3
        ============================================================ --}}
        <div style="margin-bottom:2.5rem;">
            <h2 style="font-family:'Playfair Display',serif; font-size:1.5rem; font-weight:700; color:#D4AF37; margin:0 0 1.25rem 0;">
                Mis Reseñas
            </h2>

            @if($recentReviews->isEmpty())
                <div style="background:#1A1A1A; border:1px solid #2A2A2A; border-radius:16px; padding:2rem; text-align:center;">
                    <p style="font-family:'Poppins',sans-serif; color:#6B7280; margin:0;">
                        Aún no has escrito reseñas.
                    </p>
                </div>
            @else
                <div style="display:flex; flex-direction:column; gap:1rem;">
                    @foreach($recentReviews as $review)
                        <div style="background:#1A1A1A; border:1px solid #2A2A2A; border-radius:16px; padding:1.5rem;">
                            <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:0.5rem; margin-bottom:0.75rem;">
                                <div>
                                    @if($review->restaurant)
                                        <a href="/restaurante/{{ $review->restaurant->slug }}"
                                           style="font-family:'Playfair Display',serif; font-size:1rem; font-weight:600; color:#F5F5F5; text-decoration:none;"
                                           onmouseover="this.style.color='#D4AF37'" onmouseout="this.style.color='#F5F5F5'">
                                            {{ $review->restaurant->name }}
                                        </a>
                                    @endif
                                </div>
                                <div style="font-family:'Poppins',sans-serif; font-size:0.75rem; color:#6B7280;">
                                    {{ $review->created_at->format('d M Y') }}
                                </div>
                            </div>
                            @if(!empty($review->rating))
                                <div style="font-size:0.9rem; color:#D4AF37; margin-bottom:0.5rem;">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= $review->rating)★@else☆@endif
                                    @endfor
                                </div>
                            @endif
                            @if(!empty($review->body))
                                <p style="font-family:'Poppins',sans-serif; font-size:0.875rem; color:#9CA3AF; margin:0; line-height:1.6;">
                                    {{ Str::limit($review->body, 180) }}
                                </p>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- ============================================================
             6. CTA — ¿TIENES UN RESTAURANTE?
        ============================================================ --}}
        <div style="margin-bottom:2.5rem; background:linear-gradient(135deg, #2A1F00 0%, #1A1200 50%, #0D0900 100%); border:1px solid #D4AF37; border-radius:16px; padding:2rem 2rem; text-align:center;">
            <h3 style="font-family:'Playfair Display',serif; font-size:1.35rem; font-weight:700; color:#D4AF37; margin:0 0 0.75rem 0;">
                ¿Eres dueño de un restaurante mexicano?
            </h3>
            <p style="font-family:'Poppins',sans-serif; font-size:0.9rem; color:#C4A84A; margin:0 0 1.5rem 0; max-width:520px; margin-left:auto; margin-right:auto;">
                Reclama tu perfil GRATIS y accede a estadísticas, menú digital y más.
            </p>
            <a href="/claim"
               style="display:inline-block; font-family:'Poppins',sans-serif; font-size:0.9rem; font-weight:600; background:#D4AF37; color:#0B0B0B; padding:0.75rem 2rem; border-radius:8px; text-decoration:none; transition:opacity 0.2s;"
               onmouseover="this.style.opacity='0.85'" onmouseout="this.style.opacity='1'">
                Reclamar mi restaurante
            </a>
        </div>

        {{-- ============================================================
             7. CUENTA — ACCIONES RÁPIDAS
        ============================================================ --}}
        <div style="margin-bottom:2.5rem;">
            <h2 style="font-family:'Playfair Display',serif; font-size:1.5rem; font-weight:700; color:#D4AF37; margin:0 0 1.25rem 0;">
                Mi Cuenta
            </h2>
            <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(200px, 1fr)); gap:1rem;">

                <a href="{{ route('mi-cuenta.perfil') }}"
                   style="display:block; background:#1A1A1A; border:1px solid #2A2A2A; border-radius:16px; padding:1.5rem; text-decoration:none; transition:background 0.2s, border-color 0.2s;"
                   onmouseover="this.style.background='#252525'; this.style.borderColor='rgba(212,175,55,0.35)';"
                   onmouseout="this.style.background='#1A1A1A'; this.style.borderColor='#2A2A2A';">
                    <div style="font-size:1.5rem; margin-bottom:0.5rem;">👤</div>
                    <div style="font-family:'Poppins',sans-serif; font-size:0.9rem; font-weight:600; color:#F5F5F5;">
                        Editar Perfil →
                    </div>
                </a>

                <a href="{{ route('mi-cuenta.pedidos') }}"
                   style="display:block; background:#1A1A1A; border:1px solid #2A2A2A; border-radius:16px; padding:1.5rem; text-decoration:none; transition:background 0.2s, border-color 0.2s;"
                   onmouseover="this.style.background='#252525'; this.style.borderColor='rgba(212,175,55,0.35)';"
                   onmouseout="this.style.background='#1A1A1A'; this.style.borderColor='#2A2A2A';">
                    <div style="font-size:1.5rem; margin-bottom:0.5rem;">🛍️</div>
                    <div style="font-family:'Poppins',sans-serif; font-size:0.9rem; font-weight:600; color:#F5F5F5;">
                        Historial de Pedidos →
                    </div>
                </a>

            </div>
        </div>

        {{-- ============================================================
             8. EXPLORAR — QUICK LINKS
        ============================================================ --}}
        <div>
            <h2 style="font-family:'Playfair Display',serif; font-size:1.5rem; font-weight:700; color:#D4AF37; margin:0 0 1.25rem 0;">
                Explorar
            </h2>
            <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(200px, 1fr)); gap:1rem;">

                <a href="/cerca-de-mi"
                   style="display:block; background:#1A1A1A; border:1px solid #2A2A2A; border-radius:16px; padding:1.5rem; text-decoration:none; transition:background 0.2s, border-color 0.2s;"
                   onmouseover="this.style.background='#252525'; this.style.borderColor='#3A3A3A';"
                   onmouseout="this.style.background='#1A1A1A'; this.style.borderColor='#2A2A2A';">
                    <div style="font-size:1.5rem; margin-bottom:0.5rem;">📍</div>
                    <div style="font-family:'Poppins',sans-serif; font-size:0.9rem; font-weight:600; color:#F5F5F5;">
                        Restaurantes cerca →
                    </div>
                </a>

                <a href="/rankings"
                   style="display:block; background:#1A1A1A; border:1px solid #2A2A2A; border-radius:16px; padding:1.5rem; text-decoration:none; transition:background 0.2s, border-color 0.2s;"
                   onmouseover="this.style.background='#252525'; this.style.borderColor='#3A3A3A';"
                   onmouseout="this.style.background='#1A1A1A'; this.style.borderColor='#2A2A2A';">
                    <div style="font-size:1.5rem; margin-bottom:0.5rem;">🏆</div>
                    <div style="font-family:'Poppins',sans-serif; font-size:0.9rem; font-weight:600; color:#F5F5F5;">
                        Top 10 →
                    </div>
                </a>

                <a href="/famer-awards"
                   style="display:block; background:#1A1A1A; border:1px solid #2A2A2A; border-radius:16px; padding:1.5rem; text-decoration:none; transition:background 0.2s, border-color 0.2s;"
                   onmouseover="this.style.background='#252525'; this.style.borderColor='#3A3A3A';"
                   onmouseout="this.style.background='#1A1A1A'; this.style.borderColor='#2A2A2A';">
                    <div style="font-size:1.5rem; margin-bottom:0.5rem;">⭐</div>
                    <div style="font-family:'Poppins',sans-serif; font-size:0.9rem; font-weight:600; color:#F5F5F5;">
                        FAMER Awards →
                    </div>
                </a>

            </div>
        </div>

    </div>{{-- /max-width container --}}
</div>{{-- /outer bg --}}
@endsection
