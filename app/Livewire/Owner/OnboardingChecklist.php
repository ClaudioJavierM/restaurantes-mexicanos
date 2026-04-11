<?php

namespace App\Livewire\Owner;

use App\Models\Restaurant;
use Livewire\Component;

class OnboardingChecklist extends Component
{
    public Restaurant $restaurant;

    public function mount(Restaurant $restaurant): void
    {
        $this->restaurant = $restaurant;
    }

    public function getTasksProperty(): array
    {
        return [
            [
                'key'    => 'description',
                'label'  => 'Agrega una descripción de tu restaurante',
                'done'   => !empty($this->restaurant->description) && strlen($this->restaurant->description) > 50,
                'action' => 'Completar',
            ],
            [
                'key'    => 'phone',
                'label'  => 'Verifica tu número de teléfono',
                'done'   => !empty($this->restaurant->phone),
                'action' => 'Agregar',
            ],
            [
                'key'    => 'hours',
                'label'  => 'Configura tus horarios de atención',
                'done'   => !empty($this->restaurant->hours) || !empty($this->restaurant->business_hours),
                'action' => 'Configurar',
            ],
            [
                'key'    => 'website',
                'label'  => 'Agrega tu sitio web',
                'done'   => !empty($this->restaurant->website),
                'action' => 'Agregar',
            ],
            [
                'key'    => 'photo',
                'label'  => 'Sube al menos una foto',
                'done'   => !empty($this->restaurant->image_url) || !empty($this->restaurant->photo_url),
                'action' => 'Subir foto',
            ],
        ];
    }

    public function getCompletionPercentageProperty(): int
    {
        $done = count(array_filter($this->tasks, fn ($t) => $t['done']));
        return (int) round(($done / count($this->tasks)) * 100);
    }

    public function getIsDismissedProperty(): bool
    {
        return !is_null($this->restaurant->onboarding_dismissed_at);
    }

    public function dismiss(): void
    {
        $this->restaurant->update(['onboarding_dismissed_at' => now()]);
    }

    public function render()
    {
        return view('livewire.owner.onboarding-checklist');
    }
}
