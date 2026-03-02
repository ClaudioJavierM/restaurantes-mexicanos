<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Restaurant;
use App\Models\RestaurantRanking;
use App\Models\State;

class FamerRankings extends Component
{
    use WithPagination;

    public ?string $scope = 'national';
    public ?int $stateId = null;
    public ?string $state = null; // Accept state code from URL
    public ?string $city = null;
    public ?int $year = null;

    protected $queryString = [
        'scope' => ['except' => 'national'],
        'state' => ['except' => ''], // Changed from stateId to state
        'city' => ['except' => ''],
        'year' => ['except' => ''],
    ];

    public function mount()
    {
        $this->year = $this->year ?? (now()->year - 1);
        
        // Convert state code to stateId if provided
        if ($this->state && !$this->stateId) {
            $stateModel = State::where('code', strtoupper($this->state))->first();
            if ($stateModel) {
                $this->stateId = $stateModel->id;
            }
        }
    }

    public function updatedScope()
    {
        if ($this->scope === 'national') {
            $this->stateId = null;
            $this->state = null;
            $this->city = null;
        } elseif ($this->scope === 'state') {
            $this->city = null;
        }
        $this->resetPage();
    }

    public function updatedStateId()
    {
        // Update state code when stateId changes
        if ($this->stateId) {
            $stateModel = State::find($this->stateId);
            $this->state = $stateModel?->code;
        } else {
            $this->state = null;
        }
        $this->city = null;
        $this->resetPage();
    }

    public function updatedCity()
    {
        $this->resetPage();
    }

    public function setScope(string $scope)
    {
        $this->scope = $scope;
        $this->updatedScope();
    }

    public function selectState(int $stateId)
    {
        $this->stateId = $stateId;
        $stateModel = State::find($stateId);
        $this->state = $stateModel?->code;
        $this->scope = 'state';
        $this->city = null;
        $this->resetPage();
    }

    public function selectCity(string $city, int $stateId)
    {
        $this->city = $city;
        $this->stateId = $stateId;
        $stateModel = State::find($stateId);
        $this->state = $stateModel?->code;
        $this->scope = 'city';
        $this->resetPage();
    }

    public function getRankingsProperty()
    {
        $query = RestaurantRanking::with(['restaurant' => function($q) {
            $q->with(['state', 'category']);
        }])
        ->whereHas('restaurant') // Only include rankings with existing restaurants
        ->where('year', $this->year)
        ->where('ranking_type', $this->scope);

        if ($this->scope === 'state' && $this->stateId) {
            // Get state code from state_id
            $stateModel = State::find($this->stateId);
            if ($stateModel) {
                $query->where('ranking_scope', $stateModel->code);
            }
        } elseif ($this->scope === 'city' && $this->city) {
            // ranking_scope contains the city name
            $query->where('ranking_scope', $this->city);
        } elseif ($this->scope === 'national') {
            $query->where('ranking_scope', 'usa');
        }

        return $query->orderBy('position')->paginate(25);
    }

    public function getStatesProperty()
    {
        return State::orderBy('name')->get();
    }

    public function getCitiesProperty()
    {
        if (!$this->stateId) {
            return collect();
        }

        return Restaurant::where('state_id', $this->stateId)
            ->where('status', 'approved')
            ->whereNotNull('city')
            ->where('city', '!=', '')
            ->distinct()
            ->orderBy('city')
            ->pluck('city');
    }

    public function getTopCitiesProperty()
    {
        return RestaurantRanking::where('year', $this->year)
            ->where('ranking_type', 'city')
            ->where('position', 1)
            ->whereHas('restaurant') // Only include rankings with existing restaurants
            ->with(['restaurant.state'])
            ->get()
            ->unique('ranking_scope')
            ->take(12);
    }

    public function render()
    {
        return view('livewire.famer-rankings')
            ->layout('layouts.app');
    }
}
