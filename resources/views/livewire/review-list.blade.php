<div>
    <!-- Filters and Sorting -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <!-- Sort By -->
            <div class="flex items-center gap-3">
                <span class="text-sm font-semibold text-gray-700">
                    {{ app()->getLocale() === 'en' ? 'Sort by:' : 'Ordenar por:' }}
                </span>
                <div class="flex gap-2">
                    <button wire:click="setSortBy('recent')"
                            class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ $sortBy === 'recent' ? 'bg-red-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                        {{ app()->getLocale() === 'en' ? 'Most Recent' : 'Más Recientes' }}
                    </button>
                    <button wire:click="setSortBy('helpful')"
                            class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ $sortBy === 'helpful' ? 'bg-red-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                        {{ app()->getLocale() === 'en' ? 'Most Helpful' : 'Más Útiles' }}
                    </button>
                    <button wire:click="setSortBy('rating_high')"
                            class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ $sortBy === 'rating_high' ? 'bg-red-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                        {{ app()->getLocale() === 'en' ? 'Highest Rated' : 'Mejor Calificadas' }}
                    </button>
                    <button wire:click="setSortBy('rating_low')"
                            class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ $sortBy === 'rating_low' ? 'bg-red-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                        {{ app()->getLocale() === 'en' ? 'Lowest Rated' : 'Peor Calificadas' }}
                    </button>
                </div>
            </div>

            <!-- Filter by Rating -->
            <div class="flex items-center gap-3">
                <span class="text-sm font-semibold text-gray-700">
                    {{ app()->getLocale() === 'en' ? 'Filter:' : 'Filtrar:' }}
                </span>
                <div class="flex gap-2">
                    @for($i = 5; $i >= 1; $i--)
                        <button wire:click="setFilterRating({{ $i }})"
                                class="px-3 py-2 rounded-lg text-sm font-medium transition-colors flex items-center gap-1 {{ $filterRating === $i ? 'bg-red-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                            {{ $i }}
                            <svg class="w-4 h-4 text-yellow-400 {{ $filterRating === $i ? 'text-white' : '' }}" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                            @if(isset($ratingDistribution[$i]))
                                <span class="text-xs">({{ $ratingDistribution[$i] }})</span>
                            @endif
                        </button>
                    @endfor
                </div>
            </div>
        </div>
    </div>

    <!-- Flash Messages -->
    @if(session()->has('vote-success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-4">
            {{ session('vote-success') }}
        </div>
    @endif

    @if(session()->has('vote-error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-4">
            {{ session('vote-error') }}
        </div>
    @endif

    <!-- Reviews List -->
    <div class="space-y-6">
        @forelse($reviews as $review)
            <div class="bg-white rounded-lg shadow p-6">
                <!-- Review Header -->
                <div class="flex items-start justify-between mb-4">
                    <div class="flex items-center gap-3">
                        <!-- User Avatar -->
                        <div class="w-12 h-12 bg-red-600 rounded-full flex items-center justify-center text-white font-bold text-lg">
                            {{ strtoupper(substr($review->reviewer_name, 0, 1)) }}
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-900">{{ $review->reviewer_name }}</h4>
                            <div class="flex items-center gap-2 text-sm text-gray-600">
                                <span>{{ $review->created_at->diffForHumans() }}</span>
                                @if($review->visit_date)
                                    <span>•</span>
                                    <span>{{ app()->getLocale() === 'en' ? 'Visited' : 'Visitó' }}: {{ $review->visit_date->format('M Y') }}</span>
                                @endif
                                @if($review->visit_type)
                                    <span>•</span>
                                    <span>
                                        @if($review->visit_type === 'dine_in')
                                            {{ app()->getLocale() === 'en' ? 'Dine In' : 'En el Restaurante' }}
                                        @elseif($review->visit_type === 'takeout')
                                            {{ app()->getLocale() === 'en' ? 'Takeout' : 'Para Llevar' }}
                                        @else
                                            {{ app()->getLocale() === 'en' ? 'Delivery' : 'Entrega a Domicilio' }}
                                        @endif
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Rating Stars -->
                    <div class="flex gap-1">
                        @for($i = 1; $i <= 5; $i++)
                            <svg class="w-5 h-5 {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                        @endfor
                    </div>
                </div>

                <!-- Review Title -->
                @if($review->title)
                    <h3 class="text-lg font-bold text-gray-900 mb-2">{{ $review->title }}</h3>
                @endif

                <!-- Review Content -->
                <p class="text-gray-700 mb-4 leading-relaxed">{{ $review->comment }}</p>

                <!-- Review Photos -->
                @if($review->photos->count() > 0)
                    <div class="flex gap-2 mb-4 flex-wrap">
                        @foreach($review->photos as $photo)
                            <img src="{{ asset('storage/' . $photo->photo_path) }}"
                                 alt="Review photo"
                                 class="w-32 h-32 object-cover rounded-lg hover:scale-105 transition-transform cursor-pointer">
                        @endforeach
                    </div>
                @endif

                <!-- Owner Response -->
                @if($review->owner_response)
                    <div class="bg-gray-50 rounded-lg p-4 mb-4 border-l-4 border-red-600">
                        <div class="flex items-center gap-2 mb-2">
                            <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                            </svg>
                            <span class="font-semibold text-gray-900">
                                {{ app()->getLocale() === 'en' ? 'Response from the owner' : 'Respuesta del dueño' }}
                            </span>
                            @if($review->owner_response_at)
                                <span class="text-sm text-gray-600">• {{ $review->owner_response_at->diffForHumans() }}</span>
                            @endif
                        </div>
                        <p class="text-gray-700">{{ $review->owner_response }}</p>
                    </div>
                @endif

                <!-- Helpful Buttons -->
                <div class="flex items-center gap-4 pt-4 border-t">
                    <span class="text-sm text-gray-600">
                        {{ app()->getLocale() === 'en' ? 'Was this review helpful?' : '¿Te fue útil esta reseña?' }}
                    </span>
                    <div class="flex gap-2">
                        <button wire:click="voteHelpful({{ $review->id }})"
                                class="flex items-center gap-1 px-3 py-1.5 rounded-lg border {{ $review->getUserVote()?->is_helpful === true ? 'border-green-600 bg-green-50 text-green-700' : 'border-gray-300 hover:bg-gray-50' }} transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5"/>
                            </svg>
                            <span class="text-sm font-medium">
                                {{ app()->getLocale() === 'en' ? 'Helpful' : 'Útil' }}
                                ({{ $review->helpful_count }})
                            </span>
                        </button>
                        <button wire:click="voteNotHelpful({{ $review->id }})"
                                class="flex items-center gap-1 px-3 py-1.5 rounded-lg border {{ $review->getUserVote()?->is_helpful === false ? 'border-red-600 bg-red-50 text-red-700' : 'border-gray-300 hover:bg-gray-50' }} transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14H5.236a2 2 0 01-1.789-2.894l3.5-7A2 2 0 018.736 3h4.018a2 2 0 01.485.06l3.76.94m-7 10v5a2 2 0 002 2h.096c.5 0 .905-.405.905-.904 0-.715.211-1.413.608-2.008L17 13V4m-7 10h2m5-10h2a2 2 0 012 2v6a2 2 0 01-2 2h-2.5"/>
                            </svg>
                            <span class="text-sm font-medium">
                                {{ app()->getLocale() === 'en' ? 'Not Helpful' : 'No Útil' }}
                                ({{ $review->not_helpful_count }})
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-lg shadow p-12 text-center">
                <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                </svg>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">
                    {{ app()->getLocale() === 'en' ? 'No reviews yet' : 'Aún no hay reseñas' }}
                </h3>
                <p class="text-gray-600">
                    {{ app()->getLocale() === 'en' ? 'Be the first to write a review!' : '¡Sé el primero en escribir una reseña!' }}
                </p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($reviews->hasPages())
        <div class="mt-8">
            {{ $reviews->links() }}
        </div>
    @endif
</div>
