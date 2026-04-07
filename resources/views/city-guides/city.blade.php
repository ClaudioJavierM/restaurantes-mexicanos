@extends('layouts.app')

@section('title')Mejores Restaurantes Mexicanos en {{ $cityName }}, {{ $state->name }} | FAMER@endsection
@section('meta_description')Descubre los {{ $stats->total }} mejores restaurantes mexicanos en {{ $cityName }}, {{ $state->name }}. Calificaciones verificadas, menús, horarios y más.@endsection

@push('meta')
<!-- Open Graph / Facebook -->
<meta property="og:type" content="website">
<meta property="og:url" content="{{ url()->current() }}">
<meta property="og:title" content="Mejores Restaurantes Mexicanos en {{ $cityName }}, {{ $state->name }} | FAMER">
<meta property="og:description" content="Descubre los {{ $stats->total }} mejores restaurantes mexicanos en {{ $cityName }}, {{ $state->name }}. Calificaciones verificadas, menús, horarios y más.">
@php
    $firstRest = $top10Restaurants->first();
    $ogCityImage = null;
    if ($firstRest) {
        if ($firstRest->image) {
            $ogCityImage = str_starts_with($firstRest->image, 'http') ? $firstRest->image : asset('storage/' . $firstRest->image);
        } elseif ($firstRest->getFirstMediaUrl('images')) {
            $ogCityImage = $firstRest->getFirstMediaUrl('images');
        }
    }
@endphp
@if($ogCityImage)
<meta property="og:image" content="{{ $ogCityImage }}">
@endif

<!-- Twitter Card -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="Mejores Restaurantes Mexicanos en {{ $cityName }}, {{ $state->name }}">
<meta name="twitter:description" content="{{ $stats->total }} restaurantes mexicanos verificados en {{ $cityName }}. Rating promedio: {{ number_format($stats->avg_rating ?? 0, 1) }}">

