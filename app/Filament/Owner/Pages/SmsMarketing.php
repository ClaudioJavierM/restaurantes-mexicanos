<?php

namespace App\Filament\Owner\Pages;

use App\Models\SmsAutomation;
use App\Models\SmsLog;
use App\Models\RestaurantCustomer;
use App\Services\SmsAutomationService;
use Filament\Pages\Page;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Tables;
use Illuminate\Support\Facades\Auth;

class SmsMarketing extends Page implements HasForms, HasTable
{
    use InteractsWithForms, InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-device-phone-mobile';
    protected static ?string $navigationLabel = 'SMS Marketing';
    protected static ?string $title = 'SMS Marketing';
    protected static ?string $navigationGroup = 'Mi Negocio';
    protected static ?int $navigationSort = 7;

    protected static string $view = 'filament.owner.pages.sms-marketing';

    public ?array $automationData = [];
    public $restaurant;
    public bool $showAutomationForm = false;
    public ?SmsAutomation $editingAutomation = null;
    public string $activeTab = 'automations';

    // Stats
    public int $totalSent = 0;
    public int $totalDelivered = 0;
    public int $totalClicked = 0;
    public float $clickRate = 0;
    public int $subscribedCustomers = 0;

    public static function shouldRegisterNavigation(): bool
    {
        $user = auth()->user();
        if (!$user) return false;
        $teamMember = \App\Models\RestaurantTeamMember::where('user_id', $user->id)
            ->where('status', 'active')->first();
        if ($teamMember && $teamMember->role !== 'admin') {
            $permissions = $teamMember->permissions ?? [];
            if (!($permissions['marketing'] ?? false)) {
                return false;
            }
        }
        return true;
    }

    public static function canAccess(): bool
    {
        return static::shouldRegisterNavigation();
    }

    public function mount(): void
    {
        $this->restaurant = Auth::user()->allAccessibleRestaurants()->first();
        $this->loadStats();
    }

