@section('title', $isMexico
    ? 'Restaurantes Mexicanos Famosos — El Directorio #1 de México'
    : 'Restaurantes Mexicanos Famosos en USA — FAMER | 26,000+ Restaurantes')
@section('meta_description', $isMexico
    ? 'Descubre los mejores restaurantes mexicanos en todo México. Reseñas reales, horarios, menús y rankings por estado y ciudad.'
    : 'Directorio de 26,000+ restaurantes mexicanos auténticos en Estados Unidos. Encuentra el mejor cerca de ti con calificaciones reales, fotos y reseñas.')

@push('meta')
<meta property="og:type" content="website">
@if($isMexico)
<meta property="og:title" content="🇲🇽 Restaurantes Mexicanos Famosos — El Directorio #1 de México">
<meta property="og:description" content="Descubre los mejores restaurantes de cocina mexicana auténtica. Reseñas reales, horarios, fotos y rankings por estado. Encuentra el tuyo en segundos.">
<meta property="og:image" content="https://images.unsplash.com/photo-1565299585323-38d6b0865b47?auto=format&fit=crop&w=1200&h=630&q=85">
@else
<meta property="og:title" content="🇲🇽 Los Mejores Restaurantes Mexicanos en USA — FAMER | 26,000+ Opciones">
<meta property="og:description" content="El directorio más completo de cocina mexicana auténtica en Estados Unidos. 26,000+ restaurantes con reseñas reales, fotos, horarios y rankings. ¿Cuál está cerca de ti?">
<meta property="og:image" content="https://images.unsplash.com/photo-1565299585323-38d6b0865b47?auto=format&fit=crop&w=1200&h=630&q=85">
@endif
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">
<meta property="og:image:alt" content="FAMER — Famous Mexican Restaurants">
<meta property="og:url" content="{{ url()->current() }}">
<meta property="og:site_name" content="FAMER - Famous Mexican Restaurants">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $isMexico ? '🇲🇽 Restaurantes Mexicanos Famosos — El Directorio #1' : '🇲🇽 Los Mejores Restaurantes Mexicanos en USA — FAMER' }}">
<meta name="twitter:description" content="{{ $isMexico ? 'El directorio más completo de cocina mexicana. Reseñas reales, rankings, fotos y horarios.' : '26,000+ restaurantes mexicanos auténticos en Estados Unidos. Encuentra el mejor cerca de ti.' }}">
<meta name="twitter:image" content="https://images.unsplash.com/photo-1565299585323-38d6b0865b47?auto=format&fit=crop&w=1200&h=630&q=85">
@endpush

<div>
@if($isMexico)
    @include("partials.mexico.hero")
    @include("partials.mexico.top100")
    @include("partials.mexico.top-states")
    @include("partials.mexico.about")
