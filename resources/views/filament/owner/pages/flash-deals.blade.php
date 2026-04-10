<x-filament-panels::page>
    @if($restaurant)
        @livewire('owner.flash-deals-manager')
    @else
        <div style="display:flex; align-items:center; justify-content:center; min-height:40vh;">
            <div style="text-align:center; color:#9CA3AF;">
                <p style="font-size:1rem;">No se encontró un restaurante asociado a tu cuenta.</p>
            </div>
        </div>
    @endif
</x-filament-panels::page>
