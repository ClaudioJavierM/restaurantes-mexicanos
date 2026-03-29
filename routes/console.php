<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Services\N8nWebhookService;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// ============================================================================
// Helper function to notify n8n on job failure
// ============================================================================
if (!function_exists('notifyN8nFailure')) {
function notifyN8nFailure(string $jobName, string $description): void
{
    try {
        $n8n = app(N8nWebhookService::class);
        $n8n->notifyJobFailure($jobName, $description);
    } catch (\Exception $e) {
        \Log::error("Failed to notify n8n: " . $e->getMessage());
    }
}
} // end function_exists guard

// ============================================================================
// Scheduled Tasks for Yelp Restaurant Imports
// ============================================================================

/**
 * SMART IMPORT - Runs daily and automatically:
 * - Tracks progress per city
 * - Skips cities that are 80%+ duplicates (exhausted)
 * - Processes 30 cities per run
 * - ~100-150 new restaurants per day
 * - No manual state rotation needed
 */
// Budget: Enhanced plan = 5,000 calls/month (~166/day)
// Each city: 1 search + ~20-50 detail calls = ~21-51 calls/city
// 4 cities/run × 2 runs = ~8 cities/day = ~88-408 calls/day → stays within budget
Schedule::command('yelp:import-smart --cities=4 --limit=50 --min-rating=3.5 --delay=2')
    ->dailyAt('02:00')
    ->timezone('America/New_York')
    ->description('DAILY smart import: 4 cities, budget-safe for 5K/month Enhanced plan')
    ->onSuccess(function () {
        \Log::info('Daily smart import completed successfully');
    })
    ->onFailure(function () {
        \Log::error('Daily smart import failed');
        notifyN8nFailure('yelp:import-smart', 'Daily smart import (30 cities)');
    });

/**
 * SECOND RUN at 2:00 PM - Additional import for faster growth
 */
Schedule::command('yelp:import-smart --cities=3 --limit=50 --min-rating=3.5 --delay=2')
    ->dailyAt('14:00')
    ->timezone('America/New_York')
    ->description('Afternoon smart import: 3 additional cities')
    ->onSuccess(function () {
        \Log::info('Afternoon smart import completed successfully');
    })
    ->onFailure(function () {
        \Log::error('Afternoon smart import failed');
        notifyN8nFailure('yelp:import-smart', 'Afternoon smart import (20 cities)');
    });

// ============================================================================
// Data Quality & Maintenance Tasks
// ============================================================================

/**
 * Run duplicate detection DAILY at 4:00 AM
 * (2 hours after imports complete)
 * This ensures we clean up any duplicates from daily imports
 * Uses --merge to preserve valuable data from all duplicates before removing
 */
Schedule::command('restaurants:detect-duplicates --remove --merge --similarity=80')
    ->cron('0 4 * * *')
    ->timezone('America/New_York')
    ->description('DAILY duplicate detection and cleanup')
    ->onSuccess(function () {
        \Log::info('Daily duplicate detection completed successfully');
    })
    ->onFailure(function () {
        \Log::error('Daily duplicate detection failed');
        notifyN8nFailure('restaurants:detect-duplicates', 'Duplicate detection and cleanup');
    });

/**
 * Re-match orphaned Yelp restaurants daily at 5:00 AM
 */
Schedule::command('yelp:rematch --flexible --limit=200')
    ->cron('0 5 * * *')
    ->timezone('America/New_York')
    ->description('DAILY: Re-match orphaned Yelp restaurants (200/day)')
    ->onSuccess(function () {
        \Log::info('Daily Yelp rematch completed successfully');
    })
    ->onFailure(function () {
        \Log::error('Daily Yelp rematch failed');
        notifyN8nFailure('yelp:rematch', 'Re-match orphaned Yelp restaurants');
    });

// ============================================================================
// Enrichment Tasks - Foursquare & TripAdvisor
// ============================================================================

/**
 * Enrich restaurants with Foursquare data daily at 6:00 AM
 */
Schedule::command('foursquare:enrich-restaurants --limit=250 --delay=1')
    ->cron('0 6 * * *')
    ->timezone('America/New_York')
    ->description('DAILY: Enrich restaurants with Foursquare data (250/day)')
    ->onSuccess(function () {
        \Log::info('Daily Foursquare enrichment completed successfully');
    })
    ->onFailure(function () {
        \Log::error('Daily Foursquare enrichment failed');
        notifyN8nFailure('foursquare:enrich-restaurants', 'Foursquare enrichment (250/day)');
    });

/**
 * Enrich restaurants with TripAdvisor data daily at 7:00 AM
 */
Schedule::command('tripadvisor:enrich-restaurants --limit=80 --delay=2')
    ->cron('0 7 * * *')
    ->timezone('America/New_York')
    ->description('DAILY: Enrich restaurants with TripAdvisor data (80/day)')
    ->onSuccess(function () {
        \Log::info('Daily TripAdvisor enrichment completed successfully');
    })
    ->onFailure(function () {
        \Log::error('Daily TripAdvisor enrichment failed');
        notifyN8nFailure('tripadvisor:enrich-restaurants', 'TripAdvisor enrichment (80/day)');
    });

// ============================================================================
// Communication Tasks
// ============================================================================

/**
 * Send reminder emails to inactive restaurant owners - Mondays 10:00 AM
 */
Schedule::command('owners:send-reminders --days=30 --limit=100')
    ->cron('0 10 * * 1')
    ->timezone('America/New_York')
    ->description('WEEKLY reminder to inactive restaurant owners')
    ->onSuccess(function () {
        \Log::info('Owner reminders sent successfully');
    })
    ->onFailure(function () {
        \Log::error('Owner reminders failed');
        notifyN8nFailure('owners:send-reminders', 'Weekly owner reminders');
    });

/**
 * Send claim invitations - Tuesdays and Fridays 11:00 AM
 */
Schedule::command('restaurants:send-claim-invitations --limit=50 --delay=3')
    ->cron('0 11 * * 2,5')
    ->timezone('America/New_York')
    ->description('Send claim invitations to unclaimed restaurants')
    ->onSuccess(function () {
        \Log::info('Claim invitations sent successfully');
    })
    ->onFailure(function () {
        \Log::error('Claim invitations failed');
        notifyN8nFailure('restaurants:send-claim-invitations', 'Claim invitations');
    });

// ============================================================================
// Rankings Calculation
// ============================================================================

/**
 * Recalculate Top 10 city, Top 10 state, and Top 100 national rankings
 * Runs every Sunday at 3:00 AM (after imports + duplicate cleanup are done)
 */
Schedule::command('rankings:calculate')
    ->cron('0 3 * * 0')
    ->timezone('America/New_York')
    ->description('WEEKLY: Recalculate Top 10 city/state + Top 100 national rankings')
    ->onSuccess(function () {
        \Log::info('Weekly rankings calculation completed successfully');
    })
    ->onFailure(function () {
        \Log::error('Weekly rankings calculation failed');
        notifyN8nFailure('rankings:calculate', 'Weekly Top 10 city/state rankings');
    });

// ============================================================================
// System Maintenance
// ============================================================================

/**
 * Clean up old ElevenLabs verification audio files - Hourly
 */
Schedule::command('verification:cleanup-audio')
    ->hourly()
    ->description('Clean up old verification audio files');
