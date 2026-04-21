<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class RestaurantPrinter extends Model
{
    protected $fillable = ['restaurant_id', 'name', 'token', 'is_active', 'last_poll_at', 'last_job_at'];

    protected $casts = [
        'is_active'    => 'boolean',
        'last_poll_at' => 'datetime',
        'last_job_at'  => 'datetime',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($p) {
            if (!$p->token) {
                $p->token = Str::random(32);
            }
        });
    }

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function printJobs(): HasMany
    {
        return $this->hasMany(PrintJob::class, 'restaurant_id', 'restaurant_id');
    }

    public function isOnline(): bool
    {
        return $this->last_poll_at && $this->last_poll_at->diffInSeconds(now()) < 30;
    }

    public function hasPendingJob(): bool
    {
        return PrintJob::where('restaurant_id', $this->restaurant_id)
            ->where('status', 'pending')
            ->exists();
    }

    public function getCloudprntUrl(): string
    {
        return url("/cloudprnt/{$this->token}");
    }
}
