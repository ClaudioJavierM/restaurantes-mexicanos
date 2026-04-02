@extends('layouts.app')

@section('title')Los Mejores {{ $data['title'] }} | FAMER@endsection
@section('meta_description'){{ $data['description'] }}@endsection

@push('meta')
<meta property="og:type" content="website">
<meta property="og:url" content="{{ url()->current() }}">
<meta property="og:title" content="Los Mejores {{ $data['title'] }} | FAMER">
<meta property="og:description" content="{{ $data['description'] }}">

<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $data['title'] }} | FAMER">
<meta name="twitter:description" content="{{ $data['description'] }}">
@endpush

@section('content')

{{-- ===================== HERO ===================== --}}
<div class="bg-[#0B0B0B] border-b border-[#D4AF37]/20">
    <div class="max-w-7xl mx-auto px-4 py-12 sm:px-6 lg:px-8">

        {{-- Breadcrumb --}}
        <nav class="text-sm mb-6 flex items-center gap-2 text-gray-500">
            <a href="{{ url('/') }}" class="hover:text-[#D4AF37] transition-colors">FAMER</a>
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <a href="{{ url('/restaurantes') }}" class="hover:text-[#D4AF37] transition-colors">Restaurantes</a>
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <span class="text-[#F5F5F5]">{{ $dishName }}</span>
        </nav>

        {{-- H1 --}}
        <h1 class="text-3xl md:text-5xl font-bold text-[#F5F5F5] mb-4" style="font-family: 'Playfair Display', serif;">
            Restaurantes con <span class="text-[#D4AF37]">{{ $dishName }}</span>
        </h1>
        <p class="text-lg text-gray-400 max-w-3xl">
            Encuentra los mejores <strong class="text-[#F5F5F5]">{{ strtolower($dishName) }}</strong> auténticos mexicanos.
            {{ $data['description'] }}
        </p>

        {{-- Stats --}}
        <div class="flex gap-4 mt-8 flex-wrap">
            <div class="bg-[#1A1A1A] border border-[#2A2A2A] rounded-xl px-6 py-4 text-center">
                <p class="text-3xl font-bold text-[#D4AF37]">{{ number_format($totalCount) }}</p>
                <p class="text-sm text-gray-400 mt-1">Restaurantes</p>
            </div>
            <div class="bg-[#1A1A1A] border border-[#2A2A2A] rounded-xl px-6 py-4 text-center">
                <p class="text-3xl font-bold text-[#D4AF37]">Auténtico</p>
                <p class="text-sm text-gray-400 mt-1">Cocina Mexicana</p>
            </div>
        </div>
    </div>
</div>

