<x-filament-panels::page>
    @php
        $restaurant = auth()->user()->allAccessibleRestaurants()->first();
        $plan = $restaurant->subscription_tier ?? 'free';
        $isPremium = in_array($plan, ['premium', 'elite']);

        // Calculate profile completion
        $completionFields = [
            'name' => !empty($restaurant->name),
            'description' => !empty($restaurant->description),
            'address' => !empty($restaurant->address),
            'phone' => !empty($restaurant->phone),
            'website' => !empty($restaurant->website),
            'hours' => !empty($restaurant->hours),
            'logo' => !empty($restaurant->logo),
            'cover_image' => !empty($restaurant->image),
        ];
        $completionPercent = round((count(array_filter($completionFields)) / count($completionFields)) * 100);

        // Get stats
        $totalReviews = $restaurant->reviews()->where('status', 'approved')->count();
        $avgRating = $restaurant->reviews()->where('status', 'approved')->avg('rating') ?? 0;
        $pendingResponses = $restaurant->reviews()->where('status', 'approved')->whereNull('response')->count();
        $recentReviews = $restaurant->reviews()->where('status', 'approved')->latest()->take(3)->get();
        $menuItemsCount = $restaurant->menuItems()->count();
        $photosCount = $restaurant->userPhotos()->count();

        // Get visitor stats
        $visitorStats = \Illuminate\Support\Facades\Cache::remember(
            "restaurant_stats_" . $restaurant->id,
            300,
            function () use ($restaurant) {
                $thirtyDaysAgo = now()->subDays(30);
                $totalViews = \App\Models\AnalyticsEvent::where("restaurant_id", $restaurant->id)
                    ->where("event_type", \App\Models\AnalyticsEvent::EVENT_PAGE_VIEW)
                    ->count();
                $monthlyViews = \App\Models\AnalyticsEvent::where("restaurant_id", $restaurant->id)
                    ->where("event_type", \App\Models\AnalyticsEvent::EVENT_PAGE_VIEW)
                    ->where("created_at", ">=", $thirtyDaysAgo)
                    ->count();
                return ["total" => $totalViews, "monthly" => $monthlyViews];
            }
        );
        $adValue = round($visitorStats["total"] * 1.5, 2);

        // Get pending team requests
        $pendingTeamRequests = \App\Models\TeamRequest::where('restaurant_id', $restaurant->id)
            ->where('status', 'pending')
            ->latest()
            ->get();
    @endphp

    <style>
        .famer-dashboard { display: flex; flex-direction: column; gap: 1.5rem; }

        /* Welcome banner */
        .famer-welcome {
            background: #111111;
            border-radius: 0.75rem;
            padding: 1.75rem 2rem;
            border: 1px solid #2A2A2A;
            border-left: 3px solid #D4AF37;
            box-shadow: 0 4px 24px rgba(0,0,0,0.4);
        }
        .famer-welcome-inner { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 1.25rem; }
        .famer-restaurant-name {
            font-family: 'Playfair Display', Georgia, serif;
            font-size: 1.75rem;
            font-weight: 700;
            color: #D4AF37;
            margin: 0 0 0.25rem 0;
            letter-spacing: -0.01em;
            line-height: 1.2;
        }
        .famer-welcome-sub { color: #9CA3AF; font-size: 0.875rem; margin: 0; font-family: 'Poppins', sans-serif; }

        /* Plan badge */
        .famer-plan-badge {
            display: inline-flex; align-items: center; gap: 0.375rem;
            padding: 0.375rem 0.875rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            font-family: 'Poppins', sans-serif;
        }
        .famer-plan-badge.elite { background: rgba(212,175,55,0.12); color: #D4AF37; border: 1px solid rgba(212,175,55,0.35); }
        .famer-plan-badge.premium { background: rgba(212,175,55,0.08); color: #D4AF37; border: 1px solid rgba(212,175,55,0.25); }
        .famer-plan-badge.free { background: rgba(156,163,175,0.08); color: #9CA3AF; border: 1px solid rgba(156,163,175,0.2); }

        /* Outlined button */
        .famer-btn-outline {
            display: inline-flex; align-items: center; gap: 0.5rem;
            padding: 0.5rem 1.125rem;
            border-radius: 0.5rem;
            border: 1px solid #333333;
            background: transparent;
            color: #E5E7EB;
            font-size: 0.8125rem;
            font-weight: 500;
            text-decoration: none;
            font-family: 'Poppins', sans-serif;
            transition: border-color 0.2s, color 0.2s;
        }
        .famer-btn-outline:hover { border-color: #D4AF37; color: #D4AF37; }

        /* Stat cards */
        .famer-stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; }
        .famer-stats-grid-3 { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; }
        .famer-stat-card {
            background: #111111;
            border: 1px solid #2A2A2A;
            border-radius: 0.75rem;
            padding: 1.25rem 1.375rem;
            position: relative;
            overflow: hidden;
        }
        .famer-stat-card-icon {
            position: absolute; top: 1rem; right: 1rem;
            width: 2rem; height: 2rem;
            color: #333333;
        }
        .famer-stat-label {
            font-size: 0.6875rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #6B7280;
            margin: 0 0 0.375rem 0;
            font-family: 'Poppins', sans-serif;
        }
        .famer-stat-value {
            font-size: 2rem;
            font-weight: 700;
            color: #F5F5F5;
            margin: 0;
            line-height: 1;
            font-family: 'Poppins', sans-serif;
        }
        .famer-stat-value.gold { color: #D4AF37; }
        .famer-stat-sub {
            font-size: 0.625rem;
            color: #4B5563;
            margin: 0.375rem 0 0 0;
            font-family: 'Poppins', sans-serif;
        }

        /* Section divider */
        .famer-divider { border: none; border-top: 1px solid #1A1A1A; margin: 0; }

        /* Quick actions */
        .famer-actions-card {
            background: #111111;
            border: 1px solid #2A2A2A;
            border-radius: 0.75rem;
            padding: 1.25rem;
        }
        .famer-section-title {
            font-size: 0.8125rem;
            font-weight: 600;
            color: #9CA3AF;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            margin: 0 0 1rem 0;
            font-family: 'Poppins', sans-serif;
        }
        .famer-actions-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 0.75rem; }
        .famer-action-btn {
            display: flex; flex-direction: column; align-items: center; gap: 0.625rem;
            background: #1A1A1A;
            border: 1px solid #2A2A2A;
            border-radius: 0.625rem;
            padding: 1rem 0.75rem;
            text-decoration: none;
            position: relative;
            transition: background 0.15s, border-color 0.15s;
            cursor: pointer;
        }
        .famer-action-btn:hover { background: #222222; border-color: #333333; }
        .famer-action-icon {
            width: 2.25rem; height: 2.25rem;
            color: #D4AF37;
            flex-shrink: 0;
        }
        .famer-action-label {
            color: #E5E7EB;
            font-size: 0.8125rem;
            font-weight: 500;
            font-family: 'Poppins', sans-serif;
        }
        .famer-badge-dot {
            position: absolute; top: 0.5rem; right: 0.5rem;
            background: #8B1E1E;
            color: #FECACA;
            font-size: 0.6875rem;
            font-weight: 700;
            min-width: 1.25rem; height: 1.25rem;
            border-radius: 9999px;
            display: flex; align-items: center; justify-content: center;
            padding: 0 0.25rem;
        }

        /* Certificate card */
        .famer-cert-card {
            background: #111111;
            border: 1px solid #2A2A2A;
            border-top: 1px solid #D4AF37;
            border-radius: 0.75rem;
            padding: 1.375rem 1.5rem;
            box-shadow: 0 0 0 1px rgba(212,175,55,0.05);
        }

        /* Two column layout */
        .famer-two-col { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; }
        .famer-col { display: flex; flex-direction: column; gap: 1.5rem; }

        /* Dark inner card */
        .famer-inner-card {
            background: #111111;
            border: 1px solid #2A2A2A;
            border-radius: 0.75rem;
            overflow: hidden;
        }
        .famer-inner-card-header {
            padding: 0.875rem 1.25rem;
            border-bottom: 1px solid #1A1A1A;
            display: flex; align-items: center; justify-content: space-between;
        }
        .famer-inner-card-title {
            font-size: 0.8125rem;
            font-weight: 600;
            color: #F5F5F5;
            margin: 0;
            font-family: 'Poppins', sans-serif;
        }
        .famer-link-muted {
            font-size: 0.8125rem;
            color: #6B7280;
            text-decoration: none;
            font-family: 'Poppins', sans-serif;
        }
        .famer-link-muted:hover { color: #D4AF37; }

        /* Progress bar */
        .famer-progress-track {
            width: 100%;
            background: #2A2A2A;
            border-radius: 9999px;
            height: 0.375rem;
            margin-bottom: 0.75rem;
        }
        .famer-progress-fill {
            height: 0.375rem;
            border-radius: 9999px;
            background: #D4AF37;
        }
        .famer-completion-tag {
            display: inline-block;
            background: #1A1A1A;
            border: 1px solid #2A2A2A;
            color: #9CA3AF;
            padding: 0.125rem 0.5rem;
            border-radius: 0.25rem;
            font-size: 0.6875rem;
            margin-right: 0.25rem;
            margin-bottom: 0.25rem;
            font-family: 'Poppins', sans-serif;
        }

        /* Review item */
        .famer-review-item { padding: 1rem 1.25rem; border-bottom: 1px solid #1A1A1A; }
        .famer-review-item:last-child { border-bottom: none; }
        .famer-avatar {
            width: 2.25rem; height: 2.25rem;
            background: #1A1A1A;
            border: 1px solid #2A2A2A;
            border-radius: 9999px;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        .famer-avatar span { color: #9CA3AF; font-size: 0.8125rem; font-weight: 600; }
        .famer-stars { color: #D4AF37; font-size: 0.8125rem; letter-spacing: 0.05em; }
        .famer-stars-empty { color: #333333; }

        /* Benefits card */
        .famer-benefits-card {
            background: #111111;
            border: 1px solid #2A2A2A;
            border-radius: 0.75rem;
            padding: 1.375rem 1.5rem;
        }

        /* Tips card */
        .famer-tips-card {
            background: #111111;
            border: 1px solid #2A2A2A;
            border-radius: 0.75rem;
            padding: 1.375rem 1.5rem;
        }
        .famer-tip-item {
            display: flex; align-items: flex-start; gap: 0.625rem;
            font-size: 0.8125rem;
            color: #9CA3AF;
            margin-bottom: 0.625rem;
            font-family: 'Poppins', sans-serif;
            line-height: 1.5;
        }
        .famer-tip-item:last-child { margin-bottom: 0; }
        .famer-tip-dot { color: #D4AF37; flex-shrink: 0; margin-top: 0.1rem; }

        /* Upgrade CTA */
        .famer-upgrade-card {
            background: #111111;
            border: 1px solid #2A2A2A;
            border-radius: 0.75rem;
            padding: 1.375rem 1.5rem;
        }
        .famer-btn-gold {
            display: inline-flex; align-items: center; gap: 0.5rem;
            background: #D4AF37;
            color: #0B0B0B;
            padding: 0.5rem 1.25rem;
            border-radius: 0.5rem;
            font-size: 0.8125rem;
            font-weight: 700;
            text-decoration: none;
            font-family: 'Poppins', sans-serif;
            letter-spacing: 0.02em;
        }

        /* Affiliates section */
        .famer-affiliates {
            background: #111111;
            border: 1px solid #2A2A2A;
            border-radius: 0.75rem;
            padding: 1.5rem;
        }
        .famer-affiliate-badge {
            display: inline-block;
            background: rgba(212,175,55,0.06);
            border: 1px solid rgba(212,175,55,0.2);
            color: #D4AF37;
            padding: 0.25rem 0.875rem;
            border-radius: 9999px;
            font-size: 0.6875rem;
            font-weight: 600;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            margin-bottom: 0.75rem;
            font-family: 'Poppins', sans-serif;
        }
        .famer-affiliate-link {
            display: inline-flex; align-items: center;
            padding: 0.4375rem 0.875rem;
            background: #1A1A1A;
            border: 1px solid #2A2A2A;
            border-radius: 0.5rem;
            text-decoration: none;
            color: #9CA3AF;
            font-size: 0.75rem;
            font-family: 'Poppins', sans-serif;
            transition: border-color 0.15s, color 0.15s;
        }
        .famer-affiliate-link:hover { border-color: #D4AF37; color: #D4AF37; }

        /* Danger zone */
        .famer-danger-summary {
            background: #111111;
            border: 1px solid #2A2A2A;
            border-radius: 0.5rem;
            padding: 0.875rem 1.25rem;
            cursor: pointer;
            list-style: none;
            display: flex; align-items: center; gap: 0.5rem;
            color: #6B7280;
            font-weight: 600;
            font-size: 0.8125rem;
            font-family: 'Poppins', sans-serif;
        }
        .famer-danger-body {
            background: #111111;
            border: 1px solid #2A2A2A;
            border-top: none;
            border-radius: 0 0 0.5rem 0.5rem;
            padding: 1.5rem;
        }

        /* Awards banner */
        .famer-awards-banner {
            background: #111111;
            border: 1px solid #2A2A2A;
            border-top: 2px solid #D4AF37;
            border-radius: 0.75rem;
            padding: 1.375rem 1.5rem;
        }
        .famer-awards-title {
            font-family: 'Playfair Display', Georgia, serif;
            font-size: 1.375rem;
            font-weight: 700;
            color: #F5F5F5;
            margin: 0 0 0.25rem 0;
        }
        .famer-votes-counter {
            text-align: center;
            background: #1A1A1A;
            border: 1px solid #2A2A2A;
            border-radius: 0.5rem;
            padding: 0.875rem 1.5rem;
            min-width: 7rem;
        }

        /* Rankings grid */
        .famer-rankings-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; }
        .famer-ranking-card {
            background: #111111;
            border: 1px solid #2A2A2A;
            border-radius: 0.75rem;
            padding: 1.25rem;
            text-align: center;
        }
        .famer-ranking-card.gold-accent { border-top: 2px solid #D4AF37; }
        .famer-ranking-value {
            font-size: 2rem;
            font-weight: 700;
            color: #D4AF37;
            margin: 0 0 0.25rem 0;
            font-family: 'Poppins', sans-serif;
            line-height: 1;
        }
        .famer-ranking-label {
            font-size: 0.6875rem;
            color: #6B7280;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            margin: 0;
            font-family: 'Poppins', sans-serif;
        }

        /* Bar chart */
        .famer-chart-card {
            background: #111111;
            border: 1px solid #2A2A2A;
            border-radius: 0.75rem;
            padding: 1.25rem;
        }

        /* Plan features */
        .famer-features-card {
            background: #111111;
            border: 1px solid #2A2A2A;
            border-radius: 0.75rem;
            overflow: hidden;
        }
        .famer-features-header {
            background: #1A1A1A;
            border-bottom: 1px solid #2A2A2A;
            padding: 1rem 1.25rem;
            display: flex; align-items: center; justify-content: space-between;
        }
        .famer-feature-item {
            display: flex; align-items: center; gap: 0.5rem;
            padding: 0.5rem 0.75rem;
            background: #1A1A1A;
            border: 1px solid #222222;
            border-radius: 0.375rem;
        }

        /* QR & Share */
        .famer-qr-card {
            background: #111111;
            border: 1px solid #2A2A2A;
            border-radius: 0.75rem;
            padding: 1.25rem;
        }
        .famer-share-card {
            background: #111111;
            border: 1px solid #2A2A2A;
            border-radius: 0.75rem;
            padding: 1.25rem;
        }
        .famer-share-btn {
            display: inline-flex; align-items: center; gap: 0.5rem;
            background: #1A1A1A;
            border: 1px solid #333333;
            color: #E5E7EB;
            padding: 0.5rem 1rem;
            border-radius: 0.375rem;
            text-decoration: none;
            font-size: 0.8125rem;
            font-family: 'Poppins', sans-serif;
            transition: border-color 0.15s;
        }
        .famer-share-btn:hover { border-color: #D4AF37; color: #D4AF37; }

        /* Team requests */
        .famer-team-card {
            background: #111111;
            border: 1px solid #333333;
            border-top: 2px solid #D4AF37;
            border-radius: 0.75rem;
            overflow: hidden;
        }
        .famer-team-header {
            padding: 0.875rem 1.25rem;
            border-bottom: 1px solid #1A1A1A;
            display: flex; align-items: center; justify-content: space-between;
        }
        .famer-btn-approve {
            background: transparent;
            border: 1px solid #2A5A2A;
            color: #86EFAC;
            font-size: 0.6875rem;
            font-weight: 600;
            padding: 0.3125rem 0.75rem;
            border-radius: 0.375rem;
            cursor: pointer;
            font-family: 'Poppins', sans-serif;
        }
        .famer-btn-reject {
            background: transparent;
            border: 1px solid #4A1E1E;
            color: #FCA5A5;
            font-size: 0.6875rem;
            font-weight: 600;
            padding: 0.3125rem 0.75rem;
            border-radius: 0.375rem;
            cursor: pointer;
            font-family: 'Poppins', sans-serif;
        }
    </style>

    <div class="famer-dashboard">

        {{-- Welcome Banner --}}
        <div class="famer-welcome">
            <div class="famer-welcome-inner">
                <div>
                    <h2 class="famer-restaurant-name">{{ $restaurant->name }}</h2>
                    <p class="famer-welcome-sub">Bienvenido, {{ auth()->user()->name }} &mdash; Panel de propietario</p>
                </div>
                <div style="display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap;">
                    <span class="famer-plan-badge {{ $plan }}">
                        @if($plan === 'elite') Plan Elite @elseif($plan === 'premium') Plan Premium @else Plan Gratuito @endif
                    </span>
                    <a href="{{ url('/restaurante/' . $restaurant->slug) }}" target="_blank" class="famer-btn-outline">
                        <svg style="width: 0.875rem; height: 0.875rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                        Ver Perfil
                    </a>
                </div>
            </div>
        </div>

        {{-- Stats Grid Row 1 --}}
        <div class="famer-stats-grid">
            <div class="famer-stat-card">
                <svg class="famer-stat-card-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                <p class="famer-stat-label">Resenas</p>
                <p class="famer-stat-value">{{ $totalReviews }}</p>
            </div>
            <div class="famer-stat-card">
                <svg class="famer-stat-card-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                <p class="famer-stat-label">Calificacion</p>
                <p class="famer-stat-value gold">{{ number_format($avgRating, 1) }}</p>
            </div>
            <div class="famer-stat-card">
                <svg class="famer-stat-card-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <p class="famer-stat-label">Menu Items</p>
                <p class="famer-stat-value">{{ $menuItemsCount }}</p>
            </div>
            <div class="famer-stat-card">
                <svg class="famer-stat-card-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                <p class="famer-stat-label">Fotos</p>
                <p class="famer-stat-value">{{ $photosCount }}</p>
            </div>
        </div>

        {{-- Stats Grid Row 2 — Visitor & Ad Value --}}
        <div class="famer-stats-grid-3">
            <div class="famer-stat-card">
                <svg class="famer-stat-card-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                <p class="famer-stat-label">Visitas Este Mes</p>
                <p class="famer-stat-value">{{ number_format($visitorStats["monthly"]) }}</p>
            </div>
            <div class="famer-stat-card">
                <svg class="famer-stat-card-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                <p class="famer-stat-label">Visitas Totales</p>
                <p class="famer-stat-value">{{ number_format($visitorStats["total"]) }}</p>
            </div>
            <div class="famer-stat-card">
                <svg class="famer-stat-card-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <p class="famer-stat-label">Valor Publicitario</p>
                <p class="famer-stat-value gold">${{ number_format($adValue) }}</p>
                <p class="famer-stat-sub">Equivalente Google Ads</p>
            </div>
        </div>

        {{-- Quick Actions --}}
        <div class="famer-actions-card">
            <p class="famer-section-title">Acciones Rapidas</p>
            <div class="famer-actions-grid">
                <a href="{{ route('filament.owner.resources.my-restaurants.edit', $restaurant) }}" class="famer-action-btn">
                    <svg class="famer-action-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    <span class="famer-action-label">Editar Info</span>
                </a>
                <a href="{{ route('filament.owner.resources.my-menus.index') }}" class="famer-action-btn">
                    <svg class="famer-action-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span class="famer-action-label">Menu</span>
                </a>
                <a href="{{ route('filament.owner.resources.my-photos.index') }}" class="famer-action-btn">
                    <svg class="famer-action-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <span class="famer-action-label">Fotos</span>
                </a>
                <a href="{{ route('filament.owner.resources.my-reviews.index') }}" class="famer-action-btn">
                    <svg class="famer-action-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                    <span class="famer-action-label">Resenas</span>
                    @if($pendingResponses > 0)
                    <span class="famer-badge-dot">{{ $pendingResponses }}</span>
                    @endif
                </a>
            </div>
        </div>

        {{-- Certificate Download Section --}}
        <div class="famer-cert-card">
            <div style="display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 1rem;">
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <div style="width: 3.5rem; height: 3.5rem; background: rgba(212,175,55,0.08); border: 1px solid rgba(212,175,55,0.25); border-radius: 0.625rem; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <svg style="width: 1.75rem; height: 1.75rem; color: #D4AF37;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                    </div>
                    <div>
                        <h3 style="font-size: 1rem; font-weight: 600; color: #F5F5F5; margin: 0; font-family: 'Playfair Display', Georgia, serif;">Certificado FAMER {{ date("Y") }}</h3>
                        <p style="font-size: 0.8125rem; color: #6B7280; margin: 0.25rem 0 0 0; font-family: 'Poppins', sans-serif;">Descarga tu certificado oficial de Restaurante Mexicano Verificado</p>
                    </div>
                </div>
                <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
                    <a href="{{ url('/owner/certificate/' . $restaurant->id) }}" target="_blank" class="famer-btn-outline">
                        <svg style="width: 0.875rem; height: 0.875rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        Vista Previa
                    </a>
                    <a href="{{ url('/owner/certificate-pdf/' . $restaurant->id) }}" class="famer-btn-gold">
                        <svg style="width: 0.875rem; height: 0.875rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        Descargar PDF
                    </a>
                </div>
            </div>
        </div>

        {{-- Two Column Layout --}}
        <div class="famer-two-col">

            {{-- Left Column --}}
            <div class="famer-col">

                {{-- Profile Completion --}}
                <div class="famer-inner-card">
                    <div class="famer-inner-card-header">
                        <h3 class="famer-inner-card-title">Completud del Perfil</h3>
                        <span style="font-size: 0.875rem; font-weight: 700; color: #D4AF37; font-family: 'Poppins', sans-serif;">{{ $completionPercent }}%</span>
                    </div>
                    <div style="padding: 1.25rem;">
                        <div class="famer-progress-track">
                            <div class="famer-progress-fill" style="width: {{ $completionPercent }}%;"></div>
                        </div>
                        @if($completionPercent < 100)
                        <p style="font-size: 0.75rem; color: #6B7280; margin: 0 0 0.5rem 0; font-family: 'Poppins', sans-serif;">Campos pendientes:</p>
                        <div>
                            @foreach($completionFields as $field => $completed)
                                @if(!$completed)
                                    <span class="famer-completion-tag">{{ ucfirst($field) }}</span>
                                @endif
                            @endforeach
                        </div>
                        @else
                        <p style="font-size: 0.8125rem; color: #D4AF37; margin: 0; font-family: 'Poppins', sans-serif;">Perfil completo</p>
                        @endif
                    </div>
                </div>

                {{-- Recent Reviews --}}
                <div class="famer-inner-card">
                    <div class="famer-inner-card-header">
                        <h3 class="famer-inner-card-title">Resenas Recientes</h3>
                        <a href="{{ route('filament.owner.resources.my-reviews.index') }}" class="famer-link-muted">Ver todas &rarr;</a>
                    </div>
                    @forelse($recentReviews as $review)
                    <div class="famer-review-item">
                        <div style="display: flex; align-items: flex-start; gap: 0.75rem;">
                            <div class="famer-avatar">
                                <span>{{ substr($review->reviewer_name ?? 'A', 0, 1) }}</span>
                            </div>
                            <div style="flex: 1; min-width: 0;">
                                <div style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
                                    <span style="font-weight: 500; color: #F5F5F5; font-size: 0.8125rem; font-family: 'Poppins', sans-serif;">{{ $review->reviewer_name ?? 'Anonimo' }}</span>
                                    <span class="famer-stars">{{ str_repeat('★', $review->rating) }}<span class="famer-stars-empty">{{ str_repeat('★', 5 - $review->rating) }}</span></span>
                                </div>
                                <p style="font-size: 0.8125rem; color: #6B7280; margin: 0.25rem 0 0 0; overflow: hidden; text-overflow: ellipsis; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; font-family: 'Poppins', sans-serif;">{{ $review->comment }}</p>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div style="padding: 2rem; text-align: center; color: #4B5563;">
                        <p style="margin: 0; font-size: 0.8125rem; font-family: 'Poppins', sans-serif;">Sin resenas aun</p>
                    </div>
                    @endforelse
                </div>

                {{-- Team Requests --}}
                @if($pendingTeamRequests->count() > 0)
                <div class="famer-team-card">
                    <div class="famer-team-header">
                        <h3 class="famer-inner-card-title">Solicitudes de Equipo</h3>
                        <span style="background: rgba(212,175,55,0.08); border: 1px solid rgba(212,175,55,0.2); color: #D4AF37; font-size: 0.6875rem; font-weight: 700; padding: 0.1875rem 0.625rem; border-radius: 9999px; font-family: 'Poppins', sans-serif;">{{ $pendingTeamRequests->count() }} pendiente(s)</span>
                    </div>
                    @foreach($pendingTeamRequests as $request)
                    <div style="padding: 1rem 1.25rem; border-bottom: 1px solid #1A1A1A;">
                        <div style="display: flex; align-items: flex-start; gap: 0.75rem;">
                            <div class="famer-avatar" style="{{ $request->request_type === 'ownership_dispute' ? 'border-color: #4A1E1E;' : '' }}">
                                @if($request->request_type === 'ownership_dispute')
                                <svg style="width: 1rem; height: 1rem; color: #FCA5A5;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                @else
                                <svg style="width: 1rem; height: 1rem; color: #9CA3AF;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                                @endif
                            </div>
                            <div style="flex: 1; min-width: 0;">
                                <div style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
                                    <span style="font-weight: 500; color: #F5F5F5; font-size: 0.8125rem; font-family: 'Poppins', sans-serif;">{{ $request->requester_name }}</span>
                                    @if($request->request_type === 'ownership_dispute')
                                    <span style="background: rgba(139,30,30,0.2); border: 1px solid #4A1E1E; color: #FCA5A5; font-size: 0.625rem; padding: 0.125rem 0.375rem; border-radius: 9999px; font-weight: 600; font-family: 'Poppins', sans-serif;">DISPUTA</span>
                                    @else
                                    <span style="background: rgba(30,58,138,0.2); border: 1px solid #1E3A8A; color: #93C5FD; font-size: 0.625rem; padding: 0.125rem 0.375rem; border-radius: 9999px; font-weight: 600; font-family: 'Poppins', sans-serif;">{{ strtoupper(\App\Models\TeamRequest::getRoleLabel($request->requested_role)) }}</span>
                                    @endif
                                </div>
                                <p style="font-size: 0.75rem; color: #6B7280; margin: 0.25rem 0 0 0; font-family: 'Poppins', sans-serif;">{{ $request->requester_email }}</p>
                                <p style="font-size: 0.75rem; color: #4B5563; margin: 0.25rem 0 0 0; overflow: hidden; text-overflow: ellipsis; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; font-family: 'Poppins', sans-serif;">{{ $request->message }}</p>
                                <div style="display: flex; gap: 0.5rem; margin-top: 0.75rem;">
                                    <form action="{{ url('/owner/team-request/' . $request->id . '/approve') }}" method="POST" style="display: inline;">
                                        @csrf
                                        <button type="submit" class="famer-btn-approve">Aprobar</button>
                                    </form>
                                    <form action="{{ url('/owner/team-request/' . $request->id . '/reject') }}" method="POST" style="display: inline;">
                                        @csrf
                                        <button type="submit" class="famer-btn-reject">Rechazar</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>

            {{-- Right Column --}}
            <div class="famer-col">

                {{-- FAMER Benefits --}}
                <div class="famer-benefits-card">
                    <p class="famer-section-title">Beneficios FAMER</p>
                    <p style="font-size: 0.8125rem; color: #9CA3AF; margin: 0 0 1rem 0; font-family: 'Poppins', sans-serif; line-height: 1.6;">
                        Como suscriptor <span style="color: #F5F5F5; font-weight: 500;">{{ ucfirst($plan) }}</span> obtienes
                        <span style="color: #D4AF37; font-weight: 600;">{{ $plan === 'elite' ? '15%' : ($plan === 'premium' ? '10%' : '5%') }}</span>
                        de descuento en todos los negocios afiliados.
                    </p>
                    <a href="{{ url('/owner/my-benefits') }}" class="famer-btn-outline">
                        Ver mis descuentos &rarr;
                    </a>
                </div>

                {{-- Tips --}}
                <div class="famer-tips-card">
                    <p class="famer-section-title">Consejos para mas visitas</p>
                    <ul style="margin: 0; padding: 0; list-style: none;">
                        <li class="famer-tip-item"><span class="famer-tip-dot">&mdash;</span><span>Agrega fotos de tus platillos mas populares</span></li>
                        <li class="famer-tip-item"><span class="famer-tip-dot">&mdash;</span><span>Responde a todas las resenas (buenas y malas)</span></li>
                        <li class="famer-tip-item"><span class="famer-tip-dot">&mdash;</span><span>Manten tu menu y horarios actualizados</span></li>
                        <li class="famer-tip-item"><span class="famer-tip-dot">&mdash;</span><span>Pide a clientes satisfechos que dejen resena</span></li>
                    </ul>
                </div>

                @if($plan === 'free')
                {{-- Upgrade CTA --}}
                <div class="famer-upgrade-card">
                    <p class="famer-section-title">Desbloquea mas funciones</p>
                    <p style="font-size: 0.8125rem; color: #9CA3AF; margin: 0 0 1.125rem 0; font-family: 'Poppins', sans-serif; line-height: 1.6;">
                        Analytics, QR para menu, cupones, mas fotos y 10% de descuento FAMER con el Plan Premium.
                    </p>
                    <a href="{{ url('/owner/upgrade-subscription') }}" class="famer-btn-gold">
                        Ver Planes
                    </a>
                </div>
                @endif
            </div>
        </div>

        {{-- Affiliates Section --}}
        <div class="famer-affiliates">
            <div style="text-align: center; margin-bottom: 1.25rem;">
                <div class="famer-affiliate-badge">Beneficio Exclusivo para Miembros</div>
                <h3 style="font-size: 1.125rem; font-weight: 600; color: #F5F5F5; margin: 0.5rem 0 0.5rem 0; font-family: 'Playfair Display', Georgia, serif;">Aprovecha tus descuentos</h3>
                <p style="color: #6B7280; font-size: 0.8125rem; max-width: 40rem; margin: 0 auto; font-family: 'Poppins', sans-serif; line-height: 1.6;">
                    Descuentos exclusivos en muebles, sillas, mesas, booths, platos, decoracion, copas, vasos,
                    equipo de tortilleria, equipo de paleteria mexicana, food trucks para catering, accesorios
                    y mas para subir de nivel tu restaurante.
                </p>
            </div>
            <hr class="famer-divider" style="margin-bottom: 1rem;">
            <p style="text-align: center; font-size: 0.6875rem; color: #4B5563; margin-bottom: 0.75rem; text-transform: uppercase; letter-spacing: 0.06em; font-family: 'Poppins', sans-serif;">Empresas aliadas</p>
            <div style="display: flex; flex-wrap: wrap; justify-content: center; gap: 0.5rem;">
                <a href="https://mf-imports.com" target="_blank" class="famer-affiliate-link">MF Imports</a>
                <a href="https://tormexpro.com" target="_blank" class="famer-affiliate-link">Tormex Pro</a>
                <a href="https://mftrailers.com" target="_blank" class="famer-affiliate-link">MF Trailers</a>
                <a href="https://refrimexpaleteria.com" target="_blank" class="famer-affiliate-link">Refrimex</a>
                <a href="https://muebleyarte.com" target="_blank" class="famer-affiliate-link">Mueble y Arte</a>
                <a href="https://decorarmex.com" target="_blank" class="famer-affiliate-link">Decorarmex</a>
                <a href="https://mexartandcraft.com" target="_blank" class="famer-affiliate-link">Mexican Arts</a>
                <a href="https://mueblesmexicanos.com" target="_blank" class="famer-affiliate-link">Muebles Mexicanos</a>
                <a href="https://tododetonala.com" target="_blank" class="famer-affiliate-link">Todo de Tonala</a>
            </div>
        </div>

        {{-- Danger Zone --}}
        <details style="margin-top: 0.5rem;">
            <summary class="famer-danger-summary">
                <svg style="width: 1rem; height: 1rem; color: #4B5563;" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                Zona de Peligro
            </summary>
            <div class="famer-danger-body">
                <h4 style="color: #FCA5A5; font-weight: 600; margin: 0 0 0.5rem 0; font-size: 0.875rem; font-family: 'Poppins', sans-serif;">Desvincular Restaurante de mi Cuenta</h4>
                <p style="color: #6B7280; font-size: 0.8125rem; margin: 0 0 1rem 0; font-family: 'Poppins', sans-serif; line-height: 1.6;">
                    Esta accion liberara el restaurante para que otra persona pueda reclamarlo. Perderas acceso a este restaurante y no podras recuperarlo automaticamente.
                </p>
                <form action="{{ url('/owner/restaurant/' . $restaurant->id . '/unlink') }}" method="POST"
                      onsubmit="return confirm('¿Estas seguro de que deseas desvincular {{ $restaurant->name }}? Esta accion no se puede deshacer.');">
                    @csrf
                    <button type="submit" style="background: transparent; border: 1px solid #4A1E1E; color: #FCA5A5; padding: 0.5rem 1rem; border-radius: 0.375rem; cursor: pointer; font-size: 0.8125rem; font-weight: 500; font-family: 'Poppins', sans-serif; display: inline-flex; align-items: center; gap: 0.5rem;">
                        <svg style="width: 1rem; height: 1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        Desvincular Restaurante
                    </button>
                </form>
            </div>
        </details>

    </div>

        {{-- FAMER Awards Section --}}
        @php
            $monthlyVotes = \App\Models\RestaurantVote::where('restaurant_id', $restaurant->id)
                ->where('month', now()->month)
                ->where('year', now()->year)
                ->count();
            $totalVotes = \App\Models\RestaurantVote::where('restaurant_id', $restaurant->id)->count();

            $cityRanking = \App\Models\RestaurantRanking::where('restaurant_id', $restaurant->id)
                ->where('year', now()->year)
                ->where('ranking_type', 'city')
                ->where('ranking_scope', $restaurant->city)
                ->first();
            $stateRanking = \App\Models\RestaurantRanking::where('restaurant_id', $restaurant->id)
                ->where('year', now()->year)
                ->where('ranking_type', 'state')
                ->first();
            $nationalRanking = \App\Models\RestaurantRanking::where('restaurant_id', $restaurant->id)
                ->where('year', now()->year)
                ->where('ranking_type', 'national')
                ->first();

            $monthlyVotesHistory = [];
            for ($i = 5; $i >= 0; $i--) {
                $date = now()->subMonths($i);
                $votes = \App\Models\RestaurantVote::where('restaurant_id', $restaurant->id)
                    ->where('month', $date->month)
                    ->where('year', $date->year)
                    ->count();
                $monthlyVotesHistory[] = ['month' => $date->format('M'), 'votes' => $votes];
            }
            $maxVotes = max(array_column($monthlyVotesHistory, 'votes') ?: [1]);

            $voteUrl = url("/restaurante/{$restaurant->slug}#votar");
            $qrCodeUrl = "https://api.qrserver.com/v1/create-qr-code/?size=400x400&data=" . urlencode($voteUrl);
            $shareUrl = url("/restaurante/{$restaurant->slug}");
        @endphp

        {{-- FAMER Awards Banner --}}
        <div class="famer-awards-banner" style="margin-top: 1.5rem;">
            <div style="display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 1rem;">
                <div>
                    <h3 class="famer-awards-title">FAMER Awards {{ date('Y') }}</h3>
                    <p style="font-size: 0.8125rem; color: #6B7280; margin: 0.25rem 0 0 0; font-family: 'Poppins', sans-serif;">Ranking y votacion de tu restaurante</p>
                </div>
                <div class="famer-votes-counter">
                    <div style="font-size: 1.75rem; font-weight: 700; color: #D4AF37; font-family: 'Poppins', sans-serif; line-height: 1;">{{ $monthlyVotes }}</div>
                    <div style="font-size: 0.6875rem; color: #6B7280; margin-top: 0.25rem; text-transform: uppercase; letter-spacing: 0.06em; font-family: 'Poppins', sans-serif;">Votos este mes</div>
                </div>
            </div>
        </div>

        {{-- FAMER Rankings Grid --}}
        <div class="famer-rankings-grid" style="margin-top: 1rem;">
            <div class="famer-ranking-card gold-accent">
                <p class="famer-ranking-value">{{ $cityRanking ? '#' . $cityRanking->rank : '—' }}</p>
                <p class="famer-ranking-label">{{ $restaurant->city }}</p>
            </div>
            <div class="famer-ranking-card gold-accent">
                <p class="famer-ranking-value">{{ $stateRanking ? '#' . $stateRanking->rank : '—' }}</p>
                <p class="famer-ranking-label">Estatal</p>
            </div>
            <div class="famer-ranking-card gold-accent">
                <p class="famer-ranking-value">{{ $nationalRanking ? '#' . $nationalRanking->rank : '—' }}</p>
                <p class="famer-ranking-label">Nacional</p>
            </div>
            <div class="famer-ranking-card gold-accent">
                <p class="famer-ranking-value">{{ number_format($totalVotes) }}</p>
                <p class="famer-ranking-label">Votos totales {{ date('Y') }}</p>
            </div>
        </div>

        {{-- Monthly Votes Chart --}}
        <div class="famer-chart-card" style="margin-top: 1rem;">
            <p class="famer-section-title">Historial de Votos</p>
            <div style="display: flex; align-items: flex-end; justify-content: space-between; height: 120px; gap: 0.5rem;">
                @foreach($monthlyVotesHistory as $data)
                <div style="flex: 1; display: flex; flex-direction: column; align-items: center; height: 100%;">
                    <div style="flex: 1; display: flex; align-items: flex-end; width: 100%;">
                        <div style="width: 100%; background: #D4AF37; border-radius: 0.25rem 0.25rem 0 0; height: {{ $maxVotes > 0 ? max(8, ($data['votes'] / $maxVotes) * 100) : 8 }}%; min-height: 8px; opacity: {{ $data['votes'] > 0 ? '1' : '0.15' }};"></div>
                    </div>
                    <span style="font-size: 0.6rem; color: #6B7280; margin-top: 0.5rem; font-family: 'Poppins', sans-serif;">{{ $data['month'] }}</span>
                    <span style="font-size: 0.6rem; color: #9CA3AF; font-weight: 600; font-family: 'Poppins', sans-serif;">{{ $data['votes'] }}</span>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Plan Features Section --}}
        @if(in_array($restaurant->subscription_tier, ['premium', 'elite']))
        <div class="famer-features-card" style="margin-top: 1rem;">
            <div class="famer-features-header">
                <div>
                    <h3 style="font-size: 0.9375rem; font-weight: 600; color: #F5F5F5; margin: 0; font-family: 'Playfair Display', Georgia, serif;">Plan {{ ucfirst($restaurant->subscription_tier) }}</h3>
                    <p style="font-size: 0.75rem; color: #6B7280; margin: 0.125rem 0 0 0; font-family: 'Poppins', sans-serif;">Funciones activas de tu suscripcion</p>
                </div>
                <span style="background: rgba(212,175,55,0.08); border: 1px solid rgba(212,175,55,0.2); color: #D4AF37; font-size: 0.6875rem; font-weight: 600; padding: 0.25rem 0.75rem; border-radius: 9999px; font-family: 'Poppins', sans-serif; letter-spacing: 0.04em;">Activo</span>
            </div>
            <div style="padding: 1rem 1.25rem;">
                <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 0.5rem;">
                    @foreach(['Aparece en el directorio', 'Info basica editable', 'Integracion Google Maps', 'Verificar propiedad', 'Badge Destacado', 'Top 3 busquedas locales', 'Menu Digital + QR Code', 'Sistema de Reservaciones', 'Dashboard de Analiticas', 'Chatbot AI 24/7'] as $feature)
                    <div class="famer-feature-item">
                        <svg style="width: 0.875rem; height: 0.875rem; color: #D4AF37; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        <span style="font-size: 0.8125rem; color: #E5E7EB; font-family: 'Poppins', sans-serif;">{{ $feature }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @else
        {{-- Free Plan Upgrade CTA --}}
        <div class="famer-features-card" style="margin-top: 1rem;">
            <div class="famer-features-header">
                <div>
                    <h3 style="font-size: 0.9375rem; font-weight: 600; color: #F5F5F5; margin: 0; font-family: 'Playfair Display', Georgia, serif;">Plan Gratuito</h3>
                    <p style="font-size: 0.75rem; color: #6B7280; margin: 0.125rem 0 0 0; font-family: 'Poppins', sans-serif;">Actualiza para desbloquear mas funciones</p>
                </div>
            </div>
            <div style="padding: 1rem 1.25rem;">
                <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 0.5rem;">
                    @foreach(['Aparece en el directorio', 'Info basica editable', 'Integracion Google Maps', 'Verificar propiedad'] as $feature)
                    <div class="famer-feature-item">
                        <svg style="width: 0.875rem; height: 0.875rem; color: #D4AF37; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        <span style="font-size: 0.8125rem; color: #E5E7EB; font-family: 'Poppins', sans-serif;">{{ $feature }}</span>
                    </div>
                    @endforeach
                    @foreach(['Badge Destacado', 'Top 3 busquedas', 'Menu Digital + QR', 'Reservaciones', 'Analiticas', 'Chatbot AI'] as $feature)
                    <div class="famer-feature-item" style="opacity: 0.4;">
                        <svg style="width: 0.875rem; height: 0.875rem; color: #4B5563; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        <span style="font-size: 0.8125rem; color: #6B7280; font-family: 'Poppins', sans-serif;">{{ $feature }}</span>
                    </div>
                    @endforeach
                </div>
                <div style="text-align: center; margin-top: 1.25rem;">
                    <a href="/claim" class="famer-btn-gold">
                        Actualizar a Premium
                    </a>
                </div>
            </div>
        </div>
        @endif

        {{-- QR Code & Social Share --}}
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-top: 1rem;">
            {{-- QR Code --}}
            <div class="famer-qr-card">
                <p class="famer-section-title">QR Code para Votacion</p>
                <div style="display: flex; align-items: center; gap: 1.25rem;">
                    <img src="{{ $qrCodeUrl }}" alt="QR Code" style="width: 110px; height: 110px; border-radius: 0.5rem; border: 1px solid #2A2A2A; background: #F5F5F5; padding: 0.25rem;">
                    <div>
                        <p style="font-size: 0.8125rem; color: #6B7280; margin: 0 0 0.875rem 0; font-family: 'Poppins', sans-serif; line-height: 1.5;">Imprime este QR y colocalo en tu restaurante para que tus clientes voten.</p>
                        <a href="{{ url('/owner/qr-print/' . $restaurant->id) }}" target="_blank" class="famer-btn-gold">
                            Descargar QR
                        </a>
                    </div>
                </div>
            </div>

            {{-- Social Share --}}
            <div class="famer-share-card">
                <p class="famer-section-title">Compartir y conseguir votos</p>
                <p style="font-size: 0.8125rem; color: #6B7280; margin: 0 0 1rem 0; font-family: 'Poppins', sans-serif;">Invita a tus clientes a votar por tu restaurante</p>
                <div style="display: flex; gap: 0.625rem; flex-wrap: wrap;">
                    <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode($shareUrl) }}" target="_blank" class="famer-share-btn">
                        Facebook
                    </a>
                    <a href="https://twitter.com/intent/tweet?url={{ urlencode($shareUrl) }}&text=Vota+por+{{ urlencode($restaurant->name) }}" target="_blank" class="famer-share-btn">
                        Twitter/X
                    </a>
                    <a href="https://wa.me/?text={{ urlencode('Vota por ' . $restaurant->name . ': ' . $shareUrl) }}" target="_blank" class="famer-share-btn">
                        WhatsApp
                    </a>
                </div>
                <p style="font-size: 0.625rem; color: #4B5563; margin: 0.875rem 0 0 0; font-family: 'Poppins', sans-serif; word-break: break-all;">{{ $shareUrl }}</p>
            </div>
        </div>

</x-filament-panels::page>
