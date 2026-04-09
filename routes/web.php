<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\Auth\SocialAuthController;
use App\Http\Controllers\TeamInvitationController;
use App\Http\Controllers\StateRestaurantsController;
use App\Http\Controllers\CityRestaurantsController;

// Public Routes
Route::get('/', \App\Livewire\Home::class)->name('home');
Route::get('/restaurantes', \App\Livewire\RestaurantList::class)->name('restaurants.index');
Route::get('/restaurantes/categoria/{slug}', [\App\Http\Controllers\CategoryController::class, 'show'])->name('categories.show');
Route::get('/restaurante/{slug}', \App\Livewire\RestaurantDetail::class)->name('restaurants.show');
// English URL alias for famousmexicanrestaurants.com domain
Route::get('/restaurant/{slug}', fn($slug) => redirect('/restaurante/' . $slug, 301));
Route::get('/sugerir', \App\Livewire\SmartSuggestionForm::class)->name('suggestions.create');
Route::get('/restaurantes-mexicanos-cerca-de-mi', [\App\Http\Controllers\NearMeController::class, 'index'])->name('near-me');

// City landing pages — MUST be before state routes (more specific slug pattern)
// Format: /restaurantes-mexicanos-en-dallas-tx  /best-mexican-restaurants-in-dallas-tx
Route::get('/restaurantes-mexicanos-en-{citySlug}', [CityRestaurantsController::class, 'show'])
    ->where('citySlug', '[a-z\-]+-[a-z]{2}')
    ->name('cities.show');
Route::get('/best-mexican-restaurants-in-{citySlug}', [CityRestaurantsController::class, 'show'])
    ->where('citySlug', '[a-z\-]+-[a-z]{2}')
    ->name('cities.show.en');

// State landing pages (SEO: "restaurantes mexicanos en texas")
Route::get('/estados', [StateRestaurantsController::class, 'index'])->name('states.index');
Route::get('/restaurantes-mexicanos-en-{stateSlug}', [StateRestaurantsController::class, 'show'])->name('states.show');
Route::get('/best-mexican-restaurants-in-{stateSlug}', [StateRestaurantsController::class, 'show'])->name('states.show.en');

// Dish-specific landing pages (SEO) — unified show() route covering 22 dishes
Route::get('/{dish}', [\App\Http\Controllers\DishController::class, 'show'])
    ->where('dish', 'birria|tacos|tamales|pozole|enchiladas|mole|chiles-rellenos|tortas|tostadas|quesadillas|flautas|sopes|menudo|carnitas|barbacoa|ceviche|elotes|churros|horchata|margaritas|tacos-al-pastor|carne-asada')
    ->name('dishes.show');

// Dish near-me landing pages (SEO)
Route::get('/birria-cerca-de-mi', [\App\Http\Controllers\DishNearMeController::class, 'birria'])->name('dish-near-me.birria');
Route::get('/tamales-cerca-de-mi', [\App\Http\Controllers\DishNearMeController::class, 'tamales'])->name('dish-near-me.tamales');
Route::get('/pozole-cerca-de-mi', [\App\Http\Controllers\DishNearMeController::class, 'pozole'])->name('dish-near-me.pozole');
Route::get('/carnitas-cerca-de-mi', [\App\Http\Controllers\DishNearMeController::class, 'carnitas'])->name('dish-near-me.carnitas');
Route::get('/barbacoa-cerca-de-mi', [\App\Http\Controllers\DishNearMeController::class, 'barbacoa'])->name('dish-near-me.barbacoa');
Route::get('/mole-cerca-de-mi', [\App\Http\Controllers\DishNearMeController::class, 'mole'])->name('dish-near-me.mole');
Route::get('/carne-asada-cerca-de-mi', [\App\Http\Controllers\DishNearMeController::class, 'carneAsada'])->name('dish-near-me.carne-asada');

