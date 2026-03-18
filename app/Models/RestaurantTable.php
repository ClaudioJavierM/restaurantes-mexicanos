<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class RestaurantTable extends Model
{
    protected $fillable = [
        'restaurant_id', 'name', 'table_code', 'capacity', 'is_active',
    ];

    protected $casts = ['is_active' => 'boolean'];

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function tableOrders()
    {
        return $this->hasMany(TableOrder::class, 'table_id');
    }

    public function pendingOrders()
    {
        return $this->hasMany(TableOrder::class, 'table_id')
            ->whereIn('status', ['pending', 'confirmed', 'preparing']);
    }

    public static function generateCode(): string
    {
        do {
            $code = strtoupper(Str::random(8));
        } while (self::where('table_code', $code)->exists());

        return $code;
    }

    public function getQrUrlAttribute(): string
    {
        return url('/mesa/' . $this->restaurant->slug . '/' . $this->table_code);
    }
}
