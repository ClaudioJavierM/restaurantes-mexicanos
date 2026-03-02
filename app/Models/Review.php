<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    protected $fillable = [
        'restaurant_id',
        'name',
        'email',
        'user_id',
        'guest_name',
        'guest_email',
        'rating',
        'service_rating',
        'food_rating',
        'ambiance_rating',
        'title',
        'comment',
        'owner_response',
        'owner_response_by',
        'owner_response_at',
        'helpful_count',
        'not_helpful_count',
        'status',
        'is_active',
        'approved_at',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'rating' => 'integer',
        'service_rating' => 'integer',
        'food_rating' => 'integer',
        'ambiance_rating' => 'integer',
        'helpful_count' => 'integer',
        'not_helpful_count' => 'integer',
        'approved_at' => 'datetime',
        'owner_response_at' => 'datetime',
    ];

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function photos()
    {
        return $this->hasMany(ReviewPhoto::class);
    }

    public function votes()
    {
        return $this->hasMany(ReviewVote::class);
    }

    public function reports()
    {
        return $this->hasMany(ReviewReport::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function ownerResponseBy()
    {
        return $this->belongsTo(User::class, 'owner_response_by');
    }

    // Accessors
    public function getReviewerNameAttribute(): string
    {
        return $this->user ? $this->user->name : ($this->guest_name ?? 'Anónimo');
    }

    // Scope para reviews aprobados
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved')->where('is_active', true);
    }

    // Scope para ordenar por más recientes
    public function scopeRecent($query)
    {
        return $query->latest();
    }

    // Scope para ordenar por más útiles
    public function scopeMostHelpful($query)
    {
        return $query->orderByDesc('helpful_count');
    }

    // Check if user has voted
    public function hasUserVoted(?User $user = null): bool
    {
        if (!$user) {
            $user = auth()->user();
        }

        if (!$user) {
            return false;
        }

        return $this->votes()->where('user_id', $user->id)->exists();
    }

    // Get user's vote
    public function getUserVote(?User $user = null): ?ReviewVote
    {
        if (!$user) {
            $user = auth()->user();
        }

        if (!$user) {
            return null;
        }

        return $this->votes()->where('user_id', $user->id)->first();
    }

    // Toggle vote
    public function toggleVote(bool $isHelpful, ?User $user = null)
    {
        if (!$user) {
            $user = auth()->user();
        }

        if (!$user) {
            return false;
        }

        $existingVote = $this->votes()->where('user_id', $user->id)->first();

        if ($existingVote) {
            // If same vote, remove it
            if ($existingVote->is_helpful == $isHelpful) {
                if ($isHelpful) {
                    $this->decrement('helpful_count');
                } else {
                    $this->decrement('not_helpful_count');
                }
                $existingVote->delete();
                return null;
            } else {
                // If different vote, update it
                if ($isHelpful) {
                    $this->increment('helpful_count');
                    $this->decrement('not_helpful_count');
                } else {
                    $this->increment('not_helpful_count');
                    $this->decrement('helpful_count');
                }
                $existingVote->update(['is_helpful' => $isHelpful]);
                return $isHelpful;
            }
        } else {
            // Create new vote
            ReviewVote::create([
                'review_id' => $this->id,
                'user_id' => $user->id,
                'is_helpful' => $isHelpful,
                'ip_address' => request()->ip(),
            ]);

            if ($isHelpful) {
                $this->increment('helpful_count');
            } else {
                $this->increment('not_helpful_count');
            }

            return $isHelpful;
        }
    }
}
