<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\CheckIn as CheckinModel;
use Illuminate\Support\Facades\Auth;

class CheckIn extends Component
{
    public int $restaurantId;
    public string $note = '';

    public function mount(int $restaurantId): void
    {
        $this->restaurantId = $restaurantId;
    }

    /** Total check-ins for this restaurant by all users. */
    public function getCheckinCountProperty(): int
    {
        return CheckinModel::where('restaurant_id', $this->restaurantId)->count();
    }

    /** How many times the authenticated user has checked into this restaurant. */
    public function getUserCheckinCountProperty(): int
    {
        if (!Auth::check()) {
            return 0;
        }
        return CheckinModel::where('user_id', Auth::id())
            ->where('restaurant_id', $this->restaurantId)
            ->count();
    }

    /** Whether the authenticated user has already checked in today. */
    public function getHasCheckedInTodayProperty(): bool
    {
        if (!Auth::check()) {
            return false;
        }
        return CheckinModel::where('user_id', Auth::id())
            ->where('restaurant_id', $this->restaurantId)
            ->where('visited_at', today()->toDateString())
            ->exists();
    }

    public function checkIn(): void
    {
        if (!Auth::check()) {
            $this->redirect(route('login'));
            return;
        }

        if ($this->hasCheckedInToday) {
            session()->flash('checkin_error', '¡Ya registraste tu visita hoy en este restaurante!');
            return;
        }

        CheckinModel::create([
            'user_id'      => Auth::id(),
            'restaurant_id' => $this->restaurantId,
            'visited_at'   => today()->toDateString(),
            'note'         => trim($this->note) ?: null,
        ]);

        $this->note = '';

        $totalVisits = $this->userCheckinCount; // re-computed after create
        session()->flash('checkin_success', '¡Check-in registrado! Visita #' . $totalVisits);

        $this->dispatch('checkin-recorded', restaurantId: $this->restaurantId);
    }

    public function getAchievementBadgeProperty(): array
    {
        $count = $this->userCheckinCount;

        if ($count >= 25) {
            return ['label' => 'Súper fan ⭐', 'color' => '#D4AF37'];
        }
        if ($count >= 10) {
            return ['label' => 'Fan del restaurante', 'color' => '#A78BFA'];
        }
        if ($count >= 5) {
            return ['label' => 'Visitante frecuente', 'color' => '#34D399'];
        }
        if ($count >= 1) {
            return ['label' => 'Nuevo visitante', 'color' => '#60A5FA'];
        }

        return [];
    }

    public function render()
    {
        return view('livewire.check-in');
    }
}