@else
    @include("partials.usa.hero")

    {{-- Near Me CTA Banner --}}
    <section style="padding:2rem 0; background:#0B0B0B;">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <a href="/restaurantes-mexicanos-cerca-de-mi"
               style="display:flex; align-items:center; justify-content:space-between; background:linear-gradient(135deg,#1A1A1A 0%,#2A2A2A 100%); border:1px solid #D4AF37; border-radius:16px; padding:1.5rem 2rem; text-decoration:none; flex-wrap:wrap; gap:1rem;"
               onmouseover="this.style.opacity='0.9'" onmouseout="this.style.opacity='1'">
                <div style="display:flex; align-items:center; gap:1rem;">
                    <span style="font-size:2rem;">📍</span>
                    <div>
                        <div style="font-weight:700; color:#F5F5F5; font-size:1.1rem;">Restaurantes Mexicanos Cerca de Mí</div>
                        <div style="color:#9CA3AF; font-size:0.875rem;">Usa tu ubicación para encontrar los más cercanos</div>
                    </div>
                </div>
                <span style="background:#D4AF37; color:#0B0B0B; font-weight:700; padding:0.625rem 1.25rem; border-radius:9999px; font-size:0.9rem; white-space:nowrap;">
                    Buscar Ahora →
                </span>
            </a>
        </div>
    </section>

    {{-- Trust / Authority Strip --}}
    <section class="py-6 border-t border-b border-[#D4AF37]/10" style="background-color: #1A1A1A;">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 md:gap-8">
                <div class="flex items-center justify-center gap-3">
                    <svg class="w-6 h-6 text-[#D4AF37] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                    <div>
                        <div class="text-[#D4AF37] font-bold text-lg leading-tight">{{ number_format($stats['total_restaurants'] ?? 0) }}+</div>
                        <div class="text-gray-400 text-xs font-medium">Restaurants</div>
                    </div>
                </div>
                <div class="flex items-center justify-center gap-3">
                    <svg class="w-6 h-6 text-[#D4AF37] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    <div>
                        <div class="text-[#D4AF37] font-bold text-lg leading-tight">2M+</div>
                        <div class="text-gray-400 text-xs font-medium">Annual Visits</div>
                    </div>
                </div>
                <div class="flex items-center justify-center gap-3">
                    <svg class="w-6 h-6 text-[#D4AF37] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    <div>
                        <div class="text-[#D4AF37] font-bold text-lg leading-tight">Top 10</div>
                        <div class="text-gray-400 text-xs font-medium">Rankings by City</div>
                    </div>
                </div>
                <div class="flex items-center justify-center gap-3">
                    <svg class="w-6 h-6 text-[#D4AF37] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                    <div>
                        <div class="text-[#D4AF37] font-bold text-lg leading-tight">Verified</div>
                        <div class="text-gray-400 text-xs font-medium">Listings</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Review Sources Strip --}}
    <section style="background:#111111; border-top:1px solid rgba(212,175,55,0.08); border-bottom:1px solid rgba(212,175,55,0.08); padding:0.875rem 1rem;">
        <div class="max-w-7xl mx-auto flex flex-wrap justify-center items-center gap-1">
            <span style="color:#6B7280; font-size:0.7rem; font-weight:600; text-transform:uppercase; letter-spacing:0.12em; margin-right:0.5rem;">{{ __('app.reviews_from_platforms') }}:</span>
            @foreach(['Google','Yelp','TripAdvisor','Facebook','Foursquare','Apple Maps','Uber Eats','OpenTable'] as $p)
            <span style="color:#4B5563; font-size:0.8rem; font-weight:600; padding:0.2rem 0.6rem;">{{ $p }}</span>
            @if(!$loop->last)<span style="color:#2A2A2A; font-size:0.75rem;">·</span>@endif
            @endforeach
        </div>
    </section>

    {{-- Explora por Platillo --}}
    <section style="padding:3rem 0; background:#0B0B0B;">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 style="font-family:'Playfair Display',serif; font-size:1.75rem; font-weight:700; color:#F5F5F5; margin-bottom:0.5rem;">
                Explora por Platillo
            </h2>
            <p style="color:#9CA3AF; margin-bottom:1.5rem; font-size:0.95rem;">Encuentra restaurantes especializados en tus platillos favoritos</p>
            <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 gap-3">
                @foreach([
                    ['birria',       'Birria',          'https://images.unsplash.com/photo-1585803518902-58a3ef005b51?auto=format&fit=crop&w=400&q=75'],
                    ['tamales',      'Tamales',         'https://images.unsplash.com/photo-1552332386-1634d96809cf?auto=format&fit=crop&w=400&q=75'],
                    ['pozole',       'Pozole',          'https://images.unsplash.com/photo-1695088220737-9a6d901db8f1?auto=format&fit=crop&w=400&q=75'],
                    ['enchiladas',   'Enchiladas',      'https://images.unsplash.com/photo-1613514967307-d5b3471b2453?auto=format&fit=crop&w=400&q=75'],
                    ['tacos-al-pastor','Tacos al Pastor','https://images.unsplash.com/photo-1582234363542-ee64d0ccb0d5?auto=format&fit=crop&w=400&q=75'],
                    ['mole',         'Mole',            'https://images.unsplash.com/photo-1646678259179-b0c6d5b421a5?auto=format&fit=crop&w=400&q=75'],
                    ['menudo',       'Menudo',          'https://images.unsplash.com/photo-1730243338482-78d6ffd943df?auto=format&fit=crop&w=400&q=75'],
                    ['chiles-rellenos','Chiles Rellenos','https://images.unsplash.com/photo-1708536892634-18ccb18cd513?auto=format&fit=crop&w=400&q=75'],
                    ['carne-asada',  'Carne Asada',     'https://images.unsplash.com/photo-1619719015339-133a130520f6?auto=format&fit=crop&w=400&q=75'],
                    ['carnitas',     'Carnitas',        'https://images.unsplash.com/photo-1512427691650-15fcce1dc7b1?auto=format&fit=crop&w=400&q=75'],
                    ['barbacoa',     'Barbacoa',        'https://images.unsplash.com/photo-1605209699023-97227c885c7b?auto=format&fit=crop&w=400&q=75'],
                    ['tacos',        'Tacos',           'https://images.unsplash.com/photo-1552332386-f8dd00dc2f85?auto=format&fit=crop&w=400&q=75'],
                ] as [$slug, $name, $photo])
                <a href="/{{ $slug }}"
                   style="display:block; border-radius:12px; overflow:hidden; border:1px solid #2A2A2A; text-decoration:none; position:relative; transition:border-color 0.2s, transform 0.2s;"
                   onmouseover="this.style.borderColor='#D4AF37';this.style.transform='translateY(-2px)'" onmouseout="this.style.borderColor='#2A2A2A';this.style.transform='translateY(0)'">
                    <div style="height:100px; overflow:hidden;">
                        <img src="{{ $photo }}" alt="{{ $name }}" loading="lazy"
                             style="width:100%; height:100%; object-fit:cover; transition:transform 0.4s;"
                             onmouseover="this.style.transform='scale(1.08)'" onmouseout="this.style.transform='scale(1)'">
                    </div>
                    <div style="background:linear-gradient(to top, rgba(11,11,11,0.95) 0%, rgba(11,11,11,0.5) 60%, transparent 100%); position:absolute; inset:0; pointer-events:none;"></div>
                    <div style="position:absolute; bottom:0; left:0; right:0; padding:0.5rem 0.6rem;">
                        <span style="font-weight:600; color:#F5F5F5; font-size:0.75rem; line-height:1.2; display:block; text-shadow:0 1px 3px rgba(0,0,0,0.8);">{{ $name }}</span>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Recent Activity Section --}}
    <section class="py-14 md:py-20" style="background-color: #0B0B0B;">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Section Header --}}
            <div class="text-center mb-10">
                <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full border border-[#D4AF37]/30 mb-4" style="background-color: rgba(212,175,55,0.08);">
                    <svg class="w-4 h-4 text-[#D4AF37]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                    <span class="text-[#D4AF37] text-xs font-semibold uppercase tracking-[0.15em]">Live</span>
                </div>
                <h2 class="text-3xl md:text-4xl font-display font-black text-[#F5F5F5]">
                    What's Happening Near You
                </h2>
                @if($detectedLocation && isset($detectedLocation['state_code']))
                    <p class="text-gray-400 mt-2 text-base">Recent activity in {{ $detectedLocation['state'] ?? $detectedLocation['state_code'] }}</p>
                @else
                    <p class="text-gray-400 mt-2 text-base">Latest reviews and additions across the platform</p>
                @endif
            </div>

            @if($recentActivity->count() > 0)
                {{-- Grid: 1 col mobile, 2 cols tablet, 3 cols desktop --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($recentActivity as $item)
                        <a href="/restaurante/{{ $item->restaurant_slug }}"
                           class="group block rounded-2xl overflow-hidden border border-white/5 hover:border-[#D4AF37]/30 transition-all duration-300 hover:shadow-lg hover:shadow-[#D4AF37]/5"
                           style="background-color: #1A1A1A;">

                            {{-- Image --}}
                            <div class="relative h-40 overflow-hidden">
                                @if($item->image)
                                    <img src="{{ $item->image }}" alt="{{ $item->restaurant_name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" loading="lazy"
                                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                    <div class="hidden w-full h-full items-center justify-center" style="background: linear-gradient(135deg, #1A1A1A, #2A2A2A);">
                                        <svg class="w-10 h-10 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                    </div>
                                @else
                                    <div class="w-full h-full flex items-center justify-center" style="background: linear-gradient(135deg, #1A1A1A, #2A2A2A);">
                                        <svg class="w-10 h-10 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                    </div>
                                @endif
                                {{-- Badge overlay --}}
                                <div class="absolute top-3 left-3">
                                    @if($item->type === 'review')
                                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-[#D4AF37]/90 text-[#0B0B0B]">
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                            Review
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-emerald-500/90 text-white">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                                            New
                                        </span>
                                    @endif
                                </div>
                                <div class="absolute top-3 right-3">
                                    <span class="text-white/70 text-[10px] bg-black/50 px-2 py-0.5 rounded-full backdrop-blur-sm">{{ $item->time_ago }}</span>
                                </div>
                            </div>

                            {{-- Content --}}
                            <div class="p-4">
                                <h3 class="text-[#F5F5F5] font-bold text-sm leading-snug group-hover:text-[#D4AF37] transition-colors mb-1 truncate">
                                    {{ $item->restaurant_name }}
                                </h3>
                                <p class="text-gray-500 text-xs mb-2">
                                    @if($item->city && $item->state_code)
                                        {{ $item->city }}, {{ $item->state_code }}
                                    @elseif($item->state_name)
                                        {{ $item->state_name }}
                                    @endif
                                </p>

                                @if($item->type === 'review')
                                    <div class="flex items-center gap-1 mb-1">
                                        @for($i = 1; $i <= 5; $i++)
                                            <svg class="w-3.5 h-3.5 {{ $i <= $item->rating ? 'text-[#D4AF37]' : 'text-gray-600' }}" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                            </svg>
                                        @endfor
                                        @if($item->reviewer)
                                            <span class="text-gray-500 text-[10px] ml-1">by {{ $item->reviewer }}</span>
                                        @endif
                                    </div>
                                    @if($item->snippet)
                                        <p class="text-gray-400 text-xs leading-relaxed line-clamp-2">"{{ $item->snippet }}"</p>
                                    @endif
                                @else
                                    @if($item->rating)
                                        <div class="flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5 text-[#D4AF37]" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                            </svg>
                                            <span class="text-gray-400 text-xs">{{ number_format($item->rating, 1) }} on Google</span>
                                        </div>
                                    @endif
                                @endif
                            </div>
                        </a>
                    @endforeach
                </div>
            @else
                {{-- Empty state --}}
                <div class="text-center py-12">
                    <div class="w-16 h-16 rounded-full mx-auto mb-4 flex items-center justify-center" style="background-color: rgba(212,175,55,0.08);">
                        <svg class="w-8 h-8 text-[#D4AF37]/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <p class="text-gray-400 text-lg mb-2">Explore restaurants near you</p>
                    <a href="/restaurantes" class="inline-flex items-center gap-2 text-[#D4AF37] font-semibold hover:underline text-sm">
                        Browse all restaurants
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                </div>
            @endif
        </div>
    </section>

    @include("partials.usa.top-restaurants")
    @include("partials.usa.categories")
    @include("partials.usa.top-states")
    @include("partials.usa.about")

    {{-- Final CTA --}}
    <section class="py-16 md:py-20" style="background-color: #0B0B0B;">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-3xl md:text-4xl font-display font-black text-[#F5F5F5] mb-4">
                Ready to Find Your Next Favorite Spot?
            </h2>
            <p class="text-[#CCCCCC] text-lg mb-8 max-w-2xl mx-auto">
                Explore thousands of top-rated Mexican restaurants across all 50 states. Curated by real reviews from 5+ sources.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="/restaurantes" class="inline-flex items-center justify-center px-10 py-5 bg-[#D4AF37] text-[#0B0B0B] font-bold text-lg rounded-xl hover:bg-[#c9a432] transition-all shadow-lg shadow-[#D4AF37]/20">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    Explore Restaurants
                </a>
                <a href="/guia" class="inline-flex items-center justify-center px-10 py-5 border-2 border-[#D4AF37] text-[#D4AF37] font-bold text-lg rounded-xl hover:bg-[#D4AF37]/10 transition-all">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    Browse by City
                </a>
            </div>
        </div>
    </section>
@endif
</div>
