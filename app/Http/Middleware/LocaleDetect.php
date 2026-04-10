<?php

namespace App\Http\Middleware;

use Closure;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

/**
 * Locale detection middleware with session persistence and ?lang= override.
 *
 * Domain rules:
 *   famousmexicanrestaurants.com  → 'en'
 *   everything else               → 'es'
 *
 * Override: append ?lang=en or ?lang=es to any URL to force a locale.
 * The override is stored in the session for the remainder of the visit.
 *
 * NOTE: DomainLocale.php already handles basic domain→locale mapping.
 * This middleware extends it with ?lang= query-param support and session
 * persistence. It is registered AFTER DomainLocale in bootstrap/app.php,
 * so it can override what DomainLocale set.
 *
 * Registration (already in bootstrap/app.php):
 *   ->withMiddleware(function (Middleware $m) {
 *       $m->web(append: [
 *           \App\Http\Middleware\DomainLocale::class,
 *           \App\Http\Middleware\SetLocaleFromDomain::class,
 *           \App\Http\Middleware\LocaleDetect::class,  // ← add after the others
 *       ]);
 *   })
 */
class LocaleDetect
{
    private const EN_DOMAINS = [
        'famousmexicanrestaurants.com',
        'www.famousmexicanrestaurants.com',
    ];

    private const SUPPORTED_LOCALES = ['en', 'es'];

    public function handle(Request $request, Closure $next): Response
    {
        $locale = $this->resolveLocale($request);

        App::setLocale($locale);
        Carbon::setLocale($locale);
        session(['locale' => $locale]);

        return $next($request);
    }

    private function resolveLocale(Request $request): string
    {
        // 1. Explicit ?lang= query param takes highest priority (testing / language switch)
        $langParam = $request->query('lang');
        if ($langParam && in_array($langParam, self::SUPPORTED_LOCALES, true)) {
            return $langParam;
        }

        // 2. Session persistence — if user previously chose a locale, honour it
        $sessionLocale = session('locale');
        if ($sessionLocale && in_array($sessionLocale, self::SUPPORTED_LOCALES, true)) {
            return $sessionLocale;
        }

        // 3. Domain-based default
        $host = $request->getHost();
        if (in_array($host, self::EN_DOMAINS, true)) {
            return 'en';
        }

        return 'es';
    }
}
