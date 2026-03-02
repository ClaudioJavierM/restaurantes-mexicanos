@extends('layouts.app')

@section('title', "Los Mejores Restaurantes Mexicanos en {$cityName}, {$state->code} | Guia Completa")
@section('meta_description', "Descubre los {$stats->total} mejores restaurantes mexicanos en {$cityName}, {$state->name}. Rating promedio: {$stats->avg_rating}. Encuentra tacos, burritos, enchiladas y mas cerca de ti.")

@push('meta')
<!-- Open Graph / Facebook -->
<meta property="og:type" content="website">
<meta property="og:url" content="{{ url()->current() }}">
<meta property="og:title" content="Los Mejores Restaurantes Mexicanos en {{ $cityName }}, {{ $state->code }}">
<meta property="og:description" content="Descubre los {{ $stats->total }} mejores restaurantes mexicanos en {{ $cityName }}. Rating promedio: {{ number_format($stats->avg_rating ?? 0, 1) }} estrellas.">
@php
    $firstRest = $restaurants->first();
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
<meta name="twitter:title" content="Restaurantes Mexicanos en {{ $cityName }}, {{ $state->code }}">
<meta name="twitter:description" content="{{ $stats->total }} restaurantes mexicanos verificados. Rating promedio: {{ number_format($stats->avg_rating ?? 0, 1) }}">

<!-- SEO: Geo Tags for Local Search -->
<meta name="geo.region" content="US-{{ $state->code }}">
<meta name="geo.placename" content="{{ $cityName }}, {{ $state->name }}">
@endpush

