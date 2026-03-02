<?php

namespace App\Livewire\Owner;

use App\Models\Restaurant;
use App\Models\RestaurantCustomer;
use App\Models\OwnerCampaign;
use App\Models\OwnerCampaignSend;
use App\Jobs\SendOwnerCampaign;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use League\Csv\Reader;

class EmailMarketing extends Component
{
    use WithPagination, WithFileUploads;

    public Restaurant $restaurant;
    
    // View state
    public string $activeTab = 'campaigns';
    public bool $showCampaignModal = false;
    public bool $showCustomerModal = false;
    public bool $showImportModal = false;
    public bool $showPreviewModal = false;
    
    // Campaign form
    public ?int $editingCampaignId = null;
    public string $campaignName = '';
    public string $campaignSubject = '';
    public string $campaignPreviewText = '';
    public string $campaignType = 'promo';
    public string $campaignContent = '';
    public string $campaignTemplate = 'default';
    public ?string $scheduledDate = null;
    public ?string $scheduledTime = null;
    public array $audienceFilter = [];
    public bool $includeCoupon = false;
    public string $couponCode = '';
    public string $couponDiscount = '';
    public ?string $couponExpiry = null;
    
    // Customer form
    public ?int $editingCustomerId = null;
    public string $customerEmail = '';
    public string $customerName = '';
    public string $customerPhone = '';
    public ?string $customerBirthday = null;
    public string $customerSource = 'manual';
    
    // Import
    public $importFile = null;
    public array $importPreview = [];
    public int $importCount = 0;
    
    // Filters
    public string $searchCustomers = '';
    public string $filterSource = '';
    public string $filterCampaignStatus = '';
    
    // Stats
    public array $stats = [];

    protected $rules = [
        'campaignName' => 'required|min:3',
        'campaignSubject' => 'required|min:5',
        'campaignContent' => 'required|min:20',
        'customerEmail' => 'required|email',
        'customerName' => 'nullable|string|max:255',
    ];

    public function mount(Restaurant $restaurant)
    {
        $this->restaurant = $restaurant;
        $this->loadStats();
    }

    public function loadStats()
    {
        $this->stats = [
            'total_customers' => $this->restaurant->customers()->count(),
            'subscribed' => $this->restaurant->subscribedCustomers()->count(),
            'total_campaigns' => $this->restaurant->ownerCampaigns()->count(),
            'sent_campaigns' => $this->restaurant->ownerCampaigns()->where('status', 'sent')->count(),
            'total_sent' => $this->restaurant->ownerCampaigns()->sum('sent_count'),
            'total_opens' => $this->restaurant->ownerCampaigns()->sum('opened_count'),
            'total_clicks' => $this->restaurant->ownerCampaigns()->sum('clicked_count'),
            'avg_open_rate' => 0,
            'avg_click_rate' => 0,
        ];
        
        if ($this->stats['total_sent'] > 0) {
            $this->stats['avg_open_rate'] = round(($this->stats['total_opens'] / $this->stats['total_sent']) * 100, 1);
            $this->stats['avg_click_rate'] = round(($this->stats['total_clicks'] / $this->stats['total_sent']) * 100, 1);
        }
    }

    // CAMPAIGNS
    public function getCampaignsProperty()
    {
        $query = $this->restaurant->ownerCampaigns()->latest();
        
        if ($this->filterCampaignStatus) {
            $query->where('status', $this->filterCampaignStatus);
        }
        
        return $query->paginate(10);
    }

    public function newCampaign()
    {
        $this->resetCampaignForm();
        $this->showCampaignModal = true;
    }

    public function editCampaign(int $id)
    {
        $campaign = OwnerCampaign::find($id);
        if ($campaign && $campaign->restaurant_id === $this->restaurant->id && $campaign->canEdit()) {
            $this->editingCampaignId = $id;
            $this->campaignName = $campaign->name;
            $this->campaignSubject = $campaign->subject;
            $this->campaignPreviewText = $campaign->preview_text ?? '';
            $this->campaignType = $campaign->type;
            $this->campaignContent = $campaign->content;
            $this->campaignTemplate = $campaign->template ?? 'default';
            $this->audienceFilter = $campaign->audience_filter ?? [];
            
            if ($campaign->coupon_config) {
                $this->includeCoupon = true;
                $this->couponCode = $campaign->coupon_config['code'] ?? '';
                $this->couponDiscount = $campaign->coupon_config['discount'] ?? '';
                $this->couponExpiry = $campaign->coupon_config['expiry'] ?? null;
            }
            
            $this->showCampaignModal = true;
        }
    }

