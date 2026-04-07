{{-- Top Restaurants Section --}}
<section class="py-20 lg:py-28" style="background-color: #0B0B0B;">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <span class="inline-block px-5 py-1.5 bg-[#D4AF37]/10 text-[#D4AF37] rounded-full text-xs font-semibold tracking-[0.2em] uppercase border border-[#D4AF37]/20 mb-6">
                Top Restaurants
            </span>
            <h2 class="text-3xl md:text-5xl font-display font-bold text-white mb-5 tracking-tight">
                @if($userLocation && isset($userLocation['city']))
                    Top Mexican Restaurants in {{ $userLocation['city'] }}, {{ $userLocation['state_code'] ?? '' }}
                @else
                    {{ __('app.featured_restaurants_title') }}
                @endif
            </h2>
            <p class="text-gray-400 max-w-2xl mx-auto text-lg leading-relaxed">
                @if($userLocation)
                    Restaurantes mexicanos mejor calificados cerca de ti
                @else
                    {{ __('app.top_restaurants_subtitle') }}
                @endif
            </p>

            {{-- Location indicator --}}
            @if($this->locationSource)
            <div class="mt-6 inline-flex items-center px-5 py-2.5 bg-[#1A1A1A] rounded-full text-sm border border-white/5">
                @if($this->locationSource === 'ip')
                    <svg class="w-4 h-4 mr-2 text-[#D4AF37]/60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/>
                    </svg>
                    <span class="text-gray-500">Ubicacion detectada: </span>
                @elseif($this->locationSource === 'browser')
                    <svg class="w-4 h-4 mr-2 text-[#D4AF37]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <span class="text-gray-500">Tu ubicacion: </span>
                @endif
                <span class="text-white font-medium ml-1">{{ $this->locationQuery }}</span>
                <button wire:click="clearLocation" class="ml-3 text-gray-600 hover:text-red-400 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            @else
            <div class="mt-6">
                <button wire:click="useCurrentLocation"
                        class="inline-flex items-center px-5 py-2.5 bg-[#1A1A1A] hover:bg-[#2A2A2A] text-white rounded-full text-sm font-medium transition-all border border-white/10 hover:border-[#D4AF37]/30">
                    <svg class="w-4 h-4 mr-2 text-[#D4AF37]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    Usar mi ubicacion
                </button>
            </div>
            @endif
        </div>

        {{-- Featured Restaurants --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-16">
            @foreach($featuredRestaurants->take(6) as $index => $restaurant)
                <div class="group relative bg-[#1A1A1A] rounded-2xl overflow-hidden border border-white/5 hover:border-[#D4AF37]/20 transition-all duration-500 hover:-translate-y-1">
                    {{-- Rank Badge --}}
                    <div class="absolute top-4 left-4 z-10">
                        <span class="inline-flex items-center justify-center w-10 h-10 bg-[#D4AF37] text-[#0B0B0B] font-bold rounded-full text-sm shadow-lg shadow-[#D4AF37]/20">
                            #{{ $index + 1 }}
                        </span>
                    </div>

                    {{-- Distance Badge if available --}}
                    @if(isset($restaurant->distance) && $restaurant->distance)
                    <div class="absolute top-4 right-4 z-10">
                        <span class="inline-flex items-center px-2.5 py-1 bg-[#0B0B0B]/80 backdrop-blur-sm text-white text-xs font-medium rounded-full border border-white/10">
                            {{ number_format($restaurant->distance, 1) }} mi
                        </span>
                    </div>
                    @endif

                    {{-- Image with multiple fallbacks --}}
                    <div class="relative h-52 overflow-hidden">
                        @php
                            // Determine best image source with fallbacks
                            $imgSrc = null;
                            $isExternal = false;

                            // Priority 1: image field
                            if ($restaurant->image) {
                                $isExternal = str_starts_with($restaurant->image, 'http');
                                $imgSrc = $isExternal ? $restaurant->image : asset('storage/' . $restaurant->image);
                            }
                            // Priority 2: Media library
                            elseif ($restaurant->getFirstMediaUrl('images')) {
                                $imgSrc = $restaurant->getFirstMediaUrl('images');
                                $isExternal = true;
                            }
                            // Priority 3: Yelp photos
                            elseif (is_array($restaurant->yelp_photos) && count($restaurant->yelp_photos) > 0) {
                                $imgSrc = $restaurant->yelp_photos[0];
                                $isExternal = true;
                            }
                        @endphp

                        @if($imgSrc)
                            <img src="{{ $imgSrc }}"
                                 alt="{{ $restaurant->name }}"
                                 width="400"
                                 height="208"
                                 loading="{{ $index < 3 ? 'eager' : 'lazy' }}"
                                 @if($index === 0) fetchpriority="high" @endif
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700"
                                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                            <div class="hidden w-full h-full bg-[#2A2A2A] items-center justify-center">
                                <svg class="w-12 h-12 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                        @else
                            <div class="w-full h-full bg-[#2A2A2A] flex items-center justify-center">
                                <svg class="w-12 h-12 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                        @endif
                        <div class="absolute inset-0 bg-gradient-to-t from-[#1A1A1A] via-transparent to-transparent"></div>
                    </div>

                    {{-- Content --}}
                    <div class="p-6">
                        <h3 class="text-white font-semibold text-lg mb-1.5 group-hover:text-[#D4AF37] transition-colors duration-300">
                            {{ $restaurant->name }}
                        </h3>
                        <p class="text-gray-400 text-sm mb-4">
                            {{ $restaurant->city }}, {{ $restaurant->state->code ?? '' }}
                        </p>

                        {{-- Rating with platform breakdown --}}
                        <div class="flex items-center justify-between mb-5">
                            <div class="flex items-center">
                                <div class="flex text-[#D4AF37]">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= floor($restaurant->google_rating ?? 0))
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                            </svg>
                                        @else
                                            <svg class="w-4 h-4 text-[#2A2A2A]" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                            </svg>
                                        @endif
                                    @endfor
                                </div>
                                <span class="ml-2 text-white font-semibold text-sm">{{ number_format($restaurant->google_rating ?? 0, 1) }}</span>
                            </div>
                            <span class="text-gray-600 text-xs">
                                {{ number_format(($restaurant->google_reviews_count ?? 0) + ($restaurant->yelp_reviews_count ?? 0)) }} resenas
                            </span>
                        </div>

                        {{-- View Button --}}
                        <a href="{{ route('restaurants.show', $restaurant->slug) }}"
                           class="block w-full text-center py-3 border border-[#D4AF37]/30 text-[#D4AF37] font-medium rounded-xl hover:bg-[#D4AF37]/10 transition-all duration-300 text-sm">
                            Ver Restaurante
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="text-center">
            <a href="/restaurantes?sort=rating{{ $this->userLat && $this->userLng ? '&lat=' . $this->userLat . '&lng=' . $this->userLng : '' }}"
               class="inline-flex items-center px-8 py-4 bg-[#D4AF37] text-[#0B0B0B] font-semibold text-sm rounded-xl hover:bg-[#D4AF37]/90 transition-all duration-300 tracking-wide">
                {{ __('app.view_all_restaurants') }}
                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                </svg>
            </a>
        </div>
    </div>
</section>
