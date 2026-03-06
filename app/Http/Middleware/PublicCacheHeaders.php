<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Override Livewire's aggressive no-cache headers for public-facing pages.
 * This allows Googlebot and CDNs to cache public content properly.
 */
class PublicCacheHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Only modify successful HTML responses
        if ($response->getStatusCode() !== 200) {
            return $response;
        }

        // Don't touch authenticated/private pages
        if (auth()->check()) {
            return $response;
        }

        // Don't touch Livewire AJAX update requests
        if ($request->hasHeader('X-Livewire')) {
            return $response;
        }

        // Set public cache headers for SEO
        $response->headers->set('Cache-Control', 'public, max-age=300, s-maxage=3600');
        $response->headers->remove('Pragma');
        $response->headers->remove('Expires');

        return $response;
    }
}
