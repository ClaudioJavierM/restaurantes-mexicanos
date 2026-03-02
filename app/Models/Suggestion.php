<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Suggestion extends Model
{
    protected $fillable = [
        'user_id',
        'submitter_name',
        'submitter_email',
        'submitter_phone',
        'restaurant_name',
        'restaurant_address',
        'restaurant_city',
        'restaurant_state',
        'restaurant_zip_code',
        'restaurant_phone',
        'restaurant_website',
        'category_id',
        'description',
        'notes',
        'status',
        'admin_notes',
        // Validation fields
        'trust_score',
        'validation_status',
        'validation_data',
        'google_place_id',
        'google_verified',
        'google_rating',
        'google_reviews_count',
        'yelp_id',
        'yelp_verified',
        'yelp_rating',
        'yelp_reviews_count',
        'spam_score',
        'spam_risk_level',
        'spam_flags',
        'is_spam',
        'is_potential_duplicate',
        'duplicate_check_data',
        'website_verified',
        'phone_verified',
        'verified_at',
    ];

    protected $casts = [
        'validation_data' => 'array',
        'duplicate_check_data' => 'array',
        'spam_flags' => 'array',
        'google_verified' => 'boolean',
        'yelp_verified' => 'boolean',
        'is_spam' => 'boolean',
        'is_potential_duplicate' => 'boolean',
        'website_verified' => 'boolean',
        'phone_verified' => 'boolean',
        'verified_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    // Scope para sugerencias pendientes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
}
