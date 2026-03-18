<?php

namespace App\Livewire;

use App\Models\Restaurant;
use App\Models\State;
use App\Models\User;
use App\Services\StripeService;
use App\Services\TwilioService;
use Livewire\Component;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ClaimRestaurant extends Component
{
    public $search = '';
    public $selectedState = '';
    public $searchResults = [];
    public $selectedRestaurant = null;
    public $step = 'search'; // search, verify, verify_code, select_plan, payment

    // Verification fields
    public $ownerName = '';
    public $ownerEmail = '';
    public $ownerPhone = '';
    public $verificationMethod = 'email'; // email or phone
    public $verificationCode = '';
    public $codeError = '';

    // Available verification methods for the selected restaurant
    public $availableMethods = [];

    // Selected plan
    public $selectedPlan = 'free';

    // Coupon code
    public $couponCode = '';
    public $couponApplied = false;
    public $couponMessage = '';

    public function mount()
    {
        $this->searchResults = collect();

        // Track referral code from URL
        $refCode = request()->query('ref');
        if ($refCode) {
            session(['famer_referral_code' => strtoupper($refCode)]);
        }

        $searchQuery = request()->query("search");
        if ($searchQuery) {
            $this->search = $searchQuery;
            $this->searchRestaurants();
        }

        $restaurantSlug = request()->query("restaurant");
        if ($restaurantSlug) {
            $restaurant = Restaurant::where("slug", $restaurantSlug)
                ->where("status", "approved")
                ->with("state", "category", "owner")
                ->first();

            if ($restaurant) {
                $this->selectedRestaurant = $restaurant;
                $this->search = $restaurant->name;

                if ($restaurant->is_claimed) {
                    $this->step = "claimed_options";
                } else {
                    $this->detectAvailableMethods();
                    $this->step = "verify";
                }
            }
        }
    }

    /**
     * Detect which verification methods are available for the selected restaurant.
     */
    protected function detectAvailableMethods(): void
    {
        $this->availableMethods = [];

        $hasEmail = !empty($this->selectedRestaurant->email);
        $hasPhone = !empty($this->selectedRestaurant->phone);

        if ($hasEmail) {
            $this->availableMethods[] = 'email';
        }
        if ($hasPhone) {
            $this->availableMethods[] = 'phone';
        }

        // Set default method to the first available
        if (count($this->availableMethods) > 0) {
            $this->verificationMethod = $this->availableMethods[0];
        }
    }

    public function searchRestaurants()
    {
        $this->validate([
            'search' => 'required|min:3',
        ]);

        $query = Restaurant::query()
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

        $this->searchResults = $query->with('state', 'category', 'owner')
            ->orderBy('is_claimed', 'asc')
            ->limit(20)
            ->get();
    }

    public function selectRestaurant($restaurantId)
    {
        $this->selectedRestaurant = Restaurant::with('state', 'category')->find($restaurantId);

        if (!$this->selectedRestaurant) {
            session()->flash('error', 'Restaurant not found');
            return;
        }

        if ($this->selectedRestaurant->is_claimed) {
            session()->flash('error', 'This restaurant has already been claimed');
            return;
        }

        $this->detectAvailableMethods();
        $this->step = 'verify';
    }

    public function backToSearch()
    {
        $this->step = 'search';
        $this->selectedRestaurant = null;
        $this->reset(['ownerName', 'ownerEmail', 'ownerPhone', 'verificationCode', 'codeError', 'availableMethods']);
    }

    public function backToVerify()
    {
        $this->step = 'verify';
        $this->reset(['verificationCode', 'codeError']);
    }

    public function backToVerifyCode()
    {
        $this->step = 'verify_code';
    }

    public function backToSelectPlan()
    {
        $this->step = 'select_plan';
    }

    public function submitVerification()
    {
        $this->validate([
            'ownerName' => 'required|min:2',
            'ownerEmail' => 'required|email',
            'ownerPhone' => 'required|min:10',
        ]);

        $restaurantEmail = $this->selectedRestaurant->email;
        $restaurantPhone = $this->selectedRestaurant->phone;

        $hasEmail = !empty($restaurantEmail);
        $hasPhone = !empty($restaurantPhone);

        // No contact info at all
        if (!$hasEmail && !$hasPhone) {
            session()->flash('error', 'Este restaurante no tiene correo electrónico ni teléfono registrado. Por favor contacta a soporte para verificar tu identidad como propietario.');
            return;
        }

        // Generate 6-digit verification code
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Save verification data
        $this->selectedRestaurant->update([
            'owner_name' => $this->ownerName,
            'owner_email' => $this->ownerEmail,
            'owner_phone' => $this->ownerPhone,
            'verification_method' => $this->verificationMethod,
        ]);

        // Dispatch based on selected method
        if ($this->verificationMethod === 'phone' && $hasPhone) {
            // Phone call verification
            $identifier = $restaurantPhone;
            $cacheKey = 'claim_code_' . $this->selectedRestaurant->id . '_' . md5($identifier);
            Cache::put($cacheKey, $code, now()->addMinutes(15));

            $success = $this->sendVerificationCall($code, $restaurantPhone);

            if ($success) {
                $maskedPhone = $this->maskPhone($restaurantPhone);
                session()->flash('success', 'Llamada de verificación en progreso al teléfono del restaurante: ' . $maskedPhone);
            } else {
                session()->flash('error', 'No se pudo realizar la llamada de verificación. Por favor intenta de nuevo.');
                return;
            }
        } else {
            // Email verification (default)
            if (!$hasEmail) {
                session()->flash('error', 'Este restaurante no tiene un correo electrónico registrado. Selecciona verificación por teléfono.');
                return;
            }

            $identifier = $restaurantEmail;
            $cacheKey = 'claim_code_' . $this->selectedRestaurant->id . '_' . md5($identifier);
            Cache::put($cacheKey, $code, now()->addMinutes(15));

            $this->sendVerificationEmail($code, $restaurantEmail);

            $maskedEmail = $this->maskEmail($restaurantEmail);
            session()->flash('success', 'Código de verificación enviado al correo del restaurante: ' . $maskedEmail);
        }

        $this->step = 'verify_code';
    }

    /**
     * Initiate a verification phone call via Twilio.
     */
    protected function sendVerificationCall(string $code, string $phone): bool
    {
        try {
            // Generate a random token to look up the code from the TwiML endpoint
            $twimlToken = Str::random(40);

            // Store token data in cache (TwimlController will read this)
            $cacheKey = 'twiml_token_' . $twimlToken;
            Cache::put($cacheKey, [
                'code' => $code,
                'restaurant_name' => $this->selectedRestaurant->name,
            ], now()->addMinutes(15));

            // Make the call via TwilioService
            $twilioService = app(TwilioService::class);
            return $twilioService->makeVerificationCall($phone, $twimlToken);
        } catch (\Exception $e) {
            Log::error('Failed to send verification call: ' . $e->getMessage());
            return false;
        }
    }

    protected function maskEmail(string $email): string
    {
        $parts = explode('@', $email);
        $name = $parts[0];
        $domain = $parts[1] ?? '';
        $masked = substr($name, 0, 2) . str_repeat('*', max(3, strlen($name) - 2));
        return $masked . '@' . $domain;
    }

    protected function maskPhone(string $phone): string
    {
        $cleaned = preg_replace('/[^0-9]/', '', $phone);
        $lastFour = substr($cleaned, -4);
        return '(***) ***-' . $lastFour;
    }

    protected function sendVerificationEmail($code, $recipientEmail = null)
    {
        try {
            $restaurantName = $this->selectedRestaurant->name;
            $ownerName = $this->ownerName;
            $sendTo = $recipientEmail ?? $this->selectedRestaurant->email ?? $this->ownerEmail;

            Mail::send([], [], function ($message) use ($code, $restaurantName, $ownerName, $sendTo) {
                $message->to($sendTo)
                    ->subject("FAMER - Código de verificación para {$restaurantName}")
                    ->html("
                        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
                            <div style='background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%); padding: 30px; text-align: center;'>
                                <img src='https://restaurantesmexicanosfamosos.com/images/branding/logo.png' alt='FAMER' style='max-width: 200px; height: auto; margin-bottom: 10px;'>
                                <p style='color: rgba(255,255,255,0.9); margin: 10px 0 0 0;'>Restaurantes Mexicanos Famosos</p>
                            </div>
                            <div style='padding: 30px; background: #f9fafb;'>
                                <h2 style='color: #111827; margin-top: 0;'>Hola {$ownerName},</h2>
                                <p style='color: #4b5563;'>Estás reclamando el restaurante <strong>{$restaurantName}</strong> en FAMER.</p>
                                <p style='color: #4b5563;'>Tu código de verificación es:</p>
                                <div style='background: #111827; color: white; font-size: 32px; font-weight: bold; text-align: center; padding: 20px; border-radius: 8px; letter-spacing: 8px; margin: 20px 0;'>
                                    {$code}
                                </div>
                                <p style='color: #6b7280; font-size: 14px;'>Este código expira en 15 minutos.</p>
                                <p style='color: #6b7280; font-size: 14px;'>Si no solicitaste este código, ignora este mensaje.</p>
                            </div>
                            <div style='background: #111827; padding: 20px; text-align: center;'>
                                <p style='color: #9ca3af; font-size: 12px; margin: 0;'>© 2026 FAMER - restaurantesmexicanosfamosos.com</p>
                            </div>
                        </div>
                    ");
            });
        } catch (\Exception $e) {
            Log::error('Failed to send verification email: ' . $e->getMessage());
            throw $e;
        }
    }

    public function verifyCode()
    {
        $this->validate([
            'verificationCode' => 'required|digits:6',
        ]);

        // Use the correct identifier based on verification method
        if ($this->verificationMethod === 'phone') {
            $identifier = $this->selectedRestaurant->phone;
        } else {
            $identifier = $this->selectedRestaurant->email ?? $this->ownerEmail;
        }

        $cacheKey = 'claim_code_' . $this->selectedRestaurant->id . '_' . md5($identifier);
        $storedCode = Cache::get($cacheKey);

        if (!$storedCode) {
            $this->codeError = 'El código ha expirado. Por favor solicita uno nuevo.';
            return;
        }

        if ($storedCode !== $this->verificationCode) {
            $this->codeError = 'Código incorrecto. Intenta de nuevo.';
            return;
        }

        // Code is valid - clear it from cache
        Cache::forget($cacheKey);

        // Mark as verified
        $this->selectedRestaurant->update([
            'claim_token' => Str::random(32),
            'email_verified_for_claim' => true,
        ]);

        $this->step = 'select_plan';
    }

    public function resendCode()
    {
        try {
            // Generate new code
            $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

            if ($this->verificationMethod === 'phone') {
                $restaurantPhone = $this->selectedRestaurant->phone;
                $cacheKey = 'claim_code_' . $this->selectedRestaurant->id . '_' . md5($restaurantPhone);
                Cache::put($cacheKey, $code, now()->addMinutes(15));

                $success = $this->sendVerificationCall($code, $restaurantPhone);

                if ($success) {
                    $maskedPhone = $this->maskPhone($restaurantPhone);
                    session()->flash('success', 'Nueva llamada de verificación al teléfono: ' . $maskedPhone);
                } else {
                    session()->flash('error', 'No se pudo realizar la llamada. Intenta de nuevo.');
                }
            } else {
                $restaurantEmail = $this->selectedRestaurant->email ?? $this->ownerEmail;
                $cacheKey = 'claim_code_' . $this->selectedRestaurant->id . '_' . md5($restaurantEmail);
                Cache::put($cacheKey, $code, now()->addMinutes(15));

                $this->sendVerificationEmail($code, $restaurantEmail);

                $maskedEmail = $this->maskEmail($restaurantEmail);
                session()->flash('success', 'Se ha enviado un nuevo código al correo del restaurante: ' . $maskedEmail);
            }

            $this->codeError = '';
        } catch (\Exception $e) {
            Log::error('Failed to resend verification code: ' . $e->getMessage());
            session()->flash('error', 'Hubo un error al reenviar el código. Por favor intenta de nuevo.');
        }
    }

    public function selectPlan($plan)
    {
        $this->selectedPlan = $plan;

        if ($plan === 'free') {
            return $this->completeFreeClai();
        }

        $this->step = 'payment';
    }

    public function completeFreeClai()
    {
        $user = User::firstOrCreate(
            ['email' => $this->ownerEmail],
            [
                'name' => $this->ownerName,
                'password' => bcrypt(Str::random(12)),
                'phone' => $this->ownerPhone,
            ]
        );

        if ($user->role !== 'admin') {
            $user->role = 'owner';
        }
        if (!$user->email_verified_at) {
            $user->email_verified_at = now();
        }
        $user->save();

        $this->selectedRestaurant->is_claimed = true;
        $this->selectedRestaurant->claimed_at = now();
        $this->selectedRestaurant->user_id = $user->id;
        $this->selectedRestaurant->owner_name = $this->ownerName;
        $this->selectedRestaurant->owner_email = $this->ownerEmail;
        $this->selectedRestaurant->owner_phone = $this->ownerPhone;
        $this->selectedRestaurant->subscription_tier = 'claimed';
        $this->selectedRestaurant->subscription_status = 'active';
        $this->selectedRestaurant->premium_analytics = false;
        $this->selectedRestaurant->premium_seo = false;
        $this->selectedRestaurant->premium_featured = false;
        $this->selectedRestaurant->premium_coupons = false;
        $this->selectedRestaurant->premium_email_marketing = false;
        $this->selectedRestaurant->save();

        auth()->login($user);
        session()->regenerate();

        // Track referral if came from a referral link
        $referralCode = session('famer_referral_code');
        if ($referralCode) {
            $referrerRestaurant = \App\Models\Restaurant::where('referral_code', $referralCode)->first();
            if ($referrerRestaurant && $referrerRestaurant->id !== $this->selectedRestaurant->id) {
                \App\Models\RestaurantReferral::create([
                    'referrer_restaurant_id' => $referrerRestaurant->id,
                    'referred_restaurant_id' => $this->selectedRestaurant->id,
                    'referral_code' => $referralCode,
                    'referred_email' => $user->email,
                    'status' => 'claimed',
                    'claimed_at' => now(),
                ]);
            }
            session()->forget('famer_referral_code');
        }

        session()->flash('success', '¡Felicidades! Tu restaurante ha sido reclamado exitosamente.');
        return $this->redirect(route('filament.owner.pages.dashboard'), navigate: false);
    }

    public function applyCoupon()
    {
        if (empty($this->couponCode)) {
            $this->couponMessage = 'Por favor ingresa un código de cupón';
            $this->couponApplied = false;
            return;
        }

        // First check local promotion_coupons table
        $localPromo = \Illuminate\Support\Facades\DB::table('promotion_coupons')
            ->where('code', strtoupper(trim($this->couponCode)))
            ->where('is_active', true)
            ->first();

        if ($localPromo) {
            // Check expiration
            if ($localPromo->expires_at && now()->greaterThan($localPromo->expires_at)) {
                $this->couponApplied = false;
                $this->couponMessage = 'Este cupón ha expirado';
                return;
            }
            // Check max redemptions
            if ($localPromo->max_redemptions && $localPromo->times_redeemed >= $localPromo->max_redemptions) {
                $this->couponApplied = false;
                $this->couponMessage = 'Este cupón ha alcanzado su límite de usos';
                return;
            }

            $this->couponApplied = true;
            if ($localPromo->discount_type === 'percentage') {
                $this->couponMessage = "¡Cupón aplicado! {$localPromo->discount_value}% de descuento por {$localPromo->duration_in_months} meses";
            } else {
                $this->couponMessage = "¡Cupón aplicado! \${$localPromo->discount_value} de descuento";
            }
            return;
        }

        // Fallback to Stripe validation
        $stripeService = new StripeService();
        $promotionCode = $stripeService->validatePromotionCode($this->couponCode);

        if ($promotionCode) {
            $this->couponApplied = true;
            $promoArr = $promotionCode->toArray();
            $couponId = $promoArr['promotion']['coupon'] ?? null;
            if (!$couponId && isset($promoArr['coupon'])) {
                $couponId = is_string($promoArr['coupon']) ? $promoArr['coupon'] : ($promoArr['coupon']['id'] ?? null);
            }
            $coupon = $couponId ? \Stripe\Coupon::retrieve($couponId) : null;

            if ($coupon && $coupon->percent_off) {
                $this->couponMessage = "¡Cupón aplicado! {$coupon->percent_off}% de descuento";
            } elseif ($coupon && $coupon->amount_off) {
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
            $couponCodeForStripe = null;
            $stripePromoId = null;

            // If a local promo coupon is applied, ensure it exists in Stripe
            if ($this->couponApplied) {
                $localPromo = \Illuminate\Support\Facades\DB::table('promotion_coupons')
                    ->where('code', strtoupper(trim($this->couponCode)))
                    ->where('is_active', true)
                    ->first();

                if ($localPromo) {
                    if (empty($localPromo->stripe_promotion_code_id)) {
                        // Create coupon in Stripe
                        $stripeCoupon = $stripeService->createCoupon([
                            'percent_off' => $localPromo->discount_type === 'percentage' ? $localPromo->discount_value : null,
                            'amount_off' => $localPromo->discount_type === 'fixed' ? $localPromo->discount_value * 100 : null,
                            'duration' => $localPromo->duration ?? 'repeating',
                            'duration_in_months' => $localPromo->duration_in_months,
                            'name' => $localPromo->name,
                        ]);

                        // Create promotion code in Stripe
                        $stripePromo = $stripeService->createPromotionCode($stripeCoupon->id, $localPromo->code, [
                            'max_redemptions' => $localPromo->max_redemptions,
                            'expires_at' => $localPromo->expires_at ? strtotime($localPromo->expires_at) : null,
                        ]);

                        $stripePromoId = $stripePromo->id;

                        // Save Stripe IDs back to local DB
                        \Illuminate\Support\Facades\DB::table('promotion_coupons')
                            ->where('id', $localPromo->id)
                            ->update([
                                'stripe_coupon_id' => $stripeCoupon->id,
                                'stripe_promotion_code_id' => $stripePromo->id,
                            ]);
                    } else {
                        $stripePromoId = $localPromo->stripe_promotion_code_id;
                    }
                } else {
                    // Not a local promo, pass code for Stripe lookup
                    $couponCodeForStripe = $this->couponCode;
                }
            }

            $session = $stripeService->createCheckoutSession(
                $this->selectedRestaurant,
                $this->selectedPlan,
                route('claim.success') . '?session_id={CHECKOUT_SESSION_ID}',
                route('claim.cancel'),
                $couponCodeForStripe,
                $stripePromoId
            );

            return redirect($session->url);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('processPayment error: ' . $e->getMessage(), [
                'restaurant' => $this->selectedRestaurant?->id,
                'plan' => $this->selectedPlan,
                'coupon' => $this->couponCode,
                'trace' => $e->getTraceAsString(),
            ]);
            session()->flash('error', 'Error al procesar el pago: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $states = State::where('is_active', true)->orderBy('name')->get();

        return view('livewire.claim-restaurant', [
            'states' => $states,
            'stripeKey' => config('stripe.key'),
        ])->layout('layouts.app', ['title' => 'Claim Your Restaurant']);
    }
}
