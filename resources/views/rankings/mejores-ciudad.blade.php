@extends('layouts.app')

@section('title')
Mejores Restaurantes Mexicanos en {{ $cityName }}, {{ $state->code }} {{ $year }} | FAMER
@endsection

@section('meta_description')
Los {{ $restaurants->count() }} mejores restaurantes mexicanos en {{ $cityName }}, {{ $state->name }} {{ $year }}. Rating promedio {{ number_format($stats->avg_rating, 1) }} estrellas basado en {{ number_format($stats->total_reviews) }} reseñas verificadas.
@endsection

@push('head')
<meta name="geo.region" content="US-{{ $state->code }}" />
<meta name="geo.placename" content="{{ $cityName }}, {{ $state->name }}" />
<x-open-graph
    title="Mejores Restaurantes Mexicanos en {{ $cityName }} {{ $year }}"
    description="Top {{ $restaurants->count() }} restaurantes mexicanos en {{ $cityName }}, {{ $state->code }}. Rating promedio: {{ number_format($stats->avg_rating, 1) }} estrellas."
    type="website"
/>
@endpush

@section('content')
<div class="min-h-screen bg-[#0B0B0B]">

    {{-- Hero --}}
    <div class="bg-[#0B0B0B] border-b border-[#2A2A2A]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-10 pb-12">

            {{-- Breadcrumb --}}
            <nav class="text-sm mb-8" aria-label="Breadcrumb">
                <ol class="flex items-center flex-wrap gap-x-2 gap-y-1">
                    <li>
                        <a href="{{ route('home') }}" class="text-gray-500 hover:text-[#D4AF37] transition-colors">Inicio</a>
                    </li>
                    <li><span class="text-[#2A2A2A]">/</span></li>
                    <li>
                        <a href="{{ route('rankings.mejores-nacional') }}" class="text-gray-500 hover:text-[#D4AF37] transition-colors">Mejores</a>
                    </li>
                    <li><span class="text-[#2A2A2A]">/</span></li>
                    <li>
                        <a href="{{ route('rankings.mejores-estado', $state->slug ?? strtolower($state->code)) }}"
                           class="text-gray-500 hover:text-[#D4AF37] transition-colors">{{ $state->name }}</a>
                    </li>
                    <li><span class="text-[#2A2A2A]">/</span></li>
                    <li class="text-[#F5F5F5] font-medium">{{ $cityName }}</li>
                </ol>
            </nav>

            {{-- H1 --}}
            <h1 class="text-3xl md:text-4xl lg:text-5xl font-extrabold text-[#F5F5F5] leading-tight mb-4">
                Los Mejores Restaurantes Mexicanos en<br>
                <span class="text-[#D4AF37]">{{ $cityName }}, {{ $state->code }}</span>
                <span class="text-gray-400 text-2xl md:text-3xl font-semibold"> {{ $year }}</span>
            </h1>

            <p class="text-gray-400 text-lg max-w-2xl mb-10">
                Ranking de los {{ $restaurants->count() }} mejores restaurantes de cocina mexicana en {{ $cityName }},
                basado en {{ number_format($stats->total_reviews) }} reseñas verificadas.
            </p>

            {{-- Stats bar --}}
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                <div class="bg-[#1A1A1A] border border-[#2A2A2A] rounded-xl px-5 py-4">
                    <div class="text-2xl font-bold text-[#F5F5F5]">{{ number_format($stats->total) }}</div>
                    <div class="text-xs text-gray-500 mt-1 uppercase tracking-wide">Restaurantes</div>
                </div>
                <div class="bg-[#1A1A1A] border border-[#2A2A2A] rounded-xl px-5 py-4">
                    <div class="text-2xl font-bold text-[#D4AF37]">{{ number_format($stats->avg_rating, 1) }} <span class="text-lg">★</span></div>
                    <div class="text-xs text-gray-500 mt-1 uppercase tracking-wide">Rating promedio</div>
                </div>
                <div class="bg-[#1A1A1A] border border-[#2A2A2A] rounded-xl px-5 py-4">
                    <div class="text-2xl font-bold text-[#F5F5F5]">{{ number_format($stats->total_reviews) }}</div>
                    <div class="text-xs text-gray-500 mt-1 uppercase tracking-wide">Reseñas totales</div>
                </div>
                <div class="bg-[#1A1A1A] border border-[#2A2A2A] rounded-xl px-5 py-4">
                    <div class="text-2xl font-bold text-[#F5F5F5]">{{ $year }}</div>
                    <div class="text-xs text-gray-500 mt-1 uppercase tracking-wide">Actualización</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Main content --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="lg:grid lg:grid-cols-3 lg:gap-10">

            {{-- Ranked list --}}
            <div class="lg:col-span-2">
                <h2 class="text-xl font-bold text-[#F5F5F5] mb-6">
                    Top {{ $restaurants->count() }} Restaurantes en {{ $cityName }}
                </h2>

                <div class="space-y-3">
                    @foreach($restaurants as $restaurant)
                    <a href="{{ route('restaurants.show', $restaurant->slug) }}"
                       class="flex items-center gap-4 bg-[#1A1A1A] border border-[#2A2A2A] rounded-xl p-4 hover:border-[#D4AF37]/40 transition-all group">

                        {{-- Rank badge --}}
                        <div class="w-12 h-12 rounded-full flex items-center justify-center flex-shrink-0 font-bold text-lg
                            @if($loop->index === 0) bg-[#D4AF37] text-black
                            @elseif($loop->index === 1) bg-gray-400 text-black
                            @elseif($loop->index === 2) bg-amber-700 text-white
                            @else bg-[#2A2A2A] text-gray-400 @endif">
                            #{{ $loop->index + 1 }}
                        </div>

                        {{-- Thumbnail --}}
                        <div class="w-16 h-16 rounded-lg overflow-hidden flex-shrink-0">
                            @php
                                $imageUrl = $restaurant->getFirstMediaUrl('photos', 'thumb')
                                    ?: ($restaurant->yelp_photos[0] ?? '/images/placeholder-restaurant.jpg');
                            @endphp
                            <img src="{{ $imageUrl }}"
                                 alt="{{ $restaurant->name }}"
                                 class="w-full h-full object-cover"
                                 loading="lazy">
                        </div>

                        {{-- Info --}}
                        <div class="flex-1 min-w-0">
                            <h3 class="font-bold text-[#F5F5F5] group-hover:text-[#D4AF37] transition-colors truncate">
                                {{ $restaurant->name }}
                            </h3>
                            <p class="text-sm text-gray-400 truncate">{{ $restaurant->city }}, {{ $state->code }}</p>
                            @if($restaurant->average_rating)
                            <div class="flex items-center gap-1 mt-1">
                                <span class="text-[#D4AF37] text-sm">★</span>
                                <span class="text-sm font-semibold text-[#F5F5F5]">{{ number_format($restaurant->average_rating, 1) }}</span>
                                <span class="text-xs text-gray-500">({{ number_format($restaurant->total_reviews ?? 0) }})</span>
                            </div>
                            @endif
                            @if($restaurant->address)
                            <p class="text-xs text-gray-600 truncate mt-0.5">{{ $restaurant->address }}</p>
                            @endif
                        </div>

                        {{-- Price + arrow --}}
                        <div class="flex items-center gap-3 flex-shrink-0">
                            @if($restaurant->price_range)
                            <span class="text-[#D4AF37] text-sm font-semibold">{{ $restaurant->price_range }}</span>
                            @endif
                            <svg class="w-4 h-4 text-gray-600 group-hover:text-[#D4AF37] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </div>
                    </a>
                    @endforeach
                </div>

                {{-- CTA --}}
                <div class="mt-10 text-center">
                    <a href="{{ route('restaurants.index', ['state' => $state->name, 'city' => $citySlug]) }}"
                       class="inline-flex items-center gap-2 px-8 py-4 bg-[#D4AF37] text-black font-bold rounded-xl hover:bg-[#C49B2D] transition-colors text-lg">
                        Ver todos los restaurantes en {{ $cityName }}
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                        </svg>
                    </a>
                </div>
            </div>

            {{-- Sidebar --}}
            <aside class="mt-12 lg:mt-0">
                <div class="lg:sticky lg:top-6 space-y-6">

                    {{-- City stats card --}}
                    <div class="bg-[#1A1A1A] border border-[#2A2A2A] rounded-xl p-6">
                        <h3 class="text-[#D4AF37] font-bold text-sm uppercase tracking-widest mb-5">
                            {{ $cityName }}, {{ $state->code }}
                        </h3>
                        <div class="space-y-4">
                            <div class="flex justify-between items-center border-b border-[#2A2A2A] pb-3">
                                <span class="text-gray-400 text-sm">Restaurantes en ranking</span>
                                <span class="text-[#F5F5F5] font-bold">{{ $restaurants->count() }}</span>
                            </div>
                            <div class="flex justify-between items-center border-b border-[#2A2A2A] pb-3">
                                <span class="text-gray-400 text-sm">Rating promedio</span>
                                <span class="text-[#D4AF37] font-bold">{{ number_format($stats->avg_rating, 1) }} ★</span>
                            </div>
                            <div class="flex justify-between items-center border-b border-[#2A2A2A] pb-3">
                                <span class="text-gray-400 text-sm">Reseñas totales</span>
                                <span class="text-[#F5F5F5] font-bold">{{ number_format($stats->total_reviews) }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-400 text-sm">Estado</span>
                                <span class="text-[#F5F5F5] font-bold">{{ $state->name }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- Navigation links --}}
                    <div class="bg-[#1A1A1A] border border-[#2A2A2A] rounded-xl p-6 space-y-3">
                        <h3 class="text-[#F5F5F5] font-semibold text-sm uppercase tracking-widest mb-4">Explorar</h3>

                        <a href="{{ route('rankings.mejores-estado', $state->slug ?? strtolower($state->code)) }}"
                           class="flex items-center gap-3 text-gray-400 hover:text-[#D4AF37] transition-colors text-sm group">
                            <svg class="w-4 h-4 flex-shrink-0 group-hover:text-[#D4AF37]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                            Ranking de {{ $state->name }}
                        </a>

                        <a href="{{ route('restaurants.index', ['state' => $state->name, 'city' => $citySlug]) }}"
                           class="flex items-center gap-3 text-gray-400 hover:text-[#D4AF37] transition-colors text-sm group">
                            <svg class="w-4 h-4 flex-shrink-0 group-hover:text-[#D4AF37]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                            </svg>
                            Todos los restaurantes en {{ $cityName }}
                        </a>

                        <a href="{{ route('rankings.mejores-nacional') }}"
                           class="flex items-center gap-3 text-gray-400 hover:text-[#D4AF37] transition-colors text-sm group">
                            <svg class="w-4 h-4 flex-shrink-0 group-hover:text-[#D4AF37]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064"/>
                            </svg>
                            Ranking nacional
                        </a>
                    </div>

                    {{-- Top 3 highlight --}}
                    @if($restaurants->count() >= 1)
                    <div class="bg-[#1A1A1A] border border-[#D4AF37]/20 rounded-xl p-6">
                        <h3 class="text-[#D4AF37] font-bold text-sm uppercase tracking-widest mb-4">Top 3 en {{ $cityName }}</h3>
                        <div class="space-y-3">
                            @foreach($restaurants->take(3) as $top)
                            <a href="{{ route('restaurants.show', $top->slug) }}"
                               class="flex items-center gap-3 group">
                                <span class="text-xs font-bold w-5 text-center
                                    @if($loop->index === 0) text-[#D4AF37]
                                    @elseif($loop->index === 1) text-gray-400
                                    @else text-amber-700 @endif">
                                    #{{ $loop->index + 1 }}
                                </span>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm text-[#F5F5F5] group-hover:text-[#D4AF37] transition-colors truncate font-medium">
                                        {{ $top->name }}
                                    </p>
                                    @if($top->average_rating)
                                    <p class="text-xs text-gray-500">{{ number_format($top->average_rating, 1) }} ★</p>
                                    @endif
                                </div>
                            </a>
                            @endforeach
                        </div>
                    </div>
                    @endif

                </div>
            </aside>

        </div>
    </div>
</div>
@endsection

@push('scripts')
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
                "name": "{{ addslashes($restaurant->name) }}",
                "url": "{{ route('restaurants.show', $restaurant->slug) }}",
                "address": {
                    "@@type": "PostalAddress",
                    "streetAddress": "{{ addslashes($restaurant->address ?? '') }}",
                    "addressLocality": "{{ $cityName }}",
                    "addressRegion": "{{ $state->code }}",
                    "postalCode": "{{ $restaurant->zip_code }}",
                    "addressCountry": "US"
                },
                "servesCuisine": "Mexican",
                "telephone": "{{ $restaurant->phone }}"
                @if($restaurant->average_rating)
                ,"aggregateRating": {
                    "@@type": "AggregateRating",
                    "ratingValue": "{{ number_format($restaurant->average_rating, 1) }}",
                    "reviewCount": "{{ $restaurant->total_reviews ?? 0 }}",
                    "bestRating": "5",
                    "worstRating": "1"
                }
                @endif
                ,"priceRange": "{{ $restaurant->price_range ?? '$$' }}"
            }
        }@if(!$loop->last),@endif
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
            "name": "Mejores Restaurantes Mexicanos",
            "item": "{{ url('/mejores-restaurantes-mexicanos') }}"
        },
        {
            "@@type": "ListItem",
            "position": 3,
            "name": "{{ addslashes($state->name) }}",
            "item": "{{ url('/mejores/' . ($state->slug ?? strtolower($state->code ?? ''))) }}"
        },
        {
            "@@type": "ListItem",
            "position": 4,
            "name": "{{ addslashes($cityName) }}",
            "item": "{{ url('/mejores/' . strtolower($state->code ?? '') . '/' . $citySlug) }}"
        }
    ]
}
</script>
@endpush