    public function saveCampaign()
    {
        $this->validate([
            'campaignName' => 'required|min:3',
            'campaignSubject' => 'required|min:5',
            'campaignContent' => 'required|min:20',
        ]);

        $data = [
            'restaurant_id' => $this->restaurant->id,
            'created_by' => auth()->id(),
            'name' => $this->campaignName,
            'subject' => $this->campaignSubject,
            'preview_text' => $this->campaignPreviewText,
            'type' => $this->campaignType,
            'content' => $this->campaignContent,
            'template' => $this->campaignTemplate,
            'audience_filter' => $this->audienceFilter,
        ];

        if ($this->includeCoupon) {
            $data['coupon_config'] = [
                'code' => $this->couponCode ?: strtoupper(Str::random(8)),
                'discount' => $this->couponDiscount,
                'expiry' => $this->couponExpiry,
            ];
        }

        if ($this->editingCampaignId) {
            OwnerCampaign::where('id', $this->editingCampaignId)
                ->where('restaurant_id', $this->restaurant->id)
                ->update($data);
        } else {
            OwnerCampaign::create($data);
        }

        $this->showCampaignModal = false;
        $this->resetCampaignForm();
        $this->loadStats();
        
        $this->dispatch('notify', ['message' => 'Campaña guardada exitosamente', 'type' => 'success']);
    }

    public function scheduleCampaign(int $id)
    {
        $campaign = OwnerCampaign::find($id);
        if ($campaign && $campaign->restaurant_id === $this->restaurant->id && $campaign->canSend()) {
            if ($this->scheduledDate && $this->scheduledTime) {
                $scheduledAt = $this->scheduledDate . ' ' . $this->scheduledTime;
                $campaign->schedule(new \DateTime($scheduledAt));
                $this->dispatch('notify', ['message' => 'Campaña programada', 'type' => 'success']);
            }
        }
        $this->scheduledDate = null;
        $this->scheduledTime = null;
    }

    public function sendCampaignNow(int $id)
    {
        $campaign = OwnerCampaign::find($id);
        if ($campaign && $campaign->restaurant_id === $this->restaurant->id && $campaign->canSend()) {
            $audience = $campaign->getAudience()->get();
            $campaign->update([
                'total_recipients' => $audience->count(),
                'status' => 'sending',
                'started_at' => now(),
            ]);
            
            // Queue the campaign
            SendOwnerCampaign::dispatch($campaign);
            
            $this->dispatch('notify', ['message' => 'Campaña en proceso de envío', 'type' => 'success']);
        }
        $this->loadStats();
    }

    public function cancelCampaign(int $id)
    {
        $campaign = OwnerCampaign::find($id);
        if ($campaign && $campaign->restaurant_id === $this->restaurant->id && $campaign->canCancel()) {
            $campaign->cancel();
            $this->dispatch('notify', ['message' => 'Campaña cancelada', 'type' => 'info']);
        }
        $this->loadStats();
    }

    public function deleteCampaign(int $id)
    {
        $campaign = OwnerCampaign::find($id);
        if ($campaign && $campaign->restaurant_id === $this->restaurant->id) {
            $campaign->delete();
            $this->dispatch('notify', ['message' => 'Campaña eliminada', 'type' => 'success']);
        }
        $this->loadStats();
    }

    public function previewCampaign(int $id)
    {
        $this->editingCampaignId = $id;
        $this->showPreviewModal = true;
    }

    public function getPreviewCampaignProperty()
    {
        if ($this->editingCampaignId) {
            return OwnerCampaign::find($this->editingCampaignId);
        }
        return null;
    }

    public function getAudiencePreviewCountProperty(): int
    {
        $query = $this->restaurant->subscribedCustomers();
        
        if (!empty($this->audienceFilter['source'])) {
            $query->where('source', $this->audienceFilter['source']);
        }
        if (!empty($this->audienceFilter['min_visits'])) {
            $query->where('visits_count', '>=', $this->audienceFilter['min_visits']);
        }
        
        return $query->count();
    }

    protected function resetCampaignForm()
    {
        $this->editingCampaignId = null;
        $this->campaignName = '';
        $this->campaignSubject = '';
        $this->campaignPreviewText = '';
        $this->campaignType = 'promo';
        $this->campaignContent = '';
        $this->campaignTemplate = 'default';
        $this->audienceFilter = [];
        $this->includeCoupon = false;
        $this->couponCode = '';
        $this->couponDiscount = '';
        $this->couponExpiry = null;
    }

    // CUSTOMERS
    public function getCustomersProperty()
    {
        $query = $this->restaurant->customers();
        
        if ($this->searchCustomers) {
            $query->where(function($q) {
                $q->where('email', 'like', '%' . $this->searchCustomers . '%')
                  ->orWhere('name', 'like', '%' . $this->searchCustomers . '%');
            });
        }
        
        if ($this->filterSource) {
            $query->where('source', $this->filterSource);
        }
        
        return $query->latest()->paginate(15);
    }

    public function newCustomer()
    {
        $this->resetCustomerForm();
        $this->showCustomerModal = true;
    }

