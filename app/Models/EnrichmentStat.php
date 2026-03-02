<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EnrichmentStat extends Model
{
    protected $table = "enrichment_stats";

    protected $fillable = [
        "stat_date",
        "type",
        "processed",
        "success",
        "failed",
        "skipped",
        "api_cost",
        "duration_seconds",
    ];

    protected $casts = [
        "stat_date" => "date",
        "api_cost" => "decimal:4",
    ];

    public const TYPES = [
        "seo_descriptions" => "SEO Descriptions",
        "yelp_backfill" => "Yelp Backfill",
        "google_enrich" => "Google Enrichment",
        "email_scrape" => "Email Scraping",
        "duplicate_detection" => "Duplicate Detection",
        "photo_download" => "Photo Downloads",
    ];

    // Scopes
    public function scopeForType($query, string $type)
    {
        return $query->where("type", $type);
    }

    public function scopeToday($query)
    {
        return $query->whereDate("stat_date", today());
    }

    public function scopeThisMonth($query)
    {
        return $query->whereYear("stat_date", now()->year)
                     ->whereMonth("stat_date", now()->month);
    }

    // Static helper to record enrichment
    public static function record(string $type, array $data): self
    {
        return self::updateOrCreate(
            [
                "stat_date" => $data["stat_date"] ?? today(),
                "type" => $type,
            ],
            [
                "processed" => $data["processed"] ?? 0,
                "success" => $data["success"] ?? 0,
                "failed" => $data["failed"] ?? 0,
                "skipped" => $data["skipped"] ?? 0,
                "api_cost" => $data["api_cost"] ?? 0,
                "duration_seconds" => $data["duration_seconds"] ?? null,
            ]
        );
    }

    public static function getTotals(string $period = "month"): array
    {
        $query = match ($period) {
            "today" => self::today(),
            "month" => self::thisMonth(),
            default => self::thisMonth(),
        };

        return [
            "total_processed" => (clone $query)->sum("processed"),
            "total_success" => (clone $query)->sum("success"),
            "total_failed" => (clone $query)->sum("failed"),
            "total_cost" => (clone $query)->sum("api_cost"),
        ];
    }
}
