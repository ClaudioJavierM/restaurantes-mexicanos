<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReviewEditHistory extends Model
{
    protected $table = 'review_edit_history';

    protected $fillable = [
        'review_id',
        'edited_by',
        'old_comment',
        'new_comment',
        'old_title',
        'new_title',
        'old_rating',
        'new_rating',
        'edit_reason',
        'ip_address',
    ];

    protected $casts = [
        'old_rating' => 'integer',
        'new_rating' => 'integer',
    ];

    public function review(): BelongsTo
    {
        return $this->belongsTo(Review::class);
    }

    public function editedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'edited_by');
    }
}
