<?php

namespace App\Filament\Owner\Pages;

use App\Models\Restaurant;
use App\Models\RestaurantReferral;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class ReferralProgram extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-gift';
    protected static ?string $navigationLabel = 'Programa de Referidos';
    protected static ?string $title = 'Programa de Referidos';
    protected static ?string $navigationGroup = 'Configuracion';
    protected static ?int $navigationSort = 13;

    protected static string $view = 'filament.owner.pages.referral-program';

    public $restaurant = null;
    public string $referralUrl = '';
    public array $stats = [];
    public $referrals = [];

    public static function shouldRegisterNavigation(): bool
    {
        $user = auth()->user();
        if (!$user) return false;

        $restaurant = $user->allAccessibleRestaurants()->first();
        return $restaurant && $restaurant->is_claimed;
    }

    public function mount(): void
    {
        $user = Auth::user();
        $this->restaurant = $user->allAccessibleRestaurants()->first();

        if (!$this->restaurant) return;

        // Generate referral code if not exists
        if (!$this->restaurant->referral_code) {
            $code = RestaurantReferral::generateCode();
            $this->restaurant->update(['referral_code' => $code]);
            $this->restaurant->refresh();
        }

        $this->referralUrl = url('/claim?ref=' . $this->restaurant->referral_code);

        // Load referrals
        $this->referrals = RestaurantReferral::where('referrer_restaurant_id', $this->restaurant->id)
            ->with('referred')
            ->latest()
            ->take(20)
            ->get()
            ->toArray();

        // Stats
        $allReferrals = RestaurantReferral::where('referrer_restaurant_id', $this->restaurant->id);
        $this->stats = [
            'total' => $allReferrals->count(),
            'claimed' => (clone $allReferrals)->whereIn('status', ['claimed', 'subscribed', 'rewarded'])->count(),
            'subscribed' => (clone $allReferrals)->whereIn('status', ['subscribed', 'rewarded'])->count(),
            'rewarded' => (clone $allReferrals)->where('status', 'rewarded')->count(),
        ];
    }
}
