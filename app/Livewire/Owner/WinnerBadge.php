<?php

namespace App\Livewire\Owner;

use Livewire\Component;
use App\Models\Restaurant;
use App\Models\MonthlyRanking;
use Illuminate\Support\Facades\Schema;

class WinnerBadge extends Component
{
    public int $restaurantId;

    protected static bool $isLazy = true;

    public function mount(int $restaurantId): void
    {
        $this->restaurantId = $restaurantId;
    }

    public function getBadgesProperty()
    {
        if (!Schema::hasTable('monthly_rankings')) {
            return collect();
        }

        return MonthlyRanking::where('restaurant_id', $this->restaurantId)
            ->where(function ($q) {
                $q->where('is_winner', true)
                  ->orWhere('position', 1);
            })
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();
    }

    public function getRestaurantProperty(): ?Restaurant
    {
        return Restaurant::select('id', 'name', 'city', 'state_id')
            ->with('state:id,code,name')
            ->find($this->restaurantId);
    }

    public function getScopeLabel(MonthlyRanking $badge): string
    {
        return match ($badge->ranking_type) {
            'city'     => strtoupper($badge->ranking_scope ?? $this->restaurant?->city ?? 'Ciudad'),
            'state'    => strtoupper($badge->ranking_scope ?? $this->restaurant?->state?->code ?? 'Estado'),
            'national' => 'NACIONAL',
            default    => strtoupper($badge->ranking_scope ?? 'REGIONAL'),
        };
    }

    public function getMonthName(int $month): string
    {
        $months = [
            1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
            5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
            9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre',
        ];
        return $months[$month] ?? '';
    }

    public function render()
    {
        return view('livewire.owner.winner-badge');
    }
}
