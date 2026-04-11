@php
    // Determine the best image for SEO/OG — uses getDisplayImageUrl() which prioritizes CDN URLs over potentially-empty local files
    $seoImage = $restaurant->getDisplayImageUrl() ?? asset('images/restaurant-placeholder.jpg');

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

    // Cover image for the banner — not counted in gallery total
    $coverImageUrl = null;
    if ($restaurant->image) {
        $coverImageUrl = str_starts_with($restaurant->image, 'http')
            ? $restaurant->image
            : asset('storage/' . $restaurant->image);
    } elseif ($restaurant->getFirstMediaUrl('images')) {
        $coverImageUrl = $restaurant->getFirstMediaUrl('images');
    } elseif (is_array($restaurant->yelp_photos) && count($restaurant->yelp_photos) > 0) {
        $coverImageUrl = $restaurant->yelp_photos[0];
    }

    // Gallery count = only user-uploaded photos. Banner/Yelp images are NOT counted.
    $isFreePlan = empty($restaurant->subscription_tier) || $restaurant->subscription_tier === 'free';
    $userPhotoRecords = $restaurant->userPhotos()->where('status', 'approved')->orderBy('created_at', 'desc')->get();
    $allPhotos = $userPhotoRecords->map(fn($p) => asset('storage/' . $p->photo_path))->toArray();
    if ($isFreePlan && count($allPhotos) > 5) {
        $allPhotos = array_slice($allPhotos, 0, 5);
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
        $todayIndex = $today == 0 ? 6 : $today - 1;
        $todayHours = $parsedHours[$todayIndex] ?? null;

        // open_now from Google is a real-time field — often not stored or stale.
        // Compute dynamically when not available.
        if (isset($hoursData['open_now'])) {
            $isOpenNow = (bool) $hoursData['open_now'];
        } elseif ($todayHours) {
            $hoursPart = preg_replace('/^[^:]+:\s*/', '', $todayHours);
            if (stripos($hoursPart, 'closed') === false && stripos($hoursPart, 'cerrado') === false) {
                if (preg_match('/(\d{1,2}:\d{2}\s*(?:AM|PM))\s*[-–]\s*(\d{1,2}:\d{2}\s*(?:AM|PM))/i', $hoursPart, $hm)) {
                    // Compare using UTC offset adjusted for US Central (UTC-5/6) as default.
                    // strtotime() uses server UTC — shift back 6h to approximate restaurant local time.
                    $utcNow     = time();
                    $localNow   = $utcNow - (6 * 3600); // approx US Central offset
                    $baseDate   = date('Y-m-d', $localNow);
                    $openTime   = strtotime($baseDate . ' ' . $hm[1]);
                    $closeTime  = strtotime($baseDate . ' ' . $hm[2]);
                    // Handle overnight hours (e.g. 10 PM – 2 AM)
                    if ($closeTime <= $openTime) $closeTime += 86400;
                    if ($openTime && $closeTime) {
                        $isOpenNow = ($localNow >= $openTime && $localNow <= $closeTime);
                    }
                }
            }
        }
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

{{-- Canonical: URL permanente del restaurante sin query strings --}}
@php
    $isEnDomain = str_contains(request()->getHost(), 'famousmexicanrestaurants.com');
    $canonicalSlug = $restaurant->slug;
    $canonicalPath = $isEnDomain ? '/restaurant/' . $canonicalSlug : '/restaurante/' . $canonicalSlug;
    $canonicalBase = $isEnDomain
        ? 'https://famousmexicanrestaurants.com'
        : (str_contains(request()->getHost(), '.com.mx')
            ? 'https://restaurantesmexicanosfamosos.com.mx'
            : 'https://restaurantesmexicanosfamosos.com');
    $canonicalUrl = $canonicalBase . $canonicalPath;
@endphp
@section('canonical', $canonicalUrl)

@push('meta')
    @php
        // Ranking badge for OG title
        $ogTopRanking = $restaurant->rankings()
            ->where('is_published', true)
            ->where('position', '<=', 10)
            ->orderBy('position')
            ->first();
        $ogRankingBadge = '';
        if ($ogTopRanking) {
            $scopeLabel = match($ogTopRanking->ranking_type ?? '') {
                'national' => 'Nacional',
                'state'    => $ogTopRanking->ranking_scope ?? '',
                'city'     => $ogTopRanking->ranking_scope ?? '',
                default    => $ogTopRanking->ranking_scope ?? '',
            };
            $ogRankingBadge = '🏆 #' . $ogTopRanking->position . ' en ' . $scopeLabel . ' · ';
        }

        // Rating stars for title
        $ogWeightedRating = $seoTotalWeight > 0 ? round($seoTotalWeightedScore / $seoTotalWeight, 1) : 0;
        $ogRatingStr = $ogWeightedRating > 0 ? '⭐ ' . number_format($ogWeightedRating, 1) . ' · ' : '';

        // Description
        $ogBaseDesc = app()->getLocale() === 'en'
            ? ($restaurant->ai_description_en ?: $restaurant->ai_description ?: $restaurant->description)
            : ($restaurant->ai_description ?: $restaurant->description);

        // Build rich description
        $ogDescParts = [];
        if ($ogWeightedRating > 0 && $seoCombinedReviews > 0) {
            $ogDescParts[] = number_format($ogWeightedRating, 1) . '/5 basado en ' . number_format($seoCombinedReviews) . ' reseñas';
        }
        if ($restaurant->price_range) {
            $ogDescParts[] = $restaurant->price_range;
        }
        if ($restaurant->address) {
            $ogDescParts[] = $restaurant->address . ', ' . $restaurant->city;
        }
        $ogDescHeader = !empty($ogDescParts) ? implode(' · ', $ogDescParts) . '. ' : '';
        $ogFinalDesc = $ogDescHeader . ($ogBaseDesc ? Str::limit(strip_tags($ogBaseDesc), 160) : 'Cocina mexicana auténtica en ' . $restaurant->city . ', ' . ($restaurant->state?->name ?? 'USA') . '.');

        $ogTitle = $ogRankingBadge . $ogRatingStr . $restaurant->name . ' — ' . $restaurant->city . ', ' . ($restaurant->state?->abbreviation ?? $restaurant->state?->name ?? '');
    @endphp

    {{-- Open Graph for social sharing --}}
    <meta property="og:type" content="restaurant">
    <meta property="og:title" content="{{ $ogTitle }}">
    <meta property="og:description" content="{{ Str::limit($ogFinalDesc, 250) }}">
    <meta property="og:url" content="{{ url()->current() }}">
    @if($seoImage)
        <meta property="og:image" content="{{ $seoImage }}">
    @endif
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:image:alt" content="{{ $restaurant->name }} — {{ $restaurant->city }}">
    <meta property="og:locale" content="{{ app()->getLocale() === 'en' ? 'en_US' : 'es_MX' }}">
    <meta property="og:site_name" content="FAMER - Famous Mexican Restaurants">

    {{-- Twitter Card --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $ogTitle }}">
    <meta name="twitter:description" content="{{ Str::limit($ogFinalDesc, 200) }}">
    @if($seoImage)
        <meta name="twitter:image" content="{{ $seoImage }}">
    @endif

    {{-- ═══════════════════════════════════════════════════════
         SCHEMA.ORG — Restaurant + AggregateRating + Breadcrumb
         Enables Google rich snippets (stars, address, hours)
         ═══════════════════════════════════════════════════════ --}}
    @php
        $schemaCountry = $restaurant->state?->country ?? 'US';
        $schemaStateCode = $restaurant->state?->code ?? '';

        // Build Restaurant schema
        $restaurantSchema = [
            '@context'      => 'https://schema.org',
            '@type'         => 'Restaurant',
            'name'          => $restaurant->name,
            'url'           => url()->current(),
            'servesCuisine' => 'Mexican',
        ];

        if ($seoImage) {
            $restaurantSchema['image'] = $seoImage;
        }
        if ($restaurant->phone) {
            $restaurantSchema['telephone'] = $restaurant->phone;
        }
        if ($restaurant->google_maps_url) {
            $restaurantSchema['hasMap'] = $restaurant->google_maps_url;
        }
        if ($restaurant->price_range) {
            $restaurantSchema['priceRange'] = $restaurant->price_range;
        }
        if ($restaurant->address) {
            $restaurantSchema['address'] = [
                '@type'           => 'PostalAddress',
                'streetAddress'   => $restaurant->address,
                'addressLocality' => $restaurant->city,
                'addressRegion'   => $schemaStateCode,
                'postalCode'      => $restaurant->zip_code ?? '',
                'addressCountry'  => $schemaCountry,
            ];
        }
        if ($restaurant->latitude && $restaurant->longitude) {
            $restaurantSchema['geo'] = [
                '@type'     => 'GeoCoordinates',
                'latitude'  => (float) $restaurant->latitude,
                'longitude' => (float) $restaurant->longitude,
            ];
        }
        // AggregateRating — only if we have real review data (Google requires reviewCount ≥ 1)
        if ($seoCombinedReviews > 0 && $seoDisplayRating > 0) {
            $restaurantSchema['aggregateRating'] = [
                '@type'       => 'AggregateRating',
                'ratingValue' => (string) $seoDisplayRating,
                'reviewCount' => (string) $seoCombinedReviews,
                'bestRating'  => '5',
                'worstRating' => '1',
            ];
        }
        // sameAs for cross-platform authority signals
        $sameAs = [];
        if ($restaurant->yelp_url ?? null) { $sameAs[] = $restaurant->yelp_url; }
        if ($restaurant->google_maps_url ?? null) {
            $sameAs[] = $restaurant->google_maps_url;
        } elseif ($restaurant->google_place_id ?? null) {
            $sameAs[] = 'https://maps.google.com/?cid=' . $restaurant->google_place_id;
        }
        if (count($sameAs)) { $restaurantSchema['sameAs'] = $sameAs; }

        // currenciesAccepted based on country (US = USD, MX = MXN)
        $restaurantSchema['currenciesAccepted'] = ($schemaCountry === 'MX') ? 'MXN' : 'USD';

        // paymentAccepted — from enrichment data if available
        if (!empty($restaurant->payment_methods)) {
            $paymentData = is_string($restaurant->payment_methods)
                ? json_decode($restaurant->payment_methods, true)
                : $restaurant->payment_methods;
            if (is_array($paymentData) && count($paymentData)) {
                $restaurantSchema['paymentAccepted'] = implode(', ', $paymentData);
            }
        }

        // openingHours — schema.org compact format (Google rich results)
        if (!empty($restaurant->opening_hours)) {
            $ohData = is_string($restaurant->opening_hours)
                ? json_decode($restaurant->opening_hours, true)
                : $restaurant->opening_hours;
            $weekdayText = $ohData['weekday_text'] ?? null;
            if ($weekdayText && is_array($weekdayText)) {
                $googleService = app(\App\Services\GooglePlacesService::class);
                $schemaHours = $googleService->parseOpeningHoursSchema($weekdayText);
                if (!empty($schemaHours)) {
                    $restaurantSchema['openingHours'] = $schemaHours;
                }
            }
        }

        // BreadcrumbList schema
        $breadcrumbSchema = [
            '@context'        => 'https://schema.org',
            '@type'           => 'BreadcrumbList',
            'itemListElement' => [
                [
                    '@type'    => 'ListItem',
                    'position' => 1,
                    'name'     => 'FAMER',
                    'item'     => url('/'),
                ],
                [
                    '@type'    => 'ListItem',
                    'position' => 2,
                    'name'     => app()->getLocale() === 'en' ? 'Restaurants' : 'Restaurantes',
                    'item'     => url('/restaurantes'),
                ],
            ],
        ];
        if ($restaurant->state) {
            $breadcrumbSchema['itemListElement'][] = [
                '@type'    => 'ListItem',
                'position' => 3,
                'name'     => $restaurant->state->name,
                'item'     => url('/restaurantes?state=' . urlencode($restaurant->state->name)),
            ];
            $breadcrumbSchema['itemListElement'][] = [
                '@type'    => 'ListItem',
                'position' => 4,
                'name'     => $restaurant->name,
                'item'     => url()->current(),
            ];
        } else {
            $breadcrumbSchema['itemListElement'][] = [
                '@type'    => 'ListItem',
                'position' => 3,
                'name'     => $restaurant->name,
                'item'     => url()->current(),
            ];
        }
    @endphp

    <script type="application/ld+json">
    {!! json_encode($restaurantSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
    </script>
    <script type="application/ld+json">
    {!! json_encode($breadcrumbSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
    </script>

    {{-- FAQPage Schema --}}
    @if(count($faqItems ?? []) > 0)
    @php
        $faqSchema = [
            '@context'   => 'https://schema.org',
            '@type'      => 'FAQPage',
            'mainEntity' => array_map(fn($item) => [
                '@type' => 'Question',
                'name'  => $item['q'],
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text'  => $item['a'],
                ],
            ], $faqItems),
        ];
    @endphp
    <script type="application/ld+json">
    {!! json_encode($faqSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
    </script>
    @endif
@endpush

<div>

    <!-- Cover Image Banner -->
    @php
        $heroRankings = $restaurant->rankings()
            ->where('year', now()->year - 1)
            ->where('position', '<=', 25)
            ->where('is_published', true)
            ->orderBy('position')
            ->get();
        $bestRanking = $heroRankings->first();
    @endphp
    {{-- Trophy animation keyframes — injected once --}}
    @once
    @push('styles')
    <style>
        @keyframes trophy-glow {
            0%, 100% { filter: drop-shadow(0 0 4px rgba(212,175,55,0.6)) drop-shadow(0 0 8px rgba(212,175,55,0.3)); transform: scale(1); }
            50%       { filter: drop-shadow(0 0 10px rgba(245,208,96,0.9)) drop-shadow(0 0 20px rgba(212,175,55,0.5)); transform: scale(1.08); }
        }
        .famer-trophy { animation: trophy-glow 2.4s ease-in-out infinite; display:inline-block; }
    </style>
    @endpush
    @endonce

    @if($coverImageUrl)
        <div style="position:relative; height:260px; overflow:hidden; background:#111;">
            <img src="{{ $coverImageUrl }}"
                 alt="{{ $restaurant->name }}"
                 style="width:100%; height:100%; object-fit:cover; object-position:center; display:block;"
                 fetchpriority="high"
                 decoding="async"
                 onerror="this.style.display='none';">
            <div style="position:absolute; inset:0; background:linear-gradient(to top, rgba(0,0,0,0.65) 0%, rgba(0,0,0,0.1) 50%, transparent 80%); pointer-events:none;"></div>

            {{-- Award Badge — top-LEFT, animated trophy emoji --}}
            @if($bestRanking)
            <div style="position:absolute; top:12px; left:16px; z-index:10; display:flex; flex-direction:column; align-items:flex-start; gap:6px;">
                <a href="{{ url('/guia') }}?scope={{ $bestRanking->ranking_type }}{{ $bestRanking->ranking_type !== 'national' ? '&state=' . $bestRanking->ranking_scope : '' }}"
                   style="text-decoration:none;">
                    <div style="background:linear-gradient(135deg,#B8860B,#D4AF37 40%,#F5D060 70%,#D4AF37); border-radius:14px; padding:8px 16px 8px 10px; display:flex; align-items:center; gap:10px; box-shadow:0 4px 20px rgba(0,0,0,0.5), inset 0 1px 0 rgba(255,255,255,0.35); border:1px solid rgba(255,255,255,0.2);">
                        <span class="famer-trophy" style="font-size:30px; line-height:1;">🏆</span>
                        <div>
                            <div style="display:flex; align-items:baseline; gap:6px; line-height:1;">
                                <span style="font-size:22px; font-weight:900; color:#1a1a2e;">#{{ $bestRanking->position }}</span>
                                <span style="font-size:13px; font-weight:800; color:#1a1a2e; text-transform:uppercase; letter-spacing:0.5px;">{{ $bestRanking->ranking_type === 'city' ? $bestRanking->ranking_scope : ($bestRanking->ranking_type === 'state' ? $bestRanking->ranking_scope : 'USA') }}</span>
                            </div>
                            <div style="font-size:9px; color:rgba(26,26,46,0.55); text-transform:uppercase; letter-spacing:2px; font-weight:700; margin-top:2px;">FAMER Awards {{ $bestRanking->year }}</div>
                        </div>
                    </div>
                </a>
                {{-- Additional rankings as small dark pills --}}
                @foreach($heroRankings->skip(1)->take(2) as $ranking)
                    <div style="background:rgba(0,0,0,0.72); backdrop-filter:blur(8px); border-radius:8px; padding:4px 12px; display:flex; align-items:center; gap:8px; border:1px solid rgba(212,175,55,0.35);">
                        <span style="font-size:13px;">🏆</span>
                        <span style="font-size:14px; font-weight:800; color:#F5D060;">#{{ $ranking->position }}</span>
                        <span style="font-size:10px; color:rgba(255,255,255,0.75); text-transform:uppercase; letter-spacing:1px; font-weight:600;">{{ Str::limit($ranking->ranking_scope, 14) }}</span>
                    </div>
                @endforeach
            </div>
            @endif

            {{-- Photos button — always bottom-right --}}
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

            {{-- Award Badge on fallback — top-LEFT --}}
            @if($bestRanking)
            <div style="position:absolute; top:12px; left:16px; z-index:10; display:flex; flex-direction:column; align-items:flex-start; gap:6px;">
                <div style="background:linear-gradient(135deg,#B8860B,#D4AF37 40%,#F5D060 70%,#D4AF37); border-radius:14px; padding:8px 16px 8px 10px; display:flex; align-items:center; gap:10px; box-shadow:0 4px 20px rgba(0,0,0,0.5), inset 0 1px 0 rgba(255,255,255,0.35); border:1px solid rgba(255,255,255,0.2);">
                    <span class="famer-trophy" style="font-size:30px; line-height:1;">🏆</span>
                    <div>
                        <div style="display:flex; align-items:baseline; gap:6px; line-height:1;">
                            <span style="font-size:22px; font-weight:900; color:#1a1a2e;">#{{ $bestRanking->position }}</span>
                            <span style="font-size:13px; font-weight:800; color:#1a1a2e; text-transform:uppercase; letter-spacing:0.5px;">{{ $bestRanking->ranking_type === 'city' ? $bestRanking->ranking_scope : ($bestRanking->ranking_type === 'state' ? $bestRanking->ranking_scope : 'USA') }}</span>
                        </div>
                        <div style="font-size:9px; color:rgba(26,26,46,0.55); text-transform:uppercase; letter-spacing:2px; font-weight:700; margin-top:2px;">FAMER Awards {{ $bestRanking->year }}</div>
                    </div>
                </div>
                @foreach($heroRankings->skip(1)->take(2) as $ranking)
                    <div style="background:rgba(0,0,0,0.72); backdrop-filter:blur(8px); border-radius:8px; padding:4px 12px; display:flex; align-items:center; gap:8px; border:1px solid rgba(212,175,55,0.35);">
                        <span style="font-size:13px;">🏆</span>
                        <span style="font-size:14px; font-weight:800; color:#F5D060;">#{{ $ranking->position }}</span>
                        <span style="font-size:10px; color:rgba(255,255,255,0.75); text-transform:uppercase; letter-spacing:1px; font-weight:600;">{{ Str::limit($ranking->ranking_scope, 14) }}</span>
                    </div>
                @endforeach
            </div>
            @endif

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

    {{-- Photo Gallery — Horizontal scroll strip with inline lightbox --}}
    @php
        $googlePhotos = collect($restaurant->photos ?? [])->map(function($p) {
            return str_starts_with($p, 'http') ? $p : \Illuminate\Support\Facades\Storage::url($p);
        })->toArray();
        $lightboxPhotos = array_values(array_unique(array_merge($googlePhotos, $allPhotos)));
        $galleryDisplay = array_slice($lightboxPhotos, 0, 5);
        $lightboxTotal  = count($lightboxPhotos);
    @endphp

    @if($lightboxTotal > 1)
    <script>
        var famerPhotoList = {!! json_encode($lightboxPhotos) !!};
        var famerRestaurantName = {!! json_encode($restaurant->name) !!};
    </script>

    <div style="background:#1A1A1A; border-bottom:1px solid #2A2A2A; padding:0.75rem 1rem;">
        <div class="max-w-7xl mx-auto">
            <div style="display:flex; gap:0.5rem; overflow-x:auto; padding:0.25rem 0; scrollbar-width:thin; scrollbar-color:#2A2A2A #1A1A1A; -webkit-overflow-scrolling:touch;">
                @foreach($galleryDisplay as $idx => $photo)
                <img src="{{ $photo }}"
                     alt="{{ $restaurant->name }}"
                     loading="lazy"
                     onclick="famerLightbox(famerPhotoList, {{ $idx }})"
                     style="height:160px; width:auto; min-width:200px; object-fit:cover; border-radius:8px; border:2px solid #2A2A2A; cursor:pointer; flex-shrink:0; transition:border-color 0.2s;"
                     onmouseover="this.style.borderColor='#D4AF37'" onmouseout="this.style.borderColor='#2A2A2A'">
                @endforeach
                @if($lightboxTotal > 5)
                <div onclick="famerLightbox(famerPhotoList, 5)"
                     style="height:160px; min-width:120px; border-radius:8px; background:#2A2A2A; display:flex; align-items:center; justify-content:center; cursor:pointer; flex-shrink:0; border:2px solid #2A2A2A; color:#F5F5F5; font-family:Poppins,sans-serif; font-size:0.875rem; font-weight:600; text-align:center; padding:0 1rem;">
                    +{{ $lightboxTotal - 5 }}<br>fotos
                </div>
                @endif
            </div>
        </div>
    </div>
    @endif

    @push('scripts')
    <script>
    (function() {
        // ── Lightbox ──────────────────────────────────────────────────────────
        var _lbTouchStartX = 0;

        function _lbCreate() {
            var o = document.createElement('div');
            o.id = 'famer-lightbox';
            o.style.display = 'none';
            o.innerHTML = [
                '<div id="famer-lb-backdrop" style="position:fixed;inset:0;background:rgba(0,0,0,0.95);z-index:9999;',
                'display:flex;align-items:center;justify-content:center;flex-direction:column;">',
                  '<div style="position:absolute;top:1rem;right:1rem;display:flex;gap:1rem;align-items:center;">',
                    '<span id="famer-lb-counter" style="color:#9CA3AF;font-size:0.875rem;font-family:Poppins,sans-serif;"></span>',
                    '<button onclick="famerLbClose()" style="color:#F5F5F5;background:none;border:none;font-size:1.5rem;cursor:pointer;line-height:1;padding:0.5rem;">✕</button>',
                  '</div>',
                  '<div style="position:relative;max-width:90vw;max-height:80vh;display:flex;align-items:center;">',
                    '<button onclick="famerLbNav(-1)" id="famer-lb-prev" ',
                    'style="position:absolute;left:-3.5rem;color:#F5F5F5;background:rgba(255,255,255,0.1);border:none;border-radius:50%;',
                    'width:3rem;height:3rem;font-size:1.5rem;cursor:pointer;display:flex;align-items:center;justify-content:center;z-index:1;">‹</button>',
                    '<img id="famer-lb-img" style="max-width:90vw;max-height:80vh;object-fit:contain;border-radius:4px;',
                    'touch-action:pan-y;" />',
                    '<button onclick="famerLbNav(1)" id="famer-lb-next" ',
                    'style="position:absolute;right:-3.5rem;color:#F5F5F5;background:rgba(255,255,255,0.1);border:none;border-radius:50%;',
                    'width:3rem;height:3rem;font-size:1.5rem;cursor:pointer;display:flex;align-items:center;justify-content:center;z-index:1;">›</button>',
                  '</div>',
                  '<p id="famer-lb-caption" style="color:#9CA3AF;font-size:0.875rem;margin-top:0.75rem;font-family:Poppins,sans-serif;max-width:90vw;text-align:center;"></p>',
                '</div>'
            ].join('');
            document.body.appendChild(o);

            // Close on backdrop click (not on image)
            document.getElementById('famer-lb-backdrop').addEventListener('click', function(e) {
                if (e.target === this) famerLbClose();
            });

            // Swipe on mobile
            var img = document.getElementById('famer-lb-img');
            img.addEventListener('touchstart', function(e) {
                _lbTouchStartX = e.changedTouches[0].clientX;
            }, {passive:true});
            img.addEventListener('touchend', function(e) {
                var dx = e.changedTouches[0].clientX - _lbTouchStartX;
                if (Math.abs(dx) > 40) famerLbNav(dx < 0 ? 1 : -1);
            }, {passive:true});

            // Keyboard
            document.addEventListener('keydown', function(e) {
                var lb = document.getElementById('famer-lightbox');
                if (!lb || lb.style.display === 'none') return;
                if (e.key === 'Escape')      famerLbClose();
                if (e.key === 'ArrowLeft')   famerLbNav(-1);
                if (e.key === 'ArrowRight')  famerLbNav(1);
            });
        }

        window.famerLightbox = function(photos, index) {
            if (!document.getElementById('famer-lightbox')) _lbCreate();
            var lb = document.getElementById('famer-lightbox');
            lb.style.display = 'block';
            document.body.style.overflow = 'hidden';
            window._famerLbPhotos = photos;
            window._famerLbIndex  = index;
            _famerLbUpdate();
        };

        window.famerLbUpdate = _famerLbUpdate;
        function _famerLbUpdate() {
            var photos = window._famerLbPhotos;
            var i      = window._famerLbIndex;
            document.getElementById('famer-lb-img').src = photos[i];
            document.getElementById('famer-lb-counter').textContent = (i + 1) + ' / ' + photos.length;
            document.getElementById('famer-lb-caption').textContent =
                (typeof famerRestaurantName !== 'undefined') ? famerRestaurantName : '';
            var showArrows = photos.length > 1;
            document.getElementById('famer-lb-prev').style.display = showArrows ? 'flex' : 'none';
            document.getElementById('famer-lb-next').style.display = showArrows ? 'flex' : 'none';
        }

        window.famerLbNav = function(dir) {
            var photos = window._famerLbPhotos;
            window._famerLbIndex = (window._famerLbIndex + dir + photos.length) % photos.length;
            _famerLbUpdate();
        };

        window.famerLbClose = function() {
            var el = document.getElementById('famer-lightbox');
            if (el) el.style.display = 'none';
            document.body.style.overflow = '';
        };
    })();
    </script>
    @endpush

    <!-- Breadcrumb Navigation -->
    <div style="background:#0B0B0B; padding:0.75rem 0; border-bottom:1px solid #1A1A1A;">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <nav aria-label="Breadcrumb">
                <ol style="display:flex; flex-wrap:wrap; gap:0.25rem; align-items:center; list-style:none; margin:0; padding:0; font-size:0.8125rem; color:#6B7280;">
                    <li>
                        <a href="/" style="color:#D4AF37; text-decoration:none;" onmouseover="this.style.opacity='0.8'" onmouseout="this.style.opacity='1'">FAMER</a>
                    </li>
                    <li style="color:#374151; margin:0 0.25rem;">/</li>
                    <li>
                        <a href="/restaurantes" style="color:#D4AF37; text-decoration:none;" onmouseover="this.style.opacity='0.8'" onmouseout="this.style.opacity='1'">Restaurantes</a>
                    </li>
                    @if($restaurant->state)
                    <li style="color:#374151; margin:0 0.25rem;">/</li>
                    <li>
                        <a href="/guia/{{ strtolower($restaurant->state->code ?? \Illuminate\Support\Str::slug($restaurant->state->name)) }}"
                           style="color:#D4AF37; text-decoration:none;" onmouseover="this.style.opacity='0.8'" onmouseout="this.style.opacity='1'">
                            {{ $restaurant->state->name }}
                        </a>
                    </li>
                    @endif
                    @if($restaurant->city)
                    <li style="color:#374151; margin:0 0.25rem;">/</li>
                    <li>
                        <a href="/guia/{{ strtolower($restaurant->state->code ?? \Illuminate\Support\Str::slug($restaurant->state->name ?? '')) }}/{{ \Illuminate\Support\Str::slug($restaurant->city) }}"
                           style="color:#D4AF37; text-decoration:none;" onmouseover="this.style.opacity='0.8'" onmouseout="this.style.opacity='1'">
                            {{ $restaurant->city }}
                        </a>
                    </li>
                    @endif
                    <li style="color:#374151; margin:0 0.25rem;">/</li>
                    <li style="color:#9CA3AF; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:200px;" title="{{ $restaurant->name }}">
                        {{ $restaurant->name }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Main Content Area - Overlapping Card -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" style="margin-top:-80px; position:relative; z-index:20;">
        <div class="lg:flex lg:gap-8">
            <!-- Left Column - Main Content -->
            <div class="min-w-0" style="flex: 2 1 0%">
                <!-- Restaurant Header Info - Overlapping Card -->
                <div style="background:#1A1A1A; border:1px solid rgba(212,175,55,0.2); border-radius:1rem; padding:1.5rem 2rem; margin-bottom:1.5rem;">
                    <!-- Category Badge -->
                    <div class="mb-3">
                        <span style="display:inline-block; background:#2A2A2A; color:#9CA3AF; font-size:0.875rem; font-weight:600; padding:0.25rem 0.75rem; border-radius:9999px;">{{ $restaurant->category?->name }}</span>
                    </div>

                    {{-- FAMER Ranking Badges --}}
                    @if($heroRankings->count() > 0)
                    <div class="flex flex-wrap gap-2 mb-4">
                        @foreach($heroRankings as $ranking)
                            @php
                                $pillBg = match(true) {
                                    $ranking->position == 1 => 'background:linear-gradient(135deg, #7f1d1d, #991b1b); color:white;',
                                    $ranking->position <= 3 => 'background:linear-gradient(135deg, #7f1d1d, #991b1b); color:white;',
                                    $ranking->position <= 10 => 'background:linear-gradient(135deg, #1e3a5f, #2563eb); color:white;',
                                    default => 'background:#E5E7EB; color:#374151;',
                                };
                                $scopeName = match($ranking->ranking_type) {
                                    'national' => 'USA',
                                    default => $ranking->ranking_scope,
                                };
                                $posIcon = $ranking->position === 1 ? '📍' : '';
                            @endphp
                            <a href="{{ url('/guia') }}?scope={{ $ranking->ranking_type }}{{ $ranking->ranking_type !== 'national' ? '&state=' . $ranking->ranking_scope : '' }}"
                               style="{{ $pillBg }} padding:6px 14px; border-radius:8px; font-size:13px; font-weight:700; text-decoration:none; display:inline-flex; align-items:center; gap:6px; box-shadow:0 2px 8px rgba(0,0,0,0.2); transition:transform 0.15s;"
                               onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
                                @if($posIcon)<span>{{ $posIcon }}</span>@endif
                                <span>🏆</span>
                                @if($ranking->ranking_type === 'city')
                                    #{{ $ranking->position }} Mejor Restaurante Mexicano - {{ $scopeName }} {{ $ranking->year }}
                                @else
                                    {{ $ranking->position <= 3 ? '#' . $ranking->position : 'Top ' . $ranking->position }} {{ $scopeName }} {{ $ranking->year }}
                                @endif
                            </a>
                        @endforeach
                    </div>
                    @endif

                    <!-- Restaurant Name with Logo -->
                    <div class="flex items-center gap-4 mb-3">
                        @if($restaurant->logo)
                            <img src="{{ asset('storage/' . $restaurant->logo) }}" alt="{{ $restaurant->name }}" class="w-16 h-16 md:w-20 md:h-20 rounded-full object-cover border-2 border-gray-200 shadow-sm flex-shrink-0" loading="lazy" decoding="async">
                        @endif
                        <h1 class="text-3xl md:text-4xl font-bold" style="color:#F5F5F5;">{{ $restaurant->name }}</h1>
                    </div>

                    <!-- Location -->
                    <div class="flex items-center gap-2 mb-3" style="color:#9CA3AF;">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <span class="text-base">{{ $restaurant->city }}@if($restaurant->state), {{ $restaurant->state?->name ?? '' }}@endif</span>
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
                    <div class="flex items-center gap-3 mb-4">
                        <div class="flex text-yellow-400">
                            @for($i = 0; $i < 5; $i++)
                                @if($i < floor($displayRating))
                                    <svg class="w-6 h-6 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                @elseif($i < $displayRating)
                                    <svg class="w-6 h-6" viewBox="0 0 20 20"><defs><linearGradient id="half-{{ $i }}"><stop offset="50%" stop-color="#facc15"/><stop offset="50%" stop-color="#d1d5db"/></linearGradient></defs><path fill="url(#half-{{ $i }})" d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                @else
                                    <svg class="w-6 h-6 fill-gray-300" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                @endif
                            @endfor
                        </div>
                        <span class="text-xl font-bold" style="color:#F5F5F5;">{{ number_format($displayRating, 1) }}</span>
                        <a href="#reviews" wire:click="switchTab('reviews')" style="color:#9CA3AF;">({{ number_format($combinedReviews) }} reviews)</a>
                    </div>

                    <!-- Visitor Social Proof -->
                    @if(isset($visitorStats) && ($visitorStats['monthly'] ?? 0) > 10)
                    <div class="flex items-center gap-1.5 text-sm mb-4" style="color:#9CA3AF;">
                        <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/><path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/></svg>
                        <span>{{ number_format($visitorStats['monthly']) }} {{ app()->getLocale() === 'en' ? 'people viewed this page this month' : 'personas vieron esta página este mes' }}</span>
                    </div>
                    @endif

                    <!-- Call to Action Button -->
                    @if($restaurant->phone)
                        <div class="mb-4">
                            <a href="tel:{{ $restaurant->phone }}" class="inline-flex items-center px-6 py-3 bg-[#AF0606] text-white font-bold rounded-xl hover:bg-[#8B0505] transition-colors text-base shadow-md">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                Llamar Ahora
                            </a>
                        </div>
                    @endif

                    <!-- Source Badges & Info Row -->
                    <div class="flex flex-wrap items-center gap-2 pt-4" style="border-top:1px solid #2A2A2A;">
                        @if($restaurant->is_claimed)
                            <span class="inline-flex items-center bg-blue-100 text-blue-800 text-xs font-semibold px-2.5 py-1 rounded-full">
                                <svg class="w-3.5 h-3.5 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                Verificado
                            </span>
                        @endif
                        @if($restaurant->price_range)
                            <span class="font-medium text-sm" style="color:#9CA3AF;">{{ $restaurant->price_range }}</span>
                        @endif
                        @if($restaurant->google_verified || $restaurant->google_place_id)
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium shadow-sm" style="background:#2A2A2A; color:#9CA3AF; border:1px solid #3A3A3A;">
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
                        @if($googleReviews > 0)
                            <span class="text-xs" style="color:#9CA3AF;">Google {{ number_format($googleRating, 1) }} ({{ number_format($googleReviews) }})</span>
                        @endif
                        @if($yelpReviews > 0)
                            <span class="text-xs" style="color:#9CA3AF;">Yelp {{ number_format($yelpRating, 1) }} ({{ number_format($yelpReviews) }})</span>
                        @endif
                    </div>

                    <!-- Hours Status -->
                    @if($todayHours)
                        <div class="flex items-center gap-2 mt-3 text-sm">
                            @if($isOpenNow)
                                <span class="text-green-600 font-semibold">Abierto</span>
                            @else
                                <span class="text-red-600 font-semibold">Cerrado</span>
                            @endif
                            <span style="color:#9CA3AF;">{{ preg_replace('/^[^:]+:\s*/', '', $todayHours) }}</span>
                            <button onclick="document.getElementById('hours-section').scrollIntoView({behavior: 'smooth'})" style="color:#D4AF37;" class="hover:underline">Ver horarios</button>
                        </div>
                    @endif

                    <!-- Action Buttons -->
                    <div class="flex flex-wrap gap-3 pt-4 mt-4" style="border-top:1px solid #2A2A2A;">
                        <a href="#write-review" wire:click="switchTab('reviews')" class="inline-flex items-center px-4 py-2.5 font-semibold rounded-lg transition-colors" style="background:#D4AF37; color:#0B0B0B;">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                            Escribir reseña
                        </a>
                        <button wire:click="switchTab('photos')" class="inline-flex items-center px-4 py-2.5 font-semibold rounded-lg transition-colors" style="background:#2A2A2A; border:1px solid #3A3A3A; color:#F5F5F5;">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            Añadir foto
                        </button>
                        @livewire('favorite-button', ['restaurant' => $restaurant])
                        <div x-data="{ open: false }" class="relative">
                            <button @click="open = !open" class="inline-flex items-center px-4 py-2.5 font-semibold rounded-lg transition-colors" style="background:#2A2A2A; border:1px solid #3A3A3A; color:#F5F5F5;">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/></svg>
                                Compartir
                            </button>
                            <div x-show="open" @click.away="open = false" x-transition class="absolute left-0 mt-2 w-48 rounded-lg shadow-lg z-50" style="background:#1A1A1A; border:1px solid #2A2A2A;">
                                <x-social-share :url="url()->current()" :title="$restaurant->name" :description="$restaurant->description ?: __('app.tagline')" layout="vertical" />
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Dish Badges (internal links to dish pages) --}}
                @php
                    $dishBadges = [];
                    if (!empty($restaurant->has_birria))         $dishBadges[] = ['slug' => 'birria',      'label' => 'Birria'];
                    if (!empty($restaurant->has_tamales))        $dishBadges[] = ['slug' => 'tamales',     'label' => 'Tamales'];
                    if (!empty($restaurant->has_pozole_menudo))  $dishBadges[] = ['slug' => 'pozole',      'label' => 'Pozole'];
                    if (!empty($restaurant->has_homemade_mole))  $dishBadges[] = ['slug' => 'mole',        'label' => 'Mole'];
                    if (!empty($restaurant->has_charcoal_grill)) $dishBadges[] = ['slug' => 'carne-asada', 'label' => 'Carne Asada'];
                    if (!empty($restaurant->has_carnitas))       $dishBadges[] = ['slug' => 'carnitas',    'label' => 'Carnitas'];
                    if (!empty($restaurant->has_barbacoa))       $dishBadges[] = ['slug' => 'barbacoa',    'label' => 'Barbacoa'];
                @endphp

                @if(count($dishBadges) > 0)
                <div style="background:#1A1A1A; border:1px solid #2A2A2A; border-radius:1rem; padding:1.25rem 1.5rem; margin-bottom:1.5rem;">
                    <h3 style="font-size:0.8125rem; font-weight:600; color:#9CA3AF; text-transform:uppercase; letter-spacing:0.05em; margin-bottom:0.75rem;">Especialidades</h3>
                    <div style="display:flex; flex-wrap:wrap; gap:0.5rem;">
                        @foreach($dishBadges as $badge)
                        <a href="/{{ $badge['slug'] }}"
                           style="display:inline-flex; align-items:center; background:#0B0B0B; border:1px solid #2A2A2A; color:#D4AF37; padding:0.375rem 0.875rem; border-radius:9999px; font-size:0.8125rem; font-weight:500; text-decoration:none; transition:border-color 0.2s;"
                           onmouseover="this.style.borderColor='#D4AF37'" onmouseout="this.style.borderColor='#2A2A2A'">
                            {{ $badge['label'] }}
                        </a>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Flash Deals / Ofertas Activas -->
                @livewire('restaurant-deals', ['restaurantId' => $restaurant->id])

                <!-- Popular Dishes (shown in info tab if restaurant has popular items) -->
                @if($popularMenuItems->isNotEmpty())
                <div style="background:#1A1A1A; border:1px solid #2A2A2A; border-radius:0.5rem; padding:1.5rem; margin-bottom:1.5rem; box-shadow:0 4px 6px rgba(0,0,0,0.3);">
                    <h2 class="text-lg font-bold mb-4 flex items-center gap-2" style="color:#F5F5F5;">
                        <svg class="w-5 h-5" style="color:#D4AF37;" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        {{ app()->getLocale() === 'en' ? 'Popular Dishes' : 'Platos Populares' }}
                    </h2>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                        @foreach($popularMenuItems as $item)
                        <button wire:click="showMenuItem({{ $item->id }})"
                                class="flex flex-col text-left rounded-xl overflow-hidden transition-all group" style="border:1px solid #2A2A2A;">
                            @if($item->image)
                            <div class="h-24 overflow-hidden" style="background:#2A2A2A;">
                                <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->name }}"
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform"
                                     loading="lazy" decoding="async">
                            </div>
                            @else
                            <div class="h-24 flex items-center justify-center text-3xl" style="background:#2A2A2A;">🍽️</div>
                            @endif
                            <div class="p-2">
                                <p class="font-semibold text-sm line-clamp-1" style="color:#F5F5F5;">{{ $item->name }}</p>
                                @if($item->price)
                                <p class="text-sm font-medium" style="color:#D4AF37;">${{ number_format($item->price, 2) }}</p>
                                @endif
                            </div>
                        </button>
                        @endforeach
                    </div>
                    <p class="mt-3 text-xs text-center" style="color:#6B7280;">
                        <button wire:click="switchTab('menu')" class="transition-colors" style="color:#D4AF37;">
                            {{ app()->getLocale() === 'en' ? 'View full menu →' : 'Ver menú completo →' }}
                        </button>
                    </p>
                </div>
                @endif

                @include('partials.claim-banner', ['restaurant' => $restaurant])

                <!-- About / Description Section (SEO) -->
                <div style="background:#1A1A1A; border:1px solid #2A2A2A; border-radius:0.5rem; padding:1.5rem; margin-bottom:1.5rem; box-shadow:0 4px 6px rgba(0,0,0,0.3);">
                    <h2 class="text-xl font-bold mb-4" style="color:#F5F5F5;">Acerca de {{ $restaurant->name }}</h2>
                    @php
                        $displayDescription = app()->getLocale() === 'en'
                            ? ($restaurant->ai_description_en ?: $restaurant->ai_description ?: $restaurant->description)
                            : ($restaurant->ai_description ?: $restaurant->description);
                    @endphp
                    @php
                        // Auto-generate a basic description from available data if no real description exists
                        if (!$displayDescription) {
                            $city    = $restaurant->city ? trim($restaurant->city) : null;
                            $state   = $restaurant->state?->name ?? null;
                            $cat     = $restaurant->primaryCategory?->name ?? null;
                            $loc     = collect([$city, $state])->filter()->implode(', ');
                            $displayDescription = $cat && $loc
                                ? "{$restaurant->name} es un restaurante mexicano" . ($cat !== 'Mexican Restaurant' ? " especializado en {$cat}" : '') . ($loc ? " ubicado en {$loc}." : '.')
                                : null;
                            $autoGenerated = true;
                        }
                    @endphp
                    @if($displayDescription)
                        <div class="prose prose-gray max-w-none">
                            <p class="leading-relaxed" style="color:#9CA3AF;">{{ $displayDescription }}</p>
                        </div>
                        @if(!$restaurant->is_claimed && ($autoGenerated ?? false))
                            <p class="text-sm mt-3" style="color:#6B7280;">
                                <a href="/claim?restaurant={{ $restaurant->slug }}" class="font-semibold hover:underline" style="color:#D4AF37;">¿Eres el dueño?</a>
                                Reclama este negocio para añadir una descripción completa, fotos y menú.
                            </p>
                        @endif
                    @else
                        <p class="italic" style="color:#6B7280;">
                            Descripción no disponible.
                            @if(!$restaurant->is_claimed)
                                <a href="/claim?restaurant={{ $restaurant->slug }}" class="font-semibold hover:underline ml-1" style="color:#D4AF37;">¿Eres el dueño? Reclama este negocio.</a>
                            @endif
                        </p>
                    @endif

                    <!-- Quick Info Tags -->
                    <div class="flex flex-wrap gap-2 mt-4 pt-4" style="border-top:1px solid #2A2A2A;">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm" style="background:#2A2A2A; color:#9CA3AF;">
                            <svg class="w-4 h-4 mr-1.5" style="color:#6B7280;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            {{ $restaurant->city }}, {{ $restaurant->state?->code }}
                        </span>
                        @if($restaurant->price_range)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm" style="background:#2A2A2A; color:#9CA3AF;">
                                <svg class="w-4 h-4 mr-1.5" style="color:#6B7280;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                {{ $restaurant->price_range }}
                            </span>
                        @endif
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm" style="background:#2A2A2A; color:#D4AF37;">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                            {{ $restaurant->category?->name }}
                        </span>
                        @if($restaurant->phone)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm" style="background:#2A2A2A; color:#9CA3AF;">
                                <svg class="w-4 h-4 mr-1.5" style="color:#6B7280;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                {{ $restaurant->phone }}
                            </span>
                        @endif
                    </div>
                </div>

                {{-- FAQ Accordion --}}
                @if(count($faqItems) > 0)
                <div style="background:#1A1A1A; border:1px solid #2A2A2A; border-radius:0.5rem; padding:1.5rem; margin-bottom:1.5rem; box-shadow:0 4px 6px rgba(0,0,0,0.3);">
                    <h2 class="text-xl font-bold mb-4" style="color:#F5F5F5;">
                        {{ app()->getLocale() === 'en' ? 'Frequently Asked Questions' : 'Preguntas Frecuentes' }}
                    </h2>
                    <div class="space-y-3">
                        @foreach($faqItems as $i => $faqItem)
                        <div x-data="{ open: false }" class="rounded-lg overflow-hidden" style="border:1px solid #2A2A2A;">
                            <button @click="open = !open" class="w-full flex items-center justify-between p-4 text-left font-semibold transition-colors" style="color:#F5F5F5; background:transparent;">
                                <span>{{ $faqItem['q'] }}</span>
                                <svg :class="open ? 'rotate-180' : ''" class="w-5 h-5 transition-transform flex-shrink-0 ml-2" style="color:#6B7280;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                            <div x-show="open" x-transition class="px-4 pb-4 text-sm leading-relaxed" style="color:#9CA3AF;">
                                {{ $faqItem['a'] }}
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Rating Distribution Section -->
                <div style="background:#1A1A1A; border:1px solid #2A2A2A; border-radius:0.5rem; padding:1.5rem; margin-bottom:1.5rem; box-shadow:0 4px 6px rgba(0,0,0,0.3);">
                    <h2 class="text-xl font-bold mb-4" style="color:#F5F5F5;">Calificación general</h2>
                    <div class="flex flex-col md:flex-row gap-8">
                        <!-- Overall Rating -->
                        <div class="text-center md:text-left">
                            <div class="flex justify-center md:justify-start mb-2" style="color:#D4AF37;">
                                @for($i = 0; $i < 5; $i++)
                                    <svg class="w-8 h-8 {{ $i < floor($displayRating) ? 'fill-current' : 'fill-gray-300' }}" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                @endfor
                            </div>
                            <p style="color:#9CA3AF;">{{ number_format($combinedReviews) }} reseñas</p>
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
                                    <span class="w-12 text-sm" style="color:#9CA3AF;">{{ $stars }} ★</span>
                                    <div class="flex-1 h-5 rounded-full overflow-hidden" style="background:#2A2A2A;">
                                        <div class="h-full rounded-full transition-all duration-500" style="width: {{ ($distribution[$stars] / $maxDist) * 100 }}%; background:#D4AF37;"></div>
                                    </div>
                                </div>
                            @endfor
                        </div>
                    </div>
                </div>

                <!-- Location & Hours Section -->
                <div id="hours-section" style="background:#1A1A1A; border:1px solid #2A2A2A; border-radius:0.5rem; padding:1.5rem; margin-bottom:1.5rem; box-shadow:0 4px 6px rgba(0,0,0,0.3);">
                    <h2 class="text-xl font-bold mb-4" style="color:#F5F5F5;">Ubicación y Horarios</h2>
                    <div class="grid md:grid-cols-2 gap-6">
                        <!-- Map -->
                        <div>
                            <x-google-map :name="$restaurant->name" :address="$restaurant->address . ', ' . $restaurant->city . ', ' . $restaurant->state?->code . ' ' . $restaurant->zip_code" height="250" zoom="15" />
                            <div class="mt-3">
                                <a href="https://www.google.com/maps/dir/?api=1&destination={{ urlencode($restaurant->address . ', ' . $restaurant->city . ', ' . $restaurant->state?->code) }}" target="_blank" class="hover:underline font-medium" style="color:#D4AF37;">
                                    Cómo llegar
                                </a>
                            </div>
                            <p class="mt-2" style="color:#9CA3AF;">{{ $restaurant->address }}</p>
                            <p style="color:#9CA3AF;">{{ $restaurant->city }}, {{ $restaurant->state?->code }} {{ $restaurant->zip_code }}</p>
                        </div>
                        <!-- Hours -->
                        <div>
                            @if(count($parsedHours) > 0)
                                <div class="space-y-2">
                                    @foreach($parsedHours as $index => $hours)
                                        @php
                                            $isToday = ($index == ($today == 0 ? 6 : $today - 1));
                                        @endphp
                                        <div class="flex justify-between py-1 {{ $isToday ? 'font-bold' : '' }}" style="{{ $isToday ? 'color:#F5F5F5;' : 'color:#9CA3AF;' }}">
                                            <span>{{ $hours }}</span>
                                            @if($isToday && $isOpenNow)
                                                <span class="text-green-600 text-sm">Abierto ahora</span>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="italic" style="color:#6B7280;">Horarios no disponibles</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Amenities Section -->
                @if($restaurant->atmosphere || $restaurant->dietary_options || $restaurant->special_features)
                <div style="background:#1A1A1A; border:1px solid #2A2A2A; border-radius:0.5rem; padding:1.5rem; margin-bottom:1.5rem; box-shadow:0 4px 6px rgba(0,0,0,0.3);">
                    <h2 class="text-xl font-bold mb-4" style="color:#F5F5F5;">Características</h2>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                        @if($restaurant->accepts_reservations)
                            <div class="flex items-center gap-2" style="color:#9CA3AF;"><svg class="w-5 h-5" style="color:#D4AF37;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Acepta reservaciones</div>
                        @endif
                        @if($restaurant->online_ordering)
                            <div class="flex items-center gap-2" style="color:#9CA3AF;"><svg class="w-5 h-5" style="color:#D4AF37;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Pedidos en línea</div>
                        @endif
                        @if(is_array($restaurant->atmosphere))
                            @foreach($restaurant->atmosphere as $atm)
                                <div class="flex items-center gap-2" style="color:#9CA3AF;">
                                    <svg class="w-5 h-5" style="color:#D4AF37;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    {{ ucfirst(str_replace('_', ' ', $atm)) }}
                                </div>
                            @endforeach
                        @endif
                        @if($restaurant->has_fresh_tortillas)
                            <div class="flex items-center gap-2" style="color:#9CA3AF;"><svg class="w-5 h-5" style="color:#D4AF37;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Tortillas frescas</div>
                        @endif
                        @if($restaurant->has_aguas_frescas)
                            <div class="flex items-center gap-2" style="color:#9CA3AF;"><svg class="w-5 h-5" style="color:#D4AF37;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Aguas frescas</div>
                        @endif
                        @if($restaurant->has_homemade_salsa)
                            <div class="flex items-center gap-2" style="color:#9CA3AF;"><svg class="w-5 h-5" style="color:#D4AF37;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Salsa casera</div>
                        @endif
                    </div>
                </div>
                @endif

                <!-- Menu Section (Popular Dishes) -->
                @if($menuItems->count() > 0)
                <div style="background:#1A1A1A; border:1px solid #2A2A2A; border-radius:0.5rem; padding:1.5rem; margin-bottom:1.5rem; box-shadow:0 4px 6px rgba(0,0,0,0.3);">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-xl font-bold" style="color:#F5F5F5;">Menú</h2>
                        <button wire:click="switchTab('menu')" class="hover:underline font-medium" style="color:#D4AF37;">Ver menú completo</button>
                    </div>
                    <h3 class="text-lg font-semibold mb-4" style="color:#F5F5F5;">Platillos Populares</h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        @foreach($menuItems->take(4) as $item)
                            <div wire:click="showMenuItem({{ $item->id }})" class="cursor-pointer group">
                                <div class="aspect-square rounded-lg overflow-hidden mb-2 relative" style="background:#2A2A2A;">
                                    @if($item->image)
                                        <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform" loading="lazy" decoding="async">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-4xl">🌮</div>
                                    @endif
                                    @if($item->price)
                                        <div class="absolute bottom-2 left-2 bg-black/70 text-white px-2 py-1 rounded text-sm font-semibold">${{ number_format($item->price, 2) }}</div>
                                    @endif
                                </div>
                                <p class="font-medium transition-colors truncate" style="color:#F5F5F5;">{{ $item->name }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Reviews Section -->
                <div id="reviews" style="background:#1A1A1A; border:1px solid #2A2A2A; border-radius:0.5rem; padding:1.5rem; margin-bottom:1.5rem; box-shadow:0 4px 6px rgba(0,0,0,0.3);">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-xl font-bold" style="color:#F5F5F5;">Reseñas Recomendadas</h2>
                    </div>
                    <div id="write-review" class="mb-6">
                        @livewire('write-review', ['restaurant' => $restaurant])
                    </div>
                    @livewire('review-list', ['restaurant' => $restaurant])
                </div>

                <!-- Dish Reviews Section -->
                <div style="background:#1A1A1A; border:1px solid #2A2A2A; border-radius:0.5rem; padding:1.5rem; margin-bottom:1.5rem; box-shadow:0 4px 6px rgba(0,0,0,0.3);">
                    <h2 class="text-xl font-bold mb-4" style="color:#F5F5F5;">Reseñas de Platillos</h2>
                    @livewire('dish-review-form', ['restaurantId' => $restaurant->id])
                    @livewire('dish-reviews', ['restaurantId' => $restaurant->id])
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
                    <div style="background:#1A1A1A; border:1px solid #2A2A2A; border-radius:0.75rem; overflow:hidden;" x-data="{ activeTab: '{{ $hasDeliveryOptions ? 'delivery' : 'pickup' }}' }">
                        <div class="p-5" style="border-bottom:1px solid #2A2A2A;">
                            <h3 class="text-xl font-bold" style="color:#F5F5F5;">Ordenar Comida</h3>
                        </div>

                        <!-- Tabs -->
                        <div class="flex" style="border-bottom:1px solid #2A2A2A;">
                            @if($restaurant->order_url)
                            <button @click="activeTab = 'pickup'"
                                    :class="activeTab === 'pickup' ? 'border-b-2 border-[#D4AF37]' : ''"
                                    :style="activeTab === 'pickup' ? 'color:#F5F5F5;' : 'color:#6B7280;'"
                                    class="flex-1 py-3 px-4 text-sm font-medium transition-colors">
                                Para llevar
                            </button>
                            @endif
                            @if($hasDeliveryOptions)
                            <button @click="activeTab = 'delivery'"
                                    :class="activeTab === 'delivery' ? 'border-b-2 border-[#D4AF37]' : ''"
                                    :style="activeTab === 'delivery' ? 'color:#F5F5F5;' : 'color:#6B7280;'"
                                    class="flex-1 py-3 px-4 text-sm font-medium transition-colors">
                                Delivery
                            </button>
                            @endif
                        </div>

                        <div class="p-5">
                            <!-- Pickup Tab -->
                            @if($restaurant->order_url)
                            <div x-show="activeTab === 'pickup'" x-cloak>
                                <div class="flex items-center gap-4 text-sm mb-4" style="color:#9CA3AF;">
                                    <span class="font-semibold" style="color:#F5F5F5;">Sin cargos extra</span>
                                    <span style="color:#3A3A3A;">|</span>
                                    <span>Listo en <strong style="color:#F5F5F5;">10-20</strong> min</span>
                                </div>
                                <a href="{{ $restaurant->order_url }}" target="_blank" rel="nofollow noopener"
                                   class="block w-full py-3 px-4 font-semibold text-center rounded-lg transition-colors" style="background:#D4AF37; color:#0B0B0B;">
                                    Ordenar Ahora
                                </a>
                            </div>
                            @endif

                            <!-- Delivery Tab -->
                            @if($hasDeliveryOptions)
                            <div x-show="activeTab === 'delivery'" x-cloak>
                                <p class="text-sm mb-4" style="color:#9CA3AF;">Ordena delivery a traves de:</p>
                                <div class="space-y-2">
                                    @if($restaurant->doordash_url)
                                        <a href="{{ $restaurant->doordash_url }}" target="_blank" rel="nofollow noopener"
                                           class="flex items-center justify-between w-full py-3 px-4 font-medium rounded-lg transition-colors group" style="background:#2A2A2A; border:1px solid #3A3A3A; color:#F5F5F5;">
                                            <div class="flex items-center gap-3">
                                                <div class="w-8 h-8 bg-red-500 rounded-lg flex items-center justify-center">
                                                    <span class="text-white font-bold text-sm">D</span>
                                                </div>
                                                <span>DoorDash</span>
                                            </div>
                                            <svg class="w-5 h-5 group-hover:text-red-500 transition-colors" style="color:#6B7280;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                        </a>
                                    @endif
                                    @if($restaurant->ubereats_url)
                                        <a href="{{ $restaurant->ubereats_url }}" target="_blank" rel="nofollow noopener"
                                           class="flex items-center justify-between w-full py-3 px-4 font-medium rounded-lg transition-colors group" style="background:#2A2A2A; border:1px solid #3A3A3A; color:#F5F5F5;">
                                            <div class="flex items-center gap-3">
                                                <div class="w-8 h-8 bg-green-500 rounded-lg flex items-center justify-center">
                                                    <span class="text-white font-bold text-sm">U</span>
                                                </div>
                                                <span>Uber Eats</span>
                                            </div>
                                            <svg class="w-5 h-5 group-hover:text-green-500 transition-colors" style="color:#6B7280;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                        </a>
                                    @endif
                                    @if($restaurant->grubhub_url)
                                        <a href="{{ $restaurant->grubhub_url }}" target="_blank" rel="nofollow noopener"
                                           class="flex items-center justify-between w-full py-3 px-4 font-medium rounded-lg transition-colors group" style="background:#2A2A2A; border:1px solid #3A3A3A; color:#F5F5F5;">
                                            <div class="flex items-center gap-3">
                                                <div class="w-8 h-8 bg-orange-500 rounded-lg flex items-center justify-center">
                                                    <span class="text-white font-bold text-sm">G</span>
                                                </div>
                                                <span>Grubhub</span>
                                            </div>
                                            <svg class="w-5 h-5 group-hover:text-orange-500 transition-colors" style="color:#6B7280;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                        </a>
                                    @endif
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endif

                    <!-- Check-In Widget -->
                    @livewire('check-in', ['restaurantId' => $restaurant->id])

                    <!-- Contact Card (Yelp Style) -->
                    <div style="background:#1A1A1A; border:1px solid #2A2A2A; border-radius:0.75rem; overflow:hidden;">
                        <!-- Phone -->
                        @if($restaurant->phone)
                            <a href="tel:{{ $restaurant->phone }}" class="flex items-center justify-between p-4 transition-colors" style="border-bottom:1px solid #2A2A2A;" onmouseover="this.style.background='#2A2A2A'" onmouseout="this.style.background='transparent'">
                                <span class="font-medium" style="color:#F5F5F5;">{{ $restaurant->phone }}</span>
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                            </a>
                            <div style="padding:0.75rem 1rem; border-bottom:1px solid #2A2A2A;">
                                <x-whatsapp-cta :restaurant="$restaurant" />
                            </div>
                        @endif

                        <!-- Directions -->
                        <a href="https://www.google.com/maps/dir/?api=1&destination={{ urlencode($restaurant->address . ', ' . $restaurant->city . ', ' . $restaurant->state?->code) }}" target="_blank" class="flex items-center justify-between p-4 transition-colors" style="border-bottom:1px solid #2A2A2A;" onmouseover="this.style.background='#2A2A2A'" onmouseout="this.style.background='transparent'">
                            <div>
                                <p class="font-semibold" style="color:#D4AF37;">Cómo Llegar</p>
                                <p class="text-sm" style="color:#9CA3AF;">{{ $restaurant->address }} {{ $restaurant->city }}, {{ $restaurant->state?->code }} {{ $restaurant->zip_code }}</p>
                            </div>
                            <svg class="w-5 h-5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </a>

                        <!-- External Links Row -->
                        <div class="flex" style="border-bottom:1px solid #2A2A2A;">
                            @if($restaurant->yelp_url || $restaurant->yelp_id)
                                <a href="{{ $restaurant->yelp_url ?: 'https://www.yelp.com/biz/' . $restaurant->yelp_id }}" target="_blank" rel="nofollow noopener" class="flex-1 flex items-center justify-center gap-2 p-4 transition-colors" onmouseover="this.style.background='#2A2A2A'" onmouseout="this.style.background='transparent'">
                                    <svg class="w-5 h-5 text-[#AF0606]" viewBox="0 0 24 24" fill="currentColor"><path d="M20.16 12.594l-4.995 1.433c-.96.276-1.74-.8-1.176-1.63l2.905-4.308a1.072 1.072 0 011.596-.206 9.194 9.194 0 011.67 4.711z"/></svg>
                                    <span class="text-sm font-medium" style="color:#9CA3AF;">Yelp</span>
                                </a>
                            @endif
                            @if($restaurant->google_place_id)
                                <a href="https://www.google.com/maps/place/?q=place_id:{{ $restaurant->google_place_id }}" target="_blank" rel="nofollow noopener" class="flex-1 flex items-center justify-center gap-2 p-4 transition-colors" onmouseover="this.style.background='#2A2A2A'" onmouseout="this.style.background='transparent'">
                                    <svg class="w-5 h-5" viewBox="0 0 24 24"><path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/><path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/><path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/><path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/></svg>
                                    <span class="text-sm font-medium" style="color:#9CA3AF;">Google</span>
                                </a>
                            @endif
                            @if($restaurant->website)
                                <a href="{{ $restaurant->website }}" target="_blank" rel="nofollow noopener" class="flex-1 flex items-center justify-center gap-2 p-4 transition-colors" onmouseover="this.style.background='#2A2A2A'" onmouseout="this.style.background='transparent'">
                                    <svg class="w-5 h-5" style="color:#9CA3AF;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/></svg>
                                    <span class="text-sm font-medium" style="color:#9CA3AF;">Web</span>
                                </a>
                            @endif
                            @if($restaurant->facebook_url)
                                <a href="{{ $restaurant->facebook_url }}" target="_blank" rel="nofollow noopener" class="flex-1 flex items-center justify-center gap-2 p-4 transition-colors" onmouseover="this.style.background='#2A2A2A'" onmouseout="this.style.background='transparent'" title="Facebook">
                                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="#1877F2"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                                    <span class="text-sm font-medium" style="color:#9CA3AF;">Facebook</span>
                                </a>
                            @endif
                            @if($restaurant->instagram_url)
                                <a href="{{ $restaurant->instagram_url }}" target="_blank" rel="nofollow noopener" class="flex-1 flex items-center justify-center gap-2 p-4 transition-colors" onmouseover="this.style.background='#2A2A2A'" onmouseout="this.style.background='transparent'" title="Instagram">
                                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="url(#ig-grad)"><defs><linearGradient id="ig-grad" x1="0%" y1="100%" x2="100%" y2="0%"><stop offset="0%" style="stop-color:#f09433"/><stop offset="25%" style="stop-color:#e6683c"/><stop offset="50%" style="stop-color:#dc2743"/><stop offset="75%" style="stop-color:#cc2366"/><stop offset="100%" style="stop-color:#bc1888"/></linearGradient></defs><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
                                    <span class="text-sm font-medium" style="color:#9CA3AF;">Instagram</span>
                                </a>
                            @endif
                            @if($restaurant->tiktok_url)
                                <a href="{{ $restaurant->tiktok_url }}" target="_blank" rel="nofollow noopener" class="flex-1 flex items-center justify-center gap-2 p-4 transition-colors" onmouseover="this.style.background='#2A2A2A'" onmouseout="this.style.background='transparent'" title="TikTok">
                                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="#F5F5F5"><path d="M19.59 6.69a4.83 4.83 0 01-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 01-2.88 2.5 2.89 2.89 0 01-2.89-2.89 2.89 2.89 0 012.89-2.89c.28 0 .54.04.79.1V9.01a6.33 6.33 0 00-.79-.05 6.34 6.34 0 00-6.34 6.34 6.34 6.34 0 006.34 6.34 6.34 6.34 0 006.33-6.34V8.69a8.17 8.17 0 004.77 1.52V6.74a4.85 4.85 0 01-1-.05z"/></svg>
                                    <span class="text-sm font-medium" style="color:#9CA3AF;">TikTok</span>
                                </a>
                            @endif
                        </div>

                        <!-- Suggest Edit -->
                        <a href="/claim?restaurant={{ $restaurant->slug }}" class="flex items-center justify-center gap-2 p-4 transition-colors" style="border-top:1px solid #2A2A2A; color:#6B7280;" onmouseover="this.style.background='#2A2A2A'" onmouseout="this.style.background='transparent'">
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

                    <!-- Ad Space -->
                    @livewire('advertisement-banner', ['placement' => 'sidebar', 'stateId' => $restaurant->state_id])
                </div>
            </div>
        </div>
    </div>

    <!-- También te puede gustar / You might also like -->
    @if($nearbyRestaurants->isNotEmpty())
    <div style="background:#0B0B0B; border-top:1px solid #2A2A2A; padding:2.5rem 1rem 3rem;">
        <div class="max-w-6xl mx-auto">
            <h2 style="font-family:'Playfair Display',serif; font-size:1.5rem; font-weight:700; color:#F5F5F5; margin-bottom:1.5rem;">
                {{ app()->getLocale() === 'en' ? 'You Might Also Like' : 'También te puede gustar' }}
            </h2>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                @foreach($nearbyRestaurants as $nr)
                @php
                    $nrPhoto = null;
                    if ($nr->image) {
                        $nrPhoto = str_starts_with($nr->image, 'http') ? $nr->image : asset('storage/' . $nr->image);
                    } elseif ($nr->getFirstMediaUrl('images')) {
                        $nrPhoto = $nr->getFirstMediaUrl('images');
                    } elseif (is_array($nr->yelp_photos) && count($nr->yelp_photos) > 0) {
                        $nrPhoto = $nr->yelp_photos[0];
                    } elseif (!empty($nr->photos)) {
                        $p = $nr->photos[0];
                        $nrPhoto = str_starts_with($p, 'http') ? $p : \Illuminate\Support\Facades\Storage::url($p);
                    }
                    $nrRating = $nr->google_rating ?? $nr->yelp_rating ?? null;
                    $nrStateCode = $nr->state?->code ?? '';
                @endphp
                <a href="/restaurante/{{ $nr->slug }}"
                   style="display:flex; flex-direction:column; background:#1A1A1A; border:1px solid #2A2A2A; border-radius:12px; overflow:hidden; text-decoration:none; transition:border-color 0.2s;"
                   onmouseover="this.style.borderColor='#D4AF37'"
                   onmouseout="this.style.borderColor='#2A2A2A'">
                    {{-- Photo --}}
                    <div style="position:relative; height:160px; overflow:hidden; flex-shrink:0;">
                        @if($nrPhoto)
                            <img src="{{ $nrPhoto }}"
                                 alt="{{ $nr->name }}"
                                 loading="lazy"
                                 style="width:100%; height:100%; object-fit:cover; transition:transform 0.3s;"
                                 onmouseover="this.style.transform='scale(1.04)'"
                                 onmouseout="this.style.transform='scale(1)'">
                        @else
                            <div style="width:100%; height:100%; background:linear-gradient(135deg,#1A1A1A 0%,#2A2A2A 100%); display:flex; align-items:center; justify-content:center; font-size:2.5rem;">
                                🍽️
                            </div>
                        @endif
                        @if($nr->price_range)
                            <span style="position:absolute; top:0.5rem; right:0.5rem; background:rgba(0,0,0,0.75); color:#F5F5F5; font-size:0.7rem; font-weight:600; padding:0.2rem 0.5rem; border-radius:4px;">
                                {{ $nr->price_range }}
                            </span>
                        @endif
                    </div>
                    {{-- Info --}}
                    <div style="padding:0.875rem 1rem; flex:1; display:flex; flex-direction:column;">
                        <h3 style="font-weight:700; color:#F5F5F5; font-size:0.9rem; line-height:1.35; margin:0 0 0.25rem; overflow:hidden; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical;">
                            {{ $nr->name }}
                        </h3>
                        <p style="color:#9CA3AF; font-size:0.75rem; margin:0 0 0.5rem;">
                            {{ $nr->city }}{{ $nrStateCode ? ', '.$nrStateCode : '' }}
                        </p>
                        @if($nrRating)
                            <div style="display:flex; align-items:center; gap:0.3rem; margin-top:auto;">
                                <span style="color:#D4AF37; font-size:0.8rem;">★</span>
                                <span style="font-size:0.8rem; font-weight:600; color:#F5F5F5;">{{ number_format($nrRating, 1) }}</span>
                            </div>
                        @endif
                    </div>
                </a>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <!-- Photos Tab Modal -->
    @if($activeTab === 'photos')
        <div class="fixed inset-0 z-50 flex items-center justify-center" style="background:rgba(0,0,0,0.6); padding:24px 16px;" wire:click.self="switchTab('info')">
            <div class="w-full max-w-5xl rounded-2xl shadow-2xl flex flex-col" style="max-height:calc(100vh - 48px); background:#1A1A1A; border:1px solid #2A2A2A;" wire:click.stop>
                <div class="flex justify-between items-center px-6 py-4 flex-shrink-0" style="border-bottom:1px solid #2A2A2A;">
                    <h2 class="text-xl font-bold" style="color:#F5F5F5;">Fotos de {{ $restaurant->name }}</h2>
                    <button wire:click="switchTab('info')" class="w-9 h-9 flex items-center justify-center rounded-full transition-colors" style="background:#2A2A2A; color:#9CA3AF;">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <div class="p-6 overflow-y-auto flex-1">
                    @livewire('photo-gallery', ['restaurant' => $restaurant])
                    <div class="mt-6">
                        @livewire('public-photo-upload', ['restaurantId' => $restaurant->id])
                    </div>
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
                <div style="background:#1A1A1A; border:1px solid #2A2A2A; border-radius:0.75rem; padding:1.5rem;">
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
