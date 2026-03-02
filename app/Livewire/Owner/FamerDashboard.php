<?php

namespace App\Livewire\Owner;

use Livewire\Component;
use App\Models\Restaurant;
use App\Models\RestaurantVote;
use App\Models\RestaurantRanking;
use App\Models\MonthlyRanking;
use App\Models\RestaurantBadge;
use Carbon\Carbon;

class FamerDashboard extends Component
{
    public Restaurant $restaurant;
    
    // Current stats
    public int $monthlyVotes = 0;
    public int $totalVotes = 0;
    public ?int $cityRank = null;
    public ?int $stateRank = null;
    public ?int $nationalRank = null;
    
    // Historical data
    public array $monthlyVotesHistory = [];
    public array $rankingHistory = [];
    
    public function mount(Restaurant $restaurant)
    {
        $this->restaurant = $restaurant;
        $this->loadStats();
    }
    
    protected function loadStats()
    {
        $month = now()->month;
        $year = now()->year;
        
        // Monthly votes
        $this->monthlyVotes = RestaurantVote::where('restaurant_id', $this->restaurant->id)
            ->where('month', $month)
            ->where('year', $year)
            ->count();
            
        // Total votes
        $this->totalVotes = RestaurantVote::where('restaurant_id', $this->restaurant->id)->count();
        
        // Rankings
        $cityRanking = RestaurantRanking::where('restaurant_id', $this->restaurant->id)
            ->where('year', $year)
            ->where('ranking_type', 'city')
            ->where('ranking_scope', $this->restaurant->city)
            ->first();
        $this->cityRank = $cityRanking?->rank;
        
        $stateRanking = RestaurantRanking::where('restaurant_id', $this->restaurant->id)
            ->where('year', $year)
            ->where('ranking_type', 'state')
            ->first();
        $this->stateRank = $stateRanking?->rank;
        
        $nationalRanking = RestaurantRanking::where('restaurant_id', $this->restaurant->id)
            ->where('year', $year)
            ->where('ranking_type', 'national')
            ->first();
        $this->nationalRank = $nationalRanking?->rank;
        
        // Monthly history (last 6 months)
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $votes = RestaurantVote::where('restaurant_id', $this->restaurant->id)
                ->where('month', $date->month)
                ->where('year', $date->year)
                ->count();
            $this->monthlyVotesHistory[] = [
                'month' => $date->format('M'),
                'votes' => $votes
            ];
        }
    }
    
    public function getBadgesProperty()
    {
        return RestaurantBadge::where('restaurant_id', $this->restaurant->id)
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();
    }
    
    public function getCompetitorsProperty()
    {
        return Restaurant::where('city', $this->restaurant->city)
            ->where('status', 'approved')
            ->where('id', '!=', $this->restaurant->id)
            ->withCount(['votes as monthly_votes' => function($q) {
                $q->where('month', now()->month)->where('year', now()->year);
            }])
            ->orderByDesc('monthly_votes')
            ->limit(5)
            ->get();
    }
    
    public function getVoteUrlProperty()
    {
        return url("/restaurante/{$this->restaurant->slug}#votar");
    }
    
    public function getQrCodeUrlProperty()
    {
        $voteUrl = urlencode($this->voteUrl);
        return "https://api.qrserver.com/v1/create-qr-code/?size=400x400&data={$voteUrl}";
    }

    public function getShareUrlProperty()
    {
        return route('restaurants.show', $this->restaurant->slug);
    }
    
    public function render()
    {
        return view('livewire.owner.famer-dashboard')
            ->layout('layouts.owner');
    }
}
