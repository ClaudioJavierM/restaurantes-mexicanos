<?php

namespace App\Models\Traits;

use App\Services\CountryContext;
use Illuminate\Database\Eloquent\Builder;

trait HasCountry
{
    /**
     * Scope to filter by current country context
     */
    public function scopeForCurrentCountry(Builder $query): Builder
    {
        return $query->where('country', CountryContext::getCountry());
    }

    /**
     * Scope to filter by specific country
     */
    public function scopeForCountry(Builder $query, string $country): Builder
    {
        return $query->where('country', $country);
    }

    /**
     * Scope for USA
     */
    public function scopeForUSA(Builder $query): Builder
    {
        return $query->where('country', 'US');
    }

    /**
     * Scope for Mexico
     */
    public function scopeForMexico(Builder $query): Builder
    {
        return $query->where('country', 'MX');
    }

    /**
     * Check if belongs to USA
     */
    public function isUSA(): bool
    {
        return $this->country === 'US';
    }

    /**
     * Check if belongs to Mexico
     */
    public function isMexico(): bool
    {
        return $this->country === 'MX';
    }
}
