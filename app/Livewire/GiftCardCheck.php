<?php

namespace App\Livewire;

use App\Models\GiftCard;
use Livewire\Component;

class GiftCardCheck extends Component
{
    public string $code;
    public ?GiftCard $card = null;
    public string $checkCode = '';
    public string $error = '';

    public function mount(string $code): void
    {
        $this->code = strtoupper($code);
        $this->findCard();
    }

    protected function findCard(): void
    {
        $this->card = GiftCard::with('restaurant')
            ->where('code', $this->code)
            ->first();
    }

    public function checkBalance(): void
    {
        $this->error = '';
        $normalized = strtoupper(trim($this->checkCode));

        if ($normalized !== $this->code) {
            $this->error = 'El código no coincide.';
            return;
        }

        $this->findCard();
    }

    public function render()
    {
        return view('livewire.gift-card-check')
            ->layout('layouts.guest');
    }
}
