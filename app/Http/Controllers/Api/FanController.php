<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FanController extends Controller
{
    public function myFanBadges(Request $request)
    {
        $user = $request->user();

        $fanScores = \App\Models\FanScore::where('user_id', $user->id)
            ->where('year', now()->year)
            ->where('total_points', '>=', 50)
            ->with('restaurant:id,name,slug,city,state,featured_image')
            ->orderByDesc('total_points')
            ->get()
            ->map(function ($score) {
                return [
                    'restaurant_id' => $score->restaurant_id,
                    'restaurant_name' => $score->restaurant->name ?? '',
                    'restaurant_slug' => $score->restaurant->slug ?? '',
                    'restaurant_city' => $score->restaurant->city ?? '',
                    'restaurant_image' => $score->restaurant->featured_image ?? '',
                    'total_points' => $score->total_points,
                    'fan_level' => $score->fan_level,
                    'badge_accepted' => $score->badge_accepted,
                    'level_info' => $score->getLevelInfo(),
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $fanScores,
            'total_badges' => $fanScores->count(),
        ]);
    }
}
