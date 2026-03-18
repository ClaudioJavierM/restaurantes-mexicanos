<?php

namespace App\Livewire;

use App\Models\Restaurant;
use App\Models\RestaurantWaitlist;
use Livewire\Component;

class JoinWaitlist extends Component
{
    public Restaurant $restaurant;

    public bool $showForm = false;
    public bool $joined = false;
    public ?RestaurantWaitlist $myEntry = null;

    public string $name = '';
    public string $phone = '';
    public int $party_size = 2;
    public string $special_request = '';

    protected function rules(): array
    {
        return [
            'name'            => 'required|string|max:100',
            'phone'           => 'nullable|string|max:20',
            'party_size'      => 'required|integer|min:1|max:20',
            'special_request' => 'nullable|string|max:200',
        ];
    }

    protected $messages = [
        'name.required'       => 'Tu nombre es requerido.',
        'party_size.required' => 'Indica cuántas personas son.',
    ];

    public function mount(Restaurant $restaurant)
    {
        $this->restaurant = $restaurant;

        // Pre-fill if logged in
        if (auth()->check()) {
            $this->name = auth()->user()->name;
        }
    }

    public function getQueueCountProperty(): int
    {
        return RestaurantWaitlist::where('restaurant_id', $this->restaurant->id)
            ->where('status', 'waiting')
            ->where('created_at', '>=', now()->startOfDay())
            ->count();
    }

    public function joinWaitlist(): void
    {
        $this->validate();

        $position = RestaurantWaitlist::where('restaurant_id', $this->restaurant->id)
            ->where('status', 'waiting')
            ->where('created_at', '>=', now()->startOfDay())
            ->count() + 1;

        $this->myEntry = RestaurantWaitlist::create([
            'restaurant_id'   => $this->restaurant->id,
            'user_id'         => auth()->id(),
            'name'            => $this->name,
            'phone'           => $this->phone ?: null,
            'party_size'      => $this->party_size,
            'special_request' => $this->special_request ?: null,
            'status'          => 'waiting',
            'position'        => $position,
        ]);

        $this->joined = true;
        $this->showForm = false;
    }

    public function cancelMySpot(): void
    {
        if ($this->myEntry) {
            $this->myEntry->update(['status' => 'cancelled']);
            RestaurantWaitlist::recalculatePositions($this->restaurant->id);
            $this->myEntry = null;
            $this->joined = false;
            $this->reset(['name', 'phone', 'party_size', 'special_request']);
        }
    }

    public function render()
    {
        return view('livewire.join-waitlist', [
            'queueCount' => $this->queueCount,
        ]);
    }
}
