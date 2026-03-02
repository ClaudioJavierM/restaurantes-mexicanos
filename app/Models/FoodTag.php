<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class FoodTag extends Model
{
    protected $fillable = ['name', 'slug', 'icon', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function restaurants(): BelongsToMany
    {
        return $this->belongsToMany(Restaurant::class, 'restaurant_food_tag');
    }
}
