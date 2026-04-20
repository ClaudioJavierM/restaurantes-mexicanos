<?php

namespace App\Livewire;

use App\Models\SupportTicket;
use Livewire\Attributes\On;
use Livewire\Component;

class CarmenWidget extends Component
{
    public bool $isOpen = false;
    public string $step = 'form'; // form | success
    public string $name = '';
    public string $email = '';
    public string $phone = '';
    public string $restaurantName = '';
    public string $restaurantSlug = '';
    public string $issueType = 'verification';
    public string $message = '';
    public string $ticketNumber = '';
    public bool $isLoading = false;
    public array $formErrors = [];

    public function open(array $context = []): void
    {
        $this->isOpen = true;
        $this->step = 'form';

        if (!empty($context['restaurant_name'])) {
            $this->restaurantName = $context['restaurant_name'];
        }
        if (!empty($context['restaurant_slug'])) {
            $this->restaurantSlug = $context['restaurant_slug'];
        }
        if (!empty($context['issue_type'])) {
            $this->issueType = $context['issue_type'];
        }
        if (!empty($context['name'])) {
            $this->name = $context['name'];
        }
        if (!empty($context['email'])) {
            $this->email = $context['email'];
        }
    }

    #[On('open-carmen')]
    public function openWithContext(array $context = []): void
    {
        $this->open($context);
    }

    public function close(): void
    {
        $this->isOpen = false;
        $this->step = 'form';
        $this->formErrors = [];
    }

    public function submit(): void
    {
        $this->formErrors = [];

        // Validate
        if (trim($this->name) === '') {
            $this->formErrors['name'] = 'El nombre es requerido.';
        }

        if (trim($this->email) === '') {
            $this->formErrors['email'] = 'El email es requerido.';
        } elseif (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            $this->formErrors['email'] = 'Ingresa un email válido.';
        }

        if (strlen(trim($this->message)) < 10) {
            $this->formErrors['message'] = 'El mensaje debe tener al menos 10 caracteres.';
        }

        if (!empty($this->formErrors)) {
            return;
        }

        $this->isLoading = true;

        try {
            $ticket = SupportTicket::create([
                'name'            => trim($this->name),
                'email'           => trim($this->email),
                'phone'           => trim($this->phone) ?: null,
                'restaurant_name' => trim($this->restaurantName) ?: null,
                'restaurant_slug' => trim($this->restaurantSlug) ?: null,
                'issue_type'      => $this->issueType,
                'message'         => trim($this->message),
                'status'          => 'open',
                'source'          => 'carmen_widget',
                'context'         => [
                    'url'        => request()->header('referer', ''),
                    'user_agent' => request()->userAgent(),
                ],
            ]);

            $this->ticketNumber = $ticket->ticket_number;
            $this->step = 'success';
        } catch (\Throwable $e) {
            $this->formErrors['general'] = 'Ocurrió un error. Intenta de nuevo o escríbenos a soporte@restaurantesmexicanosfamosos.com';
        } finally {
            $this->isLoading = false;
        }
    }

    public function render()
    {
        return view('livewire.carmen-widget');
    }
}
