<div class="bg-gray-900 rounded-xl shadow-lg overflow-hidden">
    {{-- Header --}}
    <div class="p-6">
        <div class="flex items-center justify-between">
            <h3 class="text-white font-bold text-xl">Estadisticas</h3>
            @if($activeNow > 0)
                <div class="flex items-center gap-2">
                    <div class="relative">
                        <div class="w-2.5 h-2.5 bg-green-500 rounded-full"></div>
                        <div class="absolute inset-0 w-2.5 h-2.5 bg-green-400 rounded-full animate-ping"></div>
                    </div>
                    <span class="text-green-400 text-sm font-medium">En vivo</span>
                </div>
            @endif
        </div>
    </div>

    {{-- Stats Grid --}}
    <div class="px-6 pb-6">
        <div class="grid grid-cols-2 gap-4">
            {{-- Monthly Views --}}
            <div class="bg-gray-800 rounded-xl p-5 text-center">
                <div class="text-4xl font-bold text-white mb-1">{{ number_format($monthlyViews) }}</div>
                <p class="text-gray-400 text-sm">Vistas este mes</p>
            </div>

            {{-- Total Views --}}
            <div class="bg-gray-800 rounded-xl p-5 text-center">
                <div class="text-4xl font-bold text-white mb-1">{{ number_format($totalViews) }}</div>
                <p class="text-gray-400 text-sm">Vistas totales</p>
            </div>
        </div>

        {{-- Active Now --}}
        @if($activeNow > 0)
            <div class="mt-4 p-4 bg-green-900/30 rounded-xl border border-green-700/50">
                <div class="flex items-center justify-center gap-2">
                    <div class="relative">
                        <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                        <div class="absolute inset-0 w-2 h-2 bg-green-400 rounded-full animate-ping"></div>
                    </div>
                    <p class="text-sm text-green-400 font-medium">
                        {{ $activeNow }} {{ $activeNow === 1 ? 'persona esta' : 'personas estan' }} viendo este restaurante ahora
                    </p>
                </div>
            </div>
        @endif
    </div>

    {{-- Claim CTA for unclaimed restaurants --}}
    @if(!$restaurant->is_claimed)
        <div class="px-6 pb-6 border-t border-gray-800 pt-5">
            <div class="flex items-start gap-3 mb-4">
                <svg class="w-6 h-6 text-amber-400 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
                <p class="text-gray-300 text-sm">
                    <span class="text-amber-400 font-semibold">¿Eres el dueno?</span>
                    Reclama tu restaurante para acceder a estadisticas completas y herramientas premium.
                </p>
            </div>
            <a href="/claim?restaurant={{ $restaurant->slug }}"
               class="block w-full py-3 px-4 bg-gradient-to-r from-red-600 to-red-700 text-white font-bold text-center rounded-xl hover:from-red-700 hover:to-red-800 transition-all shadow-lg">
                Reclamar GRATIS
            </a>
        </div>
    @endif
</div>
