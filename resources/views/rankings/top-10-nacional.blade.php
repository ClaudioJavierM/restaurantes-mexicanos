@extends('layouts.app')

@section('title', 'Top 10 Restaurantes Mexicanos en USA ' . $year . ' - Ranking Oficial')
@section('meta_description', 'Los 10 mejores restaurantes mexicanos en Estados Unidos ' . $year . '. Ranking basado en calificaciones de Google, Yelp y resenas verificadas.')

@section('content')
{{-- Schema.org ItemList for Rich Snippets --}}
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "ItemList",
    "name": "Top 10 Restaurantes Mexicanos en Estados Unidos {{ $year }}",
    "description": "Los 10 mejores restaurantes mexicanos en USA basado en calificaciones verificadas",
    "numberOfItems": 10,
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
    title="Top 10 Restaurantes Mexicanos USA {{ $year }}"
    description="Los 10 mejores restaurantes mexicanos en Estados Unidos. Ranking oficial basado en calificaciones verificadas."
    type="website"
/>

<div class="min-h-screen bg-gradient-to-b from-gray-900 via-gray-800 to-gray-900">
    {{-- Hero Section --}}
    <div class="relative overflow-hidden" style="background:linear-gradient(135deg,#0B0B0B 0%,#1A1A1A 50%,#0B0B0B 100%); border-bottom:1px solid rgba(212,175,55,0.15);">
        <img src="https://images.unsplash.com/photo-1555396273-367ea4eb4db5?auto=format&fit=crop&w=1600&q=80"
             alt="" aria-hidden="true"
             class="absolute inset-0 w-full h-full object-cover" style="opacity:0.12; pointer-events:none;">
        <div class="absolute inset-0" style="background:linear-gradient(to bottom, rgba(11,11,11,0.5) 0%, rgba(11,11,11,0.2) 100%); pointer-events:none;"></div>
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-16 relative" style="z-index:1;">
            <nav class="text-sm mb-8">
                <ol class="flex items-center space-x-2 text-gray-400">
                    <li><a href="{{ route('home') }}" class="hover:text-white">Inicio</a></li>
                    <li><span>/</span></li>
                    <li><a href="{{ route('rankings.mejores-nacional') }}" class="hover:text-white">Mejores Restaurantes</a></li>
                    <li><span>/</span></li>
                    <li class="text-yellow-400 font-semibold">Top 10</li>
                </ol>
            </nav>

            <div class="text-center">
                <span class="inline-block text-6xl mb-4">🏆</span>
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-extrabold text-white mb-4">
                    Top 10 Restaurantes Mexicanos
                </h1>
                <p class="text-2xl text-yellow-400 font-bold mb-6">Estados Unidos {{ $year }}</p>
                <p class="text-lg text-gray-300 max-w-2xl mx-auto">
                    El ranking definitivo de los mejores restaurantes de comida mexicana en USA,
                    basado en miles de resenas y calificaciones verificadas.
                </p>
            </div>
        </div>
    </div>

    {{-- Top 10 List --}}
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 pb-16">
        <div class="space-y-6">
            @foreach($restaurants as $index => $restaurant)
                <div class="relative">
                    {{-- Connector line --}}
                    @if(!$loop->last)
                        <div class="absolute left-8 top-full w-0.5 h-6 bg-gradient-to-b from-yellow-500/50 to-transparent"></div>
                    @endif

                    <a href="{{ route('restaurants.show', $restaurant->slug) }}"
                       class="block bg-gradient-to-r
                           @if($index === 0) from-yellow-500/20 via-amber-500/10 to-transparent border-yellow-500/50
                           @elseif($index === 1) from-gray-400/20 via-gray-400/10 to-transparent border-gray-400/50
                           @elseif($index === 2) from-amber-700/20 via-amber-700/10 to-transparent border-amber-700/50
                           @else from-gray-700/50 to-gray-800/50 border-gray-700
                           @endif
                           rounded-2xl border overflow-hidden hover:scale-[1.02] transition-all duration-300 group">

                        <div class="flex items-center">
                            {{-- Rank Badge --}}
                            <div class="flex-shrink-0 w-20 md:w-28 py-6 flex flex-col items-center justify-center
                                @if($index === 0) bg-gradient-to-br from-yellow-400 to-amber-500
                                @elseif($index === 1) bg-gradient-to-br from-gray-300 to-gray-400
                                @elseif($index === 2) bg-gradient-to-br from-amber-600 to-amber-700
                                @else bg-gray-700
                                @endif">
                                @if($index < 3)
                                    <span class="text-4xl mb-1">
                                        @if($index === 0) 🥇 @elseif($index === 1) 🥈 @else 🥉 @endif
                                    </span>
                                @endif
                                <span class="text-3xl md:text-4xl font-black @if($index < 3) text-white @else text-gray-300 @endif">
                                    {{ $index + 1 }}
                                </span>
                            </div>

                            {{-- Restaurant Image --}}
                            <div class="flex-shrink-0 w-32 md:w-40 h-32 md:h-40 overflow-hidden">
                                @php
                                    $imageUrl = $restaurant->getFirstMediaUrl('photos', 'thumb')
                                        ?: ($restaurant->yelp_photos[0] ?? null)
                                        ?: ($restaurant->image ? \Illuminate\Support\Facades\Storage::url($restaurant->image) : '/images/placeholder-restaurant.jpg');
                                @endphp
                                <img src="{{ $imageUrl }}" alt="{{ $restaurant->name }}"
                                     class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                            </div>

                            {{-- Restaurant Info --}}
                            <div class="flex-1 p-6">
                                <h2 class="text-xl md:text-2xl font-bold text-white group-hover:text-yellow-400 transition-colors">
                                    {{ $restaurant->name }}
                                </h2>
                                <p class="text-gray-400 mt-1">
                                    📍 {{ $restaurant->city }}, {{ $restaurant->state?->code }}
                                </p>

                                {{-- Rating Stars --}}
                                <div class="mt-3 flex items-center gap-4">
                                    <div class="flex items-center">
                                        @for($i = 1; $i <= 5; $i++)
                                            <svg class="w-5 h-5 {{ $i <= round($restaurant->average_rating) ? 'text-yellow-400' : 'text-gray-600' }}"
                                                 fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                            </svg>
                                        @endfor
                                        <span class="ml-2 text-xl font-bold text-white">{{ number_format($restaurant->average_rating, 1) }}</span>
                                    </div>
                                    <span class="text-gray-500">({{ number_format($restaurant->total_reviews ?? 0) }} resenas)</span>
                                </div>

                                {{-- Tags --}}
                                <div class="mt-3 flex flex-wrap gap-2">
                                    @if($restaurant->category)
                                        <span class="px-3 py-1 bg-red-500/20 text-red-400 text-sm font-medium rounded-full">
                                            {{ $restaurant->category->name }}
                                        </span>
                                    @endif
                                    @if($restaurant->price_range)
                                        <span class="px-3 py-1 bg-green-500/20 text-green-400 text-sm font-medium rounded-full">
                                            {{ $restaurant->price_range }}
                                        </span>
                                    @endif
                                </div>
                            </div>

                            {{-- Arrow --}}
                            <div class="flex-shrink-0 pr-6">
                                <svg class="w-8 h-8 text-gray-600 group-hover:text-yellow-400 group-hover:translate-x-2 transition-all"
                                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                                </svg>
                            </div>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>

        {{-- CTAs --}}
        <div class="mt-16 text-center space-y-4">
            <a href="{{ route('rankings.mejores-nacional') }}"
               class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-yellow-500 to-amber-500 text-gray-900 font-bold rounded-xl hover:from-yellow-400 hover:to-amber-400 transition-all shadow-lg hover:shadow-xl">
                Ver Top 50 Restaurantes
                <svg class="ml-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                </svg>
            </a>
            <p class="text-gray-500">
                o <a href="{{ route('restaurants.index') }}" class="text-yellow-400 hover:underline">explora todos los restaurantes</a>
            </p>
        </div>
    </div>
</div>
@endsection
