<?php

namespace App\Livewire\Owner;

use App\Models\RestaurantPrinter;
use Livewire\Component;
use Livewire\Attributes\Computed;

class PrinterSetup extends Component
{
    public string $printerName = 'Cocina Principal';
    public bool $showSuccess = false;

    #[Computed]
    public function restaurant()
    {
        return auth()->user()->restaurant;
    }

    #[Computed]
    public function printer(): ?RestaurantPrinter
    {
        return RestaurantPrinter::where('restaurant_id', $this->restaurant->id)
            ->where('is_active', true)
            ->first();
    }

    public function createPrinter(): void
    {
        $this->validate(['printerName' => 'required|string|max:100']);

        RestaurantPrinter::create([
            'restaurant_id' => $this->restaurant->id,
            'name'          => $this->printerName,
        ]);

        $this->showSuccess = true;
    }

    public function togglePrinter(): void
    {
        if ($this->printer) {
            $this->printer->update(['is_active' => !$this->printer->is_active]);
        }
    }

    public function deletePrinter(): void
    {
        $this->printer?->delete();
        $this->showSuccess = false;
    }

    public function render()
    {
        return view('livewire.owner.printer-setup');
    }
}
