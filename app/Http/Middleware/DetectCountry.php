<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\CountryContext;
use Symfony\Component\HttpFoundation\Response;

class DetectCountry
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Initialize country context from the request domain
        CountryContext::initFromRequest($request);

        // Share country data with all views
        view()->share('currentCountry', CountryContext::getCountry());
        view()->share('countryConfig', CountryContext::getConfig());
        view()->share('canSwitchLanguage', CountryContext::canSwitchLanguage());
        view()->share('isMexico', CountryContext::isMexico());
        view()->share('isUSA', CountryContext::isUSA());

        return $next($request);
    }
}
