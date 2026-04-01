<?php

namespace App\Livewire;

use App\Models\Restaurant;
use App\Models\MenuItem;
use App\Models\AnalyticsEvent;
use App\Models\Coupon;
use App\Models\RestaurantVote;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Livewire\Component;

class RestaurantDetail extends Component
{
    public $slug;
    public $restaurant;

    // Menu properties
    public $coupons = [];
    public $activeTab = 'info'; // info, menu, reviews
    public $selectedCategory = 'all';
    public $selectedDietaryFilter = [];
    public $selectedSpiceFilter = null;
    public $showMenuItemModal = false;
    public $selectedMenuItem = null;

    // Vote properties
    public bool $hasVoted = false;
    public ?string $voteMessage = null;
    public bool $voteSuccess = false;
    public int $monthlyVotes = 0;

    public function mount($slug)
    {
        $this->slug = $slug;

        // Load restaurant and track page view
        $this->restaurant = Restaurant::where('slug', $slug)
            ->with(['state', 'category', 'reviews', 'media'])
            ->firstOrFail();

        // Track page view event
        AnalyticsEvent::track(
            $this->restaurant->id,
            AnalyticsEvent::EVENT_PAGE_VIEW,
            ['page_path' => request()->path()]
        );

        // Load featured coupons
        $this->coupons = Coupon::where('restaurant_id', $this->restaurant->id)
            ->active()
            ->valid()
            ->where('is_featured', true)
            ->take(3)
            ->get();

        // Check if user already voted this month (only for logged in users)
        $this->checkIfAlreadyVoted();

        // Load vote count for this restaurant
        $this->loadVoteCount();
    }

    protected function loadVoteCount()
    {
        $month = Carbon::now()->month;
        $year = Carbon::now()->year;

        $this->monthlyVotes = RestaurantVote::where('restaurant_id', $this->restaurant->id)
            ->where('month', $month)
            ->where('year', $year)
            ->count();
    }

    protected function checkIfAlreadyVoted()
    {
        // Only check for logged in users
        if (!auth()->check()) {
            $this->hasVoted = false;
            return;
        }

        $month = Carbon::now()->month;
        $year = Carbon::now()->year;

        // Check if user already voted for THIS restaurant this month
        $existingVote = RestaurantVote::where('user_id', auth()->id())
            ->where('restaurant_id', $this->restaurant->id)
            ->where('month', $month)
            ->where('year', $year)
            ->first();

        $this->hasVoted = $existingVote !== null;
    }

    public function voteForRestaurant()
    {
        // Must be logged in to vote
        if (!auth()->check()) {
            $this->voteMessage = 'Debes iniciar sesion para votar.';
            $this->voteSuccess = false;
            return;
        }

        $month = Carbon::now()->month;
        $year = Carbon::now()->year;

        // Check if user already voted for THIS restaurant this month
        $existingVote = RestaurantVote::where('user_id', auth()->id())
            ->where('restaurant_id', $this->restaurant->id)
            ->where('month', $month)
            ->where('year', $year)
            ->first();

        if ($existingVote) {
            $this->voteMessage = 'Ya votaste por este restaurante este mes.';
            $this->voteSuccess = false;
            $this->hasVoted = true;
            return;
        }

        // Record vote
        RestaurantVote::create([
            'restaurant_id' => $this->restaurant->id,
            'user_id' => auth()->id(),
            'voter_ip' => request()->ip(),
            'fingerprint' => 'user_' . auth()->id(),
            'month' => $month,
            'year' => $year,
            'city' => $this->restaurant->city,
            'state_code' => $this->restaurant->state?->code ?? 'XX',
            'source' => 'detail_page',
        ]);

        $this->hasVoted = true;
        $this->voteSuccess = true;
        $this->voteMessage = 'Gracias por tu voto para ' . $this->restaurant->name . '!';
        $this->monthlyVotes++;
    }

    public function switchTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function filterByCategory($category)
    {
        $this->selectedCategory = $category;
    }

    public function toggleDietaryFilter($option)
    {
        if (in_array($option, $this->selectedDietaryFilter)) {
            $this->selectedDietaryFilter = array_values(array_diff($this->selectedDietaryFilter, [$option]));
        } else {
            $this->selectedDietaryFilter[] = $option;
        }
    }

    public function filterBySpice($level)
    {
        $this->selectedSpiceFilter = $this->selectedSpiceFilter === $level ? null : $level;
    }

    public function clearMenuFilters()
    {
        $this->selectedCategory = 'all';
        $this->selectedDietaryFilter = [];
        $this->selectedSpiceFilter = null;
    }

    public function showMenuItem($menuItemId)
    {
        $this->selectedMenuItem = MenuItem::find($menuItemId);
        $this->showMenuItemModal = true;
    }

    public function closeMenuItemModal()
    {
        $this->showMenuItemModal = false;
        $this->selectedMenuItem = null;
    }

