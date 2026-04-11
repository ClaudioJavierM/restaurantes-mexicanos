<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetPublicCacheHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        // Disable Livewire's back button cache prevention for public pages
        if ($request->isMethod('GET') && !$request->user()) {
            $path = $request->path();
            $isPrivatePath = str_starts_with($path, 'owner')
                || str_starts_with($path, 'admin')
                || str_starts_with($path, 'livewire')
                || str_starts_with($path, 'login')
                || str_starts_with($path, 'register');

            // Stateful Livewire pages must never be browser-cached
            $isStatefulPage = in_array($path, ['claim', 'sugerir', 'votar', 'checkout', 'catering'])
                || str_starts_with($path, 'claim');

            if (!$request->ajax() && !$isPrivatePath && !$isStatefulPage) {
                // Disable Livewire's cache buster before it runs
                \Livewire\Features\SupportDisablingBackButtonCache\SupportDisablingBackButtonCache::$disableBackButtonCache = false;
            }
        }

        $response = $next($request);

        // Override headers after all middleware have run
        if ($request->isMethod('GET') && !$request->user()) {
            $path = $request->path();
            $isPrivatePath = str_starts_with($path, 'owner')
                || str_starts_with($path, 'admin')
                || str_starts_with($path, 'livewire')
                || str_starts_with($path, 'login')
                || str_starts_with($path, 'register');

            // Stateful Livewire pages must never be browser-cached
            $isStatefulPage = in_array($path, ['claim', 'sugerir', 'votar', 'checkout', 'catering'])
                || str_starts_with($path, 'claim');

            if (!$request->ajax() && !$isPrivatePath && !$isStatefulPage) {
                $response->headers->set('Cache-Control', 'public, max-age=60, s-maxage=300');
                $response->headers->remove('Pragma');
                $response->headers->remove('Expires');
            } elseif ($isStatefulPage) {
                $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate');
            }
        }

        return $response;
    }
}