// State-level dish pages (SEO: "birria en texas", "tamales en california")
Route::get('/{dish}-en-{state}', [\App\Http\Controllers\DishStateController::class, 'show'])
    ->where('dish', 'birria|tamales|pozole|carnitas|barbacoa|mole')
    ->where('state', 'tx|ca|il|az|fl|co|nv|nm|ny|ga|wa|nc|or|ut|tn')
    ->name('dish-state');

// City-level dish pages (SEO: "birria en dallas-tx", "tacos en chicago-il")
// MUST be before the /{dish} wildcard — more specific pattern
Route::get('/{dish}-en-{citySlug}-{stateCode}', [\App\Http\Controllers\DishCityController::class, 'show'])
    ->where('dish', 'birria|tacos|tamales|pozole|enchiladas|mole|chiles-rellenos|menudo|carnitas|barbacoa|ceviche|carne-asada')
    ->where('stateCode', 'tx|ca|il|az|fl|co|nv|nm|ny|ga|wa|nc|or|ut|tn|mi|oh|pa|nj|md')
    ->name('dish.city');

// Blog — Mexican food content & SEO authority
Route::get('/blog', [\App\Http\Controllers\BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/categoria/{category}', [\App\Http\Controllers\BlogController::class, 'category'])->name('blog.category');
Route::get('/blog/{post}', [\App\Http\Controllers\BlogController::class, 'show'])->name('blog.show');

// For Business Owners
Route::get("/for-owners", \App\Livewire\ForOwners::class)->name("for-owners");
Route::get('/como-funciona-famer', \App\Http\Controllers\HowItWorksController::class)->name('how-it-works');
Route::get('/how-famer-works', \App\Http\Controllers\HowItWorksController::class)->name('how-it-works.en');
Route::get("/preguntas-frecuentes", \App\Livewire\Faq::class)->name("faq");
Route::get('/grader', \App\Livewire\FamerGrader::class)->name('famer.grader');
Route::get('/grader/{slug}', \App\Livewire\FamerGrader::class)->name('famer.grader.restaurant');
Route::get('/famer-awards', \App\Livewire\FamerAwards2026::class)->name('famer.awards');
Route::get('/claim', \App\Livewire\ClaimRestaurant::class)->name('claim.restaurant');
Route::get('/claim/verify/{verification}', \App\Livewire\ClaimRestaurantImproved::class)->name('claim.verify');
Route::get('/claim/success', [\App\Http\Controllers\StripeWebhookController::class, 'success'])->name('claim.success');
Route::get('/claim/cancel', [\App\Http\Controllers\StripeWebhookController::class, 'cancel'])->name('claim.cancel');

// Stripe Webhook
Route::post('/stripe/webhook', [\App\Http\Controllers\StripeWebhookController::class, 'handleWebhook'])->name('stripe.webhook');

// Team Invitations
Route::get('/team/accept/{token}', [TeamInvitationController::class, 'show'])->name('team.invitation.show');
Route::post('/team/accept/{token}', [TeamInvitationController::class, 'accept'])->name('team.invitation.accept');
Route::post('/team/decline/{token}', [TeamInvitationController::class, 'decline'])->name('team.invitation.decline');

// SEO Routes — Sitemap index + sub-sitemaps
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');
Route::get('/sitemap-main.xml', [SitemapController::class, 'main'])->name('sitemap.main');
Route::get('/sitemap-restaurants-{page}.xml', [SitemapController::class, 'restaurants'])->where('page', '[0-9]+')->name('sitemap.restaurants');
Route::get('/sitemap-guides.xml', [SitemapController::class, 'guides'])->name('sitemap.guides');
Route::get('/sitemap-rankings.xml', [SitemapController::class, 'rankings'])->name('sitemap.rankings');
Route::get('/sitemap-blog.xml', [SitemapController::class, 'blog'])->name('sitemap.blog');
Route::get('/sitemap-states.xml', [SitemapController::class, 'states'])->name('sitemap.states');
Route::get('/sitemap-dishes.xml', [SitemapController::class, 'dishes'])->name('sitemap.dishes');

// City Guides (SEO)
Route::prefix('guia')->group(function () {
    Route::get('/', [\App\Http\Controllers\CityGuideController::class, 'states'])->name('city-guides.states');
    Route::get('/{state}', [\App\Http\Controllers\CityGuideController::class, 'state'])->name('city-guides.state');
    Route::get('/{state}/{city}', [\App\Http\Controllers\CityGuideController::class, 'city'])->name('city-guides.city');
});

// SEO Ranking Pages (to compete with Yelp for "mejores restaurantes mexicanos" keywords)
Route::get('/mejores-restaurantes-mexicanos', [\App\Http\Controllers\RankingController::class, 'mejoresNacional'])->name('rankings.mejores-nacional');
Route::get('/top-10-restaurantes-mexicanos', [\App\Http\Controllers\RankingController::class, 'top10Nacional'])->name('rankings.top10-nacional');
Route::get('/mejores/{state}', [\App\Http\Controllers\RankingController::class, 'mejoresEstado'])->name('rankings.mejores-estado');
Route::get('/mejores/{state}/{city}', [\App\Http\Controllers\RankingController::class, 'mejoresCiudad'])->name('rankings.mejores-ciudad');

// Auth Routes (Breeze)
Route::view('/mi-cuenta', 'mi-cuenta')
    ->middleware(['auth'])
    ->name('dashboard');

Route::redirect('/dashboard', '/mi-cuenta');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

// Favorites (requires authentication)
Route::get('/my-favorites', \App\Livewire\MyFavorites::class)
    ->middleware(['auth'])
    ->name('favorites.index');

// Social Authentication
Route::get('auth/{provider}/redirect', [SocialAuthController::class, 'redirect'])
    ->name('auth.social.redirect');
Route::get('auth/{provider}/callback', [SocialAuthController::class, 'callback'])
    ->name('auth.social.callback');

// Legal Pages
Route::get('/privacy', [\App\Http\Controllers\LegalController::class, 'privacy'])->name('legal.privacy');
Route::get('/terms', [\App\Http\Controllers\LegalController::class, 'terms'])->name('legal.terms');
Route::get('/contact', [\App\Http\Controllers\LegalController::class, 'contact'])->name('legal.contact');


// Email Tracking
Route::get('/email/track/open/{token}', [\App\Http\Controllers\EmailTrackingController::class, 'trackOpen'])->name('email.track.open');
Route::get('/email/track/click/{token}', [\App\Http\Controllers\EmailTrackingController::class, 'trackClick'])->name('email.track.click');
Route::get('/unsubscribe', [\App\Http\Controllers\EmailTrackingController::class, 'unsubscribe'])->name('email.unsubscribe');
Route::get('/email/preview/{campaign}', [\App\Http\Controllers\EmailTrackingController::class, 'preview'])->name('email.preview')->middleware('auth');


// Owner Dashboard Routes
Route::prefix('staging/livewire-owner')->middleware(['auth'])->group(function () {
    Route::get('/{restaurant:slug}', \App\Livewire\Owner\FamerDashboard::class)->name('owner.livewire.dashboard');
    Route::get('/{restaurant:slug}/email-marketing', \App\Livewire\Owner\EmailMarketing::class)->name('owner.livewire.email-marketing');
    Route::get('/{restaurant:slug}/reviews', \App\Livewire\Owner\ReviewHub::class)->name('owner.livewire.reviews');
    Route::get('/{restaurant:slug}/menu', \App\Livewire\Owner\MenuUpload::class)->name('owner.livewire.menu');
    Route::get('/{restaurant:slug}/famer', \App\Livewire\Owner\FamerDashboard::class)->name('owner.livewire.famer');
});

// Owner Email Tracking Routes  
Route::get('/owner-email/track/open/{token}', [\App\Http\Controllers\OwnerEmailTrackingController::class, 'trackOpen'])->name('owner-email.track.open');
Route::get('/owner-email/track/click/{token}', [\App\Http\Controllers\OwnerEmailTrackingController::class, 'trackClick'])->name('owner-email.track.click');
Route::get('/owner-email/unsubscribe/{token}', [\App\Http\Controllers\OwnerEmailTrackingController::class, 'unsubscribe'])->name('owner-email.unsubscribe');



// Voting Page
Route::get('/votar', \App\Livewire\VoteRestaurant::class)->name('votar');
Route::redirect('/contacto', '/contact');

// FAMER Awards
Route::get('/awards', \App\Livewire\AwardsWinners::class)->name('awards.winners');
Route::get('/awards/hall-of-fame', \App\Livewire\AwardsHallOfFame::class)->name('awards.hall-of-fame');
Route::get('/awards/{year}/{month}', \App\Livewire\AwardsWinners::class)->name('awards.month');

// Catering
Route::get('/catering', \App\Livewire\CateringRequest::class)->name('catering');

// City SEO pages
Route::get('/restaurantes-mexicanos-en-{city}-{state}', \App\Livewire\CityRestaurants::class)->name('city.restaurants');
Route::get('/mexican-restaurants-in-{city}-{state}', \App\Livewire\CityRestaurants::class)->name('city.restaurants.en');

require __DIR__.'/auth.php';

// FAMER Email Webhook for N8N
Route::post("/webhooks/famer-emails", [\App\Http\Controllers\FamerWebhookController::class, "trigger"])
    ->name("webhooks.famer-emails")
    ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);

// Import Webhooks for N8N
Route::prefix('webhooks/import')->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class])->group(function () {
    Route::post('/smart', [\App\Http\Controllers\ImportWebhookController::class, 'smartImport'])->name('webhooks.import.smart');
    Route::post('/bulk', [\App\Http\Controllers\ImportWebhookController::class, 'bulkImport'])->name('webhooks.import.bulk');
    Route::post('/status', [\App\Http\Controllers\ImportWebhookController::class, 'status'])->name('webhooks.import.status');
    Route::post('/reset-exhausted', [\App\Http\Controllers\ImportWebhookController::class, 'resetExhausted'])->name('webhooks.import.reset');
});

