@extends('layouts.app')

@section('title')
Mejores Restaurantes Mexicanos en {{ $state->name }} | FAMER
@endsection
@section('meta_description')
Descubre los {{ number_format($stats->total) }} mejores restaurantes mexicanos en {{ $state->name }}. {{ $cities->count() }} ciudades, ratings verificados y más.
@endsection

@push('meta')
<!-- Open Graph / Facebook -->
<meta property="og:type" content="website">
<meta property="og:url" content="{{ url()->current() }}">
<meta property="og:title" content="Mejores Restaurantes Mexicanos en {{ $state->name }} | FAMER">
<meta property="og:description" content="Descubre {{ number_format($stats->total) }} restaurantes mexicanos en {{ $state->name }}. {{ $cities->count() }} ciudades con comida mexicana autentica.">
@php
    $topRest = $top10Restaurants->first();
    $ogStateImage = null;
    if ($topRest) {
        if ($topRest->image) {
            $ogStateImage = str_starts_with($topRest->image, 'http') ? $topRest->image : asset('storage/' . $topRest->image);
        } elseif ($topRest->getFirstMediaUrl('images')) {
            $ogStateImage = $topRest->getFirstMediaUrl('images');
        }
    }
@endphp
@if($ogStateImage)
<meta property="og:image" content="{{ $ogStateImage }}">
@endif

<!-- Twitter Card -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="Mejores Restaurantes Mexicanos en {{ $state->name }} | FAMER">
<meta name="twitter:description" content="{{ number_format($stats->total) }} restaurantes en {{ $cities->count() }} ciudades. Rating promedio: {{ number_format($stats->avg_rating ?? 0, 1) }}">

<!-- SEO: Geo Tags -->
<meta name="geo.region" content="{{ $state->country === 'MX' ? 'MX' : 'US' }}-{{ $state->code }}">
<meta name="geo.placename" content="{{ $state->name }}">
@endpush

@section('content')

