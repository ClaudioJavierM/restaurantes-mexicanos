<?php

namespace App\Filament\Owner\Pages;

use App\Models\LoyaltyReward;
use App\Models\LoyaltyRedemption;
use App\Models\RestaurantCustomer;
use Filament\Pages\Page;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class LoyaltyProgram extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-gift';
    protected static ?string $navigationLabel = 'Programa de Lealtad';
    protected static ?string $title = 'Programa de Lealtad';
    protected static ?string $navigationGroup = 'Mi Negocio';
    protected static ?int $navigationSort = 20;

    protected static string $view = 'filament.owner.pages.loyalty-program';

    public $restaurant;
    public bool $showRewardForm = false;
    public ?int $editingRewardId = null;
    public ?array $rewardData = [];
    
    // Settings
    public bool $loyaltyEnabled = false;
    public int $pointsPerDollar = 1;
    public int $pointsPerVisit = 10;
    
    // Stats
    public int $totalMembers = 0;
    public int $totalPointsIssued = 0;
    public int $totalRedemptions = 0;
    public int $activeRewards = 0;

    public function mount(): void
    {
        $this->restaurant = Auth::user()->restaurants()->first();
        if ($this->restaurant) {
            $this->loyaltyEnabled = $this->restaurant->loyalty_enabled ?? false;
            $this->pointsPerDollar = $this->restaurant->points_per_dollar ?? 1;
            $this->pointsPerVisit = $this->restaurant->points_per_visit ?? 10;
            $this->loadStats();
        }
    }

    public function loadStats(): void
    {
        if (!$this->restaurant) return;
        
        $this->totalMembers = RestaurantCustomer::where('restaurant_id', $this->restaurant->id)
            ->where('points', '>', 0)
            ->count();
            
        $this->totalPointsIssued = RestaurantCustomer::where('restaurant_id', $this->restaurant->id)
            ->sum('points');
            
        $this->totalRedemptions = LoyaltyRedemption::whereHas('reward', function ($q) {
            $q->where('restaurant_id', $this->restaurant->id);
        })->where('status', 'used')->count();
        
        $this->activeRewards = LoyaltyReward::where('restaurant_id', $this->restaurant->id)
            ->active()
            ->count();
    }

    public function toggleLoyalty(): void
    {
        $this->loyaltyEnabled = !$this->loyaltyEnabled;
        $this->restaurant->update(['loyalty_enabled' => $this->loyaltyEnabled]);
        
        Notification::make()
            ->title($this->loyaltyEnabled ? 'Programa activado' : 'Programa desactivado')
            ->success()
            ->send();
    }

    public function saveSettings(): void
    {
        $this->restaurant->update([
            'points_per_dollar' => $this->pointsPerDollar,
            'points_per_visit' => $this->pointsPerVisit,
        ]);
        
        Notification::make()
            ->title('Configuracion guardada')
            ->success()
            ->send();
    }

    public function getRewardsProperty()
    {
        if (!$this->restaurant) return collect();
        return LoyaltyReward::where('restaurant_id', $this->restaurant->id)
            ->orderBy('points_required')
            ->get();
    }

    public function getTopMembersProperty()
    {
        if (!$this->restaurant) return collect();
        return RestaurantCustomer::where('restaurant_id', $this->restaurant->id)
            ->where('points', '>', 0)
            ->orderByDesc('points')
            ->limit(10)
            ->get();
    }

    public function getRecentRedemptionsProperty()
    {
        if (!$this->restaurant) return collect();
        return LoyaltyRedemption::whereHas('reward', function ($q) {
            $q->where('restaurant_id', $this->restaurant->id);
        })
        ->with(['customer', 'reward'])
        ->latest()
        ->limit(10)
        ->get();
    }

    public function newReward(): void
    {
        $this->editingRewardId = null;
        $this->rewardData = [
            'name' => '',
            'description' => '',
            'points_required' => 100,
            'reward_type' => 'discount_percentage',
            'reward_value' => 10,
            'free_item_name' => '',
            'is_active' => true,
        ];
        $this->showRewardForm = true;
    }

    public function editReward(int $id): void
    {
        $reward = LoyaltyReward::find($id);
        if ($reward && $reward->restaurant_id === $this->restaurant->id) {
            $this->editingRewardId = $id;
            $this->rewardData = [
                'name' => $reward->name,
                'description' => $reward->description,
                'points_required' => $reward->points_required,
                'reward_type' => $reward->reward_type,
                'reward_value' => $reward->reward_value,
                'free_item_name' => $reward->free_item_name,
                'is_active' => $reward->is_active,
            ];
            $this->showRewardForm = true;
        }
    }

    public function saveReward(): void
    {
        $data = array_merge($this->rewardData, [
            'restaurant_id' => $this->restaurant->id,
        ]);

        if ($this->editingRewardId) {
            LoyaltyReward::where('id', $this->editingRewardId)
                ->where('restaurant_id', $this->restaurant->id)
                ->update($data);
        } else {
            LoyaltyReward::create($data);
        }

        $this->showRewardForm = false;
        $this->rewardData = [];
        $this->editingRewardId = null;
        $this->loadStats();
        
        Notification::make()->title('Recompensa guardada')->success()->send();
    }

    public function deleteReward(int $id): void
    {
        LoyaltyReward::where('id', $id)
            ->where('restaurant_id', $this->restaurant->id)
            ->delete();
        $this->loadStats();
        Notification::make()->title('Recompensa eliminada')->success()->send();
    }

    public function toggleRewardStatus(int $id): void
    {
        $reward = LoyaltyReward::find($id);
        if ($reward && $reward->restaurant_id === $this->restaurant->id) {
            $reward->update(['is_active' => !$reward->is_active]);
            $this->loadStats();
        }
    }

    public function addPointsToCustomer(int $customerId, int $points): void
    {
        $customer = RestaurantCustomer::find($customerId);
        if ($customer && $customer->restaurant_id === $this->restaurant->id) {
            $customer->addPoints($points);
            $this->loadStats();
            Notification::make()->title("{$points} puntos agregados")->success()->send();
        }
    }

    public static function shouldRegisterNavigation(): bool
    {
        $user = Auth::user();
        if (!$user) return false;
        $restaurant = $user->restaurants()->first();
        return $restaurant && $restaurant->is_claimed;
    }

    public static function getNavigationBadge(): ?string
    {
        $user = Auth::user();
        if (!$user) return null;
        $restaurant = $user->restaurants()->first();
        if ($restaurant && !in_array($restaurant->subscription_plan, ['premium', 'elite'])) {
            return 'PRO';
        }
        return null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }
}
