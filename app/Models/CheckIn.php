<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * New unified check-in model using the `checkins` table (with visited_at + note).
 * Distinct from App\Models\CheckIn (legacy `check_ins` table with lat/lng).
 */
class Checkin extends Model
{
    protected $table = 'checkins';

    protected $fillable = [
        'user_id',
        'restaurant_id',
        'visited_at',
        'note',
    ];

    protected $casts = [
        'visited_at' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }
}
