<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Livewire\Volt\Component;

new class extends Component
{
    public string $name = '';
    public string $email = '';
    public string $phone = '';
    public bool $smsConsent = false;

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $this->name = Auth::user()->name;
        $this->email = Auth::user()->email;
        $this->phone = Auth::user()->phone ?? '';
        $this->smsConsent = (bool) Auth::user()->sms_marketing_consent;
    }

    /**
     * Update the profile information for the currently authenticated user.
     */
    public function updateProfileInformation(): void
    {
        $user = Auth::user();

        $validated = $this->validate([
            'name'       => ['required', 'string', 'max:255'],
            'email'      => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)->ignore($user->id)],
            'phone'      => ['nullable', 'string', 'max:20'],
            'smsConsent' => ['boolean'],
        ]);

        $user->fill([
            'name'  => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
        ]);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        // Handle SMS consent via model methods
        if ($validated['smsConsent'] && ! $user->sms_marketing_consent) {
            $user->optInToSmsMarketing();
        } elseif (! $validated['smsConsent'] && $user->sms_marketing_consent) {
            $user->optOutFromSmsMarketing();
        }

        $user->save();

        $this->dispatch('profile-updated', name: $user->name);
    }

    /**
     * Send an email verification notification to the current user.
     */
    public function sendVerification(): void
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard', absolute: false));

            return;
        }

        $user->sendEmailVerificationNotification();

        Session::flash('status', 'verification-link-sent');
    }
}; ?>

<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            Información de Perfil
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            Actualiza tu nombre, correo, teléfono y preferencias de contacto.
        </p>
    </header>

    {{-- Avatar (social login) --}}
    @if(auth()->user()->avatar)
        <div class="mt-4 flex items-center gap-4">
            <img src="{{ auth()->user()->avatar }}" alt="{{ auth()->user()->name }}" class="w-16 h-16 rounded-full object-cover ring-2 ring-yellow-400">
            <div>
                <p class="text-sm font-medium text-gray-700">{{ auth()->user()->name }}</p>
                <p class="text-xs text-gray-500">Foto de {{ ucfirst(auth()->user()->provider ?? 'red social') }}</p>
            </div>
        </div>
    @endif

    <form wire:submit="updateProfileInformation" class="mt-6 space-y-6">
        <div>
            <x-input-label for="name" value="Nombre completo" />
            <x-text-input wire:model="name" id="name" name="name" type="text" class="mt-1 block w-full" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" value="Correo electrónico" />
            <x-text-input wire:model="email" id="email" name="email" type="email" class="mt-1 block w-full" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if (auth()->user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! auth()->user()->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-800 dark:text-gray-200">
                        Tu correo no ha sido verificado.
                        <button wire:click.prevent="sendVerification" class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                            Reenviar correo de verificación.
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600 dark:text-green-400">
                            Se envió un nuevo enlace de verificación a tu correo.
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div>
            <x-input-label for="phone" value="Teléfono (opcional)" />
            <x-text-input wire:model="phone" id="phone" name="phone" type="tel" class="mt-1 block w-full" autocomplete="tel" placeholder="+1 (555) 000-0000" />
            <x-input-error class="mt-2" :messages="$errors->get('phone')" />
        </div>

        <div>
            <label class="flex items-start gap-3 cursor-pointer">
                <input wire:model="smsConsent" type="checkbox" id="smsConsent"
                    class="mt-0.5 h-4 w-4 text-yellow-600 border-gray-300 rounded focus:ring-yellow-500">
                <div>
                    <span class="text-sm font-medium text-gray-700">Recibir ofertas y novedades por SMS</span>
                    <p class="text-xs text-gray-500 mt-0.5">Te enviaremos promociones exclusivas de restaurantes. Puedes cancelar en cualquier momento.</p>
                </div>
            </label>
            <x-input-error class="mt-2" :messages="$errors->get('smsConsent')" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>Guardar cambios</x-primary-button>

            <x-action-message class="me-3" on="profile-updated">
                ✓ Guardado.
            </x-action-message>
        </div>
    </form>
</section>
