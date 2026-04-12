<?php
namespace App\Livewire\Owner;

use App\Models\Restaurant;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class OnboardingTour extends Component
{
    public ?Restaurant $restaurant = null;
    public int $currentStep = 0;
    public bool $isVisible = false;

    // 5 pasos del tutorial
    public array $steps = [
        1 => [
            'target'  => '#onboarding-profile',
            'title'   => 'Completa tu perfil',
            'body'    => 'Agrega fotos, horarios y descripción. Los restaurantes con perfil completo reciben 4x más clics.',
            'icon'    => '📋',
            'cta'     => 'Ir a Mi Perfil →',
            'cta_url' => '/owner/profile',
        ],
        2 => [
            'target'  => '#onboarding-menu',
            'title'   => 'Publica tu menú',
            'body'    => 'Los clientes buscan el menú antes de visitar. Sube platos, fotos y precios fácilmente.',
            'icon'    => '🍽️',
            'cta'     => 'Agregar Menú →',
            'cta_url' => '/owner/menu',
        ],
        3 => [
            'target'  => '#onboarding-reviews',
            'title'   => 'Responde reseñas',
            'body'    => 'Responder reseñas aumenta tu FAMER Score y mejora tu posición en búsquedas.',
            'icon'    => '⭐',
            'cta'     => 'Ver Reseñas →',
            'cta_url' => '/owner/reviews',
        ],
        4 => [
            'target'  => '#onboarding-analytics',
            'title'   => 'Monitorea tus estadísticas',
            'body'    => 'Ve cuántas personas te encuentran, desde dónde buscan y qué páginas visitan.',
            'icon'    => '📊',
            'cta'     => 'Ver Estadísticas →',
            'cta_url' => '/owner/analytics',
        ],
        5 => [
            'target'  => '#onboarding-upgrade',
            'title'   => '¡Listo para brillar!',
            'body'    => 'Tu restaurante ya está activo en FAMER. Considera Premium para aparecer en el Top 3 de búsquedas en tu ciudad.',
            'icon'    => '🏆',
            'cta'     => 'Ver planes →',
            'cta_url' => '/claim?restaurant=', // placeholder, filled in mount
        ],
    ];

    public function mount(): void
    {
        $user = Auth::user();
        if (!$user) {
            return;
        }

        $this->restaurant = Restaurant::where('user_id', $user->id)->first();

        if (!$this->restaurant) {
            return;
        }

        // Don't show if already completed
        if ($this->restaurant->onboarding_completed_at) {
            return;
        }

        $this->currentStep = max(1, ($this->restaurant->onboarding_step ?? 0) + 1);

        if ($this->currentStep <= 5) {
            $this->isVisible = true;
        }

        // Fill restaurant slug in step 5 CTA
        $this->steps[5]['cta_url'] = '/claim?restaurant=' . ($this->restaurant->slug ?? '');
    }

    public function nextStep(): void
    {
        if (!$this->restaurant) return;

        // Mark current step complete
        $this->restaurant->update(['onboarding_step' => $this->currentStep]);

        if ($this->currentStep >= 5) {
            $this->completeOnboarding();
            return;
        }

        $this->currentStep++;
    }

    public function skipOnboarding(): void
    {
        $this->completeOnboarding();
    }

    private function completeOnboarding(): void
    {
        if ($this->restaurant) {
            $this->restaurant->update([
                'onboarding_step'         => 5,
                'onboarding_completed_at' => now(),
            ]);
        }
        $this->isVisible = false;
    }

    public function render()
    {
        return view('livewire.owner.onboarding-tour');
    }
}
