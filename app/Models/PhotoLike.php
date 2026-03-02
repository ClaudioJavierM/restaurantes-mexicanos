<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PhotoLike extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_photo_id',
        'user_id',
        'ip_address',
    ];

    // Relationships
    public function photo()
    {
        return $this->belongsTo(UserPhoto::class, 'user_photo_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
