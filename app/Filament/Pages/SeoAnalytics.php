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

    // ── GSC (Search Console) ──────────────────────────────────────────
    public bool  $hasGscData       = false;
    public int   $totalClicks      = 0;
    public int   $totalImpressions = 0;
    public float $avgCtr           = 0.0;
    public float $avgPosition      = 0.0;
    public array $topKeywords      = [];
    public array $topPages         = [];
    public array $opportunities    = [];
    public array $byDevice         = [];

    // ── Catalog health ───────────────────────────────────────────────
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
        $this->loadGscData();
        $this->loadCatalogHealth();
    }

    // ────────────────────────────────────────────────────────────────
    // GSC
    // ────────────────────────────────────────────────────────────────
    private function loadGscData(): void
    {
        try {
            $count = DB::table('gsc_performance')->count();
            $this->hasGscData = $count > 0;
        } catch (\Throwable) {
            return;
        }

        if (! $this->hasGscData) {
            return;
        }

        // No date filter — table is always truncated and replaced on each sync
        try {
            $totals = DB::table('gsc_performance')
                ->whereRaw('1=1') // no date filter — table is truncated/replaced on each sync
                ->selectRaw('SUM(clicks) as total_clicks, SUM(impressions) as total_impressions, AVG(ctr) as avg_ctr, AVG(position) as avg_position')
                ->first();

            $this->totalClicks      = (int)   ($totals->total_clicks      ?? 0);
            $this->totalImpressions = (int)   ($totals->total_impressions  ?? 0);
            $this->avgCtr           = round((float) ($totals->avg_ctr      ?? 0) * 100, 2);
            $this->avgPosition      = round((float) ($totals->avg_position ?? 0), 1);
        } catch (\Throwable) {}

        try {
            $this->topKeywords = DB::table('gsc_performance')
                ->whereRaw('1=1') // no date filter — table is truncated/replaced on each sync
                ->whereNotNull('query')
                ->selectRaw('query, SUM(clicks) as clicks, SUM(impressions) as impressions, AVG(ctr) as ctr, AVG(position) as position')
                ->groupBy('query')
                ->orderByDesc('clicks')
                ->limit(20)
                ->get()
                ->map(fn ($r) => [
                    'query'       => $r->query,
                    'clicks'      => (int) $r->clicks,
                    'impressions' => (int) $r->impressions,
                    'ctr'         => round((float) $r->ctr * 100, 2) . '%',
                    'position'    => round((float) $r->position, 1),
                ])
                ->toArray();
        } catch (\Throwable) {}

        try {
            $this->topPages = DB::table('gsc_performance')
                ->whereRaw('1=1') // no date filter — table is truncated/replaced on each sync
                ->whereNotNull('page')
                ->selectRaw('page, SUM(clicks) as clicks, SUM(impressions) as impressions, AVG(position) as position')
                ->groupBy('page')
                ->orderByDesc('clicks')
                ->limit(10)
                ->get()
                ->map(fn ($r) => [
                    'page'        => $r->page,
                    'clicks'      => (int) $r->clicks,
                    'impressions' => (int) $r->impressions,
                    'position'    => round((float) $r->position, 1),
                ])
                ->toArray();
        } catch (\Throwable) {}

        try {
            $this->opportunities = DB::table('gsc_performance')
                ->whereRaw('1=1') // no date filter — table is truncated/replaced on each sync
                ->whereNotNull('query')
                ->selectRaw('query, SUM(clicks) as clicks, SUM(impressions) as impressions, AVG(ctr) as ctr, AVG(position) as position')
                ->groupBy('query')
                ->havingRaw('AVG(position) BETWEEN 4 AND 10')
                ->orderByDesc('impressions')
                ->limit(20)
                ->get()
                ->map(fn ($r) => [
                    'query'       => $r->query,
                    'clicks'      => (int) $r->clicks,
                    'impressions' => (int) $r->impressions,
                    'ctr'         => round((float) $r->ctr * 100, 2) . '%',
                    'position'    => round((float) $r->position, 1),
                ])
                ->toArray();
        } catch (\Throwable) {}

        try {
            $this->byDevice = DB::table('gsc_performance')
                ->whereRaw('1=1') // no date filter — table is truncated/replaced on each sync
                ->whereNotNull('device')
                ->selectRaw('device, SUM(clicks) as clicks, SUM(impressions) as impressions')
                ->groupBy('device')
                ->orderByDesc('clicks')
                ->get()
                ->map(fn ($r) => [
                    'device'      => ucfirst(strtolower($r->device)),
                    'clicks'      => (int) $r->clicks,
                    'impressions' => (int) $r->impressions,
                ])
                ->toArray();
        } catch (\Throwable) {}
    }

    // ────────────────────────────────────────────────────────────────
    // Catalog health
    // ────────────────────────────────────────────────────────────────
    private function loadCatalogHealth(): void
    {
        try { $this->totalPages = Restaurant::where('status', 'approved')->count(); } catch (\Throwable) {}
        try { $this->withAiDescription = Restaurant::where('status', 'approved')->whereNotNull('ai_description')->count(); } catch (\Throwable) {}
        try { $this->withAiDescriptionEn = Restaurant::where('status', 'approved')->whereNotNull('ai_description_en')->count(); } catch (\Throwable) {}
        try {
            $this->withPhotos = Restaurant::where('status', 'approved')
                ->whereNotNull('yelp_photos')->where('yelp_photos', '!=', '[]')->where('yelp_photos', '!=', '')->count();
        } catch (\Throwable) {}
        try { $this->withRating = Restaurant::where('status', 'approved')->where('average_rating', '>', 0)->count(); } catch (\Throwable) {}
        try { $this->withCoordinates = Restaurant::where('status', 'approved')->whereNotNull('latitude')->whereNotNull('longitude')->count(); } catch (\Throwable) {}
        try { $this->withAddress = Restaurant::where('status', 'approved')->whereNotNull('address')->where('address', '!=', '')->count(); } catch (\Throwable) {}
        try { $this->withHours = Restaurant::where('status', 'approved')->whereNotNull('hours')->count(); } catch (\Throwable) {}
        try { $this->blogPostsPublished = BlogPost::where('status', 'published')->count(); } catch (\Throwable) {}
        try { $this->blogPostsDraft = BlogPost::where('status', 'draft')->count(); } catch (\Throwable) {}
        try { $this->totalReviews = Review::where('status', 'approved')->count(); } catch (\Throwable) {}
        try { $this->avgRating = (float) (Restaurant::where('status', 'approved')->where('average_rating', '>', 0)->avg('average_rating') ?? 0.0); } catch (\Throwable) {}

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
        } catch (\Throwable) {}

        $total = $this->totalPages ?: 1;
        if ($this->totalPages > 0) {
            $this->seoScore = round(
                ($this->withAiDescription / $total * 25) +
                ($this->withPhotos        / $total * 20) +
                ($this->withRating        / $total * 20) +
                ($this->withCoordinates   / $total * 15) +
                ($this->withAddress       / $total * 10) +
                ($this->withHours         / $total * 10),
                1
            );
        }

        try {
            $this->coverageByState = DB::table('restaurants')
                ->join('states', 'restaurants.state_id', '=', 'states.id')
                ->where('restaurants.status', 'approved')
                ->selectRaw("
                    states.name as state_name, states.code as state_code, COUNT(*) as count,
                    SUM(CASE WHEN restaurants.ai_description IS NOT NULL THEN 1 ELSE 0 END) as with_description,
                    SUM(CASE WHEN restaurants.yelp_photos IS NOT NULL AND restaurants.yelp_photos != '[]' THEN 1 ELSE 0 END) as with_photos
                ")
                ->groupBy('states.id', 'states.name', 'states.code')
                ->orderByDesc('count')
                ->limit(20)
                ->get()
                ->map(function ($row) {
                    $row->coverage_pct = $row->count > 0 ? round(($row->with_description / $row->count) * 100) : 0;
                    return $row;
                })
                ->toArray();
        } catch (\Throwable) {}
    }
}
