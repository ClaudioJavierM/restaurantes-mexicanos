<x-filament-panels::page>
    @if($restaurant)
        @livewire('owner.email-marketing', ['restaurant' => $restaurant])
    @else
        <div class="flex items-center justify-center min-h-[40vh]">
            <div class="text-center">
                <p class="text-lg text-gray-500 dark:text-gray-400">No se encontró un restaurante asociado a tu cuenta.</p>
            </div>
        </div>
    @endif
</x-filament-panels::page>
