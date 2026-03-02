@extends('layouts.app')

@section('title', 'Mejores Restaurantes Mexicanos en ' . $cityName . ', ' . $state->code . ' ' . $year)
@section('meta_description', 'Los mejores restaurantes mexicanos en ' . $cityName . ', ' . $state->name . ' ' . $year . '. Top ' . $restaurants->count() . ' restaurantes con las mejores calificaciones.')

@section('content')
{{-- Schema.org ItemList --}}
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "ItemList",
    "name": "Mejores Restaurantes Mexicanos en {{ $cityName }}, {{ $state->code }} {{ $year }}",
    "description": "Ranking de los mejores restaurantes mexicanos en {{ $cityName }}",
    "numberOfItems": {{ $restaurants->count() }},
    "itemListElement": [
        @foreach($restaurants as $index => $restaurant)
        {
            "@@type": "ListItem",
            "position": {{ $index + 1 }},
            "item": {
                "@@type": "Restaurant",
                "name": "{{ $restaurant->name }}",
                "url": "{{ route('restaurants.show', $restaurant->slug) }}",
                "address": {
                    "@@type": "PostalAddress",
                    "streetAddress": "{{ $restaurant->address }}",
                    "addressLocality": "{{ $cityName }}",
                    "addressRegion": "{{ $state->code }}",
                    "postalCode": "{{ $restaurant->zip_code }}",
                    "addressCountry": "US"
                },
                "servesCuisine": "Mexican",
                "telephone": "{{ $restaurant->phone }}",
                @if($restaurant->average_rating)
                "aggregateRating": {
                    "@@type": "AggregateRating",
                    "ratingValue": "{{ number_format($restaurant->average_rating, 1) }}",
                    "reviewCount": "{{ $restaurant->total_reviews ?? 0 }}",
                    "bestRating": "5",
                    "worstRating": "1"
                },
                @endif
                "priceRange": "{{ $restaurant->price_range ?? '$$' }}"
            }
        }@if(!$loop->last),@endif
        @endforeach
    ]
}
</script>

{{-- BreadcrumbList Schema --}}
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "BreadcrumbList",
    "itemListElement": [
        {
            "@@type": "ListItem",
            "position": 1,
            "name": "Mejores Restaurantes",
            "item": "{{ route('rankings.mejores-nacional') }}"
        },
        {
            "@@type": "ListItem",
            "position": 2,
            "name": "{{ $state->name }}",
            "item": "{{ route('rankings.mejores-estado', $state->slug ?? strtolower($state->code)) }}"
        },
        {
            "@@type": "ListItem",
            "position": 3,
            "name": "{{ $cityName }}",
            "item": "{{ url()->current() }}"
        }
    ]
}
</script>

{{-- Geographic Meta --}}
<meta name="geo.region" content="US-{{ $state->code }}" />
<meta name="geo.placename" content="{{ $cityName }}, {{ $state->name }}" />

{{-- Open Graph --}}
<x-open-graph
    title="Mejores Restaurantes Mexicanos en {{ $cityName }} {{ $year }}"
    description="Top {{ $restaurants->count() }} restaurantes mexicanos en {{ $cityName }}, {{ $state->code }}. Rating promedio: {{ number_format($stats->avg_rating, 1) }} estrellas."
    type="website"
/>

