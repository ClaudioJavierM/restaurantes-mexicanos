<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
        'trust_score',
        'is_verified',
        'trust_flags',
        'visit_date',
        'visit_type',
        'edit_count',
        'last_edited_at',
        'flagged_suspicious',
        'status',
        'is_active',
        'approved_at',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'is_active'          => 'boolean',
        'is_verified'        => 'boolean',
        'flagged_suspicious' => 'boolean',
        'rating'             => 'integer',
        'service_rating'     => 'integer',
        'food_rating'        => 'integer',
        'ambiance_rating'    => 'integer',
        'helpful_count'      => 'integer',
        'not_helpful_count'  => 'integer',
        'trust_score'        => 'integer',
        'edit_count'         => 'integer',
        'trust_flags'        => 'array',
        'approved_at'        => 'datetime',
        'owner_response_at'  => 'datetime',
        'last_edited_at'     => 'datetime',
        'visit_date'         => 'date',
    ];

    // ─── Relationships ────────────────────────────────────────────────────────

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function photos(): HasMany
    {
        return $this->hasMany(ReviewPhoto::class);
    }

    public function votes(): HasMany
    {
        return $this->hasMany(ReviewVote::class);
    }

    public function reports(): HasMany
    {
        return $this->hasMany(ReviewReport::class);
    }

    public function editHistory(): HasMany
    {
        return $this->hasMany(ReviewEditHistory::class);
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function ownerResponseBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_response_by');
    }

    // ─── Accessors ───────────────────────────────────────────────────────────

    public function getReviewerNameAttribute(): string
    {
        return $this->user ? $this->user->name : ($this->guest_name ?? 'Anónimo');
    }

    public function getTrustLevelAttribute(): string
    {
        if ($this->trust_score >= 80) return 'high';
        if ($this->trust_score >= 50) return 'medium';
        return 'low';
    }

    public function getTrustBadgeAttribute(): ?string
    {
        if ($this->is_verified) return 'verified';
        if ($this->trust_score >= 80) return 'trusted';
        if ($this->flagged_suspicious) return 'suspicious';
        return null;
    }

    // ─── Scopes ──────────────────────────────────────────────────────────────

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved')->where('is_active', true);
    }

    public function scopeRecent($query)
    {
        return $query->latest();
    }

    public function scopeMostHelpful($query)
    {
        return $query->orderByDesc('helpful_count');
    }

    public function scopeTrusted($query)
    {
        return $query->where('trust_score', '>=', 50)->where('flagged_suspicious', false);
    }

    public function scopeSuspicious($query)
    {
        return $query->where('flagged_suspicious', true);
    }

    // ─── Edit Tracking ───────────────────────────────────────────────────────

    public function recordEdit(string $oldComment, string $newComment, ?string $oldTitle, ?string $newTitle, int $oldRating, int $newRating, ?string $reason = null): void
    {
        $this->editHistory()->create([
            'edited_by'   => auth()->id(),
            'old_comment' => $oldComment,
            'new_comment' => $newComment,
            'old_title'   => $oldTitle,
            'new_title'   => $newTitle,
            'old_rating'  => $oldRating,
            'new_rating'  => $newRating,
            'edit_reason' => $reason,
            'ip_address'  => request()->ip(),
        ]);

        $this->increment('edit_count');
        $this->update(['last_edited_at' => now()]);
    }

    // ─── Vote Helpers ─────────────────────────────────────────────────────────

    public function hasUserVoted(?User $user = null): bool
    {
        $user ??= auth()->user();
        if (!$user) return false;
        return $this->votes()->where('user_id', $user->id)->exists();
    }

    public function getUserVote(?User $user = null): ?ReviewVote
    {
        $user ??= auth()->user();
        if (!$user) return null;
        return $this->votes()->where('user_id', $user->id)->first();
    }

    public function toggleVote(bool $isHelpful, ?User $user = null)
    {
        $user ??= auth()->user();
        if (!$user) return false;

        $existingVote = $this->votes()->where('user_id', $user->id)->first();

        if ($existingVote) {
            if ($existingVote->is_helpful == $isHelpful) {
                $isHelpful ? $this->decrement('helpful_count') : $this->decrement('not_helpful_count');
                $existingVote->delete();
                return null;
            } else {
                $isHelpful ? $this->increment('helpful_count') : $this->increment('not_helpful_count');
                $isHelpful ? $this->decrement('not_helpful_count') : $this->decrement('helpful_count');
                $existingVote->update(['is_helpful' => $isHelpful]);
                return $isHelpful;
            }
        }

        ReviewVote::create([
            'review_id'  => $this->id,
            'user_id'    => $user->id,
            'is_helpful' => $isHelpful,
            'ip_address' => request()->ip(),
        ]);

        $isHelpful ? $this->increment('helpful_count') : $this->increment('not_helpful_count');

        return $isHelpful;
    }
}
