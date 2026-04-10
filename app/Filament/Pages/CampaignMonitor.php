<?php

namespace App\Filament\Pages;

use App\Models\EmailLog;
use App\Models\EmailSuppression;
use App\Models\NewsletterEvent;
use App\Models\Restaurant;
use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;

class CampaignMonitor extends Page
{
    protected static bool $isLazy = true;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?string $navigationLabel = 'Campaign Monitor';
    protected static ?string $navigationGroup = 'Email';
    protected static ?int $navigationSort = 2;
    protected static string $view = 'filament.pages.campaign-monitor';

    // ── Period ────────────────────────────────────────────────────────────
    public int $period = 30; // days

    // ── Global totals ────────────────────────────────────────────────────
    public int $totalSent      = 0;
    public int $totalDelivered = 0;
    public int $totalOpened    = 0;
    public int $totalClicked   = 0;
    public int $totalBounced   = 0;
    public float $bounceRate   = 0.0;
    public float $openRate     = 0.0;
    public float $clickRate    = 0.0;

    // ── New metrics ───────────────────────────────────────────────────────
    public float $avgTimeToOpenHours = 0.0;   // avg hours sent → opened
    public int   $totalComplained    = 0;
    public int   $totalUnsubscribed  = 0;
    public array $suppressionsByReason = [];  // ['bounced' => N, 'complained' => N, ...]

    // ── Week-over-week ────────────────────────────────────────────────────
    public float $openRateThisWeek = 0.0;
    public float $openRateLastWeek = 0.0;
    public float $openRateDelta    = 0.0;   // positive = improvement

    /** @var \Illuminate\Support\Collection */
    public $byCategory;

    /** @var \Illuminate\Support\Collection  Top 5 emails más abiertos */
    public $topOpened;

    /** @var \Illuminate\Support\Collection  Últimas 50 aperturas (newsletter_events) */
    public $recentOpens;

    /** @var \Illuminate\Support\Collection  Feed 20 eventos */
    public $recentEvents;

    /** @var \Illuminate\Support\Collection  Daily stats for sparkline */
    public $dailyStats;

    public function mount(): void
    {
        $this->period = (int) request()->query('period', 30);
        if (!in_array($this->period, [7, 14, 30, 90])) {
            $this->period = 30;
        }

        $this->byCategory        = collect();
        $this->topOpened         = collect();
        $this->recentOpens       = collect();
        $this->recentEvents      = collect();
        $this->dailyStats        = collect();
        $this->suppressionsByReason = [];

        $this->loadStats();
    }

