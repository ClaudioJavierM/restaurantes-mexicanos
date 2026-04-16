<?php

namespace App\Livewire\Owner;

use App\Models\MenuCategory;
use App\Models\MenuItem;
use App\Models\Restaurant;
use App\Services\TwilioService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class OnboardingWizard extends Component
{
    use WithFileUploads;

    protected static bool $isLazy = false;

    public int $restaurantId;
    public int $currentStep = 1;
    public int $totalSteps = 5;
    public bool $completed = false;

    // Step 1 — Cover photo
    public $coverPhoto = null;
    public ?string $existingCoverUrl = null;

    // Step 2 — Hours
    public array $hours = [];

    // Step 3 — Menu items
    public array $menuItems = [
        ['name' => '', 'price' => '', 'description' => ''],
    ];

    // Step 4 — WhatsApp & contact
    public string $countryCode = '+52';
    public string $whatsappPhone = '';
    public string $websiteUrl = '';
    public string $orderUrl = '';

    // Step 5 — Invite first review
    public string $inviteEmail = '';
    public string $invitePhone = '';
    public bool $inviteSent = false;

    protected ?Restaurant $restaurant = null;

    // ──────────────────────────────────────────────────────────────
    // Lifecycle
    // ──────────────────────────────────────────────────────────────

    public function mount(int $restaurantId): void
    {
        $this->restaurantId = $restaurantId;
        $restaurant = $this->getRestaurant();

        // Pre-fill Step 2 from existing hours JSON
        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
        $existingHours = is_array($restaurant->hours) ? $restaurant->hours : [];

        foreach ($days as $day) {
            $this->hours[$day] = [
                'open'   => $existingHours[$day]['open']   ?? '09:00',
                'close'  => $existingHours[$day]['close']  ?? '21:00',
                'closed' => $existingHours[$day]['closed'] ?? false,
            ];
        }

        // Pre-fill Step 4
        $this->websiteUrl = $restaurant->website ?? '';
        $this->orderUrl   = $restaurant->order_url ?? '';
        $phone = $restaurant->phone ?? $restaurant->owner_phone ?? '';
        if ($phone) {
            // Strip country code prefix if already present
            $this->whatsappPhone = preg_replace('/^\+\d{1,2}/', '', $phone);
        }

        // Existing cover
        if ($restaurant->image) {
            $this->existingCoverUrl = str_starts_with($restaurant->image, 'http')
                ? $restaurant->image
                : Storage::url($restaurant->image);
        }
    }

    // ──────────────────────────────────────────────────────────────
    // Auth guard
    // ──────────────────────────────────────────────────────────────

    protected function getRestaurant(): Restaurant
    {
        if ($this->restaurant) {
            return $this->restaurant;
        }

        $restaurant = Restaurant::findOrFail($this->restaurantId);

        // Verify ownership — direct owner or team member
        $user = Auth::user();
        $isOwner = $restaurant->user_id === $user->id;
        $isTeam = $restaurant->isTeamMember($user);

        abort_unless($isOwner || $isTeam, 403, 'No autorizado.');

        $this->restaurant = $restaurant;
        return $restaurant;
    }

    // ──────────────────────────────────────────────────────────────
    // Computed
    // ──────────────────────────────────────────────────────────────

    public function getProgressPercentProperty(): float
    {
        return ($this->currentStep / $this->totalSteps) * 100;
    }

    // ──────────────────────────────────────────────────────────────
    // Navigation
    // ──────────────────────────────────────────────────────────────

    public function nextStep(): void
    {
        match ($this->currentStep) {
            1 => $this->saveStep1(),
            2 => $this->saveStep2(),
            3 => $this->saveStep3(),
            4 => $this->saveStep4(),
            default => null,
        };

        if ($this->currentStep < $this->totalSteps) {
            $this->currentStep++;
            $this->getRestaurant()->update(['onboarding_step' => $this->currentStep]);
        }
    }

    public function prevStep(): void
    {
        if ($this->currentStep > 1) {
            $this->currentStep--;
        }
    }

    public function skipStep(): void
    {
        if ($this->currentStep < $this->totalSteps) {
            $this->currentStep++;
            $this->getRestaurant()->update(['onboarding_step' => $this->currentStep]);
        }
    }

    // ──────────────────────────────────────────────────────────────
    // Step savers
    // ──────────────────────────────────────────────────────────────

    public function saveStep1(): void
    {
        if (!$this->coverPhoto) {
            return;
        }

        $this->validate([
            'coverPhoto' => 'image|max:5120', // 5 MB
        ], [
            'coverPhoto.image' => 'El archivo debe ser una imagen.',
            'coverPhoto.max'   => 'La imagen no debe superar 5 MB.',
        ]);

        $restaurant = $this->getRestaurant();
        $dir  = "restaurants/{$restaurant->id}";
        $path = $this->coverPhoto->storeAs($dir, 'cover.jpg', 'public');

        $photos = is_array($restaurant->photos) ? $restaurant->photos : [];
        array_unshift($photos, Storage::url($path));

        $restaurant->update([
            'image'  => $path,
            'photos' => array_values(array_unique($photos)),
        ]);

        $this->existingCoverUrl = Storage::url($path);
        $this->coverPhoto = null;
    }

    public function saveStep2(): void
    {
        $this->validate([
            'hours.*.open'  => 'nullable|date_format:H:i',
            'hours.*.close' => 'nullable|date_format:H:i',
        ], [
            'hours.*.open.date_format'  => 'Formato de hora inválido (HH:MM).',
            'hours.*.close.date_format' => 'Formato de hora inválido (HH:MM).',
        ]);

        $this->getRestaurant()->update(['hours' => $this->hours]);
    }

    public function addMenuItem(): void
    {
        if (count($this->menuItems) < 5) {
            $this->menuItems[] = ['name' => '', 'price' => '', 'description' => ''];
        }
    }

    public function removeMenuItem(int $index): void
    {
        if (isset($this->menuItems[$index]) && count($this->menuItems) > 1) {
            array_splice($this->menuItems, $index, 1);
            $this->menuItems = array_values($this->menuItems);
        }
    }

    public function saveStep3(): void
    {
        $this->validate([
            'menuItems.*.name'  => 'nullable|string|max:120',
            'menuItems.*.price' => 'nullable|numeric|min:0|max:9999.99',
            'menuItems.*.description' => 'nullable|string|max:300',
        ]);

        $restaurant = $this->getRestaurant();
        $items = array_filter($this->menuItems, fn($i) => !empty(trim($i['name'] ?? '')));

        if (empty($items)) {
            return;
        }

        // Find or create a default "Platillos Estrella" category
        $category = MenuCategory::firstOrCreate(
            ['restaurant_id' => $restaurant->id, 'name' => 'Platillos Estrella'],
            [
                'name_es'    => 'Platillos Estrella',
                'sort_order' => 0,
                'is_active'  => true,
            ]
        );

        foreach ($items as $item) {
            if (empty(trim($item['name']))) {
                continue;
            }

            // Only create if a menu item with this name doesn't already exist
            $exists = MenuItem::where('menu_category_id', $category->id)
                ->where('name', trim($item['name']))
                ->exists();

            if (!$exists) {
                MenuItem::create([
                    'menu_category_id' => $category->id,
                    'name'             => trim($item['name']),
                    'name_es'          => trim($item['name']),
                    'description'      => trim($item['description'] ?? ''),
                    'description_es'   => trim($item['description'] ?? ''),
                    'price'            => is_numeric($item['price']) ? (float) $item['price'] : null,
                    'is_available'     => true,
                    'is_popular'       => true,
                    'sort_order'       => 0,
                ]);
            }
        }
    }

    public function saveStep4(): void
    {
        $this->validate([
            'whatsappPhone' => 'nullable|string|max:20',
            'websiteUrl'    => 'nullable|url|max:255',
            'orderUrl'      => 'nullable|url|max:255',
        ], [
            'websiteUrl.url' => 'La URL del sitio web no es válida.',
            'orderUrl.url'   => 'La URL de pedidos no es válida.',
        ]);

        $fullPhone = !empty($this->whatsappPhone)
            ? $this->countryCode . preg_replace('/\D/', '', $this->whatsappPhone)
            : null;

        $data = [];
        if ($fullPhone)             $data['phone']     = $fullPhone;
        if ($this->websiteUrl)      $data['website']   = $this->websiteUrl;
        if ($this->orderUrl)        $data['order_url'] = $this->orderUrl;

        if (!empty($data)) {
            $this->getRestaurant()->update($data);
        }
    }

    public function saveStep5(): void
    {
        $this->validate([
            'inviteEmail' => 'nullable|email|max:255',
            'invitePhone' => 'nullable|string|max:20',
        ]);

        $restaurant = $this->getRestaurant();
        $sent = false;

        // Send email invite
        if (!empty($this->inviteEmail)) {
            try {
                $reviewUrl = url('/restaurante/' . $restaurant->slug . '#reviews');
                $message = "¡Hola! Te invitamos a dejar una reseña de {$restaurant->name} en FAMER.\n\n"
                    . "Tu opinión es muy valiosa para nosotros.\n\n"
                    . "Deja tu reseña aquí: {$reviewUrl}\n\n"
                    . "¡Gracias por apoyar a los restaurantes mexicanos!";

                Mail::raw($message, function ($mail) use ($restaurant) {
                    $mail->to($this->inviteEmail)
                        ->subject("¿Qué te pareció {$restaurant->name}? Déjanos tu reseña")
                        ->from(config('mail.from.address'), $restaurant->name);
                });
                $sent = true;
            } catch (\Exception $e) {
                Log::warning("OnboardingWizard: Failed to send invite email: " . $e->getMessage());
            }
        }

        // Send SMS invite
        if (!empty($this->invitePhone)) {
            try {
                $twilio = app(TwilioService::class);
                if ($twilio->isConfigured()) {
                    $reviewUrl = url('/restaurante/' . $restaurant->slug . '#reviews');
                    $smsText   = "¡Hola! Te invita {$restaurant->name} a dejar tu reseña en FAMER: {$reviewUrl}";
                    $twilio->sendSms($this->invitePhone, $smsText);
                    $sent = true;
                }
            } catch (\Exception $e) {
                Log::warning("OnboardingWizard: Failed to send invite SMS: " . $e->getMessage());
            }
        }

        $this->inviteSent = $sent;
        $this->completeOnboarding();
    }

    public function completeOnboarding(): void
    {
        $this->getRestaurant()->update([
            'onboarding_completed'    => true,
            'onboarding_step'         => 5,
            'onboarding_completed_at' => now(),
        ]);

        $this->completed = true;
    }

    // ──────────────────────────────────────────────────────────────
    // Render
    // ──────────────────────────────────────────────────────────────

    public function render()
    {
        return view('livewire.owner.onboarding-wizard', [
            'restaurant' => $this->getRestaurant(),
            'progressPercent' => $this->progressPercent,
        ]);
    }
}