Route::view('/offline', 'offline')->name('offline');

// Deep Links for QR Codes (White-Label)
Route::get("/r/{slug}", [App\Http\Controllers\DeepLinkController::class, "restaurant"])->name("deeplink.restaurant");
Route::get("/menu/{slug}", [App\Http\Controllers\DeepLinkController::class, "menu"])->name("deeplink.menu");
Route::get("/app/{slug}", [App\Http\Controllers\DeepLinkController::class, "pwaApp"])->name("deeplink.pwa");


// Restaurant Website (Premium/Elite - standalone website)
Route::get("/sitio/{slug}", App\Livewire\RestaurantWebsite::class)->name("restaurant.website");

// PWA Restaurant View (Elite White-Label)
Route::get("/pwa/{slug}", App\Livewire\PwaRestaurant::class)->name("pwa.restaurant");


// Twilio SMS Webhooks

// TwiML endpoint for Twilio voice verification calls (no CSRF)
Route::get('/webhooks/twilio/claim-twiml', [\App\Http\Controllers\TwimlController::class, 'claimVerification'])->name('twilio.claim-twiml');
Route::post('/webhooks/twilio/sms', [\App\Http\Controllers\TwilioWebhookController::class, 'handleIncomingSms'])
    ->name('webhooks.twilio.sms')
    ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);

