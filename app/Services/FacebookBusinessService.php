<?php

namespace App\Services;

use App\Models\Restaurant;
use App\Models\ExternalReview;
use App\Models\PlatformConnection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class FacebookBusinessService
{
    private string $appId;
    private string $appSecret;
    private string $redirectUri;
    private string $graphUrl = 'https://graph.facebook.com/v18.0';

    public function __construct()
    {
        $this->appId = config('services.facebook.client_id');
        $this->appSecret = config('services.facebook.client_secret');
        $this->redirectUri = config('services.facebook.redirect') . '/business';
    }

    /**
     * Get OAuth URL for Facebook Page access
     */
    public function getAuthUrl(int $restaurantId): string
    {
        $params = http_build_query([
            'client_id' => $this->appId,
            'redirect_uri' => $this->redirectUri,
            'scope' => 'pages_show_list,pages_read_engagement,pages_manage_engagement,pages_read_user_content',
            'response_type' => 'code',
            'state' => encrypt(['restaurant_id' => $restaurantId]),
        ]);

        return 'https://www.facebook.com/v18.0/dialog/oauth?' . $params;
    }

    /**
     * Exchange code for access token
     */
    public function exchangeCode(string $code): array
    {
        $response = Http::get($this->graphUrl . '/oauth/access_token', [
            'client_id' => $this->appId,
            'client_secret' => $this->appSecret,
            'redirect_uri' => $this->redirectUri,
            'code' => $code,
        ]);

        if (!$response->successful()) {
            throw new \Exception('Failed to exchange code: ' . $response->body());
        }

        return $response->json();
    }

    /**
     * Get long-lived access token
     */
    public function getLongLivedToken(string $shortToken): array
    {
        $response = Http::get($this->graphUrl . '/oauth/access_token', [
            'grant_type' => 'fb_exchange_token',
            'client_id' => $this->appId,
            'client_secret' => $this->appSecret,
            'fb_exchange_token' => $shortToken,
        ]);

        if (!$response->successful()) {
            throw new \Exception('Failed to get long-lived token: ' . $response->body());
        }

        return $response->json();
    }

    /**
     * Get user's Facebook Pages
     */
    public function getPages(string $accessToken): array
    {
        $response = Http::get($this->graphUrl . '/me/accounts', [
            'access_token' => $accessToken,
            'fields' => 'id,name,access_token,category,picture',
        ]);

        if (!$response->successful()) {
            throw new \Exception('Failed to get pages: ' . $response->body());
        }

        return $response->json('data', []);
    }

    /**
     * Sync reviews from a Facebook Page
     */
    public function syncReviews(Restaurant $restaurant, PlatformConnection $connection): int
    {
        $pageId = $connection->platform_account_id;
        $pageToken = $connection->access_token;

        $response = Http::get($this->graphUrl . /{$pageId}/ratings, [
            'access_token' => $pageToken,
            'fields' => 'reviewer{id,name,picture},created_time,rating,recommendation_type,review_text,open_graph_story{id}',
            'limit' => 100,
        ]);

        if (!$response->successful()) {
            throw new \Exception('Failed to get reviews: ' . $response->body());
        }

        $reviews = $response->json('data', []);
        $synced = 0;

        foreach ($reviews as $reviewData) {
            $synced += $this->upsertReview($restaurant, $reviewData) ? 1 : 0;
        }

        return $synced;
    }

    /**
     * Reply to a Facebook review/recommendation
     */
    public function replyToReview(Restaurant $restaurant, ExternalReview $review): bool
    {
        $connection = PlatformConnection::where('restaurant_id', $restaurant->id)
            ->where('platform', 'facebook')
            ->where('status', 'active')
            ->first();

        if (!$connection) {
            return false;
        }

        // Facebook reviews are actually recommendations and you reply via comments
        // on the open_graph_story
        $storyId = $review->platform_review_id;

        $response = Http::post($this->graphUrl . /{$storyId}/comments, [
            'access_token' => $connection->access_token,
            'message' => $review->owner_response,
        ]);

        if ($response->successful()) {
            $review->update([
                'response_synced' => true,
                'response_synced_at' => now(),
            ]);
            return true;
        }

        Log::error('Failed to reply to Facebook review', [
            'review_id' => $review->id,
            'response' => $response->body(),
        ]);

        return false;
    }

    /**
     * Upsert a review from Facebook
     */
    private function upsertReview(Restaurant $restaurant, array $data): bool
    {
        $reviewId = $data['open_graph_story']['id'] ?? $data['reviewer']['id'] . '_' . strtotime($data['created_time']);

        // Facebook uses recommendation_type (positive/negative) or rating (1-5)
        $rating = $data['rating'] ?? ($data['recommendation_type'] === 'positive' ? 5 : 2);

        $reviewData = [
            'restaurant_id' => $restaurant->id,
            'platform' => 'facebook',
            'platform_review_id' => $reviewId,
            'reviewer_name' => $data['reviewer']['name'] ?? 'Facebook User',
            'reviewer_avatar' => $data['reviewer']['picture']['data']['url'] ?? null,
            'rating' => $rating,
            'comment' => $data['review_text'] ?? null,
            'reviewed_at' => isset($data['created_time']) 
                ? Carbon::parse($data['created_time']) 
                : now(),
            'last_synced_at' => now(),
        ];

        $review = ExternalReview::updateOrCreate(
            [
                'restaurant_id' => $restaurant->id,
                'platform' => 'facebook',
                'platform_review_id' => $reviewId,
            ],
            $reviewData
        );

        return $review->wasRecentlyCreated;
    }
}
