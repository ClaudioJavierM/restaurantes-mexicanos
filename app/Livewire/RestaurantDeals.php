<?php

namespace App\Livewire;

use App\Models\FlashDeal;
use Livewire\Component;

class RestaurantDeals extends Component
{
    protected static bool $isLazy = true;

    public int $restaurantId;
    public bool $showCode = false;
    /** @var int|null deal ID whose code is being revealed */
    public ?int $revealedDealId = null;

    public function getDealsProperty()
    {
        return FlashDeal::where('restaurant_id', $this->restaurantId)
            ->active()
            ->orderByDesc('ends_at')
            ->get();
    }

    public function revealCode(int $dealId): void
    {
        $this->showCode = true;
        $this->revealedDealId = $dealId;
        $this->dispatch('deal-code-revealed', dealId: $dealId, restaurantId: $this->restaurantId);
    }

    public function render()
    {
        return view('livewire.restaurant-deals', [
            'deals' => $this->deals,
        ]);
    }
}
