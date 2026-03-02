<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Spatie\Translatable\HasTranslations;
use App\Models\Traits\HasCountry;

class Restaurant extends Model implements HasMedia
{
    use HasSlug, InteractsWithMedia, SoftDeletes, HasTranslations, HasCountry;

    // Translatable attributes
    public $translatable = ['description'];

    protected $fillable = [
        'user_id',
        'state_id',
        'category_id',
        'business_type',
        'mexican_region_id',
        'name',
        'slug',
        'description',
        'email',
        'phone',
        'website',
        'address',
        'city',
        'zip_code',
        'latitude',
        'longitude',
        'hours',
        'status',
        'is_featured',
        'is_active',
        'average_rating',
        'total_reviews',
        // Advanced filters
        'price_range',
        'spice_level',
        'mexican_region',
        'dietary_options',
        'atmosphere',
        'special_features',
        // Authenticity badges
        'chef_certified',
        'traditional_recipes',
        'imported_ingredients',
        // Business features
        'accepts_reservations',
        'reservation_type',
        'reservation_platform',
        'reservation_external_url',
        'reservation_settings',
        'reservation_hours',
        'reservation_capacity_per_slot',
        'reservation_tables_count',
        'reservation_notification_email',
        'reservation_notification_phone',
        'reservation_notify_whatsapp',
        'reservation_notify_sms',
        'reservation_notify_email',
        'reservation_send_confirmation',
        'reservation_send_reminder',
        'reservation_reminder_hours',
        'online_ordering',
        'has_cafe_de_olla',
        'has_fresh_tortillas',
        'has_handmade_tortillas',
        'has_aguas_frescas',
        'has_homemade_salsa',
        'has_homemade_mole',
        'has_charcoal_grill',
        'has_comal',
        'has_birria',
        'has_carnitas',
        'has_pozole_menudo',
        'has_barbacoa',
        'has_tamales',
        'has_pan_dulce',
        'has_churros',
        'has_mezcal_tequila',
        'has_micheladas',
        'has_mexican_candy',
        'has_imported_products',
        'order_url',
        'current_wait_time',
        'wait_time_updated_at',
        // Google Places integration
        'google_place_id',
        'google_verified',
        'google_maps_url',
        'google_rating',
        'google_reviews_count',
        'last_google_verification',
        // Yelp integration
        'yelp_id',
        'yelp_rating',
        'yelp_reviews_count',
        'yelp_url',
        'yelp_last_sync',
        'yelp_photos',
        'yelp_hours',
        'yelp_attributes',
        'menu_url',
        // TripAdvisor integration
        'tripadvisor_id',
        'tripadvisor_url',
        'tripadvisor_rating',
        'tripadvisor_reviews_count',
        'tripadvisor_ranking',
        'tripadvisor_price_level',
        'tripadvisor_last_sync',
        // Foursquare integration
        'foursquare_id',
        'foursquare_rating',
        'foursquare_checkins',
        'foursquare_tips_count',
        'foursquare_price',
        'foursquare_last_sync',
        // Facebook integration
        'facebook_page_id',
        'facebook_url',
        'facebook_rating',
        'facebook_review_count',
        'facebook_hours',
        'facebook_enriched_at',
        // Import metadata
        'import_source',
        'imported_at',
        // Ownership & Claim
        'is_claimed',
        'claimed_at',
        'claim_token',
        'verification_method',
        // Subscription
        'subscription_tier',
        'stripe_customer_id',
        'stripe_subscription_id',
        'subscription_started_at',
        'subscription_expires_at',
        'subscription_status',
        // Premium Features
        'premium_analytics',
        'premium_seo',
        'premium_featured',
        'premium_coupons',
        'premium_email_marketing',
        // Analytics
        'profile_views',
        'phone_clicks',
        'website_clicks',
        'direction_clicks',
        // Owner info
        'owner_name',
        'owner_email',
        'owner_phone',
        // Images
        'image',
        'logo',
        // FAMER Email Sequence
        'famer_email_1_sent_at',
        'famer_email_2_sent_at',
        'famer_email_3_sent_at',
    ];

