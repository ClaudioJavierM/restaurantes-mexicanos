<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserCollectionItem extends Model
{
    protected $fillable = [
        'collection_id',
        'restaurant_id',
        'note',
        'sort_order',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    // ─── Relationships ──────────────────────────────────────────────────────────

    public function collection(): BelongsTo
    {
        return $this->belongsTo(UserCollection::class, 'collection_id');
    }

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }
}
