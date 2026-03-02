<?php

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public string $name = '';
    public string $email = '';
    public string $phone = '';
    public string $password = '';
    public string $password_confirmation = '';
    public bool $sms_marketing_consent = false;

    // Honeypot field - should always be empty
    public string $website = '';

    // Timestamp to detect bots that submit too fast
    public int $form_loaded_at = 0;

    public function mount(): void
    {
        $this->form_loaded_at = time();
    }

    /**
     * List of disposable email domains to block
     */
    protected function getDisposableEmailDomains(): array
    {
        return [
            'tempmail.com', 'throwaway.email', 'guerrillamail.com', 'mailinator.com',
            'temp-mail.org', '10minutemail.com', 'fakeinbox.com', 'trashmail.com',
            'getnada.com', 'mohmal.com', 'emailondeck.com', 'dispostable.com',
            'yopmail.com', 'sharklasers.com', 'guerrillamailblock.com', 'pokemail.net',
            'spam4.me', 'grr.la', 'spamgourmet.com', 'mytrashmail.com',
            'mailnesia.com', 'tempr.email', 'discard.email', 'discardmail.com',
            'spamfree24.org', 'jetable.org', 'spambox.us', 'throwawaymail.com',
        ];
    }

    /**
     * Check if name looks like spam (random characters)
     */
    protected function isSpamName(string $name): bool
    {
        $cleanName = str_replace(' ', '', $name);

        if (preg_match('/[a-z][A-Z]{3,}[a-z]/', $name)) {
            return true;
        }

        if (preg_match('/[bcdfghjklmnpqrstvwxyzBCDFGHJKLMNPQRSTVWXYZ]{6,}/', $cleanName)) {
            return true;
        }

        $uppercase = preg_match_all('/[A-Z]/', $name);
        $lowercase = preg_match_all('/[a-z]/', $name);
        $total = $uppercase + $lowercase;

        if ($total > 5 && $uppercase > 0) {
            $ratio = $uppercase / $total;
            if ($ratio > 0.4 && $uppercase > 3) {
                return true;
            }
        }

        if (strlen($cleanName) > 4 && !preg_match('/[aeiouAEIOU]/', $cleanName)) {
            return true;
        }

        return false;
    }

    /**
     * Handle an incoming registration request.
     */
    public function register(): void
    {
        $key = 'register:' . request()->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            throw ValidationException::withMessages([
                'email' => [__('Too many registration attempts. Please try again in :seconds seconds.', ['seconds' => $seconds])],
            ]);
        }

        RateLimiter::hit($key, 3600);

        if (!empty($this->website)) {
            $this->redirect(route('register'), navigate: true);
            return;
        }

        if (time() - $this->form_loaded_at < 3) {
            throw ValidationException::withMessages([
                'email' => [__('Please wait a moment before submitting the form.')],
            ]);
        }

        if ($this->isSpamName($this->name)) {
            throw ValidationException::withMessages([
                'name' => [__('Please enter a valid name.')],
            ]);
        }

        $emailDomain = strtolower(substr(strrchr($this->email, "@"), 1));
        if (in_array($emailDomain, $this->getDisposableEmailDomains())) {
            throw ValidationException::withMessages([
                'email' => [__('Please use a permanent email address. Temporary email services are not allowed.')],
            ]);
        }

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255', 'min:2'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'phone' => ['required', 'string', 'min:10', 'max:20'],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
            'sms_marketing_consent' => ['boolean'],
        ]);

        $validated['password'] = Hash::make($validated['password']);
        
        if ($this->sms_marketing_consent) {
            $validated['sms_consent_at'] = now();
        }

        event(new Registered($user = User::create($validated)));

        Auth::login($user);

        $this->redirect(route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div>
    <form wire:submit="register">
        <!-- Honeypot field -->
        <div class="absolute opacity-0 -z-10" aria-hidden="true" style="position: absolute; left: -9999px;">
            <label for="website">Website</label>
            <input type="text" wire:model.blur="website" id="website" name="website" tabindex="-1" autocomplete="off" />
        </div>

        <input type="hidden" wire:model.blur="form_loaded_at" />

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input wire:model.blur="name" id="name" class="block mt-1 w-full" type="text" name="name" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input wire:model.blur="email" id="email" class="block mt-1 w-full" type="email" name="email" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Phone -->
        <div class="mt-4">
            <x-input-label for="phone" :value="__('Phone Number')" />
            <x-text-input wire:model.blur="phone" id="phone" class="block mt-1 w-full" type="tel" name="phone" required autocomplete="tel" placeholder="(555) 123-4567" />
            <x-input-error :messages="$errors->get('phone')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input wire:model.blur="password" id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
            <x-text-input wire:model.blur="password_confirmation" id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <!-- SMS Marketing Consent -->
        <div class="mt-4">
            <label class="flex items-start gap-3 cursor-pointer">
                <input type="checkbox" wire:model.blur="sms_marketing_consent" id="sms_marketing_consent" 
                       class="mt-1 rounded border-gray-300 text-red-600 shadow-sm focus:ring-red-500" />
                <span class="text-sm text-gray-600 dark:text-gray-400">
                    {{ __('I agree to receive promotional messages, special offers, and platform updates from FAMER via SMS. Message frequency varies. Reply STOP to opt out at any time. Message and data rates may apply.') }}
                </span>
            </label>
            <x-input-error :messages="$errors->get('sms_marketing_consent')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800" href="{{ route('login') }}" wire:navigate>
                {{ __('Already registered?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>

    @include('components.social-login-buttons')
</div>