{{-- ===================== HERO SECTION ===================== --}}
<div class="bg-[#0B0B0B] border-b border-[#D4AF37]/20">
    <div class="max-w-7xl mx-auto px-4 py-12 sm:px-6 lg:px-8">

        {{-- Breadcrumb --}}
        <nav class="text-sm mb-6 flex items-center gap-2 text-gray-500">
            <a href="{{ url('/') }}" class="hover:text-[#D4AF37] transition-colors">FAMER</a>
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <a href="{{ route('city-guides.states') }}" class="hover:text-[#D4AF37] transition-colors">Guía</a>
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <span class="text-[#F5F5F5]">{{ $state->name }}</span>
        </nav>

        {{-- H1 --}}
        <h1 class="text-3xl md:text-5xl font-bold text-[#F5F5F5] mb-4" style="font-family: 'Playfair Display', serif;">
            Mejores Restaurantes Mexicanos en <span class="text-[#D4AF37]">{{ $state->name }}</span>
        </h1>
        <p class="text-lg text-gray-400 max-w-3xl">
            Guía completa con los {{ number_format($stats->total) }} mejores restaurantes de comida mexicana en {{ $state->name }}.
            {{ $cities->count() }} ciudades, calificaciones verificadas de Google y más.
        </p>

        {{-- Summary Stats --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-8">
            <div class="bg-[#1A1A1A] border border-[#2A2A2A] rounded-xl p-5 text-center">
                <p class="text-3xl font-bold text-[#D4AF37]">{{ number_format($stats->total) }}</p>
                <p class="text-sm text-gray-400 mt-1">Restaurantes</p>
            </div>
            <div class="bg-[#1A1A1A] border border-[#2A2A2A] rounded-xl p-5 text-center">
                <p class="text-3xl font-bold text-[#D4AF37]">{{ $cities->count() }}</p>
                <p class="text-sm text-gray-400 mt-1">Ciudades</p>
            </div>
            <div class="bg-[#1A1A1A] border border-[#2A2A2A] rounded-xl p-5 text-center">
                <p class="text-3xl font-bold text-[#D4AF37]">{{ number_format($stats->avg_rating ?? 0, 1) }}</p>
                <p class="text-sm text-gray-400 mt-1">Rating Promedio</p>
            </div>
            <div class="bg-[#1A1A1A] border border-[#2A2A2A] rounded-xl p-5 text-center">
                <p class="text-3xl font-bold text-[#D4AF37]">{{ $stats->claimed_count ?? 0 }}</p>
                <p class="text-sm text-gray-400 mt-1">Verificados</p>
            </div>
        </div>
    </div>
</div>

<div class="bg-[#0B0B0B] min-h-screen">
    <div class="max-w-7xl mx-auto px-4 py-10 sm:px-6 lg:px-8">

        {{-- ===================== TOP 10 RANKED LIST ===================== --}}
        @if($top10Restaurants->isNotEmpty())
        <section class="mb-14">
            <div class="flex items-center gap-3 mb-2">
                <div class="w-1 h-8 bg-[#D4AF37] rounded-full"></div>
                <h2 class="text-2xl font-bold text-[#F5F5F5]" style="font-family: 'Playfair Display', serif;">
                    Top {{ $top10Restaurants->count() }} Mejores Restaurantes en {{ $state->name }}
                </h2>
            </div>
            <p class="text-gray-400 text-sm mb-6 pl-4">Ordenados por número de reseñas en Google y calificación verificada.</p>

            <div class="space-y-4">
                @foreach($top10Restaurants as $index => $restaurant)
                @php
                    $isElite = $restaurant->subscription_tier === 'elite';
                    $isPremium = $restaurant->subscription_tier === 'premium';
                    $rank = $index + 1;
                    $rankColors = match($rank) {
                        1 => ['bg' => 'bg-[#D4AF37]', 'text' => 'text-[#0B0B0B]'],
                        2 => ['bg' => 'bg-gray-400', 'text' => 'text-[#0B0B0B]'],
                        3 => ['bg' => 'bg-amber-700', 'text' => 'text-white'],
                        default => ['bg' => 'bg-[#2A2A2A]', 'text' => 'text-gray-400'],
                    };
                    $topRestImage = null;
                    if ($restaurant->image) {
                        $topRestImage = str_starts_with($restaurant->image, 'http') ? $restaurant->image : asset('storage/' . $restaurant->image);
                    } elseif ($restaurant->getFirstMediaUrl('images')) {
                        $topRestImage = $restaurant->getFirstMediaUrl('images');
                    }
                    $weightedRating = $restaurant->getWeightedRating();
                    $combinedReviews = $restaurant->getCombinedReviewCount();
                @endphp
                <a href="{{ route('restaurants.show', $restaurant->slug) }}"
                   class="flex gap-4 bg-[#1A1A1A] border {{ $isElite ? 'border-[#D4AF37]/50' : ($isPremium ? 'border-[#D4AF37]/20' : 'border-[#2A2A2A]') }} rounded-xl p-4 hover:border-[#D4AF37]/40 hover:bg-[#1F1F1F] transition-all group">

                    {{-- Rank Number --}}
                    <div class="flex-shrink-0 flex items-center">
                        <div class="{{ $rankColors['bg'] }} {{ $rankColors['text'] }} w-10 h-10 rounded-full flex items-center justify-center font-bold text-lg shadow-lg">
                            {{ $rank }}
                        </div>
                    </div>

                    {{-- Restaurant Image --}}
                    <div class="flex-shrink-0 w-20 h-20 rounded-lg overflow-hidden bg-[#2A2A2A]">
                        @if($topRestImage)
                            <img src="{{ $topRestImage }}"
                                 alt="{{ $restaurant->name }}"
                                 class="w-full h-full object-cover group-hover:scale-105 transition duration-300"
                                 loading="lazy"
                                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                            <div class="hidden w-full h-full items-center justify-center text-3xl">🍽️</div>
                        @else
                            <div class="w-full h-full flex items-center justify-center text-3xl">🇲🇽</div>
                        @endif
                    </div>

                    {{-- Info --}}
                    <div class="flex-1 min-w-0">
                        <div class="flex items-start justify-between gap-2">
                            <h3 class="font-bold text-[#F5F5F5] group-hover:text-[#D4AF37] transition-colors truncate">
                                {{ $restaurant->name }}
                            </h3>
                            <div class="flex gap-1 flex-shrink-0">
                                @if($isElite)
                                    <span class="bg-[#D4AF37]/10 text-[#D4AF37] text-xs px-2 py-0.5 rounded-full border border-[#D4AF37]/30 font-semibold whitespace-nowrap">Destacado</span>
                                @elseif($isPremium)
                                    <span style="background:rgba(212,175,55,0.08); color:#D4AF37; border:1px solid rgba(212,175,55,0.25);" class="text-xs px-2 py-0.5 rounded-full font-semibold whitespace-nowrap">Premium</span>
                                @endif
                                @if($restaurant->is_claimed)
                                    <span style="background:rgba(212,175,55,0.06); color:#D4AF37; border:1px solid rgba(212,175,55,0.15);" class="text-xs px-2 py-0.5 rounded-full whitespace-nowrap">Verificado</span>
                                @endif
                            </div>
                        </div>

                        {{-- Rating --}}
                        @if($weightedRating > 0)
                        <div class="flex items-center gap-2 mt-1.5">
                            <div class="flex text-[#D4AF37]">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= round($weightedRating))
                                        <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/></svg>
                                    @else
                                        <svg class="w-3.5 h-3.5 fill-current text-gray-600" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/></svg>
                                    @endif
                                @endfor
                            </div>
                            <span class="text-sm text-gray-300 font-semibold">{{ number_format($weightedRating, 1) }}</span>
                            <span class="text-xs text-gray-500">({{ number_format($combinedReviews) }} reseñas)</span>
                        </div>
                        @endif

                        {{-- City & Category --}}
                        <div class="flex items-center gap-3 mt-1.5 flex-wrap">
                            @if($restaurant->city)
                            <p class="text-xs text-gray-500">
                                <svg class="w-3 h-3 inline mr-0.5 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                {{ $restaurant->city }}
                            </p>
                            @endif
                            @if($restaurant->category)
                            <span class="text-xs bg-[#2A2A2A] text-gray-400 px-2 py-0.5 rounded-full">{{ $restaurant->category->name }}</span>
                            @endif
                        </div>
                    </div>
                </a>
                @endforeach
            </div>
        </section>
        @endif

        {{-- ===================== TOP CITIES GRID ===================== --}}
        <section class="mb-14">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-1 h-8 bg-[#D4AF37] rounded-full"></div>
                <h2 class="text-2xl font-bold text-[#F5F5F5]" style="font-family: 'Playfair Display', serif;">
                    Ciudades con Restaurantes Mexicanos en {{ $state->name }}
                </h2>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
                @foreach($cities as $city)
                <a href="{{ route('city-guides.city', [$state->code, Str::slug($city->city)]) }}"
                   class="bg-[#1A1A1A] border border-[#2A2A2A] rounded-xl p-5 hover:border-[#D4AF37]/40 hover:bg-[#1F1F1F] transition-all group">
                    <div class="flex flex-wrap items-start justify-between gap-2 mb-3">
                        <h3 class="text-lg font-bold text-[#F5F5F5] group-hover:text-[#D4AF37] transition-colors">
                            {{ $city->city }}
                        </h3>
                        <span class="bg-[#D4AF37]/10 text-[#D4AF37] text-xs px-2.5 py-1 rounded-full font-medium whitespace-nowrap border border-[#D4AF37]/20">
                            {{ $city->count }} restaurantes
                        </span>
                    </div>

                    <div class="flex flex-wrap items-center text-sm text-gray-500 gap-3">
                        @if($city->avg_rating)
                        <div class="flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 text-[#D4AF37] fill-current" viewBox="0 0 20 20">
                                <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                            </svg>
                            <span>{{ number_format($city->avg_rating, 1) }} promedio</span>
                        </div>
                        @endif

                        @if($city->claimed_count > 0)
                        <div class="flex items-center gap-1" style="color:#D4AF37;">
                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span>{{ $city->claimed_count }} verificados</span>
                        </div>
                        @endif
                    </div>

                    <p class="text-gray-600 text-xs mt-3 group-hover:text-gray-400 transition-colors">
                        Ver restaurantes en {{ $city->city }} →
                    </p>
                </a>
                @endforeach
            </div>

            @if($cities->isEmpty())
            <div class="bg-[#1A1A1A] border border-[#2A2A2A] rounded-xl p-8 text-center">
                <p class="text-gray-500">No se encontraron ciudades con restaurantes mexicanos en {{ $state->name }}.</p>
                <a href="{{ route('restaurants.index') }}" class="text-[#D4AF37] hover:underline mt-2 inline-block">
                    Ver todos los restaurantes
                </a>
            </div>
            @endif
        </section>

        {{-- ===================== SEO CONTENT ===================== --}}
        <section class="bg-[#1A1A1A] border border-[#2A2A2A] rounded-xl p-8">
            <h2 class="text-2xl font-bold text-[#F5F5F5] mb-4" style="font-family: 'Playfair Display', serif;">
                Comida Mexicana en {{ $state->name }}
            </h2>
            <div class="text-gray-400 space-y-4 text-sm leading-relaxed">
                <p>
                    {{ $state->name }} cuenta con {{ number_format($stats->total) }} restaurantes mexicanos
                    distribuidos en {{ $cities->count() }} ciudades. La escena culinaria mexicana en el estado
                    ofrece desde autenticas taquerias hasta restaurantes de alta cocina.
                </p>
                <p>
                    Los restaurantes mexicanos en {{ $state->name }} tienen un rating promedio de
                    {{ number_format($stats->avg_rating ?? 0, 1) }} estrellas. Encuentra tacos, burritos,
                    enchiladas, tamales, pozole, mole y mucha mas comida mexicana autentica.
                </p>
            </div>
        </section>

    </div>
</div>

@push('scripts')
{{-- BreadcrumbList Schema --}}
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "BreadcrumbList",
    "itemListElement": [
        {
            "@@type": "ListItem",
            "position": 1,
            "name": "Inicio",
            "item": "{{ url('/') }}"
        },
        {
            "@@type": "ListItem",
            "position": 2,
            "name": "Guía por Ciudad",
            "item": "{{ route('city-guides.states') }}"
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

{{-- ItemList Schema — Top 10 Restaurants --}}
@if($top10Restaurants->isNotEmpty())
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "ItemList",
    "name": "Top {{ $top10Restaurants->count() }} Mejores Restaurantes Mexicanos en {{ $state->name }}",
    "description": "Los {{ $top10Restaurants->count() }} mejores restaurantes mexicanos en {{ $state->name }} ordenados por reseñas verificadas de Google.",
    "numberOfItems": {{ $top10Restaurants->count() }},
    "itemListElement": [
        @foreach($top10Restaurants as $index => $restaurant)
        {
            "@@type": "ListItem",
            "position": {{ $index + 1 }},
            "item": {
                "@@type": "Restaurant",
                "name": "{{ addslashes($restaurant->name) }}",
                "url": "{{ route('restaurants.show', $restaurant->slug) }}"@if($restaurant->city),
                "address": {
                    "@@type": "PostalAddress",
                    "addressLocality": "{{ addslashes($restaurant->city) }}",
                    "addressRegion": "{{ $state->code }}",
                    "addressCountry": "{{ $state->country ?? 'US' }}"
                }@endif@if($restaurant->latitude && $restaurant->longitude),
                "geo": {
                    "@@type": "GeoCoordinates",
                    "latitude": {{ $restaurant->latitude }},
                    "longitude": {{ $restaurant->longitude }}
                }@endif,
                "servesCuisine": "Mexican"@if($restaurant->price_range),
                "priceRange": "{{ $restaurant->price_range }}"@endif@php $schemaRating = $restaurant->getWeightedRating(); @endphp@if($schemaRating > 0),
                "aggregateRating": {
                    "@@type": "AggregateRating",
                    "ratingValue": "{{ number_format($schemaRating, 1) }}",
                    "bestRating": "5",
                    "ratingCount": "{{ $restaurant->getCombinedReviewCount() }}"
                }@endif
            }
        }{{ $loop->last ? '' : ',' }}
        @endforeach
    ]
}
</script>
@endif

{{-- ItemList Schema — Cities --}}
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "ItemList",
    "name": "Ciudades con Restaurantes Mexicanos en {{ $state->name }}",
    "description": "Lista de {{ $cities->count() }} ciudades con restaurantes mexicanos en {{ $state->name }}",
    "numberOfItems": {{ $cities->count() }},
    "itemListElement": [
        @foreach($cities->take(10) as $index => $city)
        {
            "@@type": "ListItem",
            "position": {{ $index + 1 }},
            "item": {
                "@@type": "City",
                "name": "{{ addslashes($city->city) }}",
                "url": "{{ route('city-guides.city', [$state->code, Str::slug($city->city)]) }}",
                "containedInPlace": {
                    "@@type": "State",
                    "name": "{{ $state->name }}"
                }
            }
        }{{ $loop->last ? '' : ',' }}
        @endforeach
    ]
}
</script>
@endpush

@endsection
