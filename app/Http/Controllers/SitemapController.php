<?php

namespace App\Http\Controllers;

use App\Models\Restaurant;
use App\Models\State;
use App\Models\Category;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;

class SitemapController extends Controller
{
    /**
     * Generate dynamic sitemap.xml
     */
    public function index(): Response
    {
        // Use domain-specific cache key so each domain gets its own sitemap
        $currentDomain = request()->getHost();
        $cacheKey = 'sitemap_xml_' . md5($currentDomain);

        $xml = Cache::remember($cacheKey, 3600, function () use ($currentDomain) {
            return $this->generateSitemap($currentDomain);
        });

        return response($xml, 200)
            ->header('Content-Type', 'application/xml');
    }

    /**
     * Generate sitemap XML content
     */
    protected function generateSitemap(string $currentDomain): string
    {
        // Set base URL based on domain - handle .com.mx BEFORE .com
        $baseUrl = match(true) {
            str_contains($currentDomain, 'famousmexicanrestaurants') => 'https://famousmexicanrestaurants.com',
            str_contains($currentDomain, '.com.mx') => 'https://restaurantesmexicanosfamosos.com.mx',
            str_contains($currentDomain, 'restaurantesmexicanosfamosos') => 'https://restaurantesmexicanosfamosos.com',
            default => url('/'),
        };

        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

        // Homepage
        $xml .= $this->addUrl($baseUrl . '/', now(), 'daily', '1.0');

        // Restaurants index
        $xml .= $this->addUrl($baseUrl . '/restaurantes', now(), 'daily', '0.9');

        // Suggest page
        $xml .= $this->addUrl($baseUrl . '/sugerir', now()->subMonth(), 'monthly', '0.5');

        // Individual restaurants
        $restaurants = Restaurant::approved()
            ->select('slug', 'updated_at')
            ->get();

        foreach ($restaurants as $restaurant) {
            $xml .= $this->addUrl(
                $baseUrl . '/restaurante/' . $restaurant->slug,
                $restaurant->updated_at,
                'weekly',
                '0.8'
            );
        }

        // States pages (restaurants filtered by state)
        $states = State::has('restaurants')
            ->select('id', 'name', 'code', 'slug', 'updated_at')
            ->get();

        foreach ($states as $state) {
            $xml .= $this->addUrl(
                $baseUrl . '/restaurantes?state=' . urlencode($state->name),
                $state->updated_at ?? now()->subWeek(),
                'daily',
                '0.7'
            );
        }

        // Categories pages (restaurants filtered by category)
        $categories = Category::has('restaurants')
            ->select('slug', 'updated_at')
            ->get();

        foreach ($categories as $category) {
            $xml .= $this->addUrl(
                $baseUrl . '/restaurantes?category=' . $category->slug,
                $category->updated_at ?? now()->subWeek(),
                'daily',
                '0.7'
            );
        }

        // Advanced filter combinations (most common searches)
        // Mexican regions
        $regions = ['oaxaca', 'jalisco', 'michoacan', 'veracruz', 'yucatan', 'sinaloa'];
        foreach ($regions as $region) {
            $xml .= $this->addUrl(
                $baseUrl . '/restaurantes?region=' . $region,
                now()->subWeek(),
                'weekly',
                '0.6'
            );
        }

        // Price ranges
        $priceRanges = ['$', '$$', '$$$'];
        foreach ($priceRanges as $price) {
            $xml .= $this->addUrl(
                $baseUrl . '/restaurantes?price=' . urlencode($price),
                now()->subWeek(),
                'weekly',
                '0.6'
            );
        }

        // City Guides - Individual city pages (top cities with restaurants)
        $cities = Restaurant::query()
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
            ->limit(500) // Top 500 cities
            ->get();

        // SEO Ranking Pages (high priority for competitive keywords)
        $xml .= $this->addUrl($baseUrl . '/mejores-restaurantes-mexicanos', now(), 'weekly', '0.9');
        $xml .= $this->addUrl($baseUrl . '/top-10-restaurantes-mexicanos', now(), 'weekly', '0.9');

        // Ranking pages by state
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

        // Ranking pages by city (top 100 cities for "mejores restaurantes mexicanos en [ciudad]")
        foreach ($cities->take(100) as $city) {
            if ($city->state_code && $city->city) {
                $citySlug = \Str::slug($city->city);
                $xml .= $this->addUrl(
                    $baseUrl . '/mejores/' . strtolower($city->state_code) . '/' . $citySlug,
                    $city->last_updated ? \Carbon\Carbon::parse($city->last_updated) : now()->subWeek(),
                    'weekly',
                    '0.8'
                );
            }
        }

        // City Guides - States listing page
        $xml .= $this->addUrl($baseUrl . '/guia', now(), 'weekly', '0.8');

        // City Guides - Individual state pages
        foreach ($states as $state) {
            $xml .= $this->addUrl(
                $baseUrl . '/guia/' . strtolower($state->code ?? $state->name),
                $state->updated_at ?? now()->subWeek(),
                'weekly',
                '0.7'
            );
        }

        // City Guides - Individual city pages (reuse $cities from above)
        foreach ($cities as $city) {
            if ($city->state_code && $city->city) {
                $citySlug = \Str::slug($city->city);
                $xml .= $this->addUrl(
                    $baseUrl . '/guia/' . strtolower($city->state_code) . '/' . $citySlug,
                    $city->last_updated ? \Carbon\Carbon::parse($city->last_updated) : now()->subWeek(),
                    'weekly',
                    '0.7'
                );
            }
        }

        $xml .= '</urlset>';

        return $xml;
    }

    /**
     * Add URL to sitemap
     */
    protected function addUrl(string $url, $lastmod, string $changefreq, string $priority): string
    {
        $xml = '<url>';
        $xml .= '<loc>' . htmlspecialchars($url) . '</loc>';
        $xml .= '<lastmod>' . $lastmod->format('Y-m-d') . '</lastmod>';
        $xml .= '<changefreq>' . $changefreq . '</changefreq>';
        $xml .= '<priority>' . $priority . '</priority>';
        $xml .= '</url>';

        return $xml;
    }
}
