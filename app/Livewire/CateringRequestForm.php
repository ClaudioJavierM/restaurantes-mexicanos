<?php

namespace App\Livewire;

use App\Models\CateringRequest;
use App\Models\Restaurant;
use Livewire\Component;

class CateringRequestForm extends Component
{
    public Restaurant $restaurant;
    public bool $showForm = false;
    public bool $submitted = false;

    // Form fields
    public string $contact_name = '';
    public string $contact_email = '';
    public string $contact_phone = '';
    public string $event_date = '';
    public int $guest_count = 50;
    public string $event_type = 'corporativo';
    public string $event_location = '';
    public string $notes = '';
    public string $budget = '';

    protected function rules(): array
    {
        return [
            'contact_name' => 'required|string|max:100',
            'contact_email' => 'required|email|max:150',
            'contact_phone' => 'nullable|string|max:20',
            'event_date' => 'required|date|after:today',
            'guest_count' => 'required|integer|min:5|max:10000',
            'event_type' => 'required|string|in:' . implode(',', array_keys(CateringRequest::$eventTypes)),
            'event_location' => 'nullable|string|max:200',
            'notes' => 'nullable|string|max:1000',
            'budget' => 'nullable|numeric|min:0',
        ];
    }

    protected $messages = [
        'contact_name.required' => 'Tu nombre es requerido.',
        'contact_email.required' => 'Tu email es requerido.',
        'event_date.required' => 'La fecha del evento es requerida.',
        'event_date.after' => 'La fecha del evento debe ser en el futuro.',
        'guest_count.min' => 'El mínimo de invitados es 5.',
    ];

    public function mount(Restaurant $restaurant)
    {
        $this->restaurant = $restaurant;

        if (auth()->check()) {
            $this->contact_name = auth()->user()->name;
            $this->contact_email = auth()->user()->email;
        }
    }

    public function submitRequest(): void
    {
        $this->validate();

        CateringRequest::create([
            'restaurant_id' => $this->restaurant->id,
            'user_id' => auth()->id(),
            'contact_name' => $this->contact_name,
            'contact_email' => $this->contact_email,
            'contact_phone' => $this->contact_phone ?: null,
            'event_date' => $this->event_date,
            'guest_count' => $this->guest_count,
            'event_type' => $this->event_type,
            'event_location' => $this->event_location ?: null,
            'notes' => $this->notes ?: null,
            'budget' => $this->budget ? (float) $this->budget : null,
            'status' => 'pending',
        ]);

        $this->submitted = true;
        $this->showForm = false;
    }

    public function render()
    {
        return view('livewire.catering-request-form', [
            'eventTypes' => CateringRequest::$eventTypes,
        ]);
    }
}
