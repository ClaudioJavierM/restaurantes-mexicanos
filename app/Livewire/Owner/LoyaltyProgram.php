<?php

namespace App\Livewire\Owner;

use App\Models\LoyaltyRedemption;
use App\Models\LoyaltyReward;
use App\Models\Restaurant;
use App\Models\RestaurantCustomer;
use Livewire\Component;
use Livewire\WithPagination;

class LoyaltyProgram extends Component
{
    use WithPagination;

    public Restaurant $restaurant;

    public string $activeTab = 'settings';

    // Settings
    public bool $loyaltyEnabled = false;
    public int $pointsPerDollar = 1;
    public int $pointsPerVisit = 10;

    // Reward form
    public bool $showRewardModal = false;
    public ?int $editingRewardId = null;
    public string $rewardName = '';
    public string $rewardDescription = '';
    public int $rewardPointsRequired = 100;
    public string $rewardType = 'discount_percentage';
    public float $rewardValue = 10;
    public string $rewardFreeItemName = '';
    public ?int $rewardUsageLimit = null;
    public bool $rewardIsActive = true;

    public function mount(Restaurant $restaurant)
    {
        $this->restaurant = $restaurant;
        $this->loyaltyEnabled = (bool) $restaurant->loyalty_enabled;
        $this->pointsPerDollar = (int) ($restaurant->points_per_dollar ?? 1);
        $this->pointsPerVisit = (int) ($restaurant->points_per_visit ?? 10);
    }

    public function switchTab(string $tab): void
    {
        $this->activeTab = $tab;
        $this->resetPage();
    }

    public function saveSettings(): void
    {
        $this->validate([
            'pointsPerDollar' => 'required|integer|min:0|max:100',
            'pointsPerVisit' => 'required|integer|min:0|max:1000',
        ]);

        $this->restaurant->update([
            'loyalty_enabled' => $this->loyaltyEnabled,
            'points_per_dollar' => $this->pointsPerDollar,
            'points_per_visit' => $this->pointsPerVisit,
        ]);

        session()->flash('loyalty_success', 'Configuración guardada correctamente.');
    }

    public function openRewardModal(?int $rewardId = null): void
    {
        $this->resetRewardForm();

        if ($rewardId) {
            $reward = LoyaltyReward::where('restaurant_id', $this->restaurant->id)->find($rewardId);
            if ($reward) {
                $this->editingRewardId = $reward->id;
                $this->rewardName = $reward->name;
                $this->rewardDescription = $reward->description ?? '';
                $this->rewardPointsRequired = $reward->points_required;
                $this->rewardType = $reward->reward_type;
                $this->rewardValue = (float) $reward->reward_value;
                $this->rewardFreeItemName = $reward->free_item_name ?? '';
                $this->rewardUsageLimit = $reward->usage_limit;
                $this->rewardIsActive = (bool) $reward->is_active;
            }
        }

        $this->showRewardModal = true;
    }

    public function closeRewardModal(): void
    {
        $this->showRewardModal = false;
        $this->resetRewardForm();
    }

    protected function resetRewardForm(): void
    {
        $this->editingRewardId = null;
        $this->rewardName = '';
        $this->rewardDescription = '';
        $this->rewardPointsRequired = 100;
        $this->rewardType = 'discount_percentage';
        $this->rewardValue = 10;
        $this->rewardFreeItemName = '';
        $this->rewardUsageLimit = null;
        $this->rewardIsActive = true;
    }

    public function saveReward(): void
    {
        $this->validate([
            'rewardName' => 'required|string|max:255',
            'rewardPointsRequired' => 'required|integer|min:1',
            'rewardType' => 'required|in:discount_percentage,discount_fixed,free_item,custom',
            'rewardValue' => 'nullable|numeric|min:0',
        ]);

        $data = [
            'restaurant_id' => $this->restaurant->id,
            'name' => $this->rewardName,
            'description' => $this->rewardDescription ?: null,
            'points_required' => $this->rewardPointsRequired,
            'reward_type' => $this->rewardType,
            'reward_value' => $this->rewardValue,
            'free_item_name' => $this->rewardType === 'free_item' ? $this->rewardFreeItemName : null,
            'usage_limit' => $this->rewardUsageLimit,
            'is_active' => $this->rewardIsActive,
        ];

        if ($this->editingRewardId) {
            LoyaltyReward::where('restaurant_id', $this->restaurant->id)
                ->where('id', $this->editingRewardId)
                ->update($data);
            session()->flash('loyalty_success', 'Recompensa actualizada.');
        } else {
            LoyaltyReward::create($data);
            session()->flash('loyalty_success', 'Recompensa creada.');
        }

        $this->closeRewardModal();
    }

    public function deleteReward(int $rewardId): void
    {
        LoyaltyReward::where('restaurant_id', $this->restaurant->id)
            ->where('id', $rewardId)
            ->delete();

        session()->flash('loyalty_success', 'Recompensa eliminada.');
    }

    public function toggleRewardActive(int $rewardId): void
    {
        $reward = LoyaltyReward::where('restaurant_id', $this->restaurant->id)->find($rewardId);
        if ($reward) {
            $reward->update(['is_active' => !$reward->is_active]);
        }
    }

    public function render()
    {
        $rewards = LoyaltyReward::where('restaurant_id', $this->restaurant->id)
            ->orderBy('sort_order')
            ->orderBy('points_required')
            ->get();

        $redemptions = LoyaltyRedemption::whereHas('reward', function ($q) {
                $q->where('restaurant_id', $this->restaurant->id);
            })
            ->with(['reward', 'customer'])
            ->latest()
            ->paginate(15);

        $topCustomers = RestaurantCustomer::where('restaurant_id', $this->restaurant->id)
            ->where('points', '>', 0)
            ->orderByDesc('points')
            ->limit(10)
            ->get();

        $stats = [
            'total_rewards' => $rewards->count(),
            'active_rewards' => $rewards->where('is_active', true)->count(),
            'total_redemptions' => LoyaltyRedemption::whereHas('reward', fn($q) => $q->where('restaurant_id', $this->restaurant->id))->count(),
            'total_points_distributed' => RestaurantCustomer::where('restaurant_id', $this->restaurant->id)->sum('points'),
        ];

        return view('livewire.owner.loyalty-program', [
            'rewards' => $rewards,
            'redemptions' => $redemptions,
            'topCustomers' => $topCustomers,
            'stats' => $stats,
            'rewardTypes' => LoyaltyReward::getRewardTypes(),
        ]);
    }
}
