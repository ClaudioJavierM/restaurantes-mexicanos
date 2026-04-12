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
    public $step = 'search'; // search, verify, verify_code, select_role, create_account, select_plan

    // Owner role (selected in select_role step)
    public string $ownerRole = '';

    // Verification fields
    public $ownerName = '';
    public $ownerEmail = '';
    public $ownerPhone = '';
    public string $password = '';
    public string $passwordConfirmation = '';
    public bool $emailConsent = true;
    public bool $smsConsent = true;
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

    // Promo banner
    public bool $promoActive = false;
    public string $promoCode = '';
    public string $promoLabel = '';
    public string $promoMessage = '';

    // Stripe embedded payment
    public ?string $stripeClientSecret = null;

    // Restaurant stats for plan selection modal
    public int $restaurantMonthlyViews = 0;
    public int $restaurantTotalViews = 0;
    public int $competitorCount = 0;
    public int $socialProofCount = 0;

    public function mount()
    {
        $promo = config('famer-promo');
        if ($promo['active'] && !empty($promo['code'])) {
            // Check expiry
            $expired = $promo['expires_at'] && now()->isAfter($promo['expires_at']);
            if (!$expired) {
                $this->promoActive = true;
                $this->promoCode   = $promo['code'];
                $this->promoLabel  = $promo['label'];
                $this->promoMessage = $promo['message'];
                // Auto-fill coupon if not already set by user
                if (empty($this->couponCode)) {
                    $this->couponCode = $promo['code'];
                }
            }
        }

        $this->searchResults = collect();

        // If user is already logged in and has a claimed restaurant, skip straight to plan selection
        if (auth()->check()) {
            $user = auth()->user();
            $restaurant = \App\Models\Restaurant::where('user_id', $user->id)
                ->where('status', 'approved')
                ->with('state', 'category', 'owner')
                ->first();

            if ($restaurant) {
                $this->selectedRestaurant = $restaurant;
                $this->search = $restaurant->name;

                // Load stats for the plan modal
                try {
                    $this->restaurantMonthlyViews = \App\Models\AnalyticsEvent::where('restaurant_id', $restaurant->id)
                        ->where('event_type', \App\Models\AnalyticsEvent::EVENT_PAGE_VIEW)
                        ->where('created_at', '>=', now()->startOfMonth())
                        ->count();
                    $this->restaurantTotalViews = \App\Models\AnalyticsEvent::where('restaurant_id', $restaurant->id)
                        ->where('event_type', \App\Models\AnalyticsEvent::EVENT_PAGE_VIEW)
                        ->count();
                    $this->competitorCount = \App\Models\Restaurant::where('state_id', $restaurant->state_id)
                        ->where('status', 'approved')
                        ->where('id', '!=', $restaurant->id)
                        ->count();
                } catch (\Exception $e) {}

                $this->step = 'select_plan';
                $this->dispatch('scroll-top');
                return;
            }
        }

        // Track claim page view
        try {
            \App\Models\AnalyticsEvent::create([
                'restaurant_id' => null,
                'event_type' => 'claim_page_view',
                'user_type' => 'owner',
                'ip_address' => request()->ip(),
                'referrer' => request()->header('referer'),
                'user_agent' => request()->userAgent(),
                'session_id' => session()->getId(),
                'metadata' => ['url' => request()->fullUrl()],
            ]);
        } catch (\Exception $e) {
            Log::warning('Failed to track claim_page_view: ' . $e->getMessage());
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
                    $this->dispatch('scroll-top');
                } else {
                    // Track claim start for abandoned recovery
                    if (!$restaurant->user_id) {
                        try {
                            $restaurant->update(['claim_started_at' => now()]);
                        } catch (\Exception $e) {
                            Log::warning('Failed to update claim_started_at: ' . $e->getMessage());
                        }
                    }

                    // Social proof: premium/elite restaurants in same state
                    try {
                        $this->socialProofCount = \App\Models\Restaurant::where('state_id', $restaurant->state_id)
                            ->whereIn('subscription_tier', ['premium', 'elite'])
                            ->where('id', '!=', $restaurant->id)
                            ->count();
                    } catch (\Exception $e) {}

                    $this->detectAvailableMethods();
                    $this->step = "verify";
                    $this->dispatch('scroll-top');
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

        // Track claim search
        try {
            \App\Models\AnalyticsEvent::create([
                'restaurant_id' => null,
                'event_type' => 'claim_search',
                'user_type' => 'owner',
                'ip_address' => request()->ip(),
                'referrer' => request()->header('referer'),
                'user_agent' => request()->userAgent(),
                'session_id' => session()->getId(),
                'metadata' => ['query' => $this->search, 'state' => $this->selectedState],
            ]);
        } catch (\Exception $e) {
            Log::warning('Failed to track claim_search: ' . $e->getMessage());
        }

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

        // If the logged-in user already owns this restaurant, skip to plan selection
        if (auth()->check() && $this->selectedRestaurant->user_id === auth()->id()) {
            try {
                $this->restaurantMonthlyViews = \App\Models\AnalyticsEvent::where('restaurant_id', $this->selectedRestaurant->id)
                    ->where('event_type', \App\Models\AnalyticsEvent::EVENT_PAGE_VIEW)
                    ->where('created_at', '>=', now()->startOfMonth())
                    ->count();
                $this->restaurantTotalViews = \App\Models\AnalyticsEvent::where('restaurant_id', $this->selectedRestaurant->id)
                    ->where('event_type', \App\Models\AnalyticsEvent::EVENT_PAGE_VIEW)
                    ->count();
                $this->competitorCount = \App\Models\Restaurant::where('state_id', $this->selectedRestaurant->state_id)
                    ->where('status', 'approved')
                    ->where('id', '!=', $this->selectedRestaurant->id)
                    ->count();
            } catch (\Exception $e) {}

            $this->step = 'select_plan';
            $this->dispatch('scroll-top');
            return;
        }

        if ($this->selectedRestaurant->is_claimed) {
            session()->flash('error', 'This restaurant has already been claimed');
            return;
        }

        // Track restaurant selected
        try {
            \App\Models\AnalyticsEvent::create([
                'restaurant_id' => $this->selectedRestaurant->id,
                'event_type' => 'claim_restaurant_selected',
                'user_type' => 'owner',
                'ip_address' => request()->ip(),
                'referrer' => request()->header('referer'),
                'user_agent' => request()->userAgent(),
                'session_id' => session()->getId(),
                'metadata' => [
                    'restaurant_name' => $this->selectedRestaurant->name,
                    'restaurant_slug' => $this->selectedRestaurant->slug,
                    'search_query' => $this->search,
                ],
            ]);
        } catch (\Exception $e) {
            Log::warning('Failed to track claim_restaurant_selected: ' . $e->getMessage());
        }

        // Track claim start for abandoned recovery
        if ($this->selectedRestaurant && !$this->selectedRestaurant->user_id) {
            try {
                $this->selectedRestaurant->update(['claim_started_at' => now()]);
            } catch (\Exception $e) {
                Log::warning('Failed to update claim_started_at: ' . $e->getMessage());
            }
        }

        // Social proof: premium/elite restaurants in same state
        try {
            $this->socialProofCount = \App\Models\Restaurant::where('state_id', $this->selectedRestaurant->state_id)
                ->whereIn('subscription_tier', ['premium', 'elite'])
                ->where('id', '!=', $this->selectedRestaurant->id)
                ->count();
        } catch (\Exception $e) {}

        $this->detectAvailableMethods();
        $this->step = 'verify';
        $this->dispatch('scroll-top');
    }

    public function backToSearch()
    {
        $this->step = 'search';
        $this->dispatch('scroll-top');
        $this->selectedRestaurant = null;
        $this->reset(['ownerName', 'ownerEmail', 'ownerPhone', 'verificationCode', 'codeError', 'availableMethods']);
    }

    public function backToVerify()
    {
        $this->step = 'verify';
        $this->dispatch('scroll-top');
        $this->reset(['verificationCode', 'codeError']);
    }

    public function backToVerifyCode()
    {
        $this->step = 'verify_code';
        $this->dispatch('scroll-top');
    }

    public function backToSelectPlan()
    {
        $this->step = 'select_plan';
        $this->stripeClientSecret = null;
        $this->dispatch('scroll-top');
    }

    public function submitVerification()
    {
        $this->validate([
            'ownerName' => 'required|min:2',
            'ownerEmail' => 'required|email',
            'ownerPhone' => 'required|min:10',
        ]);

        // Track verification started
        try {
            \App\Models\AnalyticsEvent::create([
                'restaurant_id' => $this->selectedRestaurant->id ?? null,
                'event_type' => 'claim_verification_started',
                'user_type' => 'owner',
                'ip_address' => request()->ip(),
                'referrer' => request()->header('referer'),
                'user_agent' => request()->userAgent(),
                'session_id' => session()->getId(),
                'metadata' => [
                    'method' => $this->verificationMethod,
                    'restaurant_id' => $this->selectedRestaurant->id ?? null,
                ],
            ]);
        } catch (\Exception $e) {
            Log::warning('Failed to track claim_verification_started: ' . $e->getMessage());
        }

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
                // Twilio not configured — fallback to email verification
                Log::warning('Phone verification failed, falling back to email for restaurant ' . $this->selectedRestaurant->id);

                $fallbackEmail = $hasEmail ? $restaurantEmail : $this->ownerEmail;
                $emailCacheKey = 'claim_code_' . $this->selectedRestaurant->id . '_' . md5($fallbackEmail);
                Cache::put($emailCacheKey, $code, now()->addMinutes(15));

                $this->sendVerificationEmail($code, $fallbackEmail);
                $this->verificationMethod = 'email';

                $maskedEmail = $this->maskEmail($fallbackEmail);
                session()->flash('success', 'La verificación por teléfono no está disponible. Se envió el código al correo: ' . $maskedEmail);
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
        $this->dispatch('scroll-top');
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

        // Track verification completed
        try {
            \App\Models\AnalyticsEvent::create([
                'restaurant_id' => $this->selectedRestaurant->id ?? null,
                'event_type' => 'claim_verification_completed',
                'user_type' => 'owner',
                'ip_address' => request()->ip(),
                'referrer' => request()->header('referer'),
                'user_agent' => request()->userAgent(),
                'session_id' => session()->getId(),
                'metadata' => [
                    'method' => $this->verificationMethod,
                    'restaurant_id' => $this->selectedRestaurant->id ?? null,
                ],
            ]);
        } catch (\Exception $e) {
            Log::warning('Failed to track claim_verification_completed: ' . $e->getMessage());
        }

        $this->step = 'select_role';
        $this->dispatch('scroll-top');
    }

    public function selectRole(string $role): void
    {
        $this->ownerRole = $role;

        $this->step = 'create_account';
        $this->dispatch('scroll-top');
    }

    public function submitCreateAccount(): void
    {
        $this->validate([
            'password' => 'required|min:8',
            'passwordConfirmation' => 'required|same:password',
        ]);

        // Save consents to restaurant
        if ($this->selectedRestaurant) {
            $this->selectedRestaurant->update([
                'email_consent' => $this->emailConsent,
                'sms_consent'   => $this->smsConsent,
                'owner_role'    => $this->ownerRole ?: 'owner',
            ]);
        }

        // Create or find user
        $user = \App\Models\User::where('email', $this->ownerEmail)->first();
        if (!$user) {
            $user = \App\Models\User::create([
                'name'     => $this->ownerName,
                'email'    => $this->ownerEmail,
                'password' => \Illuminate\Support\Facades\Hash::make($this->password),
                'phone'    => $this->ownerPhone ?? null,
            ]);
        }

        // Link user to restaurant
        if ($this->selectedRestaurant && !$this->selectedRestaurant->user_id) {
            $this->selectedRestaurant->update(['user_id' => $user->id]);
        }

        // Store for Stripe session (paid plans need this)
        session([
            'claim_password'      => \Illuminate\Support\Facades\Hash::make($this->password),
            'claim_owner_name'    => $this->ownerName,
            'claim_owner_email'   => $this->ownerEmail,
            'claim_owner_phone'   => $this->ownerPhone,
            'claim_restaurant_id' => $this->selectedRestaurant?->id,
            'claim_owner_role'    => $this->ownerRole,
        ]);

        // Log the user in
        \Illuminate\Support\Facades\Auth::login($user);

        // Load restaurant stats for plan selection modal
        $this->restaurantMonthlyViews = \App\Models\AnalyticsEvent::where('restaurant_id', $this->selectedRestaurant->id)
            ->where('event_type', \App\Models\AnalyticsEvent::EVENT_PAGE_VIEW)
            ->where('created_at', '>=', now()->startOfMonth())
            ->count();

        $this->restaurantTotalViews = \App\Models\AnalyticsEvent::where('restaurant_id', $this->selectedRestaurant->id)
            ->where('event_type', \App\Models\AnalyticsEvent::EVENT_PAGE_VIEW)
            ->count();

        // Count competitor restaurants in the same state
        $this->competitorCount = \App\Models\Restaurant::where('state_id', $this->selectedRestaurant->state_id)
            ->where('status', 'approved')
            ->where('id', '!=', $this->selectedRestaurant->id)
            ->count();

        $this->step = 'select_plan';
        $this->dispatch('scroll-top');
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

        if ($plan !== 'free') {
            // Track upgrade to premium
            try {
                \App\Models\AnalyticsEvent::create([
                    'restaurant_id' => $this->selectedRestaurant->id ?? null,
                    'event_type' => 'claim_upgrade_to_premium',
                    'user_type' => 'owner',
                    'ip_address' => request()->ip(),
                    'referrer' => request()->header('referer'),
                    'user_agent' => request()->userAgent(),
                    'session_id' => session()->getId(),
                    'metadata' => [
                        'plan' => $plan,
                        'restaurant_id' => $this->selectedRestaurant->id ?? null,
                    ],
                ]);
            } catch (\Exception $e) {
                Log::warning('Failed to track claim_upgrade_to_premium: ' . $e->getMessage());
            }
        }

        if ($plan === 'free') {
            return $this->completeFreeClai();
        }

        // Session data (owner info, restaurant id, etc.) was already stored in submitCreateAccount().
        // Store coupon in session so ClaimPaymentController can use it.
        if ($this->couponApplied && $this->couponCode) {
            session(['claim_coupon_code' => $this->couponCode]);
        }

        // Redirect to embedded checkout page (on FAMER domain)
        $this->redirect(
            route('claim.pay', ['restaurant' => $this->selectedRestaurant->slug, 'plan' => $plan]),
            navigate: false
        );
    }

    public function completeFreeClai()
    {
        $this->selectedRestaurant->update([
            'is_claimed' => true,
            'claimed_at' => now(),
            'subscription_tier' => 'free',
            'subscription_status' => 'active',
            'premium_analytics' => false,
            'premium_seo' => false,
            'premium_featured' => false,
            'premium_coupons' => false,
            'premium_email_marketing' => false,
        ]);

        // User is already created and logged in by submitCreateAccount().
        // Fallback: create user if somehow not already authenticated.
        if (!auth()->check()) {
            $user = User::firstOrCreate(
                ['email' => $this->ownerEmail],
                [
                    'name'     => $this->ownerName,
                    'password' => \Illuminate\Support\Facades\Hash::make($this->password),
                    'phone'    => $this->ownerPhone,
                    'email_verified_at' => now(),
                ]
            );

            $user->role = 'owner';
            if (!$user->email_verified_at) {
                $user->email_verified_at = now();
            }
            $user->save();

            if (!$this->selectedRestaurant->user_id) {
                $this->selectedRestaurant->update(['user_id' => $user->id]);
            }

            \Illuminate\Support\Facades\Auth::login($user);
        } else {
            $user = auth()->user();
            $user->role = 'owner';
            if (!$user->email_verified_at) {
                $user->email_verified_at = now();
                $user->save();
            }
        }

        session()->regenerate();

        // Track claim completed
        try {
            \App\Models\AnalyticsEvent::create([
                'restaurant_id' => $this->selectedRestaurant->id ?? null,
                'event_type' => 'claim_completed',
                'user_type' => 'owner',
                'ip_address' => request()->ip(),
                'referrer' => request()->header('referer'),
                'user_agent' => request()->userAgent(),
                'session_id' => session()->getId(),
                'metadata' => [
                    'plan' => $this->selectedPlan,
                    'restaurant_id' => $this->selectedRestaurant->id ?? null,
                    'restaurant_name' => $this->selectedRestaurant->name ?? null,
                ],
            ]);
        } catch (\Exception $e) {
            Log::warning('Failed to track claim_completed: ' . $e->getMessage());
        }

        // Send welcome email with coupon
        try {
            \App\Mail\ClaimWelcomeMail::sendAndLog(
                $this->selectedRestaurant,
                $this->ownerName,
                $this->ownerEmail,
                'free'
            );
        } catch (\Exception $e) {
            Log::warning('Failed to send ClaimWelcomeMail: ' . $e->getMessage());
        }

        // Dispatch 48-hour follow-up email job
        try {
            \App\Jobs\SendFreeClaimFollowUpJob::dispatch($this->selectedRestaurant->id)
                ->delay(now()->addHours(48));
        } catch (\Exception $e) {
            Log::warning('Could not dispatch FreeClaimFollowUpJob: ' . $e->getMessage());
        }

        session()->flash('success', '¡Felicidades! Tu restaurante ha sido reclamado exitosamente.');
        return $this->redirect('/owner', navigate: false);
    }

    public function applyCoupon()
    {
        if (empty($this->couponCode)) {
            $this->couponMessage = 'Please enter a coupon code';
            $this->couponApplied = false;
            return;
        }

        $stripeService = new StripeService();
        $promotionCode = $stripeService->validatePromotionCode($this->couponCode);

        if ($promotionCode) {
            $this->couponApplied = true;
            // Stripe PHP v18: coupon is not directly expanded on PromotionCode
            $promoArr = $promotionCode->toArray();
            $couponId = $promoArr['promotion']['coupon'] ?? null;
            if (!$couponId && isset($promoArr['coupon'])) {
                $couponId = is_string($promoArr['coupon']) ? $promoArr['coupon'] : ($promoArr['coupon']['id'] ?? null);
            }
            $coupon = $couponId ? \Stripe\Coupon::retrieve($couponId) : null;

            if ($coupon && $coupon->percent_off) {
                $this->couponMessage = "Coupon applied! {$coupon->percent_off}% off";
            } elseif ($coupon && $coupon->amount_off) {
                $amount = $coupon->amount_off / 100;
                $this->couponMessage = "Coupon applied! \${$amount} off";
            }
        } else {
            $this->couponApplied = false;
            $this->couponMessage = 'Invalid or expired coupon code';
        }
    }

    public function processPayment()
    {
        try {
            $stripeService = new StripeService();

            $session = $stripeService->createCheckoutSession(
                $this->selectedRestaurant,
                $this->selectedPlan,
                route('claim.success') . '?session_id={CHECKOUT_SESSION_ID}',
                route('claim.cancel'),
                $this->couponApplied ? $this->couponCode : null
            );

            return redirect($session->url);
        } catch (\Exception $e) {
            session()->flash('error', 'Error processing payment: ' . $e->getMessage());
        }
    }

    public function initStripePayment(): void
    {
        try {
            $stripeService = new StripeService();
            $result = $stripeService->createSubscriptionSetupIntent(
                $this->selectedRestaurant,
                $this->selectedPlan,
                $this->couponApplied ? $this->couponCode : null
            );
            $this->stripeClientSecret = $result['clientSecret'];
            // Dispatch browser event so JS can initialize Stripe after Livewire morphs the DOM
            $this->dispatch('mount-stripe-card',
                clientSecret: $result['clientSecret'],
                stripeKey: config('stripe.key')
            );
        } catch (\Exception $e) {
            Log::error('Error initializing Stripe payment: ' . $e->getMessage());
            session()->flash('error', 'Error al inicializar el pago. Por favor intenta de nuevo.');
        }
    }

    public function completeSubscriptionPayment(string $setupIntentId): mixed
    {
        try {
            $stripeService = new StripeService();

            // Create subscription from the confirmed SetupIntent
            $subscription = $stripeService->createSubscriptionFromSetupIntent(
                $setupIntentId,
                $this->selectedRestaurant,
                $this->selectedPlan,
                $this->couponApplied ? $this->couponCode : null
            );

            // Mark restaurant as claimed and set subscription data
            $stripeService->handleSuccessfulSubscription(
                $subscription->id,
                $this->selectedRestaurant,
                $this->selectedPlan
            );

            // Create or find the owner user account
            $user = User::firstOrCreate(
                ['email' => $this->ownerEmail],
                [
                    'name' => $this->ownerName,
                    'password' => bcrypt(Str::random(12)),
                    'phone' => $this->ownerPhone,
                ]
            );
            $user->role = 'owner';
            if (!$user->email_verified_at) {
                $user->email_verified_at = now();
            }
            $user->save();
            $this->selectedRestaurant->update(['user_id' => $user->id]);

            auth()->login($user);
            session()->regenerate();

            // Track claim completed
            try {
                \App\Models\AnalyticsEvent::create([
                    'restaurant_id' => $this->selectedRestaurant->id ?? null,
                    'event_type' => 'claim_completed',
                    'user_type' => 'owner',
                    'ip_address' => request()->ip(),
                    'referrer' => request()->header('referer'),
                    'user_agent' => request()->userAgent(),
                    'session_id' => session()->getId(),
                    'metadata' => [
                        'plan' => $this->selectedPlan,
                        'restaurant_id' => $this->selectedRestaurant->id ?? null,
                        'restaurant_name' => $this->selectedRestaurant->name ?? null,
                        'subscription_id' => $subscription->id,
                    ],
                ]);
            } catch (\Exception $e) {
                Log::warning('Failed to track claim_completed: ' . $e->getMessage());
            }

            session()->flash('success', '¡Felicidades! Tu restaurante ha sido reclamado y tu suscripción activada.');
            return $this->redirect('/owner', navigate: false);
        } catch (\Exception $e) {
            Log::error('Error completing subscription payment: ' . $e->getMessage());
            $this->dispatch('stripe-payment-error', message: 'Error al completar el pago: ' . $e->getMessage());
            return null;
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
