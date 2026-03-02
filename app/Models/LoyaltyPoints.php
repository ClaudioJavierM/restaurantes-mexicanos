<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LoyaltyPoints extends Model
{
    protected $fillable = [
        'user_id',
        'points',
        'level',
        'total_check_ins',
        'total_reviews',
        'total_referrals',
    ];

    const LEVELS = [
        'bronce' => ['min' => 0, 'discount' => 5, 'color' => '#CD7F32'],
        'plata' => ['min' => 500, 'discount' => 10, 'color' => '#C0C0C0'],
        'oro' => ['min' => 1500, 'discount' => 15, 'color' => '#FFD700'],
        'platino' => ['min' => 5000, 'discount' => 20, 'color' => '#E5E4E2'],
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(PointTransaction::class, 'user_id', 'user_id');
    }

    public static function getOrCreate(int $userId): self
    {
        return self::firstOrCreate(
            ['user_id' => $userId],
            ['points' => 0, 'level' => 'bronce']
        );
    }

    public static function addPoints(int $userId, int $points, string $type, string $description, ?Model $pointable = null): void
    {
        $loyalty = self::getOrCreate($userId);
        $loyalty->increment('points', $points);

        if ($type === 'check_in') {
            $loyalty->increment('total_check_ins');
        } elseif ($type === 'review') {
            $loyalty->increment('total_reviews');
        } elseif ($type === 'referral') {
            $loyalty->increment('total_referrals');
        }

        // Create transaction record
        PointTransaction::create([
            'user_id' => $userId,
            'points' => $points,
            'type' => $type,
            'description' => $description,
            'pointable_type' => $pointable ? get_class($pointable) : null,
            'pointable_id' => $pointable ? $pointable->id : null,
        ]);

        // Check for level upgrade
        $loyalty->updateLevel();
    }

    public function updateLevel(): void
    {
        $newLevel = 'bronce';
        foreach (self::LEVELS as $level => $config) {
            if ($this->points >= $config['min']) {
                $newLevel = $level;
            }
        }

        if ($newLevel !== $this->level) {
            $this->update(['level' => $newLevel]);
        }
    }

    public function getLevelInfo(): array
    {
        return self::LEVELS[$this->level] ?? self::LEVELS['bronce'];
    }

    public function getNextLevelInfo(): ?array
    {
        $levels = array_keys(self::LEVELS);
        $currentIndex = array_search($this->level, $levels);
        
        if ($currentIndex < count($levels) - 1) {
            $nextLevel = $levels[$currentIndex + 1];
            return [
                'level' => $nextLevel,
                'points_needed' => self::LEVELS[$nextLevel]['min'] - $this->points,
                ...(self::LEVELS[$nextLevel])
            ];
        }
        
        return null;
    }
}
