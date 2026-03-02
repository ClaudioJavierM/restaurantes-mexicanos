<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\CountryContext;
use Symfony\Component\HttpFoundation\Response;

class SetLocaleFromDomain
{
    /**
     * Handle an incoming request.
     *
     * Detects the domain and sets the appropriate country and locale:
     * - restaurantesmexicanosfamosos.com.mx → Mexico, Spanish only
     * - restaurantesmexicanosfamosos.com → USA, Spanish/English
     * - famousmexicanrestaurants.com → USA, English default
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Initialize country context (handles both country and locale)
        CountryContext::initFromRequest($request);

        // Share country data with all views
        view()->share('currentCountry', CountryContext::getCountry());
        view()->share('countryConfig', CountryContext::getConfig());
        view()->share('canSwitchLanguage', CountryContext::canSwitchLanguage());
        view()->share('isMexico', CountryContext::isMexico());
        view()->share('isUSA', CountryContext::isUSA());
        view()->share('countryFlag', CountryContext::getConfig('flag'));

        return $next($request);
    }
}
