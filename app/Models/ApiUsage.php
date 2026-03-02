<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApiUsage extends Model
{
    protected $table = 'api_usage';

    protected $fillable = [
        'service',
        'endpoint',
        'requests_count',
        'estimated_cost',
        'usage_date',
        'metadata',
    ];

    protected $casts = [
        'usage_date' => 'date',
        'metadata' => 'array',
        'estimated_cost' => 'decimal:4',
    ];

    // Get total cost for current month
    public static function getCurrentMonthCost(): float
    {
        return self::whereYear('usage_date', now()->year)
            ->whereMonth('usage_date', now()->month)
            ->sum('estimated_cost');
    }

    // Get total requests for today
    public static function getTodayRequests(): int
    {
        return self::whereDate('usage_date', today())
            ->sum('requests_count');
    }

    // Get total cost for today
    public static function getTodayCost(): float
    {
        return self::whereDate('usage_date', today())
            ->sum('estimated_cost');
    }

    // Check if we can make more requests today
    public static function canMakeRequest(int $requestCount = 1): bool
    {
        $dailyLimit = (int) config('services.google.daily_request_limit', 200);
        $todayRequests = self::getTodayRequests();
        
        return ($todayRequests + $requestCount) <= $dailyLimit;
    }

    // Check if we're within monthly budget
    public static function withinMonthlyBudget(float $estimatedCost = 0): bool
    {
        $monthlyBudget = (float) config('services.google.monthly_budget_limit', 180);
        $currentCost = self::getCurrentMonthCost();
        
        return ($currentCost + $estimatedCost) <= $monthlyBudget;
    }
}
