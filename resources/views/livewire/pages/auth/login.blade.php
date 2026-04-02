<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public LoginForm $form;

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();

        $this->form->authenticate();

        Session::regenerate();

        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div>
    {{-- Title --}}
    <h2 style="font-family:'Playfair Display',serif; font-size:1.625rem; font-weight:700; color:#F5F5F5; margin:0 0 1.5rem; text-align:center;">
        Iniciar Sesión
    </h2>

    {{-- Status --}}
    @if (session('status'))
    <div style="background:rgba(74,222,128,0.1); border:1px solid rgba(74,222,128,0.3); border-radius:0.625rem; padding:0.75rem 1rem; margin-bottom:1.25rem;">
        <p style="color:#4ADE80; font-size:0.875rem; margin:0;">{{ session('status') }}</p>
    </div>
    @endif
    @if (session('error'))
    <div style="background:rgba(239,68,68,0.1); border:1px solid rgba(239,68,68,0.3); border-radius:0.625rem; padding:0.75rem 1rem; margin-bottom:1.25rem;">
        <p style="color:#FCA5A5; font-size:0.875rem; margin:0;">{{ session('error') }}</p>
    </div>
    @endif

    <form wire:submit="login">
        {{-- Email --}}
        <div style="margin-bottom:1.125rem;">
            <label for="email" style="display:block; font-size:0.75rem; font-weight:600; color:#9CA3AF; margin-bottom:0.4rem; text-transform:uppercase; letter-spacing:0.06em;">Email</label>
            <input wire:model="form.email" id="email" type="email" name="email" required autofocus autocomplete="username"
                   style="width:100%; background:#111; border:1px solid #2A2A2A; color:#F5F5F5; border-radius:0.625rem; padding:0.75rem 1rem; font-size:0.9375rem; box-sizing:border-box; outline:none; font-family:'Poppins',sans-serif;"
                   onfocus="this.style.borderColor='rgba(212,175,55,0.5)'" onblur="this.style.borderColor='#2A2A2A'">
            @error('form.email') <p style="color:#FCA5A5; font-size:0.8125rem; margin:0.375rem 0 0;">{{ $message }}</p> @enderror
        </div>

        {{-- Password --}}
        <div style="margin-bottom:1.125rem;">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:0.4rem;">
                <label for="password" style="font-size:0.75rem; font-weight:600; color:#9CA3AF; text-transform:uppercase; letter-spacing:0.06em;">Contraseña</label>
                @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" wire:navigate style="font-size:0.75rem; color:#D4AF37; text-decoration:none; font-weight:500;">¿Olvidaste tu contraseña?</a>
                @endif
            </div>
            <input wire:model="form.password" id="password" type="password" name="password" required autocomplete="current-password"
                   style="width:100%; background:#111; border:1px solid #2A2A2A; color:#F5F5F5; border-radius:0.625rem; padding:0.75rem 1rem; font-size:0.9375rem; box-sizing:border-box; outline:none; font-family:'Poppins',sans-serif;"
                   onfocus="this.style.borderColor='rgba(212,175,55,0.5)'" onblur="this.style.borderColor='#2A2A2A'">
            @error('form.password') <p style="color:#FCA5A5; font-size:0.8125rem; margin:0.375rem 0 0;">{{ $message }}</p> @enderror
        </div>

        {{-- Remember me --}}
        <div style="margin-bottom:1.5rem;">
            <label style="display:inline-flex; align-items:center; gap:0.5rem; cursor:pointer;">
                <input wire:model="form.remember" id="remember" type="checkbox" name="remember" style="accent-color:#D4AF37; width:1rem; height:1rem; cursor:pointer;">
                <span style="font-size:0.875rem; color:#9CA3AF;">Recordarme</span>
            </label>
        </div>

        {{-- Submit --}}
        <button type="submit"
                wire:loading.attr="disabled"
                style="width:100%; background:#D4AF37; color:#0B0B0B; border:none; padding:0.875rem; border-radius:0.75rem; font-weight:700; font-size:1rem; cursor:pointer; transition:background 0.2s; font-family:'Poppins',sans-serif;"
                onmouseover="this.style.background='#E8C67A'" onmouseout="this.style.background='#D4AF37'">
            <span wire:loading.remove>Iniciar Sesión</span>
            <span wire:loading>Entrando...</span>
        </button>
    </form>

    {{-- Social Login --}}
    @include('components.social-login-buttons')

    {{-- Register link --}}
    <p style="text-align:center; margin-top:1.25rem; font-size:0.875rem; color:#6B7280;">
        ¿No tienes cuenta?
        <a href="{{ route('register') }}" wire:navigate style="color:#D4AF37; font-weight:600; text-decoration:none;">Crear cuenta gratis</a>
    </p>
</div>
