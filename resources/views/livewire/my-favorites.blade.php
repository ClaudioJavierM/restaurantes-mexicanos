<div class="min-h-screen py-8" style="background:#0B0B0B;">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold" style="color:#F5F5F5; font-family:'Playfair Display',serif;">Mis Restaurantes Favoritos</h1>
            <p class="mt-2" style="color:#9CA3AF;">Los restaurantes que has guardado para visitar después</p>
        </div>

        <!-- Favorites Grid -->
        @if($favorites->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                @foreach($favorites as $restaurant)
                    <div class="rounded-xl overflow-hidden transition-all duration-300 hover:scale-[1.02]" style="background:#1A1A1A; border:1px solid #2A2A2A;">
                        <!-- Image -->
                        <a href="{{ route('restaurants.show', $restaurant->slug) }}" class="block relative">
                            @php $imgUrl = $restaurant->getDisplayImageUrl(); @endphp
                            @if($imgUrl)
                                <img src="{{ $imgUrl }}" alt="{{ $restaurant->name }}" class="w-full h-48 object-cover" loading="lazy">
                            @else
                                <div class="w-full h-48 flex items-center justify-center" style="background:#111;">
                                    <span style="font-size:3rem;">🍽️</span>
                                </div>
                            @endif
                            <!-- Remove button overlay -->
                            <button
                                wire:click.prevent="removeFavorite({{ $restaurant->id }})"
                                wire:confirm="¿Seguro que quieres quitar este restaurante de favoritos?"
                                class="absolute top-3 right-3 w-9 h-9 rounded-full flex items-center justify-center transition-all"
                                style="background:rgba(0,0,0,0.6); backdrop-filter:blur(4px);"
                                title="Quitar de favoritos"
                            >
                                <svg class="w-5 h-5" fill="#D4AF37" stroke="#D4AF37" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                </svg>
                            </button>
                        </a>

                        <!-- Content -->
                        <div class="p-4">
                            <a href="{{ route('restaurants.show', $restaurant->slug) }}">
                                <h3 class="text-lg font-bold mb-1 hover:underline" style="color:#F5F5F5;">
                                    {{ $restaurant->name }}
                                </h3>
                            </a>

                            <!-- Location -->
                            <p class="text-sm mb-2" style="color:#9CA3AF;">
                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                {{ $restaurant->city }}{{ $restaurant->state ? ', ' . $restaurant->state->code : '' }}
                            </p>

                            <!-- Rating -->
                            @php $favWeightedRating = $restaurant->getWeightedRating(); @endphp
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center">
                                    <span class="font-semibold" style="color:#D4AF37;">{{ number_format($favWeightedRating, 1) }}</span>
                                    <span class="ml-1" style="color:#D4AF37;">⭐</span>
                                    <span class="ml-2 text-xs" style="color:#6B7280;">({{ $restaurant->getCombinedReviewCount() }} reseñas)</span>
                                </div>
                                @if($restaurant->price_range)
                                    <span class="text-sm font-medium" style="color:#22c55e;">{{ $restaurant->price_range }}</span>
                                @endif
                            </div>

                            <!-- Description -->
                            @if($restaurant->description)
                                <p class="text-sm line-clamp-2 mb-3" style="color:#9CA3AF;">
                                    {{ $restaurant->description }}
                                </p>
                            @endif

                            <!-- View Button -->
                            <a href="{{ route('restaurants.show', $restaurant->slug) }}"
                               class="block w-full text-center py-2.5 rounded-lg font-semibold transition-colors"
                               style="background:#D4AF37; color:#0B0B0B;"
                               onmouseover="this.style.background='#E8C67A'" onmouseout="this.style.background='#D4AF37'">
                                Ver Restaurante
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-8">
                {{ $favorites->links() }}
            </div>
        @else
            <!-- Empty State -->
            <div class="text-center py-16">
                <div class="w-20 h-20 rounded-full mx-auto mb-6 flex items-center justify-center" style="background:rgba(212,175,55,0.1);">
                    <svg class="w-10 h-10" style="color:#D4AF37;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-bold mb-2" style="color:#F5F5F5;">Aún no tienes favoritos</h3>
                <p class="mb-6" style="color:#9CA3AF;">Explora y guarda tus restaurantes mexicanos favoritos</p>
                <a href="{{ route('restaurants.index') }}"
                   class="inline-flex items-center px-6 py-3 rounded-lg font-semibold transition-colors"
                   style="background:#D4AF37; color:#0B0B0B;"
                   onmouseover="this.style.background='#E8C67A'" onmouseout="this.style.background='#D4AF37'">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    Explorar Restaurantes
                </a>
            </div>
        @endif
    </div>
</div>
