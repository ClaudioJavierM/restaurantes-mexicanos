<?php

namespace App\Filament\Pages;

use App\Models\ApiCallLog;
use App\Models\EmailLog;
use App\Models\Restaurant;
use App\Models\Review;
use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class OperationsCenter extends Page
{
    protected static bool $isLazy = true;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?string $navigationLabel = 'Centro de Operaciones';
    protected static ?string $navigationGroup = 'Sistema';
    protected static ?int $navigationSort = 1;
    protected static string $view = 'filament.pages.operations-center';

    public int $pendingRestaurants = 0;
    public int $rejectedThisWeek = 0;
    public int $duplicatesEstimate = 0;
    public int $restaurantsWithoutCoords = 0;
    public int $restaurantsWithoutPhotos = 0;
    public int $restaurantsWithoutDescription = 0;
    public int $yelpCallsToday = 0;
    public int $yelpCallsThisMonth = 0;
    public int $yelpBudgetRemaining = 0;
    public int $googleCallsToday = 0;
    public int $emailsSentToday = 0;
    public int $emailsSentThisWeek = 0;
    public int $claimEmailsPending = 0;
    public ?string $schedulerLastRun = null;
    public bool $schedulerActive = false;
    public int $enrichmentQueue = 0;
    public int $pendingReviews = 0;
    public int $recentErrors = 0;
    public int $totalApproved = 0;
    public float $errorRate24h = 0.0;

    public function mount(): void
    {
        try {
            $this->pendingRestaurants = Restaurant::where('status', 'pending')->count();
        } catch (\Throwable $e) {
            $this->pendingRestaurants = 0;
        }

        try {
            $this->rejectedThisWeek = Restaurant::where('status', 'rejected')
                ->where('updated_at', '>=', now()->subDays(7))
                ->count();
        } catch (\Throwable $e) {
            $this->rejectedThisWeek = 0;
        }

        try {
            $this->duplicatesEstimate = (int) DB::table('restaurants')
                ->where('status', 'approved')
                ->selectRaw('COUNT(*) as cnt')
                ->groupByRaw('LOWER(name), LOWER(city)')
                ->havingRaw('COUNT(*) > 1')
                ->get()
                ->sum('cnt');
        } catch (\Throwable $e) {
            $this->duplicatesEstimate = 0;
        }

        try {
            $this->totalApproved = Restaurant::where('status', 'approved')->count();
        } catch (\Throwable $e) {
            $this->totalApproved = 0;
        }

        try {
            $this->restaurantsWithoutCoords = Restaurant::where('status', 'approved')
                ->where(function ($q) {
                    $q->whereNull('latitude')->orWhereNull('longitude');
                })
                ->count();
        } catch (\Throwable $e) {
            $this->restaurantsWithoutCoords = 0;
        }

        try {
            $this->restaurantsWithoutPhotos = Restaurant::where('status', 'approved')
                ->where(function ($q) {
                    $q->whereNull('yelp_photos')
                      ->orWhere('yelp_photos', '[]')
                      ->orWhere('yelp_photos', '');
                })
                ->count();
        } catch (\Throwable $e) {
            $this->restaurantsWithoutPhotos = 0;
        }

        try {
            $this->restaurantsWithoutDescription = Restaurant::where('status', 'approved')
                ->whereNull('ai_description')
                ->count();
        } catch (\Throwable $e) {
            $this->restaurantsWithoutDescription = 0;
        }

        try {
            $this->yelpCallsToday = ApiCallLog::where('service', 'like', '%yelp%')
                ->whereDate('called_at', today())
                ->count();
        } catch (\Throwable $e) {
            $this->yelpCallsToday = 0;
        }

        try {
            $this->yelpCallsThisMonth = ApiCallLog::where('service', 'like', '%yelp%')
                ->where('called_at', '>=', now()->startOfMonth())
                ->count();
        } catch (\Throwable $e) {
            $this->yelpCallsThisMonth = 0;
        }

        $this->yelpBudgetRemaining = max(0, 35000 - $this->yelpCallsThisMonth);

        try {
            $this->googleCallsToday = ApiCallLog::where('service', 'like', '%google%')
                ->whereDate('called_at', today())
                ->count();
        } catch (\Throwable $e) {
            $this->googleCallsToday = 0;
        }

        try {
            $this->emailsSentToday = EmailLog::whereDate('created_at', today())->count();
        } catch (\Throwable $e) {
            $this->emailsSentToday = 0;
        }

        try {
            $this->emailsSentThisWeek = EmailLog::where('created_at', '>=', now()->subDays(7))->count();
        } catch (\Throwable $e) {
            $this->emailsSentThisWeek = 0;
        }

        try {
            $hasClaimColumn = Schema::hasColumn('restaurants', 'claim_invitation_sent_at');
            if ($hasClaimColumn) {
                $this->claimEmailsPending = Restaurant::where('status', 'approved')
                    ->whereNotNull('email')
                    ->where('email', '!=', '')
                    ->whereNull('claim_invitation_sent_at')
                    ->where('is_claimed', false)
                    ->count();
            } else {
                $this->claimEmailsPending = 0;
            }
        } catch (\Throwable $e) {
            $this->claimEmailsPending = 0;
        }

        try {
            $lastLog = ApiCallLog::latest('called_at')->first();
            if ($lastLog) {
                $this->schedulerLastRun = $lastLog->called_at->diffForHumans();
                $this->schedulerActive = $lastLog->called_at->gte(now()->subHours(3));
            } else {
                $this->schedulerLastRun = 'Nunca';
                $this->schedulerActive = false;
            }
        } catch (\Throwable $e) {
            $this->schedulerLastRun = 'Desconocido';
            $this->schedulerActive = false;
        }

        try {
            $this->enrichmentQueue = Restaurant::whereNotNull('yelp_id')
                ->whereNull('yelp_enriched_at')
                ->count();
        } catch (\Throwable $e) {
            $this->enrichmentQueue = 0;
        }

        try {
            $this->pendingReviews = Review::where('status', 'pending')->count();
        } catch (\Throwable $e) {
            $this->pendingReviews = 0;
        }

        try {
            $this->recentErrors = ApiCallLog::where('success', false)
                ->where('called_at', '>=', now()->subDay())
                ->count();
        } catch (\Throwable $e) {
            $this->recentErrors = 0;
        }

        try {
            $totalCalls24h = ApiCallLog::where('called_at', '>=', now()->subDay())->count();
            $this->errorRate24h = $totalCalls24h > 0
                ? round($this->recentErrors / $totalCalls24h * 100, 1)
                : 0.0;
        } catch (\Throwable $e) {
            $this->errorRate24h = 0.0;
        }
    }
}