    protected $casts = [
        'hours' => 'array',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
        'average_rating' => 'decimal:2',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'dietary_options' => 'array',
        'atmosphere' => 'array',
        'special_features' => 'array',
        'chef_certified' => 'boolean',
        'traditional_recipes' => 'boolean',
        'imported_ingredients' => 'boolean',
        'accepts_reservations' => 'boolean',
        'online_ordering' => 'boolean',
        'has_cafe_de_olla' => 'boolean',
        'has_fresh_tortillas' => 'boolean',
        'has_handmade_tortillas' => 'boolean',
        'has_aguas_frescas' => 'boolean',
        'has_homemade_salsa' => 'boolean',
        'has_homemade_mole' => 'boolean',
        'has_charcoal_grill' => 'boolean',
        'has_comal' => 'boolean',
        'has_birria' => 'boolean',
        'has_carnitas' => 'boolean',
        'has_pozole_menudo' => 'boolean',
        'has_barbacoa' => 'boolean',
        'has_tamales' => 'boolean',
        'has_pan_dulce' => 'boolean',
        'has_churros' => 'boolean',
        'has_mezcal_tequila' => 'boolean',
        'has_micheladas' => 'boolean',
        'has_mexican_candy' => 'boolean',
        'has_imported_products' => 'boolean',
        'wait_time_updated_at' => 'datetime',
        // Yelp integration
        'yelp_rating' => 'decimal:1',
        'yelp_last_sync' => 'datetime',
        'yelp_photos' => 'array',
        'yelp_hours' => 'array',
        'yelp_attributes' => 'array',
        // TripAdvisor integration
        'tripadvisor_rating' => 'decimal:1',
        'tripadvisor_last_sync' => 'datetime',
        // Foursquare integration
        'foursquare_rating' => 'decimal:1',
        'foursquare_last_sync' => 'datetime',
        'imported_at' => 'datetime',
        // Facebook integration
        'facebook_rating' => 'decimal:2',
        'facebook_hours' => 'array',
        'facebook_enriched_at' => 'datetime',
        // Subscription fields
        'is_claimed' => 'boolean',
        'claimed_at' => 'datetime',
        'famer_email_1_sent_at' => 'datetime',
        'famer_email_2_sent_at' => 'datetime',
        'famer_email_3_sent_at' => 'datetime',
        'subscription_started_at' => 'datetime',
        'subscription_expires_at' => 'datetime',
        'premium_analytics' => 'boolean',
        'premium_seo' => 'boolean',
        'premium_featured' => 'boolean',
        'premium_coupons' => 'boolean',
        'premium_email_marketing' => 'boolean',
        // Reservation settings
        'reservation_settings' => 'array',
        'reservation_hours' => 'array',
        'reservation_notify_whatsapp' => 'boolean',
        'reservation_notify_sms' => 'boolean',
        'reservation_notify_email' => 'boolean',
        'reservation_send_confirmation' => 'boolean',
        'reservation_send_reminder' => 'boolean',
    ];

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('images')
            ->useFallbackUrl('/images/restaurant-placeholder.jpg');

        $this->addMediaCollection('menu')
            ->useFallbackUrl('/images/menu-placeholder.jpg');

