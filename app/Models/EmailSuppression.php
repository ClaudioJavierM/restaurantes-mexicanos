<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailSuppression extends Model
{
    protected $fillable = [
        'email',
        'reason',
        'source',
        'suppressed_at',
    ];

    protected $casts = [
        'suppressed_at' => 'datetime',
    ];

    /**
     * Check if an email address is suppressed.
     */
    public static function isSuppressed(string $email): bool
    {
        return static::where('email', strtolower(trim($email)))->exists();
    }

    /**
     * Suppress an email address, or update reason/source if already suppressed.
     */
    public static function suppress(string $email, string $reason, string $source = 'system'): self
    {
        return static::updateOrCreate(
            ['email' => strtolower(trim($email))],
            [
                'reason'        => $reason,
                'source'        => $source,
                'suppressed_at' => now(),
            ]
        );
    }
}
