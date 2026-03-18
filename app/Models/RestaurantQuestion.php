<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RestaurantQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'restaurant_id',
        'user_id',
        'author_name',
        'author_email',
        'question',
        'answer',
        'answered_by',
        'answered_at',
        'is_public',
        'is_approved',
        'helpful_count',
    ];

    protected $casts = [
        'is_public' => 'boolean',
        'is_approved' => 'boolean',
        'answered_at' => 'datetime',
    ];

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function answeredBy()
    {
        return $this->belongsTo(User::class, 'answered_by');
    }

    public function scopePublic($query)
    {
        return $query->where('is_public', true)->where('is_approved', true);
    }

    public function scopeAnswered($query)
    {
        return $query->whereNotNull('answer');
    }

    public function scopeUnanswered($query)
    {
        return $query->whereNull('answer');
    }

    public function getDisplayNameAttribute(): string
    {
        if ($this->user) {
            return $this->user->name;
        }
        return $this->author_name ?? 'Visitante';
    }
}
