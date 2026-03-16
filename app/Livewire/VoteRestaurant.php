<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Restaurant;
use App\Models\RestaurantVote;
use App\Models\FanScore;
use App\Models\State;
use Carbon\Carbon;

class VoteRestaurant extends Component
{
    public ?string $stateCode = null;
    public ?string $city = null;
    public string $search = '';
    public ?int $votedRestaurantId = null;
    public bool $showThankYou = false;
    public ?string $voteError = null;
    public ?Restaurant $preselectedRestaurant = null;

    protected $queryString = ['stateCode', 'city', 'search'];

    public function mount($state = null, $city = null, $slug = null)
    {
        if ($state) {
            $this->stateCode = strtoupper($state);
        }
        if ($city) {
            $this->city = urldecode($city);
        }
        // Pre-select restaurant when arriving via /votar/{slug}
        if ($slug) {
            $this->preselectedRestaurant = Restaurant::where('slug', $slug)->first();
            if ($this->preselectedRestaurant) {
                $this->stateCode = $this->preselectedRestaurant->state?->code;
                $this->city = $this->preselectedRestaurant->city;
            }
        }
    }
    
    public function vote($restaurantId)
    {
        $restaurant = Restaurant::find($restaurantId);
        if (!$restaurant) {
            $this->voteError = 'Restaurante no encontrado';
            return;
        }
        
        $fingerprint = $this->generateFingerprint();
        $month = Carbon::now()->month;
        $year = Carbon::now()->year;
        
        // Check if already voted this month
        $existingVote = RestaurantVote::where('voter_fingerprint', $fingerprint)
            ->where('month', $month)
            ->where('year', $year)
            ->first();
            
        if ($existingVote) {
            $this->voteError = 'Ya votaste este mes. Puedes votar de nuevo el próximo mes.';
            return;
        }
        
        // Check IP limit (max 3 votes per IP per month)
        $ipVotes = RestaurantVote::where('voter_ip', request()->ip())
            ->where('month', $month)
            ->where('year', $year)
            ->count();
            
        if ($ipVotes >= 3) {
            $this->voteError = 'Límite de votos alcanzado desde esta conexión.';
            return;
        }
        
        // Record vote
        RestaurantVote::create([
            'restaurant_id' => $restaurantId,
            'user_id' => auth()->id(),
            'voter_ip' => request()->ip(),
            'voter_fingerprint' => $fingerprint,
            'month' => $month,
            'year' => $year,
        ]);

        // Update fan score if authenticated
        if (auth()->id()) {
            $fanScore = FanScore::getOrCreate(auth()->id(), $restaurantId, $year);
            $fanScore->addAction('vote');
        }

        $this->votedRestaurantId = $restaurantId;
        $this->showThankYou = true;
        $this->voteError = null;
    }
    
    protected function generateFingerprint()
    {
        $data = [
            request()->ip(),
            request()->userAgent(),
            request()->header('Accept-Language'),
        ];
        return hash('sha256', implode('|', $data));
    }
    
    public function getStatesProperty()
    {
        return State::orderBy('name')->get();
    }
    
    public function getCitiesProperty()
    {
        if (!$this->stateCode) return collect();
        
        return Restaurant::where('status', 'approved')
            ->whereHas('state', fn($q) => $q->where('code', $this->stateCode))
            ->select('city')
            ->distinct()
            ->orderBy('city')
            ->pluck('city');
    }
    
    public function getRestaurantsProperty()
    {
        $query = Restaurant::where('status', 'approved')
            ->with(['state', 'rankings' => fn($q) => $q->where('year', now()->year)->where('ranking_type', 'city')])
            ->withCount(['votes as monthly_votes' => function($q) {
                $q->where('month', now()->month)->where('year', now()->year);
            }]);
            
        if ($this->stateCode) {
            $query->whereHas('state', fn($q) => $q->where('code', $this->stateCode));
        }
        
        if ($this->city) {
            $query->where('city', $this->city);
        }
        
        if ($this->search) {
            $query->where('name', 'like', '%' . $this->search . '%');
        }
        
        return $query->orderByDesc('monthly_votes')
            ->orderByDesc('google_rating')
            ->limit(50)
            ->get();
    }
    
    public function closeThankYou()
    {
        $this->showThankYou = false;
    }
    
    public function render()
    {
        return view('livewire.vote-restaurant')
            ->layout('layouts.app');
    }
}
