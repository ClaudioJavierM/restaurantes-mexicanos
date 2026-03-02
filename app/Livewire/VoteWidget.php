<?php

namespace App\Livewire;

use App\Models\Restaurant;
use App\Models\RestaurantVote;
use App\Models\RestaurantRanking;
use Livewire\Component;

class VoteWidget extends Component
{
    public Restaurant $restaurant;
    public bool $hasVoted = false;
    public bool $justVoted = false;
    public int $monthlyVotes = 0;
    public int $yearlyVotes = 0;
    public ?int $lastYearPosition = null;
    public ?string $lastYearScope = null;
    public bool $isDefendingChampion = false;

    public function mount(Restaurant $restaurant)
    {
        $this->restaurant = $restaurant;
        $this->checkVoteStatus();
        $this->loadVoteCount();
        $this->loadRankingInfo();
    }

    protected function loadRankingInfo()
    {
        // Check last year's city ranking
        $lastYearRanking = RestaurantRanking::where('restaurant_id', $this->restaurant->id)
            ->where('year', now()->year - 1)
            ->where('ranking_type', 'city')
            ->first();

        if ($lastYearRanking) {
            $this->lastYearPosition = $lastYearRanking->position;
            $this->lastYearScope = $lastYearRanking->ranking_scope;
            $this->isDefendingChampion = $lastYearRanking->position === 1;
        }
    }

    protected function checkVoteStatus()
    {
        $fingerprint = $this->getFingerprint();
        $year = now()->year;
        $month = now()->month;

        $this->hasVoted = RestaurantVote::where('restaurant_id', $this->restaurant->id)
            ->where('year', $year)
            ->where('month', $month)
            ->where(function ($query) use ($fingerprint) {
                $query->where('voter_fingerprint', $fingerprint);
                if (auth()->check()) {
                    $query->orWhere('user_id', auth()->id());
                }
            })
            ->exists();
    }

    protected function loadVoteCount()
    {
        $this->monthlyVotes = RestaurantVote::where('restaurant_id', $this->restaurant->id)
            ->where('year', now()->year)
            ->where('month', now()->month)
            ->count();

        $this->yearlyVotes = RestaurantVote::where('restaurant_id', $this->restaurant->id)
            ->where('year', now()->year)
            ->count();
    }

    protected function getFingerprint(): string
    {
        $ip = request()->ip();
        $userAgent = request()->userAgent();
        return md5($ip . $userAgent);
    }

    public function vote()
    {
        if ($this->hasVoted) {
            return;
        }

        $fingerprint = $this->getFingerprint();

        RestaurantVote::create([
            'restaurant_id' => $this->restaurant->id,
            'user_id' => auth()->id(),
            'voter_fingerprint' => $fingerprint,
            'voter_ip' => request()->ip(),
            'year' => now()->year,
            'month' => now()->month,
            'vote_type' => 'up',
        ]);

        $this->hasVoted = true;
        $this->justVoted = true;
        $this->monthlyVotes++;
        $this->yearlyVotes++;

        // Track analytics
        try {
            \App\Models\AnalyticsEvent::create([
                'restaurant_id' => $this->restaurant->id,
                'event_type' => 'vote',
                'user_id' => auth()->id(),
                'session_id' => session()->getId(),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        } catch (\Exception $e) {}
    }

    public function render()
    {
        return view('livewire.vote-widget');
    }
}