@section('content')
<div class="bg-gradient-to-br from-emerald-600 via-green-600 to-red-600 text-white">
    <div class="max-w-7xl mx-auto px-4 py-12 sm:px-6 lg:px-8">
        <!-- Breadcrumb -->
        <nav class="text-sm mb-4 opacity-90">
            <a href="{{ route('city-guides.states') }}" class="hover:underline">Estados</a>
            <span class="mx-2">/</span>
            <a href="{{ route('city-guides.state', $state->code) }}" class="hover:underline">{{ $state->name }}</a>
            <span class="mx-2">/</span>
            <span>{{ $cityName }}</span>
        </nav>

        <h1 class="text-3xl md:text-4xl font-bold mb-4">
            Restaurantes Mexicanos en {{ $cityName }}, {{ $state->code }}
        </h1>
        <p class="text-lg opacity-90 max-w-3xl">
            Guia completa de los mejores restaurantes de comida mexicana en {{ $cityName }}.
            Encuentra desde taquerias autenticas hasta restaurantes gourmet.
        </p>

        <!-- Stats -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-8">
            <div class="bg-white/10 backdrop-blur rounded-lg p-4 text-center">
                <p class="text-3xl font-bold">{{ $stats->total }}</p>
                <p class="text-sm opacity-80">Restaurantes</p>
            </div>
            <div class="bg-white/10 backdrop-blur rounded-lg p-4 text-center">
                <p class="text-3xl font-bold">{{ number_format($stats->avg_rating ?? 0, 1) }}</p>
                <p class="text-sm opacity-80">Rating Promedio</p>
            </div>
            <div class="bg-white/10 backdrop-blur rounded-lg p-4 text-center">
                <p class="text-3xl font-bold">{{ number_format($stats->total_reviews ?? 0) }}</p>
                <p class="text-sm opacity-80">Resenas Totales</p>
            </div>
            <div class="bg-white/10 backdrop-blur rounded-lg p-4 text-center">
                <p class="text-3xl font-bold">{{ $stats->claimed_count ?? 0 }}</p>
                <p class="text-sm opacity-80">Verificados</p>
            </div>
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 py-8 sm:px-6 lg:px-8">
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        <!-- Sidebar -->
        <aside class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow p-6 sticky top-4">
                <h3 class="font-bold text-gray-900 mb-4">Categorias Populares</h3>
                <ul class="space-y-2">
                    @foreach($topCategories as $cat)
                        @if($cat->category)
                        <li>
                            <a href="{{ route('restaurants.index', ['category' => $cat->category->slug, 'state' => $state->code]) }}"
                               class="flex justify-between items-center text-gray-600 hover:text-emerald-600">
                                <span>{{ $cat->category->name }}</span>
                                <span class="bg-gray-100 text-gray-600 text-xs px-2 py-1 rounded">{{ $cat->count }}</span>
                            </a>
                        </li>
                        @endif
                    @endforeach
                </ul>

                <hr class="my-6">

                <h3 class="font-bold text-gray-900 mb-4">Eres Dueno?</h3>
                <p class="text-sm text-gray-600 mb-4">
                    Reclama tu restaurante gratis y toma control de tu perfil.
                </p>
                <a href="{{ route('claim.restaurant') }}"
                   class="block w-full text-center bg-emerald-600 text-white py-2 rounded-lg hover:bg-emerald-700 transition">
                    Reclamar Gratis
                </a>
            </div>
        </aside>

        <!-- Restaurant Grid -->
        <main class="lg:col-span-3">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">
                Mejores Restaurantes Mexicanos en {{ $cityName }}
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @foreach($restaurants as $index => $restaurant)
                @php
                    $isElite = $restaurant->subscription_tier === 'elite';
                    $isPremium = $restaurant->subscription_tier === 'premium';
                    $cardClass = $isElite ? 'ring-2 ring-yellow-400 bg-gradient-to-br from-yellow-50 to-white' :
                                ($isPremium ? 'ring-2 ring-emerald-400 bg-gradient-to-br from-emerald-50 to-white' : 'bg-white');
                @endphp
                <a href="{{ route('restaurants.show', $restaurant->slug) }}"
                   class="{{ $cardClass }} rounded-lg shadow hover:shadow-lg transition overflow-hidden group">
                    <!-- Image -->
                    <div class="relative h-48 bg-gray-100">
                        @php
                            $cityRestImage = null;
                            if ($restaurant->image) {
                                $cityRestImage = str_starts_with($restaurant->image, 'http') ? $restaurant->image : asset('storage/' . $restaurant->image);
                            } elseif ($restaurant->getFirstMediaUrl('images')) {
                                $cityRestImage = $restaurant->getFirstMediaUrl('images');
                            }
                        @endphp
                        @if($cityRestImage)
                            <img src="{{ $cityRestImage }}"
                                 alt="{{ $restaurant->name }}"
                                 class="w-full h-full object-cover group-hover:scale-105 transition duration-300"
                                 loading="lazy"
                                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                            <div class="hidden w-full h-full relative bg-gradient-to-br from-green-600 via-white to-red-600 overflow-hidden items-center justify-center">
                                <span class="text-5xl">🍽️</span>
                            </div>
                        @else
                            <div class="w-full h-full relative bg-gradient-to-br from-green-600 via-white to-red-600 overflow-hidden">
                                <!-- Decorative pattern -->
                                <div class="absolute inset-0 opacity-10">
                                    <div class="absolute top-2 left-4 text-4xl">🌮</div>
                                    <div class="absolute top-8 right-6 text-3xl">🌶️</div>
                                    <div class="absolute bottom-6 left-8 text-3xl">🫔</div>
                                    <div class="absolute bottom-2 right-4 text-4xl">🍹</div>
                                </div>
                                <!-- Center content -->
                                <div class="absolute inset-0 flex flex-col items-center justify-center">
                                    <span class="text-5xl mb-2">🇲🇽</span>
                                    <span class="text-xs font-medium text-gray-700 bg-white/80 px-2 py-1 rounded">¿Eres el dueño?</span>
                                </div>
                            </div>
                        @endif

                        <!-- Elite/Premium Badge OR Rank Badge -->
                        <div class="absolute top-3 left-3">
                            @if($isElite)
                                <span class="bg-gradient-to-r from-yellow-400 to-amber-500 text-white text-xs px-2.5 py-1 rounded-full font-semibold shadow flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                    Destacado
                                </span>
                            @elseif($isPremium)
                                <span class="bg-gradient-to-r from-emerald-500 to-green-600 text-white text-xs px-2.5 py-1 rounded-full font-semibold shadow flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                    Recomendado
                                </span>
                            @elseif($index < 3)
                                <div class="{{ $index === 0 ? 'bg-yellow-400' : ($index === 1 ? 'bg-gray-300' : 'bg-amber-600') }} text-white w-8 h-8 rounded-full flex items-center justify-center font-bold shadow">
                                    {{ $index + 1 }}
                                </div>
                            @endif
                        </div>

                        <!-- Verified Badge -->
                        <div class="absolute top-3 right-3 flex gap-1">
                            @if($restaurant->is_claimed)
                                <span class="bg-emerald-500 text-white text-xs px-2 py-1 rounded">Verificado</span>
                            @endif
                        </div>

                        <!-- Data source icons -->
                        <div class="absolute bottom-2 right-2 flex gap-1">
                            @if($restaurant->google_place_id)
                                <span class="bg-white/90 rounded px-1.5 py-0.5 text-xs font-medium flex items-center gap-1" title="Datos de Google">
                                    <svg class="w-3 h-3" viewBox="0 0 24 24"><path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/><path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/><path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/><path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/></svg>
                                </span>
                            @endif
                            @if($restaurant->yelp_id)
                                <span class="bg-white/90 rounded px-1.5 py-0.5 text-xs font-medium flex items-center gap-1" title="Datos de Yelp">
                                    <svg class="w-3 h-3" viewBox="0 0 24 24"><path fill="#FF1A1A" d="M12.14 11.94l3.54-2.56c.2-.15.52-.07.62.18.1.25.02.55-.18.7l-3.54 2.56c-.2.15-.52.07-.62-.18-.1-.25-.02-.55.18-.7zm-1.46 4.26l.7-4.3c.03-.25.27-.43.52-.4.25.03.43.27.4.52l-.7 4.3c-.03.25-.27.43-.52.4-.25-.03-.43-.27-.4-.52zm-2.76-2.4l3.88-1.52c.24-.09.5.03.59.27.09.24-.03.5-.27.59l-3.88 1.52c-.24.09-.5-.03-.59-.27-.09-.24.03-.5.27-.59zm1.08-5.3l2.88 3.36c.17.2.15.5-.05.67-.2.17-.5.15-.67-.05l-2.88-3.36c-.17-.2-.15-.5.05-.67.2-.17.5-.15.67.05zM12 1C5.92 1 1 5.92 1 12s4.92 11 11 11 11-4.92 11-11S18.08 1 12 1z"/></svg>
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Content -->
                    <div class="p-4">
                        <h3 class="font-bold text-gray-900 group-hover:text-emerald-600 transition">
                            {{ $restaurant->name }}
                        </h3>

                        <!-- Rating (weighted average) -->
                        @php
                            $cityWeightedRating = $restaurant->getWeightedRating();
                            $cityCombinedReviews = $restaurant->getCombinedReviewCount();
                        @endphp
                        @if($cityWeightedRating > 0)
                        <div class="flex items-center mt-2">
                            <div class="flex text-yellow-400">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= round($cityWeightedRating))
                                        <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/></svg>
                                    @else
                                        <svg class="w-4 h-4 fill-current text-gray-300" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/></svg>
                                    @endif
                                @endfor
                            </div>
                            <span class="ml-2 text-sm text-gray-600">{{ number_format($cityWeightedRating, 1) }} ({{ $cityCombinedReviews }} resenas)</span>
                        </div>
                        @endif

                        <!-- Address -->
                        <p class="text-sm text-gray-500 mt-2">
                            {{ $restaurant->address }}
                        </p>

                        <!-- Category -->
                        @if($restaurant->category)
                        <span class="inline-block mt-2 text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded">
                            {{ $restaurant->category->name }}
                        </span>
                        @endif
                    </div>
                </a>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-8">
                {{ $restaurants->links() }}
            </div>
        </main>
    </div>

    <!-- SEO Content -->
    <section class="mt-12 bg-gray-50 rounded-lg p-8">
        <h2 class="text-2xl font-bold text-gray-900 mb-4">
            Guia de Comida Mexicana en {{ $cityName }}, {{ $state->name }}
        </h2>
        <div class="prose max-w-none text-gray-600">
            <p>
                {{ $cityName }} cuenta con una vibrante escena de restaurantes mexicanos que ofrecen desde
                autentica comida callejera hasta experiencias gastronomicas de alta cocina. Nuestra guia
                incluye {{ $stats->total }} restaurantes verificados con datos de Yelp y Google para
                ayudarte a encontrar el lugar perfecto.
            </p>
            <p class="mt-4">
                Los restaurantes mexicanos en {{ $cityName }} tienen un rating promedio de
                {{ number_format($stats->avg_rating ?? 0, 1) }} estrellas basado en
                {{ number_format($stats->total_reviews ?? 0) }} resenas de clientes reales.
                Encuentra tacos, burritos, enchiladas, tamales, pozole y mucho mas.
            </p>
        </div>
    </section>
