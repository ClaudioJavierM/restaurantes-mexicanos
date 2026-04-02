@extends('layouts.app')

@section('title')
{{ $isEn
    ? "Best Mexican Restaurants in {$state->name} — Top {$total} | FAMER"
    : "Restaurantes Mexicanos en {$state->name} — Top {$total} | FAMER" }}
@endsection

@section('meta_description'){{ $isEn
    ? "Discover the {$total} best Mexican restaurants in {$state->name}. Verified reviews, photos and menus."
    : "Descubre los {$total} mejores restaurantes mexicanos en {$state->name}. Reseñas verificadas, fotos y menús." }}@endsection

@push('meta')
<meta name="geo.region" content="{{ $state->country === 'MX' ? 'MX' : 'US' }}-{{ $state->code }}">
<meta name="geo.placename" content="{{ $state->name }}">
@endpush

@section('content')
<div class="min-h-screen" style="background:#0B0B0B; color:#F5F5F5;">

    {{-- ─── HERO ─────────────────────────────────────────────────────── --}}
    <div style="background:linear-gradient(135deg,#0B0B0B 0%,#1A1A1A 50%,#0B0B0B 100%); border-bottom:1px solid #2A2A2A; padding:4rem 0 3rem;">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">

            {{-- Breadcrumb --}}
            <nav style="margin-bottom:1.5rem;">
                <ol style="display:flex; justify-content:center; flex-wrap:wrap; gap:0.5rem; align-items:center; font-size:0.875rem; color:#9CA3AF;">
                    <li><a href="/" style="color:#D4AF37; text-decoration:none;">FAMER</a></li>
                    <li style="color:#4B5563;">/</li>
                    <li><a href="/estados" style="color:#D4AF37; text-decoration:none;">
                        {{ $isEn ? 'States' : 'Estados' }}
                    </a></li>
                    <li style="color:#4B5563;">/</li>
                    <li style="color:#9CA3AF;">{{ $state->name }}</li>
                </ol>
            </nav>

            {{-- H1 --}}
            <h1 style="font-family:'Playfair Display',serif; font-size:clamp(1.75rem,5vw,3.25rem); font-weight:700; color:#F5F5F5; margin-bottom:1rem; line-height:1.2;">
                {{ $isEn ? 'Best Mexican Restaurants in' : 'Los Mejores Restaurantes Mexicanos en' }}<br>
                <span style="color:#D4AF37;">{{ $state->name }}</span>
            </h1>

            <p style="color:#9CA3AF; font-size:1.125rem; max-width:600px; margin:0 auto 2.5rem;">
                @if($isEn)
                    Discover {{ number_format($total) }} authentic Mexican restaurants in {{ $state->name }}. Verified reviews, real photos and menus.
                @else
                    Descubre {{ number_format($total) }} restaurantes mexicanos auténticos en {{ $state->name }}. Reseñas verificadas, fotos reales y menús.
                @endif
            </p>
        </div>
    </div>

    {{-- ─── STATS BAR ────────────────────────────────────────────────── --}}
    <div style="background:#1A1A1A; border-bottom:1px solid #2A2A2A; padding:1.25rem 0;">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div style="display:flex; flex-wrap:wrap; gap:1rem; justify-content:center;">

                {{-- Total restaurants --}}
                <div style="display:flex; align-items:center; gap:0.75rem; background:#0B0B0B; border:1px solid #D4AF37; border-radius:9999px; padding:0.6rem 1.4rem;">
                    <svg style="width:1.1rem; height:1.1rem; color:#D4AF37;" fill="none" stroke="#D4AF37" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                    <span style="color:#F5F5F5; font-size:0.9375rem; font-weight:600;">
                        {{ number_format($total) }} {{ $isEn ? 'Restaurants' : 'Restaurantes' }}
                    </span>
                </div>

                {{-- Top city --}}
                @if($topCity)
                <div style="display:flex; align-items:center; gap:0.75rem; background:#0B0B0B; border:1px solid #D4AF37; border-radius:9999px; padding:0.6rem 1.4rem;">
                    <svg style="width:1.1rem; height:1.1rem;" fill="none" stroke="#D4AF37" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <span style="color:#F5F5F5; font-size:0.9375rem; font-weight:600;">
                        {{ $isEn ? 'Top City:' : 'Ciudad Top:' }} {{ $topCity }}
                    </span>
                </div>
                @endif

                {{-- Avg rating --}}
                @if($avgRating)
                <div style="display:flex; align-items:center; gap:0.75rem; background:#0B0B0B; border:1px solid #D4AF37; border-radius:9999px; padding:0.6rem 1.4rem;">
                    <span style="color:#D4AF37; font-size:1rem;">★</span>
                    <span style="color:#F5F5F5; font-size:0.9375rem; font-weight:600;">
                        {{ number_format($avgRating, 1) }} {{ $isEn ? 'Avg Rating' : 'Calificación Promedio' }}
                    </span>
                </div>
                @endif

            </div>
        </div>
    </div>

    {{-- ─── MAIN CONTENT ──────────────────────────────────────────────── --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

        {{-- TOP 10 RESTAURANTS ──────────────────────────────────────── --}}
        <section style="margin-bottom:4rem;">
            <h2 style="font-family:'Playfair Display',serif; font-size:1.75rem; font-weight:700; color:#F5F5F5; margin-bottom:0.5rem;">
                {{ $isEn ? "Top Restaurants in {$state->name}" : "Top Restaurantes en {$state->name}" }}
            </h2>
            <p style="color:#9CA3AF; margin-bottom:2rem;">
                {{ $isEn
                    ? 'Ranked by Google rating and number of reviews.'
                    : 'Ordenados por calificación Google y número de reseñas.' }}
            </p>

            <div style="display:grid; grid-template-columns:repeat(auto-fill,minmax(280px,1fr)); gap:1rem;">
                @foreach($restaurants as $index => $restaurant)
                @php
                    $rating = $restaurant->google_rating ?? $restaurant->average_rating ?? null;
                    $reviewsCount = $restaurant->google_reviews_count ?? $restaurant->total_reviews ?? 0;
                    // Get first photo from JSON array
                    $photoUrl = $restaurant->getFirstMediaUrl('photos', 'thumb')
                        ?: $restaurant->getFirstMediaUrl('images')
                        ?: ($restaurant->yelp_photos[0] ?? null)
                        ?: ($restaurant->image ? (str_starts_with($restaurant->image, 'http') ? $restaurant->image : \Illuminate\Support\Facades\Storage::url($restaurant->image)) : null);
                @endphp
                <a href="/restaurante/{{ $restaurant->slug }}"
                   style="display:block; background:#1A1A1A; border:1px solid #2A2A2A; border-radius:12px; overflow:hidden; text-decoration:none; transition:border-color 0.2s; position:relative;"
                   onmouseover="this.style.borderColor='#D4AF37'" onmouseout="this.style.borderColor='#2A2A2A'">

                    {{-- Rank badge --}}
                    <div style="position:absolute; top:0.75rem; left:0.75rem; background:#D4AF37; color:#0B0B0B; width:1.75rem; height:1.75rem; border-radius:50%; display:flex; align-items:center; justify-content:center; font-weight:700; font-size:0.8125rem; z-index:1;">
                        {{ $index + 1 }}
                    </div>

                    {{-- Photo --}}
                    @if($photoUrl)
                    <div style="height:160px; overflow:hidden;">
                        <img src="{{ $photoUrl }}" alt="{{ $restaurant->name }}" loading="lazy"
                             style="width:100%; height:100%; object-fit:cover;">
                    </div>
                    @else
                    <div style="height:100px; background:#2A2A2A; display:flex; align-items:center; justify-content:center;">
                        <span style="font-size:2rem;">🌮</span>
                    </div>
                    @endif

                    {{-- Info --}}
                    <div style="padding:1rem;">
                        <h3 style="font-weight:600; color:#F5F5F5; margin-bottom:0.25rem; font-size:0.9375rem; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                            {{ $restaurant->name }}
                        </h3>
                        <p style="color:#9CA3AF; font-size:0.8125rem; margin-bottom:0.625rem;">
                            {{ $restaurant->city }}{{ $restaurant->city ? ', ' : '' }}{{ $state->code }}
                        </p>
                        <div style="display:flex; align-items:center; gap:0.75rem; flex-wrap:wrap;">
                            @if($rating)
                            <span style="color:#D4AF37; font-size:0.875rem; font-weight:600;">
                                ★ {{ number_format($rating, 1) }}
                            </span>
                            @endif
                            @if($reviewsCount > 0)
                            <span style="color:#6B7280; font-size:0.8125rem;">
                                ({{ number_format($reviewsCount) }} {{ $isEn ? 'reviews' : 'reseñas' }})
                            </span>
                            @endif
                            @if($restaurant->price_range)
                            <span style="color:#6B7280; font-size:0.8125rem;">{{ $restaurant->price_range }}</span>
                            @endif
                        </div>
                    </div>
                </a>
                @endforeach
            </div>

            {{-- Link to full list --}}
            <div style="text-align:center; margin-top:2rem;">
                <a href="/restaurantes?state={{ $state->code }}"
                   style="display:inline-flex; align-items:center; gap:0.5rem; border:1px solid #D4AF37; color:#D4AF37; padding:0.75rem 2rem; border-radius:9999px; text-decoration:none; font-weight:600; font-size:0.9375rem; transition:background 0.2s;"
                   onmouseover="this.style.background='#D4AF3720'" onmouseout="this.style.background='transparent'">
                    {{ $isEn ? "See all {$total} restaurants in {$state->name}" : "Ver los {$total} restaurantes en {$state->name}" }}
                    <svg style="width:1rem; height:1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>
        </section>

        {{-- TOP CITIES ──────────────────────────────────────────────── --}}
        @if($cities->count() > 0)
        <section style="margin-bottom:4rem;">
            <h2 style="font-family:'Playfair Display',serif; font-size:1.75rem; font-weight:700; color:#F5F5F5; margin-bottom:0.5rem;">
                {{ $isEn ? "Top Cities in {$state->name}" : "Principales Ciudades en {$state->name}" }}
            </h2>
            <p style="color:#9CA3AF; margin-bottom:1.5rem;">
                {{ $isEn ? 'Cities with the most Mexican restaurants.' : 'Ciudades con más restaurantes mexicanos.' }}
            </p>
            <div style="display:flex; flex-wrap:wrap; gap:0.75rem;">
                @foreach($cities as $cityRow)
                @php $citySlug = \Illuminate\Support\Str::slug($cityRow->city); @endphp
                <a href="/restaurantes?state={{ $state->code }}&city={{ urlencode($cityRow->city) }}"
                   style="display:inline-flex; align-items:center; gap:0.5rem; background:#1A1A1A; border:1px solid #2A2A2A; color:#F5F5F5; padding:0.625rem 1.25rem; border-radius:9999px; text-decoration:none; font-size:0.9375rem; transition:border-color 0.2s;"
                   onmouseover="this.style.borderColor='#D4AF37';this.style.color='#D4AF37'" onmouseout="this.style.borderColor='#2A2A2A';this.style.color='#F5F5F5'">
                    <svg style="width:0.875rem; height:0.875rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    {{ $cityRow->city }}
                    <span style="color:#D4AF37; font-size:0.8125rem; font-weight:600;">{{ number_format($cityRow->count) }}</span>
                </a>
                @endforeach
            </div>
        </section>
        @endif

        {{-- SEO CONTENT ─────────────────────────────────────────────── --}}
        <section style="padding:3rem 0; border-top:1px solid #2A2A2A;">
            <div style="max-width:800px; margin:0 auto;">
                <h2 style="font-family:'Playfair Display',serif; font-size:1.5rem; font-weight:700; color:#F5F5F5; margin-bottom:1.25rem;">
                    {{ $isEn
                        ? "Authentic Mexican Food in {$state->name}"
                        : "Comida Mexicana Auténtica en {$state->name}" }}
                </h2>
                <div style="color:#9CA3AF; line-height:1.8; font-size:1rem;">
                    @if($isEn)
                    <p style="margin-bottom:1rem;">
                        FAMER is the most complete directory of authentic Mexican restaurants in {{ $state->name }}.
                        With {{ number_format($total) }} verified listings, you'll find everything from family taquerías
                        to high-end Mexican cuisine — all with real reviews from our community.
                    </p>
                    <p>
                        Looking for something specific? Browse by dish:
                        <a href="/birria" style="color:#D4AF37;">birria</a>,
                        <a href="/tamales" style="color:#D4AF37;">tamales</a>,
                        <a href="/pozole" style="color:#D4AF37;">pozole</a>,
                        <a href="/carnitas" style="color:#D4AF37;">carnitas</a> and more.
                    </p>
                    @else
                    <p style="margin-bottom:1rem;">
                        FAMER es el directorio más completo de restaurantes mexicanos auténticos en {{ $state->name }}.
                        Con {{ number_format($total) }} restaurantes verificados, encontrarás desde pequeñas taquerías
                        familiares hasta alta cocina mexicana — con reseñas reales de nuestra comunidad.
                    </p>
                    <p>
                        ¿Buscas algo específico? Explora por platillo:
                        <a href="/birria" style="color:#D4AF37;">birria</a>,
                        <a href="/tamales" style="color:#D4AF37;">tamales</a>,
                        <a href="/pozole" style="color:#D4AF37;">pozole</a>,
                        <a href="/carnitas" style="color:#D4AF37;">carnitas</a> y más.
                    </p>
                    @endif
                </div>
            </div>
        </section>

    </div>
</div>
@endsection

@push('scripts')
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "ItemList",
    "name": "{{ $isEn ? "Best Mexican Restaurants in {$state->name}" : "Restaurantes Mexicanos en {$state->name}" }}",
    "description": "{{ $isEn ? "Top {$total} Mexican restaurants in {$state->name} ranked by rating." : "Top {$total} restaurantes mexicanos en {$state->name} ordenados por calificación." }}",
    "numberOfItems": {{ $restaurants->count() }},
    "itemListElement": [
        @foreach($restaurants as $index => $restaurant)
        {
            "@@type": "ListItem",
            "position": {{ $index + 1 }},
            "item": {
                "@@type": "Restaurant",
                "name": "{{ addslashes($restaurant->name) }}",
                "url": "{{ url('/restaurante/' . $restaurant->slug) }}",
                "address": {
                    "@@type": "PostalAddress",
                    "addressLocality": "{{ addslashes($restaurant->city ?? '') }}",
                    "addressRegion": "{{ $state->code }}",
                    "addressCountry": "{{ $state->country === 'MX' ? 'MX' : 'US' }}"
                }
                @if($restaurant->google_rating ?? $restaurant->average_rating)
                ,"aggregateRating": {
                    "@@type": "AggregateRating",
                    "ratingValue": "{{ number_format($restaurant->google_rating ?? $restaurant->average_rating, 1) }}",
                    "bestRating": "5",
                    "reviewCount": "{{ $restaurant->google_reviews_count ?? $restaurant->total_reviews ?? 1 }}"
                }
                @endif
            }
        }{{ !$loop->last ? ',' : '' }}
        @endforeach
    ]
}
</script>

<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "BreadcrumbList",
    "itemListElement": [
        {
            "@@type": "ListItem",
            "position": 1,
            "name": "FAMER",
            "item": "{{ url('/') }}"
        },
        {
            "@@type": "ListItem",
            "position": 2,
            "name": "{{ $isEn ? 'States' : 'Estados' }}",
            "item": "{{ url('/estados') }}"
        },
        {
            "@@type": "ListItem",
            "position": 3,
            "name": "{{ $state->name }}",
            "item": "{{ url()->current() }}"
        }
    ]
}
</script>
@endpush
