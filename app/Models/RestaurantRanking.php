<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class RestaurantRanking extends Model
{
    protected $fillable = [
        'restaurant_id', 'year', 'ranking_type', 'ranking_scope',
        'position', 'final_score', 'score_breakdown', 'badge_name',
        'certificate_path', 'is_published', 'published_at',
    ];

    protected $casts = [
        'score_breakdown' => 'array',
        'is_published' => 'boolean',
        'published_at' => 'datetime',
    ];

    const TYPES = [
        'city' => 'Top 10 Ciudad',
        'state' => 'Top 10 Estado',
        'national' => 'Top 100 USA',
        'category' => 'Mejor en Categoria',
    ];

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    public function scopeForYear(Builder $query, int $year): Builder
    {
        return $query->where('year', $year);
    }

    public function scopeCity(Builder $query, string $city): Builder
    {
        return $query->where('ranking_type', 'city')->where('ranking_scope', $city);
    }

    public function scopeState(Builder $query, string $state): Builder
    {
        return $query->where('ranking_type', 'state')->where('ranking_scope', $state);
    }

    public function scopeNational(Builder $query): Builder
    {
        return $query->where('ranking_type', 'national');
    }

    public function getBadgeColorAttribute(): string
    {
        return match(true) {
            $this->position === 1 => '#FFD700', // Gold
            $this->position === 2 => '#C0C0C0', // Silver
            $this->position === 3 => '#CD7F32', // Bronze
            $this->position <= 10 => '#3B82F6', // Blue
            default => '#6B7280', // Gray
        };
    }

    public function getBadgeIconAttribute(): string
    {
        return match(true) {
            $this->position <= 3 => 'trophy',
            $this->position <= 10 => 'star',
            default => 'badge-check',
        };
    }

    public static function generateBadgeName(string $type, string $scope, int $position, int $year): string
    {
        return match($type) {
            'city' => "Top {$position} {$scope} {$year}",
            'state' => "Top {$position} {$scope} {$year}",
            'national' => "Top {$position} USA {$year}",
            'category' => "Mejor {$scope} {$year}",
            default => "Ranking #{$position} {$year}",
        };
    }

    public static function calculateCityRankings(string $city, int $year, int $limit = 10): void
    {
        $restaurants = Restaurant::approved()
            ->where('city', $city)
            ->whereHas('score')
            ->with('score')
            ->get()
            ->sortByDesc(fn($r) => $r->score->total_score)
            ->take($limit);

        $position = 1;
        foreach ($restaurants as $restaurant) {
            self::updateOrCreate(
                [
                    'restaurant_id' => $restaurant->id,
                    'year' => $year,
                    'ranking_type' => 'city',
                    'ranking_scope' => $city,
                ],
                [
                    'position' => $position,
                    'final_score' => $restaurant->score->total_score,
                    'score_breakdown' => $restaurant->score->toArray(),
                    'badge_name' => self::generateBadgeName('city', $city, $position, $year),
                ]
            );
            $position++;
        }
    }

    public static function calculateStateRankings(string $stateCode, int $year, int $limit = 10): void
    {
        $restaurants = Restaurant::approved()
            ->whereHas('state', fn($q) => $q->where('code', $stateCode))
            ->whereHas('score')
            ->with('score')
            ->get()
            ->sortByDesc(fn($r) => $r->score->total_score)
            ->take($limit);

        $position = 1;
        foreach ($restaurants as $restaurant) {
            self::updateOrCreate(
                [
                    'restaurant_id' => $restaurant->id,
                    'year' => $year,
                    'ranking_type' => 'state',
                    'ranking_scope' => $stateCode,
                ],
                [
                    'position' => $position,
                    'final_score' => $restaurant->score->total_score,
                    'score_breakdown' => $restaurant->score->toArray(),
                    'badge_name' => self::generateBadgeName('state', $stateCode, $position, $year),
                ]
            );
            $position++;
        }
    }

    public static function calculateNationalRankings(int $year, int $limit = 100): void
    {
        $restaurants = Restaurant::approved()
            ->whereHas('score')
            ->with('score')
            ->get()
            ->sortByDesc(fn($r) => $r->score->total_score)
            ->take($limit);

        $position = 1;
        foreach ($restaurants as $restaurant) {
            self::updateOrCreate(
                [
                    'restaurant_id' => $restaurant->id,
                    'year' => $year,
                    'ranking_type' => 'national',
                    'ranking_scope' => 'usa',
                ],
                [
                    'position' => $position,
                    'final_score' => $restaurant->score->total_score,
                    'score_breakdown' => $restaurant->score->toArray(),
                    'badge_name' => self::generateBadgeName('national', 'USA', $position, $year),
                ]
            );
            $position++;
        }
    }
}
