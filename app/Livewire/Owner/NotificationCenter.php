<?php

namespace App\Livewire\Owner;

use App\Models\OwnerNotification;
use Livewire\Attributes\On;
use Livewire\Component;

class NotificationCenter extends Component
{
    protected static bool $isLazy = true;

    public int $restaurantId;
    public bool $showDropdown = false;
    public array $notifications = [];

    public function mount(): void
    {
        $this->refreshNotifications();
    }

    #[On('notification-received')]
    public function refreshNotifications(): void
    {
        $this->notifications = OwnerNotification::forRestaurant($this->restaurantId)
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get()
            ->toArray();
    }

    public function getUnreadCountProperty(): int
    {
        return collect($this->notifications)
            ->filter(fn ($n) => is_null($n['read_at']))
            ->count();
    }

    public function markAllRead(): void
    {
        OwnerNotification::forRestaurant($this->restaurantId)
            ->unread()
            ->update(['read_at' => now(), 'is_read' => true]);

        $this->refreshNotifications();
    }

    public function markRead(int $id): void
    {
        $notification = OwnerNotification::where('id', $id)
            ->where('restaurant_id', $this->restaurantId)
            ->first();

        if ($notification) {
            $notification->markAsRead();
            $this->refreshNotifications();
        }
    }

    public function toggleDropdown(): void
    {
        $this->showDropdown = !$this->showDropdown;
    }

    public function closeDropdown(): void
    {
        $this->showDropdown = false;
    }

    public function render()
    {
        return view('livewire.owner.notification-center');
    }
}
