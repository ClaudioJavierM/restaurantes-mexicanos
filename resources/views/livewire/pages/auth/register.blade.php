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
    {{-- Title --}}
    <h2 style="font-family:'Playfair Display',serif; font-size:1.625rem; font-weight:700; color:#F5F5F5; margin:0 0 1.5rem; text-align:center;">
        Crear Cuenta
    </h2>

    <form wire:submit="register">
        {{-- Honeypot --}}
        <div aria-hidden="true" style="position:absolute; left:-9999px; opacity:0;">
            <input type="text" wire:model.blur="website" id="website" name="website" tabindex="-1" autocomplete="off">
        </div>
        <input type="hidden" wire:model.blur="form_loaded_at">

        {{-- Name --}}
        <div style="margin-bottom:1.125rem;">
            <label for="name" style="display:block; font-size:0.75rem; font-weight:600; color:#9CA3AF; margin-bottom:0.4rem; text-transform:uppercase; letter-spacing:0.06em;">Nombre</label>
            <input wire:model.blur="name" id="name" type="text" name="name" required autofocus autocomplete="name"
                   style="width:100%; background:#111; border:1px solid #2A2A2A; color:#F5F5F5; border-radius:0.625rem; padding:0.75rem 1rem; font-size:0.9375rem; box-sizing:border-box; outline:none; font-family:'Poppins',sans-serif;"
                   onfocus="this.style.borderColor='rgba(212,175,55,0.5)'" onblur="this.style.borderColor='#2A2A2A'">
            @error('name') <p style="color:#FCA5A5; font-size:0.8125rem; margin:0.375rem 0 0;">{{ $message }}</p> @enderror
        </div>

        {{-- Email --}}
        <div style="margin-bottom:1.125rem;">
            <label for="email" style="display:block; font-size:0.75rem; font-weight:600; color:#9CA3AF; margin-bottom:0.4rem; text-transform:uppercase; letter-spacing:0.06em;">Email</label>
            <input wire:model.blur="email" id="email" type="email" name="email" required autocomplete="username"
                   style="width:100%; background:#111; border:1px solid #2A2A2A; color:#F5F5F5; border-radius:0.625rem; padding:0.75rem 1rem; font-size:0.9375rem; box-sizing:border-box; outline:none; font-family:'Poppins',sans-serif;"
                   onfocus="this.style.borderColor='rgba(212,175,55,0.5)'" onblur="this.style.borderColor='#2A2A2A'">
            @error('email') <p style="color:#FCA5A5; font-size:0.8125rem; margin:0.375rem 0 0;">{{ $message }}</p> @enderror
        </div>

        {{-- Phone --}}
        <div style="margin-bottom:1.125rem;">
            <label for="phone" style="display:block; font-size:0.75rem; font-weight:600; color:#9CA3AF; margin-bottom:0.4rem; text-transform:uppercase; letter-spacing:0.06em;">Teléfono</label>
            <input wire:model.blur="phone" id="phone" type="tel" name="phone" required autocomplete="tel" placeholder="(555) 123-4567"
                   style="width:100%; background:#111; border:1px solid #2A2A2A; color:#F5F5F5; border-radius:0.625rem; padding:0.75rem 1rem; font-size:0.9375rem; box-sizing:border-box; outline:none; font-family:'Poppins',sans-serif;"
                   onfocus="this.style.borderColor='rgba(212,175,55,0.5)'" onblur="this.style.borderColor='#2A2A2A'">
            @error('phone') <p style="color:#FCA5A5; font-size:0.8125rem; margin:0.375rem 0 0;">{{ $message }}</p> @enderror
        </div>

        {{-- Password --}}
        <div style="margin-bottom:1.125rem;">
            <label for="password" style="display:block; font-size:0.75rem; font-weight:600; color:#9CA3AF; margin-bottom:0.4rem; text-transform:uppercase; letter-spacing:0.06em;">Contraseña</label>
            <input wire:model.blur="password" id="password" type="password" name="password" required autocomplete="new-password"
                   style="width:100%; background:#111; border:1px solid #2A2A2A; color:#F5F5F5; border-radius:0.625rem; padding:0.75rem 1rem; font-size:0.9375rem; box-sizing:border-box; outline:none; font-family:'Poppins',sans-serif;"
                   onfocus="this.style.borderColor='rgba(212,175,55,0.5)'" onblur="this.style.borderColor='#2A2A2A'">
            @error('password') <p style="color:#FCA5A5; font-size:0.8125rem; margin:0.375rem 0 0;">{{ $message }}</p> @enderror
        </div>

        {{-- Confirm Password --}}
        <div style="margin-bottom:1.25rem;">
            <label for="password_confirmation" style="display:block; font-size:0.75rem; font-weight:600; color:#9CA3AF; margin-bottom:0.4rem; text-transform:uppercase; letter-spacing:0.06em;">Confirmar Contraseña</label>
            <input wire:model.blur="password_confirmation" id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                   style="width:100%; background:#111; border:1px solid #2A2A2A; color:#F5F5F5; border-radius:0.625rem; padding:0.75rem 1rem; font-size:0.9375rem; box-sizing:border-box; outline:none; font-family:'Poppins',sans-serif;"
                   onfocus="this.style.borderColor='rgba(212,175,55,0.5)'" onblur="this.style.borderColor='#2A2A2A'">
            @error('password_confirmation') <p style="color:#FCA5A5; font-size:0.8125rem; margin:0.375rem 0 0;">{{ $message }}</p> @enderror
        </div>

        {{-- SMS consent --}}
        <div style="margin-bottom:1.5rem;">
            <label style="display:flex; align-items:flex-start; gap:0.625rem; cursor:pointer;">
                <input type="checkbox" wire:model.blur="sms_marketing_consent" id="sms_marketing_consent"
                       style="accent-color:#D4AF37; width:1rem; height:1rem; margin-top:0.2rem; flex-shrink:0; cursor:pointer;">
                <span style="font-size:0.8125rem; color:#6B7280; line-height:1.5;">
                    Acepto recibir mensajes promocionales de FAMER vía SMS. Responde STOP para cancelar. Aplican tarifas de mensajes.
                </span>
            </label>
            @error('sms_marketing_consent') <p style="color:#FCA5A5; font-size:0.8125rem; margin:0.375rem 0 0;">{{ $message }}</p> @enderror
        </div>

        {{-- Submit --}}
        <button type="submit"
                wire:loading.attr="disabled"
                style="width:100%; background:#D4AF37; color:#0B0B0B; border:none; padding:0.875rem; border-radius:0.75rem; font-weight:700; font-size:1rem; cursor:pointer; transition:background 0.2s; font-family:'Poppins',sans-serif;"
                onmouseover="this.style.background='#E8C67A'" onmouseout="this.style.background='#D4AF37'">
            <span wire:loading.remove>Crear Cuenta</span>
            <span wire:loading>Creando cuenta...</span>
        </button>
    </form>

    {{-- Social Login --}}
    @include('components.social-login-buttons')

    {{-- Login link --}}
    <p style="text-align:center; margin-top:1.25rem; font-size:0.875rem; color:#6B7280;">
        ¿Ya tienes cuenta?
        <a href="{{ route('login') }}" wire:navigate style="color:#D4AF37; font-weight:600; text-decoration:none;">Iniciar sesión</a>
    </p>
</div>
