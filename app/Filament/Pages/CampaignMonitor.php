<?php

namespace App\Filament\Pages;

use App\Models\EmailLog;
use App\Models\NewsletterEvent;
use Filament\Pages\Page;

class CampaignMonitor extends Page
{
    protected static bool $isLazy = true;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?string $navigationLabel = 'Campaign Monitor';
    protected static ?string $navigationGroup = 'Email';
    protected static ?int $navigationSort = 2;
    protected static string $view = 'filament.pages.campaign-monitor';

    public int $totalSent      = 0;
    public int $totalDelivered = 0;
    public int $totalOpened    = 0;
    public int $totalClicked   = 0;
    public int $totalBounced   = 0;
    public float $bounceRate   = 0.0;
    public float $openRate     = 0.0;
    public float $clickRate    = 0.0;

    /** @var \Illuminate\Support\Collection */
    public $byCategory;

    /** @var \Illuminate\Support\Collection */
    public $dailyStats;

    /** @var \Illuminate\Support\Collection */
    public $recentEvents;

    public function mount(): void
    {
        $this->byCategory   = collect();
        $this->dailyStats   = collect();
        $this->recentEvents = collect();

        $this->loadStats();
    }

    public function loadStats(): void
    {
        try {
            $base = fn() => EmailLog::whereNotNull('from_email');

            $this->totalSent      = $base()->count();
            $this->totalDelivered = $base()->where('status', 'delivered')->count();
            $this->totalOpened    = $base()->where('status', 'opened')->count();
            $this->totalClicked   = $base()->where('status', 'clicked')->count();
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

        try {
            $this->byCategory = EmailLog::whereNotNull('from_email')
                ->selectRaw("category, COUNT(*) as sent,
                             SUM(status='delivered') as delivered,
                             SUM(status='opened') as opened,
                             SUM(status='clicked') as clicked,
                             SUM(status='bounced') as bounced")
                ->groupBy('category')
                ->orderByDesc('sent')
                ->get();
        } catch (\Throwable $e) {
            $this->byCategory = collect();
        }

        try {
            $this->dailyStats = EmailLog::whereNotNull('from_email')
                ->where('sent_at', '>=', now()->subDays(30))
                ->selectRaw("DATE(sent_at) as date, COUNT(*) as sent,
                             SUM(status='opened') as opened,
                             SUM(status='clicked') as clicked")
                ->groupBy('date')
                ->orderBy('date')
                ->get();
        } catch (\Throwable $e) {
            $this->dailyStats = collect();
        }

        try {
            $this->recentEvents = NewsletterEvent::latest('occurred_at')
                ->take(20)
                ->get();
        } catch (\Throwable $e) {
            $this->recentEvents = collect();
        }
    }

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
}
