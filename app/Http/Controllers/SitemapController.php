<?php

namespace App\Http\Controllers;

use App\Models\Restaurant;
use App\Models\State;
use App\Models\Category;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Carbon\Carbon;

class SitemapController extends Controller
{
    /**
     * Sitemap index — links to sub-sitemaps.
     * Google recommends splitting large sitemaps for faster processing.
     */
    public function index(): Response
    {
        $baseUrl = $this->getBaseUrl();
        $cacheKey = 'sitemap_index_' . md5($baseUrl);

        $xml = Cache::remember($cacheKey, 3600, function () use ($baseUrl) {
            $xml = '<?xml version="1.0" encoding="UTF-8"?>';
            $xml .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

            $xml .= '<sitemap>';
            $xml .= '<loc>' . $baseUrl . '/sitemap-main.xml</loc>';
            $xml .= '<lastmod>' . now()->format('Y-m-d') . '</lastmod>';
            $xml .= '</sitemap>';

            // Split restaurants into chunks of 5,000
            $totalRestaurants = Restaurant::approved()->count();
            $chunks = ceil($totalRestaurants / 5000);

            for ($i = 1; $i <= $chunks; $i++) {
                $xml .= '<sitemap>';
                $xml .= '<loc>' . $baseUrl . '/sitemap-restaurants-' . $i . '.xml</loc>';
                $xml .= '<lastmod>' . now()->format('Y-m-d') . '</lastmod>';
                $xml .= '</sitemap>';
            }

            $xml .= '<sitemap>';
            $xml .= '<loc>' . $baseUrl . '/sitemap-guides.xml</loc>';
            $xml .= '<lastmod>' . now()->format('Y-m-d') . '</lastmod>';
            $xml .= '</sitemap>';

            $xml .= '<sitemap>';
            $xml .= '<loc>' . $baseUrl . '/sitemap-rankings.xml</loc>';
            $xml .= '<lastmod>' . now()->format('Y-m-d') . '</lastmod>';
            $xml .= '</sitemap>';

            $xml .= '</sitemapindex>';

            return $xml;
        });

        return $this->xmlResponse($xml);
    }

    /**
     * Main pages sitemap (homepage, static pages, categories).
     */
    public function main(): Response
    {
        $baseUrl = $this->getBaseUrl();
        $cacheKey = 'sitemap_main_' . md5($baseUrl);

        $xml = Cache::remember($cacheKey, 3600, function () use ($baseUrl) {
            $xml = $this->openUrlset();

            // Homepage
            $xml .= $this->addUrl($baseUrl . '/', now(), 'daily', '1.0');

            // Restaurant listing
            $xml .= $this->addUrl($baseUrl . '/restaurantes', now(), 'daily', '0.9');

            // Suggest page
            $xml .= $this->addUrl($baseUrl . '/sugerir', now()->subMonth(), 'monthly', '0.5');

            // Category pages (clean URLs, not query strings)
            $categories = Category::has('restaurants')->select('slug', 'updated_at')->get();
            foreach ($categories as $category) {
                $xml .= $this->addUrl(
                    $baseUrl . '/restaurantes/categoria/' . $category->slug,
                    $category->updated_at ?? now()->subWeek(),
                    'daily',
                    '0.7'
                );
            }

            $xml .= '</urlset>';
            return $xml;
        });

        return $this->xmlResponse($xml);
    }

    /**
     * Restaurant pages sitemap (paginated, 5,000 per file).
     */
    public function restaurants(int $page = 1): Response
    {
        $baseUrl = $this->getBaseUrl();
        $cacheKey = 'sitemap_restaurants_' . $page . '_' . md5($baseUrl);

        $xml = Cache::remember($cacheKey, 3600, function () use ($baseUrl, $page) {
            $xml = $this->openUrlset();

            $restaurants = Restaurant::approved()
                ->select('slug', 'updated_at')
                ->orderBy('id')
                ->offset(($page - 1) * 5000)
                ->limit(5000)
                ->get();

            foreach ($restaurants as $restaurant) {
                $xml .= $this->addUrl(
                    $baseUrl . $this->getRestaurantPath() . $restaurant->slug,
                    $restaurant->updated_at,
                    'weekly',
                    '0.8'
                );
            }

            $xml .= '</urlset>';
            return $xml;
        });

        return $this->xmlResponse($xml);
    }

    /**
     * City guides sitemap.
     */
    public function guides(): Response
    {
        $baseUrl = $this->getBaseUrl();
        $cacheKey = 'sitemap_guides_' . md5($baseUrl);

        $xml = Cache::remember($cacheKey, 3600, function () use ($baseUrl) {
            $xml = $this->openUrlset();

            // Guides index
            $xml .= $this->addUrl($baseUrl . '/guia', now(), 'weekly', '0.8');

            $states = State::has('restaurants')
                ->select('id', 'name', 'code', 'slug', 'updated_at')
                ->get();

            // State guide pages
            foreach ($states as $state) {
                $xml .= $this->addUrl(
                    $baseUrl . '/guia/' . strtolower($state->code ?? $state->name),
                    $state->updated_at ?? now()->subWeek(),
                    'weekly',
                    '0.7'
                );
            }

            // City guide pages
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
     * Rankings sitemap ("mejores restaurantes mexicanos en...").
     */
    public function rankings(): Response
    {
        $baseUrl = $this->getBaseUrl();
        $cacheKey = 'sitemap_rankings_' . md5($baseUrl);

        $xml = Cache::remember($cacheKey, 3600, function () use ($baseUrl) {
            $xml = $this->openUrlset();

            // Top-level ranking pages
            $xml .= $this->addUrl($baseUrl . '/mejores-restaurantes-mexicanos', now(), 'weekly', '0.9');
            $xml .= $this->addUrl($baseUrl . '/top-10-restaurantes-mexicanos', now(), 'weekly', '0.9');

            $states = State::has('restaurants')
                ->select('id', 'name', 'code', 'slug', 'updated_at')
                ->get();

            // State ranking pages
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

    // ─── Helpers ──────────────────────────────────────────

    protected function getBaseUrl(): string
    {
        $currentDomain = request()->getHost();

        return match (true) {
            str_contains($currentDomain, 'famousmexicanrestaurants') => 'https://famousmexicanrestaurants.com',
            str_contains($currentDomain, '.com.mx') => 'https://restaurantesmexicanosfamosos.com.mx',
            str_contains($currentDomain, 'restaurantesmexicanosfamosos') => 'https://restaurantesmexicanosfamosos.com',
            default => url('/'),
        };
    }

    protected function getRestaurantPath(): string
    {
        $host = request()->getHost();
        return str_contains($host, 'famousmexicanrestaurants') ? '/restaurant/' : '/restaurante/';
    }

    protected function getTopCities(int $limit): \Illuminate\Support\Collection
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

    protected function openUrlset(): string
    {
        return '<?xml version="1.0" encoding="UTF-8"?>'
            . '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
    }

    protected function addUrl(string $url, $lastmod, string $changefreq, string $priority): string
    {
        return '<url>'
            . '<loc>' . htmlspecialchars($url) . '</loc>'
            . '<lastmod>' . $lastmod->format('Y-m-d') . '</lastmod>'
            . '<changefreq>' . $changefreq . '</changefreq>'
            . '<priority>' . $priority . '</priority>'
            . '</url>';
    }

    protected function xmlResponse(string $xml): Response
    {
        return response($xml, 200)
            ->header('Content-Type', 'application/xml')
            ->header('Cache-Control', 'public, max-age=3600, s-maxage=86400')
            ->header('Content-Encoding', 'identity');
    }
}
