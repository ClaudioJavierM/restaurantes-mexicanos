<?php

namespace App\Filament\Owner\Pages;

use App\Models\SponsoredListing;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class SponsoredListings extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-megaphone';
    protected static ?string $navigationLabel = 'Destacar Restaurante';
    protected static ?string $title = 'Listados Patrocinados';
    protected static ?string $navigationGroup = 'Configuracion';
    protected static ?int $navigationSort = 14;

    protected static string $view = 'filament.owner.pages.sponsored-listings';

    public $restaurant = null;
    public array $listings = [];
    public string $selectedPlacement = 'homepage_featured';
    public int $selectedWeeks = 4;

    public static function shouldRegisterNavigation(): bool
    {
        $user = Auth::user();
        if (!$user) return false;
        $restaurant = $user->allAccessibleRestaurants()->first();
        return $restaurant && $restaurant->is_claimed;
    }

    public function mount(): void
    {
        $this->restaurant = Auth::user()->allAccessibleRestaurants()->first();
        $this->loadListings();
    }

    protected function loadListings(): void
    {
        if (!$this->restaurant) return;
        $this->listings = SponsoredListing::where('restaurant_id', $this->restaurant->id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->toArray();
    }

    public function getQuoteProperty(): array
    {
        $pricePerWeek = SponsoredListing::$prices[$this->selectedPlacement] ?? 49;
        $total = $pricePerWeek * $this->selectedWeeks;
        $ends = now()->addWeeks($this->selectedWeeks)->format('d M, Y');

        return [
            'price_per_week' => $pricePerWeek,
            'weeks'          => $this->selectedWeeks,
            'total'          => $total,
            'ends'           => $ends,
            'placement_label' => SponsoredListing::$placements[$this->selectedPlacement] ?? '',
        ];
    }

    public function requestSponsorship(): void
    {
        if (!$this->restaurant) return;

        $pricePerWeek = SponsoredListing::$prices[$this->selectedPlacement] ?? 49;
        $total = $pricePerWeek * $this->selectedWeeks;

        SponsoredListing::create([
            'restaurant_id' => $this->restaurant->id,
            'placement'     => $this->selectedPlacement,
            'starts_at'     => now()->toDateString(),
            'ends_at'       => now()->addWeeks($this->selectedWeeks)->toDateString(),
            'amount_paid'   => $total,
            'status'        => 'pending',
            'notes'         => 'Solicitud enviada por el propietario',
        ]);

        $this->loadListings();

        Notification::make()
            ->title('¡Solicitud enviada!')
            ->body('Te contactaremos para coordinar el pago y activar tu listado.')
            ->success()
            ->send();
    }
}
