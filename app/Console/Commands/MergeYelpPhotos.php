<?php

namespace App\Console\Commands;

use App\Models\Restaurant;
use Illuminate\Console\Command;

class MergeYelpPhotos extends Command
{
    protected $signature = 'restaurants:merge-yelp-photos
                            {--limit=500 : Number of restaurants to process}
                            {--force : Re-merge even if already merged}';

    protected $description = 'Merge yelp_photos URLs into the photos JSON column (Yelp appended after Google photos)';

    public function handle()
    {
        $query = Restaurant::whereNotNull('yelp_photos')
            ->where('yelp_photos', '!=', '[]')
            ->where('yelp_photos', '!=', '');

        if (!$this->option('force')) {
            // Only process restaurants where photos doesn't already contain a Yelp URL
            // We detect Yelp photos by checking if any item in photos starts with 'http'
            $query->where(function ($q) {
                $q->whereNull('photos')
                  ->orWhere('photos', '[]')
                  ->orWhere('photos', '')
                  ->orWhereRaw("JSON_SEARCH(photos, 'one', 'https://s3%', NULL, '$[*]') IS NULL");
            });
        }

        $total = $query->count();
        $this->info("Found {$total} restaurants to process");

        $merged = 0;
        $skipped = 0;

        $query->limit($this->option('limit'))->chunk(100, function ($restaurants) use (&$merged, &$skipped) {
            foreach ($restaurants as $restaurant) {
                $yelpPhotos = $restaurant->yelp_photos ?? [];
                if (empty($yelpPhotos)) {
                    $skipped++;
                    continue;
                }

                $currentPhotos = $restaurant->photos ?? [];

                // Don't add Yelp URLs already in photos
                $existingUrls = array_filter($currentPhotos, fn ($p) => str_starts_with($p, 'http'));
                $newYelpPhotos = array_filter($yelpPhotos, fn ($url) => !in_array($url, $existingUrls));

                if (empty($newYelpPhotos)) {
                    $skipped++;
                    continue;
                }

                // Merge: Google photos first, then Yelp (cap at 13 total)
                $merged_photos = array_slice(array_merge($currentPhotos, array_values($newYelpPhotos)), 0, 13);

                // Use updateQuietly to skip observer (no geocoding, no cache clear)
                $restaurant->updateQuietly(['photos' => $merged_photos]);
                $merged++;
            }
        });

        $this->info("Done — Merged: {$merged} | Skipped: {$skipped}");
        return 0;
    }
}
