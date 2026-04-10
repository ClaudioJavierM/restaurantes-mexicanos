<?php

namespace App\Filament\Owner\Pages;

use App\Models\OwnerNotification;
use App\Services\OwnerNotificationService;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class Notifications extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-bell';
    protected static ?string $navigationLabel = 'Notificaciones';
    protected static ?string $title = 'Mis Notificaciones';
    protected static ?int $navigationSort = 2;

    protected static string $view = 'filament.owner.pages.notifications';

    public $notifications = [];
    public $unreadCount = 0;
    public $restaurant = null;
    public ?int $restaurantId = null;

    public function mount(): void
    {
        $user = Auth::user();
        $this->restaurant = $user->firstAccessibleRestaurant();

        if ($this->restaurant) {
            $this->restaurantId = $this->restaurant->id;
            $this->loadNotifications();
        }
    }

    public function loadNotifications(): void
    {
        if (!$this->restaurant) {
            return;
        }

        $this->notifications = OwnerNotification::forRestaurant($this->restaurant->id)
            ->orderBy('created_at', 'desc')
            ->take(50)
            ->get();

        $this->unreadCount = OwnerNotification::forRestaurant($this->restaurant->id)
            ->unread()
            ->count();
    }

    public function markAsRead($notificationId): void
    {
        $notification = OwnerNotification::find($notificationId);
        if ($notification && $notification->restaurant_id === $this->restaurant->id) {
            $notification->markAsRead();
            $this->loadNotifications();
        }
    }

    public function markAllAsRead(): void
    {
        OwnerNotification::forRestaurant($this->restaurant->id)
            ->unread()
            ->update(['read_at' => now(), 'is_read' => true]);

        $this->loadNotifications();
    }

    public static function getNavigationBadge(): ?string
    {
        $user = Auth::user();
        if (!$user) return null;

        $restaurant = $user->firstAccessibleRestaurant();
        if (!$restaurant) return null;

        $count = OwnerNotification::forRestaurant($restaurant->id)
            ->unread()
            ->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
    }
}
