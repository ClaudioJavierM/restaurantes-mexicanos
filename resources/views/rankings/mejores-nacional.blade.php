@extends('layouts.app')

@section('title', 'Los Mejores Restaurantes Mexicanos en Estados Unidos ' . $year . ' - FAMER')
@section('meta_description', 'Descubre los mejores restaurantes mexicanos en USA. Ranking ' . $year . ' con ' . number_format($totalRestaurants) . '+ restaurantes evaluados por calificaciones y resenas de clientes.')

@section('content')
{{-- Schema.org ItemList for Rich Snippets --}}
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "ItemList",
    "name": "Los Mejores Restaurantes Mexicanos en Estados Unidos {{ $year }}",
    "description": "Ranking de los mejores restaurantes mexicanos en USA basado en calificaciones y resenas de clientes",
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
                    "addressRegion": "{{ $restaurant->state?->code }}",
                    "addressCountry": "US"
                },
                "servesCuisine": "Mexican",
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

{{-- Open Graph --}}
<x-open-graph
    title="Los Mejores Restaurantes Mexicanos {{ $year }}"
    description="Ranking de los mejores restaurantes mexicanos en Estados Unidos. {{ number_format($totalRestaurants) }}+ restaurantes evaluados."
    type="website"
/>

<div class="min-h-screen bg-gray-50">
    {{-- Hero Section --}}
    <div class="bg-gradient-to-br from-red-700 via-red-600 to-orange-500 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <nav class="text-sm mb-6">
                <ol class="flex items-center space-x-2">
                    <li><a href="{{ route('home') }}" class="hover:underline opacity-80">Inicio</a></li>
                    <li><span class="opacity-60">/</span></li>
                    <li class="font-semibold">Mejores Restaurantes Mexicanos</li>
                </ol>
            </nav>

            <h1 class="text-4xl md:text-5xl lg:text-6xl font-extrabold mb-4">
                Los Mejores Restaurantes Mexicanos<br>
                <span class="text-yellow-300">en Estados Unidos {{ $year }}</span>
            </h1>
            <p class="text-xl md:text-2xl opacity-90 max-w-3xl mb-8">
                Ranking oficial basado en {{ number_format($totalRestaurants) }}+ restaurantes evaluados por calificaciones de Google, Yelp y resenas de clientes reales.
            </p>

            {{-- Stats --}}
            <div class="flex flex-wrap gap-6 text-center">
                <div class="bg-white/10 rounded-xl px-6 py-4">
                    <div class="text-3xl font-bold">{{ number_format($totalRestaurants) }}+</div>
                    <div class="text-sm opacity-80">Restaurantes</div>
                </div>
                <div class="bg-white/10 rounded-xl px-6 py-4">
                    <div class="text-3xl font-bold">{{ number_format($avgRating, 1) }}</div>
                    <div class="text-sm opacity-80">Rating Promedio</div>
                </div>
                <div class="bg-white/10 rounded-xl px-6 py-4">
                    <div class="text-3xl font-bold">50</div>
                    <div class="text-sm opacity-80">Estados</div>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="lg:flex lg:gap-8">
            {{-- Main Content --}}
            <div class="lg:w-2/3">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">
                    Top 50 Mejores Restaurantes Mexicanos {{ $year }}
                </h2>

                <div class="space-y-4">
                    @foreach($restaurants as $index => $restaurant)
                        <a href="{{ route('restaurants.show', $restaurant->slug) }}"
                           class="block bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-lg transition-all duration-200 group">
                            <div class="flex">
                                {{-- Rank Badge --}}
                                <div class="flex-shrink-0 w-16 md:w-20 flex items-center justify-center
                                    @if($index < 3) bg-gradient-to-br from-yellow-400 to-amber-500 @else bg-gray-100 @endif">
                                    <span class="text-2xl md:text-3xl font-extrabold @if($index < 3) text-white @else text-gray-700 @endif">
                                        #{{ $index + 1 }}
                                    </span>
                                </div>

                                {{-- Restaurant Image --}}
                                <div class="flex-shrink-0 w-24 md:w-32 h-24 md:h-32">
                                    @php
                                        $imageUrl = $restaurant->getFirstMediaUrl('photos', 'thumb')
                                            ?: ($restaurant->yelp_photos[0] ?? '/images/placeholder-restaurant.jpg');
                                    @endphp
                                    <img src="{{ $imageUrl }}" alt="{{ $restaurant->name }}"
                                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                </div>

                                {{-- Restaurant Info --}}
                                <div class="flex-1 p-4">
                                    <div class="flex items-start justify-between">
                                        <div>
                                            <h3 class="text-lg font-bold text-gray-900 group-hover:text-red-600 transition-colors">
                                                {{ $restaurant->name }}
                                            </h3>
                                            <p class="text-sm text-gray-600">
                                                {{ $restaurant->city }}, {{ $restaurant->state?->code }}
                                            </p>
                                        </div>
                                        @if($index < 3)
                                            <span class="flex-shrink-0 text-2xl">
                                                @if($index === 0) @endif
                                                @if($index === 1) @endif
                                                @if($index === 2) @endif
                                            </span>
                                        @endif
                                    </div>

                                    {{-- Rating --}}
                                    <div class="mt-2 flex items-center gap-3">
                                        <div class="flex items-center">
                                            @for($i = 1; $i <= 5; $i++)
                                                <svg class="w-4 h-4 {{ $i <= round($restaurant->average_rating) ? 'text-yellow-400' : 'text-gray-300' }}"
                                                     fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                </svg>
                                            @endfor
                                            <span class="ml-2 font-bold text-gray-900">{{ number_format($restaurant->average_rating, 1) }}</span>
                                        </div>
                                        <span class="text-sm text-gray-500">({{ number_format($restaurant->total_reviews ?? 0) }} resenas)</span>
                                        @if($restaurant->price_range)
                                            <span class="text-sm text-green-600 font-medium">{{ $restaurant->price_range }}</span>
                                        @endif
                                    </div>

                                    {{-- Category --}}
                                    @if($restaurant->category)
                                        <span class="mt-2 inline-block px-2 py-1 bg-red-50 text-red-700 text-xs font-medium rounded">
                                            {{ $restaurant->category->name }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>

                {{-- CTA --}}
                <div class="mt-12 text-center">
                    <a href="{{ route('restaurants.index') }}"
                       class="inline-flex items-center px-8 py-4 bg-red-600 text-white font-bold rounded-xl hover:bg-red-700 transition-colors">
                        Ver Todos los Restaurantes
                        <svg class="ml-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                        </svg>
                    </a>
                </div>
            </div>

            {{-- Sidebar --}}
            <div class="lg:w-1/3 mt-8 lg:mt-0">
                <div class="lg:sticky lg:top-4 space-y-6">
                    {{-- Top States --}}
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Mejores por Estado</h3>
                        <div class="space-y-3">
                            @foreach($topStates as $stateData)
                                @if($stateData->state)
                                    <a href="{{ route('rankings.mejores-estado', $stateData->state->slug ?? strtolower($stateData->state->code)) }}"
                                       class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-red-50 transition-colors group">
                                        <span class="font-medium text-gray-900 group-hover:text-red-600">
                                            {{ $stateData->state->name }}
                                        </span>
                                        <span class="text-sm text-gray-500">{{ number_format($stateData->count) }} restaurantes</span>
                                    </a>
                                @endif
                            @endforeach
                        </div>
                    </div>

                    {{-- Quick Links --}}
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Rankings Populares</h3>
                        <div class="space-y-2">
                            <a href="{{ route('rankings.top10-nacional') }}"
                               class="block p-3 bg-gradient-to-r from-yellow-50 to-amber-50 rounded-lg hover:from-yellow-100 hover:to-amber-100 transition-colors">
                                <span class="font-semibold text-amber-700">Top 10 Restaurantes Mexicanos</span>
                            </a>
                            <a href="{{ route('famer.awards') }}"
                               class="block p-3 bg-gradient-to-r from-red-50 to-orange-50 rounded-lg hover:from-red-100 hover:to-orange-100 transition-colors">
                                <span class="font-semibold text-red-700">FAMER Awards {{ $year }}</span>
                            </a>
                        </div>
                    </div>

                    {{-- Methodology --}}
                    <div class="bg-gradient-to-br from-gray-800 to-gray-900 rounded-xl p-6 text-white">
                        <h3 class="text-lg font-bold mb-3">Como calculamos el ranking?</h3>
                        <ul class="space-y-2 text-sm text-gray-300">
                            <li class="flex items-start gap-2">
                                <svg class="w-5 h-5 text-green-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <span>Ratings de Google y Yelp (70%)</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <svg class="w-5 h-5 text-green-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <span>Cantidad de resenas (30%)</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <svg class="w-5 h-5 text-green-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <span>Actualizado mensualmente</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
