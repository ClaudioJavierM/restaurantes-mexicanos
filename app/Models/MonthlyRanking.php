<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MonthlyRanking extends Model
{
    protected $fillable = [
        'restaurant_id',
        'year',
        'month',
        'ranking_type',
        'ranking_scope',
        'position',
        'total_votes',
        'favorite_votes',
        'monthly_score',
        'cumulative_score',
        'badge_name',
        'is_winner',
    ];

    protected $casts = [
        'year' => 'integer',
        'month' => 'integer',
        'position' => 'integer',
        'total_votes' => 'integer',
        'favorite_votes' => 'integer',
        'monthly_score' => 'decimal:2',
        'cumulative_score' => 'decimal:2',
        'is_winner' => 'boolean',
    ];

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function scopeForMonth($query, int $year, int $month)
    {
        return $query->where('year', $year)->where('month', $month);
    }

    public function scopeWinners($query)
    {
        return $query->where('is_winner', true);
    }

    public function scopeByCity($query, string $city)
    {
        return $query->where('ranking_type', 'city')->where('ranking_scope', $city);
    }

    public function scopeByState($query, string $stateCode)
    {
        return $query->where('ranking_type', 'state')->where('ranking_scope', $stateCode);
    }

    public function scopeNational($query)
    {
        return $query->where('ranking_type', 'national');
    }

    public function getMonthNameAttribute(): string
    {
        return \Carbon\Carbon::create()->month($this->month)->translatedFormat('F');
    }
}