<div class="bg-[#0B0B0B] min-h-screen">
    <div class="max-w-7xl mx-auto px-4 py-10 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">

            {{-- ===================== SIDEBAR ===================== --}}
            <aside class="lg:col-span-1 order-last lg:order-first">
                <div class="bg-[#1A1A1A] border border-[#2A2A2A] rounded-xl p-6 sticky top-24 space-y-6">

                    {{-- About this dish --}}
                    <div>
                        <h3 class="font-bold text-[#F5F5F5] mb-3 text-sm uppercase tracking-wider">
                            Sobre {{ $dishName }}
                        </h3>
                        <p class="text-sm text-gray-400 leading-relaxed">{{ $data['body'] }}</p>
                    </div>

                    <div class="border-t border-[#2A2A2A]"></div>

                    {{-- Other dishes --}}
                    <div>
                        <h3 class="font-bold text-[#F5F5F5] mb-3 text-sm uppercase tracking-wider">Otros Platillos</h3>
                        <ul class="space-y-1">
                            @foreach([
                                'birria'          => 'Birria',
                                'tacos'           => 'Tacos',
                                'tamales'         => 'Tamales',
                                'pozole'          => 'Pozole',
                                'enchiladas'      => 'Enchiladas',
                                'mole'            => 'Mole',
                                'carnitas'        => 'Carnitas',
                                'barbacoa'        => 'Barbacoa',
                                'menudo'          => 'Menudo',
                                'chiles-rellenos' => 'Chiles Rellenos',
                                'ceviche'         => 'Ceviche',
                            ] as $slug => $label)
                            @if($slug !== $dish)
                            <li>
                                <a href="{{ url('/' . $slug) }}"
                                   class="flex justify-between items-center text-gray-400 hover:text-[#D4AF37] transition-colors text-sm py-1">
                                    <span>{{ $label }}</span>
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                </a>
                            </li>
                            @endif
                            @endforeach
                        </ul>
                    </div>

                    <div class="border-t border-[#2A2A2A]"></div>

                    {{-- CTA --}}
                    <div>
                        <h3 class="font-bold text-[#F5F5F5] mb-2 text-sm uppercase tracking-wider">¿Eres Dueño?</h3>
                        <p class="text-sm text-gray-400 mb-4">Reclama tu restaurante y muestra tu menú en FAMER.</p>
                        <a href="{{ route('claim.restaurant') }}"
                           class="block w-full text-center bg-[#D4AF37] text-[#0B0B0B] py-2.5 rounded-lg font-bold text-sm hover:bg-[#E8C67A] transition-colors">
                            Reclamar Gratis
                        </a>
                    </div>
                </div>
            </aside>

            {{-- ===================== MAIN CONTENT ===================== --}}
            <main class="lg:col-span-3">

                {{-- Section heading --}}
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-1 h-8 bg-[#D4AF37] rounded-full"></div>
                    <h2 class="text-2xl font-bold text-[#F5F5F5]" style="font-family: 'Playfair Display', serif;">
                        Los Mejores Restaurantes de {{ $dishName }}
                    </h2>
                </div>
                <p class="text-gray-400 text-sm mb-6">
                    {{ number_format($totalCount) }} restaurantes · Ordenados por calificación.
                </p>

                {{-- Restaurant Grid --}}
                @forelse($restaurants as $restaurant)
                @php
                    $isElite   = $restaurant->subscription_tier === 'elite';
                    $isPremium = $restaurant->subscription_tier === 'premium';
                    $restImage = null;
                    if ($restaurant->image) {
                        $restImage = str_starts_with($restaurant->image, 'http')
                            ? $restaurant->image
                            : asset('storage/' . $restaurant->image);
                    } elseif (method_exists($restaurant, 'getFirstMediaUrl') && $restaurant->getFirstMediaUrl('images')) {
                        $restImage = $restaurant->getFirstMediaUrl('images');
                    }
                    $rating  = method_exists($restaurant, 'getWeightedRating') ? $restaurant->getWeightedRating() : ($restaurant->average_rating ?? 0);
                    $reviews = method_exists($restaurant, 'getCombinedReviewCount') ? $restaurant->getCombinedReviewCount() : ($restaurant->total_reviews ?? 0);
                @endphp

                <a href="{{ route('restaurants.show', $restaurant->slug) }}"
                   class="flex gap-4 bg-[#1A1A1A] border {{ $isElite ? 'border-[#D4AF37]/50' : ($isPremium ? 'border-[#D4AF37]/20' : 'border-[#2A2A2A]') }} rounded-xl p-4 mb-4 hover:border-[#D4AF37]/40 hover:bg-[#1F1F1F] transition-all group">

                    {{-- Image --}}
                    <div class="flex-shrink-0 w-20 h-20 rounded-lg overflow-hidden bg-[#2A2A2A]">
                        @if($restImage)
                            <img src="{{ $restImage }}"
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
                        @if($rating > 0)
                        <div class="flex items-center gap-2 mt-1.5">
                            <div class="flex text-[#D4AF37]">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= round($rating))
                                        <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/></svg>
                                    @else
                                        <svg class="w-3.5 h-3.5 fill-current text-gray-600" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/></svg>
                                    @endif
                                @endfor
                            </div>
                            <span class="text-sm text-gray-300 font-semibold">{{ number_format($rating, 1) }}</span>
                            <span class="text-xs text-gray-500">({{ number_format($reviews) }} reseñas)</span>
                        </div>
                        @endif

                        {{-- Location & meta --}}
                        <div class="flex items-center gap-3 mt-1.5 flex-wrap">
                            @if($restaurant->city)
                            <p class="text-xs text-gray-500">
                                <svg class="w-3 h-3 inline mr-0.5 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                {{ $restaurant->city }}@if($restaurant->state), {{ $restaurant->state->code ?? '' }}@endif
                            </p>
                            @endif
                            @if($restaurant->price_range)
                            <span class="text-xs text-gray-500">{{ $restaurant->price_range }}</span>
                            @endif
                            @if($restaurant->category)
                            <span class="text-xs bg-[#2A2A2A] text-gray-400 px-2 py-0.5 rounded-full">{{ $restaurant->category->name }}</span>
                            @endif
                        </div>
                    </div>
                </a>
                @empty
                <div class="text-center py-16 bg-[#1A1A1A] border border-[#2A2A2A] rounded-xl">
                    <p class="text-4xl mb-4">🍽️</p>
                    <p class="text-gray-400">No se encontraron restaurantes para <strong class="text-[#F5F5F5]">{{ $dishName }}</strong> aún.</p>
                    <p class="text-sm text-gray-500 mt-2">¿Tienes un restaurante? <a href="{{ route('claim.restaurant') }}" class="text-[#D4AF37] hover:underline">Reclámalo gratis</a></p>
                </div>
                @endforelse

                {{-- Pagination --}}
                @if($restaurants->hasPages())
                <div class="mt-8">
                    {{ $restaurants->links() }}
                </div>
                @endif

                {{-- SEO Content Block --}}
                <section class="mt-12 bg-[#1A1A1A] border border-[#2A2A2A] rounded-xl p-8">
                    <h2 class="text-xl font-bold text-[#F5F5F5] mb-4" style="font-family: 'Playfair Display', serif;">
                        ¿Dónde comer {{ $dishName }} auténtica?
                    </h2>
                    <div class="text-gray-400 space-y-4 text-sm leading-relaxed">
                        <p>{{ $data['body'] }}</p>
                        <p>
                            Nuestra guía incluye <strong class="text-[#F5F5F5]">{{ number_format($totalCount) }} restaurantes verificados</strong>
                            que sirven {{ strtolower($dishName) }} auténtica. Todos los datos de calificaciones provienen de Google y son verificados periódicamente.
                        </p>
                    </div>

                    {{-- State links grid --}}
                    @if(in_array($dish, ['birria','tamales','pozole','carnitas','barbacoa','mole','tacos','enchiladas']))
                    <div class="mt-6 grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-3">
                        @foreach(['tx'=>'Texas','ca'=>'California','il'=>'Illinois','az'=>'Arizona','fl'=>'Florida','co'=>'Colorado','nv'=>'Nevada','nm'=>'Nuevo México','ny'=>'Nueva York','wa'=>'Washington'] as $code => $stateName)
                        <a href="{{ url('/' . $dish . '-en-' . $code) }}"
                           class="bg-[#0B0B0B] border border-[#2A2A2A] rounded-lg p-3 text-center hover:border-[#D4AF37]/40 transition-colors">
                            <div class="text-xs font-semibold text-[#F5F5F5]">{{ $stateName }}</div>
                            <div class="text-xs text-[#D4AF37] mt-0.5">{{ strtoupper($code) }}</div>
                        </a>
                        @endforeach
                    </div>
                    @endif
                </section>

            </main>
        </div>
    </div>
