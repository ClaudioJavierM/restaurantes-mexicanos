<?php

namespace App\Livewire;

use App\Models\Restaurant;
use App\Models\MenuItem;
use App\Models\AnalyticsEvent;
use App\Models\Coupon;
use App\Models\RestaurantVote;
use Carbon\Carbon;
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

        return view('livewire.restaurant-detail', [
            'restaurant' => $this->restaurant,
            'menuItems' => $menuItems,
            'popularMenuItems' => $popularMenuItems,
            'availableCategories' => $availableCategories,
            'visitorStats' => $visitorStats,
        ])->layout('layouts.app', [
            'title' => $this->restaurant->name . ' — ' . $this->restaurant->city . ', ' . ($this->restaurant->state?->name ?? ''),
            'seoRestaurant' => $this->restaurant,
        ]);
    }
}
