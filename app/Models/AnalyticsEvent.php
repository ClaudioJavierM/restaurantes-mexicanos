<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AnalyticsEvent extends Model
{
    protected $fillable = [
        'restaurant_id',
        'event_type',
        'user_type',
        'user_id',
        'ip_address',
        'user_agent',
        'referrer',
        'device_type',
        'browser',
        'platform',
        'country_code',
        'city',
        'region',
        'metadata',
        'session_id',
    ];

    protected $casts = [
        'metadata' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Event types constants
    const EVENT_PAGE_VIEW = 'page_view';
    const EVENT_PHONE_CLICK = 'phone_click';
    const EVENT_WEBSITE_CLICK = 'website_click';
    const EVENT_DIRECTION_CLICK = 'direction_click';
    const EVENT_COUPON_VIEW = 'coupon_view';
    const EVENT_COUPON_CLICK = 'coupon_click';
    const EVENT_PHOTO_VIEW = 'photo_view';
    const EVENT_REVIEW_VIEW = 'review_view';
    const EVENT_MENU_VIEW = 'menu_view';
    const EVENT_SOCIAL_CLICK = 'social_click';

    /**
     * Relationships
     */
    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Track an event
     */
    public static function track(
        int $restaurantId,
        string $eventType,
        ?array $metadata = null,
        ?int $userId = null
    ): ?self {
        // Filter out known bots
        $userAgent = strtolower(request()->userAgent() ?? '');
        $botPatterns = ['bot', 'crawler', 'spider', 'semrush', 'ahrefs', 'yandex', 'bingbot', 'googlebot', 'facebook', 'facebookexternalhit', 'meta-externalagent', 'meta-webindexer', 'mfgroup-seo', 'bytespider', 'gptbot', 'claudebot', 'applebot', 'duckduckbot', 'slurp', 'baidu', 'sogou', 'ia_archiver', 'archive.org'];
        foreach ($botPatterns as $pattern) {
            if (str_contains($userAgent, $pattern)) {
                return null;
            }
        }

        $request = request();

        return self::create([
            'restaurant_id' => $restaurantId,
            'event_type' => $eventType,
            'user_type' => auth()->check() ? 'authenticated' : 'guest',
            'user_id' => $userId ?? auth()->id(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'referrer' => $request->headers->get('referer'),
            'device_type' => self::detectDeviceType($request->userAgent()),
            'browser' => self::detectBrowser($request->userAgent()),
            'platform' => self::detectPlatform($request->userAgent()),
            'session_id' => session()->getId(),
            'metadata' => $metadata,
        ]);
    }

    /**
     * Get analytics for a restaurant within a date range
     */
    public static function getRestaurantAnalytics(
        int $restaurantId,
        Carbon $startDate,
        Carbon $endDate
    ): array {
        $events = self::where('restaurant_id', $restaurantId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        return [
            'total_views' => $events->where('event_type', self::EVENT_PAGE_VIEW)->count(),
            'phone_clicks' => $events->where('event_type', self::EVENT_PHONE_CLICK)->count(),
            'website_clicks' => $events->where('event_type', self::EVENT_WEBSITE_CLICK)->count(),
            'direction_clicks' => $events->where('event_type', self::EVENT_DIRECTION_CLICK)->count(),
            'coupon_views' => $events->where('event_type', self::EVENT_COUPON_VIEW)->count(),
            'coupon_clicks' => $events->where('event_type', self::EVENT_COUPON_CLICK)->count(),
            'photo_views' => $events->where('event_type', self::EVENT_PHOTO_VIEW)->count(),
            'unique_visitors' => $events->unique('session_id')->count(),
            'mobile_percentage' => $events->where('device_type', 'mobile')->count() / max($events->count(), 1) * 100,
        ];
    }

    /**
     * Get daily analytics for chart
     */
    public static function getDailyAnalytics(
        int $restaurantId,
        Carbon $startDate,
        Carbon $endDate,
        ?string $eventType = null
    ): array {
        $query = self::where('restaurant_id', $restaurantId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('date')
            ->orderBy('date');

        if ($eventType) {
            $query->where('event_type', $eventType);
        }

        return $query->get()->pluck('count', 'date')->toArray();
    }

    /**
     * Get top referring sources
     */
    public static function getTopReferrers(
        int $restaurantId,
        Carbon $startDate,
        Carbon $endDate,
        int $limit = 10
    ): array {
        return self::where('restaurant_id', $restaurantId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->whereNotNull('referrer')
            ->select('referrer', DB::raw('COUNT(*) as count'))
            ->groupBy('referrer')
            ->orderByDesc('count')
            ->limit($limit)
            ->get()
            ->pluck('count', 'referrer')
            ->toArray();
    }

    /**
     * Get device breakdown
     */
    public static function getDeviceBreakdown(
        int $restaurantId,
        Carbon $startDate,
        Carbon $endDate
    ): array {
        return self::where('restaurant_id', $restaurantId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select('device_type', DB::raw('COUNT(*) as count'))
            ->groupBy('device_type')
            ->get()
            ->pluck('count', 'device_type')
            ->toArray();
    }

    /**
     * Detect device type from user agent
     */
    protected static function detectDeviceType(?string $userAgent): string
    {
        if (!$userAgent) {
            return 'unknown';
        }

        if (preg_match('/mobile|android|iphone|ipod|blackberry|iemobile|opera mini/i', $userAgent)) {
            return 'mobile';
        }

        if (preg_match('/tablet|ipad|playbook|silk/i', $userAgent)) {
            return 'tablet';
        }

        return 'desktop';
    }

    /**
     * Detect browser from user agent
     */
    protected static function detectBrowser(?string $userAgent): ?string
    {
        if (!$userAgent) {
            return null;
        }

        if (preg_match('/Edge/i', $userAgent)) return 'Edge';
        if (preg_match('/Chrome/i', $userAgent)) return 'Chrome';
        if (preg_match('/Safari/i', $userAgent)) return 'Safari';
        if (preg_match('/Firefox/i', $userAgent)) return 'Firefox';
        if (preg_match('/MSIE|Trident/i', $userAgent)) return 'Internet Explorer';
        if (preg_match('/Opera/i', $userAgent)) return 'Opera';

        return 'Other';
    }

    /**
     * Detect platform from user agent
     */
    protected static function detectPlatform(?string $userAgent): ?string
    {
        if (!$userAgent) {
            return null;
        }

        if (preg_match('/windows/i', $userAgent)) return 'Windows';
        if (preg_match('/mac os/i', $userAgent)) return 'macOS';
        if (preg_match('/linux/i', $userAgent)) return 'Linux';
        if (preg_match('/android/i', $userAgent)) return 'Android';
        if (preg_match('/ios|iphone|ipad|ipod/i', $userAgent)) return 'iOS';

        return 'Other';
    }
}
