<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class BlogPost extends Model
{
    protected $fillable = [
        'title',
        'title_en',
        'slug',
        'excerpt',
        'excerpt_en',
        'content',
        'content_en',
        'cover_image',
        'author',
        'category',
        'tags',
        'is_published',
        'published_at',
        'seo_title',
        'seo_description',
        'view_count',
        'featured',
    ];

    protected $casts = [
        'tags'         => 'array',
        'is_published' => 'boolean',
        'featured'     => 'boolean',
        'published_at' => 'datetime',
    ];

    // ── Route Model Binding ──────────────────────────────────────────────────────

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    // ── Scopes ───────────────────────────────────────────────────────────────────

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true)
                     ->where(function ($q) {
                         $q->whereNull('published_at')
                           ->orWhere('published_at', '<=', now());
                     });
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('featured', true);
    }

    public function scopeByCategory(Builder $query, string $category): Builder
    {
        return $query->where('category', $category);
    }

    // ── Accessors ────────────────────────────────────────────────────────────────

    /**
     * Estimated reading time in minutes (words / 200 wpm).
     */
    public function getReadTimeAttribute(): int
    {
        $wordCount = str_word_count(strip_tags($this->content));
        return max(1, (int) ceil($wordCount / 200));
    }

    // ── Helpers ──────────────────────────────────────────────────────────────────

    public static function categories(): array
    {
        return [
            'historia' => 'Historia',
            'recetas'  => 'Recetas',
            'cultura'  => 'Cultura',
            'guias'    => 'Guías',
            'chefs'    => 'Chefs',
        ];
    }
}
