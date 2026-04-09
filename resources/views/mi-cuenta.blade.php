@extends('layouts.app')
@section('title', 'Mi Cuenta — FAMER')

@php
    $user = auth()->user();
    $favoritesCount = $user->favorites()->count();
    try { $reviewsCount = $user->reviews()->count(); } catch(\Exception $e) { $reviewsCount = 0; }
    try { $checkInsCount = $user->checkIns()->count(); } catch(\Exception $e) { $checkInsCount = 0; }
    $recentFavorites = $user->favorites()->latest('favorites.created_at')->take(4)->get();
    try { $recentReviews = $user->reviews()->with('restaurant')->latest()->take(3)->get(); } catch(\Exception $e) { $recentReviews = collect(); }
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
            <a href="{{ route('logout') }}"
               onclick="event.preventDefault(); document.getElementById('logout-form-cuenta').submit();"
               style="font-family:'Poppins',sans-serif; font-size:0.8rem; color:#6B7280; text-decoration:none; transition:color 0.2s;"
               onmouseover="this.style.color='#9CA3AF'" onmouseout="this.style.color='#6B7280'">
                Cerrar sesión
            </a>
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
        </div>

        {{-- ============================================================
             3. MIS FAVORITOS — LAST 4
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
                                {{ $restaurant->city }}@if($restaurant->state), {{ $restaurant->state }}@endif
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
             4. MIS RESEÑAS — LAST 3
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
             5. CTA — ¿TIENES UN RESTAURANTE?
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
             6. EXPLORAR — 3 QUICK LINKS
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
