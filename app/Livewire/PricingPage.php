<?php

namespace App\Livewire;

use Livewire\Component;

class PricingPage extends Component
{
    public function render()
    {
        return view('livewire.pricing-page')
            ->layout('layouts.app');
    }
}
