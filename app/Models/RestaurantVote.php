<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RestaurantVote extends Model
{
    protected $fillable = [
        'restaurant_id',
        'user_id',
        'voter_ip',
        'voter_fingerprint',
        'voter_email',
        'year',
        'month',
        'vote_type',
        'comment',
        'is_verified',
    ];

    protected $casts = [
        'is_verified' => 'boolean',
        'year' => 'integer',
        'month' => 'integer',
    ];

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Scope para votos de un mes específico
    public function scopeForMonth($query, int $year, int $month)
    {
        return $query->where('year', $year)->where('month', $month);
    }

    // Scope para votos del año actual
    public function scopeCurrentYear($query)
    {
        return $query->where('year', now()->year);
    }

    // Verificar si un usuario ya votó
    public static function hasVoted(int $restaurantId, ?int $userId, ?string $fingerprint, int $year, int $month): bool
    {
        $query = self::where('restaurant_id', $restaurantId)
            ->where('year', $year)
            ->where('month', $month);

        if ($userId) {
            return $query->where('user_id', $userId)->exists();
        }

        if ($fingerprint) {
            return $query->where('voter_fingerprint', $fingerprint)->exists();
        }

        return false;
    }
}
