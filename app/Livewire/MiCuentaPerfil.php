<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Validation\Rule;

class MiCuentaPerfil extends Component
{
    public string $name = '';
    public string $email = '';
    public string $phone = '';

    public bool $saved = false;

    public function mount(): void
    {
        $user = auth()->user();
        $this->name = $user->name;
        $this->email = $user->email;
        $this->phone = $user->phone ?? '';
    }

    public function save(): void
    {
        $user = auth()->user();

        $this->validate([
            'name'  => ['required', 'string', 'min:2', 'max:100'],
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:20'],
        ]);

        $user->update([
            'name'  => $this->name,
            'email' => $this->email,
            'phone' => $this->phone ?: null,
        ]);

        $this->saved = true;
        $this->dispatch('profile-saved');
    }

    public function render()
    {
        return view('livewire.mi-cuenta-perfil')
            ->layout('layouts.app', ['title' => 'Mi Perfil — FAMER']);
    }
}
