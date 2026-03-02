<?php

namespace App\Services;

use App\Models\Restaurant;
use App\Models\ExternalReview;
use App\Models\PlatformConnection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class GoogleBusinessService
{
    private string $clientId;
    private string $clientSecret;
    private string $redirectUri;
    private string $baseUrl = 'https://mybusiness.googleapis.com/v4';

    public function __construct()
    {
        $this->clientId = config('services.google.client_id');
        $this->clientSecret = config('services.google.client_secret');
        $this->redirectUri = config('services.google.redirect');
    }

    /**
     * Get OAuth URL for connecting Google Business Profile
     */
    public function getAuthUrl(int $restaurantId): string
    {
        $params = http_build_query([
            'client_id' => $this->clientId,
            'redirect_uri' => $this->redirectUri,
            'response_type' => 'code',
            'scope' => 'https://www.googleapis.com/auth/business.manage',
            'access_type' => 'offline',
            'prompt' => 'consent',
            'state' => encrypt($restaurantId),
        ]);

        return 'https://accounts.google.com/o/oauth2/v2/auth?' . $params;
    }

    /**
     * Exchange authorization code for tokens
     */
    public function exchangeCode(string $code): array
    {
        $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'code' => $code,
            'grant_type' => 'authorization_code',
            'redirect_uri' => $this->redirectUri,
        ]);

        if (!$response->successful()) {
            throw new \Exception('Failed to exchange code: ' . $response->body());
        }

        return $response->json();
    }

    /**
     * Refresh access token
     */
    public function refreshToken(PlatformConnection $connection): bool
    {
        $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'refresh_token' => $connection->refresh_token,
            'grant_type' => 'refresh_token',
        ]);

        if (!$response->successful()) {
            $connection->markAsExpired();
            return false;
        }

        $data = $response->json();

        $connection->update([
            'access_token' => $data['access_token'],
            'token_expires_at' => now()->addSeconds($data['expires_in']),
        ]);

        return true;
    }

    /**
     * Get business accounts
     */
    public function getAccounts(PlatformConnection $connection): array
    {
        $this->ensureValidToken($connection);

        $response = Http::withToken($connection->access_token)
            ->get($this->baseUrl . '/accounts');

        if (!$response->successful()) {
            throw new \Exception('Failed to get accounts: ' . $response->body());
        }

        return $response->json('accounts', []);
    }

    /**
     * Get locations for an account
     */
    public function getLocations(PlatformConnection $connection, string $accountId): array
    {
        $this->ensureValidToken($connection);

        $response = Http::withToken($connection->access_token)
            ->get($this->baseUrl . /accounts/{$accountId}/locations);

        if (!$response->successful()) {
            throw new \Exception('Failed to get locations: ' . $response->body());
        }

        return $response->json('locations', []);
    }

    /**
     * Sync reviews from Google Business Profile
     */
    public function syncReviews(Restaurant $restaurant, PlatformConnection $connection): int
    {
        $this->ensureValidToken($connection);

        $locationId = $connection->platform_account_id;
        $accountId = $this->extractAccountId($locationId);

        $response = Http::withToken($connection->access_token)
            ->get($this->baseUrl . /{$locationId}/reviews);

        if (!$response->successful()) {
            throw new \Exception('Failed to get reviews: ' . $response->body());
        }

        $reviews = $response->json('reviews', []);
        $synced = 0;

        foreach ($reviews as $reviewData) {
            $synced += $this->upsertReview($restaurant, $reviewData) ? 1 : 0;
        }

        return $synced;
    }

    /**
     * Reply to a Google review
     */
    public function replyToReview(Restaurant $restaurant, ExternalReview $review): bool
    {
        $connection = PlatformConnection::where('restaurant_id', $restaurant->id)
            ->where('platform', 'google')
            ->where('status', 'active')
            ->first();

        if (!$connection) {
            return false;
        }

        $this->ensureValidToken($connection);

        $reviewName = $review->platform_review_id;

        $response = Http::withToken($connection->access_token)
            ->put($this->baseUrl . /{$reviewName}/reply, [
                'comment' => $review->owner_response,
            ]);

        if ($response->successful()) {
            $review->update([
                'response_synced' => true,
                'response_synced_at' => now(),
            ]);
            return true;
        }

        Log::error('Failed to reply to Google review', [
            'review_id' => $review->id,
            'response' => $response->body(),
        ]);

        return false;
    }

    /**
     * Upsert a review from Google
     */
    private function upsertReview(Restaurant $restaurant, array $data): bool
    {
        $reviewId = $data['name'] ?? $data['reviewId'];

        // Parse rating (Google uses STAR_RATING enum)
        $rating = $this->parseStarRating($data['starRating'] ?? 'STAR_RATING_UNSPECIFIED');

        $reviewData = [
            'restaurant_id' => $restaurant->id,
            'platform' => 'google',
            'platform_review_id' => $reviewId,
            'platform_url' => $data['reviewLink'] ?? null,
            'reviewer_name' => $data['reviewer']['displayName'] ?? 'Usuario de Google',
            'reviewer_avatar' => $data['reviewer']['profilePhotoUrl'] ?? null,
            'rating' => $rating,
            'comment' => $data['comment'] ?? null,
            'reviewed_at' => isset($data['createTime']) 
                ? Carbon::parse($data['createTime']) 
                : now(),
            'last_synced_at' => now(),
        ];

        // Check if reply exists
        if (isset($data['reviewReply'])) {
            $reviewData['owner_response'] = $data['reviewReply']['comment'];
            $reviewData['owner_response_at'] = Carbon::parse($data['reviewReply']['updateTime']);
            $reviewData['response_synced'] = true;
            $reviewData['status'] = 'responded';
        }

        $review = ExternalReview::updateOrCreate(
            [
                'restaurant_id' => $restaurant->id,
                'platform' => 'google',
                'platform_review_id' => $reviewId,
            ],
            $reviewData
        );

        return $review->wasRecentlyCreated;
    }

    /**
     * Parse Google's star rating enum to integer
     */
    private function parseStarRating(string $rating): int
    {
        return match($rating) {
            'FIVE' => 5,
            'FOUR' => 4,
            'THREE' => 3,
            'TWO' => 2,
            'ONE' => 1,
            default => 0,
        };
    }

    /**
     * Extract account ID from location name
     */
    private function extractAccountId(string $locationName): string
    {
        // Location format: accounts/{accountId}/locations/{locationId}
        preg_match('/accounts\/([\d]+)/', $locationName, $matches);
        return $matches[1] ?? '';
    }

    /**
     * Ensure token is valid, refresh if needed
     */
    private function ensureValidToken(PlatformConnection $connection): void
    {
        if ($connection->needsRefresh()) {
            if (!$this->refreshToken($connection)) {
                throw new \Exception('Failed to refresh Google token');
            }
            $connection->refresh();
        }
    }
}
