<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Restaurant;
use Illuminate\Support\Facades\DB;

class AwardsHallOfFame extends Component
{
    public function getAllTimeLeadersProperty()
    {
        $results = DB::table('restaurant_votes')
            ->select('restaurant_id', DB::raw('COUNT(*) as total_votes'))
            ->groupBy('restaurant_id')
            ->orderByDesc('total_votes')
            ->limit(25)
            ->get();

        return $results->map(function ($row) {
            $restaurant = Restaurant::with('state')->find($row->restaurant_id);
            if (! $restaurant) {
                return null;
            }

            $restaurant->total_votes = $row->total_votes;

            // Count distinct months this restaurant appeared in the votes table
            $restaurant->months_participated = DB::table('restaurant_votes')
                ->selectRaw('COUNT(DISTINCT CONCAT(year, "-", LPAD(month,2,"0"))) as cnt')
                ->where('restaurant_id', $row->restaurant_id)
                ->value('cnt') ?? 0;

            return $restaurant;
        })->filter()->values();
    }

    public function render()
    {
        return view('livewire.awards-hall-of-fame', [
            'leaders' => $this->allTimeLeaders,
        ])->layout('layouts.app', ['title' => 'FAMER Awards — Hall of Fame']);
    }
}