<div class="min-h-screen bg-gray-50">
    {{-- Hero --}}
    <div class="bg-gradient-to-br from-red-700 via-red-600 to-orange-500 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <nav class="text-sm mb-6">
                <ol class="flex items-center space-x-2">
                    <li><a href="{{ route('home') }}" class="hover:underline opacity-80">Inicio</a></li>
                    <li><span class="opacity-60">/</span></li>
                    <li><a href="{{ route('rankings.mejores-nacional') }}" class="hover:underline opacity-80">Mejores Restaurantes</a></li>
                    <li><span class="opacity-60">/</span></li>
                    <li><a href="{{ route('rankings.mejores-estado', $state->slug ?? strtolower($state->code)) }}" class="hover:underline opacity-80">{{ $state->name }}</a></li>
                    <li><span class="opacity-60">/</span></li>
                    <li class="font-semibold">{{ $cityName }}</li>
                </ol>
            </nav>

            <h1 class="text-3xl md:text-4xl lg:text-5xl font-extrabold mb-4">
                Los Mejores Restaurantes Mexicanos<br>
                <span class="text-yellow-300">en {{ $cityName }}, {{ $state->code }} {{ $year }}</span>
            </h1>

            <p class="text-lg opacity-90 max-w-2xl mb-6">
                Descubre los {{ $restaurants->count() }} mejores restaurantes de comida mexicana en {{ $cityName }}.
                Ranking basado en {{ number_format($stats->total_reviews) }} resenas verificadas.
            </p>

            <div class="flex flex-wrap gap-6">
                <div class="bg-white/10 rounded-lg px-4 py-3">
                    <div class="text-2xl font-bold">{{ number_format($stats->total) }}</div>
                    <div class="text-sm opacity-80">Restaurantes</div>
                </div>
                <div class="bg-white/10 rounded-lg px-4 py-3">
                    <div class="text-2xl font-bold">{{ number_format($stats->avg_rating, 1) }} ⭐</div>
                    <div class="text-sm opacity-80">Rating Promedio</div>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">
            Top {{ $restaurants->count() }} Restaurantes Mexicanos en {{ $cityName }}
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($restaurants as $index => $restaurant)
                <a href="{{ route('restaurants.show', $restaurant->slug) }}"
                   class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-xl transition-all group relative">

                    {{-- Rank Badge --}}
                    <div class="absolute top-3 left-3 z-10
                        @if($index < 3) bg-gradient-to-br from-yellow-400 to-amber-500
                        @else bg-red-600
                        @endif text-white w-10 h-10 rounded-full flex items-center justify-center font-bold text-lg shadow-lg">
                        {{ $index + 1 }}
                    </div>

                    {{-- Image --}}
                    <div class="h-48 overflow-hidden">
                        @php
                            $imageUrl = $restaurant->getFirstMediaUrl('photos', 'thumb')
                                ?: ($restaurant->yelp_photos[0] ?? '/images/placeholder-restaurant.jpg');
                        @endphp
                        <img src="{{ $imageUrl }}" alt="{{ $restaurant->name }}"
                             class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300">
                    </div>

                    {{-- Content --}}
                    <div class="p-4">
                        <h3 class="text-lg font-bold text-gray-900 group-hover:text-red-600 transition-colors line-clamp-1">
                            {{ $restaurant->name }}
                        </h3>

                        {{-- Rating --}}
                        <div class="mt-2 flex items-center gap-2">
                            <div class="flex items-center">
                                @for($i = 1; $i <= 5; $i++)
                                    <svg class="w-4 h-4 {{ $i <= round($restaurant->average_rating) ? 'text-yellow-400' : 'text-gray-300' }}"
                                         fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                @endfor
                            </div>
                            <span class="font-bold text-gray-900">{{ number_format($restaurant->average_rating, 1) }}</span>
                            <span class="text-sm text-gray-500">({{ number_format($restaurant->total_reviews ?? 0) }})</span>
                        </div>

                        {{-- Address --}}
                        <p class="mt-2 text-sm text-gray-600 line-clamp-1">
                            📍 {{ $restaurant->address }}
                        </p>

                        {{-- Tags --}}
                        <div class="mt-3 flex flex-wrap gap-2">
                            @if($restaurant->category)
                                <span class="px-2 py-1 bg-red-50 text-red-700 text-xs font-medium rounded">
                                    {{ $restaurant->category->name }}
                                </span>
                            @endif
                            @if($restaurant->price_range)
                                <span class="px-2 py-1 bg-green-50 text-green-700 text-xs font-medium rounded">
                                    {{ $restaurant->price_range }}
                                </span>
                            @endif
                        </div>
                    </div>
                </a>
            @endforeach
        </div>

        {{-- CTAs --}}
        <div class="mt-12 text-center space-y-4">
            <a href="{{ route('restaurants.index', ['state' => $state->name]) }}"
               class="inline-flex items-center px-6 py-3 bg-red-600 text-white font-bold rounded-lg hover:bg-red-700">
                Ver Todos los Restaurantes en {{ $state->name }}
            </a>
            <div>
                <a href="{{ route('rankings.mejores-estado', $state->slug ?? strtolower($state->code)) }}"
                   class="text-red-600 hover:underline font-medium">
                    ← Volver a ranking de {{ $state->name }}
                </a>
            </div>
        </div>

        {{-- Related Cities (would need to pass this from controller) --}}
        {{--
        <div class="mt-16">
            <h3 class="text-xl font-bold text-gray-900 mb-6">Otras Ciudades en {{ $state->name }}</h3>
            <div class="flex flex-wrap gap-3">
                @foreach($relatedCities ?? [] as $city)
                    <a href="{{ route('rankings.mejores-ciudad', [$state->slug ?? strtolower($state->code), Str::slug($city)]) }}"
                       class="px-4 py-2 bg-white border border-gray-200 rounded-lg hover:border-red-300 hover:bg-red-50 transition-colors">
                        {{ $city }}
                    </a>
                @endforeach
            </div>
        </div>
        --}}
    </div>
</div>
@endsection
