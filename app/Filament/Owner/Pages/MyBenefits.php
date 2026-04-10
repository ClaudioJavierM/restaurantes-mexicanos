<?php

namespace App\Filament\Owner\Pages;

use App\Models\SubscriberCoupon;
use App\Models\SubscriptionBenefit;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class MyBenefits extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-gift';
    protected static ?string $navigationLabel = 'Mis Beneficios FAMER';
    protected static ?string $title = 'Mis Beneficios FAMER';
    protected static ?string $navigationGroup = 'Configuración';
    protected static ?int $navigationSort = 10;

    protected static string $view = 'filament.owner.pages.my-benefits';

    public $subscriberCoupon = null;
    public $benefits = [];
    public $restaurant = null;

    public function mount(): void
    {
        $user = Auth::user();
        $this->restaurant = $user->firstAccessibleRestaurant();

        if ($this->restaurant) {
            // Get or create subscriber coupon
            $this->subscriberCoupon = SubscriberCoupon::where('restaurant_id', $this->restaurant->id)
                ->where('user_id', $user->id)
                ->first();

            // If no coupon exists, create one
            if (!$this->subscriberCoupon && $this->restaurant->is_claimed) {
                $tier = $this->restaurant->subscription_tier ?? 'free';
                $this->subscriberCoupon = SubscriberCoupon::createForRestaurant(
                    $this->restaurant,
                    $user,
                    $tier
                );
            }

            if ($this->subscriberCoupon) {
                $this->benefits = $this->subscriberCoupon->getBenefitsWithStatus();
            }
        }
    }

    public function copyCode(): void
    {
        if ($this->subscriberCoupon) {
            $this->dispatch('copy-to-clipboard', code: $this->subscriberCoupon->code);
        }
    }

    public static function shouldRegisterNavigation(): bool
    {
        $user = Auth::user();
        if (!$user) return false;

        $restaurant = $user->firstAccessibleRestaurant();
        return $restaurant && $restaurant->is_claimed;
    }
}
