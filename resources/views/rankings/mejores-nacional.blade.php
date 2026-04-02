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

<div style="min-height:100vh; background:#0B0B0B; color:#F5F5F5;">

    {{-- Hero Section --}}
    <div style="background:#0B0B0B; border-bottom:1px solid rgba(212,175,55,0.3); position:relative; overflow:hidden;">
        <img src="https://images.unsplash.com/photo-1414235077428-338989a2e8c0?auto=format&fit=crop&w=1600&q=80"
             alt="" aria-hidden="true"
             style="position:absolute; inset:0; width:100%; height:100%; object-fit:cover; opacity:0.35; pointer-events:none;">
        <div style="position:absolute; inset:0; background:rgba(0,0,0,0.55); pointer-events:none;"></div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16" style="position:relative; z-index:1;">

            {{-- Breadcrumb --}}
            <nav style="margin-bottom:1.5rem;">
                <ol style="display:flex; flex-wrap:wrap; gap:0.5rem; align-items:center; font-size:0.875rem; color:#9CA3AF;">
                    <li><a href="{{ route('home') }}" style="color:#D4AF37; text-decoration:none;">Inicio</a></li>
                    <li style="color:#4B5563;">/</li>
                    <li style="color:#9CA3AF; font-weight:600;">Mejores Restaurantes Mexicanos</li>
                </ol>
            </nav>

            <h1 style="font-family:'Playfair Display',serif; font-size:clamp(2rem,5vw,3.5rem); font-weight:700; color:#F5F5F5; margin-bottom:1rem; line-height:1.2;">
                Los Mejores Restaurantes Mexicanos<br>
                <span style="color:#D4AF37;">en Estados Unidos {{ $year }}</span>
            </h1>

            <p style="font-size:1.125rem; color:#9CA3AF; max-width:48rem; margin-bottom:2.5rem; line-height:1.7;">
                Ranking oficial basado en {{ number_format($totalRestaurants) }}+ restaurantes evaluados por calificaciones de Google, Yelp y resenas de clientes reales.
            </p>

            {{-- Stats Pills --}}
            <div style="display:flex; flex-wrap:wrap; gap:1.5rem;">
                <div style="background:#1A1A1A; border:1px solid rgba(212,175,55,0.4); border-radius:0.75rem; padding:1rem 1.5rem; text-align:center;">
                    <div style="font-size:1.875rem; font-weight:700; color:#D4AF37;">{{ number_format($totalRestaurants) }}+</div>
                    <div style="font-size:0.875rem; color:#9CA3AF;">Restaurantes</div>
                </div>
                <div style="background:#1A1A1A; border:1px solid rgba(212,175,55,0.4); border-radius:0.75rem; padding:1rem 1.5rem; text-align:center;">
                    <div style="font-size:1.875rem; font-weight:700; color:#D4AF37;">{{ number_format($avgRating, 1) }}</div>
                    <div style="font-size:0.875rem; color:#9CA3AF;">Rating Promedio</div>
                </div>
                <div style="background:#1A1A1A; border:1px solid rgba(212,175,55,0.4); border-radius:0.75rem; padding:1rem 1.5rem; text-align:center;">
                    <div style="font-size:1.875rem; font-weight:700; color:#D4AF37;">50</div>
                    <div style="font-size:0.875rem; color:#9CA3AF;">Estados</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Body --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="lg:flex lg:gap-8">

            {{-- Main Content --}}
            <div class="lg:w-2/3">
                <h2 style="font-size:1.5rem; font-weight:700; color:#F5F5F5; margin-bottom:1.5rem;">
                    Top 100 Restaurantes Mexicanos que Debes Visitar en Estados Unidos
                </h2>

                <div style="display:flex; flex-direction:column; gap:1rem;">
                    @foreach($restaurants as $index => $restaurant)
                        <a href="{{ route('restaurants.show', $restaurant->slug) }}"
                           class="famer-card-link"
                           style="display:block; background:#1A1A1A; border:1px solid #2A2A2A; border-radius:12px; overflow:hidden; text-decoration:none; transition:border-color 0.2s, box-shadow 0.2s;"
                           onmouseover="this.style.borderColor='#D4AF37';this.style.boxShadow='0 4px 24px rgba(212,175,55,0.12)';"
                           onmouseout="this.style.borderColor='#2A2A2A';this.style.boxShadow='none';">
                            <div style="display:flex;">

                                {{-- Rank Badge --}}
                                <div style="flex-shrink:0; width:5rem; display:flex; align-items:center; justify-content:center;
                                    {{ $index < 3 ? 'background:#D4AF37;' : 'background:#0B0B0B;' }}">
                                    <span style="font-size:1.5rem; font-weight:800;
                                        {{ $index < 3 ? 'color:#0B0B0B;' : 'color:#6B7280;' }}">
                                        #{{ $index + 1 }}
                                    </span>
                                </div>

                                {{-- Restaurant Image --}}
                                <div style="flex-shrink:0; width:7rem; height:7rem; position:relative; overflow:hidden;">
                                    @php
                                        $imageUrl = $restaurant->getFirstMediaUrl('photos', 'thumb')
                                            ?: ($restaurant->yelp_photos[0] ?? null)
                                            ?: ($restaurant->image ? \Illuminate\Support\Facades\Storage::url($restaurant->image) : null);
                                    @endphp
                                    @if($imageUrl)
                                    <img src="{{ $imageUrl }}" alt="{{ $restaurant->name }}"
                                         style="width:100%; height:100%; object-fit:cover; transition:transform 0.3s;"
                                         onmouseover="this.style.transform='scale(1.05)';"
                                         onmouseout="this.style.transform='scale(1)';"
                                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                    @endif
                                    <div style="display:{{ $imageUrl ? 'none' : 'flex' }}; width:100%; height:100%; align-items:center; justify-content:center; background:#111; flex-direction:column; gap:0.25rem;">
                                        <span style="font-size:1.75rem;">🍽️</span>
                                        <span style="font-size:0.6rem; color:#4B5563; text-align:center; padding:0 0.25rem; line-height:1.2;">{{ Str::limit($restaurant->name, 18) }}</span>
                                    </div>
                                </div>

                                {{-- Restaurant Info --}}
                                <div style="flex:1; padding:1rem;">
                                    <div style="display:flex; align-items:flex-start; justify-content:space-between;">
                                        <div>
                                            <h3 style="font-size:1.125rem; font-weight:700; color:#F5F5F5; margin-bottom:0.25rem; transition:color 0.2s;">
                                                {{ $restaurant->name }}
                                            </h3>
                                            <p style="font-size:0.875rem; color:#9CA3AF;">
                                                {{ $restaurant->city }}, {{ $restaurant->state?->code }}
                                            </p>
                                        </div>
                                        @if($index < 3)
                                            <span style="font-size:1.5rem; flex-shrink:0;">
                                                @if($index === 0) 🥇 @endif
                                                @if($index === 1) 🥈 @endif
                                                @if($index === 2) 🥉 @endif
                                            </span>
                                        @endif
                                    </div>

                                    {{-- Rating --}}
                                    <div style="margin-top:0.5rem; display:flex; align-items:center; gap:0.75rem; flex-wrap:wrap;">
                                        <div style="display:flex; align-items:center;">
                                            @for($i = 1; $i <= 5; $i++)
                                                <svg style="width:1rem; height:1rem; color:{{ $i <= round($restaurant->average_rating) ? '#D4AF37' : '#374151' }};"
                                                     fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                </svg>
                                            @endfor
                                            <span style="margin-left:0.5rem; font-weight:700; color:#F5F5F5;">{{ number_format($restaurant->average_rating, 1) }}</span>
                                        </div>
                                        <span style="font-size:0.875rem; color:#6B7280;">({{ number_format($restaurant->total_reviews ?? 0) }} resenas)</span>
                                        @if($restaurant->price_range)
                                            <span style="font-size:0.875rem; color:#D4AF37; font-weight:500;">{{ $restaurant->price_range }}</span>
                                        @endif
                                    </div>

                                    {{-- Category --}}
                                    @if($restaurant->category)
                                        <span style="display:inline-block; margin-top:0.5rem; padding:0.125rem 0.5rem; background:rgba(212,175,55,0.1); color:#D4AF37; border:1px solid rgba(212,175,55,0.3); border-radius:0.25rem; font-size:0.75rem; font-weight:500;">
                                            {{ $restaurant->category->name }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>

                {{-- CTA --}}
                <div style="margin-top:3rem; text-align:center;">
                    <a href="{{ route('restaurants.index') }}"
                       style="display:inline-flex; align-items:center; padding:1rem 2rem; background:#D4AF37; color:#0B0B0B; font-weight:700; border-radius:0.75rem; text-decoration:none; transition:background 0.2s;"
                       onmouseover="this.style.background='#B08A1E';"
                       onmouseout="this.style.background='#D4AF37';">
                        Ver Todos los Restaurantes
                        <svg style="margin-left:0.5rem; width:1.25rem; height:1.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                        </svg>
                    </a>
                </div>
            </div>

            {{-- Sidebar --}}
            <div class="lg:w-1/3" style="margin-top:2rem;">
                <div class="lg:sticky lg:top-4" style="display:flex; flex-direction:column; gap:1.5rem;">

                    {{-- Top States --}}
                    <div style="background:#1A1A1A; border:1px solid #2A2A2A; border-radius:0.75rem; padding:1.5rem;">
                        <h3 style="font-size:1.125rem; font-weight:700; color:#F5F5F5; margin-bottom:1rem;">Mejores por Estado</h3>
                        <div style="display:flex; flex-direction:column; gap:0.75rem;">
                            @foreach($topStates as $stateData)
                                @if($stateData->state)
                                    <a href="{{ route('rankings.mejores-estado', $stateData->state->slug ?? strtolower($stateData->state->code)) }}"
                                       style="display:flex; align-items:center; justify-content:space-between; padding:0.75rem; background:#0B0B0B; border:1px solid #2A2A2A; border-radius:0.5rem; text-decoration:none; transition:border-color 0.2s, background 0.2s;"
                                       onmouseover="this.style.borderColor='#D4AF37';this.style.background='rgba(212,175,55,0.05)';"
                                       onmouseout="this.style.borderColor='#2A2A2A';this.style.background='#0B0B0B';">
                                        <span style="font-weight:500; color:#F5F5F5;">
                                            {{ $stateData->state->name }}
                                        </span>
                                        <span style="font-size:0.875rem; color:#9CA3AF;">{{ number_format($stateData->count) }} restaurantes</span>
                                    </a>
                                @endif
                            @endforeach
                        </div>
                    </div>

                    {{-- Quick Links --}}
                    <div style="background:#1A1A1A; border:1px solid #2A2A2A; border-radius:0.75rem; padding:1.5rem;">
                        <h3 style="font-size:1.125rem; font-weight:700; color:#F5F5F5; margin-bottom:1rem;">Rankings Populares</h3>
                        <div style="display:flex; flex-direction:column; gap:0.5rem;">
                            <a href="{{ route('rankings.top10-nacional') }}"
                               style="display:block; padding:0.75rem; background:rgba(212,175,55,0.08); border:1px solid rgba(212,175,55,0.25); border-radius:0.5rem; text-decoration:none; transition:background 0.2s, border-color 0.2s;"
                               onmouseover="this.style.background='rgba(212,175,55,0.15)';this.style.borderColor='rgba(212,175,55,0.5)';"
                               onmouseout="this.style.background='rgba(212,175,55,0.08)';this.style.borderColor='rgba(212,175,55,0.25)';">
                                <span style="font-weight:600; color:#D4AF37;">Top 10 Restaurantes Mexicanos</span>
                            </a>
                            <a href="{{ route('famer.awards') }}"
                               style="display:block; padding:0.75rem; background:rgba(212,175,55,0.08); border:1px solid rgba(212,175,55,0.25); border-radius:0.5rem; text-decoration:none; transition:background 0.2s, border-color 0.2s;"
                               onmouseover="this.style.background='rgba(212,175,55,0.15)';this.style.borderColor='rgba(212,175,55,0.5)';"
                               onmouseout="this.style.background='rgba(212,175,55,0.08)';this.style.borderColor='rgba(212,175,55,0.25)';">
                                <span style="font-weight:600; color:#D4AF37;">FAMER Awards {{ $year }}</span>
                            </a>
                        </div>
                    </div>

                    {{-- Methodology --}}
                    <div style="background:linear-gradient(135deg,#1A1A1A 0%,#0B0B0B 100%); border:1px solid rgba(212,175,55,0.2); border-radius:0.75rem; padding:1.5rem;">
                        <h3 style="font-size:1.125rem; font-weight:700; color:#F5F5F5; margin-bottom:0.75rem;">Como calculamos el ranking?</h3>
                        <ul style="display:flex; flex-direction:column; gap:0.5rem; list-style:none; padding:0; margin:0;">
                            <li style="display:flex; align-items:flex-start; gap:0.5rem; font-size:0.875rem; color:#9CA3AF;">
                                <svg style="width:1.25rem; height:1.25rem; color:#D4AF37; flex-shrink:0; margin-top:0.125rem;" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <span>Ratings de Google y Yelp (70%)</span>
                            </li>
                            <li style="display:flex; align-items:flex-start; gap:0.5rem; font-size:0.875rem; color:#9CA3AF;">
                                <svg style="width:1.25rem; height:1.25rem; color:#D4AF37; flex-shrink:0; margin-top:0.125rem;" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <span>Cantidad de resenas (30%)</span>
                            </li>
                            <li style="display:flex; align-items:flex-start; gap:0.5rem; font-size:0.875rem; color:#9CA3AF;">
                                <svg style="width:1.25rem; height:1.25rem; color:#D4AF37; flex-shrink:0; margin-top:0.125rem;" fill="currentColor" viewBox="0 0 20 20">
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
