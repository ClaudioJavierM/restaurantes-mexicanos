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
 * FAMER Campaign — Rampa progresiva (Resend Pro: 50K/mes, sin límite diario)
 *
 * Semana 1 (días 1-3): 300/día  → warming domain reputation
 * Semana 1 (días 4-7): 500/día  → subida gradual
 * Semana 2+:           800/día  → velocidad de crucero (pipeline ~3,944 US en ~2 semanas)
 *
 * AJUSTAR --limit manualmente según semana:
 *   Semana 1 inicio: --limit=300
 *   Semana 1 final:  --limit=500
 *   Semana 2+:       --limit=800
 *
 * País: solo US (country='US') — MX tiene campaña separada pendiente de diseño
 */
Schedule::command('restaurants:send-claim-invitations --limit=500 --delay=1')
    ->dailyAt('11:00')
    ->timezone('America/New_York')
    ->description('DAILY: Claim invitations US — 500/día (Resend Pro 50K/mes)')
    ->onSuccess(function () {
        \Log::info('Claim invitations sent successfully');
    })
    ->onFailure(function () {
        \Log::error('Claim invitations failed');
        notifyN8nFailure('restaurants:send-claim-invitations', 'Claim invitations');
    });

/**
 * FAMER Email Sequence — Email 2 (How It Works) — Diario 10am ET
 * Restaurantes que recibieron Email 1 hace 10+ días y no han reclamado (solo US)
 */
Schedule::command('famer:send-emails --email2 --limit=200')
    ->dailyAt('10:00')
    ->timezone('America/New_York')
    ->description('DAILY: FAMER Email 2 follow-up — 200/día (US only)')
    ->onFailure(function () {
        \Log::error('FAMER Email 2 sequence failed');
    });

/**
 * FAMER Email Sequence — Email 3 (Final Reminder) — Diario 10:30am ET
 * Restaurantes que recibieron Email 2 hace 10+ días y no han reclamado (solo US)
 */
Schedule::command('famer:send-emails --email3 --limit=200')
    ->dailyAt('10:30')
    ->timezone('America/New_York')
    ->description('DAILY: FAMER Email 3 final reminder — 200/día (US only)')
    ->onFailure(function () {
        \Log::error('FAMER Email 3 sequence failed');
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

/**
 * Recalculate FAMER Scores for all restaurants
 * Runs every Sunday at 4:00 AM (after rankings:calculate finishes)
 * Combines Google/Yelp/TripAdvisor ratings + vote bonus into a single score
 */
Schedule::command('famer:recalculate-scores')
    ->cron('0 4 * * 0')
    ->timezone('America/New_York')
    ->description('WEEKLY: Recalculate FAMER composite scores for all restaurants')
    ->onSuccess(function () {
        \Log::info('Weekly FAMER score recalculation completed');
    })
    ->onFailure(function () {
        \Log::error('Weekly FAMER score recalculation failed');
        notifyN8nFailure('famer:recalculate-scores', 'Weekly FAMER score recalculation');
    });

/**
 * Assign monthly badges and populate monthly_rankings from votes
 * Runs on the 1st of each month at 2:00 AM (processes previous month's votes)
 */
Schedule::command('famer:assign-badges')
    ->cron('0 2 1 * *')
    ->timezone('America/New_York')
    ->description('MONTHLY: Assign city/state/national badges from previous month votes')
    ->onSuccess(function () {
        \Log::info('Monthly badge assignment completed');
    })
    ->onFailure(function () {
        \Log::error('Monthly badge assignment failed');
        notifyN8nFailure('famer:assign-badges', 'Monthly badge assignment');
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

// ============================================================================
// Campaign Report — monitoreo para agente Maya (OpenClaw)
// ============================================================================

/**
 * Campaign Report Diario — genera reporte y lo guarda en storage para Maya
 * Corre cada mañana a las 9am ET (8am CST)
 */
Schedule::command('famer:campaign-report --period=7 --format=markdown')
    ->dailyAt('09:00')
    ->timezone('America/New_York')
    ->description('Daily campaign health report')
    ->sendOutputTo(storage_path('logs/campaign-report-daily.md'))
    ->onSuccess(function () {
        \Log::info('Daily campaign report generated');
    });

/**
 * Alert check cada hora — solo notifica si hay problemas
 */
Schedule::command('famer:campaign-report --alert --format=json')
    ->hourly()
    ->timezone('America/New_York')
    ->description('Hourly bounce rate alert check')
    ->onSuccess(function () {
        \Log::info('Hourly campaign alert check OK');
    });

/**
 * Email health check — cada 30 minutos, solo output si bounce/complaint crítico
 */
Schedule::command('famer:email-health --alert')
    ->everyThirtyMinutes()
    ->timezone('America/New_York')
    ->description('Email health check — alerta si bounce/complaint rate crítico');

// ============================================================================
// GSC Sync — datos de keywords, impressiones, CTR y posiciones desde Google
// Corre diario a las 6 AM ET (GSC tiene retraso de ~3 días en los datos)
// Requiere GOOGLE_SERVICE_ACCOUNT_JSON en .env — sin credenciales, no crashea
// ============================================================================
Schedule::command('famer:sync-gsc --days=7')
    ->dailyAt('06:00')
    ->timezone('America/New_York')
    ->description('Sync GSC data: keywords, impressions, CTR, positions')
    ->onFailure(function () {
        \Log::error('GSC sync failed');
        notifyN8nFailure('famer:sync-gsc', 'Google Search Console sync');
    });
