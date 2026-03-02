<x-filament-panels::page>
    @if($restaurant)
        <div class="space-y-6">
            @livewire('owner.menu-upload', ['restaurant' => $restaurant])
        </div>
    @else
        <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-6 text-center">
            <div class="text-4xl mb-3">🏪</div>
            <h3 class="text-lg font-semibold text-yellow-800">No tienes restaurantes</h3>
            <p class="text-yellow-700 mt-2">
                Primero necesitas reclamar o agregar un restaurante para subir tu menú.
            </p>
            <a href="{{ route('claim.restaurant') }}" 
               class="inline-block mt-4 px-6 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition">
                Reclamar Restaurante
            </a>
        </div>
    @endif
</x-filament-panels::page>