Route::post('/webhooks/twilio/status', [\App\Http\Controllers\TwilioWebhookController::class, 'handleStatusCallback'])
    ->name('webhooks.twilio.status')
    ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);
require __DIR__.'/chat-widget.php';

// QR Print page for restaurant owners
Route::get('/owner/qr-print/{restaurant}', function (App\Models\Restaurant $restaurant) {
    $voteUrl = url("/restaurante/{$restaurant->slug}#votar");
    $qrUrl = "https://api.qrserver.com/v1/create-qr-code/?size=400x400&data=" . urlencode($voteUrl);

    return view('owner.qr-print', [
        'restaurant' => $restaurant,
        'voteUrl' => $voteUrl,
        'qrUrl' => $qrUrl,
    ]);
})->middleware(['auth'])->name('owner.qr-print');

// QR PDF download for restaurant owners
Route::get('/owner/qr-pdf/{restaurant}', function (App\Models\Restaurant $restaurant) {
    $voteUrl = url("/restaurante/{$restaurant->slug}#votar");

    // Generate QR as base64 (dompdf can't load remote images)
    $qrApiUrl = "https://api.qrserver.com/v1/create-qr-code/?size=400x400&format=png&data=" . urlencode($voteUrl);
    $qrImage = @file_get_contents($qrApiUrl);
    $qrBase64 = $qrImage ? 'data:image/png;base64,' . base64_encode($qrImage) : '';

    // Site logo as base64
    $logoPath = public_path('images/branding/icon.png');
    $logoBase64 = file_exists($logoPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath)) : '';

    $pdf = Barryvdh\DomPDF\Facade\Pdf::loadView('owner.qr-print-pdf', [
        'restaurant' => $restaurant,
        'voteUrl' => $voteUrl,
        'qrBase64' => $qrBase64,
        'logoBase64' => $logoBase64,
    ]);
    $pdf->setPaper('letter');
    return $pdf->download('qr-votacion-' . $restaurant->slug . '.pdf');
})->middleware(['auth'])->name('owner.qr-pdf');

