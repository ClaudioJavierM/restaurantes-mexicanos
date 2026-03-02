<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Restaurant;
use App\Models\PlatformConnection;
use App\Services\GoogleBusinessService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class OAuthController extends Controller
{
    /**
     * Redirect to Google OAuth for Business Profile
     */
    public function googleRedirect(Request $request, $restaurantId)
    {
        // Verify ownership
        $restaurant = Restaurant::where('id', $restaurantId)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        $params = http_build_query([
            'client_id' => config('services.google.client_id'),
            'redirect_uri' => config('services.google.redirect') . '/business',
            'response_type' => 'code',
            'scope' => 'https://www.googleapis.com/auth/business.manage',
            'access_type' => 'offline',
            'prompt' => 'consent',
            'state' => encrypt(['restaurant_id' => $restaurantId, 'user_id' => $request->user()->id]),
        ]);

        return response()->json([
            'success' => true,
            'redirect_url' => 'https://accounts.google.com/o/oauth2/v2/auth?' . $params,
        ]);
    }

    /**
     * Handle Google OAuth callback for Business Profile
     */
    public function googleCallback(Request $request)
    {
        if ($request->has('error')) {
            return redirect('/owner/review-hub?error=' . $request->error);
        }

        try {
            $state = decrypt($request->state);
            $restaurantId = $state['restaurant_id'];
            $userId = $state['user_id'];

            // Exchange code for tokens
            $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
                'client_id' => config('services.google.client_id'),
                'client_secret' => config('services.google.client_secret'),
                'code' => $request->code,
                'grant_type' => 'authorization_code',
                'redirect_uri' => config('services.google.redirect') . '/business',
            ]);

            if (!$response->successful()) {
                throw new \Exception('Failed to exchange code: ' . $response->body());
            }

            $tokens = $response->json();

            // Get account info
            $accountInfo = $this->getGoogleAccountInfo($tokens['access_token']);

            // Save connection
            PlatformConnection::updateOrCreate(
                [
                    'restaurant_id' => $restaurantId,
                    'platform' => 'google',
                ],
                [
                    'user_id' => $userId,
                    'platform_account_id' => $accountInfo['account_id'] ?? null,
                    'platform_account_name' => $accountInfo['account_name'] ?? 'Google Business',
                    'access_token' => $tokens['access_token'],
                    'refresh_token' => $tokens['refresh_token'] ?? null,
                    'token_expires_at' => now()->addSeconds($tokens['expires_in']),
                    'status' => 'active',
                    'connected_at' => now(),
                ]
            );

            // Trigger initial sync
            $this->syncGoogleReviews($restaurantId);

            return redirect('/owner/review-hub?success=google_connected');

        } catch (\Exception $e) {
            \Log::error('Google OAuth callback failed', ['error' => $e->getMessage()]);
            return redirect('/owner/review-hub?error=connection_failed');
        }
    }

    /**
     * Get Google account info
     */
    private function getGoogleAccountInfo(string $accessToken): array
    {
        try {
            $response = Http::withToken($accessToken)
                ->get('https://mybusiness.googleapis.com/v4/accounts');

            if ($response->successful() && !empty($response->json('accounts'))) {
                $account = $response->json('accounts')[0];
                return [
                    'account_id' => $account['name'] ?? null,
                    'account_name' => $account['accountName'] ?? 'Google Business',
                ];
            }
        } catch (\Exception $e) {
            \Log::error('Failed to get Google account info', ['error' => $e->getMessage()]);
        }

        return [];
    }

    /**
     * Trigger Google reviews sync
     */
    private function syncGoogleReviews(int $restaurantId): void
    {
        try {
            $restaurant = Restaurant::find($restaurantId);
            $connection = PlatformConnection::where('restaurant_id', $restaurantId)
                ->where('platform', 'google')
                ->first();

            if ($restaurant && $connection) {
                app(GoogleBusinessService::class)->syncReviews($restaurant, $connection);
            }
        } catch (\Exception $e) {
            \Log::error('Initial Google sync failed', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Redirect to Facebook OAuth for Page access
     */
    public function facebookRedirect(Request $request, $restaurantId)
    {
        // Verify ownership
        $restaurant = Restaurant::where('id', $restaurantId)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        $service = app(\App\Services\FacebookBusinessService::class);
        
        return response()->json([
            'success' => true,
            'redirect_url' => $service->getAuthUrl($restaurantId),
        ]);
    }

    /**
     * Handle Facebook OAuth callback
     */
    public function facebookCallback(Request $request)
    {
        if ($request->has('error')) {
            return redirect('/owner/review-hub?error=' . $request->error_description);
        }

        try {
            $state = decrypt($request->state);
            $restaurantId = $state['restaurant_id'];

            $service = app(\App\Services\FacebookBusinessService::class);

            // Exchange code for token
            $tokens = $service->exchangeCode($request->code);
            $shortToken = $tokens['access_token'];

            // Get long-lived token
            $longLived = $service->getLongLivedToken($shortToken);
            $userToken = $longLived['access_token'];

            // Get user's pages
            $pages = $service->getPages($userToken);

            if (empty($pages)) {
                return redirect('/owner/review-hub?error=no_pages_found');
            }

            // For now, use the first page (or could show selection UI)
            $page = $pages[0];

            // Get restaurant to find owner
            $restaurant = Restaurant::findOrFail($restaurantId);

            // Save connection with PAGE token (not user token)
            PlatformConnection::updateOrCreate(
                [
                    'restaurant_id' => $restaurantId,
                    'platform' => 'facebook',
                ],
                [
                    'user_id' => $restaurant->user_id,
                    'platform_account_id' => $page['id'],
                    'platform_account_name' => $page['name'],
                    'access_token' => $page['access_token'], // Page token
                    'refresh_token' => null, // Page tokens don't expire
                    'token_expires_at' => null,
                    'status' => 'active',
                    'connected_at' => now(),
                ]
            );

            // Trigger initial sync
            try {
                $connection = PlatformConnection::where('restaurant_id', $restaurantId)
                    ->where('platform', 'facebook')
                    ->first();
                $service->syncReviews($restaurant, $connection);
                $connection->recordSync();
            } catch (\Exception $e) {
                \Log::error('Initial Facebook sync failed', ['error' => $e->getMessage()]);
            }

            return redirect('/owner/review-hub?success=facebook_connected&page=' . urlencode($page['name']));

        } catch (\Exception $e) {
            \Log::error('Facebook OAuth callback failed', ['error' => $e->getMessage()]);
            return redirect('/owner/review-hub?error=connection_failed');
        }
    }
}
