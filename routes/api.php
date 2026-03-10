<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\RestaurantController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\Api\ReservationController;
use App\Http\Controllers\Api\OwnerController;
use App\Http\Controllers\Api\OwnerAppController;
use App\Http\Controllers\Api\CouponController;
use App\Http\Controllers\Api\CheckInController;
use App\Http\Controllers\Api\SubscriberCouponApiController;
use App\Http\Controllers\Api\CarmenApiController;

/*
|--------------------------------------------------------------------------
| API Routes v1
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Public Routes (no authentication required)
    |--------------------------------------------------------------------------
    */

    // Health check
    Route::get('/health', function () {
        return response()->json([
            'success' => true,
            'message' => 'FAMER API is running',
            'version' => 'v1.1',
            'timestamp' => now()->toIso8601String(),
        ]);
    });

    // Authentication (rate limited)
    Route::prefix('auth')->middleware('throttle:10,1')->group(function () {
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/login', [AuthController::class, 'login']);
        Route::post('/social', [AuthController::class, 'socialLogin']);
        Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
    });

    // Restaurants (public)
    Route::prefix('restaurants')->group(function () {
        Route::get('/', [RestaurantController::class, 'index']);
        Route::get('/nearby', [RestaurantController::class, 'nearby']);
        Route::get('/featured', [RestaurantController::class, 'featured']);
        Route::get('/popular', [RestaurantController::class, 'popular']);
        Route::get('/search', [RestaurantController::class, 'search']);
        Route::get('/{id}', [RestaurantController::class, 'show']);
        Route::get('/{id}/reviews', [ReviewController::class, 'index']);
        Route::get('/{id}/menu', [RestaurantController::class, 'menu']);
        Route::get('/{id}/photos', [RestaurantController::class, 'photos']);
    });

    // Categories & States (public)
    Route::get('/categories', [RestaurantController::class, 'categories']);
    Route::get('/states', [RestaurantController::class, 'states']);

    // Coupons (public browse)
    Route::get('/coupons', [CouponController::class, 'index']);
    Route::get('/restaurants/{restaurantId}/coupons', [CouponController::class, 'restaurantCoupons']);
    Route::get('/restaurants/{restaurantId}/check-ins/count', [CheckInController::class, 'count']);

    // MF Group Subscriber Coupons API
    Route::prefix('subscriber-coupons')->group(function () {
        Route::post('/validate', [SubscriberCouponApiController::class, 'validate']);
        Route::post('/redeem', [SubscriberCouponApiController::class, 'redeem']);
        Route::get('/{code}', [SubscriberCouponApiController::class, 'show']);
    });

    // Subscription Benefits
    Route::get('/subscription-benefits/{tier}', [SubscriberCouponApiController::class, 'benefits']);

    /*
    |--------------------------------------------------------------------------
    | Protected Routes (authentication required)
    |--------------------------------------------------------------------------
    */

    Route::middleware('auth:sanctum')->group(function () {

        // Auth
        Route::post('/auth/logout', [AuthController::class, 'logout']);
        Route::get('/auth/user', [AuthController::class, 'user']);

        // User Profile
        Route::prefix('user')->group(function () {
            Route::get('/profile', [UserController::class, 'profile']);
            Route::put('/profile', [UserController::class, 'updateProfile']);
            Route::put('/password', [UserController::class, 'updatePassword']);
            Route::delete('/account', [UserController::class, 'deleteAccount']);

            // Favorites
            Route::get('/favorites', [UserController::class, 'favorites']);
            Route::post('/favorites/{restaurantId}', [UserController::class, 'addFavorite']);
            Route::delete('/favorites/{restaurantId}', [UserController::class, 'removeFavorite']);
            Route::get('/favorites/{restaurantId}/check', [UserController::class, 'isFavorite']);

            // User's reservations
            Route::get('/reservations', [UserController::class, 'reservations']);

            // User's reviews
            Route::get('/reviews', [UserController::class, 'reviews']);

            // User's coupons & check-ins
            Route::get('/coupons', [CouponController::class, 'userCoupons']);
            Route::get('/check-ins', [CheckInController::class, 'userCheckIns']);
        });

        // Claim a coupon
        Route::post('/coupons/{id}/claim', [CouponController::class, 'claim']);

        // Check-in at a restaurant
        Route::post('/restaurants/{restaurantId}/check-in', [CheckInController::class, 'checkIn']);

        // Reviews (authenticated)
        Route::prefix('restaurants/{restaurantId}')->group(function () {
            Route::post('/reviews', [ReviewController::class, 'store']);
        });

        Route::prefix('reviews')->group(function () {
            Route::put('/{id}', [ReviewController::class, 'update']);
            Route::delete('/{id}', [ReviewController::class, 'destroy']);
            Route::post('/{id}/helpful', [ReviewController::class, 'markHelpful']);
            Route::post('/{id}/report', [ReviewController::class, 'report']);
        });

        // Reservations (authenticated)
        Route::prefix('restaurants/{restaurantId}')->group(function () {
            Route::get('/reservations/slots', [ReservationController::class, 'availableSlots']);
            Route::post('/reservations', [ReservationController::class, 'store']);
        });

        Route::prefix('reservations')->group(function () {
            Route::get('/{id}', [ReservationController::class, 'show']);
            Route::put('/{id}', [ReservationController::class, 'update']);
            Route::post('/{id}/cancel', [ReservationController::class, 'cancel']);
        });

        /*
        |--------------------------------------------------------------------------
        | Owner App Routes (simplified, auto-detect restaurant from token)
        | Used by the FAMER Owners Flutter mobile app
        |--------------------------------------------------------------------------
        */

        Route::prefix('owner')->middleware('throttle:60,1')->group(function () {
            // Dashboard & restaurant (auto-detect from auth user)
            Route::get('/dashboard', [OwnerAppController::class, 'dashboard']);
            Route::get('/restaurant', [OwnerAppController::class, 'show']);
            Route::put('/restaurant', [OwnerAppController::class, 'update']);

            // Reviews
            Route::get('/reviews', [OwnerAppController::class, 'reviews']);
            Route::post('/reviews/{reviewId}/respond', [OwnerAppController::class, 'respondToReview']);

            // Reservations
            Route::get('/reservations', [OwnerAppController::class, 'reservations']);
            Route::put('/reservations/{reservationId}', [OwnerAppController::class, 'updateReservation']);

            // Photos
            Route::get('/photos', [OwnerAppController::class, 'photos']);
            Route::delete('/photos', [OwnerAppController::class, 'deletePhoto']);

            // Coupons
            Route::get('/coupons', [OwnerAppController::class, 'coupons']);
            Route::post('/coupons', [OwnerAppController::class, 'createCoupon']);
            Route::put('/coupons/{couponId}', [OwnerAppController::class, 'updateCoupon']);
            Route::delete('/coupons/{couponId}', [OwnerAppController::class, 'deleteCoupon']);

            // Menu management
            Route::get('/menu', [OwnerAppController::class, 'menu']);
            Route::put('/menu', [OwnerAppController::class, 'saveMenu']);

            // Hours
            Route::put('/hours', [OwnerAppController::class, 'updateHours']);

            // FAMER Score & Analytics
            Route::get('/score', [OwnerAppController::class, 'score']);
            Route::get('/analytics', [OwnerAppController::class, 'analytics']);

            // Orders (Elite only — live order management)
            Route::get('/orders', [OwnerAppController::class, 'orders']);
            Route::put('/orders/{orderId}', [OwnerAppController::class, 'updateOrder']);

            // Subscription info & feature gates
            Route::get('/subscription', [OwnerAppController::class, 'subscription']);

            // SMS Marketing (Elite only)
            Route::get('/sms/stats', [OwnerAppController::class, 'smsStats']);
            Route::get('/sms/automations', [OwnerAppController::class, 'smsAutomations']);

            // Account
            Route::delete('/account', [OwnerAppController::class, 'deleteAccount']);
        });

        /*
        |--------------------------------------------------------------------------
        | Owner Routes (restaurant owner app — legacy with explicit restaurantId)
        |--------------------------------------------------------------------------
        */

        Route::prefix('owner')->group(function () {
            // My restaurants
            Route::get('/restaurants', [OwnerController::class, 'restaurants']);
            
            // Restaurant management
            Route::prefix('restaurants/{restaurantId}')->group(function () {
                Route::get('/dashboard', [OwnerController::class, 'dashboard']);
                Route::get('/analytics', [OwnerController::class, 'analytics']);
                Route::put('/', [OwnerController::class, 'updateRestaurant']);
                Route::post('/photos', [OwnerController::class, 'uploadPhotos']);
                Route::get('/reviews', [OwnerController::class, 'reviews']);
                Route::get('/reservations', [OwnerController::class, 'reservations']);
            });

            // Review responses
            Route::post('/reviews/{reviewId}/respond', [OwnerController::class, 'respondToReview']);

            // Reservation management
            Route::put('/reservations/{reservationId}/status', [OwnerController::class, 'updateReservationStatus']);

            /*
            |--------------------------------------------------------------------------
            | Review Hub - Manage reviews from all platforms
            |--------------------------------------------------------------------------
            */

            Route::prefix("review-hub")->group(function () {
                Route::get("/{restaurantId}/dashboard", [\App\Http\Controllers\Api\ReviewHubController::class, "dashboard"]);
                Route::get("/{restaurantId}/reviews", [\App\Http\Controllers\Api\ReviewHubController::class, "reviews"]);
                Route::get("/reviews/{reviewId}", [\App\Http\Controllers\Api\ReviewHubController::class, "showReview"]);
                Route::post("/reviews/{reviewId}/respond", [\App\Http\Controllers\Api\ReviewHubController::class, "respondToReview"]);
                Route::get("/{restaurantId}/connections", [\App\Http\Controllers\Api\ReviewHubController::class, "connections"]);
                Route::post("/{restaurantId}/connections/{platform}/sync", [\App\Http\Controllers\Api\ReviewHubController::class, "syncPlatform"]);
                Route::delete("/{restaurantId}/connections/{platform}", [\App\Http\Controllers\Api\ReviewHubController::class, "disconnectPlatform"]);
                Route::get("/{restaurantId}/templates", [\App\Http\Controllers\Api\ReviewHubController::class, "templates"]);
                Route::post("/{restaurantId}/templates", [\App\Http\Controllers\Api\ReviewHubController::class, "createTemplate"]);
                Route::get("/{restaurantId}/analytics", [\App\Http\Controllers\Api\ReviewHubController::class, "analytics"]);
            });
        });
    });
});

