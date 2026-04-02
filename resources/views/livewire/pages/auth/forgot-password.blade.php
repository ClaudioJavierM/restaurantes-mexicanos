<?php

use Illuminate\Support\Facades\Password;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public string $email = '';

    /**
     * Send a password reset link to the provided email address.
     */
    public function sendPasswordResetLink(): void
    {
        $this->validate([
            'email' => ['required', 'string', 'email'],
        ]);

        // We will send the password reset link to this user. Once we have attempted
        // to send the link, we will examine the response then see the message we
        // need to show to the user. Finally, we'll send out a proper response.
        $status = Password::sendResetLink(
            $this->only('email')
        );

        if ($status != Password::RESET_LINK_SENT) {
            $this->addError('email', __($status));

            return;
        }

        $this->reset('email');

        session()->flash('status', __($status));
    }
}; ?>

<div>
    {{-- Title --}}
    <h2 style="font-family:'Playfair Display',serif; font-size:1.625rem; font-weight:700; color:#F5F5F5; margin:0 0 0.75rem; text-align:center;">
        Recuperar Contraseña
    </h2>
    <p style="color:#9CA3AF; font-size:0.875rem; text-align:center; margin:0 0 1.5rem; line-height:1.6;">
        Ingresa tu email y te enviaremos un enlace para restablecer tu contraseña.
    </p>

    {{-- Status --}}
    @if (session('status'))
    <div style="background:rgba(74,222,128,0.1); border:1px solid rgba(74,222,128,0.3); border-radius:0.625rem; padding:0.75rem 1rem; margin-bottom:1.25rem;">
        <p style="color:#4ADE80; font-size:0.875rem; margin:0;">{{ session('status') }}</p>
    </div>
    @endif

    <form wire:submit="sendPasswordResetLink">
        {{-- Email --}}
        <div style="margin-bottom:1.5rem;">
            <label for="email" style="display:block; font-size:0.75rem; font-weight:600; color:#9CA3AF; margin-bottom:0.4rem; text-transform:uppercase; letter-spacing:0.06em;">Email</label>
            <input wire:model.blur="email" id="email" type="email" name="email" required autofocus
                   style="width:100%; background:#111; border:1px solid #2A2A2A; color:#F5F5F5; border-radius:0.625rem; padding:0.75rem 1rem; font-size:0.9375rem; box-sizing:border-box; outline:none; font-family:'Poppins',sans-serif;"
                   onfocus="this.style.borderColor='rgba(212,175,55,0.5)'" onblur="this.style.borderColor='#2A2A2A'">
            @error('email') <p style="color:#FCA5A5; font-size:0.8125rem; margin:0.375rem 0 0;">{{ $message }}</p> @enderror
        </div>

        {{-- Submit --}}
        <button type="submit"
                wire:loading.attr="disabled"
                style="width:100%; background:#D4AF37; color:#0B0B0B; border:none; padding:0.875rem; border-radius:0.75rem; font-weight:700; font-size:1rem; cursor:pointer; transition:background 0.2s; font-family:'Poppins',sans-serif;"
                onmouseover="this.style.background='#E8C67A'" onmouseout="this.style.background='#D4AF37'">
            <span wire:loading.remove>Enviar Enlace de Recuperación</span>
            <span wire:loading>Enviando...</span>
        </button>
    </form>

    <p style="text-align:center; margin-top:1.25rem; font-size:0.875rem; color:#6B7280;">
        <a href="{{ route('login') }}" wire:navigate style="color:#D4AF37; font-weight:600; text-decoration:none;">← Volver al inicio de sesión</a>
    </p>
</div>
