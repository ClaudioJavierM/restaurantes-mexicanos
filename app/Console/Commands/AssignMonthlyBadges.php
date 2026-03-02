<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Restaurant;
use App\Models\RestaurantVote;
use App\Models\RestaurantBadge;
use App\Models\MonthlyRanking;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AssignMonthlyBadges extends Command
{
    protected $signature = 'famer:assign-badges {--month= : Month number (1-12)} {--year= : Year}';
    protected $description = 'Assign monthly badges to top restaurants in each city and state';

    public function handle()
    {
        $month = $this->option('month') ?? now()->subMonth()->month;
        $year = $this->option('year') ?? ($month == 12 ? now()->year - 1 : now()->year);
        
        $this->info("Assigning badges for {}/{}...");
        
        // Get vote counts by city
        $cityVotes = RestaurantVote::select('restaurant_id', 'city', 'state_code', DB::raw('COUNT(*) as vote_count'))
            ->where('month', $month)
            ->where('year', $year)
            ->groupBy('restaurant_id', 'city', 'state_code')
            ->orderByDesc('vote_count')
            ->get()
            ->groupBy('city');
        
        $cityBadges = 0;
        $stateBadges = 0;
        
        // Assign city badges (Top 1 per city)
        foreach ($cityVotes as $city => $votes) {
            if ($votes->count() < 3) continue; // Need at least 3 participants
            
            $winner = $votes->first();
            $restaurant = Restaurant::find($winner->restaurant_id);
            if (!$restaurant) continue;
            
            // Check if badge already exists
            $exists = RestaurantBadge::where('restaurant_id', $winner->restaurant_id)
                ->where('month', $month)
                ->where('year', $year)
                ->where('badge_type', 'city_winner')
                ->exists();
                
            if (!$exists) {
                RestaurantBadge::create([
                    'restaurant_id' => $winner->restaurant_id,
                    'badge_type' => 'city_winner',
                    'badge_name' => "Best in {$city}",
                    'badge_icon' => '🏆',
                    'month' => $month,
                    'year' => $year,
                    'rank' => 1,
                    'scope' => $city,
                    'metadata' => ['votes' => $winner->vote_count, 'state' => $winner->state_code],
                ]);
                $cityBadges++;
            }
            
            // Save to monthly rankings
            MonthlyRanking::updateOrCreate(
                [
                    'restaurant_id' => $winner->restaurant_id,
                    'month' => $month,
                    'year' => $year,
                    'ranking_type' => 'city',
                    'ranking_scope' => $city,
                ],
                [
                    'rank' => 1,
                    'votes_count' => $winner->vote_count,
                    'score' => $winner->vote_count,
                ]
            );
        }
        
        // Get vote counts by state
        $stateVotes = RestaurantVote::select('restaurant_id', 'state_code', DB::raw('COUNT(*) as vote_count'))
            ->where('month', $month)
            ->where('year', $year)
            ->groupBy('restaurant_id', 'state_code')
            ->orderByDesc('vote_count')
            ->get()
            ->groupBy('state_code');
        
        // Assign state badges (Top 10 per state)
        foreach ($stateVotes as $state => $votes) {
            if ($votes->count() < 10) continue; // Need at least 10 participants
            
            $rank = 1;
            foreach ($votes->take(10) as $winner) {
                $restaurant = Restaurant::find($winner->restaurant_id);
                if (!$restaurant) continue;
                
                if ($rank == 1) {
                    $exists = RestaurantBadge::where('restaurant_id', $winner->restaurant_id)
                        ->where('month', $month)
                        ->where('year', $year)
                        ->where('badge_type', 'state_winner')
                        ->exists();
                        
                    if (!$exists) {
                        RestaurantBadge::create([
                            'restaurant_id' => $winner->restaurant_id,
                            'badge_type' => 'state_winner',
                            'badge_name' => "Best in {$state}",
                            'badge_icon' => '🥇',
                            'month' => $month,
                            'year' => $year,
                            'rank' => 1,
                            'scope' => $state,
                            'metadata' => ['votes' => $winner->vote_count],
                        ]);
                        $stateBadges++;
                    }
                }
                
                // Save to monthly rankings
                MonthlyRanking::updateOrCreate(
                    [
                        'restaurant_id' => $winner->restaurant_id,
                        'month' => $month,
                        'year' => $year,
                        'ranking_type' => 'state',
                        'ranking_scope' => $state,
                    ],
                    [
                        'rank' => $rank,
                        'votes_count' => $winner->vote_count,
                        'score' => $winner->vote_count,
                    ]
                );
                
                $rank++;
            }
        }
        
        // Assign national badge (Top 100 USA)
        $nationalVotes = RestaurantVote::select('restaurant_id', DB::raw('COUNT(*) as vote_count'))
            ->where('month', $month)
            ->where('year', $year)
            ->groupBy('restaurant_id')
            ->orderByDesc('vote_count')
            ->limit(100)
            ->get();
        
        $nationalBadges = 0;
        if ($nationalVotes->count() >= 100) {
            $rank = 1;
            foreach ($nationalVotes as $winner) {
                if ($rank == 1) {
                    $exists = RestaurantBadge::where('restaurant_id', $winner->restaurant_id)
                        ->where('month', $month)
                        ->where('year', $year)
                        ->where('badge_type', 'national_winner')
                        ->exists();
                        
                    if (!$exists) {
                        RestaurantBadge::create([
                            'restaurant_id' => $winner->restaurant_id,
                            'badge_type' => 'national_winner',
                            'badge_name' => "#1 in USA",
                            'badge_icon' => '👑',
                            'month' => $month,
                            'year' => $year,
                            'rank' => 1,
                            'scope' => 'USA',
                            'metadata' => ['votes' => $winner->vote_count],
                        ]);
                        $nationalBadges++;
                    }
                }
                
                MonthlyRanking::updateOrCreate(
                    [
                        'restaurant_id' => $winner->restaurant_id,
                        'month' => $month,
                        'year' => $year,
                        'ranking_type' => 'national',
                        'ranking_scope' => 'USA',
                    ],
                    [
                        'rank' => $rank,
                        'votes_count' => $winner->vote_count,
                        'score' => $winner->vote_count,
                    ]
                );
                
                $rank++;
            }
        }
        
        $this->info("Badges assigned: {$cityBadges} city, {$stateBadges} state, {$nationalBadges} national");
        
        return Command::SUCCESS;
    }
}