</div>

@push('scripts')
<!-- BreadcrumbList Schema -->
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
            "item": "{{ route('city-guides.state', $state->code) }}"
        },
        {
            "@@type": "ListItem",
            "position": 4,
            "name": "{{ $cityName }}"
        }
    ]
}
</script>

<!-- ItemList Schema for Restaurant Results -->
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "ItemList",
    "name": "Mejores Restaurantes Mexicanos en {{ $cityName }}, {{ $state->code }}",
    "description": "Lista de los {{ $stats->total }} mejores restaurantes de comida mexicana en {{ $cityName }}, {{ $state->name }}",
    "numberOfItems": {{ $stats->total }},
    "itemListElement": [
        @foreach($restaurants->take(10) as $index => $restaurant)
        {
            "@@type": "ListItem",
            "position": {{ $index + 1 }},
            "item": {
                "@@type": "Restaurant",
                "@@id": "{{ route('restaurants.show', $restaurant->slug) }}",
                "name": "{{ $restaurant->name }}",
                "servesCuisine": "Mexican",
                "priceRange": "{{ $restaurant->price_range ?? '$$' }}",
                "address": {
                    "@@type": "PostalAddress",
                    "streetAddress": "{{ $restaurant->address }}",
                    "addressLocality": "{{ $restaurant->city }}",
                    "addressRegion": "{{ $state->code }}",
                    "addressCountry": "US"
                },
                "aggregateRating": {
                    "@@type": "AggregateRating",
                    "ratingValue": "{{ number_format($restaurant->average_rating ?? 0, 1) }}",
                    "reviewCount": "{{ $restaurant->total_reviews ?? 0 }}",
                    "bestRating": "5",
                    "worstRating": "1"
                }
            }
        }{{ $loop->last ? '' : ',' }}
        @endforeach
    ]
}
</script>

<!-- LocalBusiness Schema for City Page -->
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "WebPage",
    "name": "Mexican Restaurants in {{ $cityName }}, {{ $state->code }}",
    "description": "Find the best {{ $stats->total }} Mexican restaurants in {{ $cityName }}, {{ $state->name }}. Average rating: {{ number_format($stats->avg_rating ?? 0, 1) }} stars.",
    "url": "{{ url()->current() }}",
    "inLanguage": "{{ app()->getLocale() }}",
    "isPartOf": {
        "@@type": "WebSite",
        "name": "{{ __('app.site_name') }}",
        "url": "{{ url('/') }}"
    },
    "about": {
        "@@type": "City",
        "name": "{{ $cityName }}",
        "containedInPlace": {
            "@@type": "State",
            "name": "{{ $state->name }}",
            "containedInPlace": {
                "@@type": "Country",
                "name": "United States"
            }
        }
    },
    "mainEntity": {
        "@@type": "ItemList",
        "numberOfItems": {{ $stats->total }},
        "itemListElement": "Mexican Restaurants"
    }
}
</script>
@endpush
@endsection
