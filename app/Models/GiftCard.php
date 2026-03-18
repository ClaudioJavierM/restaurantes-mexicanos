<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GiftCard extends Model
{
    protected $fillable = [
        'restaurant_id', 'code', 'initial_amount', 'balance',
        'purchaser_name', 'purchaser_email',
        'recipient_name', 'recipient_email', 'message',
        'status', 'expires_at',
    ];

    protected $casts = [
        'initial_amount' => 'decimal:2',
        'balance'        => 'decimal:2',
        'expires_at'     => 'date',
    ];

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    public static function generateCode(): string
    {
        do {
            $raw = strtoupper(substr(str_shuffle('ABCDEFGHJKLMNPQRSTUVWXYZ23456789'), 0, 16));
            $code = implode('-', str_split($raw, 4));
        } while (self::where('code', $code)->exists());

        return $code;
    }

    public function isValid(): bool
    {
        return $this->status === 'active'
            && $this->balance > 0
            && ($this->expires_at === null || $this->expires_at->isFuture());
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active')->where('balance', '>', 0);
    }
}
