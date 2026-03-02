<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReviewResponseTemplate extends Model
{
    protected $fillable = [
        'restaurant_id',
        'user_id',
        'name',
        'category',
        'template',
        'is_global',
        'usage_count',
    ];

    protected $casts = [
        'is_global' => 'boolean',
    ];

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Fill template with actual values
     */
    public function fillPlaceholders(array $data): string
    {
        $response = $this->template;
        
        foreach ($data as $key => $value) {
            $response = str_replace('{' . $key . '}', $value, $response);
        }
        
        return $response;
    }

    /**
     * Get available placeholders
     */
    public static function getPlaceholders(): array
    {
        return [
            '{customer_name}' => 'Nombre del cliente',
            '{restaurant_name}' => 'Nombre del restaurante',
            '{owner_name}' => 'Nombre del dueño',
            '{rating}' => 'Calificación (1-5)',
        ];
    }

    /**
     * Increment usage counter
     */
    public function recordUsage(): void
    {
        $this->increment('usage_count');
    }
}
