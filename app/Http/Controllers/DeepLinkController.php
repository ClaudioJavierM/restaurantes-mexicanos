<?php

namespace App\Http\Controllers;

use App\Models\AnalyticsEvent;
use App\Models\Restaurant;
use Illuminate\Http\Request;

class DeepLinkController extends Controller
{
    /**
     * Handle restaurant deep links from QR codes
     * URL: /r/{slug}?source=qr&campaign=table_1
     */
    public function restaurant(Request $request, string $slug)
    {
        $restaurant = Restaurant::where('slug', $slug)
            ->where('status', 'approved')
            ->first();

        if (!$restaurant) {
            abort(404);
        }

        // Track the QR scan
        $this->trackDeepLink($request, $restaurant, 'restaurant');

        // Redirect to the appropriate page based on parameters
        $action = $request->get('action', 'view');

        return match ($action) {
            'menu' => redirect()->route('restaurants.show', ['slug' => $slug, '#menu']),
            'order' => redirect()->route('restaurant.menu', $slug),
            'reserve' => redirect()->route('restaurants.show', $slug)->with('openReservation', true),
            default => redirect()->route('restaurants.show', $slug),
        };
    }

    /**
     * Handle PWA app deep links
     * URL: /app/{slug}?source=qr
     */
    public function pwaApp(Request $request, string $slug)
    {
        $restaurant = Restaurant::where('slug', $slug)
            ->where('status', 'approved')
            ->first();

        if (!$restaurant) {
            abort(404);
        }

        // Track the deep link access
        $this->trackDeepLink($request, $restaurant, 'pwa_app');

        // For PWA, we'll redirect to a dedicated PWA view
        return redirect()->route('pwa.restaurant', $slug);
    }

    /**
     * Handle menu QR deep links (for restaurants with digital menu)
     * URL: /menu/{slug}?source=qr&table=5
     */
    public function menu(Request $request, string $slug)
    {
        $restaurant = Restaurant::where('slug', $slug)
            ->where('status', 'approved')
            ->first();

        if (!$restaurant) {
            abort(404);
        }

        // Track the menu access
        $this->trackDeepLink($request, $restaurant, 'menu_qr', [
            'table' => $request->get('table'),
        ]);

        // Store table number in session for order association
        if ($request->has('table')) {
            session(['table_number' => $request->get('table')]);
        }

        return redirect()->route('restaurant.menu', $slug);
    }

    /**
     * Generate a trackable QR code URL
     */
    public static function generateQrUrl(Restaurant $restaurant, string $type = 'menu', array $params = []): string
    {
        $baseParams = [
            'source' => 'qr',
            'utm_medium' => 'qr_code',
            'utm_campaign' => $type,
            'ts' => time(), // Timestamp for cache busting
        ];

        $allParams = array_merge($baseParams, $params);

        $route = match ($type) {
            'app' => route('deeplink.pwa', $restaurant->slug),
            'menu' => route('deeplink.menu', $restaurant->slug),
            'order' => route('deeplink.restaurant', ['slug' => $restaurant->slug, 'action' => 'order']),
            default => route('deeplink.restaurant', $restaurant->slug),
        };

        return $route . '?' . http_build_query($allParams);
    }

    /**
     * Track the deep link access
     */
    protected function trackDeepLink(Request $request, Restaurant $restaurant, string $linkType, array $extraData = []): void
    {
        try {
            AnalyticsEvent::create([
                'restaurant_id' => $restaurant->id,
                'user_id' => auth()->id(),
                'event_type' => 'qr_scan',
                'page_url' => $request->fullUrl(),
                'referrer' => $request->get('source', 'unknown'),
                'user_agent' => $request->userAgent(),
                'ip_address' => $request->ip(),
                'session_id' => session()->getId(),
                'metadata' => array_merge([
                    'link_type' => $linkType,
                    'source' => $request->get('source'),
                    'campaign' => $request->get('campaign') ?? $request->get('utm_campaign'),
                    'table' => $request->get('table'),
                    'device' => $this->detectDevice($request),
                ], $extraData),
            ]);
        } catch (\Exception $e) {
            // Don't fail on tracking errors
            \Log::warning('Failed to track deep link: ' . $e->getMessage());
        }
    }

    /**
     * Detect device type from user agent
     */
    protected function detectDevice(Request $request): string
    {
        $userAgent = strtolower($request->userAgent() ?? '');

        if (str_contains($userAgent, 'iphone') || str_contains($userAgent, 'ipad')) {
            return 'ios';
        }

        if (str_contains($userAgent, 'android')) {
            return 'android';
        }

        if (str_contains($userAgent, 'mobile')) {
            return 'mobile_other';
        }

        return 'desktop';
    }
}
