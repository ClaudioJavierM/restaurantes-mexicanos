<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class UserPhoto extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'restaurant_id',
        'user_id',
        'caption',
        'photo_path',
        'thumbnail_path',
        'status',
        'approved_at',
        'approved_by',
        'rejection_reason',
        'width',
        'height',
        'file_size',
        'mime_type',
        'views_count',
        'likes_count',
        'reports_count',
        'photo_type',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
    ];

    // Photo statuses
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';
    const STATUS_FLAGGED = 'flagged'; // Auto-flagged by community reports

    // Photo types
    const TYPE_FOOD = 'food';
    const TYPE_INTERIOR = 'interior';
    const TYPE_EXTERIOR = 'exterior';
    const TYPE_MENU = 'menu';
    const TYPE_DRINK = 'drink';
    const TYPE_OTHER = 'other';

    // Relationships
    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function likes()
    {
        return $this->hasMany(PhotoLike::class, 'user_photo_id');
    }

    public function reports()
    {
        return $this->hasMany(PhotoReport::class, 'user_photo_id');
    }

    // Scopes
    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeRejected($query)
    {
        return $query->where('status', self::STATUS_REJECTED);
    }

    public function scopeFlagged($query)
    {
        return $query->where('status', self::STATUS_FLAGGED);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('photo_type', $type);
    }

    // Approve photo
    public function approve(?int $approvedBy = null)
    {
        $this->update([
            'status' => self::STATUS_APPROVED,
            'approved_at' => now(),
            'approved_by' => $approvedBy ?? auth()->id(),
        ]);
    }

    // Reject photo
    public function reject(string $reason, ?int $rejectedBy = null)
    {
        $this->update([
            'status' => self::STATUS_REJECTED,
            'rejection_reason' => $reason,
            'approved_by' => $rejectedBy ?? auth()->id(),
        ]);
    }

    // Increment views
    public function incrementViews()
    {
        $this->increment('views_count');
    }

    // Check if user liked this photo
    public function isLikedBy(?User $user = null): bool
    {
        if (!$user) {
            $user = auth()->user();
        }

        if (!$user) {
            return false;
        }

        return $this->likes()->where('user_id', $user->id)->exists();
    }

    // Toggle like
    public function toggleLike(?User $user = null)
    {
        if (!$user) {
            $user = auth()->user();
        }

        if (!$user) {
            return false;
        }

        $like = $this->likes()->where('user_id', $user->id)->first();

        if ($like) {
            $like->delete();
            $this->decrement('likes_count');
            return false; // unliked
        } else {
            PhotoLike::create([
                'user_photo_id' => $this->id,
                'user_id' => $user->id,
                'ip_address' => request()->ip(),
            ]);
            $this->increment('likes_count');
            return true; // liked
        }
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

    // Get file size in human readable format
    public function getFileSizeFormatted(): string
    {
        if (!$this->file_size) {
            return 'N/A';
        }

        $units = ['B', 'KB', 'MB', 'GB'];
        $size = $this->file_size;
        $unit = 0;

        while ($size >= 1024 && $unit < count($units) - 1) {
            $size /= 1024;
            $unit++;
        }

        return round($size, 2) . ' ' . $units[$unit];
    }

    // Get photo type label
    public function getPhotoTypeLabel(): string
    {
        return match($this->photo_type) {
            self::TYPE_FOOD => 'Comida',
            self::TYPE_INTERIOR => 'Interior',
            self::TYPE_EXTERIOR => 'Exterior',
            self::TYPE_MENU => 'Menú',
            self::TYPE_DRINK => 'Bebida',
            default => 'Otro',
        };
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
