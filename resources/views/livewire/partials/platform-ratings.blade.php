{{-- Platform Ratings Summary --}}
@php
    $platforms = collect([
        [
            'name' => 'Google',
            'rating' => $restaurant->google_rating,
            'count' => $restaurant->google_reviews_count,
            'icon' => '/images/icons/google.svg',
            'color' => 'bg-blue-50 border-blue-200',
            'textColor' => 'text-blue-700',
            'url' => $restaurant->google_maps_url,
        ],
        [
            'name' => 'Yelp',
            'rating' => $restaurant->yelp_rating,
            'count' => $restaurant->yelp_reviews_count,
            'icon' => '/images/icons/yelp.svg',
            'color' => 'bg-red-50 border-red-200',
            'textColor' => 'text-red-700',
            'url' => $restaurant->yelp_url,
        ],
        [
            'name' => 'TripAdvisor',
            'rating' => $restaurant->tripadvisor_rating,
            'count' => $restaurant->tripadvisor_reviews_count,
            'icon' => '/images/icons/tripadvisor.svg',
            'color' => 'bg-green-50 border-green-200',
            'textColor' => 'text-green-700',
            'url' => $restaurant->tripadvisor_url,
        ],
        [
            'name' => 'Foursquare',
            'rating' => $restaurant->foursquare_rating ? $restaurant->foursquare_rating / 2 : null,
            'count' => $restaurant->foursquare_tips_count,
            'icon' => '/images/icons/foursquare.svg',
            'color' => 'bg-purple-50 border-purple-200',
            'textColor' => 'text-purple-700',
            'url' => null,
        ],
    ])->filter(fn($p) => $p['rating'] > 0);
@endphp

@if($platforms->count() > 0)
<div class="bg-white rounded-lg shadow p-6 mt-6">
    <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
        <svg class="w-5 h-5 text-amber-500" fill="currentColor" viewBox="0 0 20 20">
            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
        </svg>
        Calificaciones en Otras Plataformas
    </h3>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        @foreach($platforms as $platform)
        <div class="{{ $platform['color'] }} border rounded-xl p-4 text-center hover:shadow-md transition-shadow">
            <div class="text-2xl mb-2">
                @if($platform['name'] === 'Google')
                    <span class="text-blue-500">G</span>
                @elseif($platform['name'] === 'Yelp')
                    <span class="text-red-500 font-bold">Y</span>
                @elseif($platform['name'] === 'TripAdvisor')
                    <span class="text-green-500">🦉</span>
                @else
                    <span class="text-purple-500">📍</span>
                @endif
            </div>
            <div class="font-semibold {{ $platform['textColor'] }}">{{ $platform['name'] }}</div>
            <div class="flex items-center justify-center gap-1 mt-2">
                <span class="text-xl font-bold text-gray-900">{{ number_format($platform['rating'], 1) }}</span>
                <svg class="w-5 h-5 text-yellow-400 fill-current" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                </svg>
            </div>
            <div class="text-xs text-gray-500 mt-1">{{ number_format($platform['count']) }} reseñas</div>
            @if($platform['url'])
            <a href="{{ $platform['url'] }}" target="_blank" class="text-xs {{ $platform['textColor'] }} hover:underline mt-2 inline-block">
                Ver en {{ $platform['name'] }} →
            </a>
            @endif
        </div>
        @endforeach
    </div>
</div>
@endif
