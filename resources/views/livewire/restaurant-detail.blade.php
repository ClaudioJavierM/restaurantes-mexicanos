@php
    // Determine the best image for SEO/OG with external URL support
    $seoImage = null;
    if ($restaurant->image) {
        $seoImage = str_starts_with($restaurant->image, 'http')
            ? $restaurant->image
            : asset('storage/' . $restaurant->image);
    } elseif ($restaurant->getFirstMediaUrl('images')) {
        $seoImage = $restaurant->getFirstMediaUrl('images');
    } elseif (is_array($restaurant->yelp_photos) && count($restaurant->yelp_photos) > 0) {
        $seoImage = $restaurant->yelp_photos[0];
    } else {
        $seoImage = asset('images/restaurant-placeholder.jpg');
    }

    // Calculate weighted average rating for SEO and display
    $seoInternalReviewCount = $restaurant->reviews()->where('status', 'approved')->count();
    $seoGoogleReviews = $restaurant->google_reviews_count ?? 0;
    $seoYelpReviews = $restaurant->yelp_reviews_count ?? 0;
    $seoCombinedReviews = $seoGoogleReviews + $seoYelpReviews + $seoInternalReviewCount;

    $seoGoogleRating = $restaurant->google_rating ?? 0;
    $seoYelpRating = $restaurant->yelp_rating ?? 0;
    $seoInternalRating = $restaurant->average_rating ?? 0;

    $seoTotalWeightedScore = 0;
    $seoTotalWeight = 0;

    if ($seoGoogleRating > 0 && $seoGoogleReviews > 0) {
        $seoTotalWeightedScore += $seoGoogleRating * $seoGoogleReviews;
        $seoTotalWeight += $seoGoogleReviews;
    }
    if ($seoYelpRating > 0 && $seoYelpReviews > 0) {
        $seoTotalWeightedScore += $seoYelpRating * $seoYelpReviews;
        $seoTotalWeight += $seoYelpReviews;
    }
    if ($seoInternalRating > 0 && $seoInternalReviewCount > 0) {
        $seoTotalWeightedScore += $seoInternalRating * $seoInternalReviewCount;
        $seoTotalWeight += $seoInternalReviewCount;
    }

    $seoDisplayRating = $seoTotalWeight > 0
        ? round($seoTotalWeightedScore / $seoTotalWeight, 1)
        : ($seoGoogleRating ?: ($seoYelpRating ?: 0));

    // Collect all available photos
    $allPhotos = [];
    if ($restaurant->image) {
        $allPhotos[] = str_starts_with($restaurant->image, 'http')
            ? $restaurant->image
            : asset('storage/' . $restaurant->image);
    }
    foreach ($restaurant->getMedia('images') as $media) {
        $allPhotos[] = $media->getUrl();
    }
    if (is_array($restaurant->yelp_photos)) {
        foreach ($restaurant->yelp_photos as $yelpPhoto) {
            $allPhotos[] = $yelpPhoto;
        }
    }
    // Include user-uploaded photos (from owner panel)
    foreach ($restaurant->userPhotos()->where('status', 'approved')->orderBy('created_at', 'desc')->get() as $userPhoto) {
        $allPhotos[] = asset('storage/' . $userPhoto->photo_path);
    }
    $allPhotos = array_unique($allPhotos);

    // Free plan: limit gallery to 5 most recent photos
    $isFreePlan = empty($restaurant->subscription_tier) || $restaurant->subscription_tier === 'free';
    if ($isFreePlan && count($allPhotos) > 5) {
        $allPhotos = array_slice($allPhotos, -5);
    }

    $totalPhotos = count($allPhotos);
    $displayPhotos = array_slice($allPhotos, 0, 5);

    // Parse hours for display
    $parsedHours = [];
    $isOpenNow = false;
    $todayHours = null;
    $today = date('w'); // 0 = Sunday

    if (!empty($restaurant->opening_hours)) {
        // Google Places API format: {weekday_text: [...], open_now: bool}
        $hoursData = is_string($restaurant->opening_hours) ? json_decode($restaurant->opening_hours, true) : $restaurant->opening_hours;
        if (isset($hoursData['weekday_text']) && is_array($hoursData['weekday_text'])) {
            foreach ($hoursData['weekday_text'] as $index => $text) {
                $parsedHours[$index] = $text;
            }
        }
        $isOpenNow = $hoursData['open_now'] ?? false;
        $todayIndex = $today == 0 ? 6 : $today - 1;
        $todayHours = $parsedHours[$todayIndex] ?? null;
    }

    // Fallback to owner-managed hours if opening_hours is empty
    if (empty($parsedHours) && !empty($restaurant->hours)) {
        $ownerHours = is_string($restaurant->hours) ? json_decode($restaurant->hours, true) : $restaurant->hours;
        if (is_array($ownerHours) && count($ownerHours) > 0) {
            // Owner hours format: {'monday': ['11:00 AM - 10:00 PM'], ...} or {'Lunes': '10:00 AM - 10:00 PM', ...}
            $dayMap = [
                'monday' => 'Lunes', 'tuesday' => 'Martes', 'wednesday' => 'Miércoles',
                'thursday' => 'Jueves', 'friday' => 'Viernes', 'saturday' => 'Sábado',
                'sunday' => 'Domingo', 'domingo' => 'Domingo',
                'lunes' => 'Lunes', 'martes' => 'Martes', 'miércoles' => 'Miércoles',
                'miercoles' => 'Miércoles', 'jueves' => 'Jueves', 'viernes' => 'Viernes',
                'sábado' => 'Sábado', 'sabado' => 'Sábado',
            ];
            $dayOrder = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];

            // Normalize keys to Spanish day names
            $normalized = [];
            foreach ($ownerHours as $key => $val) {
                $lookupKey = mb_strtolower(trim($key));
                $spanishDay = $dayMap[$lookupKey] ?? ucfirst($key);
                // Value can be array ['11:00 AM - 10:00 PM'] or string '11:00 AM - 10:00 PM'
                if (is_array($val)) {
                    $hourStr = implode(', ', $val);
                } else {
                    $hourStr = (string) $val;
                }
                // Fix comma-separated format like "8:00 AM, 10:00 PM" to "8:00 AM - 10:00 PM"
                if (preg_match('/^(\d{1,2}:\d{2}\s*(?:AM|PM)?)\s*,\s*(\d{1,2}:\d{2}\s*(?:AM|PM)?)$/i', $hourStr, $m)) {
                    $hourStr = $m[1] . ' - ' . $m[2];
                }
                $normalized[$spanishDay] = $hourStr;
            }

            foreach ($dayOrder as $index => $dayName) {
                $hourValue = $normalized[$dayName] ?? null;
                if ($hourValue) {
                    $parsedHours[$index] = $dayName . ': ' . $hourValue;
                } else {
                    $parsedHours[$index] = $dayName . ': Cerrado';
                }
            }

            $todayIndex = $today == 0 ? 6 : $today - 1;
            $todayHours = $parsedHours[$todayIndex] ?? null;

            // Try to determine if currently open
            if ($todayHours) {
                $hoursPart = preg_replace('/^[^:]+:\s*/', '', $todayHours);
                if (stripos($hoursPart, 'cerrado') === false && stripos($hoursPart, 'closed') === false) {
                    if (preg_match('/(\d{1,2}:\d{2}\s*(?:AM|PM))\s*[-–]\s*(\d{1,2}:\d{2}\s*(?:AM|PM))/i', $hoursPart, $m)) {
                        $openTime = strtotime($m[1]);
                        $closeTime = strtotime($m[2]);
                        $now = time();
                        if ($openTime && $closeTime) {
                            $isOpenNow = ($now >= $openTime && $now <= $closeTime);
                        }
                    }
                }
            }
        }
    }