    public function loadStats(): void
    {
        if (!$this->restaurant) return;

        $thirtyDaysAgo = now()->subDays(30);

        $logs = SmsLog::where('restaurant_id', $this->restaurant->id)
            ->where('created_at', '>=', $thirtyDaysAgo);

        $this->totalSent = (clone $logs)->sent()->count();
        $this->totalDelivered = (clone $logs)->where('status', 'delivered')->count();
        $this->totalClicked = (clone $logs)->where('status', 'clicked')->count();
        $this->clickRate = $this->totalSent > 0 
            ? round(($this->totalClicked / $this->totalSent) * 100, 1) 
            : 0;

        $this->subscribedCustomers = RestaurantCustomer::where('restaurant_id', $this->restaurant->id)
            ->where('sms_subscribed', true)
            ->count();
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                SmsAutomation::query()
                    ->where('restaurant_id', $this->restaurant?->id ?? 0)
                    ->latest()
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable(),
                Tables\Columns\BadgeColumn::make('trigger_type')
                    ->label('Trigger')
                    ->formatStateUsing(fn (string $state) => SmsAutomation::triggerTypes()[$state] ?? $state)
                    ->colors([
                        'danger' => 'abandoned_cart',
                        'warning' => 'winback',
                        'success' => 'birthday',
                        'info' => 'loyalty_milestone',
                        'primary' => 'post_order',
                        'gray' => 'welcome',
                    ]),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Activo')
                    ->boolean(),
                Tables\Columns\TextColumn::make('sends_count')
                    ->label('Enviados')
                    ->numeric(),
                Tables\Columns\TextColumn::make('click_rate')
                    ->label('Click Rate')
                    ->formatStateUsing(fn ($record) => $record->click_rate . '%'),
                Tables\Columns\TextColumn::make('delay_minutes')
                    ->label('Delay')
                    ->formatStateUsing(fn (int $state) => $state < 60 ? "{$state} min" : round($state / 60) . " hrs"),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('trigger_type')
                    ->label('Trigger')
                    ->options(SmsAutomation::triggerTypes()),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Activo'),
            ])
            ->actions([
                Tables\Actions\Action::make('toggle')
                    ->label(fn (SmsAutomation $record) => $record->is_active ? 'Desactivar' : 'Activar')
                    ->icon(fn (SmsAutomation $record) => $record->is_active ? 'heroicon-o-pause' : 'heroicon-o-play')
                    ->color(fn (SmsAutomation $record) => $record->is_active ? 'danger' : 'success')
                    ->action(function (SmsAutomation $record) {
                        $record->update(['is_active' => !$record->is_active]);
                        Notification::make()
                            ->title($record->is_active ? 'Automación activada' : 'Automación desactivada')
                            ->success()
                            ->send();
                    }),
                Tables\Actions\Action::make('edit')
                    ->label('Editar')
                    ->icon('heroicon-o-pencil')
                    ->action(fn (SmsAutomation $record) => $this->editAutomation($record)),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public function getAutomationForm(): array
    {
        return [
            Forms\Components\TextInput::make('name')
                ->label('Nombre de la Automación')
                ->required()
                ->maxLength(255),

            Forms\Components\Select::make('trigger_type')
                ->label('Trigger (Cuándo enviar)')
                ->options(SmsAutomation::triggerTypes())
                ->required()
                ->reactive()
                ->afterStateUpdated(fn ($state) => $this->loadDefaultTemplate($state)),

            Forms\Components\Textarea::make('message_template')
                ->label('Mensaje SMS')
                ->required()
                ->rows(4)
                ->maxLength(320)
                ->helperText('Variables: {customer_name}, {restaurant_name}, {cart_total}, {points}, {coupon_code}, {coupon_discount}, {order_url}')
                ->hint(fn ($state) => strlen($state ?? '') . '/320 caracteres'),

            Forms\Components\Grid::make(2)->schema([
                Forms\Components\TextInput::make('delay_minutes')
                    ->label('Delay (minutos)')
                    ->numeric()
                    ->default(15)
                    ->required()
                    ->helperText('Ej: 15 = 15 min, 1440 = 24 hrs'),

                Forms\Components\Toggle::make('is_active')
                    ->label('Activar inmediatamente')
                    ->default(true),
            ]),

            Forms\Components\Section::make('Cupón (Opcional)')
                ->collapsed()
                ->schema([
                    Forms\Components\Grid::make(3)->schema([
                        Forms\Components\TextInput::make('coupon_code')
                            ->label('Código')
                            ->maxLength(20)
                            ->placeholder('VUELVE10'),

                        Forms\Components\TextInput::make('coupon_discount')
                            ->label('Descuento')
                            ->numeric()
                            ->placeholder('10'),

                        Forms\Components\Select::make('coupon_type')
                            ->label('Tipo')
                            ->options([
                                'percent' => 'Porcentaje (%)',
                                'fixed' => 'Monto fijo ($)',
                            ])
                            ->default('percent'),
                    ]),
                ]),
        ];
    }

    public function loadDefaultTemplate(?string $triggerType): void
    {
        if ($triggerType && isset(SmsAutomation::defaultTemplates()[$triggerType])) {
            $this->automationData['message_template'] = SmsAutomation::defaultTemplates()[$triggerType];
        }
    }

    public function createAutomation(): void
    {
        $this->editingAutomation = null;
        $this->automationData = [
            'delay_minutes' => 15,
            'is_active' => true,
        ];
        $this->showAutomationForm = true;
    }

    public function editAutomation(SmsAutomation $automation): void
    {
        $this->editingAutomation = $automation;
        $this->automationData = $automation->toArray();
        $this->showAutomationForm = true;
    }

    public function saveAutomation(): void
    {
        $data = $this->automationData;
        $data['restaurant_id'] = $this->restaurant->id;

        if ($this->editingAutomation) {
            $this->editingAutomation->update($data);
            $message = 'Automación actualizada';
        } else {
            SmsAutomation::create($data);
            $message = 'Automación creada';
        }

        $this->showAutomationForm = false;
        $this->automationData = [];
        $this->editingAutomation = null;

        Notification::make()
            ->title($message)
            ->success()
            ->send();
    }

    public function cancelForm(): void
    {
        $this->showAutomationForm = false;
        $this->automationData = [];
        $this->editingAutomation = null;
    }

    public function sendTestSms(): void
    {
        $user = Auth::user();
        
        if (empty($user->phone)) {
            Notification::make()
                ->title('Error')
                ->body('No tienes número de teléfono configurado')
                ->danger()
                ->send();
            return;
        }

        try {
            $service = app(SmsAutomationService::class);
            $service->sendTransactionalSms(
                $this->restaurant,
                $user->phone,
                "🧪 SMS de prueba desde {$this->restaurant->name}\n\nTu sistema de SMS está funcionando correctamente.\n\nReply STOP to unsubscribe",
                'test'
            );

            Notification::make()
                ->title('SMS de prueba enviado')
                ->body("Enviado a {$user->phone}")
                ->success()
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->title('Error al enviar')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function getRecentLogsProperty()
    {
        return SmsLog::where('restaurant_id', $this->restaurant?->id ?? 0)
            ->with('customer')
            ->latest()
            ->limit(10)
            ->get();
    }
}
