<?php

namespace App\Livewire;

use App\Models\User;
use App\Services\ListmonkService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Validate;
use Livewire\Component;

class NewsletterSignup extends Component
{
    protected static bool $isLazy = true;

    #[Validate('required|email|max:255')]
    public string $email = '';

    #[Validate('nullable|string|max:100')]
    public string $name = '';

    #[Validate('nullable|string|max:100')]
    public string $city = '';

    public bool $success    = false;
    public bool $duplicate  = false;
    public string $errorMsg = '';

    public function mount(): void
    {
        // Pre-fill fields if user is authenticated
        if (Auth::check()) {
            $user        = Auth::user();
            $this->email = $user->email ?? '';
            $this->name  = $user->name  ?? '';
        }
    }

    public function subscribe(ListmonkService $listmonk): void
    {
        $this->errorMsg  = '';
        $this->duplicate = false;

        $this->validate();

        $listId = (int) config('services.listmonk.list_users_id', 1);

        // If we have no list ID from config, resolve by name
        if ($listId === 0) {
            $listId = $listmonk->getOrCreateList('FAMER Usuarios', 'public');
        }

        $attribs = [
            'source' => 'newsletter_form',
            'city'   => $this->city ?: '',
        ];

        $ok = $listmonk->subscribe(
            email   : $this->email,
            name    : $this->name ?: $this->email,
            listIds : [$listId],
            attribs : $attribs,
        );

        if (!$ok) {
            // subscribe() returns true even for 409 (already subscribed) — false = real error
            $this->errorMsg = 'Hubo un problema al suscribirte. Intenta de nuevo en unos segundos.';
            return;
        }

        // Update the authenticated user's record if applicable
        if (Auth::check()) {
            /** @var User $user */
            $user = Auth::user();

            if ($user->email === $this->email) {
                if (!$user->newsletter_subscribed) {
                    $user->update([
                        'newsletter_subscribed'    => true,
                        'newsletter_subscribed_at' => now(),
                    ]);
                } else {
                    // User was already subscribed — flag as duplicate for UX
                    $this->duplicate = true;
                }
            }
        }

        $this->success = true;
        $this->dispatch('newsletter-subscribed', email: $this->email);
    }

    public function render()
    {
        return view('livewire.newsletter-signup');
    }
}
