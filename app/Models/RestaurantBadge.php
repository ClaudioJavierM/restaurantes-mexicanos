<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RestaurantBadge extends Model
{
    protected $fillable = [
        'restaurant_id',
        'badge_type',
        'badge_scope',
        'scope_name',
        'year',
        'month',
        'position',
        'title',
        'icon',
        'color',
        'certificate_path',
        'is_active',
    ];

    protected $casts = [
        'year' => 'integer',
        'month' => 'integer',
        'position' => 'integer',
        'is_active' => 'boolean',
    ];

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeMonthly($query)
    {
        return $query->whereNotNull('month');
    }

    public function scopeAnnual($query)
    {
        return $query->whereNull('month');
    }

    public function getIconEmojiAttribute(): string
    {
        return match($this->badge_type) {
            'monthly_winner' => '🏆',
            'top_3' => '🥇',
            'top_10' => '⭐',
            'top_100' => '🌟',
            'annual_winner' => '👑',
            'peoples_choice' => '❤️',
            default => '🏅',
        };
    }

    public function getColorClassAttribute(): string
    {
        return match($this->color) {
            'gold' => 'from-yellow-400 to-amber-500',
            'silver' => 'from-gray-300 to-gray-400',
            'bronze' => 'from-amber-600 to-orange-700',
            default => 'from-blue-400 to-blue-500',
        };
    }

    // Crear badge para ganador mensual
    public static function createMonthlyWinner(Restaurant $restaurant, string $scope, string $scopeName, int $year, int $month): self
    {
        $monthName = \Carbon\Carbon::create()->month($month)->translatedFormat('F');
        
        return self::create([
            'restaurant_id' => $restaurant->id,
            'badge_type' => 'monthly_winner',
            'badge_scope' => $scope,
            'scope_name' => $scopeName,
            'year' => $year,
            'month' => $month,
            'position' => 1,
            'title' => "Restaurante del Mes - {$scopeName} - {$monthName} {$year}",
            'icon' => '🏆',
            'color' => 'gold',
        ]);
    }
}
