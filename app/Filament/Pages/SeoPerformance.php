<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SeoPerformance extends Page
{
    protected static bool $isLazy = true;

    protected static ?string $navigationIcon  = 'heroicon-o-magnifying-glass-circle';
    protected static ?string $navigationLabel = 'SEO Performance';
    protected static ?string $navigationGroup = 'Marketing & SEO';
    protected static ?int    $navigationSort  = 2;
    protected static string  $view            = 'filament.pages.seo-performance';

    // ----------------------------------------------------------------
    // State
    // ----------------------------------------------------------------
    public bool  $hasData        = false;
    public int   $totalClicks    = 0;
    public int   $totalImpressions = 0;
    public float $avgCtr         = 0.0;
    public float $avgPosition    = 0.0;

    public array $topKeywords    = [];   // top 20 por clicks
    public array $topPages       = [];   // top 10 por clicks
    public array $opportunities  = [];   // posición 4-10 (oportunidades)
    public array $clicksByDay    = [];   // tendencia últimos 30 días
    public array $byDevice       = [];   // desglose por device

    public function mount(): void
    {
        // Verificar si la tabla existe y tiene datos
        try {
            $count = DB::table('gsc_performance')->count();
            $this->hasData = $count > 0;
        } catch (\Throwable) {
            $this->hasData = false;
            return;
        }

        if (! $this->hasData) {
            return;
        }

        $since = Carbon::now()->subDays(30)->format('Y-m-d');

        // ---- Métricas globales ----
        try {
            $totals = DB::table('gsc_performance')
                ->where('date', '>=', $since)
                ->selectRaw('
                    SUM(clicks) as total_clicks,
                    SUM(impressions) as total_impressions,
                    AVG(ctr) as avg_ctr,
                    AVG(position) as avg_position
                ')
                ->first();

            $this->totalClicks      = (int)   ($totals->total_clicks      ?? 0);
            $this->totalImpressions = (int)   ($totals->total_impressions  ?? 0);
            $this->avgCtr           = round((float) ($totals->avg_ctr      ?? 0) * 100, 2); // como %
            $this->avgPosition      = round((float) ($totals->avg_position ?? 0), 1);
        } catch (\Throwable) {}

        // ---- Top 20 keywords por clicks ----
        try {
            $this->topKeywords = DB::table('gsc_performance')
                ->where('date', '>=', $since)
                ->whereNotNull('query')
                ->selectRaw('
                    query,
                    SUM(clicks) as clicks,
                    SUM(impressions) as impressions,
                    AVG(ctr) as ctr,
                    AVG(position) as position
                ')
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

        // ---- Top 10 páginas por clicks ----
        try {
            $this->topPages = DB::table('gsc_performance')
                ->where('date', '>=', $since)
                ->whereNotNull('page')
                ->selectRaw('
                    page,
                    SUM(clicks) as clicks,
                    SUM(impressions) as impressions,
                    AVG(position) as position
                ')
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

        // ---- Oportunidades: posición 4-10 ----
        try {
            $this->opportunities = DB::table('gsc_performance')
                ->where('date', '>=', $since)
                ->whereNotNull('query')
                ->selectRaw('
                    query,
                    SUM(clicks) as clicks,
                    SUM(impressions) as impressions,
                    AVG(ctr) as ctr,
                    AVG(position) as position
                ')
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

        // ---- Tendencia de clicks por día (últimos 30 días) ----
        try {
            $this->clicksByDay = DB::table('gsc_performance')
                ->where('date', '>=', $since)
                ->selectRaw('date, SUM(clicks) as clicks, SUM(impressions) as impressions')
                ->groupBy('date')
                ->orderBy('date')
                ->get()
                ->map(fn ($r) => [
                    'date'        => $r->date,
                    'clicks'      => (int) $r->clicks,
                    'impressions' => (int) $r->impressions,
                ])
                ->toArray();
        } catch (\Throwable) {}

        // ---- Desglose por device ----
        try {
            $this->byDevice = DB::table('gsc_performance')
                ->where('date', '>=', $since)
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
}
