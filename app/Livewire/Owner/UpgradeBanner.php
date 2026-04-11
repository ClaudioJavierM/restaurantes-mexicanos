<?php

namespace App\Livewire\Owner;

use Livewire\Component;
use App\Models\Restaurant;

class UpgradeBanner extends Component
{
    public Restaurant $restaurant;

    public function mount(Restaurant $restaurant): void
    {
        $this->restaurant = $restaurant;
    }

    /**
     * Show banner if:
     *   - subscription_tier is null or 'free'   (not on a paid plan), OR
     *   - subscription_status is not 'active'   (lapsed/canceled)
     * AND the banner has not been dismissed in this session.
     *
     * Restaurant model uses:
     *   - subscription_tier  : null | 'free' | 'premium' | 'elite'  (plan name)
     *   - subscription_status: null | 'active' | 'canceled' | 'expired' | 'past_due'
     */
    public function getIsVisibleProperty(): bool
    {
        $isPaidAndActive = !empty($this->restaurant->subscription_tier)
            && $this->restaurant->subscription_tier !== 'free'
            && $this->restaurant->subscription_status === 'active';

        $dismissed = session('upgrade_banner_dismissed_' . $this->restaurant->id, false);

        return !$isPaidAndActive && !$dismissed;
    }

    public function dismiss(): void
    {
        session(['upgrade_banner_dismissed_' . $this->restaurant->id => true]);
    }

    public function render()
    {
        return view('livewire.owner.upgrade-banner');
    }
}
