<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Restaurant;
use App\Models\State;
use Illuminate\Support\Str;

class CityRestaurants extends Component
{
    use WithPagination;

    public string $city = '';
    public string $stateCode = '';
    public ?State $stateModel = null;
    public string $sortBy = 'rating';

    public function mount(string $city, string $state): void
    {
        // Normalize: "dallas-tx" city param, "tx" state param
        $this->city = Str::title(str_replace('-', ' ', $city));
        $this->stateCode = strtoupper($state);
        $this->stateModel = State::where('code', $this->stateCode)->first();
    }

    public function getRestaurantsProperty()
    {
        $query = Restaurant::approved()
            ->where('city', 'LIKE', $this->city . '%')
            ->when($this->stateModel, fn($q) => $q->where('state_id', $this->stateModel->id))
            ->with('state');

        $sorted = match($this->sortBy) {
            'rating'  => $query->orderByDesc('average_rating'),
            'reviews' => $query->orderByDesc('total_reviews'),
            'name'    => $query->orderBy('name'),
            default   => $query->orderByDesc('average_rating'),
        };

        return $sorted->paginate(24);
    }

    public function render()
    {
        $restaurants = $this->restaurants;
        $cityName    = $this->city;
        $stateName   = $this->stateModel?->name ?? $this->stateCode;
        $stateCode   = $this->stateCode;
        $total       = $restaurants->total();

        $title    = "Restaurantes Mexicanos en {$cityName}, {$stateCode}";
        $metaDesc = "Descubre los mejores {$total} restaurantes mexicanos en {$cityName}, {$stateCode}. Reseñas verificadas, fotos, menús y más en FAMER.";

        return view('livewire.city-restaurants', compact(
            'restaurants', 'cityName', 'stateName', 'stateCode', 'total', 'title', 'metaDesc'
        ))->layout('layouts.app', ['title' => $title]);
    }
}
