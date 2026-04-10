<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Restaurant;
use App\Models\RestaurantVote;

class RestaurantComparator extends Component
{
    public string $search1 = '';
    public string $search2 = '';
    public ?int $restaurant1Id = null;
    public ?int $restaurant2Id = null;
    public array $results1 = [];
    public array $results2 = [];

    // Pre-load from query string ?r1=slug&r2=slug
    public function mount(?string $r1 = null, ?string $r2 = null): void
    {
        if ($r1) {
            $r = Restaurant::approved()->where('slug', $r1)->first();
            if ($r) {
                $this->restaurant1Id = $r->id;
                $this->search1 = $r->name;
            }
        }
        if ($r2) {
            $r = Restaurant::approved()->where('slug', $r2)->first();
            if ($r) {
                $this->restaurant2Id = $r->id;
                $this->search2 = $r->name;
            }
        }
    }

    public function updatedSearch1(): void
    {
        $this->results1 = $this->searchRestaurants($this->search1);
        $this->restaurant1Id = null;
    }

    public function updatedSearch2(): void
    {
        $this->results2 = $this->searchRestaurants($this->search2);
        $this->restaurant2Id = null;
    }

    private function searchRestaurants(string $term): array
    {
        if (strlen($term) < 2) return [];
        return Restaurant::approved()
            ->where('name', 'LIKE', "%{$term}%")
            ->limit(6)
            ->get(['id', 'name', 'city', 'slug'])
            ->map(fn($r) => [
                'id'   => $r->id,
                'name' => $r->name,
                'city' => $r->city,
                'slug' => $r->slug,
            ])
            ->toArray();
    }

    public function selectRestaurant(int $slot, int $id, string $name): void
    {
        if ($slot === 1) {
            $this->restaurant1Id = $id;
            $this->search1 = $name;
            $this->results1 = [];
        } else {
            $this->restaurant2Id = $id;
            $this->search2 = $name;
            $this->results2 = [];
        }
    }

    public function clearSlot(int $slot): void
    {
        if ($slot === 1) {
            $this->restaurant1Id = null;
            $this->search1 = '';
            $this->results1 = [];
        } else {
            $this->restaurant2Id = null;
            $this->search2 = '';
            $this->results2 = [];
        }
    }

    public function getRestaurant1Property(): ?Restaurant
    {
        return $this->restaurant1Id
            ? Restaurant::with('state')->find($this->restaurant1Id)
            : null;
    }

    public function getRestaurant2Property(): ?Restaurant
    {
        return $this->restaurant2Id
            ? Restaurant::with('state')->find($this->restaurant2Id)
            : null;
    }

    private function getVoteCount(int $id): int
    {
        return RestaurantVote::where('restaurant_id', $id)
            ->where('year', now()->year)
            ->where('month', now()->month)
            ->count();
    }

    public function render()
    {
        $r1 = $this->restaurant1;
        $r2 = $this->restaurant2;

        $votes1 = $r1 ? $this->getVoteCount($r1->id) : 0;
        $votes2 = $r2 ? $this->getVoteCount($r2->id) : 0;

        return view('livewire.restaurant-comparator', compact('r1', 'r2', 'votes1', 'votes2'))
            ->layout('layouts.app', ['title' => 'Comparar Restaurantes — FAMER']);
    }
}