<!-- SEO: Geo Tags for Local Search -->
<meta name="geo.region" content="{{ $state->country === 'MX' ? 'MX' : 'US' }}-{{ $state->code }}">
<meta name="geo.placename" content="{{ $cityName }}, {{ $state->name }}">
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
            <a href="{{ route('city-guides.state', strtolower($state->code)) }}" class="hover:text-[#D4AF37] transition-colors">{{ $state->name }}</a>
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <span class="text-[#F5F5F5]">{{ $cityName }}</span>
        </nav>

        {{-- H1 --}}
        <h1 class="text-3xl md:text-5xl font-bold text-[#F5F5F5] mb-4" style="font-family: 'Playfair Display', serif;">
            Mejores Restaurantes Mexicanos en <span class="text-[#D4AF37]">{{ $cityName }}</span>
        </h1>
        <p class="text-lg text-gray-400 max-w-3xl">
            Guía completa con los {{ $stats->total }} mejores restaurantes de comida mexicana en {{ $cityName }}, {{ $state->name }}.
            Calificaciones de Google verificadas, menús, horarios y más.
        </p>

        {{-- Summary Stats --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-8">
            <div class="bg-[#1A1A1A] border border-[#2A2A2A] rounded-xl p-5 text-center">
                <p class="text-3xl font-bold text-[#D4AF37]">{{ number_format($stats->total) }}</p>
                <p class="text-sm text-gray-400 mt-1">Restaurantes</p>
            </div>
            <div class="bg-[#1A1A1A] border border-[#2A2A2A] rounded-xl p-5 text-center">
                <p class="text-3xl font-bold text-[#D4AF37]">{{ number_format($stats->avg_rating ?? 0, 1) }}</p>
                <p class="text-sm text-gray-400 mt-1">Rating Promedio</p>
            </div>
            <div class="bg-[#1A1A1A] border border-[#2A2A2A] rounded-xl p-5 text-center">
                <p class="text-3xl font-bold text-[#D4AF37]">{{ number_format($stats->total_reviews ?? 0) }}</p>
                <p class="text-sm text-gray-400 mt-1">Reseñas en Google</p>
            </div>
            <div class="bg-[#1A1A1A] border border-[#2A2A2A] rounded-xl p-5 text-center">
                <p class="text-3xl font-bold text-[#D4AF37]">{{ $stats->category_count ?? $topCategories->count() }}</p>
                <p class="text-sm text-gray-400 mt-1">Categorías</p>
            </div>
        </div>
    </div>
</div>

<div class="bg-[#0B0B0B] min-h-screen">
    <div class="max-w-7xl mx-auto px-4 py-10 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">

            {{-- ===================== SIDEBAR ===================== --}}
            <aside class="lg:col-span-1 order-last lg:order-first">
                <div class="bg-[#1A1A1A] border border-[#2A2A2A] rounded-xl p-6 sticky top-24">

                    <h3 class="font-bold text-[#F5F5F5] mb-4 text-sm uppercase tracking-wider">Categorías Populares</h3>
                    <ul class="space-y-2">
                        @foreach($topCategories as $cat)
                            @if($cat->category)
                            <li>
                                <a href="{{ route('restaurants.index', ['category' => $cat->category->slug, 'state' => $state->code]) }}"
                                   class="flex justify-between items-center text-gray-400 hover:text-[#D4AF37] transition-colors text-sm py-1">
                                    <span>{{ $cat->category->name }}</span>
                                    <span class="bg-[#2A2A2A] text-gray-400 text-xs px-2 py-0.5 rounded-full">{{ $cat->count }}</span>
                                </a>
                            </li>
                            @endif
                        @endforeach
                    </ul>

                    <div class="border-t border-[#2A2A2A] my-6"></div>

                    <h3 class="font-bold text-[#F5F5F5] mb-3 text-sm uppercase tracking-wider">¿Eres Dueño?</h3>
                    <p class="text-sm text-gray-400 mb-4">
                        Reclama tu restaurante gratis y toma control de tu perfil en FAMER.
                    </p>
                    <a href="{{ route('claim.restaurant') }}"
                       class="block w-full text-center bg-[#D4AF37] text-[#0B0B0B] py-2.5 rounded-lg font-bold text-sm hover:bg-[#E8C67A] transition-colors">
                        Reclamar Gratis
                    </a>

                    <div class="border-t border-[#2A2A2A] my-6"></div>

                    <h3 class="font-bold text-[#F5F5F5] mb-3 text-sm uppercase tracking-wider">Más en {{ $state->name }}</h3>
                    <a href="{{ route('city-guides.state', strtolower($state->code)) }}"
                       class="flex items-center gap-2 text-sm text-gray-400 hover:text-[#D4AF37] transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        Ver todas las ciudades
                    </a>
                </div>
            </aside>

            {{-- ===================== MAIN CONTENT ===================== --}}
            <main class="lg:col-span-3">

                {{-- ===== TOP 10 RANKED LIST ===== --}}
                <section class="mb-12">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-1 h-8 bg-[#D4AF37] rounded-full"></div>
                        <h2 class="text-2xl font-bold text-[#F5F5F5]" style="font-family: 'Playfair Display', serif;">
                            Top {{ $top10Restaurants->count() }} Mejores Restaurantes Mexicanos en {{ $cityName }}
                        </h2>
                    </div>
                    <p class="text-gray-400 text-sm mb-6">Ordenados por número de reseñas en Google y calificación verificada.</p>

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
                            $topRestImage = $restaurant->getDisplayImageUrl();
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

                                {{-- Address & Category --}}
                                <div class="flex items-center gap-3 mt-1.5 flex-wrap">
                                    @if($restaurant->address)
                                    <p class="text-xs text-gray-500 truncate">
                                        <svg class="w-3 h-3 inline mr-0.5 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                        {{ $restaurant->address }}
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

                {{-- ===== FULL LIST (Paginated) ===== --}}
                @if($restaurants->total() > 10)
                <section>
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-1 h-8 bg-[#D4AF37] rounded-full"></div>
                        <h2 class="text-xl font-bold text-[#F5F5F5]" style="font-family: 'Playfair Display', serif;">
                            Todos los Restaurantes en {{ $cityName }}
                        </h2>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        @foreach($restaurants as $index => $restaurant)
                        @php
                            $isEliteCard = $restaurant->subscription_tier === 'elite';
                            $isPremiumCard = $restaurant->subscription_tier === 'premium';
                            $cardRestImage = $restaurant->getDisplayImageUrl();
                            $cardRating = $restaurant->getWeightedRating();
                            $cardReviews = $restaurant->getCombinedReviewCount();
                        @endphp
                        <a href="{{ route('restaurants.show', $restaurant->slug) }}"
                           class="bg-[#1A1A1A] border {{ $isEliteCard ? 'border-[#D4AF37]/40' : ($isPremiumCard ? 'border-[#D4AF37]/15' : 'border-[#2A2A2A]') }} rounded-xl overflow-hidden hover:border-[#D4AF37]/30 hover:bg-[#1F1F1F] transition-all group">

                            {{-- Image --}}
                            <div class="relative h-44 bg-[#2A2A2A]">
                                @if($cardRestImage)
                                    <img src="{{ $cardRestImage }}"
                                         alt="{{ $restaurant->name }}"
                                         class="w-full h-full object-cover group-hover:scale-105 transition duration-300"
                                         loading="lazy"
                                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                    <div class="hidden w-full h-full items-center justify-center text-4xl">🍽️</div>
                                @else
                                    <div class="w-full h-full flex flex-col items-center justify-center gap-2">
                                        <span class="text-4xl">🇲🇽</span>
                                        <span class="text-xs text-gray-500">¿Eres el dueño?</span>
                                    </div>
                                @endif

                                {{-- Badges --}}
                                <div class="absolute top-3 left-3 flex gap-1">
                                    @if($isEliteCard)
                                        <span class="bg-[#D4AF37] text-[#0B0B0B] text-xs px-2.5 py-1 rounded-full font-bold shadow flex items-center gap-1">
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                            Destacado
                                        </span>
                                    @elseif($isPremiumCard)
                                        <span style="background:rgba(212,175,55,0.15); color:#D4AF37; border:1px solid rgba(212,175,55,0.3);" class="text-xs px-2.5 py-1 rounded-full font-semibold shadow">Premium</span>
                                    @endif
                                </div>
                                @if($restaurant->is_claimed)
                                <div class="absolute top-3 right-3">
                                    <span style="background:rgba(212,175,55,0.1); color:#D4AF37; border:1px solid rgba(212,175,55,0.25);" class="text-xs px-2 py-1 rounded">Verificado</span>
                                </div>
                                @endif
                            </div>

                            {{-- Content --}}
                            <div class="p-4">
                                <h3 class="font-bold text-[#F5F5F5] group-hover:text-[#D4AF37] transition-colors">
                                    {{ $restaurant->name }}
                                </h3>

                                @if($cardRating > 0)
                                <div class="flex items-center gap-2 mt-2">
                                    <div class="flex text-[#D4AF37]">
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($i <= round($cardRating))
                                                <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/></svg>
                                            @else
                                                <svg class="w-3.5 h-3.5 fill-current text-gray-600" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/></svg>
                                            @endif
                                        @endfor
                                    </div>
                                    <span class="text-sm text-gray-300 font-medium">{{ number_format($cardRating, 1) }}</span>
                                    <span class="text-xs text-gray-500">({{ number_format($cardReviews) }})</span>
                                </div>
                                @endif

                                <p class="text-xs text-gray-500 mt-2 truncate">{{ $restaurant->address }}</p>

                                @if($restaurant->category)
                                <span class="inline-block mt-2 text-xs bg-[#2A2A2A] text-gray-400 px-2 py-0.5 rounded-full">
                                    {{ $restaurant->category->name }}
                                </span>
                                @endif
                            </div>
                        </a>
                        @endforeach
                    </div>

                    {{-- Pagination --}}
                    <div class="mt-8">
                        {{ $restaurants->links() }}
                    </div>
                </section>
                @endif

                {{-- ===== SEO Content Block ===== --}}
                <section class="mt-12 bg-[#1A1A1A] border border-[#2A2A2A] rounded-xl p-8">
                    <h2 class="text-xl font-bold text-[#F5F5F5] mb-4" style="font-family: 'Playfair Display', serif;">
                        Guía de Comida Mexicana en {{ $cityName }}, {{ $state->name }}
                    </h2>
                    <div class="text-gray-400 space-y-4 text-sm leading-relaxed">
                        <p>
                            {{ $cityName }} cuenta con una vibrante escena de restaurantes mexicanos que ofrecen desde
                            auténtica comida callejera hasta experiencias gastronómicas de alta cocina. Nuestra guía
                            incluye <strong class="text-[#F5F5F5]">{{ $stats->total }} restaurantes verificados</strong> con datos de Google
                            para ayudarte a encontrar el lugar perfecto.
                        </p>
                        <p>
                            Los restaurantes mexicanos en {{ $cityName }} tienen un rating promedio de
                            <strong class="text-[#D4AF37]">{{ number_format($stats->avg_rating ?? 0, 1) }} estrellas</strong> basado en
                            más de <strong class="text-[#F5F5F5]">{{ number_format($stats->total_reviews ?? 0) }} reseñas</strong> de clientes reales.
                            Encuentra tacos, burritos, enchiladas, tamales, pozole y mucho más.
                        </p>
                        @if($topCategories->isNotEmpty())
                        <p>
                            Las categorías más populares en {{ $cityName }} incluyen:
                            @foreach($topCategories->take(3) as $cat)
                                @if($cat->category)<strong class="text-[#F5F5F5]">{{ $cat->category->name }}</strong>{{ !$loop->last ? ', ' : '' }}@endif
                            @endforeach
                            — con opciones para todos los gustos y presupuestos.
                        </p>
                        @endif
                    </div>
                </section>

            </main>
        </div>
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
            "item": "{{ route('city-guides.state', strtolower($state->code)) }}"
        },
        {
            "@@type": "ListItem",
            "position": 4,
            "name": "{{ $cityName }}",
            "item": "{{ url()->current() }}"
        }
    ]
}
</script>

{{-- ItemList Schema for Top 10 --}}
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "ItemList",
    "name": "Mejores Restaurantes Mexicanos en {{ $cityName }}, {{ $state->name }}",
    "description": "Top {{ $top10Restaurants->count() }} mejores restaurantes mexicanos en {{ $cityName }}, {{ $state->name }}. Calificaciones verificadas de Google.",
    "numberOfItems": {{ $top10Restaurants->count() }},
    "itemListElement": [
        @foreach($top10Restaurants as $schemaIndex => $schemaRest)
        @php
            $schemaRestRating = $schemaRest->getWeightedRating();
            $schemaItem = [
                '@type' => 'Restaurant',
                'name' => $schemaRest->name,
                'url' => route('restaurants.show', $schemaRest->slug),
                'servesCuisine' => 'Mexican',
            ];
            if ($schemaRest->address || $schemaRest->city) {
                $addr = ['@type' => 'PostalAddress', 'addressLocality' => $cityName, 'addressRegion' => $state->code, 'addressCountry' => $state->country ?? 'US'];
                if ($schemaRest->address) $addr['streetAddress'] = $schemaRest->address;
                $schemaItem['address'] = $addr;
            }
            if ($schemaRest->latitude && $schemaRest->longitude) {
                $schemaItem['geo'] = ['@type' => 'GeoCoordinates', 'latitude' => (float)$schemaRest->latitude, 'longitude' => (float)$schemaRest->longitude];
            }
            if ($schemaRest->price_range) $schemaItem['priceRange'] = $schemaRest->price_range;
            if ($schemaRestRating > 0) {
                $schemaItem['aggregateRating'] = ['@type' => 'AggregateRating', 'ratingValue' => number_format($schemaRestRating, 1), 'bestRating' => '5', 'ratingCount' => $schemaRest->getCombinedReviewCount()];
            }
            $schemaListItem = ['@type' => 'ListItem', 'position' => $schemaIndex + 1, 'item' => $schemaItem];
        @endphp
        {!! json_encode($schemaListItem, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}{{ $loop->last ? '' : ',' }}
        @endforeach
    ]
}
</script>

{{-- WebPage Schema --}}
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "WebPage",
    "name": "Mejores Restaurantes Mexicanos en {{ $cityName }}, {{ $state->name }}",
    "description": "Descubre los {{ $stats->total }} mejores restaurantes mexicanos en {{ $cityName }}, {{ $state->name }}. Calificaciones verificadas, menús, horarios y más.",
    "url": "{{ url()->current() }}",
    "inLanguage": "es",
    "about": {
        "@@type": "City",
        "name": "{{ $cityName }}",
        "containedInPlace": {
            "@@type": "State",
            "name": "{{ $state->name }}"
        }
    }
}
</script>
@endpush
@endsection
