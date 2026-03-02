<div class="bg-gray-900 rounded-xl shadow-lg overflow-hidden">
    {{-- Header --}}
    <div class="p-6">
        <div class="flex items-start justify-between">
            <div class="flex items-center gap-3">
                <span class="text-3xl">🏆</span>
                <div>
                    <h3 class="text-white font-bold text-lg">FAMER AWARDS {{ now()->year }}</h3>
                    <p class="text-gray-400 text-sm">Votacion del mes</p>
                </div>
            </div>
            <div class="text-right">
                <div class="text-3xl font-bold text-white">{{ number_format($monthlyVotes) }}</div>
                <p class="text-gray-400 text-sm">votos</p>
            </div>
        </div>
    </div>

    {{-- Last Year's Ranking Badge --}}
    @if($lastYearPosition)
        <div class="px-6 pb-4">
            @if($isDefendingChampion)
                <div class="text-center">
                    <div class="inline-flex items-center gap-2 bg-gradient-to-r from-yellow-400 to-amber-500 text-gray-900 px-4 py-2 rounded-full shadow-md">
                        <span class="text-lg">🥇</span>
                        <span class="font-bold text-sm">#1 en {{ $lastYearScope }} {{ now()->year - 1 }}</span>
                    </div>
                    <p class="text-amber-400 text-xl font-bold mt-4">¡Defiende al campeon!</p>
                    <p class="text-gray-300 text-sm mt-2">
                        Tu voto puede ayudar a <span class="text-white font-semibold">{{ $restaurant->name }}</span> a
                        <span class="text-amber-400 font-semibold">mantener el #1</span> en {{ $lastYearScope }} este {{ now()->year }}
                    </p>
                </div>
            @elseif($lastYearPosition <= 10)
                <div class="text-center">
                    <div class="inline-flex items-center gap-2 bg-gradient-to-r from-blue-500 to-indigo-600 text-white px-4 py-2 rounded-full shadow-md">
                        <span class="text-lg">⭐</span>
                        <span class="font-bold text-sm">Top {{ $lastYearPosition }} - {{ $lastYearScope }} {{ now()->year - 1 }}</span>
                    </div>
                    <p class="text-gray-300 text-sm mt-3">¡Ayudalo a llegar al #1!</p>
                </div>
            @endif
        </div>
    @endif

    {{-- Vote Section --}}
    <div class="px-6 pb-6">
        @if($hasVoted)
            @if($justVoted)
                <div class="text-center py-5 px-4 bg-green-900/30 rounded-xl border border-green-700">
                    <div class="w-14 h-14 bg-green-500/20 rounded-full flex items-center justify-center mx-auto mb-3">
                        <svg class="w-7 h-7 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <p class="text-green-400 font-bold text-lg">¡Gracias por votar!</p>
                    <p class="text-gray-400 text-sm mt-1">Tu voto ha sido registrado</p>
                </div>
            @else
                <div class="text-center py-5 px-4 bg-gray-800 rounded-xl">
                    <div class="w-12 h-12 bg-amber-500/20 rounded-full flex items-center justify-center mx-auto mb-2">
                        <svg class="w-6 h-6 text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <p class="text-gray-300 font-medium">Ya votaste este mes</p>
                    <p class="text-gray-500 text-xs mt-1">Puedes votar de nuevo el proximo mes</p>
                </div>
            @endif
        @else
            @auth
                <button wire:click="vote"
                        wire:loading.attr="disabled"
                        wire:loading.class="opacity-75 cursor-wait"
                        class="w-full py-4 px-6 bg-gradient-to-r from-amber-500 to-orange-500 text-white font-bold rounded-xl hover:from-amber-600 hover:to-orange-600 transition-all shadow-lg hover:shadow-xl flex items-center justify-center gap-3 text-lg">
                    <span wire:loading.remove class="flex items-center gap-3">
                        <span class="text-2xl">🏆</span>
                        Votar por este restaurante
                    </span>
                    <span wire:loading class="flex items-center gap-2">
                        <svg class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                        Registrando...
                    </span>
                </button>
            @else
                <a href="{{ route('login') }}"
                   class="w-full py-4 px-6 bg-gradient-to-r from-amber-500 to-orange-500 text-white font-bold rounded-xl hover:from-amber-600 hover:to-orange-600 transition-all shadow-lg hover:shadow-xl flex items-center justify-center gap-3 text-lg">
                    <span class="text-2xl">🏆</span>
                    Inicia sesion para votar
                </a>
                <p class="text-center text-gray-400 text-sm mt-4">
                    ¿No tienes cuenta? <a href="{{ route('register') }}" class="text-amber-400 hover:text-amber-300 font-semibold">Registrate gratis en 30 segundos</a>
                </p>
            @endauth
        @endif
    </div>

    {{-- Footer Link --}}
    <div class="px-6 pb-6 pt-2 border-t border-gray-800">
        <a href="{{ route('famer.awards') }}" class="block text-center text-amber-400 hover:text-amber-300 font-medium transition-colors">
            Ver todos los restaurantes nominados en {{ $restaurant->city ?? 'tu ciudad' }} →
        </a>
    </div>
</div>
