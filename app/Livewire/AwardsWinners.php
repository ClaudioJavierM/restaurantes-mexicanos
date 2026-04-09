<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Restaurant;
use Illuminate\Support\Facades\DB;

class AwardsWinners extends Component
{
    public int $year;
    public int $month;

    public function mount(int $year = 0, int $month = 0): void
    {
        $this->year  = $year  ?: now()->year;
        $this->month = $month ?: now()->month;
    }

    public function setMonth(int $year, int $month): void
    {
        $this->year  = $year;
        $this->month = $month;
    }

    public function getWinnersProperty()
    {
        $results = DB::table('restaurant_votes')
            ->select('restaurant_id', DB::raw('COUNT(*) as vote_count'))
            ->where('year', $this->year)
            ->where('month', $this->month)
            ->groupBy('restaurant_id')
            ->orderByDesc('vote_count')
            ->limit(10)
            ->get();

        return $results->map(function ($row) {
            $restaurant = Restaurant::with('state')->find($row->restaurant_id);
            if (! $restaurant) {
                return null;
            }
            $restaurant->vote_count = $row->vote_count;
            return $restaurant;
        })->filter()->values();
    }

    public function getMonthNameProperty(): string
    {
        $months = [
            '', 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
            'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre',
        ];

        return $months[$this->month] ?? '';
    }

    public function getAvailableMonthsProperty()
    {
        return DB::table('restaurant_votes')
            ->select('year', 'month', DB::raw('COUNT(*) as total'))
            ->groupBy('year', 'month')
            ->orderByDesc('year')
            ->orderByDesc('month')
            ->limit(24)
            ->get();
    }

    public function render()
    {
        $title = "FAMER Awards — {$this->monthName} {$this->year}";

        return view('livewire.awards-winners', [
            'winners'         => $this->winners,
            'monthName'       => $this->monthName,
            'availableMonths' => $this->availableMonths,
        ])->layout('layouts.app', ['title' => $title]);
    }
}