    public function loadStats(): void
    {
        $since = now()->subDays($this->period);
        $base  = fn() => EmailLog::whereNotNull('from_email')->where('sent_at', '>=', $since);

        // ── Global totals ─────────────────────────────────────────────────
        try {
            $this->totalSent      = $base()->count();
            $this->totalDelivered = $base()->whereNotNull('delivered_at')->count();
            $this->totalOpened    = $base()->whereNotNull('opened_at')->count();
            $this->totalClicked   = $base()->whereNotNull('clicked_at')->count();
            $this->totalBounced   = $base()->where('status', 'bounced')->count();

            $this->bounceRate = $this->totalSent > 0
                ? round(($this->totalBounced / $this->totalSent) * 100, 2)
                : 0.0;

            $this->openRate = $this->totalSent > 0
                ? round(($this->totalOpened / $this->totalSent) * 100, 1)
                : 0.0;

            $this->clickRate = $this->totalSent > 0
                ? round(($this->totalClicked / $this->totalSent) * 100, 1)
                : 0.0;
        } catch (\Throwable $e) {
            // leave defaults
        }

        // ── Avg time to open (hours) ──────────────────────────────────────
        try {
            $avgSeconds = EmailLog::whereNotNull('from_email')
                ->where('sent_at', '>=', $since)
                ->whereNotNull('opened_at')
                ->whereNotNull('sent_at')
                ->selectRaw('AVG(TIMESTAMPDIFF(SECOND, sent_at, opened_at)) as avg_sec')
                ->value('avg_sec');

            $this->avgTimeToOpenHours = $avgSeconds > 0
                ? round($avgSeconds / 3600, 1)
                : 0.0;
        } catch (\Throwable $e) {
            $this->avgTimeToOpenHours = 0.0;
        }

        // ── Suppressions ──────────────────────────────────────────────────
        try {
            $suppGroups = EmailSuppression::selectRaw('reason, COUNT(*) as total')
                ->groupBy('reason')
                ->get();

            $this->suppressionsByReason = $suppGroups->pluck('total', 'reason')->toArray();

            // Complained / unsubscribed from newsletter_events (may not be in suppressions)
            $this->totalComplained   = NewsletterEvent::where('event_type', 'complained')->count();
            $this->totalUnsubscribed = NewsletterEvent::where('event_type', 'unsubscribed')->count();
        } catch (\Throwable $e) {
            // leave defaults
        }

        // ── Week-over-week open rate ───────────────────────────────────────
        try {
            $thisWeekSince = now()->subDays(7);
            $lastWeekSince = now()->subDays(14);
            $lastWeekUntil = now()->subDays(7);

            $thisWeekSent   = EmailLog::whereNotNull('from_email')->where('sent_at', '>=', $thisWeekSince)->count();
            $thisWeekOpened = EmailLog::whereNotNull('from_email')->where('sent_at', '>=', $thisWeekSince)->whereNotNull('opened_at')->count();
            $lastWeekSent   = EmailLog::whereNotNull('from_email')->whereBetween('sent_at', [$lastWeekSince, $lastWeekUntil])->count();
            $lastWeekOpened = EmailLog::whereNotNull('from_email')->whereBetween('sent_at', [$lastWeekSince, $lastWeekUntil])->whereNotNull('opened_at')->count();

            $this->openRateThisWeek = $thisWeekSent > 0 ? round(($thisWeekOpened / $thisWeekSent) * 100, 1) : 0.0;
            $this->openRateLastWeek = $lastWeekSent > 0 ? round(($lastWeekOpened / $lastWeekSent) * 100, 1) : 0.0;
            $this->openRateDelta    = round($this->openRateThisWeek - $this->openRateLastWeek, 1);
        } catch (\Throwable $e) {
            // leave defaults
        }

        // ── By category (funnel) ──────────────────────────────────────────
        try {
            $this->byCategory = EmailLog::whereNotNull('from_email')
                ->where('sent_at', '>=', $since)
                ->selectRaw("
                    category,
                    COUNT(*) as sent,
                    SUM(delivered_at IS NOT NULL) as delivered,
                    SUM(opened_at   IS NOT NULL) as opened,
                    SUM(clicked_at  IS NOT NULL) as clicked,
                    SUM(status = 'bounced')      as bounced
                ")
                ->groupBy('category')
                ->orderByDesc('sent')
                ->get();
        } catch (\Throwable $e) {
            $this->byCategory = collect();
        }

        // ── Top 5 most-opened emails ──────────────────────────────────────
        try {
            $this->topOpened = EmailLog::whereNotNull('from_email')
                ->where('sent_at', '>=', $since)
                ->whereNotNull('opened_at')
                ->select('to_email', 'to_name', 'restaurant_id', 'open_count', 'subject', 'category', 'opened_at')
                ->orderByDesc('open_count')
                ->limit(5)
                ->get()
                ->map(function ($log) {
                    // Attach restaurant name when available
                    $log->restaurant_name = null;
                    if ($log->restaurant_id) {
                        try {
                            $log->restaurant_name = Restaurant::find($log->restaurant_id)?->name;
                        } catch (\Throwable $e) {}
                    }
                    return $log;
                });
        } catch (\Throwable $e) {
            $this->topOpened = collect();
        }

        // ── Last 50 opens (newsletter_events) ─────────────────────────────
        try {
            $this->recentOpens = NewsletterEvent::where('event_type', 'opened')
                ->latest('occurred_at')
                ->limit(50)
                ->get(['email', 'campaign_name', 'source', 'occurred_at']);
        } catch (\Throwable $e) {
            $this->recentOpens = collect();
        }

        // ── Recent events feed (20) ───────────────────────────────────────
        try {
            $this->recentEvents = NewsletterEvent::latest('occurred_at')
                ->take(20)
                ->get();
        } catch (\Throwable $e) {
            $this->recentEvents = collect();
        }

        // ── Daily stats (sparkline) ───────────────────────────────────────
        try {
            $this->dailyStats = EmailLog::whereNotNull('from_email')
                ->where('sent_at', '>=', $since)
                ->selectRaw("DATE(sent_at) as date, COUNT(*) as sent,
                             SUM(opened_at IS NOT NULL) as opened,
                             SUM(clicked_at IS NOT NULL) as clicked")
                ->groupBy('date')
                ->orderBy('date')
                ->get();
        } catch (\Throwable $e) {
            $this->dailyStats = collect();
        }
    }

    // ── Helpers ────────────────────────────────────────────────────────────

    public static function getCategoryLabel(string $category): string
    {
        return match ($category) {
            'claim_invitation' => 'Invitación Claim',
            'famer_email_1'    => 'Email 1 — Intro',
            'famer_email_2'    => 'Email 2 — Cómo funciona',
            'famer_email_3'    => 'Email 3 — Recordatorio',
            'verification'     => 'Verificación',
            'welcome'          => 'Bienvenida',
            'other'            => 'Otros',
            default            => ucfirst(str_replace('_', ' ', $category)),
        };
    }

    public function getTotalSuppressions(): int
    {
        return array_sum($this->suppressionsByReason);
    }
}
