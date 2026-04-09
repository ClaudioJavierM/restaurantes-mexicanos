<?php

namespace App\Filament\Pages;

use App\Models\BlogPost;
use App\Models\Restaurant;
use App\Models\Review;
use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;

class SeoAnalytics extends Page
{
    protected static bool $isLazy = true;

    protected static ?string $navigationIcon = 'heroicon-o-magnifying-glass';
    protected static ?string $navigationLabel = 'SEO Analytics';
    protected static ?string $navigationGroup = 'Marketing & SEO';
    protected static ?int $navigationSort = 1;
    protected static string $view = 'filament.pages.seo-analytics';

    public int $totalPages = 0;
    public int $withAiDescription = 0;
    public int $withAiDescriptionEn = 0;
    public int $withPhotos = 0;
    public int $withRating = 0;
    public int $withCoordinates = 0;
    public int $withAddress = 0;
    public int $withHours = 0;
    public int $blogPostsPublished = 0;
    public int $blogPostsDraft = 0;
    public int $totalReviews = 0;
    public float $avgRating = 0.0;
    public array $topStates = [];
    public float $seoScore = 0.0;
    public array $coverageByState = [];

    public function mount(): void
    {
        try {
            $this->totalPages = Restaurant::where('status', 'approved')->count();
        } catch (\Throwable $e) {
            $this->totalPages = 0;
        }

        try {
            $this->withAiDescription = Restaurant::where('status', 'approved')
                ->whereNotNull('ai_description')
                ->count();
        } catch (\Throwable $e) {
            $this->withAiDescription = 0;
        }

        try {
            $this->withAiDescriptionEn = Restaurant::where('status', 'approved')
                ->whereNotNull('ai_description_en')
                ->count();
        } catch (\Throwable $e) {
            $this->withAiDescriptionEn = 0;
        }

        try {
            $this->withPhotos = Restaurant::where('status', 'approved')
                ->whereNotNull('yelp_photos')
                ->where('yelp_photos', '!=', '[]')
                ->where('yelp_photos', '!=', '')
                ->count();
        } catch (\Throwable $e) {
            $this->withPhotos = 0;
        }

        try {
            $this->withRating = Restaurant::where('status', 'approved')
                ->where('average_rating', '>', 0)
                ->count();
        } catch (\Throwable $e) {
            $this->withRating = 0;
        }

        try {
            $this->withCoordinates = Restaurant::where('status', 'approved')
                ->whereNotNull('latitude')
                ->whereNotNull('longitude')
                ->count();
        } catch (\Throwable $e) {
            $this->withCoordinates = 0;
        }

        try {
            $this->withAddress = Restaurant::where('status', 'approved')
                ->whereNotNull('address')
                ->where('address', '!=', '')
                ->count();
        } catch (\Throwable $e) {
            $this->withAddress = 0;
        }

        try {
            $this->withHours = Restaurant::where('status', 'approved')
                ->whereNotNull('hours')
                ->count();
        } catch (\Throwable $e) {
            $this->withHours = 0;
        }

        try {
            $this->blogPostsPublished = BlogPost::where('status', 'published')->count();
        } catch (\Throwable $e) {
            $this->blogPostsPublished = 0;
        }

        try {
            $this->blogPostsDraft = BlogPost::where('status', 'draft')->count();
        } catch (\Throwable $e) {
            $this->blogPostsDraft = 0;
        }

        try {
            $this->totalReviews = Review::where('status', 'approved')->count();
        } catch (\Throwable $e) {
            $this->totalReviews = 0;
        }

        try {
            $this->avgRating = (float) Restaurant::where('status', 'approved')
                ->where('average_rating', '>', 0)
                ->avg('average_rating') ?? 0.0;
        } catch (\Throwable $e) {
            $this->avgRating = 0.0;
        }

        try {
            $this->topStates = DB::table('restaurants')
                ->join('states', 'restaurants.state_id', '=', 'states.id')
                ->where('restaurants.status', 'approved')
                ->selectRaw('states.name as state_name, states.code as state_code, COUNT(*) as count')
                ->groupBy('states.id', 'states.name', 'states.code')
                ->orderByDesc('count')
                ->limit(10)
                ->get()
                ->toArray();
        } catch (\Throwable $e) {
            $this->topStates = [];
        }

        // SEO Score: weighted calculation
        if ($this->totalPages > 0) {
            $this->seoScore = round(
                ($this->withAiDescription / $this->totalPages * 25) +
                ($this->withPhotos / $this->totalPages * 20) +
                ($this->withRating / $this->totalPages * 20) +
                ($this->withCoordinates / $this->totalPages * 15) +
                ($this->withAddress / $this->totalPages * 10) +
                ($this->withHours / $this->totalPages * 10),
                1
            );
        }

        try {
            $this->coverageByState = DB::table('restaurants')
                ->join('states', 'restaurants.state_id', '=', 'states.id')
                ->where('restaurants.status', 'approved')
                ->selectRaw("
                    states.name as state_name,
                    states.code as state_code,
                    COUNT(*) as count,
                    SUM(CASE WHEN restaurants.ai_description IS NOT NULL THEN 1 ELSE 0 END) as with_description,
                    SUM(CASE WHEN restaurants.yelp_photos IS NOT NULL AND restaurants.yelp_photos != '[]' THEN 1 ELSE 0 END) as with_photos
                ")
                ->groupBy('states.id', 'states.name', 'states.code')
                ->orderByDesc('count')
                ->limit(20)
                ->get()
                ->map(function ($row) {
                    $row->coverage_pct = $row->count > 0
                        ? round(($row->with_description / $row->count) * 100)
                        : 0;
                    return $row;
                })
                ->toArray();
        } catch (\Throwable $e) {
            $this->coverageByState = [];
        }
    }
}