@endphp
<div>

    <!-- Cover Image Banner -->
    @php
        $coverUrl = $restaurant->image ? asset('storage/' . $restaurant->image) : null;
    @endphp
    @if($coverUrl)
        <div style="position:relative; height:260px; overflow:hidden; background:#111;">
            <img src="{{ $coverUrl }}" alt="{{ $restaurant->name }}" style="width:100%; height:100%; object-fit:cover; object-position:center; display:block;">
            <div style="position:absolute; inset:0; background:linear-gradient(to top, rgba(0,0,0,0.5) 0%, transparent 60%);"></div>
            @if($totalPhotos > 0)
                <div style="position:absolute; bottom:12px; right:16px; z-index:10;">
                    <button wire:click="switchTab('photos')" style="background:rgba(255,255,255,0.92); color:#111; padding:7px 16px; border-radius:8px; font-size:13px; font-weight:600; border:none; cursor:pointer; display:flex; align-items:center; gap:6px; box-shadow:0 2px 8px rgba(0,0,0,0.3);">
                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        Ver {{ $totalPhotos }} fotos
                    </button>
                </div>
            @endif
        </div>
    @else
        <div style="position:relative; height:200px; background:linear-gradient(135deg, #1F2937, #111827); display:flex; align-items:center; justify-content:center;">
            <div style="text-align:center; color:white;">
                <span style="font-size:64px; display:block; margin-bottom:8px;">🍽️</span>
                <p style="font-size:16px; opacity:0.8;">{{ $restaurant->name }}</p>
            </div>
            @if($totalPhotos > 0)
                <div style="position:absolute; bottom:12px; right:16px; z-index:10;">
                    <button wire:click="switchTab('photos')" style="background:rgba(255,255,255,0.92); color:#111; padding:7px 16px; border-radius:8px; font-size:13px; font-weight:600; border:none; cursor:pointer; display:flex; align-items:center; gap:6px; box-shadow:0 2px 8px rgba(0,0,0,0.3);">
                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        Ver {{ $totalPhotos }} fotos
                    </button>
                </div>
            @endif
        </div>
    @endif

    <!-- Main Content Area -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="lg:flex lg:gap-8">
            <!-- Left Column - Main Content -->
            <div class="min-w-0" style="flex: 2 1 0%">
                <!-- Restaurant Header Info -->
                <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
                    <!-- Category & Badges Row -->
                    <div class="flex flex-wrap items-center gap-2 mb-3">
                        <span class="bg-red-100 text-red-800 text-sm font-semibold px-3 py-1 rounded-full">{{ $restaurant->category->name }}</span>
                        @if($restaurant->is_claimed)
                            <span class="inline-flex items-center bg-blue-100 text-blue-800 text-sm font-semibold px-3 py-1 rounded-full">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                Verificado
                            </span>
                        @endif
                        @if($restaurant->price_range)
                            <span class="text-gray-600 font-medium">{{ $restaurant->price_range }}</span>
                        @endif
                        @if($restaurant->google_verified || $restaurant->google_place_id)
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-white text-gray-700 border border-gray-200 shadow-sm">
                                <svg class="w-3.5 h-3.5 mr-1" viewBox="0 0 24 24"><path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/><path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/><path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/><path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/></svg>
                                Google
                            </span>
                        @endif
                        @if($restaurant->yelp_id && $restaurant->yelp_rating)
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-[#AF0606] text-white shadow-sm">
                                <svg class="w-3.5 h-3.5 mr-1" viewBox="0 0 24 24" fill="currentColor"><path d="M20.16 12.594l-4.995 1.433c-.96.276-1.74-.8-1.176-1.63l2.905-4.308a1.072 1.072 0 011.596-.206 9.194 9.194 0 011.67 4.711z"/></svg>
                                Yelp
                            </span>
                        @endif
                    </div>

                    <!-- Restaurant Name with Logo -->
                    <div class="flex items-center gap-4 mb-2">
                        @if($restaurant->logo)
                            <img src="{{ asset('storage/' . $restaurant->logo) }}" alt="{{ $restaurant->name }}" class="w-16 h-16 md:w-20 md:h-20 rounded-full object-cover border-2 border-gray-200 shadow-sm flex-shrink-0">
                        @endif
                        <h1 class="text-3xl md:text-4xl font-bold text-gray-900">{{ $restaurant->name }}</h1>
                    </div>

                    <!-- Rating Row -->
                    @php
                        $internalReviewCount = $restaurant->reviews()->where('status', 'approved')->count();
                        $googleReviews = $restaurant->google_reviews_count ?? 0;
                        $yelpReviews = $restaurant->yelp_reviews_count ?? 0;
                        $combinedReviews = $googleReviews + $yelpReviews + $internalReviewCount;
                        $googleRating = $restaurant->google_rating ?? 0;
                        $yelpRating = $restaurant->yelp_rating ?? 0;
                        $internalRating = $internalReviewCount > 0 ? $restaurant->reviews()->where('status', 'approved')->avg('rating') : 0;
                        $totalWeightedScore = 0;
                        $totalWeight = 0;
                        if ($googleRating > 0 && $googleReviews > 0) { $totalWeightedScore += $googleRating * $googleReviews; $totalWeight += $googleReviews; }
                        if ($yelpRating > 0 && $yelpReviews > 0) { $totalWeightedScore += $yelpRating * $yelpReviews; $totalWeight += $yelpReviews; }
                        if ($internalRating > 0 && $internalReviewCount > 0) { $totalWeightedScore += $internalRating * $internalReviewCount; $totalWeight += $internalReviewCount; }
                        $displayRating = $totalWeight > 0 ? $totalWeightedScore / $totalWeight : ($googleRating ?: ($yelpRating ?: 0));
                    @endphp
                    <div class="flex items-center gap-3 mb-2">
                        <div class="flex text-red-500">
                            @for($i = 0; $i < 5; $i++)
                                @if($i < floor($displayRating))
                                    <svg class="w-6 h-6 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                @elseif($i < $displayRating)
                                    <svg class="w-6 h-6" viewBox="0 0 20 20"><defs><linearGradient id="half"><stop offset="50%" stop-color="#ef4444"/><stop offset="50%" stop-color="#d1d5db"/></linearGradient></defs><path fill="url(#half)" d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                @else
                                    <svg class="w-6 h-6 fill-gray-300" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                @endif
                            @endfor
                        </div>
                        <span class="text-lg font-bold text-gray-900">{{ number_format($displayRating, 1) }}</span>
                        <a href="#reviews" wire:click="switchTab('reviews')" class="text-gray-600 hover:text-red-600">({{ number_format($combinedReviews) }} resenas)</a>
                    </div>

                    <!-- Review Sources Breakdown -->
                    <div class="flex flex-wrap items-center gap-3 mb-3 text-sm">
                        @if($googleReviews > 0)
                            <div class="flex items-center gap-1.5 bg-gray-50 px-3 py-1.5 rounded-full">
                                <svg class="w-4 h-4" viewBox="0 0 24 24"><path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/><path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/><path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/><path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/></svg>
                                <span class="text-gray-700"><strong>{{ number_format($googleRating, 1) }}</strong> ({{ number_format($googleReviews) }})</span>
                            </div>
                        @endif
                        @if($yelpReviews > 0)
                            <div class="flex items-center gap-1.5 bg-[#AF0606]/10 px-3 py-1.5 rounded-full">
                                <svg class="w-4 h-4 text-[#AF0606]" viewBox="0 0 24 24" fill="currentColor"><path d="M20.16 12.594l-4.995 1.433c-.96.276-1.74-.8-1.176-1.63l2.905-4.308a1.072 1.072 0 011.596-.206 9.194 9.194 0 011.67 4.711zm-6.728 6.272a1.07 1.07 0 01-.18 1.602 9.2 9.2 0 01-4.638 1.838 1.073 1.073 0 01-1.143-.857l-.887-5.166c-.171-1.001.956-1.65 1.705-1.006l3.143 2.589zm-8.476-2.612a9.2 9.2 0 01-.934-4.98 1.072 1.072 0 011.32-.897l5.022 1.524c.97.294.97 1.69 0 1.984l-5.022 1.524a1.07 1.07 0 01-.386-.155zm3.042-8.476l.887-5.166a1.073 1.073 0 011.143-.857 9.2 9.2 0 014.638 1.838 1.07 1.07 0 01.18 1.602L11.7 8.784c-.749.643-1.876-.005-1.705-1.006zm.84-6.184a1.072 1.072 0 011.596.206l2.905 4.308c.564.83-.216 1.906-1.176 1.63l-4.995-1.433a1.073 1.073 0 01-.426-1.808 9.19 9.19 0 012.096-2.903z"/></svg>
                                <span class="text-gray-700"><strong>{{ number_format($yelpRating, 1) }}</strong> ({{ number_format($yelpReviews) }})</span>
                            </div>
                        @endif
                        @if($internalReviewCount > 0)
                            <div class="flex items-center gap-1.5 bg-yellow-50 px-3 py-1.5 rounded-full">
                                <svg class="w-4 h-4 text-yellow-600" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                <span class="text-gray-700"><strong>{{ number_format($internalRating, 1) }}</strong> ({{ number_format($internalReviewCount) }} FAMER)</span>
                            </div>
                        @endif
                    </div>

                    <!-- Hours Status -->
                    @if($todayHours)
                        <div class="flex items-center gap-2 mb-4 text-sm">
                            @if($isOpenNow)
                                <span class="text-green-600 font-semibold">Abierto</span>
                            @else
                                <span class="text-danger-600 font-semibold">Cerrado</span>
                            @endif
                            <span class="text-gray-600">{{ preg_replace('/^[^:]+:\s*/', '', $todayHours) }}</span>
                            <button onclick="document.getElementById('hours-section').scrollIntoView({behavior: 'smooth'})" class="text-blue-600 hover:underline">Ver horarios</button>
                        </div>
                    @endif

                    <!-- Action Buttons - Yelp Style -->
                    <div class="flex flex-wrap gap-3 pt-4 border-t border-gray-100">
                        <a href="#write-review" wire:click="switchTab('reviews')" class="inline-flex items-center px-4 py-2.5 bg-[#AF0606] text-white font-semibold rounded-lg hover:bg-[#8B0505] transition-colors">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                            Escribir reseña
                        </a>
                        <button wire:click="switchTab('photos')" class="inline-flex items-center px-4 py-2.5 bg-white border border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 transition-colors">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            Añadir foto
                        </button>
                        <div x-data="{ open: false }" class="relative">
                            <button @click="open = !open" class="inline-flex items-center px-4 py-2.5 bg-white border border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 transition-colors">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/></svg>
                                Compartir
                            </button>
                            <div x-show="open" @click.away="open = false" x-transition class="absolute left-0 mt-2 w-48 bg-white rounded-lg shadow-lg border z-50">
                                <x-social-share :url="url()->current()" :title="$restaurant->name" :description="$restaurant->description ?: __('app.tagline')" layout="vertical" />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- About / Description Section (SEO) -->
                <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Acerca de {{ $restaurant->name }}</h2>
                    @if($restaurant->description)
                        <div class="prose prose-gray max-w-none">
                            <p class="text-gray-700 leading-relaxed">{{ $restaurant->description }}</p>
                        </div>
                    @else
                        <div class="bg-gray-50 rounded-lg p-4 border border-dashed border-gray-300">
                            <p class="text-gray-500 italic mb-3">
                                Este restaurante aún no tiene una descripción detallada.
                            </p>
                            @if(!$restaurant->is_claimed)
                                <p class="text-sm text-gray-600">
                                    <a href="/claim?restaurant={{ $restaurant->slug }}" class="text-green-600 font-semibold hover:underline">¿Eres el dueño?</a>
                                    Reclama este negocio para añadir una descripción, fotos, menú y más información que ayudará a los clientes a encontrarte.
                                </p>
                            @endif
                        </div>
                    @endif

                    <!-- Quick Info Tags -->
                    <div class="flex flex-wrap gap-2 mt-4 pt-4 border-t border-gray-100">
                        <span class="inline-flex items-center px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-sm">
                            <svg class="w-4 h-4 mr-1.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            {{ $restaurant->city }}, {{ $restaurant->state->code }}
                        </span>
                        @if($restaurant->price_range)
                            <span class="inline-flex items-center px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-sm">
                                <svg class="w-4 h-4 mr-1.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                {{ $restaurant->price_range }}
                            </span>
                        @endif
                        <span class="inline-flex items-center px-3 py-1 bg-red-100 text-red-700 rounded-full text-sm">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                            {{ $restaurant->category->name }}
                        </span>
                        @if($restaurant->phone)
                            <span class="inline-flex items-center px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-sm">
                                <svg class="w-4 h-4 mr-1.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                {{ $restaurant->phone }}
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Rating Distribution Section -->
                <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Calificación general</h2>
                    <div class="flex flex-col md:flex-row gap-8">
                        <!-- Overall Rating -->
                        <div class="text-center md:text-left">
                            <div class="flex text-red-500 justify-center md:justify-start mb-2">
                                @for($i = 0; $i < 5; $i++)
                                    <svg class="w-8 h-8 {{ $i < floor($displayRating) ? 'fill-current' : 'fill-gray-300' }}" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                @endfor
                            </div>
                            <p class="text-gray-500">{{ number_format($combinedReviews) }} reseñas</p>
                        </div>

                        <!-- Rating Distribution Bars -->
                        @php
                            // Calculate real distribution from internal reviews
                            $ratingCounts = $restaurant->reviews()->where('status', 'approved')
                                ->selectRaw('rating, COUNT(*) as count')
                                ->groupBy('rating')
                                ->pluck('count', 'rating')
                                ->toArray();
                            $distribution = [
                                5 => $ratingCounts[5] ?? 0,
                                4 => $ratingCounts[4] ?? 0,
                                3 => $ratingCounts[3] ?? 0,
                                2 => $ratingCounts[2] ?? 0,
                                1 => $ratingCounts[1] ?? 0,
                            ];
                            // If no internal reviews but has external, estimate from external
                            if (array_sum($distribution) === 0 && ($googleReviews ?? 0) + ($yelpReviews ?? 0) > 0) {
                                $extTotal = ($googleReviews ?? 0) + ($yelpReviews ?? 0);
                                $distribution = [
                                    5 => round($extTotal * 0.65),
                                    4 => round($extTotal * 0.20),
                                    3 => round($extTotal * 0.08),
                                    2 => round($extTotal * 0.04),
                                    1 => round($extTotal * 0.03),
                                ];
                            }
                            $maxDist = max($distribution) ?: 1;
                        @endphp
                        <div class="flex-1 space-y-2">
                            @for($stars = 5; $stars >= 1; $stars--)
                                <div class="flex items-center gap-2">
                                    <span class="w-12 text-sm text-gray-700">{{ $stars }} ★</span>
                                    <div class="flex-1 h-5 bg-gray-200 rounded-full overflow-hidden">
                                        <div class="h-full bg-red-500 rounded-full transition-all duration-500" style="width: {{ ($distribution[$stars] / $maxDist) * 100 }}%"></div>
                                    </div>
                                </div>
                            @endfor
                        </div>
                    </div>
                </div>

                <!-- Location & Hours Section -->
                <div id="hours-section" class="bg-white rounded-lg shadow-lg p-6 mb-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Ubicación y Horarios</h2>
                    <div class="grid md:grid-cols-2 gap-6">
                        <!-- Map -->
                        <div>
                            <x-google-map :name="$restaurant->name" :address="$restaurant->address . ', ' . $restaurant->city . ', ' . $restaurant->state->code . ' ' . $restaurant->zip_code" height="250" zoom="15" />
                            <div class="mt-3">
                                <a href="https://www.google.com/maps/dir/?api=1&destination={{ urlencode($restaurant->address . ', ' . $restaurant->city . ', ' . $restaurant->state->code) }}" target="_blank" class="text-blue-600 hover:underline font-medium">
                                    Cómo llegar
                                </a>
                            </div>
                            <p class="text-gray-700 mt-2">{{ $restaurant->address }}</p>
                            <p class="text-gray-600">{{ $restaurant->city }}, {{ $restaurant->state->code }} {{ $restaurant->zip_code }}</p>
                        </div>
                        <!-- Hours -->
                        <div>
                            @if(count($parsedHours) > 0)
                                <div class="space-y-2">
                                    @foreach($parsedHours as $index => $hours)
                                        @php
                                            $isToday = ($index == ($today == 0 ? 6 : $today - 1));
                                        @endphp
                                        <div class="flex justify-between py-1 {{ $isToday ? 'font-bold text-gray-900' : 'text-gray-600' }}">
                                            <span>{{ $hours }}</span>
                                            @if($isToday && $isOpenNow)
                                                <span class="text-green-600 text-sm">Abierto ahora</span>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-gray-500 italic">Horarios no disponibles</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Amenities Section -->
                @if($restaurant->atmosphere || $restaurant->dietary_options || $restaurant->special_features)
                <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Características</h2>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                        @if($restaurant->accepts_reservations)
                            <div class="flex items-center gap-2 text-gray-700"><svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Acepta reservaciones</div>
                        @endif
                        @if($restaurant->online_ordering)
                            <div class="flex items-center gap-2 text-gray-700"><svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Pedidos en línea</div>
                        @endif
                        @if(is_array($restaurant->atmosphere))
                            @foreach($restaurant->atmosphere as $atm)
                                <div class="flex items-center gap-2 text-gray-700">
                                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    {{ ucfirst(str_replace('_', ' ', $atm)) }}
                                </div>
                            @endforeach
                        @endif
                        @if($restaurant->has_fresh_tortillas)
                            <div class="flex items-center gap-2 text-gray-700"><svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Tortillas frescas</div>
                        @endif
                        @if($restaurant->has_aguas_frescas)
                            <div class="flex items-center gap-2 text-gray-700"><svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Aguas frescas</div>
                        @endif
                        @if($restaurant->has_homemade_salsa)
                            <div class="flex items-center gap-2 text-gray-700"><svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Salsa casera</div>
                        @endif
                    </div>
                </div>
                @endif

                <!-- Menu Section (Popular Dishes) -->
                @if($menuItems->count() > 0)
                <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-xl font-bold text-gray-900">Menú</h2>
                        <button wire:click="switchTab('menu')" class="text-blue-600 hover:underline font-medium">Ver menú completo</button>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Platillos Populares</h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        @foreach($menuItems->take(4) as $item)
                            <div wire:click="showMenuItem({{ $item->id }})" class="cursor-pointer group">
                                <div class="aspect-square rounded-lg overflow-hidden bg-gray-100 mb-2 relative">
                                    @if($item->image)
                                        <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-4xl">🌮</div>
                                    @endif
                                    @if($item->price)
                                        <div class="absolute bottom-2 left-2 bg-black/70 text-white px-2 py-1 rounded text-sm font-semibold">${{ number_format($item->price, 2) }}</div>
                                    @endif
                                </div>
                                <p class="font-medium text-gray-900 group-hover:text-red-600 transition-colors truncate">{{ $item->name }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Reviews Section -->
                <div id="reviews" class="bg-white rounded-lg shadow-lg p-6 mb-6">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-xl font-bold text-gray-900">Reseñas Recomendadas</h2>
                    </div>
                    <div id="write-review" class="mb-6">
                        @livewire('write-review', ['restaurant' => $restaurant])
                    </div>
                    @livewire('review-list', ['restaurant' => $restaurant])
                </div>
            </div>

            <!-- Right Column - Sticky Sidebar -->
            <div class="min-w-0" style="flex: 1 1 0%">
                <div class="lg:sticky lg:top-4 space-y-4">
                    <!-- Order Food Widget (Yelp Style) -->
                    @php
                        $hasDeliveryOptions = $restaurant->doordash_url || $restaurant->ubereats_url || $restaurant->grubhub_url;
                        $hasAnyOrderOption = $restaurant->order_url || $hasDeliveryOptions;
                    @endphp
                    @if($hasAnyOrderOption)
                    <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden" x-data="{ activeTab: '{{ $hasDeliveryOptions ? 'delivery' : 'pickup' }}' }">
                        <div class="p-5 border-b border-gray-100">
                            <h3 class="text-xl font-bold text-gray-900">Ordenar Comida</h3>
                        </div>

                        <!-- Tabs -->
                        <div class="flex border-b border-gray-200">
                            @if($restaurant->order_url)
                            <button @click="activeTab = 'pickup'"
                                    :class="activeTab === 'pickup' ? 'border-b-2 border-red-600 text-gray-900' : 'text-gray-500'"
                                    class="flex-1 py-3 px-4 text-sm font-medium hover:text-gray-900 transition-colors">
                                Para llevar
                            </button>
                            @endif
                            @if($hasDeliveryOptions)
                            <button @click="activeTab = 'delivery'"
                                    :class="activeTab === 'delivery' ? 'border-b-2 border-red-600 text-gray-900' : 'text-gray-500'"
                                    class="flex-1 py-3 px-4 text-sm font-medium hover:text-gray-900 transition-colors">
                                Delivery
                            </button>
                            @endif
                        </div>

                        <div class="p-5">
                            <!-- Pickup Tab -->
                            @if($restaurant->order_url)
                            <div x-show="activeTab === 'pickup'" x-cloak>
                                <div class="flex items-center gap-4 text-sm text-gray-600 mb-4">
                                    <span class="font-semibold text-gray-900">Sin cargos extra</span>
                                    <span class="text-gray-300">|</span>
                                    <span>Listo en <strong class="text-gray-900">10-20</strong> min</span>
                                </div>
                                <a href="{{ $restaurant->order_url }}" target="_blank" rel="nofollow noopener"
                                   class="block w-full py-3 px-4 bg-red-600 hover:bg-red-700 text-white font-semibold text-center rounded-lg transition-colors">
                                    Ordenar Ahora
                                </a>
                            </div>
                            @endif

                            <!-- Delivery Tab -->
                            @if($hasDeliveryOptions)
                            <div x-show="activeTab === 'delivery'" x-cloak>
                                <p class="text-sm text-gray-600 mb-4">Ordena delivery a traves de:</p>
                                <div class="space-y-2">
                                    @if($restaurant->doordash_url)
                                        <a href="{{ $restaurant->doordash_url }}" target="_blank" rel="nofollow noopener"
                                           class="flex items-center justify-between w-full py-3 px-4 bg-white hover:bg-red-50 text-gray-900 font-medium rounded-lg border border-gray-200 hover:border-red-300 transition-colors group">
                                            <div class="flex items-center gap-3">
                                                <div class="w-8 h-8 bg-red-500 rounded-lg flex items-center justify-center">
                                                    <span class="text-white font-bold text-sm">D</span>
                                                </div>
                                                <span>DoorDash</span>
                                            </div>
                                            <svg class="w-5 h-5 text-gray-400 group-hover:text-red-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                        </a>
                                    @endif
                                    @if($restaurant->ubereats_url)
                                        <a href="{{ $restaurant->ubereats_url }}" target="_blank" rel="nofollow noopener"
                                           class="flex items-center justify-between w-full py-3 px-4 bg-white hover:bg-green-50 text-gray-900 font-medium rounded-lg border border-gray-200 hover:border-green-300 transition-colors group">
                                            <div class="flex items-center gap-3">
                                                <div class="w-8 h-8 bg-green-500 rounded-lg flex items-center justify-center">
                                                    <span class="text-white font-bold text-sm">U</span>
                                                </div>
                                                <span>Uber Eats</span>
                                            </div>
                                            <svg class="w-5 h-5 text-gray-400 group-hover:text-green-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                        </a>
                                    @endif
                                    @if($restaurant->grubhub_url)
                                        <a href="{{ $restaurant->grubhub_url }}" target="_blank" rel="nofollow noopener"
                                           class="flex items-center justify-between w-full py-3 px-4 bg-white hover:bg-orange-50 text-gray-900 font-medium rounded-lg border border-gray-200 hover:border-orange-300 transition-colors group">
                                            <div class="flex items-center gap-3">
                                                <div class="w-8 h-8 bg-orange-500 rounded-lg flex items-center justify-center">
                                                    <span class="text-white font-bold text-sm">G</span>
                                                </div>
                                                <span>Grubhub</span>
                                            </div>
                                            <svg class="w-5 h-5 text-gray-400 group-hover:text-orange-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                        </a>
                                    @endif
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endif

                    <!-- Contact Card (Yelp Style) -->
                    <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
                        <!-- Phone -->
                        @if($restaurant->phone)
                            <a href="tel:{{ $restaurant->phone }}" class="flex items-center justify-between p-4 hover:bg-gray-50 transition-colors border-b border-gray-100">
                                <span class="text-gray-900 font-medium">{{ $restaurant->phone }}</span>
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                            </a>
                        @endif

                        <!-- Directions -->
                        <a href="https://www.google.com/maps/dir/?api=1&destination={{ urlencode($restaurant->address . ', ' . $restaurant->city . ', ' . $restaurant->state->code) }}" target="_blank" class="flex items-center justify-between p-4 hover:bg-gray-50 transition-colors border-b border-gray-100">
                            <div>
                                <p class="text-blue-600 font-semibold">Cómo Llegar</p>
                                <p class="text-gray-600 text-sm">{{ $restaurant->address }} {{ $restaurant->city }}, {{ $restaurant->state->code }} {{ $restaurant->zip_code }}</p>
                            </div>
                            <svg class="w-5 h-5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </a>

                        <!-- External Links Row -->
                        <div class="flex divide-x divide-gray-100">
                            @if($restaurant->yelp_url || $restaurant->yelp_id)
                                <a href="{{ $restaurant->yelp_url ?: 'https://www.yelp.com/biz/' . $restaurant->yelp_id }}" target="_blank" rel="nofollow noopener" class="flex-1 flex items-center justify-center gap-2 p-4 hover:bg-gray-50 transition-colors">
                                    <svg class="w-5 h-5 text-[#AF0606]" viewBox="0 0 24 24" fill="currentColor"><path d="M20.16 12.594l-4.995 1.433c-.96.276-1.74-.8-1.176-1.63l2.905-4.308a1.072 1.072 0 011.596-.206 9.194 9.194 0 011.67 4.711z"/></svg>
                                    <span class="text-sm font-medium text-gray-700">Yelp</span>
                                </a>
                            @endif
                            @if($restaurant->google_place_id)
                                <a href="https://www.google.com/maps/place/?q=place_id:{{ $restaurant->google_place_id }}" target="_blank" rel="nofollow noopener" class="flex-1 flex items-center justify-center gap-2 p-4 hover:bg-gray-50 transition-colors">
                                    <svg class="w-5 h-5" viewBox="0 0 24 24"><path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/><path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/><path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/><path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/></svg>
                                    <span class="text-sm font-medium text-gray-700">Google</span>
                                </a>
                            @endif
                            @if($restaurant->website)
                                <a href="{{ $restaurant->website }}" target="_blank" rel="nofollow noopener" class="flex-1 flex items-center justify-center gap-2 p-4 hover:bg-gray-50 transition-colors">
                                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/></svg>
                                    <span class="text-sm font-medium text-gray-700">Web</span>
                                </a>
                            @endif
                        </div>

                        <!-- Suggest Edit -->
                        <a href="/claim?restaurant={{ $restaurant->slug }}" class="flex items-center justify-center gap-2 p-4 border-t border-gray-100 hover:bg-gray-50 transition-colors text-gray-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            <span class="text-sm">Sugerir una corrección</span>
                        </a>
                    </div>

                    <!-- Reservations (Premium/Elite only) -->
                    @if(in_array($restaurant->subscription_tier, ['premium', 'elite']))
                        @livewire('reservation-form', ['restaurant' => $restaurant])
                    @endif

                    <!-- FAMER Awards Widget -->
                    @livewire('vote-widget', ['restaurant' => $restaurant])

                    <!-- Live Statistics -->
                    @livewire('live-visits', ['restaurant' => $restaurant])

                    <!-- Claim CTA -->
                    @if(!$restaurant->is_claimed)
                        <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-5">
                            <div class="flex items-start gap-4">
                                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                </div>
                                <div class="flex-1">
                                    <h3 class="font-bold text-gray-900">¿Es tu restaurante?</h3>
                                    <p class="text-gray-600 text-sm mt-1">Reclama tu negocio para actualizar la información, responder reseñas y más.</p>
                                    <a href="/claim?restaurant={{ $restaurant->slug }}" data-turbo="false" class="inline-block mt-3 text-blue-600 hover:text-blue-700 font-semibold text-sm">
                                        Reclamar este negocio
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Ad Space -->
                    @livewire('advertisement-banner', ['placement' => 'sidebar', 'stateId' => $restaurant->state_id])
                </div>
            </div>
        </div>
    </div>

    <!-- Photos Tab Modal -->
    @if($activeTab === 'photos')
        <div class="fixed inset-0 z-50 flex items-center justify-center" style="background:rgba(0,0,0,0.6); padding:24px 16px;" wire:click.self="switchTab('info')">
            <div class="w-full max-w-5xl bg-white rounded-2xl shadow-2xl flex flex-col" style="max-height:calc(100vh - 48px);" wire:click.stop>
                <div class="flex justify-between items-center px-6 py-4 border-b border-gray-200 flex-shrink-0">
                    <h2 class="text-xl font-bold text-gray-900">Fotos de {{ $restaurant->name }}</h2>
                    <button wire:click="switchTab('info')" class="w-9 h-9 flex items-center justify-center rounded-full bg-gray-100 hover:bg-gray-200 text-gray-600 hover:text-gray-900 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <div class="p-6 overflow-y-auto flex-1">
                    @livewire('photo-gallery', ['restaurant' => $restaurant])
                </div>
            </div>
        </div>
    @endif

    <!-- Full Menu Tab -->
    @if($activeTab === 'menu')
        <div class="fixed inset-0 bg-black/80 z-50 overflow-y-auto" wire:click.self="switchTab('info')">
            <div class="max-w-5xl mx-auto py-8 px-4">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold text-white">Menu de {{ $restaurant->name }}</h2>
                    <button wire:click="switchTab('info')" class="text-white hover:text-gray-300">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <div class="bg-white rounded-xl p-6">
                    @include('livewire.partials.restaurant-menu')
                </div>
            </div>
        </div>
    @endif

    <!-- Menu Item Modal -->
    @if($showMenuItemModal && $selectedMenuItem)
        @include('livewire.partials.menu-item-modal')
    @endif
</div>
