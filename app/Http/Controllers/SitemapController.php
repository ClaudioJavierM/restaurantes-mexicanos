<?php

namespace App\Http\Controllers;

use App\Models\Restaurant;
use App\Models\State;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Carbon\Carbon;

class SitemapController extends Controller
{
    /**
     * Sitemap Index - points to all sub-sitemaps
     */
    public function index(): Response
    {
        $currentDomain = request()->getHost();
        $baseUrl = $this->getBaseUrl($currentDomain);

        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

        $sitemaps = ['sitemap-pages.xml', 'sitemap-restaurants.xml', 'sitemap-guides.xml', 'sitemap-rankings.xml'];
        foreach ($sitemaps as $sitemap) {
            $xml .= '<sitemap>';
            $xml .= '<loc>' . $baseUrl . '/' . $sitemap . '</loc>';
            $xml .= '<lastmod>' . now()->format('Y-m-d') . '</lastmod>';
            $xml .= '</sitemap>';
        }

        $xml .= '</sitemapindex>';

        return $this->xmlResponse($xml);
    }

    /**
     * Static pages sitemap
     */
    public function pages(): Response
    {
        $currentDomain = request()->getHost();
        $cacheKey = 'sitemap_pages_' . md5($currentDomain);

        $xml = Cache::remember($cacheKey, 3600, function () use ($currentDomain) {
            $baseUrl = $this->getBaseUrl($currentDomain);

            $xml = $this->openUrlset();
            $xml .= $this->addUrl($baseUrl . '/', now(), 'daily', '1.0');
            $xml .= $this->addUrl($baseUrl . '/restaurantes', now(), 'daily', '0.9');
            $xml .= $this->addUrl($baseUrl . '/sugerir', now()->subMonth(), 'monthly', '0.5');
            $xml .= $this->addUrl($baseUrl . '/mejores-restaurantes-mexicanos', now(), 'weekly', '0.9');
            $xml .= $this->addUrl($baseUrl . '/top-10-restaurantes-mexicanos', now(), 'weekly', '0.9');
            $xml .= '</urlset>';

            return $xml;
        });

        return $this->xmlResponse($xml);
    }

    /**
     * Restaurants sitemap
     */
    public function restaurants(): Response
    {
        $currentDomain = request()->getHost();
        $cacheKey = 'sitemap_restaurants_' . md5($currentDomain);

        $xml = Cache::remember($cacheKey, 3600, function () use ($currentDomain) {
            $baseUrl = $this->getBaseUrl($currentDomain);

            $xml = $this->openUrlset();

            Restaurant::approved()
                ->select('slug', 'updated_at')
                ->orderBy('id')
                ->chunk(1000, function ($restaurants) use (&$xml, $baseUrl) {
                    foreach ($restaurants as $restaurant) {
                        $xml .= $this->addUrl(
                            $baseUrl . '/restaurante/' . $restaurant->slug,
                            $restaurant->updated_at,
                            'weekly',
                            '0.8'
                        );
                    }
                });

            $xml .= '</urlset>';

            return $xml;
        });

        return $this->xmlResponse($xml);
    }

    /**
     * City guides sitemap
     */
    public function guides(): Response
    {
        $currentDomain = request()->getHost();
        $cacheKey = 'sitemap_guides_' . md5($currentDomain);

        $xml = Cache::remember($cacheKey, 3600, function () use ($currentDomain) {
            $baseUrl = $this->getBaseUrl($currentDomain);

            $xml = $this->openUrlset();

            // Guides index
            $xml .= $this->addUrl($baseUrl . '/guia', now(), 'weekly', '0.8');

            // State pages
            $states = State::has('restaurants')->select('id', 'name', 'code', 'slug', 'updated_at')->get();
            foreach ($states as $state) {
                $xml .= $this->addUrl(
                    $baseUrl . '/guia/' . strtolower($state->code ?? $state->name),
                    $state->updated_at ?? now()->subWeek(),
                    'weekly',
                    '0.7'
                );
            }

            // City pages
            $cities = $this->getTopCities(500);
            foreach ($cities as $city) {
                if ($city->state_code && $city->city) {
                    $xml .= $this->addUrl(
                        $baseUrl . '/guia/' . strtolower($city->state_code) . '/' . Str::slug($city->city),
                        $city->last_updated ? Carbon::parse($city->last_updated) : now()->subWeek(),
                        'weekly',
                        '0.7'
                    );
                }
            }

            $xml .= '</urlset>';

            return $xml;
        });

        return $this->xmlResponse($xml);
    }

