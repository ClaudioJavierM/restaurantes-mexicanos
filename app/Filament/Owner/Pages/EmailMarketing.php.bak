<?php

namespace App\Filament\Owner\Pages;

use App\Models\RestaurantCustomer;
use App\Models\OwnerCampaign;
use App\Jobs\SendOwnerCampaign;
use Filament\Pages\Page;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Tables;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use Livewire\WithFileUploads;
use League\Csv\Reader;

class EmailMarketing extends Page implements HasForms, HasTable
{
    use InteractsWithForms, InteractsWithTable, WithFileUploads;

    protected static ?string $navigationIcon = 'heroicon-o-envelope';
    protected static ?string $navigationLabel = 'Email Marketing';
    protected static ?string $title = 'Email Marketing';
    protected static ?string $navigationGroup = 'Mi Negocio';
    protected static ?int $navigationSort = 6;
    
    protected static string $view = 'filament.owner.pages.email-marketing';

    public ?array $campaignData = [];
    public ?array $customerData = [];
    public $restaurant;
    public bool $showCampaignForm = false;
    public bool $showCustomerForm = false;
    public bool $showImportForm = false;
    public string $activeTab = 'campaigns';
    public ?OwnerCampaign $editingCampaign = null;
    public $csvFile = null;
    
    // Stats
    public int $totalCustomers = 0;
    public int $subscribedCustomers = 0;
    public int $totalCampaigns = 0;
    public int $sentEmails = 0;
    public float $avgOpenRate = 0;

    public function mount(): void
    {
        $this->restaurant = Auth::user()->restaurants()->first();
        $this->loadStats();
    }

