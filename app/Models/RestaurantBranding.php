<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Cache;

class RestaurantBranding extends Model
{
    protected $table = 'restaurant_branding';

    protected $fillable = [
        'restaurant_id',
        'app_name',
        'app_short_name',
        'app_description',
        'primary_color',
        'secondary_color',
        'accent_color',
        'text_color',
        'background_color',
        'logo_url',
        'icon_192_url',
        'icon_512_url',
        'splash_image_url',
        'banner_image_url',
        'display_mode',
        'orientation',
        'theme_color',
        'background_splash_color',
        'custom_domain',
        'custom_domain_verified',
        'hide_famer_branding',
        'custom_splash_screen',
        'powered_by_text',
        'facebook_url',
        'instagram_url',
        'tiktok_url',
        'twitter_url',
        'google_analytics_id',
        'facebook_pixel_id',
        'is_active',
    ];

    protected $casts = [
        'custom_domain_verified' => 'boolean',
        'hide_famer_branding' => 'boolean',
        'custom_splash_screen' => 'boolean',
        'is_active' => 'boolean',
    ];

    protected $attributes = [
        'primary_color' => '#dc2626',
        'secondary_color' => '#991b1b',
        'accent_color' => '#f59e0b',
        'text_color' => '#1f2937',
        'background_color' => '#ffffff',
        'display_mode' => 'standalone',
        'orientation' => 'portrait',
        'is_active' => true,
    ];

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    /**
     * Get or create branding for a restaurant
     */
    public static function getForRestaurant(int $restaurantId): self
    {
        return Cache::remember("branding_{$restaurantId}", 3600, function () use ($restaurantId) {
            return self::firstOrCreate(
                ['restaurant_id' => $restaurantId],
                ['app_name' => null]
            );
        });
    }

    /**
     * Clear cache when updated
     */
    protected static function booted(): void
    {
        static::saved(function ($branding) {
            Cache::forget("branding_{$branding->restaurant_id}");
        });

        static::deleted(function ($branding) {
            Cache::forget("branding_{$branding->restaurant_id}");
        });
    }

    /**
     * Generate the manifest.json for this restaurant's PWA
     */
    public function generateManifest(): array
    {
        $restaurant = $this->restaurant;

        return [
            'name' => $this->app_name ?? $restaurant->name,
            'short_name' => $this->app_short_name ?? substr($restaurant->name, 0, 12),
            'description' => $this->app_description ?? "Ordena en línea de {$restaurant->name}",
            'start_url' => "/app/{$restaurant->slug}?source=pwa",
            'display' => $this->display_mode,
            'background_color' => $this->background_color,
            'theme_color' => $this->theme_color ?? $this->primary_color,
            'orientation' => $this->orientation === 'any' ? 'any' : "{$this->orientation}-primary",
            'scope' => "/app/{$restaurant->slug}",
            'lang' => 'es',
            'categories' => ['food', 'restaurants'],
            'icons' => $this->getIcons(),
            'shortcuts' => [
                [
                    'name' => 'Ver Menú',
                    'short_name' => 'Menú',
                    'url' => "/app/{$restaurant->slug}/menu",
                    'icons' => [['src' => '/images/icons/menu-96x96.png', 'sizes' => '96x96']]
                ],
                [
                    'name' => 'Mis Pedidos',
                    'short_name' => 'Pedidos',
                    'url' => "/app/{$restaurant->slug}/orders",
                    'icons' => [['src' => '/images/icons/orders-96x96.png', 'sizes' => '96x96']]
                ],
            ],
        ];
    }

    /**
     * Get PWA icons array
     */
    protected function getIcons(): array
    {
        $icons = [];
        $sizes = [72, 96, 128, 144, 152, 192, 384, 512];

        foreach ($sizes as $size) {
            $iconUrl = match ($size) {
                192 => $this->icon_192_url,
                512 => $this->icon_512_url,
                default => null,
            };

            $icons[] = [
                'src' => $iconUrl ?? "/images/icons/icon-{$size}x{$size}.png",
                'sizes' => "{$size}x{$size}",
                'type' => 'image/png',
                'purpose' => 'any maskable',
            ];
        }

        return $icons;
    }

    /**
     * Get CSS variables for theming
     */
    public function getCssVariables(): string
    {
        return "
            :root {
                --color-primary: {$this->primary_color};
                --color-secondary: {$this->secondary_color};
                --color-accent: {$this->accent_color};
                --color-text: {$this->text_color};
                --color-background: {$this->background_color};
            }
        ";
    }

    /**
     * Check if restaurant has Elite branding features
     */
    public function hasEliteFeatures(): bool
    {
        return $this->hide_famer_branding || $this->custom_domain;
    }
}
