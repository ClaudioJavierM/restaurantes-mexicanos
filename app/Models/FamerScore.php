<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FamerScore extends Model
{
    protected $fillable = [
        'restaurant_id',
        'overall_score',
        'letter_grade',
        'profile_completeness_score',
        'online_presence_score',
        'customer_engagement_score',
        'menu_offerings_score',
        'mexican_authenticity_score',
        'digital_readiness_score',
        'profile_breakdown',
        'presence_breakdown',
        'engagement_breakdown',
        'menu_breakdown',
        'authenticity_breakdown',
        'digital_breakdown',
        'recommendations',
        'area_rank',
        'area_total',
        'category_rank',
        'category_total',
        'area_average',
        'category_average',
        'calculated_at',
        'expires_at',
        'version',
    ];

    protected $casts = [
        'profile_breakdown' => 'array',
        'presence_breakdown' => 'array',
        'engagement_breakdown' => 'array',
        'menu_breakdown' => 'array',
        'authenticity_breakdown' => 'array',
        'digital_breakdown' => 'array',
        'recommendations' => 'array',
        'calculated_at' => 'datetime',
        'expires_at' => 'datetime',
        'area_average' => 'decimal:2',
        'category_average' => 'decimal:2',
        'overall_score' => 'integer',
        'profile_completeness_score' => 'integer',
        'online_presence_score' => 'integer',
        'customer_engagement_score' => 'integer',
        'menu_offerings_score' => 'integer',
        'mexican_authenticity_score' => 'integer',
        'digital_readiness_score' => 'integer',
    ];

    // Grade color mapping
    protected const GRADE_COLORS = [
        'A+' => 'emerald',
        'A' => 'emerald',
        'A-' => 'emerald',
        'B+' => 'blue',
        'B' => 'blue',
        'B-' => 'blue',
        'C+' => 'yellow',
        'C' => 'yellow',
        'C-' => 'yellow',
        'D+' => 'orange',
        'D' => 'orange',
        'D-' => 'orange',
        'F' => 'red',
    ];

    // Category weights for reference
    public const CATEGORY_WEIGHTS = [
        'profile_completeness' => 20,
        'online_presence' => 25,
        'customer_engagement' => 20,
        'menu_offerings' => 15,
        'mexican_authenticity' => 10,
        'digital_readiness' => 10,
    ];

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function requests(): HasMany
    {
        return $this->hasMany(FamerScoreRequest::class);
    }

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function isValid(): bool
    {
        return !$this->isExpired();
    }

    public function getGradeColorAttribute(): string
    {
        return self::GRADE_COLORS[$this->letter_grade] ?? 'gray';
    }

    public function getGradeColorClassAttribute(): string
    {
        return match ($this->grade_color) {
            'emerald' => 'text-emerald-600 bg-emerald-100',
            'blue' => 'text-blue-600 bg-blue-100',
            'yellow' => 'text-yellow-600 bg-yellow-100',
            'orange' => 'text-orange-600 bg-orange-100',
            'red' => 'text-red-600 bg-red-100',
            default => 'text-gray-600 bg-gray-100',
        };
    }

    public function getPercentileAttribute(): int
    {
        if (!$this->area_rank || !$this->area_total || $this->area_total === 0) {
            return 0;
        }
        return (int) round((1 - (($this->area_rank - 1) / $this->area_total)) * 100);
    }

    public function getCategoryScoresAttribute(): array
    {
        return [
            [
                'name' => 'Profile Completeness',
                'key' => 'profile_completeness',
                'score' => $this->profile_completeness_score,
                'weight' => self::CATEGORY_WEIGHTS['profile_completeness'],
                'breakdown' => $this->profile_breakdown,
                'icon' => 'user-circle',
            ],
            [
                'name' => 'Online Presence',
                'key' => 'online_presence',
                'score' => $this->online_presence_score,
                'weight' => self::CATEGORY_WEIGHTS['online_presence'],
                'breakdown' => $this->presence_breakdown,
                'icon' => 'globe-alt',
            ],
            [
                'name' => 'Customer Engagement',
                'key' => 'customer_engagement',
                'score' => $this->customer_engagement_score,
                'weight' => self::CATEGORY_WEIGHTS['customer_engagement'],
                'breakdown' => $this->engagement_breakdown,
                'icon' => 'chat-bubble-left-right',
            ],
            [
                'name' => 'Menu & Offerings',
                'key' => 'menu_offerings',
                'score' => $this->menu_offerings_score,
                'weight' => self::CATEGORY_WEIGHTS['menu_offerings'],
                'breakdown' => $this->menu_breakdown,
                'icon' => 'book-open',
            ],
            [
                'name' => 'Mexican Authenticity',
                'key' => 'mexican_authenticity',
                'score' => $this->mexican_authenticity_score,
                'weight' => self::CATEGORY_WEIGHTS['mexican_authenticity'],
                'breakdown' => $this->authenticity_breakdown,
                'icon' => 'fire',
            ],
            [
                'name' => 'Digital Readiness',
                'key' => 'digital_readiness',
                'score' => $this->digital_readiness_score,
                'weight' => self::CATEGORY_WEIGHTS['digital_readiness'],
                'breakdown' => $this->digital_breakdown,
                'icon' => 'device-phone-mobile',
            ],
        ];
    }

    public function getTopRecommendationsAttribute(): array
    {
        return array_slice($this->recommendations ?? [], 0, 3);
    }

    public function getScoreDescriptionAttribute(): string
    {
        return match (true) {
            $this->overall_score >= 90 => 'Excellent! Your restaurant has outstanding online presence.',
            $this->overall_score >= 80 => 'Great! Your restaurant is well optimized.',
            $this->overall_score >= 70 => 'Good, but there\'s room for improvement.',
            $this->overall_score >= 60 => 'Fair. Several areas need attention.',
            default => 'Needs work. Follow our recommendations to improve.',
        };
    }
}