    /**
     * Rankings sitemap
     */
    public function rankings(): Response
    {
        $currentDomain = request()->getHost();
        $cacheKey = 'sitemap_rankings_' . md5($currentDomain);

        $xml = Cache::remember($cacheKey, 3600, function () use ($currentDomain) {
            $baseUrl = $this->getBaseUrl($currentDomain);

            $xml = $this->openUrlset();

            // State ranking pages
            $states = State::has('restaurants')->select('id', 'name', 'code', 'slug', 'updated_at')->get();
            foreach ($states as $state) {
                $stateSlug = $state->slug ?? strtolower($state->code ?? '');
                if ($stateSlug) {
                    $xml .= $this->addUrl(
                        $baseUrl . '/mejores/' . $stateSlug,
                        $state->updated_at ?? now()->subWeek(),
                        'weekly',
                        '0.8'
                    );
                }
            }

            // City ranking pages (top 100)
            $cities = $this->getTopCities(100);
            foreach ($cities as $city) {
                if ($city->state_code && $city->city) {
                    $xml .= $this->addUrl(
                        $baseUrl . '/mejores/' . strtolower($city->state_code) . '/' . Str::slug($city->city),
                        $city->last_updated ? Carbon::parse($city->last_updated) : now()->subWeek(),
                        'weekly',
                        '0.8'
                    );
                }
            }

            $xml .= '</urlset>';

            return $xml;
        });

        return $this->xmlResponse($xml);
    }

    /**
     * Get top cities with restaurants
     */
    protected function getTopCities(int $limit)
    {
        return Restaurant::query()
            ->join('states', 'restaurants.state_id', '=', 'states.id')
            ->where('restaurants.status', 'approved')
            ->where('restaurants.is_active', true)
            ->whereNull('restaurants.deleted_at')
            ->select('restaurants.city', 'states.code as state_code')
            ->selectRaw('COUNT(*) as restaurant_count')
            ->selectRaw('MAX(restaurants.updated_at) as last_updated')
            ->groupBy('restaurants.city', 'states.code')
            ->having('restaurant_count', '>=', 1)
            ->orderByDesc('restaurant_count')
            ->limit($limit)
            ->get();
    }

    /**
     * Get base URL from domain
     */
    protected function getBaseUrl(string $domain): string
    {
        return match (true) {
            str_contains($domain, 'famousmexicanrestaurants') => 'https://famousmexicanrestaurants.com',
            str_contains($domain, '.com.mx') => 'https://restaurantesmexicanosfamosos.com.mx',
            str_contains($domain, 'restaurantesmexicanosfamosos') => 'https://restaurantesmexicanosfamosos.com',
            default => url('/'),
        };
    }

    /**
     * Open urlset XML tag
     */
    protected function openUrlset(): string
    {
        return '<?xml version="1.0" encoding="UTF-8"?>'
            . '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
    }

    /**
     * Add URL entry to sitemap
     */
    protected function addUrl(string $url, $lastmod, string $changefreq, string $priority): string
    {
        return '<url>'
            . '<loc>' . htmlspecialchars($url) . '</loc>'
            . '<lastmod>' . $lastmod->format('Y-m-d') . '</lastmod>'
            . '<changefreq>' . $changefreq . '</changefreq>'
            . '<priority>' . $priority . '</priority>'
            . '</url>';
    }

    /**
     * Return XML response with proper cache headers
     */
    protected function xmlResponse(string $xml): Response
    {
        return response($xml, 200)
            ->header('Content-Type', 'application/xml; charset=UTF-8')
            ->header('Cache-Control', 'public, max-age=3600, s-maxage=86400');
    }
}
