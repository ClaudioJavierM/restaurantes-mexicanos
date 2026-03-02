<?php

namespace App\Livewire;

use App\Models\Restaurant;
use App\Models\AnalyticsEvent;
use Livewire\Component;
use Carbon\Carbon;

class LiveVisits extends Component
{
    public Restaurant $restaurant;
    public int $activeNow = 0;
    public int $monthlyViews = 0;
    public int $totalViews = 0;

    public function mount(Restaurant $restaurant)
    {
        $this->restaurant = $restaurant;
        $this->loadVisitData();
    }

    public function loadVisitData()
    {
        // Active now (last 15 minutes)
        $this->activeNow = AnalyticsEvent::where('restaurant_id', $this->restaurant->id)
            ->where('event_type', AnalyticsEvent::EVENT_PAGE_VIEW)
            ->where('created_at', '>=', now()->subMinutes(15))
            ->distinct('session_id')
            ->count('session_id');

        // This month's views
        $this->monthlyViews = AnalyticsEvent::where('restaurant_id', $this->restaurant->id)
            ->where('event_type', AnalyticsEvent::EVENT_PAGE_VIEW)
            ->whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->count();

        // Total views (all time)
        $this->totalViews = AnalyticsEvent::where('restaurant_id', $this->restaurant->id)
            ->where('event_type', AnalyticsEvent::EVENT_PAGE_VIEW)
            ->count();
    }

    public function render()
    {
        return view('livewire.live-visits');
    }
}
