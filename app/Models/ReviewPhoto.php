<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ReviewPhoto extends Model
{
    use HasFactory;

    protected $fillable = [
        'review_id',
        'photo_path',
        'thumbnail_path',
        'display_order',
    ];

    // Relationships
    public function review()
    {
        return $this->belongsTo(Review::class);
    }

    // Get photo URL
    public function getPhotoUrl(): string
    {
        return Storage::disk('public')->url($this->photo_path);
    }

    // Get thumbnail URL
    public function getThumbnailUrl(): ?string
    {
        if (!$this->thumbnail_path) {
            return $this->getPhotoUrl();
        }

        return Storage::disk('public')->url($this->thumbnail_path);
    }

    // Delete photo files
    public function deleteFiles(): bool
    {
        $deleted = true;

        if ($this->photo_path && Storage::disk('public')->exists($this->photo_path)) {
            $deleted = Storage::disk('public')->delete($this->photo_path);
        }

        if ($this->thumbnail_path && Storage::disk('public')->exists($this->thumbnail_path)) {
            $deleted = $deleted && Storage::disk('public')->delete($this->thumbnail_path);
        }

        return $deleted;
    }

    // Boot method
    protected static function boot()
    {
        parent::boot();

        // Delete files when photo is deleted
        static::deleting(function ($photo) {
            $photo->deleteFiles();
        });
    }
}
