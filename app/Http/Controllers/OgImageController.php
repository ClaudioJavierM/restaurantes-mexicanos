<?php

namespace App\Http\Controllers;

use App\Models\Restaurant;
use App\Services\OgImageService;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

class OgImageController extends Controller
{
    public function __construct(private readonly OgImageService $ogImageService) {}

    /**
     * Generate (or serve cached) a dynamic OG image for a restaurant.
     *
     * Route: GET /og-image/{slug}.jpg
     *
     * Returns a redirect to the generated/cached image URL so that social
     * crawlers (WhatsApp, Facebook, Twitter) can cache it at the direct URL.
     */
    public function show(string $slug): Response|\Illuminate\Http\RedirectResponse
    {
        $restaurant = Restaurant::approved()
            ->where('slug', $slug)
            ->with(['state', 'category'])
            ->firstOrFail();

        $imageUrl = $this->ogImageService->generate($restaurant);

        // Redirect to the actual image file. Social crawlers follow this redirect
        // and cache the final URL independently.
        return redirect($imageUrl, 302);
    }
}
