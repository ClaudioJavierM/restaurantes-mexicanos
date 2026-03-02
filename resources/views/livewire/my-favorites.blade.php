<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">❤️ {{ __('My Favorite Restaurants') }}</h1>
            <p class="mt-2 text-gray-600">{{ __('Restaurants you have saved for later') }}</p>
        </div>

        <!-- Flash Messages -->
        @if (session()->has('success'))
            <div class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        <!-- Favorites Grid -->
        @if($favorites->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                @foreach($favorites as $restaurant)
                    <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition-shadow duration-300">
                        <!-- Image -->
                        <a href="{{ route('restaurants.show', $restaurant->slug) }}" class="block">
                            @if($restaurant->image)
                                <img src="{{ Storage::url($restaurant->image) }}" alt="{{ $restaurant->name }}" class="w-full h-48 object-cover">
                            @else
                                <div class="w-full h-48 bg-gradient-to-br from-red-400 to-orange-400 flex items-center justify-center">
                                    <svg class="w-20 h-20 text-white opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                    </svg>
                                </div>
                            @endif
                        </a>

                        <!-- Content -->
                        <div class="p-4">
                            <div class="flex items-start justify-between mb-2">
                                <a href="{{ route('restaurants.show', $restaurant->slug) }}" class="flex-1">
                                    <h3 class="text-lg font-bold text-gray-900 hover:text-red-600 transition-colors">
                                        {{ $restaurant->name }}
                                    </h3>
                                </a>
                                <button
                                    wire:click="removeFavorite({{ $restaurant->id }})"
                                    wire:confirm="Are you sure you want to remove this from favorites?"
                                    class="text-red-600 hover:text-red-800 transition-colors ml-2"
                                    title="{{ __('Remove from favorites') }}"
                                >
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M6 18L18 6M6 6l12 12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                    </svg>
                                </button>
                            </div>

                            <!-- Location -->
                            <p class="text-sm text-gray-600 mb-2">
                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                {{ $restaurant->city }}, {{ $restaurant->state->code }}
                            </p>

                            <!-- Rating & Reviews (weighted average) -->
                            @php $favWeightedRating = $restaurant->getWeightedRating(); @endphp
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center">
                                    <span class="text-yellow-500 font-semibold">{{ number_format($favWeightedRating, 1) }}</span>
                                    <span class="ml-1 text-yellow-400">⭐</span>
                                    <span class="ml-2 text-xs text-gray-500">({{ $restaurant->getCombinedReviewCount() }} {{ __('reviews') }})</span>
                                </div>
                                @if($restaurant->price_range)
                                    <span class="text-sm text-green-600 font-medium">{{ $restaurant->price_range }}</span>
                                @endif
                            </div>

                            <!-- Description -->
                            @if($restaurant->description)
                                <p class="text-sm text-gray-600 line-clamp-2 mb-3">
                                    {{ $restaurant->description }}
                                </p>
                            @endif

                            <!-- View Button -->
                            <a
                                href="{{ route('restaurants.show', $restaurant->slug) }}"
                                class="block w-full text-center bg-red-600 text-white py-2 rounded-lg hover:bg-red-700 transition-colors font-medium"
                            >
                                {{ __('View Restaurant') }}
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
            <div class="text-center py-12">
                <svg class="w-24 h-24 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                </svg>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">{{ __('No favorites yet') }}</h3>
                <p class="text-gray-600 mb-6">{{ __('Start exploring and save your favorite Mexican restaurants!') }}</p>
                <a
                    href="{{ route('restaurants.index') }}"
                    class="inline-flex items-center px-6 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors font-medium"
                >
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    {{ __('Explore Restaurants') }}
                </a>
            </div>
        @endif
    </div>
</div>
