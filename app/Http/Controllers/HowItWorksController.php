<?php

namespace App\Http\Controllers;

use App\Models\Restaurant;
use Illuminate\View\View;

class HowItWorksController extends Controller
{
    public function __invoke(): View
    {
        $totalRestaurants = Restaurant::where('status', 'approved')->count();
        $totalStates      = \App\Models\State::whereHas('restaurants')->count();
        $totalCities      = Restaurant::where('status', 'approved')
            ->whereNotNull('city')
            ->distinct('city')
            ->count();

        $isEn = str_contains(request()->getHost(), 'famousmexicanrestaurants.com');

        return view('pages.como-funciona', compact(
            'totalRestaurants',
            'totalStates',
            'totalCities',
            'isEn',
        ));
    }
}
