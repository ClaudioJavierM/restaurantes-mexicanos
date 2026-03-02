<?php

namespace App\Livewire;

use App\Models\Advertisement;
use Livewire\Component;

class AdvertisementBanner extends Component
{
    public string $placement = 'sidebar';
    public ?int $stateId = null;
    public $advertisement = null;

    public function mount(string $placement = 'sidebar', ?int $stateId = null)
    {
        $this->placement = $placement;
        $this->stateId = $stateId;

        // Obtener un anuncio activo aleatorio para esta ubicación y estado
        $this->advertisement = Advertisement::active()
            ->forPlacement($this->placement)
            ->forState($this->stateId)
            ->inRandomOrder()
            ->first();

        // Incrementar el contador de vistas si hay un anuncio
        if ($this->advertisement) {
            $this->advertisement->incrementViews();
        }
    }

    public function trackClick()
    {
        if ($this->advertisement) {
            $this->advertisement->incrementClicks();
        }

        // Redirigir al link del anuncio
        if ($this->advertisement && $this->advertisement->link_url) {
            return redirect()->away($this->advertisement->link_url);
        }
    }

    public function render()
    {
        return view('livewire.advertisement-banner');
    }
}
