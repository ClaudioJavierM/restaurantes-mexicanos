{{-- Rating Distribution Section - Yelp Style --}}
@php
    $totalReviews = ($restaurant->google_reviews_count ?? 0) + ($restaurant->yelp_reviews_count ?? 0) + ($restaurant->tripadvisor_reviews_count ?? 0);
    $avgRating = $restaurant->google_rating ?? $restaurant->yelp_rating ?? $restaurant->average_rating ?? 0;
    
    // Estimate distribution based on average rating (simplified model)
    // In a real scenario, you'd have actual counts from API or reviews
    if ($avgRating >= 4.5) {
        $distribution = [5 => 70, 4 => 20, 3 => 7, 2 => 2, 1 => 1];
    } elseif ($avgRating >= 4.0) {
        $distribution = [5 => 50, 4 => 30, 3 => 12, 2 => 5, 1 => 3];
    } elseif ($avgRating >= 3.5) {
        $distribution = [5 => 30, 4 => 35, 3 => 20, 2 => 10, 1 => 5];
    } elseif ($avgRating >= 3.0) {
        $distribution = [5 => 20, 4 => 25, 3 => 30, 2 => 15, 1 => 10];
    } else {
        $distribution = [5 => 10, 4 => 15, 3 => 25, 2 => 25, 1 => 25];
    }
    
    $maxPercent = max($distribution);
@endphp

@if($totalReviews > 0)
<div class="bg-white rounded-lg shadow p-6">
    <h2 class="text-2xl font-bold text-gray-900 mb-6">Calificacion General</h2>
    
    <div class="flex flex-col md:flex-row gap-8">
        {{-- Overall Rating --}}
        <div class="text-center md:w-1/3">
            <div class="text-6xl font-bold text-gray-900">{{ number_format($avgRating, 1) }}</div>
            <div class="flex justify-center my-3">
                @for($i = 1; $i <= 5; $i++)
                    @if($i <= floor($avgRating))
                        <svg class="w-6 h-6 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                    @elseif($i - $avgRating < 1)
                        <svg class="w-6 h-6 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                            <defs>
                                <linearGradient id="half-star-{{ $restaurant->id }}">
                                    <stop offset="50%" stop-color="currentColor"/>
                                    <stop offset="50%" stop-color="#D1D5DB"/>
                                </linearGradient>
                            </defs>
                            <path fill="url(#half-star-{{ $restaurant->id }})" d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                    @else
                        <svg class="w-6 h-6 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                    @endif
                @endfor
            </div>
            <p class="text-gray-500">{{ number_format($totalReviews) }} resenas</p>
            
            {{-- Platform breakdown --}}
            <div class="mt-4 space-y-2">
                @if($restaurant->google_rating)
                <div class="flex items-center justify-center text-sm">
                    <img src="https://www.google.com/favicon.ico" alt="Google" class="w-4 h-4 mr-2">
                    <span class="text-gray-700 font-medium">{{ number_format($restaurant->google_rating, 1) }}</span>
                    <span class="text-gray-400 ml-1">({{ number_format($restaurant->google_reviews_count ?? 0) }})</span>
                </div>
                @endif
                @if($restaurant->yelp_rating)
                <div class="flex items-center justify-center text-sm">
                    <span class="w-4 h-4 bg-red-500 text-white text-xs font-bold rounded flex items-center justify-center mr-2">Y</span>
                    <span class="text-gray-700 font-medium">{{ number_format($restaurant->yelp_rating, 1) }}</span>
                    <span class="text-gray-400 ml-1">({{ number_format($restaurant->yelp_reviews_count ?? 0) }})</span>
                </div>
                @endif
            </div>
        </div>
        
        {{-- Distribution Bars --}}
        <div class="flex-1 space-y-2">
            @foreach([5, 4, 3, 2, 1] as $stars)
                @php
                    $percent = $distribution[$stars];
                    $width = ($percent / $maxPercent) * 100;
                @endphp
                <div class="flex items-center">
                    <span class="w-8 text-sm text-gray-600 font-medium">{{ $stars }}</span>
                    <svg class="w-4 h-4 text-yellow-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                    </svg>
                    <div class="flex-1 bg-gray-200 rounded-full h-3 overflow-hidden">
                        <div class="h-full rounded-full transition-all duration-500
                            {{ $stars === 5 ? 'bg-green-500' : '' }}
                            {{ $stars === 4 ? 'bg-green-400' : '' }}
                            {{ $stars === 3 ? 'bg-yellow-400' : '' }}
                            {{ $stars === 2 ? 'bg-orange-400' : '' }}
                            {{ $stars === 1 ? 'bg-red-400' : '' }}"
                            style="width: {{ $width }}%">
                        </div>
                    </div>
                    <span class="w-12 text-right text-sm text-gray-500">{{ $percent }}%</span>
                </div>
            @endforeach
        </div>
    </div>
    
    {{-- Write Review CTA --}}
    <div class="mt-6 pt-6 border-t border-gray-200 text-center">
        <button wire:click="switchTab('reviews')" 
                class="inline-flex items-center px-6 py-3 bg-red-600 hover:bg-red-700 text-white font-bold rounded-xl transition-colors">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
            </svg>
            Escribir una Resena
        </button>
    </div>
</div>
@endif
