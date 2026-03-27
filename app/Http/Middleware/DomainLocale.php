<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class DomainLocale
{
    private const EN_DOMAINS = [
        'famousmexicanrestaurants.com',
        'www.famousmexicanrestaurants.com',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $host = $request->getHost();

        if (in_array($host, self::EN_DOMAINS)) {
            App::setLocale('en');
            config(['app.locale' => 'en']);
        } else {
            App::setLocale('es');
            config(['app.locale' => 'es']);
        }

        return $next($request);
    }
}
