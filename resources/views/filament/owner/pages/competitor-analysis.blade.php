<x-filament-panels::page>
    @if($restaurantId)
        @livewire('owner.competitor-insights', ['restaurantId' => $restaurantId])
    @else
        <div style="text-align:center; padding:3rem; color:#9CA3AF;">
            <p>No se encontró un restaurante asociado a tu cuenta.</p>
        </div>
    @endif
</x-filament-panels::page>
