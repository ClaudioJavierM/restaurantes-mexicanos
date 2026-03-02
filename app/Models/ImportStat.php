<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImportStat extends Model
{
    protected $table = "import_stats";

    protected $fillable = [
        "stat_date",
        "source",
        "command",
        "total_found",
        "imported",
        "duplicates_skipped",
        "errors",
        "api_calls",
        "estimated_cost",
        "states_processed",
        "cities_processed",
        "duration_seconds",
    ];

    protected $casts = [
        "stat_date" => "date",
        "states_processed" => "array",
        "cities_processed" => "array",
        "estimated_cost" => "decimal:4",
    ];

    // Scopes
    public function scopeForSource($query, string $source)
    {
        return $query->where("source", $source);
    }

    public function scopeToday($query)
    {
        return $query->whereDate("stat_date", today());
    }

    public function scopeThisWeek($query)
    {
        return $query->where("stat_date", ">=", now()->startOfWeek());
    }

    public function scopeThisMonth($query)
    {
        return $query->whereYear("stat_date", now()->year)
                     ->whereMonth("stat_date", now()->month);
    }

    public function scopeLast7Days($query)
    {
        return $query->where("stat_date", ">=", now()->subDays(7));
    }

    public function scopeLast30Days($query)
    {
        return $query->where("stat_date", ">=", now()->subDays(30));
    }

    // Static helpers
    public static function recordImport(array $data): self
    {
        return self::updateOrCreate(
            [
                "stat_date" => $data["stat_date"] ?? today(),
                "source" => $data["source"] ?? "yelp",
                "command" => $data["command"] ?? null,
            ],
            [
                "total_found" => ($data["total_found"] ?? 0) + (self::where([
                    "stat_date" => $data["stat_date"] ?? today(),
                    "source" => $data["source"] ?? "yelp",
                    "command" => $data["command"] ?? null,
                ])->first()->total_found ?? 0),
                "imported" => ($data["imported"] ?? 0) + (self::where([
                    "stat_date" => $data["stat_date"] ?? today(),
                    "source" => $data["source"] ?? "yelp",
                    "command" => $data["command"] ?? null,
                ])->first()->imported ?? 0),
                "duplicates_skipped" => ($data["duplicates_skipped"] ?? 0) + (self::where([
                    "stat_date" => $data["stat_date"] ?? today(),
                    "source" => $data["source"] ?? "yelp",
                    "command" => $data["command"] ?? null,
                ])->first()->duplicates_skipped ?? 0),
                "errors" => ($data["errors"] ?? 0),
                "api_calls" => ($data["api_calls"] ?? 0),
                "estimated_cost" => ($data["estimated_cost"] ?? 0),
                "states_processed" => $data["states_processed"] ?? null,
                "cities_processed" => $data["cities_processed"] ?? null,
                "duration_seconds" => $data["duration_seconds"] ?? null,
            ]
        );
    }

    // Get totals for dashboard
    public static function getTotals(string $period = "month"): array
    {
        $query = match ($period) {
            "today" => self::today(),
            "week" => self::thisWeek(),
            "month" => self::thisMonth(),
            "7days" => self::last7Days(),
            "30days" => self::last30Days(),
            default => self::thisMonth(),
        };

        return [
            "total_found" => $query->sum("total_found"),
            "imported" => $query->sum("imported"),
            "duplicates_skipped" => $query->sum("duplicates_skipped"),
            "errors" => $query->sum("errors"),
            "api_calls" => $query->sum("api_calls"),
            "estimated_cost" => $query->sum("estimated_cost"),
        ];
    }
}