    public function editCustomer(int $id)
    {
        $customer = RestaurantCustomer::find($id);
        if ($customer && $customer->restaurant_id === $this->restaurant->id) {
            $this->editingCustomerId = $id;
            $this->customerEmail = $customer->email;
            $this->customerName = $customer->name ?? '';
            $this->customerPhone = $customer->phone ?? '';
            $this->customerBirthday = $customer->birthday?->format('Y-m-d');
            $this->customerSource = $customer->source;
            $this->showCustomerModal = true;
        }
    }

    public function saveCustomer()
    {
        $this->validate([
            'customerEmail' => 'required|email',
            'customerName' => 'nullable|string|max:255',
        ]);

        $data = [
            'restaurant_id' => $this->restaurant->id,
            'email' => $this->customerEmail,
            'name' => $this->customerName ?: null,
            'phone' => $this->customerPhone ?: null,
            'birthday' => $this->customerBirthday ?: null,
            'source' => $this->customerSource,
            'subscribed_at' => now(),
        ];

        if ($this->editingCustomerId) {
            RestaurantCustomer::where('id', $this->editingCustomerId)
                ->where('restaurant_id', $this->restaurant->id)
                ->update($data);
        } else {
            $data['email_subscribed'] = true;
            RestaurantCustomer::updateOrCreate(
                ['restaurant_id' => $this->restaurant->id, 'email' => $this->customerEmail],
                $data
            );
        }

        $this->showCustomerModal = false;
        $this->resetCustomerForm();
        $this->loadStats();
        
        $this->dispatch('notify', ['message' => 'Cliente guardado exitosamente', 'type' => 'success']);
    }

    public function deleteCustomer(int $id)
    {
        RestaurantCustomer::where('id', $id)
            ->where('restaurant_id', $this->restaurant->id)
            ->delete();
        $this->loadStats();
        $this->dispatch('notify', ['message' => 'Cliente eliminado', 'type' => 'success']);
    }

    public function toggleSubscription(int $id)
    {
        $customer = RestaurantCustomer::find($id);
        if ($customer && $customer->restaurant_id === $this->restaurant->id) {
            $customer->update(['email_subscribed' => !$customer->email_subscribed]);
            $this->loadStats();
        }
    }

    protected function resetCustomerForm()
    {
        $this->editingCustomerId = null;
        $this->customerEmail = '';
        $this->customerName = '';
        $this->customerPhone = '';
        $this->customerBirthday = null;
        $this->customerSource = 'manual';
    }

    // IMPORT
    public function openImport()
    {
        $this->importFile = null;
        $this->importPreview = [];
        $this->importCount = 0;
        $this->showImportModal = true;
    }

    public function updatedImportFile()
    {
        if ($this->importFile) {
            try {
                $csv = Reader::createFromPath($this->importFile->getRealPath());
                $csv->setHeaderOffset(0);
                $records = iterator_to_array($csv->getRecords());
                $this->importCount = count($records);
                $this->importPreview = array_slice($records, 0, 5);
            } catch (\Exception $e) {
                $this->dispatch('notify', ['message' => 'Error al leer archivo: ' . $e->getMessage(), 'type' => 'error']);
            }
        }
    }

    public function processImport()
    {
        if (!$this->importFile) {
            return;
        }

        try {
            $csv = Reader::createFromPath($this->importFile->getRealPath());
            $csv->setHeaderOffset(0);
            
            $imported = 0;
            $skipped = 0;
            
            foreach ($csv->getRecords() as $record) {
                $email = $record['email'] ?? $record['Email'] ?? $record['EMAIL'] ?? null;
                if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $skipped++;
                    continue;
                }
                
                RestaurantCustomer::updateOrCreate(
                    ['restaurant_id' => $this->restaurant->id, 'email' => $email],
                    [
                        'name' => $record['name'] ?? $record['Name'] ?? $record['nombre'] ?? null,
                        'phone' => $record['phone'] ?? $record['Phone'] ?? $record['telefono'] ?? null,
                        'source' => 'import',
                        'email_subscribed' => true,
                        'subscribed_at' => now(),
                    ]
                );
                $imported++;
            }
            
            $this->showImportModal = false;
            $this->loadStats();
            $this->dispatch('notify', [
                'message' => "Importados: {$imported}, Omitidos: {$skipped}",
                'type' => 'success'
            ]);
        } catch (\Exception $e) {
            $this->dispatch('notify', ['message' => 'Error: ' . $e->getMessage(), 'type' => 'error']);
        }
    }

    // Templates
    public function getTemplatesProperty(): array
    {
        return [
            'default' => 'Plantilla Básica',
            'promo' => 'Promoción',
            'event' => 'Evento Especial',
            'birthday' => 'Cumpleaños',
            'newsletter' => 'Newsletter',
        ];
    }

    public function getCampaignTypesProperty(): array
    {
        return OwnerCampaign::typeLabels();
    }

    public function render()
    {
        return view('livewire.owner.email-marketing')
            ->layout('layouts.owner');
    }
}