    public function render()
    {
        // Get menu categories with items
        $availableCategories = $this->restaurant->menuCategories()
            ->active()
            ->whereHas('items', function($q) {
                $q->where('is_available', true);
            })
            ->ordered()
            ->get();

        // Load menu items with filters
        $menuItemsQuery = $this->restaurant->menuItems()
            ->where('is_available', true)
            ->orderBy('sort_order')
            ->orderBy('name');

        // Apply category filter
        if ($this->selectedCategory !== 'all') {
            $menuItemsQuery->whereHas('category', function($q) {
                $q->where('id', $this->selectedCategory);
            });
        }

        // Apply dietary filters
        if (!empty($this->selectedDietaryFilter)) {
            foreach ($this->selectedDietaryFilter as $dietary) {
                $menuItemsQuery->whereJsonContains('dietary_tags', $dietary);
            }
        }

        $menuItems = $menuItemsQuery->get();

        // Get popular items
        $popularMenuItems = $this->restaurant->menuItems()
            ->where('is_available', true)
            ->where('is_popular', true)
            ->limit(6)
            ->get();

        // Get visitor stats for this restaurant (cached for 5 minutes)
        $visitorStats = cache()->remember(
            'restaurant_stats_' . $this->restaurant->id,
            300,
            function () {
                $startOfMonth = now()->startOfMonth();

                $totalViews = AnalyticsEvent::where('restaurant_id', $this->restaurant->id)
                    ->where('event_type', AnalyticsEvent::EVENT_PAGE_VIEW)
                    ->count();

                $monthlyViews = AnalyticsEvent::where('restaurant_id', $this->restaurant->id)
                    ->where('event_type', AnalyticsEvent::EVENT_PAGE_VIEW)
                    ->where('created_at', '>=', $startOfMonth)
                    ->count();

                return [
                    'total' => $totalViews,
                    'monthly' => $monthlyViews,
                ];
            }
        );

        // Related restaurants: same city first, fallback to same state/category
        $nearbyRestaurants = Restaurant::approved()
            ->where('id', '!=', $this->restaurant->id)
            ->with(['state', 'media'])
            ->where(function($q) {
                $q->where('city', $this->restaurant->city)
                  ->orWhere('state_id', $this->restaurant->state_id);
            })
            ->orderByRaw("CASE WHEN city = ? THEN 0 ELSE 1 END, google_reviews_count DESC", [$this->restaurant->city])
            ->limit(6)
            ->get();

        $r = $this->restaurant;
        $stateCode = $r->state?->code ?? $r->state?->name ?? '';
        $isEn = app()->getLocale() === 'en';

        // SEO-optimized title: Name | Best Mexican Restaurant in City, ST | FAMER
        $seoTitle = $isEn
            ? "{$r->name} | Best Mexican Restaurant in {$r->city}, {$stateCode} | FAMER"
            : "{$r->name} | Restaurante Mexicano en {$r->city}, {$stateCode} | FAMER";

        // Rich meta description with rating + review count for higher CTR
        $totalReviews = (int)(($r->google_reviews_count ?? 0) + ($r->yelp_reviews_count ?? 0)
            + $r->reviews()->where('status', 'approved')->count());
        $displayRating = $r->google_rating ?? $r->yelp_rating ?? null;

        // Prefer locale-specific AI description, fallback chain: ai_en → ai_es → description
        $bestDescription = $isEn
            ? ($r->ai_description_en ?: $r->ai_description ?: $r->description)
            : ($r->ai_description ?: $r->description);
        $hasAiDesc = $isEn ? (bool) $r->ai_description_en : (bool) $r->ai_description;

        if ($bestDescription) {
            $descBase = Str::limit(strip_tags($bestDescription), 155);
        } elseif ($isEn) {
            $descBase = "Authentic Mexican restaurant in {$r->city}, {$stateCode}";
        } else {
            $descBase = "Restaurante mexicano en {$r->city}, {$stateCode}";
        }

        // Only append rating snippet if description doesn't already mention it
        $ratingSnippet = '';
        if ($displayRating && $totalReviews > 0 && ! $hasAiDesc) {
            $ratingSnippet = $isEn
                ? " Rated {$displayRating}/5 from " . number_format($totalReviews) . " reviews."
                : " Calificación {$displayRating}/5 con " . number_format($totalReviews) . " reseñas.";
        }

        $ctaSnippet = $isEn ? ' View menu, hours & reserve a table.'
                            : ' Ver menú, horarios y reservar mesa.';

        $seoDescription = $descBase . $ratingSnippet . $ctaSnippet;

        return view('livewire.restaurant-detail', [
            'restaurant' => $r,
            'menuItems' => $menuItems,
            'popularMenuItems' => $popularMenuItems,
            'availableCategories' => $availableCategories,
            'visitorStats' => $visitorStats,
            'nearbyRestaurants' => $nearbyRestaurants,
        ])->layout('layouts.app', [
            'title'           => $seoTitle,
            'metaDescription' => $seoDescription,
            'seoRestaurant'   => $r,
        ]);
    }
}
