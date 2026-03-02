<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class WidgetToken extends Model
{
    protected $fillable = [
        'restaurant_id',
        'token',
        'allowed_domain',
        'settings',
        'is_active',
        'views',
    ];

    protected $casts = [
        'settings' => 'array',
        'is_active' => 'boolean',
    ];

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    public static function generateToken(): string
    {
        do {
            $token = 'fmr_' . Str::random(32);
        } while (self::where('token', $token)->exists());
        
        return $token;
    }

    public static function createForRestaurant(int $restaurantId, ?string $domain = null, array $settings = []): self
    {
        $defaultSettings = [
            'theme' => 'dark',
            'show_reviews' => true,
            'show_rating' => true,
            'show_photos' => true,
            'show_hours' => true,
            'show_menu_button' => true,
            'show_reservation_button' => true,
            'max_reviews' => 3,
        ];

        return self::create([
            'restaurant_id' => $restaurantId,
            'token' => self::generateToken(),
            'allowed_domain' => $domain,
            'settings' => array_merge($defaultSettings, $settings),
            'is_active' => true,
        ]);
    }

    public function incrementViews(): void
    {
        $this->increment('views');
    }

    public function getEmbedCode(): string
    {
        $baseUrl = config('app.url');
        return '<script src="' . $baseUrl . '/widget/' . $this->token . '.js" async></script>
<div id="famer-widget" data-token="' . $this->token . '"></div>';
    }
}
