<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public LoginForm $form;

    public function login(): void
    {
        $this->validate();

        $this->form->authenticate();

        Session::regenerate();

        $user = auth()->user();

        if ($user->role === 'admin') {
            $this->redirect('/admin', navigate: false);
        } elseif (
            in_array($user->role, ['owner', 'admin']) &&
            ($user->restaurants()->exists() || $user->teamMemberships()->where('status', 'active')->exists())
        ) {
            $this->redirect('/owner', navigate: false);
        } else {
            $this->redirect(route('dashboard', absolute: false), navigate: false);
        }
    }
}; ?>

<div>
    <div class="mb-5 text-center">
        <h2 class="text-xl font-bold text-gray-900">Bienvenido de vuelta</h2>
        <p class="text-sm text-gray-500 mt-1">Accede para ver tus favoritos, reservaciones y más</p>
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
                <input wire:model="form.remember" id="remember" type="checkbox" class="rounded border-gray-300 text-red-600 shadow-sm focus:ring-red-500" name="remember">
                <span class="ms-2 text-sm text-gray-600">Recordarme</span>
            </label>
        </div>

        <div class="flex items-center justify-end mt-4">
            @if (Route::has('password.request'))
                <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500" href="{{ route('password.request') }}" wire:navigate>
                    ¿Olvidaste tu contraseña?
                </a>
            @endif

            <x-primary-button class="ms-3">
                Entrar
            </x-primary-button>
        </div>
    </form>

    <!-- Owner CTA -->
    <div class="mt-6 pt-5 border-t border-gray-100 text-center">
        <p class="text-sm text-gray-500">¿Eres dueño de un restaurante?</p>
        <a href="/owner/login" class="mt-1 inline-flex items-center gap-1.5 text-sm font-semibold text-amber-700 hover:text-amber-900 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
            </svg>
            Accede al panel de negocios →
        </a>
    </div>
</div>
