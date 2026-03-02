<?php

namespace App\Livewire;

use App\Models\Restaurant;
use App\Models\State;
use App\Models\ClaimVerification;
use App\Services\StripeService;
use App\Notifications\ClaimVerificationCode;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Notification;

class ClaimRestaurantImproved extends Component
{
    use WithFileUploads;

    public $search = '';
    public $selectedState = '';
    public $searchResults = [];
    public $selectedRestaurant = null;
    public $step = 'search'; // search, verify, enter_code, select_plan, payment, success

    // Verification fields
    public $ownerName = '';
    public $ownerEmail = '';
    public $ownerPhone = '';
    public $verificationMethod = 'email'; // email, phone, document
    public $document = null;

    // Verification code
    public $verificationCode = '';
    public $currentVerification = null;

    // Selected plan
    public $selectedPlan = 'free'; // free, premium, elite

    // Coupon code
    public $couponCode = '';
    public $couponApplied = false;
    public $couponMessage = '';

    // Error/Success messages
    public $errorMessage = '';
    public $successMessage = '';

    public function mount()
    {
        $this->searchResults = collect();
    }

    public function searchRestaurants()
    {
        $this->validate([
            'search' => 'required|min:3',
        ]);

        $query = Restaurant::query()
            ->where('is_claimed', false)
            ->where('status', 'approved');

        if ($this->search) {
            $query->where(function($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('address', 'like', '%' . $this->search . '%')
                  ->orWhere('city', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->selectedState) {
            $query->where('state_id', $this->selectedState);
        }

        $this->searchResults = $query->with('state', 'category')
            ->limit(20)
            ->get();

        if ($this->searchResults->isEmpty()) {
            $this->errorMessage = 'No se encontraron restaurantes disponibles para reclamar.';
        }
    }

    public function selectRestaurant($restaurantId)
    {
        $this->selectedRestaurant = Restaurant::with('state', 'category')->find($restaurantId);

        if (!$this->selectedRestaurant) {
            $this->errorMessage = 'Restaurante no encontrado';
            return;
        }

        if ($this->selectedRestaurant->is_claimed) {
            $this->errorMessage = 'Este restaurante ya ha sido reclamado';
            return;
        }

        // Pre-fill owner email if restaurant has one
        if ($this->selectedRestaurant->email) {
            $this->ownerEmail = $this->selectedRestaurant->email;
        }

        // Pre-fill owner phone if restaurant has one
        if ($this->selectedRestaurant->phone) {
            $this->ownerPhone = $this->selectedRestaurant->phone;
        }

        $this->step = 'verify';
        $this->errorMessage = '';
    }

    public function backToSearch()
    {
        $this->step = 'search';
        $this->selectedRestaurant = null;
        $this->reset(['ownerName', 'ownerEmail', 'ownerPhone', 'errorMessage', 'successMessage']);
    }

    public function submitVerification()
    {
        $this->validate([
            'ownerName' => 'required|min:2',
            'ownerEmail' => 'required|email',
            'ownerPhone' => 'required|min:10',
        ]);

        try {
            // Create verification request
            $verification = ClaimVerification::createVerification(
                $this->selectedRestaurant,
                $this->ownerName,
                $this->ownerEmail,
                $this->ownerPhone,
                $this->verificationMethod
            );

            $this->currentVerification = $verification;

            // Send verification code to the RESTAURANT's email (not the claimant's)
            if ($this->verificationMethod === 'email') {
                $restaurantEmail = $this->selectedRestaurant->email;

                if (empty($restaurantEmail)) {
                    $this->errorMessage = 'Este restaurante no tiene un correo electrónico registrado. Por favor contacta a soporte.';
                    return;
                }

                Notification::route('mail', $restaurantEmail)
                    ->notify(new ClaimVerificationCode($verification));

                // Mask email for display
                $parts = explode('@', $restaurantEmail);
                $masked = substr($parts[0], 0, 2) . str_repeat('*', max(3, strlen($parts[0]) - 2)) . '@' . ($parts[1] ?? '');
                $this->successMessage = '¡Código de verificación enviado al correo del restaurante: ' . $masked . '!';
            }

            // TODO: Implement phone verification (SMS)
            if ($this->verificationMethod === 'phone') {
                $this->successMessage = '¡Código de verificación enviado por SMS a ' . $this->ownerPhone . '!';
            }

            $this->step = 'enter_code';
            $this->errorMessage = '';

        } catch (\Exception $e) {
            $this->errorMessage = 'Error al enviar el código de verificación: ' . $e->getMessage();
        }
    }

    public function verifyCode()
    {
        $this->validate([
            'verificationCode' => 'required|size:6|numeric',
        ]);

        if (!$this->currentVerification) {
            $this->errorMessage = 'Sesión de verificación no encontrada. Por favor intenta de nuevo.';
            return;
        }

        $verified = $this->currentVerification->verifyCode($this->verificationCode);

        if ($verified) {
            $this->successMessage = '¡Código verificado exitosamente!';
            $this->step = 'select_plan';
            $this->errorMessage = '';
        } else {
            $remaining = $this->currentVerification->remainingAttempts();

            if ($remaining > 0) {
                $this->errorMessage = 'Código incorrecto. Te quedan ' . $remaining . ' intentos.';
            } else {
                $this->errorMessage = 'Has excedido el número máximo de intentos. Por favor solicita un nuevo código.';
                $this->step = 'verify';
            }
        }
    }

    public function resendCode()
    {
        if (!$this->currentVerification) {
            $this->errorMessage = 'Sesión de verificación no encontrada.';
            return;
        }

        $resent = $this->currentVerification->resendCode();

        if ($resent) {
            // Send new code to the RESTAURANT's email
            $restaurantEmail = $this->selectedRestaurant->email ?? $this->ownerEmail;
            Notification::route('mail', $restaurantEmail)
                ->notify(new ClaimVerificationCode($this->currentVerification->fresh()));

            $parts = explode('@', $restaurantEmail);
            $masked = substr($parts[0], 0, 2) . str_repeat('*', max(3, strlen($parts[0]) - 2)) . '@' . ($parts[1] ?? '');
            $this->successMessage = '¡Nuevo código enviado al correo del restaurante: ' . $masked . '!';
            $this->errorMessage = '';
        } else {
            $this->errorMessage = 'No se pudo reenviar el código. Por favor intenta de nuevo más tarde.';
        }
    }

    public function selectPlan($plan)
    {
        $this->selectedPlan = $plan;

        if ($plan === 'free') {
            // For free plan, complete claim immediately
            $this->completeClaim();
        } else {
            // For paid plans, go to payment
            $this->step = 'payment';
        }
    }

    public function completeClaim()
    {
        try {
            // Approve the claim
            $this->currentVerification->approve();

            $this->selectedRestaurant->update([
                'subscription_tier' => $this->selectedPlan,
                'subscription_status' => 'active',
                'subscription_started_at' => now(),
            ]);

            $this->step = 'success';
            $this->successMessage = '¡Felicidades! Has reclamado exitosamente ' . $this->selectedRestaurant->name;

            // TODO: Send welcome email with login credentials

        } catch (\Exception $e) {
            $this->errorMessage = 'Error al completar la reclamación: ' . $e->getMessage();
        }
    }

    public function applyCoupon()
    {
        if (empty($this->couponCode)) {
            $this->couponMessage = 'Por favor ingresa un código de cupón';
            $this->couponApplied = false;
            return;
        }

        $stripeService = new StripeService();
        $promotionCode = $stripeService->validatePromotionCode($this->couponCode);

        if ($promotionCode) {
            $this->couponApplied = true;
            $coupon = $promotionCode->coupon;

            if ($coupon->percent_off) {
                $this->couponMessage = "¡Cupón aplicado! {$coupon->percent_off}% de descuento";
            } elseif ($coupon->amount_off) {
                $amount = $coupon->amount_off / 100;
                $this->couponMessage = "¡Cupón aplicado! \${$amount} de descuento";
            }
        } else {
            $this->couponApplied = false;
            $this->couponMessage = 'Código de cupón inválido o expirado';
        }
    }

    public function processPayment()
    {
        try {
            $stripeService = new StripeService();

            // Create Stripe Checkout Session
            $session = $stripeService->createCheckoutSession(
                $this->selectedRestaurant,
                $this->selectedPlan,
                route('claim.success') . '?session_id={CHECKOUT_SESSION_ID}',
                route('claim.cancel'),
                $this->couponApplied ? $this->couponCode : null
            );

            return redirect($session->url);
        } catch (\Exception $e) {
            $this->errorMessage = 'Error al procesar el pago: ' . $e->getMessage();
        }
    }

    public function render()
    {
        $states = State::where('is_active', true)->orderBy('name')->get();

        return view('livewire.claim-restaurant-improved', [
            'states' => $states,
        ])->layout('layouts.app', ['title' => 'Reclama Tu Restaurante']);
    }
}
