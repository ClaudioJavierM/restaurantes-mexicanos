<?php

namespace App\Http\Controllers;

use App\Models\AnalyticsEvent;
use App\Models\Restaurant;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class ForOwnersController extends Controller
{
    public function index()
    {
        $stats = Cache::remember("platform_stats_for_owners", 300, function () {
            $firstRecord = AnalyticsEvent::orderBy("created_at", "asc")->first();
            $totalRestaurants = Restaurant::where("status", "approved")->count();
            
            if (!$firstRecord) {
                return [
                    "total_views" => 0,
                    "daily_avg" => 0,
                    "total_restaurants" => $totalRestaurants,
                    "weekly_growth" => 0,
                    "start_date" => now()->format("M d, Y"),
                ];
            }
            
            $firstDate = Carbon::parse($firstRecord->created_at);
            $totalDays = max(1, $firstDate->diffInDays(now()) + 1);
            $totalViews = AnalyticsEvent::where("event_type", "page_view")->count();
            $avgPerDay = $totalDays > 0 ? $totalViews / $totalDays : 0;
            
            $thisWeekViews = AnalyticsEvent::where("event_type", "page_view")
                ->where("created_at", ">=", Carbon::now()->startOfWeek())
                ->count();
            $lastWeekViews = AnalyticsEvent::where("event_type", "page_view")
                ->whereBetween("created_at", [Carbon::now()->subWeek()->startOfWeek(), Carbon::now()->subWeek()->endOfWeek()])
                ->count();
            
            $weeklyGrowth = $lastWeekViews > 0 ? (($thisWeekViews - $lastWeekViews) / $lastWeekViews) * 100 : 0;
            
            return [
                "total_views" => $totalViews,
                "daily_avg" => round($avgPerDay),
                "total_restaurants" => $totalRestaurants,
                "weekly_growth" => round($weeklyGrowth, 1),
                "start_date" => $firstDate->format("M d, Y"),
                "google_ads_value" => round($totalViews * 2),
            ];
        });
        
        return view("for-owners", compact("stats"));
    }
}
