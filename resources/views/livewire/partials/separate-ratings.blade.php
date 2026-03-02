<!-- Separate Ratings (Optional) -->
<div class="bg-gradient-to-r from-gray-50 to-gray-100 rounded-lg p-4">
    <p class="text-sm font-semibold text-gray-700 mb-4 flex items-center gap-2">
        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        {{ app()->getLocale() === 'en' ? 'Rate specific aspects (Optional)' : 'Califica aspectos específicos (Opcional)' }}
    </p>
    <div class="grid md:grid-cols-3 gap-4">
        <!-- Service Rating -->
        <div class="bg-white rounded-lg p-3 shadow-sm">
            <label class="block text-xs font-medium text-gray-600 mb-2">
                🍽️ {{ app()->getLocale() === 'en' ? 'Service' : 'Servicio' }}
            </label>
            <div class="flex gap-1">
                @for($i = 1; $i <= 5; $i++)
                    <button type="button" wire:click="setServiceRating({{ $i }})" class="focus:outline-none transition-transform hover:scale-110">
                        <svg class="w-6 h-6 {{ $serviceRating >= $i ? 'text-blue-500' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                    </button>
                @endfor
            </div>
        </div>

        <!-- Food Rating -->
        <div class="bg-white rounded-lg p-3 shadow-sm">
            <label class="block text-xs font-medium text-gray-600 mb-2">
                🌮 {{ app()->getLocale() === 'en' ? 'Food' : 'Comida' }}
            </label>
            <div class="flex gap-1">
                @for($i = 1; $i <= 5; $i++)
                    <button type="button" wire:click="setFoodRating({{ $i }})" class="focus:outline-none transition-transform hover:scale-110">
                        <svg class="w-6 h-6 {{ $foodRating >= $i ? 'text-orange-500' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                    </button>
                @endfor
            </div>
        </div>

        <!-- Ambiance Rating -->
        <div class="bg-white rounded-lg p-3 shadow-sm">
            <label class="block text-xs font-medium text-gray-600 mb-2">
                🎭 {{ app()->getLocale() === 'en' ? 'Ambiance' : 'Ambiente' }}
            </label>
            <div class="flex gap-1">
                @for($i = 1; $i <= 5; $i++)
                    <button type="button" wire:click="setAmbianceRating({{ $i }})" class="focus:outline-none transition-transform hover:scale-110">
                        <svg class="w-6 h-6 {{ $ambianceRating >= $i ? 'text-purple-500' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                    </button>
                @endfor
            </div>
        </div>
    </div>
</div>
