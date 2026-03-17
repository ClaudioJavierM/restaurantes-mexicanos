<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use App\Models\Restaurant;
use App\Models\YelpCityProgress;

class ImportWebhookController extends Controller
{
    /**
     * N8N webhook to trigger Yelp imports
     *
     * Endpoints:
     * - POST /webhooks/import/smart - Run smart import
     * - POST /webhooks/import/status - Get import status
     * - POST /webhooks/import/reset-exhausted - Reset exhausted cities
     */
    public function smartImport(Request $request)
    {
        if (!$this->validateSecret($request)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $cities = min((int) $request->input('cities', 30), 100);
        $limit = min((int) $request->input('limit', 50), 100);
        $minRating = $request->input('min_rating', '3.5');
        $delay = min((int) $request->input('delay', 2), 10);

        Log::info('Import webhook triggered', [
            'cities' => $cities,
            'limit' => $limit,
            'min_rating' => $minRating,
        ]);

        // Run the command in the background
        $exitCode = Artisan::call('yelp:import-smart', [
            '--cities' => $cities,
            '--limit' => $limit,
            '--min-rating' => $minRating,
            '--delay' => $delay,
        ]);

        $output = Artisan::output();

        // Parse results from output
        $results = $this->parseImportOutput($output);

        return response()->json([
            'success' => $exitCode === 0,
            'action' => 'smart_import',
            'parameters' => [
                'cities' => $cities,
                'limit' => $limit,
                'min_rating' => $minRating,
            ],
            'results' => $results,
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    /**
     * Get current import status and statistics
     */
    public function status(Request $request)
    {
        if (!$this->validateSecret($request)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Get stats
        $totalRestaurants = Restaurant::count();
        $approvedRestaurants = Restaurant::approved()->count();
        $todayImported = Restaurant::whereDate('created_at', today())->count();
        $weekImported = Restaurant::where('created_at', '>=', now()->subWeek())->count();

        // Get city progress stats if table exists
        $cityStats = [
            'active' => 0,
            'exhausted' => 0,
            'total' => 0,
        ];

        try {
            if (class_exists(YelpCityProgress::class)) {
                $cityStats = [
                    'active' => YelpCityProgress::where('is_exhausted', false)->count(),
                    'exhausted' => YelpCityProgress::where('is_exhausted', true)->count(),
                    'total' => YelpCityProgress::count(),
                ];
            }
        } catch (\Exception $e) {
            // Table might not exist
        }

        // Get recent imports by state
        $recentByState = Restaurant::query()
            ->where('created_at', '>=', now()->subDay())
            ->join('states', 'restaurants.state_id', '=', 'states.id')
            ->selectRaw('states.code, COUNT(*) as count')
            ->groupBy('states.code')
            ->orderByDesc('count')
            ->limit(10)
            ->get()
            ->pluck('count', 'code')
            ->toArray();

        return response()->json([
            'success' => true,
            'statistics' => [
                'total_restaurants' => $totalRestaurants,
                'approved_restaurants' => $approvedRestaurants,
                'imported_today' => $todayImported,
                'imported_this_week' => $weekImported,
            ],
            'city_progress' => $cityStats,
            'recent_by_state' => $recentByState,
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    /**
     * Reset exhausted cities to try importing again
     */
    public function resetExhausted(Request $request)
    {
        if (!$this->validateSecret($request)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $exitCode = Artisan::call('yelp:import-smart', [
            '--reset-exhausted' => true,
            '--cities' => 0, // Don't import, just reset
        ]);

        Log::info('Reset exhausted cities triggered via webhook');

        return response()->json([
            'success' => $exitCode === 0,
            'action' => 'reset_exhausted',
            'message' => 'Exhausted cities have been reset',
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    /**
     * Run bulk import for specific states
     */
    public function bulkImport(Request $request)
    {
        if (!$this->validateSecret($request)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $states = $request->input('states', []);
        $citiesPerState = min((int) $request->input('cities_per_state', 5), 20);
        $limit = min((int) $request->input('limit', 50), 100);
        $minRating = $request->input('min_rating', '3.5');

        if (empty($states)) {
            return response()->json(['error' => 'No states provided'], 400);
        }

        Log::info('Bulk import webhook triggered', [
            'states' => $states,
            'cities_per_state' => $citiesPerState,
        ]);

        $args = [
            '--cities-per-state' => $citiesPerState,
            '--limit' => $limit,
            '--min-rating' => $minRating,
            '--delay' => 2,
        ];

        // Add each state
        foreach ((array) $states as $state) {
            $args['--states'][] = strtoupper($state);
        }

        $exitCode = Artisan::call('yelp:import-bulk', $args);
        $output = Artisan::output();
        $results = $this->parseImportOutput($output);

        return response()->json([
            'success' => $exitCode === 0,
            'action' => 'bulk_import',
            'states' => $states,
            'results' => $results,
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    /**
     * Parse import command output to extract results
     */
    private function parseImportOutput(string $output): array
    {
        $results = [
            'new_restaurants' => 0,
            'duplicates' => 0,
            'cities_processed' => 0,
            'raw_output' => substr($output, 0, 2000), // Truncate for JSON
        ];

        // Try to parse numbers from output
        if (preg_match('/New:\s*(\d+)/i', $output, $matches)) {
            $results['new_restaurants'] = (int) $matches[1];
        }
        if (preg_match('/Dups?:\s*(\d+)/i', $output, $matches)) {
            $results['duplicates'] = (int) $matches[1];
        }
        if (preg_match('/Cities processed\s*\|\s*(\d+)/i', $output, $matches)) {
            $results['cities_processed'] = (int) $matches[1];
        }

        return $results;
    }

    /**
     * Validate webhook secret
     */
    private function validateSecret(Request $request): bool
    {
        $secret = $request->header('X-Webhook-Secret')
            ?? $request->input('secret');

        $expectedSecret = config('services.n8n.webhook_secret');

        if (empty($expectedSecret)) {
            return false;
        }

        return $secret === $expectedSecret;
    }
}
