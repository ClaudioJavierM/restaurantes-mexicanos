<?php

namespace App\Filament\Owner\Pages;

use App\Models\AutoCampaignConfig;
use App\Models\OwnerCampaign;
use App\Models\RestaurantCustomer;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class EmailMarketing extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-envelope';
    protected static ?string $navigationLabel = 'Email Marketing';
    protected static ?string $title = 'Email Marketing';
    protected static ?string $navigationGroup = 'Mi Negocio';
    protected static ?int $navigationSort = 7;

    protected static string $view = 'filament.owner.pages.email-marketing';

    public $restaurant = null;

    // Segments
    public array $segments = [];
    public array $campaigns = [];
    public array $autoCampaigns = [];

    // New campaign form
    public bool $showCampaignForm = false;
    public string $campaignName = '';
    public string $campaignSubject = '';
    public string $campaignContent = '';
    public string $campaignType = 'promo';
    public string $campaignAudience = 'all';

    public static function shouldRegisterNavigation(): bool
    {
        $user = Auth::user();
        if (!$user) return false;
        $restaurant = $user->allAccessibleRestaurants()->first();
        return $restaurant && $restaurant->is_claimed;
    }

    public function mount(): void
    {
        $this->restaurant = Auth::user()->allAccessibleRestaurants()->first();
        if (!$this->restaurant) return;

        $this->loadSegments();
        $this->loadCampaigns();
        $this->loadAutoCampaigns();
    }

    protected function loadSegments(): void
    {
        $q = RestaurantCustomer::where('restaurant_id', $this->restaurant->id);

        $this->segments = [
            'total'          => (clone $q)->count(),
            'subscribed'     => (clone $q)->where('email_subscribed', true)->count(),
            'frequent'       => (clone $q)->where('visits_count', '>=', 3)->count(),
            'new'            => (clone $q)->where('created_at', '>=', now()->subDays(30))->count(),
            'inactive'       => (clone $q)->where(function ($sq) {
                                    $sq->whereNull('last_visit_at')
                                       ->orWhere('last_visit_at', '<', now()->subDays(90));
                                })->count(),
            'birthday_month' => (clone $q)->whereNotNull('birthday')
                                          ->whereMonth('birthday', now()->month)->count(),
        ];
    }

    protected function loadCampaigns(): void
    {
        $this->campaigns = OwnerCampaign::where('restaurant_id', $this->restaurant->id)
            ->latest()
            ->take(10)
            ->get()
            ->toArray();
    }

    protected function loadAutoCampaigns(): void
    {
        $existing = AutoCampaignConfig::where('restaurant_id', $this->restaurant->id)
            ->get()
            ->keyBy('type');

        $this->autoCampaigns = [];
        foreach (AutoCampaignConfig::$types as $type => $label) {
            if ($existing->has($type)) {
                $this->autoCampaigns[$type] = $existing[$type]->toArray();
            } else {
                $this->autoCampaigns[$type] = [
                    'type'                    => $type,
                    'is_active'               => false,
                    'subject'                 => AutoCampaignConfig::defaultSubject($type, $this->restaurant->name),
                    'message'                 => AutoCampaignConfig::defaultMessage($type, $this->restaurant->name),
                    'coupon_discount_percent' => 15,
                    'coupon_valid_days'        => 7,
                    'total_sent'              => 0,
                    'last_run_at'             => null,
                ];
            }
        }
    }

    public function createCampaign(): void
    {
        $this->validate([
            'campaignName'     => 'required|string|max:150',
            'campaignSubject'  => 'required|string|max:200',
            'campaignContent'  => 'required|string|min:20',
            'campaignType'     => 'required|string',
            'campaignAudience' => 'required|string',
        ]);

        $audienceFilter = match($this->campaignAudience) {
            'frequent'       => ['min_visits' => 3],
            'inactive'       => ['inactive_days' => 90],
            'birthday_month' => ['birthday_month' => now()->month],
            default          => [],
        };

        $campaign = OwnerCampaign::create([
            'restaurant_id'   => $this->restaurant->id,
            'created_by'      => auth()->id(),
            'name'            => $this->campaignName,
            'subject'         => $this->campaignSubject,
            'content'         => $this->campaignContent,
            'type'            => $this->campaignType,
            'audience_filter' => $audienceFilter,
            'status'          => 'draft',
        ]);

        $this->showCampaignForm = false;
        $this->reset(['campaignName', 'campaignSubject', 'campaignContent', 'campaignType', 'campaignAudience']);
        $this->loadCampaigns();

        Notification::make()
            ->title('Campaña creada como borrador')
            ->body('Revisa y programa el envío desde la lista.')
            ->success()->send();
    }

    public function scheduleCampaign(int $campaignId): void
    {
        $campaign = OwnerCampaign::where('restaurant_id', $this->restaurant->id)
            ->findOrFail($campaignId);

        if (!$campaign->isDraft()) return;

        $audienceCount = $campaign->getAudienceCount();

        $campaign->update([
            'status'            => 'scheduled',
            'scheduled_at'      => now()->addMinutes(5),
            'total_recipients'  => $audienceCount,
        ]);

        $this->loadCampaigns();
        Notification::make()
            ->title("Campaña programada — {$audienceCount} destinatarios")
            ->success()->send();
    }

    public function saveAutoCampaign(string $type): void
    {
        $data = $this->autoCampaigns[$type] ?? [];

        AutoCampaignConfig::updateOrCreate(
            ['restaurant_id' => $this->restaurant->id, 'type' => $type],
            [
                'is_active'               => $data['is_active'] ?? false,
                'subject'                 => $data['subject'] ?? AutoCampaignConfig::defaultSubject($type, $this->restaurant->name),
                'message'                 => $data['message'] ?? AutoCampaignConfig::defaultMessage($type, $this->restaurant->name),
                'coupon_discount_percent' => $data['coupon_discount_percent'] ?? 15,
                'coupon_valid_days'        => $data['coupon_valid_days'] ?? 7,
            ]
        );

        Notification::make()
            ->title('Campaña automática guardada')
            ->success()->send();
    }
}
