<?php

namespace App\Livewire\Owner;

use App\Models\Restaurant;
use App\Services\CompetitorInsightsService;
use Livewire\Component;

class CompetitorInsights extends Component
{
    public int $restaurantId;

    protected static bool $isLazy = true;

    public function mount(int $restaurantId): void
    {
        $this->restaurantId = $restaurantId;
    }

    public function getRestaurantProperty(): Restaurant
    {
        return Restaurant::findOrFail($this->restaurantId);
    }

    public function getInsightsProperty(): array
    {
        return app(CompetitorInsightsService::class)->getInsights($this->restaurant);
    }

    public function refreshData(): void
    {
        app(CompetitorInsightsService::class)->refreshInsights($this->restaurant);
        $this->dispatch('$refresh');
    }

    public function render()
    {
        return view('livewire.owner.competitor-insights')
            ->layout('layouts.owner');
    }
}
