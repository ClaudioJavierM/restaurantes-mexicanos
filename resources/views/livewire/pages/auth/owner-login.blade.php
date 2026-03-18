<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest-owner')] class extends Component
{
    public LoginForm $form;

    public function login(): void
    {
        $this->validate();

        $this->form->authenticate();

        Session::regenerate();

        $user = auth()->user();

        $canAccess = in_array($user->role, ['owner', 'admin'])
            || $user->teamMemberships()->where('status', 'active')->exists();

        if (! $canAccess) {
            auth()->logout();
            Session::invalidate();
            $this->addError('form.email', 'Esta cuenta no tiene acceso al panel de negocios. Si eres dueño, primero registra o reclama tu restaurante.');
            return;
        }

        $this->redirect('/owner', navigate: false);
    }
}; ?>

<div>
    <div class="mb-5 text-center">
        <h2 class="text-xl font-bold text-gray-900">Panel de Negocios</h2>
        <p class="text-sm text-gray-500 mt-1">Administra tu restaurante, menú, reseñas y más</p>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    @if (session('error'))
        <div class="mb-4 p-4 rounded-md bg-red-50 border border-red-200">
            <p class="text-sm text-red-600">{{ session('error') }}</p>
        </div>
    @endif

    <form wire:submit="login">
        <div>
            <x-input-label for="email" value="Correo electrónico" />
            <x-text-input wire:model="form.email" id="email" class="block mt-1 w-full" type="email" name="email" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('form.email')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password" value="Contraseña" />
            <x-text-input wire:model="form.password" id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('form.password')" class="mt-2" />
        </div>

        <div class="block mt-4">
            <label for="remember" class="inline-flex items-center">
                <input wire:model="form.remember" id="remember" type="checkbox" class="rounded border-gray-300 text-amber-600 shadow-sm focus:ring-amber-500" name="remember">
                <span class="ms-2 text-sm text-gray-600">Recordarme</span>
            </label>
        </div>

        <div class="flex items-center justify-end mt-4">
            @if (Route::has('password.request'))
                <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500" href="{{ route('password.request') }}" wire:navigate>
                    ¿Olvidaste tu contraseña?
                </a>
            @endif

            <button type="button" wire:click="login" class="ms-3 inline-flex items-center px-4 py-2 bg-amber-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-amber-700 focus:bg-amber-700 active:bg-amber-900 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2 transition ease-in-out duration-150">
                Entrar al panel
            </button>
        </div>
    </form>

    <!-- Customer CTA -->
    <div class="mt-6 pt-5 border-t border-gray-100 text-center">
        <p class="text-sm text-gray-500">¿Buscas restaurantes?</p>
        <a href="/login" class="mt-1 inline-flex items-center gap-1.5 text-sm font-semibold text-red-600 hover:text-red-800 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            Accede como cliente →
        </a>
    </div>
</div>
