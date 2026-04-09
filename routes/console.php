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
 * SMART IMPORT - 4 runs/day × 10 cities = ~600 calls/day
 * Goal: exhaust 7 trial keys (35,000 calls) before they expire ~Apr 28
 * Keys are consumed sequentially (key 1 → 2 → 3...) via findActiveKeyIndex()
 */
// IMPORT RUN 1 — 1:00 AM
Schedule::command('yelp:import-smart --cities=10 --limit=50 --min-rating=3.5 --delay=1')
    ->dailyAt('01:00')
    ->timezone('America/New_York')
    ->description('Import run 1/4: 10 cities')
    ->onSuccess(function () { \Log::info('Yelp import run 1 completed'); })
    ->onFailure(function () {
        \Log::error('Yelp import run 1 failed');
        notifyN8nFailure('yelp:import-smart', 'Import run 1 (10 cities)');
    });

// IMPORT RUN 2 — 7:00 AM
Schedule::command('yelp:import-smart --cities=10 --limit=50 --min-rating=3.5 --delay=1')
    ->dailyAt('07:00')
    ->timezone('America/New_York')
    ->description('Import run 2/4: 10 cities')
    ->onSuccess(function () { \Log::info('Yelp import run 2 completed'); })
    ->onFailure(function () {
        \Log::error('Yelp import run 2 failed');
        notifyN8nFailure('yelp:import-smart', 'Import run 2 (10 cities)');
    });

// IMPORT RUN 3 — 1:00 PM
Schedule::command('yelp:import-smart --cities=10 --limit=50 --min-rating=3.5 --delay=1')
    ->dailyAt('13:00')
    ->timezone('America/New_York')
    ->description('Import run 3/4: 10 cities')
    ->onSuccess(function () { \Log::info('Yelp import run 3 completed'); })
    ->onFailure(function () {
        \Log::error('Yelp import run 3 failed');
        notifyN8nFailure('yelp:import-smart', 'Import run 3 (10 cities)');
    });

// IMPORT RUN 4 — 7:00 PM
Schedule::command('yelp:import-smart --cities=10 --limit=50 --min-rating=3.5 --delay=1')
    ->dailyAt('19:00')
    ->timezone('America/New_York')
    ->description('Import run 4/4: 10 cities')
    ->onSuccess(function () { \Log::info('Yelp import run 4 completed'); })
    ->onFailure(function () {
        \Log::error('Yelp import run 4 failed');
        notifyN8nFailure('yelp:import-smart', 'Import run 4 (10 cities)');
    });

/**
 * BACKFILL — every 2 hours (12 runs/day × 100 restaurants = 1,200 calls/day)
 * - Enriches existing 26K restaurants with photos, hours, attributes, coords
 * - Combined with imports: ~1,800 calls/day total
 * - 35,000 calls ÷ 1,800/day = ~19 days → exhausted before Apr 28 expiry
 * - yelp_enriched_at semaphore prevents duplicate calls across commands
 */
Schedule::command('yelp:backfill --limit=100')
    ->cron('0 */2 * * *')
    ->timezone('America/New_York')
    ->description('ENRICHMENT: Backfill photos/hours/attributes (every 2h, 12x/day)')
    ->onSuccess(function () { \Log::info('Yelp backfill run completed'); })
    ->onFailure(function () {
        notifyN8nFailure('yelp:backfill', 'Enrichment backfill run');
    });

// ============================================================================
// Google Photo Backfill — 50 restaurants/day at 3:30 AM
// Cost: ~$4.35/day ($0.017 details + 10 × $0.007 photos per restaurant)
// Monthly: ~$130 — stays within $200 free credit even with other usage
// ============================================================================
Schedule::command('restaurants:download-photos --limit=50')
    ->dailyAt('03:30')
    ->timezone('America/New_York')
    ->description('DAILY: Backfill Google photos for 50 restaurants (budget-safe rate)')
    ->onSuccess(function () { \Log::info('Photo backfill completed (50 restaurants)'); })
    ->onFailure(function () {
        \Log::error('Photo backfill failed');
        notifyN8nFailure('restaurants:download-photos', 'Daily Google photo backfill');
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
// Yelp Photo Merge — weekly Sunday 1:00 AM
// ============================================================================

/**
 * Merge yelp_photos URLs into photos[] column so gallery shows Google + Yelp together
 * Runs weekly after rankings (3 AM) and backfills cycle
 */
Schedule::command('restaurants:merge-yelp-photos --limit=5000')
    ->weekly()
    ->sundays()
    ->at('01:00')
    ->timezone('America/New_York')
    ->description('WEEKLY: Merge Yelp photo URLs into photos column')
    ->onSuccess(function () {
        \Log::info('Weekly Yelp photo merge completed successfully');
    })
    ->onFailure(function () {
        \Log::error('Weekly Yelp photo merge failed');
        notifyN8nFailure('restaurants:merge-yelp-photos', 'Weekly Yelp photo merge');
    });

// ============================================================================
// System Maintenance
// ============================================================================

// verification:cleanup-audio command removed (command no longer exists)

// ============================================================================
// AI Description Generator — twice daily (ES + EN)
// 100 restaurants/run × 4 runs/day = ~400/day → ~26K done in ~65 days
// Cost: ~$0.003/restaurant with GPT-4o-mini
// ============================================================================
Schedule::command('famer:generate-descriptions --batch=100 --lang=es')
    ->twiceDaily(1, 13)
    ->timezone('America/New_York')
    ->description('Generate AI descriptions (ES) for 100 restaurants per run')
    ->onFailure(fn() => notifyN8nFailure('famer:generate-descriptions', 'AI descriptions ES'));

Schedule::command('famer:generate-descriptions --batch=100 --lang=en')
    ->twiceDaily(2, 14)
    ->timezone('America/New_York')
    ->description('Generate AI descriptions (EN) for 100 restaurants per run')
    ->onFailure(fn() => notifyN8nFailure('famer:generate-descriptions', 'AI descriptions EN'));

// ============================================================================
// Review Request Automation — Hourly
// Sends SMS review requests to customers 1-4h after completed order/reservation
// SmsLog deduplication ensures each customer is only contacted once per 7 days
// ============================================================================
Schedule::command('reviews:send-requests')
    ->hourly()
    ->timezone('America/New_York')
    ->description('Send post-visit review request SMS to customers')
    ->onFailure(function () {
        notifyN8nFailure('reviews:send-requests', 'Post-visit review request SMS');
    });

// ============================================================================
// AI Blog Post Auto-Generation — Mon & Thu 10:00 AM (4 posts/week)
// Uses GPT-4o-mini, ~$0.01/post, skips already-covered topics automatically
// ============================================================================
Schedule::command('blog:generate-posts --count=2 --lang=es')
    ->weeklyOn(1, '10:00')
    ->timezone('America/New_York')
    ->description('Auto-generate 2 AI blog posts (Monday)')
    ->onFailure(fn() => notifyN8nFailure('blog:generate-posts', 'Weekly AI blog generation'));

Schedule::command('blog:generate-posts --count=2 --lang=es')
    ->weeklyOn(4, '10:00')
    ->timezone('America/New_York')
    ->description('Auto-generate 2 AI blog posts (Thursday)')
    ->onFailure(fn() => notifyN8nFailure('blog:generate-posts', 'Weekly AI blog generation Thu'));