    public function loadStats(): void
    {
        if (!$this->restaurant) return;
        
        $this->totalCustomers = RestaurantCustomer::where('restaurant_id', $this->restaurant->id)->count();
        $this->subscribedCustomers = RestaurantCustomer::where('restaurant_id', $this->restaurant->id)->subscribed()->count();
        $this->totalCampaigns = OwnerCampaign::where('restaurant_id', $this->restaurant->id)->count();
        
        $campaigns = OwnerCampaign::where('restaurant_id', $this->restaurant->id)->where('status', 'sent');
        $this->sentEmails = $campaigns->sum('sent_count');
        
        $totalSent = $campaigns->sum('sent_count');
        $totalOpened = $campaigns->sum('opened_count');
        $this->avgOpenRate = $totalSent > 0 ? round(($totalOpened / $totalSent) * 100, 1) : 0;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                OwnerCampaign::query()
                    ->where('restaurant_id', $this->restaurant?->id ?? 0)
                    ->latest()
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable(),
                Tables\Columns\TextColumn::make('subject')
                    ->label('Asunto')
                    ->limit(40),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Estado')
                    ->colors([
                        'gray' => 'draft',
                        'info' => 'scheduled',
                        'warning' => 'sending',
                        'success' => 'sent',
                        'danger' => 'cancelled',
                    ]),
                Tables\Columns\TextColumn::make('sent_count')
                    ->label('Enviados')
                    ->numeric(),
                Tables\Columns\TextColumn::make('open_rate')
                    ->label('Aperturas')
                    ->formatStateUsing(fn ($state) => $state . '%'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('d M Y')
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\Action::make('send')
                    ->label('Enviar')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('success')
                    ->visible(fn (OwnerCampaign $record) => $record->canSend())
                    ->requiresConfirmation()
                    ->modalHeading('Enviar Campana')
                    ->modalDescription(fn (OwnerCampaign $record) => 'Se enviara a ' . $record->getAudienceCount() . ' destinatarios.')
                    ->action(function (OwnerCampaign $record) {
                        $audience = $record->getAudience()->get();
                        $record->update([
                            'total_recipients' => $audience->count(),
                            'status' => 'sending',
                            'started_at' => now(),
                        ]);
                        SendOwnerCampaign::dispatch($record);
                        Notification::make()->title('Campana en envio')->success()->send();
                    }),
                Tables\Actions\Action::make('edit')
                    ->label('Editar')
                    ->icon('heroicon-o-pencil')
                    ->visible(fn (OwnerCampaign $record) => $record->canEdit())
                    ->action(fn (OwnerCampaign $record) => $this->editCampaign($record)),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn (OwnerCampaign $record) => $record->isDraft()),
            ])
            ->emptyStateHeading('Sin campanas')
            ->emptyStateDescription('Crea tu primera campana de email marketing')
            ->emptyStateActions([
                Tables\Actions\Action::make('create')
                    ->label('Nueva Campana')
                    ->icon('heroicon-o-plus')
                    ->action(fn () => $this->showCampaignForm = true),
            ]);
    }

    public function getCampaignFormSchema(): array
    {
        return [
            Forms\Components\Grid::make(2)->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nombre de la campana')
                    ->required()
                    ->maxLength(100),
                Forms\Components\Select::make('type')
                    ->label('Tipo')
                    ->options(OwnerCampaign::typeLabels())
                    ->default('promo')
                    ->required(),
            ]),
            Forms\Components\TextInput::make('subject')
                ->label('Asunto del email')
                ->required()
                ->maxLength(150),
            Forms\Components\TextInput::make('preview_text')
                ->label('Texto de preview')
                ->helperText('Texto que aparece en la bandeja de entrada')
                ->maxLength(200),
            Forms\Components\Textarea::make('content')
                ->label('Contenido del email')
                ->required()
                ->rows(8)
                ->helperText('Usa {nombre} para el nombre del cliente, {restaurante} para tu restaurante'),
            Forms\Components\Section::make('Cupon (opcional)')
                ->collapsed()
                ->schema([
                    Forms\Components\Toggle::make('include_coupon')
                        ->label('Incluir cupon'),
                    Forms\Components\Grid::make(3)->schema([
                        Forms\Components\TextInput::make('coupon_code')
                            ->label('Codigo'),
                        Forms\Components\TextInput::make('coupon_discount')
                            ->label('Descuento')
                            ->placeholder('20% o $10'),
                        Forms\Components\DatePicker::make('coupon_expiry')
                            ->label('Expira'),
                    ]),
                ]),
        ];
    }

    public function saveCampaign(): void
    {
        $data = $this->campaignData;
        
        $campaignData = [
            'restaurant_id' => $this->restaurant->id,
            'created_by' => auth()->id(),
            'name' => $data['name'],
            'subject' => $data['subject'],
            'preview_text' => $data['preview_text'] ?? null,
            'type' => $data['type'],
            'content' => $data['content'],
            'status' => 'draft',
        ];

        if (!empty($data['include_coupon'])) {
            $campaignData['coupon_config'] = [
                'code' => $data['coupon_code'] ?? strtoupper(\Str::random(8)),
                'discount' => $data['coupon_discount'] ?? null,
                'expiry' => $data['coupon_expiry'] ?? null,
            ];
        }

        if ($this->editingCampaign) {
            $this->editingCampaign->update($campaignData);
        } else {
            OwnerCampaign::create($campaignData);
        }

        $this->showCampaignForm = false;
        $this->campaignData = [];
        $this->editingCampaign = null;
        $this->loadStats();
        
        Notification::make()->title('Campana guardada')->success()->send();
    }

    public function editCampaign(OwnerCampaign $campaign): void
    {
        $this->editingCampaign = $campaign;
        $this->campaignData = [
            'name' => $campaign->name,
            'subject' => $campaign->subject,
            'preview_text' => $campaign->preview_text,
            'type' => $campaign->type,
            'content' => $campaign->content,
            'include_coupon' => !empty($campaign->coupon_config),
            'coupon_code' => $campaign->coupon_config['code'] ?? null,
            'coupon_discount' => $campaign->coupon_config['discount'] ?? null,
            'coupon_expiry' => $campaign->coupon_config['expiry'] ?? null,
        ];
        $this->showCampaignForm = true;
    }

    public function getCustomerFormSchema(): array
    {
        return [
            Forms\Components\TextInput::make('email')
                ->label('Email')
                ->email()
                ->required(),
            Forms\Components\TextInput::make('name')
                ->label('Nombre'),
            Forms\Components\TextInput::make('phone')
                ->label('Telefono')
                ->tel(),
            Forms\Components\DatePicker::make('birthday')
                ->label('Cumpleanos'),
        ];
    }

    public function saveCustomer(): void
    {
        $data = $this->customerData;
        
        RestaurantCustomer::updateOrCreate(
            ['restaurant_id' => $this->restaurant->id, 'email' => $data['email']],
            [
                'name' => $data['name'] ?? null,
                'phone' => $data['phone'] ?? null,
                'birthday' => $data['birthday'] ?? null,
                'source' => 'manual',
                'email_subscribed' => true,
                'subscribed_at' => now(),
            ]
        );

        $this->showCustomerForm = false;
        $this->customerData = [];
        $this->loadStats();
        
        Notification::make()->title('Cliente guardado')->success()->send();
    }

    public function getCustomersQuery(): Builder
    {
        return RestaurantCustomer::query()
            ->where('restaurant_id', $this->restaurant?->id ?? 0)
            ->latest();
    }

    public function toggleSubscription(int $customerId): void
    {
        $customer = RestaurantCustomer::find($customerId);
        if ($customer && $customer->restaurant_id === $this->restaurant->id) {
            $customer->update(['email_subscribed' => !$customer->email_subscribed]);
            $this->loadStats();
        }
    }

    public function deleteCustomer(int $customerId): void
    {
        RestaurantCustomer::where('id', $customerId)
            ->where('restaurant_id', $this->restaurant->id)
            ->delete();
        $this->loadStats();
        Notification::make()->title('Cliente eliminado')->success()->send();
    }

    public function processImport(): void
    {
        if (!$this->csvFile) {
            Notification::make()->title('Selecciona un archivo')->warning()->send();
            return;
        }

        try {
            $csv = Reader::createFromPath($this->csvFile->getRealPath());
            $csv->setHeaderOffset(0);
            
            $imported = 0;
            foreach ($csv->getRecords() as $record) {
                $email = $record['email'] ?? $record['Email'] ?? null;
                if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) continue;
                
                RestaurantCustomer::updateOrCreate(
                    ['restaurant_id' => $this->restaurant->id, 'email' => $email],
                    [
                        'name' => $record['name'] ?? $record['Name'] ?? null,
                        'phone' => $record['phone'] ?? $record['Phone'] ?? null,
                        'source' => 'import',
                        'email_subscribed' => true,
                        'subscribed_at' => now(),
                    ]
                );
                $imported++;
            }
            
            $this->showImportForm = false;
            $this->csvFile = null;
            $this->loadStats();
            Notification::make()->title("{$imported} clientes importados")->success()->send();
        } catch (\Exception $e) {
            Notification::make()->title('Error: ' . $e->getMessage())->danger()->send();
        }
    }

    public static function shouldRegisterNavigation(): bool
    {
        $user = Auth::user();
        if (!$user) return false;
        $restaurant = $user->restaurants()->first();
        return $restaurant && $restaurant->is_claimed;
    }
    
    public static function getNavigationBadge(): ?string
    {
        $user = Auth::user();
        if (!$user) return null;
        $restaurant = $user->restaurants()->first();
        if ($restaurant && !in_array($restaurant->subscription_plan, ['premium', 'elite'])) {
            return 'PRO';
        }
        return null;
    }
    
    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }
}
