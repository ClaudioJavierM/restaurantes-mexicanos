<div class="min-h-screen bg-gradient-to-b from-amber-50 to-orange-100 py-8 px-4">
    <div class="max-w-md mx-auto">
        
        {{-- Restaurant Card --}}
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
            {{-- Image --}}
            <div class="h-48 bg-gradient-to-r from-amber-400 to-orange-400 relative">
                @if($restaurant->image)
                    <img src="{{ $restaurant->image }}" alt="{{ $restaurant->name }}" class="w-full h-full object-cover">
                @else
                    <div class="flex items-center justify-center h-full">
                        <span class="text-6xl">🌮</span>
                    </div>
                @endif
                <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                <div class="absolute bottom-4 left-4 right-4 text-white">
                    <h1 class="text-2xl font-bold">{{ $restaurant->name }}</h1>
                    <p class="text-sm opacity-90">{{ $restaurant->city }}, {{ $restaurant->state?->code }}</p>
                </div>
            </div>

            <div class="p-6">
                @if($hasVoted)
                    {{-- Already Voted State --}}
                    <div class="text-center">
                        @if($justVoted)
                            <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                            <h2 class="text-2xl font-bold text-gray-900 mb-2">Gracias por tu voto!</h2>
                            @if($emailVerified)
                                <p class="text-green-600 font-medium mb-2">✓ Voto verificado con email</p>
                            @endif
                            <p class="text-gray-600">Tu voto ha sido registrado para los FAMER Awards 2026</p>
                        @else
                            <div class="w-20 h-20 bg-amber-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg class="w-10 h-10 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                </svg>
                            </div>
                            <h2 class="text-xl font-bold text-gray-900 mb-2">Ya votaste este mes</h2>
                            <p class="text-gray-600">Puedes votar nuevamente el proximo mes</p>
                        @endif

                        {{-- Stats --}}
                        <div class="mt-6 p-4 bg-amber-50 rounded-xl">
                            <p class="text-sm text-amber-800">
                                <span class="font-bold text-2xl">{{ number_format($monthlyVotes) }}</span> 
                                votos este mes
                            </p>
                        </div>

                        {{-- Share buttons --}}
                        @if($justVoted)
                        <div class="mt-6">
                            <p class="text-sm text-gray-500 mb-3">Comparte tu apoyo:</p>
                            <div class="flex justify-center gap-3">
                                <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(route('restaurants.show', $restaurant->slug)) }}" 
                                   target="_blank"
                                   class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm hover:bg-blue-700">
                                    Facebook
                                </a>
                                <a href="https://twitter.com/intent/tweet?text=Vote por {{ urlencode($restaurant->name) }} en los FAMER Awards!&url={{ urlencode(route('restaurants.show', $restaurant->slug)) }}" 
                                   target="_blank"
                                   class="px-4 py-2 bg-sky-500 text-white rounded-lg text-sm hover:bg-sky-600">
                                    Twitter
                                </a>
                            </div>
                        </div>
                        @endif
                    </div>
                @else
                    {{-- Vote State --}}
                    <div class="text-center">
                        <div class="mb-6">
                            <span class="inline-block px-4 py-1 bg-amber-100 text-amber-700 rounded-full text-sm font-medium mb-3">
                                FAMER Awards 2026
                            </span>
                            <h2 class="text-xl font-bold text-gray-900">Vota por este restaurante</h2>
                            <p class="text-gray-600 mt-2">Ayuda a {{ $restaurant->name }} a ganar reconocimiento</p>
                        </div>

                        {{-- Quick Vote Button --}}
                        @if(!$showEmailForm)
                        <button wire:click="vote" 
                                wire:loading.attr="disabled"
                                class="w-full py-4 px-6 bg-gradient-to-r from-amber-500 to-orange-500 text-white text-lg font-bold rounded-xl hover:from-amber-600 hover:to-orange-600 transition shadow-lg">
                            <span wire:loading.remove>
                                <svg class="w-6 h-6 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5"></path>
                                </svg>
                                Votar Ahora
                            </span>
                            <span wire:loading class="flex items-center justify-center">
                                <svg class="animate-spin h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Registrando...
                            </span>
                        </button>

                        {{-- Email Option Toggle --}}
                        <div class="mt-4">
                            <button wire:click="toggleEmailForm" class="text-sm text-gray-500 hover:text-amber-600">
                                ¿Quieres verificar tu voto con email?
                            </button>
                        </div>
                        @endif

                        {{-- Email Form --}}
                        @if($showEmailForm)
                        <div class="bg-gray-50 rounded-xl p-4">
                            <p class="text-sm text-gray-600 mb-3">
                                Ingresa tu email para un voto verificado (opcional)
                            </p>
                            <form wire:submit.prevent="voteWithEmail" class="space-y-3">
                                <input type="email" 
                                       wire:model="email" 
                                       placeholder="tu@email.com"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                                @error('email') 
                                    <p class="text-red-500 text-sm">{{ $message }}</p> 
                                @enderror
                                
                                <button type="submit" 
                                        wire:loading.attr="disabled"
                                        class="w-full py-3 bg-green-600 text-white font-bold rounded-lg hover:bg-green-700 transition">
                                    <span wire:loading.remove>Votar con Email</span>
                                    <span wire:loading>Registrando...</span>
                                </button>
                            </form>
                            
                            <button wire:click="vote" class="w-full mt-2 py-2 text-gray-500 hover:text-gray-700 text-sm">
                                O votar sin email
                            </button>
                        </div>
                        @endif

                        {{-- Stats --}}
                        <div class="mt-6 p-4 bg-gray-50 rounded-xl">
                            <p class="text-sm text-gray-600">
                                Este restaurante tiene 
                                <span class="font-bold text-amber-600">{{ number_format($monthlyVotes) }}</span> 
                                votos este mes
                            </p>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- Link to restaurant page --}}
        <div class="text-center mt-6">
            <a href="{{ route('restaurants.show', $restaurant->slug) }}" 
               class="text-amber-700 hover:text-amber-800 font-medium">
                Ver perfil completo →
            </a>
        </div>

        {{-- FAMER Branding --}}
        <div class="text-center mt-8">
            <p class="text-sm text-gray-500">
                Powered by <span class="font-bold text-amber-600">FAMER Awards</span>
            </p>
        </div>
    </div>
</div>
