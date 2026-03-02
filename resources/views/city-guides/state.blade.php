@extends('layouts.app')

@section('title', "Restaurantes Mexicanos en {$state->name} | Las Mejores Ciudades")
@section('meta_description', "Descubre los mejores restaurantes mexicanos en {$state->name}. {$stats->total} restaurantes en {$cities->count()} ciudades. Rating promedio: {$stats->avg_rating}.")

@push('meta')
<!-- Open Graph / Facebook -->
<meta property="og:type" content="website">
<meta property="og:url" content="{{ url()->current() }}">
<meta property="og:title" content="Restaurantes Mexicanos en {{ $state->name }} | {{ $cities->count() }} Ciudades">
<meta property="og:description" content="Descubre {{ number_format($stats->total) }} restaurantes mexicanos en {{ $state->name }}. {{ $cities->count() }} ciudades con comida mexicana autentica.">
@php
    $topRest = $topRestaurants->first();
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
<meta name="twitter:title" content="Restaurantes Mexicanos en {{ $state->name }}">
<meta name="twitter:description" content="{{ number_format($stats->total) }} restaurantes en {{ $cities->count() }} ciudades. Rating promedio: {{ number_format($stats->avg_rating ?? 0, 1) }}">

<!-- SEO: Geo Tags for Local Search -->
<meta name="geo.region" content="US-{{ $state->code }}">
<meta name="geo.placename" content="{{ $state->name }}, USA">
@endpush

