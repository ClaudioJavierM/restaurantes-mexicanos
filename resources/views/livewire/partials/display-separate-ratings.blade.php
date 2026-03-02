<!-- Separate Ratings Display -->
@if($restaurant->avg_service_rating || $restaurant->avg_food_rating || $restaurant->avg_ambiance_rating)
<div class="mt-4 flex flex-wrap gap-4 text-sm">
    @if($restaurant->avg_service_rating)
    <div class="flex items-center gap-1 bg-blue-50 px-3 py-1.5 rounded-full">
        <span class="text-blue-600">🍽️</span>
        <span class="font-medium text-blue-900">{{ app()->getLocale() === 'en' ? 'Service' : 'Servicio' }}</span>
        <div class="flex text-blue-400 ml-1">
            @for($i = 1; $i <= 5; $i++)
                <svg class="w-3.5 h-3.5 {{ $i <= round($restaurant->avg_service_rating) ? 'fill-current' : 'text-blue-200' }}" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                </svg>
            @endfor
        </div>
        <span class="text-blue-700 font-semibold ml-0.5">{{ number_format($restaurant->avg_service_rating, 1) }}</span>
    </div>
    @endif

    @if($restaurant->avg_food_rating)
    <div class="flex items-center gap-1 bg-orange-50 px-3 py-1.5 rounded-full">
        <span class="text-orange-600">🌮</span>
        <span class="font-medium text-orange-900">{{ app()->getLocale() === 'en' ? 'Food' : 'Comida' }}</span>
        <div class="flex text-orange-400 ml-1">
            @for($i = 1; $i <= 5; $i++)
                <svg class="w-3.5 h-3.5 {{ $i <= round($restaurant->avg_food_rating) ? 'fill-current' : 'text-orange-200' }}" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                </svg>
            @endfor
        </div>
        <span class="text-orange-700 font-semibold ml-0.5">{{ number_format($restaurant->avg_food_rating, 1) }}</span>
    </div>
    @endif

    @if($restaurant->avg_ambiance_rating)
    <div class="flex items-center gap-1 bg-purple-50 px-3 py-1.5 rounded-full">
        <span class="text-purple-600">🎭</span>
        <span class="font-medium text-purple-900">{{ app()->getLocale() === 'en' ? 'Ambiance' : 'Ambiente' }}</span>
        <div class="flex text-purple-400 ml-1">
            @for($i = 1; $i <= 5; $i++)
                <svg class="w-3.5 h-3.5 {{ $i <= round($restaurant->avg_ambiance_rating) ? 'fill-current' : 'text-purple-200' }}" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                </svg>
            @endfor
        </div>
        <span class="text-purple-700 font-semibold ml-0.5">{{ number_format($restaurant->avg_ambiance_rating, 1) }}</span>
    </div>
    @endif
</div>
@endif