</div>

@push('scripts')
{{-- ItemList Schema --}}
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "ItemList",
    "name": "{{ addslashes($data['title']) }}",
    "description": "{{ addslashes($data['description']) }}",
    "numberOfItems": {{ $totalCount }},
    "itemListElement": [
        @foreach($restaurants->take(10) as $schemaIndex => $schemaRest)
        @php
            $schemaRating = method_exists($schemaRest, 'getWeightedRating') ? $schemaRest->getWeightedRating() : ($schemaRest->average_rating ?? 0);
        @endphp
        {
            "@@type": "ListItem",
            "position": {{ $schemaIndex + 1 }},
            "item": {
                "@@type": "Restaurant",
                "name": "{{ addslashes($schemaRest->name) }}",
                "url": "{{ route('restaurants.show', $schemaRest->slug) }}",
                "servesCuisine": "Mexican"
                @if($schemaRest->city || ($schemaRest->state ?? null)),
                "address": {
                    "@@type": "PostalAddress",
                    "addressLocality": "{{ addslashes($schemaRest->city ?? '') }}"
                    @if($schemaRest->state ?? null),
                    "addressRegion": "{{ addslashes($schemaRest->state->code ?? '') }}"
                    @endif
                }
                @endif
                @if($schemaRating > 0),
                "aggregateRating": {
                    "@@type": "AggregateRating",
                    "ratingValue": "{{ number_format($schemaRating, 1) }}",
                    "bestRating": "5",
                    "ratingCount": "{{ $schemaRest->total_reviews ?? 0 }}"
                }
                @endif
            }
        }{{ $loop->last ? '' : ',' }}
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
            "name": "FAMER",
            "item": "{{ url('/') }}"
        },
        {
            "@@type": "ListItem",
            "position": 2,
            "name": "Restaurantes",
            "item": "{{ url('/restaurantes') }}"
        },
        {
            "@@type": "ListItem",
            "position": 3,
            "name": "{{ addslashes($dishName) }}",
            "item": "{{ url()->current() }}"
        }
    ]
}
</script>
@endpush

@endsection
