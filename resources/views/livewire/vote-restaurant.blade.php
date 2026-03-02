<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-gradient-to-r from-amber-600 to-orange-600 text-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-4xl font-bold mb-4">Vota por tu Restaurante Favorito</h1>
            <p class="text-xl text-amber-100">FAMER Awards 2026 - Tu voto cuenta</p>
            <p class="text-amber-200 mt-2">{{ now()->format('F Y') }} - Votación del mes</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- State Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                    <select wire:model.live="stateCode" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500">
                        <option value="">Todos los estados</option>
                        @foreach($this->states as $state)
                            <option value="{{ $state->code }}">{{ $state->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <!-- City Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ciudad</label>
                    <select wire:model.live="city" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500" @if(!$stateCode) disabled @endif>
                        <option value="">Todas las ciudades</option>
                        @foreach($this->cities as $cityName)
                            <option value="{{ $cityName }}">{{ $cityName }}</option>
                        @endforeach
                    </select>
                </div>
                
                <!-- Search -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Buscar</label>
                    <input 
                        type="text" 
                        wire:model.live.debounce.300ms="search" 
                        placeholder="Nombre del restaurante..."
                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500"
                    >
                </div>
            </div>
        </div>
    </div>

    <!-- Error Message -->
    @if($voteError)
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-6">
        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
            <div class="flex items-center">
                <span class="text-red-500 mr-2">⚠️</span>
                <span class="text-red-700">{{ $voteError }}</span>
            </div>
        </div>
    </div>
    @endif

    <!-- Restaurants Grid -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($this->restaurants as $restaurant)
            <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow {{ $votedRestaurantId === $restaurant->id ? 'ring-2 ring-green-500' : '' }}">
                <!-- Restaurant Image -->
                <a href="{{ route('restaurants.show', $restaurant->slug) }}" class="block h-48 bg-gradient-to-br from-amber-100 to-orange-100 relative">
                    @if($restaurant->image)
                        <img src="{{ str_starts_with($restaurant->image, 'http') ? $restaurant->image : asset('storage/' . $restaurant->image) }}"
                             alt="{{ $restaurant->name }}"
                             class="w-full h-full object-cover"
                             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                        <div class="hidden items-center justify-center h-full absolute inset-0 bg-gradient-to-br from-amber-100 to-orange-100">
                            <span class="text-6xl">🌮</span>
                        </div>
                    @elseif($restaurant->hasMedia('images'))
                        <img src="{{ $restaurant->getFirstMediaUrl('images') }}"
                             alt="{{ $restaurant->name }}"
                             class="w-full h-full object-cover"
                             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                        <div class="hidden items-center justify-center h-full absolute inset-0 bg-gradient-to-br from-amber-100 to-orange-100">
                            <span class="text-6xl">🌮</span>
                        </div>
                    @elseif($restaurant->logo)
                        <img src="{{ str_starts_with($restaurant->logo, 'http') ? $restaurant->logo : asset('storage/' . $restaurant->logo) }}"
                             alt="{{ $restaurant->name }}"
                             class="w-full h-full object-contain p-4"
                             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                        <div class="hidden items-center justify-center h-full absolute inset-0 bg-gradient-to-br from-amber-100 to-orange-100">
                            <span class="text-6xl">🌮</span>
                        </div>
                    @else
                        <div class="flex items-center justify-center h-full">
                            <span class="text-6xl">🌮</span>
                        </div>
                    @endif
                    
                    <!-- Votes Badge -->
                    <div class="absolute top-3 right-3 bg-amber-500 text-white px-3 py-1 rounded-full text-sm font-bold">
                        {{ $restaurant->monthly_votes ?? 0 }} votos
                    </div>
                </a>
                
                <!-- Restaurant Info -->
                <div class="p-5">
                    <a href="{{ route('restaurants.show', $restaurant->slug) }}" class="hover:text-amber-600 transition-colors">
                        <h3 class="font-bold text-lg text-gray-900 mb-1 hover:text-amber-600">{{ $restaurant->name }}</h3>
                    </a>
                    <p class="text-gray-500 text-sm mb-3">
                        {{ $restaurant->city }}, {{ $restaurant->state?->code }}
                    </p>
                    
                    <!-- Ratings -->
                    <div class="flex items-center gap-4 mb-4 text-sm">
                        @if($restaurant->google_rating)
                        <div class="flex items-center">
                            <span class="text-yellow-500">⭐</span>
                            <span class="ml-1 font-medium">{{ number_format($restaurant->google_rating, 1) }}</span>
                            <span class="text-gray-400 ml-1">Google</span>
                        </div>
                        @endif
                        @if($restaurant->yelp_rating)
                        <div class="flex items-center">
                            <span class="text-red-500">🔴</span>
                            <span class="ml-1 font-medium">{{ number_format($restaurant->yelp_rating, 1) }}</span>
                            <span class="text-gray-400 ml-1">Yelp</span>
                        </div>
                        @endif
                    </div>
                    
                    <!-- Vote Button -->
                    @if($votedRestaurantId === $restaurant->id)
                        <button disabled class="w-full bg-green-500 text-white py-3 rounded-lg font-bold">
                            ✓ ¡Votaste por este!
                        </button>
                    @else
                        <button 
                            wire:click="vote({{ $restaurant->id }})"
                            wire:loading.attr="disabled"
                            class="w-full bg-gradient-to-r from-amber-500 to-orange-500 text-white py-3 rounded-lg font-bold hover:from-amber-600 hover:to-orange-600 transition-colors disabled:opacity-50">
                            <span wire:loading.remove wire:target="vote({{ $restaurant->id }})">🗳️ Votar</span>
                            <span wire:loading wire:target="vote({{ $restaurant->id }})">Votando...</span>
                        </button>
                    @endif
                </div>
            </div>
            @empty
            <div class="col-span-full text-center py-12">
                <span class="text-6xl">🔍</span>
                <p class="text-gray-500 mt-4">No se encontraron restaurantes con esos filtros</p>
            </div>
            @endforelse
        </div>
    </div>

    <!-- Thank You Modal -->
    @if($showThankYou)
    <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50" wire:click.self="closeThankYou">
        <div class="bg-white rounded-2xl shadow-2xl max-w-md mx-4 p-8 text-center transform animate-bounce-in">
            <div class="text-6xl mb-4">🎉</div>
            <h3 class="text-2xl font-bold text-gray-900 mb-2">¡Gracias por tu voto!</h3>
            <p class="text-gray-600 mb-6">Tu opinión es importante para seleccionar a los mejores restaurantes mexicanos.</p>
            
            <div class="space-y-3">
                <button wire:click="closeThankYou" class="w-full bg-amber-500 text-white py-3 rounded-lg font-bold hover:bg-amber-600">
                    Seguir Votando
                </button>
                <a href="{{ route('famer-awards-2026') }}" class="block w-full bg-gray-100 text-gray-700 py-3 rounded-lg font-bold hover:bg-gray-200">
                    Volver a FAMER Awards
                </a>
            </div>
            
            <!-- Share -->
            <div class="mt-6 pt-6 border-t">
                <p class="text-sm text-gray-500 mb-3">Comparte con tus amigos</p>
                <div class="flex justify-center gap-4">
                    <a href="https://twitter.com/intent/tweet?text=Acabo%20de%20votar%20por%20mi%20restaurante%20mexicano%20favorito%20en%20FAMER%20Awards%202026&url={{ urlencode(url()->current()) }}" target="_blank" class="w-10 h-10 bg-blue-400 text-white rounded-full flex items-center justify-center hover:bg-blue-500">
                        𝕏
                    </a>
                    <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}" target="_blank" class="w-10 h-10 bg-blue-600 text-white rounded-full flex items-center justify-center hover:bg-blue-700">
                        f
                    </a>
                    <a href="whatsapp://send?text=Vota%20por%20tu%20restaurante%20mexicano%20favorito%20{{ urlencode(url()->current()) }}" class="w-10 h-10 bg-green-500 text-white rounded-full flex items-center justify-center hover:bg-green-600">
                        📱
                    </a>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

<style>
@keyframes bounce-in {
    0% { transform: scale(0.8); opacity: 0; }
    50% { transform: scale(1.05); }
    100% { transform: scale(1); opacity: 1; }
}
.animate-bounce-in {
    animation: bounce-in 0.3s ease-out;
}
</style>