/*
|--------------------------------------------------------------------------
| Carmen AI Assistant API Routes
|--------------------------------------------------------------------------
*/

Route::prefix('carmen')->middleware('api')->group(function () {
    Route::post('/order-status', [CarmenApiController::class, 'orderStatus']);
    Route::post('/reservation-status', [CarmenApiController::class, 'reservationStatus']);
    Route::post('/search-restaurant', [CarmenApiController::class, 'searchRestaurant']);
    Route::post('/restaurant-info', [CarmenApiController::class, 'restaurantInfo']);
    Route::post('/claim-status', [CarmenApiController::class, 'claimStatus']);
    Route::post('/suggestions', [CarmenApiController::class, 'getSuggestions']);
    Route::post('/subscription-plans', [CarmenApiController::class, 'subscriptionPlans']);
    Route::post('/claim-guide', [CarmenApiController::class, 'claimGuide']);
    Route::post('/check-ownership', [CarmenApiController::class, 'checkRestaurantOwnership']);
    Route::post('/welcome', [CarmenApiController::class, 'getWelcomeMessage']);
    Route::post('/owner-summary', [CarmenApiController::class, 'ownerSummary']);
});

/*
|--------------------------------------------------------------------------
| MF Imports Integration API Routes
|--------------------------------------------------------------------------
*/

