@extends('layouts.app')

@section('title')
Mejores Restaurantes Mexicanos en {{ $state->name }} {{ $year }} | FAMER
@endsection

@section('meta_description')
Los {{ number_format($stats->total) }} mejores restaurantes mexicanos en {{ $state->name }} {{ $year }}. Ranking con calificaciones verificadas.
@endsection

@section('content')

{{-- Geographic Meta --}}
<meta name="geo.region" content="US-{{ $state->code }}" />
<meta name="geo.placename" content="{{ $state->name }}" />

{{-- Open Graph --}}
<x-open-graph
    title="Mejores Restaurantes Mexicanos en {{ $state->name }} {{ $year }}"
    description="Top {{ $restaurants->count() }} restaurantes mexicanos en {{ $state->name }}. Rating promedio: {{ number_format($stats->avg_rating, 1) }} estrellas."
    type="website"
/>

<div class="min-h-screen" style="background-color:#0B0B0B;">

    {{-- Hero --}}
    <div class="border-b" style="background-color:#0B0B0B; border-color:rgba(212,175,55,0.2);">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

            {{-- Breadcrumb --}}
            <nav class="text-sm mb-8">
                <ol class="flex items-center flex-wrap gap-2">
                    <li>
                        <a href="{{ route('home') }}" class="transition-colors" style="color:rgba(245,245,245,0.6);" onmouseover="this.style.color='#D4AF37'" onmouseout="this.style.color='rgba(245,245,245,0.6)'">Inicio</a>
                    </li>
                    <li style="color:rgba(245,245,245,0.3);">/</li>
                    <li>
                        <a href="{{ route('rankings.mejores-nacional') }}" class="transition-colors" style="color:rgba(245,245,245,0.6);" onmouseover="this.style.color='#D4AF37'" onmouseout="this.style.color='rgba(245,245,245,0.6)'">Mejores Restaurantes</a>
                    </li>
                    <li style="color:rgba(245,245,245,0.3);">/</li>
                    <li style="color:#F5F5F5;" class="font-semibold">{{ $state->name }}</li>
                </ol>
            </nav>

            {{-- Heading --}}
            <div class="mb-8">
                <div class="flex items-center gap-3 mb-4">
                    <div class="h-px flex-1" style="background:linear-gradient(to right, #D4AF37, transparent);"></div>
                    <span class="text-xs font-semibold tracking-widest uppercase" style="color:#D4AF37;">Ranking {{ $year }}</span>
                    <div class="h-px flex-1" style="background:linear-gradient(to left, #D4AF37, transparent);"></div>
                </div>
                <h1 class="text-3xl md:text-4xl lg:text-5xl font-extrabold mb-3" style="color:#F5F5F5; font-family:'Playfair Display',serif;">
                    Mejores Restaurantes Mexicanos
                </h1>
                <h2 class="text-2xl md:text-3xl font-bold" style="color:#D4AF37; font-family:'Playfair Display',serif;">
                    en {{ $state->name }}
                </h2>
            </div>

            {{-- Stats bar --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-8">
                <div class="rounded-xl p-4 border" style="background-color:#1A1A1A; border-color:#2A2A2A;">
                    <div class="text-2xl font-bold" style="color:#D4AF37;">{{ number_format($stats->total) }}</div>
                    <div class="text-sm mt-1" style="color:rgba(245,245,245,0.6);">Restaurantes</div>
                </div>
                <div class="rounded-xl p-4 border" style="background-color:#1A1A1A; border-color:#2A2A2A;">
                    <div class="text-2xl font-bold" style="color:#D4AF37;">{{ number_format($stats->avg_rating, 1) }} ★</div>
                    <div class="text-sm mt-1" style="color:rgba(245,245,245,0.6);">Rating Promedio</div>
                </div>
                <div class="rounded-xl p-4 border" style="background-color:#1A1A1A; border-color:#2A2A2A;">
                    <div class="text-2xl font-bold" style="color:#D4AF37;">{{ number_format($stats->total_reviews) }}</div>
                    <div class="text-sm mt-1" style="color:rgba(245,245,245,0.6);">Reseñas Totales</div>
                </div>
                <div class="rounded-xl p-4 border" style="background-color:#1A1A1A; border-color:#2A2A2A;">
                    <div class="text-2xl font-bold" style="color:#D4AF37;">Top {{ $restaurants->count() }}</div>
                    <div class="text-sm mt-1" style="color:rgba(245,245,245,0.6);">Este Ranking</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Main content --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="lg:flex lg:gap-8">

            {{-- Ranking list --}}
            <div class="lg:w-2/3">
                <h2 class="text-xl font-bold mb-6" style="color:#F5F5F5; font-family:'Playfair Display',serif;">
                    Top {{ $restaurants->count() }} en {{ $state->name }}
                </h2>

                <div class="space-y-3">
                    @foreach($restaurants as $index => $restaurant)
                        @php
                            $imageUrl = $restaurant->getFirstMediaUrl('photos', 'thumb')
                                ?: ($restaurant->yelp_photos[0] ?? '/images/placeholder-restaurant.jpg');
                        @endphp
                        <a href="{{ route('restaurants.show', $restaurant->slug) }}"
                           class="flex items-center gap-4 rounded-xl p-4 border transition-all group"
                           style="background-color:#1A1A1A; border-color:#2A2A2A;"
                           onmouseover="this.style.borderColor='rgba(212,175,55,0.4)'"
                           onmouseout="this.style.borderColor='#2A2A2A'">

                            {{-- Rank badge --}}
                            <div class="w-12 h-12 rounded-full flex items-center justify-center flex-shrink-0 font-bold text-base
                                @if($index === 0) text-black
                                @elseif($index === 1) text-black
                                @elseif($index === 2) text-white
                                @else text-gray-400 @endif"
                                style="
                                @if($index === 0) background-color:#D4AF37;
                                @elseif($index === 1) background-color:#9CA3AF;
                                @elseif($index === 2) background-color:#92400E;
                                @else background-color:#2A2A2A; @endif">
                                #{{ $index + 1 }}
                            </div>

                            {{-- Image --}}
                            <div class="flex-shrink-0 w-16 h-16 rounded-lg overflow-hidden" style="border:1px solid #2A2A2A;">
                                <img src="{{ $imageUrl }}"
                                     alt="{{ $restaurant->name }}"
                                     class="w-full h-full object-cover">
                            </div>

                            {{-- Info --}}
                            <div class="flex-1 min-w-0">
                                <h3 class="font-bold truncate transition-colors group-hover:text-[#D4AF37]" style="color:#F5F5F5;">
                                    {{ $restaurant->name }}
                                </h3>
                                <p class="text-sm mt-0.5" style="color:rgba(245,245,245,0.5);">
                                    {{ $restaurant->city }}, {{ $state->code }}
                                </p>
                                <div class="flex items-center gap-2 mt-1">
                                    <span class="font-bold text-sm" style="color:#D4AF37;">
                                        {{ number_format($restaurant->average_rating, 1) }} ★
                                    </span>
                                    <span class="text-xs" style="color:rgba(245,245,245,0.4);">
                                        ({{ number_format($restaurant->total_reviews ?? 0) }} reseñas)
                                    </span>
                                </div>
                            </div>

                            {{-- Arrow --}}
                            <div class="flex-shrink-0 transition-transform group-hover:translate-x-1" style="color:rgba(212,175,55,0.5);">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </div>
                        </a>
                    @endforeach
                </div>

                {{-- CTA --}}
                <div class="mt-10 text-center">
                    <a href="{{ route('restaurants.index', ['state' => $state->name]) }}"
                       class="inline-flex items-center gap-2 px-8 py-3 rounded-xl font-bold transition-all"
                       style="background-color:#D4AF37; color:#0B0B0B;"
                       onmouseover="this.style.backgroundColor='rgba(212,175,55,0.8)'"
                       onmouseout="this.style.backgroundColor='#D4AF37'">
                        Ver Todos los Restaurantes en {{ $state->name }}
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                        </svg>
                    </a>
                </div>
            </div>

            {{-- Sidebar --}}
            <div class="lg:w-1/3 mt-10 lg:mt-0">
                <div class="lg:sticky lg:top-6 space-y-6">

                    {{-- Top Cities --}}
                    <div class="rounded-xl border p-6" style="background-color:#1A1A1A; border-color:#2A2A2A;">
                        <h3 class="text-base font-bold mb-4" style="color:#F5F5F5; font-family:'Playfair Display',serif;">
                            Ciudades Principales
                        </h3>
                        <div class="space-y-2">
                            @foreach($topCities as $city)
                                <a href="{{ route('rankings.mejores-ciudad', [$state->slug ?? strtolower($state->code), Str::slug($city->city)]) }}"
                                   class="flex items-center justify-between p-3 rounded-lg border transition-all group"
                                   style="background-color:#0B0B0B; border-color:#2A2A2A;"
                                   onmouseover="this.style.borderColor='rgba(212,175,55,0.4)'"
                                   onmouseout="this.style.borderColor='#2A2A2A'">
                                    <span class="font-medium text-sm transition-colors group-hover:text-[#D4AF37]" style="color:#F5F5F5;">
                                        {{ $city->city }}
                                    </span>
                                    <div class="flex items-center gap-2 text-xs">
                                        <span style="color:rgba(245,245,245,0.4);">{{ $city->count }}</span>
                                        <span style="color:#D4AF37;">{{ number_format($city->avg_rating, 1) }}★</span>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>

                    {{-- National Ranking link --}}
                    <div class="rounded-xl border p-6" style="background-color:#1A1A1A; border-color:#2A2A2A;">
                        <h3 class="text-base font-bold mb-4" style="color:#F5F5F5; font-family:'Playfair Display',serif;">
                            Ranking Nacional
                        </h3>
                        <a href="{{ route('rankings.mejores-nacional') }}"
                           class="flex items-center justify-between p-4 rounded-xl border transition-all group"
                           style="background-color:#0B0B0B; border-color:rgba(212,175,55,0.3);"
                           onmouseover="this.style.borderColor='rgba(212,175,55,0.7)'"
                           onmouseout="this.style.borderColor='rgba(212,175,55,0.3)'">
                            <div>
                                <div class="font-semibold text-sm" style="color:#D4AF37;">Ver Ranking Nacional</div>
                                <div class="text-xs mt-0.5" style="color:rgba(245,245,245,0.5);">Los mejores de todo el país</div>
                            </div>
                            <svg class="w-5 h-5 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color:#D4AF37;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </a>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>

@endsection

@push('scripts')
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
        }
    ]
}
</script>
@endpush
