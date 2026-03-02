<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MfImportsPromoCode extends Model
{
    protected $table = 'mf_imports_promo_codes';
    
    protected $fillable = [
        'mf_customer_email',
        'mf_customer_name',
        'mf_order_id',
        'mf_order_total',
        'promo_code',
        'stripe_promotion_code_id',
        'tier',
        'used_famer_discount_on_order',
        'is_redeemed',
        'redeemed_at',
        'redeemed_by_restaurant_id',
        'expires_at',
        'is_active',
    ];
    
    protected $casts = [
        'mf_order_total' => 'decimal:2',
        'used_famer_discount_on_order' => 'boolean',
        'is_redeemed' => 'boolean',
        'redeemed_at' => 'datetime',
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
    ];
    
    public function redeemedByRestaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class, 'redeemed_by_restaurant_id');
    }
    
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
                     ->where('is_redeemed', false)
                     ->where(function($q) {
                         $q->whereNull('expires_at')
                           ->orWhere('expires_at', '>', now());
                     });
    }
    
    public function scopeForCustomer($query, string $email)
    {
        return $query->where('mf_customer_email', $email);
    }
    
    public function isValid(): bool
    {
        if (!$this->is_active || $this->is_redeemed) {
            return false;
        }
        
        if ($this->expires_at && $this->expires_at->isPast()) {
            return false;
        }
        
        return true;
    }
    
    public function getMonthsFreeAttribute(): int
    {
        return $this->tier === '6_months' ? 6 : 3;
    }
    
    public function getValueDescriptionAttribute(): string
    {
        return $this->months_free . ' meses GRATIS de suscripción FAMER';
    }
}
