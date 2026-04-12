<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GoogleAnalyticsService
{
    protected ?string $accessToken = null;
    protected ?array $serviceAccount = null;
    protected string $propertyId;
    protected string $gscSiteUrl;
    protected int $cacheTtl;

    public function __construct()
    {
        $this->propertyId = config('analytics.ga4_property_id', '');
        $this->gscSiteUrl = config('analytics.gsc_site_url', '');
        $this->cacheTtl = (int) config('analytics.cache_ttl', 1800);
        $this->serviceAccount = $this->resolveCredentials();
    }

    public function isConfigured(): bool
    {
        return $this->serviceAccount !== null && !empty($this->propertyId);
    }

    public function isGscConfigured(): bool
    {
        return $this->serviceAccount !== null && !empty($this->gscSiteUrl);
    }

    // ── GA4 Data API ─────────────────────────────────────────────────

    public function getGA4Report(string $startDate, string $endDate, array $metrics, array $dimensions = []): ?array
    {
        $cacheKey = 'ga4_report_' . md5("{$startDate}_{$endDate}_" . implode(',', $metrics) . '_' . implode(',', $dimensions));

        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($startDate, $endDate, $metrics, $dimensions) {
            $token = $this->getAccessToken('https://www.googleapis.com/auth/analytics.readonly');
            if (!$token) return null;

            $body = [
                'dateRanges' => [['startDate' => $startDate, 'endDate' => $endDate]],
                'metrics' => array_map(fn($m) => ['name' => $m], $metrics),
            ];

            if (!empty($dimensions)) {
                $body['dimensions'] = array_map(fn($d) => ['name' => $d], $dimensions);
            }

            $response = Http::withToken($token)
                ->timeout(15)
                ->post("https://analyticsdata.googleapis.com/v1beta/properties/{$this->propertyId}:runReport", $body);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('GA4 API error: ' . $response->body());
            return null;
        });
    }

    public function getOverviewStats(int $days = 30): array
    {
        $endDate = 'today';
        $startDate = "{$days}daysAgo";

        $report = $this->getGA4Report($startDate, $endDate, [
            'activeUsers', 'sessions', 'screenPageViews', 'bounceRate',
            'averageSessionDuration', 'newUsers',
        ]);

        if (!$report || empty($report['rows'])) {
            return $this->emptyOverviewStats();
        }

        $row = $report['rows'][0]['metricValues'] ?? [];

        return [
            'active_users' => (int) ($row[0]['value'] ?? 0),
            'sessions' => (int) ($row[1]['value'] ?? 0),
            'pageviews' => (int) ($row[2]['value'] ?? 0),
            'bounce_rate' => round((float) ($row[3]['value'] ?? 0) * 100, 1),
            'avg_session_duration' => round((float) ($row[4]['value'] ?? 0), 0),
            'new_users' => (int) ($row[5]['value'] ?? 0),
        ];
    }

    public function getTopPages(int $days = 30, int $limit = 20): array
    {
        $report = $this->getGA4Report("{$days}daysAgo", 'today',
            ['screenPageViews', 'activeUsers', 'averageSessionDuration'],
            ['pagePath']
        );

        if (!$report || empty($report['rows'])) return [];

        $pages = [];
        foreach (array_slice($report['rows'], 0, $limit) as $row) {
            $pages[] = [
                'path' => $row['dimensionValues'][0]['value'] ?? '',
                'pageviews' => (int) ($row['metricValues'][0]['value'] ?? 0),
                'users' => (int) ($row['metricValues'][1]['value'] ?? 0),
                'avg_duration' => round((float) ($row['metricValues'][2]['value'] ?? 0), 0),
            ];
        }

        return $pages;
    }

    public function getDailyTraffic(int $days = 30): array
    {
        $report = $this->getGA4Report("{$days}daysAgo", 'today',
            ['sessions', 'activeUsers', 'screenPageViews'],
            ['date']
        );

        if (!$report || empty($report['rows'])) return [];

        $daily = [];
        foreach ($report['rows'] as $row) {
            $dateStr = $row['dimensionValues'][0]['value'] ?? '';
            $daily[] = [
                'date' => substr($dateStr, 0, 4) . '-' . substr($dateStr, 4, 2) . '-' . substr($dateStr, 6, 2),
                'sessions' => (int) ($row['metricValues'][0]['value'] ?? 0),
                'users' => (int) ($row['metricValues'][1]['value'] ?? 0),
                'pageviews' => (int) ($row['metricValues'][2]['value'] ?? 0),
            ];
        }

        usort($daily, fn($a, $b) => strcmp($a['date'], $b['date']));
        return $daily;
    }

    public function getTrafficSources(int $days = 30): array
    {
        $report = $this->getGA4Report("{$days}daysAgo", 'today',
            ['sessions', 'activeUsers'],
            ['sessionDefaultChannelGroup']
        );

        if (!$report || empty($report['rows'])) return [];

        $sources = [];
        foreach ($report['rows'] as $row) {
            $sources[] = [
                'channel' => $row['dimensionValues'][0]['value'] ?? 'Unknown',
                'sessions' => (int) ($row['metricValues'][0]['value'] ?? 0),
                'users' => (int) ($row['metricValues'][1]['value'] ?? 0),
            ];
        }

        usort($sources, fn($a, $b) => $b['sessions'] - $a['sessions']);
        return $sources;
    }

    // ── GSC Search Console ───────────────────────────────────────────

    public function getSearchConsoleData(int $days = 30, int $limit = 50): array
    {
        $cacheKey = "gsc_data_{$days}_{$limit}";

        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($days, $limit) {
            $token = $this->getAccessToken('https://www.googleapis.com/auth/webmasters.readonly');
            if (!$token) return [];

            $startDate = now()->subDays($days + 3)->format('Y-m-d');
            $endDate = now()->subDays(3)->format('Y-m-d');

            $response = Http::withToken($token)
                ->timeout(15)
                ->post("https://www.googleapis.com/webmasters/v3/sites/" . urlencode($this->gscSiteUrl) . "/searchAnalytics/query", [
                    'startDate' => $startDate,
                    'endDate' => $endDate,
                    'dimensions' => ['query', 'page'],
                    'rowLimit' => $limit,
                    'dataState' => 'final',
                ]);

            if ($response->successful()) {
                return $response->json('rows', []);
            }

            Log::error('GSC API error: ' . $response->body());
            return [];
        });
    }

    // ── Auth (JWT Service Account) ───────────────────────────────────

    protected function getAccessToken(string $scope): ?string
    {
        if (!$this->serviceAccount) return null;

        $cacheKey = 'google_token_' . md5($scope);

        return Cache::remember($cacheKey, 3500, function () use ($scope) {
            try {
                $now = time();
                $header = $this->base64UrlEncode(json_encode(['alg' => 'RS256', 'typ' => 'JWT']));
                $claims = $this->base64UrlEncode(json_encode([
                    'iss' => $this->serviceAccount['client_email'],
                    'scope' => $scope,
                    'aud' => 'https://oauth2.googleapis.com/token',
                    'exp' => $now + 3600,
                    'iat' => $now,
                ]));

                $signingInput = "{$header}.{$claims}";
                $privateKey = openssl_pkey_get_private($this->serviceAccount['private_key']);
                if (!$privateKey) return null;

                $signature = '';
                openssl_sign($signingInput, $signature, $privateKey, OPENSSL_ALGO_SHA256);

                $jwt = "{$signingInput}." . $this->base64UrlEncode($signature);

                $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
                    'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                    'assertion' => $jwt,
                ]);

                return $response->successful() ? $response->json('access_token') : null;
            } catch (\Throwable $e) {
                Log::error('Google auth error: ' . $e->getMessage());
                return null;
            }
        });
    }

    protected function resolveCredentials(): ?array
    {
        $path = config('analytics.ga4_service_account')
            ?? config('services.google.service_account_path')
            ?? env('GOOGLE_SERVICE_ACCOUNT_PATH');

        if ($path && file_exists($path)) {
            $json = json_decode(file_get_contents($path), true);
            if ($json) return $json;
        }

        $inline = env('GOOGLE_SERVICE_ACCOUNT_JSON');
        if ($inline) {
            $json = json_decode($inline, true);
            if ($json) return $json;
        }

        return null;
    }

    protected function emptyOverviewStats(): array
    {
        return [
            'active_users' => 0, 'sessions' => 0, 'pageviews' => 0,
            'bounce_rate' => 0, 'avg_session_duration' => 0, 'new_users' => 0,
        ];
    }

    protected function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
}
