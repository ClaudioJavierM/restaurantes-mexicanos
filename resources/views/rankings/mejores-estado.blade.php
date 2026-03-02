@extends('layouts.app')

@section('title', 'Mejores Restaurantes Mexicanos en ' . $state->name . ' ' . $year . ' - Top Ranking')
@section('meta_description', 'Los mejores restaurantes mexicanos en ' . $state->name . ' ' . $year . '. Ranking de ' . number_format($stats->total) . ' restaurantes con calificaciones y resenas verificadas.')

@section('content')
{{-- Schema.org ItemList --}}
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "ItemList",
    "name": "Mejores Restaurantes Mexicanos en {{ $state->name }} {{ $year }}",
    "description": "Ranking de los mejores restaurantes mexicanos en {{ $state->name }}",
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
                    "addressLocality": "{{ $restaurant->city }}",
                    "addressRegion": "{{ $state->code }}",
                    "addressCountry": "US"
                },
                "servesCuisine": "Mexican",
                @if($restaurant->average_rating)
                "aggregateRating": {
                    "@@type": "AggregateRating",
                    "ratingValue": "{{ number_format($restaurant->average_rating, 1) }}",
                    "reviewCount": "{{ $restaurant->total_reviews ?? 0 }}"
                },
                @endif
                "priceRange": "{{ $restaurant->price_range ?? '$$' }}"
            }
        }@if(!$loop->last),@endif
        @endforeach
    ]
}
</script>

{{-- Geographic Meta --}}
<meta name="geo.region" content="US-{{ $state->code }}" />
<meta name="geo.placename" content="{{ $state->name }}" />

{{-- Open Graph --}}
<x-open-graph
    title="Mejores Restaurantes Mexicanos en {{ $state->name }} {{ $year }}"
    description="Top {{ $restaurants->count() }} restaurantes mexicanos en {{ $state->name }}. Rating promedio: {{ number_format($stats->avg_rating, 1) }} estrellas."
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
                    <li class="font-semibold">{{ $state->name }}</li>
                </ol>
            </nav>

            <h1 class="text-3xl md:text-4xl lg:text-5xl font-extrabold mb-4">
                Mejores Restaurantes Mexicanos<br>
                <span class="text-yellow-300">en {{ $state->name }} {{ $year }}</span>
            </h1>

            <div class="flex flex-wrap gap-6 mt-6">
                <div class="bg-white/10 rounded-lg px-4 py-3">
                    <div class="text-2xl font-bold">{{ number_format($stats->total) }}</div>
                    <div class="text-sm opacity-80">Restaurantes</div>
                </div>
                <div class="bg-white/10 rounded-lg px-4 py-3">
                    <div class="text-2xl font-bold">{{ number_format($stats->avg_rating, 1) }} ⭐</div>
                    <div class="text-sm opacity-80">Rating Promedio</div>
                </div>
                <div class="bg-white/10 rounded-lg px-4 py-3">
                    <div class="text-2xl font-bold">{{ number_format($stats->total_reviews) }}</div>
                    <div class="text-sm opacity-80">Resenas Totales</div>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="lg:flex lg:gap-8">
            {{-- Main Content --}}
            <div class="lg:w-2/3">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">
                    Top {{ $restaurants->count() }} en {{ $state->name }}
                </h2>

                <div class="space-y-4">
                    @foreach($restaurants as $index => $restaurant)
                        <a href="{{ route('restaurants.show', $restaurant->slug) }}"
                           class="block bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-lg transition-all group">
                            <div class="flex">
                                <div class="flex-shrink-0 w-16 flex items-center justify-center
                                    @if($index < 3) bg-gradient-to-br from-yellow-400 to-amber-500 text-white @else bg-gray-100 text-gray-700 @endif">
                                    <span class="text-2xl font-extrabold">#{{ $index + 1 }}</span>
                                </div>

                                <div class="flex-shrink-0 w-24 h-24">
                                    @php
                                        $imageUrl = $restaurant->getFirstMediaUrl('photos', 'thumb')
                                            ?: ($restaurant->yelp_photos[0] ?? '/images/placeholder-restaurant.jpg');
                                    @endphp
                                    <img src="{{ $imageUrl }}" alt="{{ $restaurant->name }}"
                                         class="w-full h-full object-cover">
                                </div>

                                <div class="flex-1 p-4">
                                    <h3 class="text-lg font-bold text-gray-900 group-hover:text-red-600">
                                        {{ $restaurant->name }}
                                    </h3>
                                    <p class="text-sm text-gray-600">{{ $restaurant->city }}, {{ $state->code }}</p>
                                    <div class="mt-2 flex items-center gap-2">
                                        <span class="font-bold text-gray-900">{{ number_format($restaurant->average_rating, 1) }}</span>
                                        <span class="text-yellow-400">★</span>
                                        <span class="text-sm text-gray-500">({{ number_format($restaurant->total_reviews ?? 0) }})</span>
                                    </div>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>

                <div class="mt-8 text-center">
                    <a href="{{ route('restaurants.index', ['state' => $state->name]) }}"
                       class="inline-flex items-center px-6 py-3 bg-red-600 text-white font-bold rounded-lg hover:bg-red-700">
                        Ver Todos en {{ $state->name }}
                    </a>
                </div>
            </div>

            {{-- Sidebar --}}
            <div class="lg:w-1/3 mt-8 lg:mt-0">
                <div class="lg:sticky lg:top-4 space-y-6">
                    {{-- Top Cities --}}
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Ciudades Principales</h3>
                        <div class="space-y-2">
                            @foreach($topCities as $city)
                                <a href="{{ route('rankings.mejores-ciudad', [$state->slug ?? strtolower($state->code), Str::slug($city->city)]) }}"
                                   class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-red-50 transition-colors group">
                                    <span class="font-medium text-gray-900 group-hover:text-red-600">{{ $city->city }}</span>
                                    <div class="text-right">
                                        <span class="text-sm text-gray-500">{{ $city->count }}</span>
                                        <span class="ml-2 text-yellow-500">{{ number_format($city->avg_rating, 1) }}★</span>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>

                    {{-- Other States --}}
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Otros Estados</h3>
                        <a href="{{ route('rankings.mejores-nacional') }}"
                           class="block p-3 bg-gradient-to-r from-red-50 to-orange-50 rounded-lg text-red-700 font-semibold hover:from-red-100 hover:to-orange-100">
                            Ver Ranking Nacional
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