@section('content')
<div class="bg-gradient-to-br from-emerald-600 via-green-600 to-red-600 text-white">
    <div class="max-w-7xl mx-auto px-4 py-12 sm:px-6 lg:px-8">
        <!-- Breadcrumb -->
        <nav class="text-sm mb-4 opacity-90">
            <a href="{{ route('city-guides.states') }}" class="hover:underline">Estados</a>
            <span class="mx-2">/</span>
            <span>{{ $state->name }}</span>
        </nav>

        <h1 class="text-3xl md:text-4xl font-bold mb-4">
            Restaurantes Mexicanos en {{ $state->name }}
        </h1>
        <p class="text-lg opacity-90 max-w-3xl">
            Explora las mejores ciudades para comida mexicana en {{ $state->name }}.
            Desde taquerias locales hasta restaurantes de alta cocina.
        </p>

        <!-- Stats -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-8">
            <div class="bg-white/10 backdrop-blur rounded-lg p-4 text-center">
                <p class="text-3xl font-bold">{{ number_format($stats->total) }}</p>
                <p class="text-sm opacity-80">Restaurantes</p>
            </div>
            <div class="bg-white/10 backdrop-blur rounded-lg p-4 text-center">
                <p class="text-3xl font-bold">{{ $cities->count() }}</p>
                <p class="text-sm opacity-80">Ciudades</p>
            </div>
            <div class="bg-white/10 backdrop-blur rounded-lg p-4 text-center">
                <p class="text-3xl font-bold">{{ number_format($stats->avg_rating ?? 0, 1) }}</p>
                <p class="text-sm opacity-80">Rating Promedio</p>
            </div>
            <div class="bg-white/10 backdrop-blur rounded-lg p-4 text-center">
                <p class="text-3xl font-bold">{{ $stats->claimed_count ?? 0 }}</p>
                <p class="text-sm opacity-80">Verificados</p>
            </div>
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 py-8 sm:px-6 lg:px-8">
    <!-- Top Restaurants in State - FIRST -->
    @if($topRestaurants->isNotEmpty())
    <section class="mb-12">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">
            Mejores Restaurantes en {{ $state->name }}
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($topRestaurants as $index => $restaurant)
            @php
                $isElite = $restaurant->subscription_tier === 'elite';
                $isPremium = $restaurant->subscription_tier === 'premium';
                $cardClass = $isElite ? 'ring-2 ring-yellow-400 bg-gradient-to-br from-yellow-50 to-white' :
                            ($isPremium ? 'ring-2 ring-emerald-400 bg-gradient-to-br from-emerald-50 to-white' : 'bg-white');
            @endphp
            <a href="{{ route('restaurants.show', $restaurant->slug) }}"
               class="{{ $cardClass }} rounded-lg shadow hover:shadow-lg transition overflow-hidden group">
                <div class="relative h-40 bg-gray-100">
                    @php
                        $stateRestImage = null;
                        if ($restaurant->image) {
                            $stateRestImage = str_starts_with($restaurant->image, 'http') ? $restaurant->image : asset('storage/' . $restaurant->image);
                        } elseif ($restaurant->getFirstMediaUrl('images')) {
                            $stateRestImage = $restaurant->getFirstMediaUrl('images');
                        }
                    @endphp
                    @if($stateRestImage)
                        <img src="{{ $stateRestImage }}"
                             alt="{{ $restaurant->name }}"
                             class="w-full h-full object-cover group-hover:scale-105 transition duration-300"
                             loading="lazy"
                             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                        <div class="hidden w-full h-full relative bg-gradient-to-br from-green-600 via-white to-red-600 overflow-hidden items-center justify-center">
                            <span class="text-4xl">🍽️</span>
                        </div>
                    @else
                        <div class="w-full h-full relative bg-gradient-to-br from-green-600 via-white to-red-600 overflow-hidden">
                            <!-- Decorative pattern -->
                            <div class="absolute inset-0 opacity-10">
                                <div class="absolute top-2 left-4 text-4xl">🌮</div>
                                <div class="absolute top-6 right-6 text-3xl">🌶️</div>
                                <div class="absolute bottom-4 left-8 text-3xl">🫔</div>
                                <div class="absolute bottom-2 right-4 text-4xl">🍹</div>
                            </div>
                            <!-- Center content -->
                            <div class="absolute inset-0 flex flex-col items-center justify-center">
                                <span class="text-4xl mb-1">🇲🇽</span>
                                <span class="text-xs font-medium text-gray-700 bg-white/80 px-2 py-0.5 rounded">¿Eres el dueño?</span>
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

                <div class="p-4">
                    <h3 class="font-bold text-gray-900 group-hover:text-emerald-600 transition">
                        {{ $restaurant->name }}
                    </h3>
                    <p class="text-sm text-gray-500">{{ $restaurant->city }}</p>

                    @php $stateWeightedRating = $restaurant->getWeightedRating(); @endphp
                    @if($stateWeightedRating > 0)
                    <div class="flex items-center mt-2">
                        <div class="flex text-yellow-400">
                            @for($i = 1; $i <= 5; $i++)
                                <svg class="w-4 h-4 fill-current {{ $i <= round($stateWeightedRating) ? '' : 'text-gray-300' }}" viewBox="0 0 20 20">
                                    <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                                </svg>
                            @endfor
                        </div>
                        <span class="ml-2 text-sm text-gray-600">{{ number_format($stateWeightedRating, 1) }}</span>
                    </div>
                    @endif
                </div>
            </a>
            @endforeach
        </div>
    </section>
    @endif

    <!-- Cities Grid -->
    <div class="mb-8">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">
            Ciudades con Restaurantes Mexicanos en {{ $state->name }}
        </h2>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach($cities as $city)
            <a href="{{ route('city-guides.city', [$state->code, Str::slug($city->city)]) }}"
               class="bg-white rounded-lg shadow hover:shadow-lg transition p-6 group">
                <div class="mb-3">
                    <div class="flex flex-wrap items-start justify-between gap-2">
                        <h3 class="text-xl font-bold text-gray-900 group-hover:text-emerald-600 transition">
                            {{ $city->city }}
                        </h3>
                        <span class="bg-emerald-100 text-emerald-700 text-sm px-3 py-1 rounded-full font-medium whitespace-nowrap">
                            {{ $city->count }} restaurantes
                        </span>
                    </div>
                </div>

                <div class="flex flex-wrap items-center text-sm text-gray-600 gap-4">
                    @if($city->avg_rating)
                    <div class="flex items-center">
                        <svg class="w-4 h-4 text-yellow-400 fill-current mr-1" viewBox="0 0 20 20">
                            <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                        </svg>
                        <span>{{ number_format($city->avg_rating, 1) }} promedio</span>
                    </div>
                    @endif

                    @if($city->claimed_count > 0)
                    <div class="flex items-center text-emerald-600">
                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <span>{{ $city->claimed_count }} verificados</span>
                    </div>
                    @endif
                </div>

                <p class="text-gray-500 text-sm mt-3 group-hover:text-gray-600 transition">
                    Ver los mejores restaurantes mexicanos en {{ $city->city }} →
                </p>
            </a>
            @endforeach
        </div>

        @if($cities->isEmpty())
        <div class="bg-gray-50 rounded-lg p-8 text-center">
            <p class="text-gray-600">No se encontraron ciudades con restaurantes mexicanos en {{ $state->name }}.</p>
            <a href="{{ route('restaurants.index') }}" class="text-emerald-600 hover:underline mt-2 inline-block">
                Ver todos los restaurantes
            </a>
        </div>
        @endif
    </div>

    <!-- SEO Content -->
    <section class="mt-12 bg-gray-50 rounded-lg p-8">
        <h2 class="text-2xl font-bold text-gray-900 mb-4">
            Comida Mexicana en {{ $state->name }}
        </h2>
        <div class="prose max-w-none text-gray-600">
            <p>
                {{ $state->name }} cuenta con {{ number_format($stats->total) }} restaurantes mexicanos
                distribuidos en {{ $cities->count() }} ciudades. La escena culinaria mexicana en el estado
                ofrece desde autenticas taquerias hasta restaurantes de alta cocina.
            </p>
            <p class="mt-4">
                Los restaurantes mexicanos en {{ $state->name }} tienen un rating promedio de
                {{ number_format($stats->avg_rating ?? 0, 1) }} estrellas. Encuentra tacos, burritos,
                enchiladas, tamales, pozole, mole y mucha mas comida mexicana autentica.
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
            "name": "{{ $state->name }}"
        }
    ]
}
</script>

<!-- ItemList Schema for Cities -->
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
                "name": "{{ $city->city }}",
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

<!-- WebPage Schema -->
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "WebPage",
    "name": "Mexican Restaurants in {{ $state->name }}",
    "description": "Find the best Mexican restaurants across {{ $cities->count() }} cities in {{ $state->name }}. {{ number_format($stats->total) }} restaurants with an average rating of {{ number_format($stats->avg_rating ?? 0, 1) }} stars.",
    "url": "{{ url()->current() }}",
    "inLanguage": "{{ app()->getLocale() }}",
    "about": {
        "@@type": "State",
        "name": "{{ $state->name }}",
        "containedInPlace": {
            "@@type": "Country",
            "name": "United States"
        }
    }
}
</script>
@endpush
@endsection
