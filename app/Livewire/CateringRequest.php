<?php

namespace App\Livewire;

use App\Models\CateringRequest as CateringRequestModel;
use App\Models\Restaurant;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;

class CateringRequest extends Component
{
    public ?int $restaurantId = null;
    public string $contactName  = '';
    public string $contactEmail = '';
    public string $contactPhone = '';
    public string $eventType    = 'otro';
    public string $eventDate    = '';
    public string $guestCount   = '';
    public string $eventLocation = '';
    public string $budgetRange  = '';
    public string $message      = '';
    public bool   $submitted    = false;

    /** @var Restaurant|null */
    public $restaurant = null;

    // ── Lifecycle ──────────────────────────────────────────────────

    public function mount(?int $restaurant = null): void
    {
        $this->restaurantId = $restaurant;

        if ($restaurant) {
            $this->restaurant = Restaurant::find($restaurant);
        }

        if (Auth::check()) {
            $user = Auth::user();
            $this->contactName  = $user->name  ?? '';
            $this->contactEmail = $user->email ?? '';
        }
    }

    // ── Validation ─────────────────────────────────────────────────

    protected function rules(): array
    {
        return [
            'contactName'  => ['required', 'string', 'max:255'],
            'contactEmail' => ['required', 'email', 'max:255'],
            'contactPhone' => ['nullable', 'string', 'max:30'],
            'eventType'    => ['required', 'in:boda,quinceañera,corporativo,cumpleaños,graduacion,otro'],
            'eventDate'    => ['nullable', 'date', 'after:today'],
            'guestCount'   => ['nullable', 'integer', 'min:1', 'max:10000'],
            'eventLocation'=> ['nullable', 'string', 'max:255'],
            'budgetRange'  => ['nullable', 'string', 'max:100'],
            'message'      => ['required', 'string', 'min:20'],
        ];
    }

    protected function messages(): array
    {
        return [
            'contactName.required'  => 'Tu nombre es requerido.',
            'contactEmail.required' => 'Tu correo electrónico es requerido.',
            'contactEmail.email'    => 'Ingresa un correo electrónico válido.',
            'message.required'      => 'Por favor describe los detalles de tu evento.',
            'message.min'           => 'El mensaje debe tener al menos 20 caracteres.',
            'eventDate.after'       => 'La fecha del evento debe ser posterior a hoy.',
        ];
    }

    // ── Actions ────────────────────────────────────────────────────

    public function submit(): void
    {
        $this->validate();

        $catering = CateringRequestModel::create([
            'restaurant_id'  => $this->restaurantId,
            'user_id'        => Auth::id(),
            'contact_name'   => $this->contactName,
            'contact_email'  => $this->contactEmail,
            'contact_phone'  => $this->contactPhone ?: null,
            'event_type'     => $this->eventType,
            'event_date'     => $this->eventDate ?: null,
            'guest_count'    => $this->guestCount ? (int) $this->guestCount : null,
            'event_location' => $this->eventLocation ?: null,
            'budget_range'   => $this->budgetRange ?: null,
            'message'        => $this->message,
            'status'         => 'pending',
        ]);

        // Send notification email to restaurant owner (or admin if no restaurant)
        try {
            $recipientEmail = null;

            if ($this->restaurant && $this->restaurant->email) {
                $recipientEmail = $this->restaurant->email;
            } elseif (config('mail.from.address')) {
                $recipientEmail = config('mail.from.address');
            }

            if ($recipientEmail) {
                Mail::raw(
                    "Nueva solicitud de catering #{$catering->id}\n\n"
                    . "Contacto: {$catering->contact_name} <{$catering->contact_email}>\n"
                    . "Teléfono: " . ($catering->contact_phone ?? 'No proporcionado') . "\n"
                    . "Tipo de evento: {$catering->event_type_label}\n"
                    . "Fecha: " . ($catering->event_date ? $catering->event_date->format('d/m/Y') : 'Por definir') . "\n"
                    . "Invitados: " . ($catering->guest_count ?? 'Por definir') . "\n"
                    . "Lugar: " . ($catering->event_location ?? 'Por definir') . "\n"
                    . "Presupuesto: " . ($catering->budget_range ?? 'Por definir') . "\n\n"
                    . "Mensaje:\n{$catering->message}\n\n"
                    . "Ver en admin: " . config('app.url') . "/admin",
                    fn ($mail) => $mail
                        ->to($recipientEmail)
                        ->subject("Nueva solicitud de catering — {$catering->event_type_label}")
                );
            }
        } catch (\Throwable $e) {
            Log::warning('CateringRequest: no se pudo enviar email de notificación', [
                'catering_id' => $catering->id,
                'error'       => $e->getMessage(),
            ]);
        }

        Log::info('CateringRequest submitted', [
            'id'           => $catering->id,
            'restaurant_id'=> $catering->restaurant_id,
            'contact_email'=> $catering->contact_email,
            'event_type'   => $catering->event_type,
        ]);

        $this->submitted = true;
    }

    // ── Render ─────────────────────────────────────────────────────

    public function render()
    {
        return view('livewire.catering-request')
            ->layout('layouts.app', [
                'title'           => 'Solicitar Catering — FAMER',
                'metaDescription' => 'Solicita catering de restaurantes mexicanos auténticos para tu boda, quinceañera, evento corporativo o cualquier celebración especial.',
            ]);
    }
}
