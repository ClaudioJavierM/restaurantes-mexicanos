{{-- Top Restaurants Section --}}
<section class="py-16 bg-gradient-to-b from-gray-900 to-gray-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <span class="inline-block px-4 py-2 bg-yellow-500 text-gray-900 rounded-full text-sm font-black mb-4">
                TOP RESTAURANTES
            </span>
            <h2 class="text-3xl md:text-4xl font-display font-black text-white mb-4">
                @if($userLocation && isset($userLocation['city']))
                    Los Mejores en {{ $userLocation['city'] }}, {{ $userLocation['state_code'] ?? '' }}
                @else
                    {{ __('app.featured_restaurants_title') }}
                @endif
            </h2>
            <p class="text-gray-400 max-w-2xl mx-auto">
                @if($userLocation)
                    Restaurantes mexicanos mejor calificados cerca de ti
                @else
                    {{ __('app.top_restaurants_subtitle') }}
                @endif
            </p>

            {{-- Location indicator --}}
            @if($this->locationSource)
            <div class="mt-4 inline-flex items-center px-4 py-2 bg-gray-800 rounded-full text-sm">
                @if($this->locationSource === 'ip')
                    <svg class="w-4 h-4 mr-2 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/>
                    </svg>
                    <span class="text-gray-400">Ubicacion detectada: </span>
                @elseif($this->locationSource === 'browser')
                    <svg class="w-4 h-4 mr-2 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <span class="text-gray-400">Tu ubicacion: </span>
                @endif
                <span class="text-white font-medium ml-1">{{ $this->locationQuery }}</span>
                <button wire:click="clearLocation" class="ml-3 text-gray-500 hover:text-red-400 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            @else
            <div class="mt-4">
                <button wire:click="useCurrentLocation"
                        class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-full text-sm font-medium transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    Usar mi ubicacion
                </button>
            </div>
            @endif
        </div>

        {{-- Featured Restaurants --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-12">
            @foreach($featuredRestaurants->take(6) as $index => $restaurant)
                <div class="group relative bg-gray-800 rounded-xl overflow-hidden shadow-xl hover:shadow-2xl transition-all transform hover:-translate-y-1 border border-gray-700">
                    {{-- Rank Badge --}}
                    <div class="absolute top-4 left-4 z-10">
                        <span class="inline-flex items-center justify-center w-10 h-10 bg-yellow-500 text-gray-900 font-black rounded-full text-lg shadow-lg">
                            #{{ $index + 1 }}
                        </span>
                    </div>

                    {{-- Distance Badge if available --}}
                    @if(isset($restaurant->distance) && $restaurant->distance)
                    <div class="absolute top-4 right-4 z-10">
                        <span class="inline-flex items-center px-2 py-1 bg-blue-600 text-white text-xs font-bold rounded-full">
                            {{ number_format($restaurant->distance, 1) }} mi
                        </span>
                    </div>
                    @endif

                    {{-- Image with multiple fallbacks --}}
                    <div class="relative h-48 overflow-hidden">
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
                                 height="192"
                                 loading="{{ $index < 3 ? 'eager' : 'lazy' }}"
                                 @if($index === 0) fetchpriority="high" @endif
                                 class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
                                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                            <div class="hidden w-full h-full bg-gradient-to-br from-green-600 to-red-600 items-center justify-center">
                                <span class="text-6xl">🍽️</span>
                            </div>
                        @else
                            <div class="w-full h-full bg-gradient-to-br from-green-600 to-red-600 flex items-center justify-center">
                                <span class="text-6xl">🍽️</span>
                            </div>
                        @endif
                        <div class="absolute inset-0 bg-gradient-to-t from-gray-900/80 to-transparent"></div>
                    </div>

                    {{-- Content --}}
                    <div class="p-5">
                        <h3 class="text-white font-bold text-xl mb-2 group-hover:text-yellow-400 transition-colors">
                            {{ $restaurant->name }}
                        </h3>
                        <p class="text-gray-400 text-sm mb-3">
                            {{ $restaurant->city }}, {{ $restaurant->state->code ?? '' }}
                        </p>

                        {{-- Rating with platform breakdown --}}
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="flex text-yellow-400">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= floor($restaurant->google_rating ?? 0))
                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                            </svg>
                                        @else
                                            <svg class="w-5 h-5 text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                            </svg>
                                        @endif
                                    @endfor
                                </div>
                                <span class="ml-2 text-white font-bold">{{ number_format($restaurant->google_rating ?? 0, 1) }}</span>
                            </div>
                            <span class="text-gray-500 text-sm">
                                {{ number_format(($restaurant->google_reviews_count ?? 0) + ($restaurant->yelp_reviews_count ?? 0)) }} resenas
                            </span>
                        </div>

                        {{-- View Button --}}
                        <a href="{{ route('restaurants.show', $restaurant->slug) }}"
                           class="mt-4 block w-full text-center py-3 bg-gradient-to-r from-green-600 to-red-600 text-white font-bold rounded-lg hover:from-green-500 hover:to-red-500 transition-all">
                            Ver Restaurante
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="text-center">
            <a href="/restaurantes?sort=rating{{ $this->userLat && $this->userLng ? '&lat=' . $this->userLat . '&lng=' . $this->userLng : '' }}"
               class="inline-flex items-center px-8 py-4 bg-yellow-500 text-gray-900 font-black text-lg rounded-xl hover:bg-yellow-400 transition-all shadow-lg">
                {{ __('app.view_all_restaurants') }}
                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                </svg>
            </a>
        </div>
    </div>
</section>
