<?php

namespace App\Livewire;

use App\Models\Restaurant;
use App\Models\RestaurantVote;
use Livewire\Component;

class QuickVote extends Component
{
    public Restaurant $restaurant;
    public bool $hasVoted = false;
    public bool $justVoted = false;
    public int $monthlyVotes = 0;
    
    // Optional email verification
    public bool $showEmailForm = false;
    public string $email = '';
    public bool $emailVerified = false;

    public function mount(string $slug)
    {
        $this->restaurant = Restaurant::where('slug', $slug)
            ->where('status', 'approved')
            ->firstOrFail();

        $this->checkVoteStatus();
        $this->loadVoteCount();
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
            'vote_type' => 'qr_scan',
        ]);

        $this->hasVoted = true;
        $this->justVoted = true;
        $this->monthlyVotes++;

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

    public function toggleEmailForm()
    {
        $this->showEmailForm = !$this->showEmailForm;
    }

    public function voteWithEmail()
    {
        $this->validate([
            'email' => 'required|email',
        ]);

        if ($this->hasVoted) {
            return;
        }

        // Check if this email already voted this month
        $emailVoted = RestaurantVote::where('restaurant_id', $this->restaurant->id)
            ->where('year', now()->year)
            ->where('month', now()->month)
            ->where('voter_email', $this->email)
            ->exists();

        if ($emailVoted) {
            $this->addError('email', 'Este email ya voto este mes por este restaurante.');
            return;
        }

        $fingerprint = $this->getFingerprint();

        RestaurantVote::create([
            'restaurant_id' => $this->restaurant->id,
            'user_id' => auth()->id(),
            'voter_fingerprint' => $fingerprint,
            'voter_ip' => request()->ip(),
            'voter_email' => $this->email,
            'year' => now()->year,
            'month' => now()->month,
            'vote_type' => 'qr_email',
            'is_verified' => true,
        ]);

        $this->hasVoted = true;
        $this->justVoted = true;
        $this->emailVerified = true;
        $this->monthlyVotes++;

        // Track analytics
        try {
            \App\Models\AnalyticsEvent::create([
                'restaurant_id' => $this->restaurant->id,
                'event_type' => 'vote_verified',
                'user_id' => auth()->id(),
                'session_id' => session()->getId(),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        } catch (\Exception $e) {}
    }

    public function render()
    {
        return view('livewire.quick-vote')
            ->layout('layouts.app', [
                'title' => 'Vota por ' . $this->restaurant->name . ' | FAMER Awards',
            ]);
    }
}
