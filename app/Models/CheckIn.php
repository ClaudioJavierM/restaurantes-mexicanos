<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Unified check-in model using the `checkins` table (with visited_at + note).
 */
class CheckIn extends Model
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
