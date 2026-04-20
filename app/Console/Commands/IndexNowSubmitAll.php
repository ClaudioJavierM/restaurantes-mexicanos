<?php

namespace App\Console\Commands;

use App\Models\Restaurant;
use App\Models\State;
use App\Services\IndexNowService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class IndexNowSubmitAll extends Command
{
    protected $signature = 'indexnow:submit-all
                            {--type=all : all|restaurants|states|cities|dishes}';

    protected $description = 'Submit all FAMER pages to IndexNow for Bing/Yandex indexing';

    private const CHUNK_SIZE = 500;

    private const DISHES = [
        'birria', 'tacos', 'tamales', 'pozole', 'carnitas',
        'barbacoa', 'mole', 'enchiladas', 'quesadillas', 'fajitas',
        'churros', 'horchata', 'margaritas',
    ];

    private const DISH_STATES = [
        'tx', 'ca', 'il', 'az', 'fl', 'co', 'nv', 'nm',
        'ny', 'ga', 'wa', 'nc', 'or', 'ut', 'tn',
    ];

    public function handle(): int
    {
        $type = $this->option('type');
        $total = 0;

        $this->info("IndexNow: starting bulk submission — type=[{$type}]");

        if ($type === 'all' || $type === 'restaurants') {
            $total += $this->submitRestaurants();
        }

        if ($type === 'all' || $type === 'states') {
            $total += $this->submitStates();
        }

        if ($type === 'all' || $type === 'cities') {
            $total += $this->submitCities();
        }

        if ($type === 'all' || $type === 'dishes') {
            $total += $this->submitDishes();
        }

        $this->info("IndexNow: done — {$total} total URLs submitted across all sections.");

        return self::SUCCESS;
    }

    private function submitRestaurants(): int
    {
        $this->info('--- Restaurants ---');

        $slugs = Restaurant::where('status', 'approved')
            ->whereNotNull('slug')
            ->pluck('slug')
            ->toArray();

        $count = count($slugs);
        $this->info("Found {$count} approved restaurants.");

        $chunks = array_chunk($slugs, self::CHUNK_SIZE);
        $submitted = 0;

        foreach ($chunks as $i => $chunk) {
            IndexNowService::notifyBatch($chunk);
            // notifyBatch submits 3 URLs per slug (3 FAMER domains)
            $submitted += count($chunk) * 3;
            $chunkNum = $i + 1;
            $total = count($chunks);
            $this->info("  Chunk {$chunkNum}/{$total} submitted (" . count($chunk) . " slugs → " . (count($chunk) * 3) . " URLs)");
        }

        $this->info("Restaurants: {$submitted} URLs submitted.");

        return $submitted;
    }

    private function submitStates(): int
    {
        $this->info('--- States ---');

        $states = State::where('is_active', true)->get(['name', 'code']);

        if ($states->isEmpty()) {
            $this->warn('No active states found.');
            return 0;
        }

        $urls = $states->map(function ($state) {
            $slug = Str::slug($state->name);
            return 'https://restaurantesmexicanosfamosos.com.mx/restaurantes-mexicanos-en-' . $slug;
        })->toArray();

        $this->info('Found ' . count($urls) . ' state pages.');

        $chunks = array_chunk($urls, self::CHUNK_SIZE);
        $submitted = 0;

        foreach ($chunks as $i => $chunk) {
            IndexNowService::submitUrls($chunk, 'restaurantesmexicanosfamosos.com.mx');
            $submitted += count($chunk);
            $chunkNum = $i + 1;
            $total = count($chunks);
            $this->info("  Chunk {$chunkNum}/{$total} submitted (" . count($chunk) . " URLs)");
        }

        $this->info("States: {$submitted} URLs submitted.");

        return $submitted;
    }

    private function submitCities(): int
    {
        $this->info('--- Cities (top 200) ---');

        $cities = DB::table('restaurants')
            ->join('states', 'restaurants.state_id', '=', 'states.id')
            ->where('restaurants.status', 'approved')
            ->whereNotNull('restaurants.city')
            ->groupBy('restaurants.city', 'states.code')
            ->selectRaw('restaurants.city, states.code as state_code, COUNT(*) as cnt')
            ->orderByDesc('cnt')
            ->limit(200)
            ->get();

        if ($cities->isEmpty()) {
            $this->warn('No cities found.');
            return 0;
        }

        $urls = $cities->map(function ($row) {
            $citySlug  = Str::slug($row->city);
            $stateCode = strtolower((string) $row->state_code);
            return 'https://restaurantesmexicanosfamosos.com.mx/restaurantes-en-' . $citySlug . '-' . $stateCode;
        })->toArray();

        $this->info('Found ' . count($urls) . ' city pages.');

        $chunks = array_chunk($urls, self::CHUNK_SIZE);
        $submitted = 0;

        foreach ($chunks as $i => $chunk) {
            IndexNowService::submitUrls($chunk, 'restaurantesmexicanosfamosos.com.mx');
            $submitted += count($chunk);
            $chunkNum = $i + 1;
            $total = count($chunks);
            $this->info("  Chunk {$chunkNum}/{$total} submitted (" . count($chunk) . " URLs)");
        }

        $this->info("Cities: {$submitted} URLs submitted.");

        return $submitted;
    }

    private function submitDishes(): int
    {
        $this->info('--- Dishes × States ---');

        $urls = [];

        foreach (self::DISHES as $dish) {
            foreach (self::DISH_STATES as $stateCode) {
                $urls[] = 'https://restaurantesmexicanosfamosos.com.mx/' . $dish . '-en-' . $stateCode;
            }
        }

        $this->info('Found ' . count($urls) . ' dish×state combination pages.');

        $chunks = array_chunk($urls, self::CHUNK_SIZE);
        $submitted = 0;

        foreach ($chunks as $i => $chunk) {
            IndexNowService::submitUrls($chunk, 'restaurantesmexicanosfamosos.com.mx');
            $submitted += count($chunk);
            $chunkNum = $i + 1;
            $total = count($chunks);
            $this->info("  Chunk {$chunkNum}/{$total} submitted (" . count($chunk) . " URLs)");
        }

        $this->info("Dishes: {$submitted} URLs submitted.");

        return $submitted;
    }
}
