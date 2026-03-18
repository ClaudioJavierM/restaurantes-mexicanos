<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">⭐ Mis Reseñas</h1>
            <p class="mt-2 text-gray-600">Las opiniones que has compartido sobre restaurantes mexicanos</p>
        </div>

        @if($reviews->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                @foreach($reviews as $review)
                    @php
                        $statusMap = [
                            'approved' => ['label' => 'Aprobada', 'class' => 'bg-green-100 text-green-800'],
                            'pending'  => ['label' => 'Pendiente', 'class' => 'bg-yellow-100 text-yellow-800'],
                            'rejected' => ['label' => 'Rechazada', 'class' => 'bg-red-100 text-red-700'],
                        ];
                        $badge = $statusMap[$review->status] ?? ['label' => ucfirst($review->status), 'class' => 'bg-gray-100 text-gray-600'];
                    @endphp

                    <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition-shadow duration-300">
                        <!-- Restaurant image -->
                        <a href="{{ $review->restaurant ? route('restaurants.show', $review->restaurant->slug) : '#' }}" class="block">
                            @if($review->restaurant && $review->restaurant->image)
                                <img src="{{ Storage::url($review->restaurant->image) }}"
                                     alt="{{ $review->restaurant->name }}"
                                     class="w-full h-40 object-cover">
                            @else
                                <div class="w-full h-40 bg-gradient-to-br from-amber-400 to-orange-500 flex items-center justify-center">
                                    <svg class="w-16 h-16 text-white opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                                    </svg>
                                </div>
                            @endif
                        </a>

                        <div class="p-4">
                            <!-- Restaurant + status -->
                            <div class="flex items-start justify-between mb-3">
                                <a href="{{ $review->restaurant ? route('restaurants.show', $review->restaurant->slug) : '#' }}">
                                    <h3 class="text-base font-bold text-gray-900 hover:text-red-600 transition-colors leading-tight">
                                        {{ $review->restaurant->name ?? 'Restaurante' }}
                                    </h3>
                                </a>
                                <span class="ml-2 flex-shrink-0 text-xs font-semibold px-2 py-1 rounded-full {{ $badge['class'] }}">
                                    {{ $badge['label'] }}
                                </span>
                            </div>

                            <!-- Stars -->
                            <div class="flex items-center mb-2">
                                @for($i = 1; $i <= 5; $i++)
                                    <svg class="w-4 h-4 {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-200' }}"
                                         fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                @endfor
                                <span class="ml-2 text-sm text-gray-600">{{ number_format($review->rating, 1) }}/5</span>
                                @if($review->photos->count() > 0)
                                    <span class="ml-auto text-xs text-gray-400 flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                        {{ $review->photos->count() }}
                                    </span>
                                @endif
                            </div>

                            <!-- Title -->
                            @if($review->title)
                                <p class="text-sm font-semibold text-gray-800 mb-1">{{ $review->title }}</p>
                            @endif

                            <!-- Comment -->
                            <p class="text-sm text-gray-600 line-clamp-3 mb-3">{{ $review->comment }}</p>

                            <!-- Date -->
                            <p class="text-xs text-gray-400 mb-3">{{ $review->created_at->diffForHumans() }}</p>

                            <!-- Owner response -->
                            @if($review->owner_response)
                                <div class="bg-blue-50 border border-blue-100 rounded-lg p-3">
                                    <p class="text-xs font-semibold text-blue-700 mb-1">Respuesta del restaurante:</p>
                                    <p class="text-xs text-blue-600 line-clamp-3">{{ $review->owner_response }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-8">
                {{ $reviews->links() }}
            </div>

        @else
            <!-- Empty State -->
            <div class="text-center py-16">
                <svg class="w-24 h-24 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                </svg>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">No has escrito reseñas</h3>
                <p class="text-gray-600 mb-6">Comparte tu experiencia en un restaurante mexicano y ayuda a la comunidad.</p>
                <a href="/restaurantes" class="inline-flex items-center px-6 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors font-medium">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    Explorar Restaurantes
                </a>
            </div>
        @endif
    </div>
</div>
