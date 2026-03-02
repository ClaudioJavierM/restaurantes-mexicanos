<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MexicanRegion extends Model
{
    protected $fillable = ['name', 'slug', 'icon', 'description', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function restaurants(): HasMany
    {
        return $this->hasMany(Restaurant::class);
    }
}
