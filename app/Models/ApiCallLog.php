<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApiCallLog extends Model
{
    protected $table = "api_call_logs";

    protected $fillable = [
        "service",
        "endpoint",
        "status_code",
        "success",
        "cost",
        "params",
        "error_message",
        "called_at",
    ];

    protected $casts = [
        "success" => "boolean",
        "params" => "array",
        "cost" => "decimal:6",
        "called_at" => "datetime",
    ];

    // API cost per call (approximate)
    public const API_COSTS = [
        "yelp" => [
            "search" => 0.00,  // Yelp free tier
            "details" => 0.00,
            "reviews" => 0.00,
        ],
        "google_places" => [
            "text_search" => 0.032,  // $32 per 1000
            "details" => 0.017,      // $17 per 1000
            "photos" => 0.007,       // $7 per 1000
        ],
        "foursquare" => [
            "search" => 0.001,    // Varies by plan
            "details" => 0.001,
        ],
        "tripadvisor" => [
            "search" => 0.00,     // Free tier
            "details" => 0.00,
        ],
        "apple_maps" => [
            "search" => 0.00,     // Free with Apple Developer account
            "details" => 0.00,
        ],
    ];

    // Scopes
    public function scopeForService($query, string $service)
    {
        return $query->where("service", $service);
    }

    public function scopeSuccessful($query)
    {
        return $query->where("success", true);
    }

    public function scopeFailed($query)
    {
        return $query->where("success", false);
    }

    public function scopeToday($query)
    {
        return $query->whereDate("called_at", today());
    }

    public function scopeThisMonth($query)
    {
        return $query->whereYear("called_at", now()->year)
                     ->whereMonth("called_at", now()->month);
    }

    // Static helper to log a call
    public static function logCall(
        string $service,
        string $endpoint,
        bool $success = true,
        ?int $statusCode = null,
        ?array $params = null,
        ?string $errorMessage = null
    ): self {
        $cost = self::API_COSTS[$service][$endpoint] ?? 0;

        return self::create([
            "service" => $service,
            "endpoint" => $endpoint,
            "status_code" => $statusCode,
            "success" => $success,
            "cost" => $cost,
            "params" => $params,
            "error_message" => $errorMessage,
            "called_at" => now(),
        ]);
    }

    // Get statistics
    public static function getStats(string $period = "month"): array
    {
        $query = match ($period) {
            "today" => self::today(),
            "month" => self::thisMonth(),
            default => self::thisMonth(),
        };

        return [
            "total_calls" => (clone $query)->count(),
            "successful" => (clone $query)->successful()->count(),
            "failed" => (clone $query)->failed()->count(),
            "total_cost" => (clone $query)->sum("cost"),
            "by_service" => (clone $query)
                ->selectRaw("service, COUNT(*) as calls, SUM(cost) as cost")
                ->groupBy("service")
                ->get()
                ->keyBy("service")
                ->toArray(),
        ];
    }
}