Route::prefix('mf-imports')->group(function () {
    Route::post('/generate-promo', [\App\Http\Controllers\Api\MfImportsPromoController::class, 'generatePromo']);
    Route::get('/validate-promo/{code}', [\App\Http\Controllers\Api\MfImportsPromoController::class, 'validatePromo']);
    Route::get('/customer-promos/{email}', [\App\Http\Controllers\Api\MfImportsPromoController::class, 'customerPromos']);
});

/*
|--------------------------------------------------------------------------
| Campaign API Routes (for n8n + Listmonk integration)
|--------------------------------------------------------------------------
*/

Route::prefix('campaign')->group(function () {
    Route::get('/new-today', [\App\Http\Controllers\Api\CampaignApiController::class, 'newRestaurantsToday']);
    Route::get('/new-range', [\App\Http\Controllers\Api\CampaignApiController::class, 'newRestaurantsRange']);
    Route::get('/platform-stats', [\App\Http\Controllers\Api\CampaignApiController::class, 'platformStats']);
    Route::get('/restaurant/{id}/stats', [\App\Http\Controllers\Api\CampaignApiController::class, 'restaurantStats']);
    Route::get('/unclaimed-after-days', [\App\Http\Controllers\Api\CampaignApiController::class, 'unclaimedAfterDays']);
    Route::get('/pending-followups', [\App\Http\Controllers\Api\CampaignApiController::class, 'pendingFollowups']);
    Route::post('/mark-reminder-sent', [\App\Http\Controllers\Api\CampaignApiController::class, 'markReminderSent']);
});

// FAMER Campaign Routes
Route::prefix("campaign/famer")->group(function () {
    Route::get("/pending-email1", [App\Http\Controllers\Api\CampaignApiController::class, "famerPendingEmail1"]);
    Route::get("/pending-email2", [App\Http\Controllers\Api\CampaignApiController::class, "famerPendingEmail2"]);
    Route::get("/pending-email3", [App\Http\Controllers\Api\CampaignApiController::class, "famerPendingEmail3"]);
    Route::post("/mark-sent", [App\Http\Controllers\Api\CampaignApiController::class, "famerMarkSent"]);
    Route::get("/stats", [App\Http\Controllers\Api\CampaignApiController::class, "famerStats"]);
});

// Campaign API Routes (for N8N)
Route::prefix("v1/campaigns")->group(function () {
    Route::post("/send-batch", [\App\Http\Controllers\Api\CampaignApiController::class, "sendBatch"]);
    Route::get("/contacts", [\App\Http\Controllers\Api\CampaignApiController::class, "getContacts"]);
    Route::get("/stats", [\App\Http\Controllers\Api\CampaignApiController::class, "getStats"]);
});

// Resend Webhook (sin autenticación)
Route::post("/webhooks/resend", [\App\Http\Controllers\ResendWebhookController::class, "handle"]);

// City statistics for owners page
Route::get('/city-stats', [\App\Http\Controllers\Api\CityStatsController::class, 'getStats']);
Route::get('/city-search', [\App\Http\Controllers\Api\CityStatsController::class, 'searchCities']);

