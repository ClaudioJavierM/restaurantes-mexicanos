@props([
    'type' => 'website',
    'title' => null,
    'description' => null,
    'image' => null,
    'url' => null,
    'restaurant' => null,
    'city' => null,
    'state' => null,
    'restaurantCount' => null
])

@php
// Generate dynamic Open Graph tags based on page type and data
$ogType = $type;
$ogTitle = $title;
$ogDescription = $description;
$ogImage = $image;
$ogUrl = $url ?? request()->url();

// Restaurant page specific OG tags
if ($restaurant) {
    $ogType = 'restaurant';
    $stateName = $restaurant->state ? $restaurant->state->name : 'USA';
    $rating = $restaurant->average_rating > 0 ? number_format($restaurant->average_rating, 1) : 'New';
    $ratingStars = $restaurant->average_rating > 0 ? str_repeat('⭐', min(floor($restaurant->average_rating), 5)) : '';
    
    $ogTitle = "{$restaurant->name} - Mexican Food in {$restaurant->city}, {$stateName}";
    
    // Build description with available data
    $descriptionParts = [];
    if ($restaurant->average_rating > 0) {
        $descriptionParts[] = "{$rating} {$ratingStars}";
    }
    
    // Add restaurant type/specialty if available
    if ($restaurant->category) {
        $descriptionParts[] = $restaurant->category->name;
    } else {
        $descriptionParts[] = "Authentic Mexican Restaurant";
    }
    
    $descriptionParts[] = $restaurant->address;
    $descriptionParts[] = "Hours, menu & reviews";
    
    $ogDescription = implode(' | ', $descriptionParts);
    
    // Restaurant image — use dynamic branded OG image endpoint
    // Falls back to static photo if OG image generation fails (the controller handles this)
    $ogImage = route('og-image', ['slug' => $restaurant->slug]);
}

// City guide page specific OG tags
if ($city && $state && $restaurantCount) {
    $ogTitle = "Best Mexican Restaurants in {$city} | Top {$restaurantCount} Rated";
    $ogDescription = "Discover the best Mexican food in {$city}, {$state}. {$restaurantCount} restaurants rated {$ratingStars}";
    $ogImage = asset("images/city-guides/{$state}/{$city}.jpg"); // Fallback to default if not exists
}

// Default fallbacks
$ogTitle = $ogTitle ?? config('app.name') . ' - ' . __('app.tagline');
$ogDescription = $ogDescription ?? __('app.tagline') . ' - Descubre los mejores restaurantes mexicanos auténticos en Estados Unidos';
$ogImage = $ogImage ?? asset('images/branding/og-default.jpg');

// Ensure image is absolute URL
if ($ogImage && !str_starts_with($ogImage, 'http')) {
    $ogImage = asset($ogImage);
}
@endphp

{{-- Open Graph Meta Tags --}}
<meta property="og:type" content="{{ $ogType }}">
<meta property="og:title" content="{{ $ogTitle }}">
<meta property="og:description" content="{{ $ogDescription }}">
<meta property="og:image" content="{{ $ogImage }}">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">
<meta property="og:url" content="{{ $ogUrl }}">
<meta property="og:site_name" content="Restaurantes Mexicanos Famosos">

{{-- Twitter Card Meta Tags --}}
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $ogTitle }}">
<meta name="twitter:description" content="{{ $ogDescription }}">
<meta name="twitter:image" content="{{ $ogImage }}">

{{-- Additional restaurant-specific meta --}}
@if($restaurant)
    <meta property="restaurant:contact_info:street_address" content="{{ $restaurant->address }}">
    <meta property="restaurant:contact_info:locality" content="{{ $restaurant->city }}">
    <meta property="restaurant:contact_info:region" content="{{ $restaurant->state ? $restaurant->state->code : '' }}">
    <meta property="restaurant:contact_info:postal_code" content="{{ $restaurant->zip_code }}">
    <meta property="restaurant:contact_info:country_name" content="{{ $restaurant->country ?? 'United States' }}">
    @if($restaurant->phone)
        <meta property="restaurant:contact_info:phone_number" content="{{ $restaurant->phone }}">
    @endif
    @if($restaurant->website)
        <meta property="restaurant:contact_info:website" content="{{ $restaurant->website }}">
    @endif
@endif

{{-- SEO Meta Tags --}}
<meta name="title" content="{{ $ogTitle }}">
<meta name="description" content="{{ $ogDescription }}">
<link rel="canonical" href="{{ $ogUrl }}">