// Legacy redirect: old Livewire /owner/{slug} URLs -> Filament dashboard
Route::get('/owner/{slug}', function ($slug) {
    // Only redirect if slug looks like a restaurant slug (not a Filament resource path)
    $filamentPaths = ['login', 'logout', 'my-restaurants', 'menu-items', 'reviews', 'photos',
                       'analytics', 'chatbot-settings', 'email-marketing', 'events', 'flash-deals',
                       'live-orders', 'loyalty-program', 'menu-qr-code', 'my-benefits', 'my-coupons',
                       'promotions', 'qr-print', 'qr-pdf', 'reservations', 'staff', 'restaurant'];

    if (in_array($slug, $filamentPaths)) {
        abort(404); // Let Filament handle its own routes
    }

    return redirect('/owner');
})->middleware(['auth'])->name('owner-legacy-redirect');


// Certificate preview
Route::get('/owner/certificate/{restaurant}', function (App\Models\Restaurant $restaurant) {
    $year = date('Y');
    $certificate_id = 'FAMER-' . $year . '-' . str_pad($restaurant->id, 6, '0', STR_PAD_LEFT);
    $issue_date = now()->format('d/m/Y');

    return view('certificates.famer-certificate', compact('restaurant', 'year', 'certificate_id', 'issue_date'));
})->middleware(['auth'])->name('owner.certificate');

// Certificate PDF download
Route::get('/owner/certificate-pdf/{restaurant}', function (App\Models\Restaurant $restaurant) {
    $year = date('Y');
    $certificate_id = 'FAMER-' . $year . '-' . str_pad($restaurant->id, 6, '0', STR_PAD_LEFT);
    $issue_date = now()->format('d/m/Y');
    $isPdf = true;

    $pdf = Barryvdh\DomPDF\Facade\Pdf::loadView('certificates.famer-certificate', compact('restaurant', 'year', 'certificate_id', 'issue_date', 'isPdf'));
    $pdf->setPaper([0, 0, 792, 612]); // 11x8.5 landscape in points

    return $pdf->download('certificado-famer-' . $restaurant->slug . '-' . $year . '.pdf');
})->middleware(['auth'])->name('owner.certificate-pdf');

// Checkout
Route::get('/checkout', \App\Livewire\Checkout::class)->name('checkout');

// Order confirmation
Route::get('/pedido/{order_number}', \App\Livewire\OrderConfirmation::class)->name('order.confirmation');

// Customer account sub-pages
Route::middleware(['auth'])->group(function () {
    Route::get('/mi-cuenta/pedidos', \App\Livewire\MiCuentaPedidos::class)->name('mi-cuenta.pedidos');
    Route::get('/mi-cuenta/perfil', \App\Livewire\MiCuentaPerfil::class)->name('mi-cuenta.perfil');
});
