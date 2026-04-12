<?php

namespace App\Filament\Pages;

use App\Services\GoogleAnalyticsService;
use Filament\Pages\Page;

class WebAnalytics extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?string $navigationLabel = 'Web Analytics (GA4)';
    protected static ?string $navigationGroup = 'Marketing & SEO';
    protected static ?int $navigationSort = 2;
    protected static string $view = 'filament.pages.web-analytics';

    public bool $isConfigured = false;
    public array $overview = [];
    public array $topPages = [];
    public array $dailyTraffic = [];
    public array $trafficSources = [];
    public array $searchConsole = [];
    public int $days = 30;

    public function mount(): void
    {
        $this->loadData();
    }

    public function setDays(int $days): void
    {
        $this->days = $days;
        $this->loadData();
    }

    protected function loadData(): void
    {
        $service = app(GoogleAnalyticsService::class);
        $this->isConfigured = $service->isConfigured();

        if (!$this->isConfigured) return;

        $this->overview = $service->getOverviewStats($this->days);
        $this->topPages = $service->getTopPages($this->days);
        $this->dailyTraffic = $service->getDailyTraffic($this->days);
        $this->trafficSources = $service->getTrafficSources($this->days);

        if ($service->isGscConfigured()) {
            $this->searchConsole = $service->getSearchConsoleData($this->days);
        }
    }
}
