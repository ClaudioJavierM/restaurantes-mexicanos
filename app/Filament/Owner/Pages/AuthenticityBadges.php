<?php

namespace App\Filament\Owner\Pages;

use App\Models\AuthenticityBadgeRequest;
use Filament\Pages\Page;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class AuthenticityBadges extends Page
{
    protected static ?string $navigationIcon  = 'heroicon-o-shield-check';
    protected static ?string $navigationLabel = 'Insignias de Autenticidad';
    protected static ?string $title           = 'Insignias de Autenticidad';
    protected static ?string $navigationGroup = 'Mi Negocio';
    protected static ?int    $navigationSort  = 5;

    protected static string $view = 'filament.owner.pages.authenticity-badges';

    // ── State ─────────────────────────────────────────────────────
    public $restaurant;

    /** ID of the badge being requested (e.g. 'family_owned') */
    public ?string $requestingBadgeId = null;

    /** Evidence textarea content */
    public string $evidence = '';

    // ── Lifecycle ─────────────────────────────────────────────────
    public function mount(): void
    {
        $this->restaurant = Auth::user()->restaurants()->first();
    }

    // ── Navigation guard ─────────────────────────────────────────
    public static function shouldRegisterNavigation(): bool
    {
        $user = Auth::user();
        if (!$user) return false;
        $restaurant = $user->restaurants()->first();
        return $restaurant && $restaurant->is_claimed;
    }

    // ── Computed ─────────────────────────────────────────────────

    /**
     * Returns the badge catalog enriched with the restaurant's current
     * request status for each badge.
     *
     * @return array<string, array>
     */
    public function getBadgesProperty(): array
    {
        if (!$this->restaurant) return [];

        // Load all requests for this restaurant, keyed by badge_id
        $requests = AuthenticityBadgeRequest::where('restaurant_id', $this->restaurant->id)
            ->get()
            ->keyBy('badge_id');

        $approved = collect($this->restaurant->authenticity_badges ?? [])
            ->keyBy('id');

        $result = [];
        foreach (AuthenticityBadgeRequest::$catalog as $id => $badge) {
            $request = $requests->get($id);

            if ($approved->has($id)) {
                $status = 'approved';
            } elseif ($request) {
                $status = $request->status; // pending | rejected
            } else {
                $status = 'none';
            }

            $result[$id] = array_merge($badge, ['status' => $status]);
        }

        return $result;
    }

    // ── Actions ──────────────────────────────────────────────────

    public function openRequestModal(string $badgeId): void
    {
        $this->requestingBadgeId = $badgeId;
        $this->evidence = '';
        $this->dispatch('open-badge-modal');
    }

    public function closeModal(): void
    {
        $this->requestingBadgeId = null;
        $this->evidence = '';
        $this->dispatch('close-badge-modal');
    }

    public function submitRequest(): void
    {
        if (!$this->restaurant || !$this->requestingBadgeId) return;

        // Prevent duplicates (pending or approved already exists)
        $existing = AuthenticityBadgeRequest::where('restaurant_id', $this->restaurant->id)
            ->where('badge_id', $this->requestingBadgeId)
            ->whereIn('status', ['pending', 'approved'])
            ->first();

        if ($existing) {
            Notification::make()
                ->warning()
                ->title('Ya existe una solicitud')
                ->body('Esta insignia ya fue solicitada o aprobada.')
                ->send();
            $this->closeModal();
            return;
        }

        $this->validate([
            'evidence' => 'required|min:20|max:1000',
        ], [
            'evidence.required' => 'Por favor explica por qué merece esta insignia tu restaurante.',
            'evidence.min'      => 'La explicación debe tener al menos 20 caracteres.',
            'evidence.max'      => 'La explicación no debe exceder 1000 caracteres.',
        ]);

        AuthenticityBadgeRequest::create([
            'restaurant_id' => $this->restaurant->id,
            'user_id'       => Auth::id(),
            'badge_id'      => $this->requestingBadgeId,
            'status'        => 'pending',
            'evidence'      => trim($this->evidence),
        ]);

        Notification::make()
            ->success()
            ->title('Solicitud enviada')
            ->body('El equipo de FAMER revisará tu solicitud.')
            ->send();

        $this->closeModal();
    }

    // ── Navigation badge (show count of approved badges) ─────────
    public static function getNavigationBadge(): ?string
    {
        $user = Auth::user();
        if (!$user) return null;
        $restaurant = $user->restaurants()->first();
        if (!$restaurant) return null;
        $count = count($restaurant->authenticity_badges ?? []);
        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'success';
    }
}
