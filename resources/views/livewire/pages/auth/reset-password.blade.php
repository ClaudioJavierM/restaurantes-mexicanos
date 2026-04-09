<?php

use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    #[Locked]
    public string $token = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    /**
     * Mount the component.
     */
    public function mount(string $token): void
    {
        $this->token = $token;

        $this->email = request()->string('email');
    }

    /**
     * Reset the password for the given user.
     */
    public function resetPassword(): void
    {
        $this->validate([
            'token' => ['required'],
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        // Here we will attempt to reset the user's password. If it is successful we
        // will update the password on an actual user model and persist it to the
        // database. Otherwise we will parse the error and return the response.
        $status = Password::reset(
            $this->only('email', 'password', 'password_confirmation', 'token'),
            function ($user) {
                $user->forceFill([
                    'password' => Hash::make($this->password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        // If the password was successfully reset, we will redirect the user back to
        // the application's home authenticated view. If there is an error we can
        // redirect them back to where they came from with their error message.
        if ($status != Password::PASSWORD_RESET) {
            $this->addError('email', __($status));

            return;
        }

        Session::flash('status', __($status));

        $this->redirectRoute('login', navigate: true);
    }
}; ?>

<div>
    <h2 style="font-family:'Playfair Display',serif; font-size:1.625rem; font-weight:700; color:#F5F5F5; margin:0 0 0.5rem; text-align:center;">
        Nueva contraseña
    </h2>
    <p style="color:#9CA3AF; font-size:0.875rem; text-align:center; margin:0 0 1.75rem; line-height:1.6;">
        Elige una contraseña segura para tu cuenta.
    </p>

    <form wire:submit="resetPassword">

        {{-- Email (hidden, auto-filled) --}}
        <input type="hidden" wire:model="email">
        @error('email')
        <div style="background:rgba(239,68,68,0.1); border:1px solid rgba(239,68,68,0.3); border-radius:0.625rem; padding:0.75rem 1rem; margin-bottom:1.25rem;">
            <p style="color:#FCA5A5; font-size:0.875rem; margin:0;">{{ $message }}</p>
        </div>
        @enderror

        {{-- Nueva contraseña --}}
        <div style="margin-bottom:1.25rem;">
            <label for="password" style="display:block; font-size:0.75rem; font-weight:600; color:#9CA3AF; margin-bottom:0.4rem; text-transform:uppercase; letter-spacing:0.06em;">
                Nueva contraseña
            </label>
            <input wire:model="password" id="password" type="password" name="password"
                   required autocomplete="new-password"
                   style="width:100%; background:#111; border:1px solid #2A2A2A; color:#F5F5F5; border-radius:0.625rem; padding:0.75rem 1rem; font-size:0.9375rem; box-sizing:border-box; outline:none; font-family:'Poppins',sans-serif;"
                   onfocus="this.style.borderColor='rgba(212,175,55,0.5)'" onblur="this.style.borderColor='#2A2A2A'">
            @error('password')
            <p style="color:#FCA5A5; font-size:0.8125rem; margin:0.375rem 0 0;">{{ $message }}</p>
            @enderror
        </div>

        {{-- Confirmar contraseña --}}
        <div style="margin-bottom:1.75rem;">
            <label for="password_confirmation" style="display:block; font-size:0.75rem; font-weight:600; color:#9CA3AF; margin-bottom:0.4rem; text-transform:uppercase; letter-spacing:0.06em;">
                Confirmar contraseña
            </label>
            <input wire:model="password_confirmation" id="password_confirmation" type="password"
                   name="password_confirmation" required autocomplete="new-password"
                   style="width:100%; background:#111; border:1px solid #2A2A2A; color:#F5F5F5; border-radius:0.625rem; padding:0.75rem 1rem; font-size:0.9375rem; box-sizing:border-box; outline:none; font-family:'Poppins',sans-serif;"
                   onfocus="this.style.borderColor='rgba(212,175,55,0.5)'" onblur="this.style.borderColor='#2A2A2A'">
            @error('password_confirmation')
            <div style="background:rgba(239,68,68,0.1); border:1px solid rgba(239,68,68,0.3); border-radius:0.5rem; padding:0.625rem 0.875rem; margin-top:0.5rem;">
                <p style="color:#FCA5A5; font-size:0.875rem; margin:0;">⚠ {{ $message }}</p>
            </div>
            @enderror
        </div>

        {{-- Submit --}}
        <button type="submit"
                wire:loading.attr="disabled"
                style="width:100%; background:#D4AF37; color:#0B0B0B; border:none; padding:0.875rem; border-radius:0.75rem; font-weight:700; font-size:1rem; cursor:pointer; transition:background 0.2s; font-family:'Poppins',sans-serif;"
                onmouseover="this.style.background='#E8C67A'" onmouseout="this.style.background='#D4AF37'">
            <span wire:loading.remove>Guardar nueva contraseña</span>
            <span wire:loading>Guardando...</span>
        </button>
    </form>

    <p style="text-align:center; margin-top:1.25rem; font-size:0.875rem; color:#6B7280;">
        <a href="{{ route('login') }}" wire:navigate style="color:#D4AF37; font-weight:600; text-decoration:none;">← Volver al inicio de sesión</a>
    </p>
</div>