        $this->addMediaCollection('logo')
            ->singleFile()
            ->useFallbackUrl('/images/logo-placeholder.jpg');
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, "user_id");
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function famerScore(): HasOne
    {
        return $this->hasOne(FamerScore::class);
    }

    public function mexicanRegionRelation(): BelongsTo
    {
        return $this->belongsTo(MexicanRegion::class, 'mexican_region_id');
    }

    public function foodTags(): BelongsToMany
    {
        return $this->belongsToMany(FoodTag::class, 'restaurant_food_tag');
    }

    public function features(): BelongsToMany
    {
        return $this->belongsToMany(Feature::class, 'restaurant_feature');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function votes(): HasMany
    {
        return $this->hasMany(RestaurantVote::class);
    }

    public function approvedReviews(): HasMany
    {
        return $this->reviews()->where('status', 'approved')->where('is_active', true);
    }

    public function coupons(): HasMany
    {
        return $this->hasMany(Coupon::class);
    }

    public function activeCoupons(): HasMany
    {
        return $this->coupons()->active()->valid()->hasUsagesLeft();
    }

    public function rankings(): HasMany
    {
        return $this->hasMany(RestaurantRanking::class);
    }

    public function menuCategories(): HasMany
    {
        return $this->hasMany(MenuCategory::class);
    }

    public function menuItems(): HasManyThrough
    {
        return $this->hasManyThrough(
            MenuItem::class,
            MenuCategory::class,
            'restaurant_id', // Foreign key on menu_categories
            'menu_category_id', // Foreign key on menu_items
            'id', // Local key on restaurants
            'id' // Local key on menu_categories
        );
    }

    public function availableMenuItems(): HasManyThrough
    {
        return $this->menuItems()->where('menu_items.is_available', true)->orderBy('menu_items.sort_order')->orderBy('menu_items.name');
    }

    public function popularMenuItems(): HasManyThrough
    {
        return $this->menuItems()->where('menu_items.is_popular', true)->where('menu_items.is_available', true);
    }

    public function menuUploads(): HasMany
    {
        return $this->hasMany(MenuUpload::class);
    }

    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class);
    }

    public function userPhotos(): HasMany
    {
        return $this->hasMany(UserPhoto::class);
    }

    public function approvedPhotos(): HasMany
    {
        return $this->userPhotos()->where('status', 'approved');
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    public function upcomingReservations(): HasMany
    {
        return $this->reservations()
            ->where('reservation_date', '>=', now()->toDateString())
            ->whereIn('status', ['pending', 'confirmed'])
            ->orderBy('reservation_date')
            ->orderBy('reservation_time');
    }

    public function favoritedByUsers()
    {
        return $this->belongsToMany(User::class, 'favorites')->withTimestamps();
    }

    public function favorites_count()
    {
        return $this->favorites()->count();
    }

    // Team members
    public function teamMembers(): HasMany
    {
        return $this->hasMany(RestaurantTeamMember::class);
    }

    public function activeTeamMembers(): HasMany
    {
        return $this->teamMembers()->where('status', 'active');
    }

    public function teamOwners(): HasMany
    {
        return $this->activeTeamMembers()->where('role', 'owner');
    }

    public function teamManagers(): HasMany
    {
        return $this->activeTeamMembers()->where('role', 'manager');
    }

    public function teamStaff(): HasMany
    {
        return $this->activeTeamMembers()->where('role', 'staff');
    }

    public function teamUsers()
    {
        return $this->belongsToMany(User::class, 'restaurant_team')
            ->withPivot(['role', 'permissions', 'status', 'invited_at', 'accepted_at'])
            ->wherePivot('status', 'active');
    }

    // Customer & Email Marketing relationships
    public function customers(): HasMany
    {
        return $this->hasMany(RestaurantCustomer::class);
    }

    public function subscribedCustomers(): HasMany
    {
        return $this->hasMany(RestaurantCustomer::class)->where("email_subscribed", true);
    }

    public function ownerCampaigns(): HasMany
    {
        return $this->hasMany(OwnerCampaign::class);
    }

    public function pendingInvitations(): HasMany
    {
        return $this->teamMembers()->where('status', 'pending');
    }

    public function isTeamMember(User $user): bool
    {
        return $this->activeTeamMembers()->where('user_id', $user->id)->exists();
    }

    public function getTeamMember(User $user): ?RestaurantTeamMember
    {
        return $this->teamMembers()->where('user_id', $user->id)->first();
    }

    // Scope para restaurantes aprobados
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved')->where('is_active', true);
    }

    // Scope para restaurantes destacados
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    // Advanced filter scopes
    public function scopePriceRange($query, $range)
    {
        if ($range) {
            return $query->where('price_range', $range);
        }
        return $query;
    }

    public function scopeSpiceLevel($query, $minLevel, $maxLevel = null)
    {
        if ($minLevel) {
            $query->where('spice_level', '>=', $minLevel);
        }
        if ($maxLevel) {
            $query->where('spice_level', '<=', $maxLevel);
        }
        return $query;
    }

    public function scopeMexicanRegion($query, $region)
    {
        if ($region) {
            return $query->where('mexican_region', $region);
        }
        return $query;
    }

    public function scopeWithDietaryOption($query, $option)
    {
        if ($option) {
            return $query->whereJsonContains('dietary_options', $option);
        }
        return $query;
    }

    public function scopeWithAtmosphere($query, $atmosphere)
    {
        if ($atmosphere) {
            return $query->whereJsonContains('atmosphere', $atmosphere);
        }
        return $query;
    }

    public function scopeWithFeature($query, $feature)
    {
        if ($feature) {
            return $query->whereJsonContains('special_features', $feature);
        }
        return $query;
    }

    public function scopeAuthentic($query)
    {
        return $query->where(function($q) {
            $q->where('chef_certified', true)
              ->orWhere('traditional_recipes', true)
              ->orWhere('imported_ingredients', true);
        });
    }

    // Helper methods
    public function getSpiceLevelIconsAttribute(): string
    {
        return str_repeat('🌶️', $this->spice_level);
    }

    public function getAuthenticityBadgesAttribute(): array
    {
        $badges = [];
        if ($this->chef_certified) {
            $badges[] = ['name' => 'Chef Certificado', 'icon' => '👨‍🍳', 'color' => 'blue'];
        }
        if ($this->traditional_recipes) {
            $badges[] = ['name' => 'Recetas Tradicionales', 'icon' => '📖', 'color' => 'green'];
        }
        if ($this->imported_ingredients) {
            $badges[] = ['name' => 'Ingredientes de México', 'icon' => '🇲🇽', 'color' => 'red'];
        }
        return $badges;
    }

    public function getPriceRangeSymbolAttribute(): string
    {
        return $this->price_range ?? '$$';
    }

    /**
     * Get combined total reviews from all platforms
     */
    public function getCombinedReviewsCountAttribute(): int
    {
        return (int)($this->google_reviews_count ?? 0)
             + (int)($this->yelp_reviews_count ?? 0)
             + (int)($this->tripadvisor_reviews_count ?? 0)
             + (int)($this->foursquare_tips_count ?? 0);
    }

    public function getEstimatedPriceAttribute(): string
    {
        $prices = [
            '$' => '$5-15 por persona',
            '$$' => '$15-30 por persona',
            '$$$' => '$30-50 por persona',
            '$$$$' => '$50+ por persona',
        ];
        return $prices[$this->price_range] ?? $prices['$$'];
    }

    // Constants for filters
    public static function getMexicanRegions(): array
    {
        return [
            'Oaxaca' => 'Oaxaca',
            'Jalisco' => 'Jalisco',
            'Michoacán' => 'Michoacán',
            'Yucatán' => 'Yucatán',
            'Puebla' => 'Puebla',
            'Veracruz' => 'Veracruz',
            'Sinaloa' => 'Sinaloa',
            'Chihuahua' => 'Chihuahua',
            'Sonora' => 'Sonora',
            'Baja California' => 'Baja California',
            'Ciudad de México' => 'Ciudad de México',
            'Guanajuato' => 'Guanajuato',
            'General' => 'Comida Mexicana General',
        ];
    }

    public static function getDietaryOptions(): array
    {
        return [
            'vegetarian' => 'Vegetariano',
            'vegan' => 'Vegano',
            'gluten_free' => 'Sin Gluten',
            'halal' => 'Halal',
            'keto' => 'Keto',
            'dairy_free' => 'Sin Lácteos',
        ];
    }

    public static function getAtmosphereOptions(): array
    {
        return [
            'family_friendly' => 'Familiar',
            'romantic' => 'Romántico',
            'casual' => 'Casual',
            'formal' => 'Formal',
            'outdoor_seating' => 'Terraza/Patio',
            'bar_atmosphere' => 'Ambiente de Bar',
        ];
    }

    public static function getSpecialFeatures(): array
    {
        return [
            'live_music' => 'Música en Vivo',
            'mariachi' => 'Mariachi',
            'outdoor_patio' => 'Patio Exterior',
            'full_bar' => 'Bar Completo',
            'takeout' => 'Para Llevar',
            'delivery' => 'Delivery',
            'parking' => 'Estacionamiento',
            'catering' => 'Servicio de Catering',
            'private_events' => 'Eventos Privados',
            'wifi' => 'WiFi Gratis',
        ];
    }

    // Reservation helper methods
    public function usesInternalReservations(): bool
    {
        return $this->reservation_type === 'restaurante_famoso';
    }

    public function usesExternalReservations(): bool
    {
        return $this->reservation_type === 'external';
    }

    public function hasReservationsEnabled(): bool
    {
        return $this->accepts_reservations && $this->reservation_type !== 'none';
    }

    public function getReservationSetting(string $key, $default = null)
    {
        return data_get($this->reservation_settings, $key, $default);
    }

    public function getReservationHoursForDay(string $day): ?array
    {
        return data_get($this->reservation_hours, strtolower($day));
    }

    public function isOpenForReservations(string $day): bool
    {
        $hours = $this->getReservationHoursForDay($day);
        return $hours && !($hours['closed'] ?? false);
    }

    public function getDefaultReservationSettings(): array
    {
        return [
            'min_party_size' => 1,
            'max_party_size' => 20,
            'default_duration_minutes' => 90,
            'advance_booking_days' => 30,
            'same_day_cutoff_hours' => 2,
            'time_slot_interval' => 30,
            'require_confirmation' => true,
            'auto_confirm' => false,
            'confirmation_deadline_hours' => 24,
            'no_show_policy' => 'none',
            'deposit_amount' => null,
        ];
    }

    public static function getReservationPlatforms(): array
    {
        return [
            'opentable' => 'OpenTable',
            'yelp' => 'Yelp Reservations',
            'resy' => 'Resy',
            'google' => 'Google Reserve',
            'tablein' => 'Tablein',
            'other' => 'Otro',
        ];
    }

    // Update restaurant rating based on approved reviews
    public function updateRating(): void
    {
        $stats = $this->reviews()
            ->where('status', 'approved')
            ->selectRaw('AVG(rating) as average, COUNT(*) as total')
            ->first();

        $this->update([
            'average_rating' => $stats->average ? round($stats->average, 2) : 0,
            'total_reviews' => $stats->total ?? 0,
        ]);
    }

    /**
     * Get the weighted average rating combining Google, Yelp, and internal reviews.
     * Each source is weighted by its number of reviews.
     */
    public function getWeightedRating(): float
    {
        $googleReviews = $this->google_reviews_count ?? 0;
        $yelpReviews = $this->yelp_reviews_count ?? 0;
        $internalReviewCount = $this->reviews()->where('is_approved', true)->count();

        $googleRating = $this->google_rating ?? 0;
        $yelpRating = $this->yelp_rating ?? 0;
        $internalRating = $this->average_rating ?? 0;

        $totalWeightedScore = 0;
        $totalWeight = 0;

        if ($googleRating > 0 && $googleReviews > 0) {
            $totalWeightedScore += $googleRating * $googleReviews;
            $totalWeight += $googleReviews;
        }
        if ($yelpRating > 0 && $yelpReviews > 0) {
            $totalWeightedScore += $yelpRating * $yelpReviews;
            $totalWeight += $yelpReviews;
        }
        if ($internalRating > 0 && $internalReviewCount > 0) {
            $totalWeightedScore += $internalRating * $internalReviewCount;
            $totalWeight += $internalReviewCount;
        }

        if ($totalWeight > 0) {
            return round($totalWeightedScore / $totalWeight, 1);
        }

        return $googleRating ?: ($yelpRating ?: 0);
    }

    /**
     * Get the combined review count from all sources.
     */
    public function getCombinedReviewCount(): int
    {
        $googleReviews = $this->google_reviews_count ?? 0;
        $yelpReviews = $this->yelp_reviews_count ?? 0;
        $internalReviewCount = $this->reviews()->where('is_approved', true)->count();

        return $googleReviews + $yelpReviews + $internalReviewCount;
    }
}
