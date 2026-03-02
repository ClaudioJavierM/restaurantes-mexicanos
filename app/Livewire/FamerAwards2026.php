<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Restaurant;
use App\Models\RestaurantNomination;
use App\Models\FamerSubscription;
use App\Models\State;

class FamerAwards2026 extends Component
{
    // Nomination form
    public string $restaurantName = '';
    public string $city = '';
    public string $stateCode = '';
    public string $address = '';
    public string $phone = '';
    public string $googleMapsUrl = '';
    public string $whyNominate = '';
    public string $nominatorName = '';
    public string $nominatorEmail = '';
    
    public bool $showNominationForm = false;
    public bool $nominationSubmitted = false;
    
    // Stats
    public int $totalRestaurants = 0;
    public int $totalCities = 0;
    public int $totalStates = 0;
    public int $daysUntilStart = 0;

    protected $rules = [
        'restaurantName' => 'required|min:3|max:255',
        'city' => 'required|min:2|max:100',
        'stateCode' => 'required|size:2',
        'nominatorEmail' => 'required|email',
        'whyNominate' => 'nullable|max:500',
    ];

    public function mount()
    {
        $this->totalRestaurants = Restaurant::where('status', 'approved')->count();
        $this->totalCities = Restaurant::where('status', 'approved')->distinct('city')->count('city');
        $this->totalStates = Restaurant::where('status', 'approved')->distinct('state_id')->count('state_id');
        
        $startDate = \Carbon\Carbon::create(2026, 1, 1);
        $this->daysUntilStart = max(0, now()->diffInDays($startDate, false));
    }

    public function toggleNominationForm()
    {
        $this->showNominationForm = !$this->showNominationForm;
    }

    public function submitNomination()
    {
        $this->validate();

        RestaurantNomination::create([
            'user_id' => auth()->id(),
            'restaurant_name' => $this->restaurantName,
            'address' => $this->address,
            'city' => $this->city,
            'state_code' => strtoupper($this->stateCode),
            'phone' => $this->phone,
            'google_maps_url' => $this->googleMapsUrl,
            'why_nominate' => $this->whyNominate,
            'nominator_name' => $this->nominatorName,
            'nominator_email' => $this->nominatorEmail,
            'nominator_ip' => request()->ip(),
            'status' => 'pending',
        ]);

        $this->nominationSubmitted = true;
        $this->reset(['restaurantName', 'city', 'stateCode', 'address', 'phone', 'googleMapsUrl', 'whyNominate']);
    }

    public function getStatesProperty()
    {
        return State::orderBy('name')->get();
    }

    public function render()
    {
        return view('livewire.famer-awards-2026')
            ->layout('layouts.app');
    }
}
