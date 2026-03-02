<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExternalReview extends Model
{
    protected $fillable = [
        'restaurant_id',
        'platform',
        'platform_review_id',
        'platform_url',
        'reviewer_name',
        'reviewer_avatar',
        'reviewer_profile_url',
        'reviewer_review_count',
        'rating',
        'comment',
        'photos',
        'reviewed_at',
        'owner_response',
        'owner_response_at',
        'response_synced',
        'response_synced_at',
        'status',
        'is_featured',
        'helpful_count',
        'sentiment',
        'keywords',
        'last_synced_at',
    ];

    protected $casts = [
        'photos' => 'array',
        'keywords' => 'array',
        'reviewed_at' => 'datetime',
        'owner_response_at' => 'datetime',
        'response_synced_at' => 'datetime',
        'last_synced_at' => 'datetime',
        'response_synced' => 'boolean',
        'is_featured' => 'boolean',
    ];

    // Platforms
    const PLATFORM_GOOGLE = 'google';
    const PLATFORM_YELP = 'yelp';
    const PLATFORM_TRIPADVISOR = 'tripadvisor';
    const PLATFORM_FACEBOOK = 'facebook';

    // Statuses
    const STATUS_PENDING = 'pending';
    const STATUS_RESPONDED = 'responded';
    const STATUS_FLAGGED = 'flagged';
    const STATUS_HIDDEN = 'hidden';

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeNeedsResponse($query)
    {
        return $query->whereNull('owner_response');
    }

    public function scopeByPlatform($query, string $platform)
    {
        return $query->where('platform', $platform);
    }

    public function scopeNegative($query)
    {
        return $query->where('rating', '<=', 2);
    }

    public function scopePositive($query)
    {
        return $query->where('rating', '>=', 4);
    }

    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('reviewed_at', '>=', now()->subDays($days));
    }

    // Helpers
    public function getPlatformLabel(): string
    {
        return match($this->platform) {
            self::PLATFORM_GOOGLE => 'Google',
            self::PLATFORM_YELP => 'Yelp',
            self::PLATFORM_TRIPADVISOR => 'TripAdvisor',
            self::PLATFORM_FACEBOOK => 'Facebook',
            default => ucfirst($this->platform),
        };
    }

    public function getPlatformIcon(): string
    {
        return match($this->platform) {
            self::PLATFORM_GOOGLE => 'fab fa-google',
            self::PLATFORM_YELP => 'fab fa-yelp',
            self::PLATFORM_TRIPADVISOR => 'fab fa-tripadvisor',
            self::PLATFORM_FACEBOOK => 'fab fa-facebook',
            default => 'fas fa-star',
        };
    }

    public function getPlatformColor(): string
    {
        return match($this->platform) {
            self::PLATFORM_GOOGLE => '#4285F4',
            self::PLATFORM_YELP => '#D32323',
            self::PLATFORM_TRIPADVISOR => '#00AA6C',
            self::PLATFORM_FACEBOOK => '#1877F2',
            default => '#666666',
        };
    }

    public function canRespondViaApi(): bool
    {
        return in_array($this->platform, [
            self::PLATFORM_GOOGLE,
            self::PLATFORM_FACEBOOK,
        ]);
    }

    public function isNegative(): bool
    {
        return $this->rating <= 2;
    }

    public function isPositive(): bool
    {
        return $this->rating >= 4;
    }

    public function needsResponse(): bool
    {
        return is_null($this->owner_response);
    }

    public function getTimeAgo(): string
    {
        return $this->reviewed_at?->diffForHumans() ?? 'Unknown';
    }
}
