<?php

namespace App\Filament\Owner\Pages;

use Filament\Pages\Page;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class ChatbotSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';
    protected static ?string $navigationLabel = 'Chatbot IA';
    protected static ?string $title = 'Configuración del Chatbot';
    protected static ?string $navigationGroup = 'Contenido';
    protected static ?int $navigationSort = 3;
    
    protected static string $view = 'filament.owner.pages.chatbot-settings';

    public ?array $data = [];
    public $isPremium = false;
    public $chatStats = [];
    public $restaurantName = '';
    public $hasRestaurant = false;

    protected function getRestaurant()
    {
        return Auth::user()?->firstAccessibleRestaurant();
    }

    public function mount(): void
    {
        $restaurant = $this->getRestaurant();
        $this->hasRestaurant = (bool) $restaurant;

        if ($restaurant) {
            $this->restaurantName = $restaurant->name;
            $this->isPremium = in_array($restaurant->subscription_tier, ['premium', 'elite']);

            $this->chatStats = [
                'conversations' => 0,
                'messages' => 0,
                'satisfaction' => 0,
            ];

            // Load saved settings or use defaults
            $saved = $restaurant->chatbot_settings ?? [];

            $this->form->fill([
                'chatbot_enabled' => $saved['chatbot_enabled'] ?? true,
                'chatbot_welcome_es' => $saved['chatbot_welcome_es'] ?? '¡Hola! Soy el asistente virtual de ' . $restaurant->name . '. ¿En qué puedo ayudarte?',
                'chatbot_welcome_en' => $saved['chatbot_welcome_en'] ?? 'Hello! I am the virtual assistant of ' . $restaurant->name . '. How can I help you?',
                'chatbot_hours_response' => $saved['chatbot_hours_response'] ?? true,
                'chatbot_menu_response' => $saved['chatbot_menu_response'] ?? true,
                'chatbot_reservations_response' => $saved['chatbot_reservations_response'] ?? true,
                'chatbot_directions_response' => $saved['chatbot_directions_response'] ?? true,
                'chatbot_custom_faqs' => $saved['chatbot_custom_faqs'] ?? [],
            ]);
        }
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Estado del Chatbot')
                    ->schema([
                        Forms\Components\Toggle::make('chatbot_enabled')
                            ->label('Chatbot Activo')
                            ->helperText('Activa el chatbot en la página de tu restaurante')
                            ->live(),
                    ]),

                Forms\Components\Section::make('Mensaje de Bienvenida')
                    ->description('Personaliza el saludo inicial del chatbot')
                    ->schema([
                        Forms\Components\Textarea::make('chatbot_welcome_es')
                            ->label('Saludo en Español')
                            ->rows(2)
                            ->maxLength(500),

                        Forms\Components\Textarea::make('chatbot_welcome_en')
                            ->label('Greeting in English')
                            ->rows(2)
                            ->maxLength(500),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Respuestas Automáticas')
                    ->description('El chatbot responderá automáticamente a estas preguntas')
                    ->schema([
                        Forms\Components\Toggle::make('chatbot_hours_response')
                            ->label('🕐 Horarios de Apertura')
                            ->helperText('Responde preguntas sobre horarios'),

                        Forms\Components\Toggle::make('chatbot_menu_response')
                            ->label('🍽️ Información del Menú')
                            ->helperText('Responde preguntas sobre platillos y precios'),

                        Forms\Components\Toggle::make('chatbot_reservations_response')
                            ->label('📅 Reservaciones')
                            ->helperText('Ayuda a los clientes a hacer reservaciones'),

                        Forms\Components\Toggle::make('chatbot_directions_response')
                            ->label('📍 Ubicación y Direcciones')
                            ->helperText('Proporciona indicaciones para llegar'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Preguntas Frecuentes Personalizadas')
                    ->description('Agrega respuestas a preguntas específicas de tu restaurante')
                    ->schema([
                        Forms\Components\Repeater::make('chatbot_custom_faqs')
                            ->label('')
                            ->schema([
                                Forms\Components\TextInput::make('question')
                                    ->label('Pregunta')
                                    ->placeholder('¿Tienen opciones vegetarianas?')
                                    ->required(),

                                Forms\Components\Textarea::make('answer')
                                    ->label('Respuesta')
                                    ->placeholder('Sí, ofrecemos varias opciones vegetarianas...')
                                    ->rows(2)
                                    ->required(),
                            ])
                            ->columns(1)
                            ->addActionLabel('Agregar Pregunta')
                            ->maxItems(10)
                            ->collapsible()
                            ->defaultItems(0),
                    ])
                    ->collapsed(),
            ])
            ->statePath('data');
    }

    public function saveSettings(): void
    {
        $data = $this->form->getState();

        $restaurant = $this->getRestaurant();

        if ($restaurant) {
            $restaurant->update([
                'chatbot_settings' => $data,
            ]);

            Notification::make()
                ->title('Configuración guardada')
                ->body('La configuración del chatbot ha sido actualizada.')
                ->success()
                ->send();
        }
    }

    public static function shouldRegisterNavigation(): bool
    {
        $user = Auth::user();
        if (!$user) return false;

        $restaurant = $user->firstAccessibleRestaurant();
        return $restaurant && $restaurant->is_claimed;
    }

    public static function getNavigationBadge(): ?string
    {
        $user = Auth::user();
        if (!$user) return null;

        $restaurant = $user->firstAccessibleRestaurant();
        if ($restaurant && !in_array($restaurant->subscription_tier, ['premium', 'elite'])) {
            return 'PRO';
        }
        return null;
    }
    
    public static function getNavigationBadgeColor(): ?string
    {
        return "warning";
    }
}
