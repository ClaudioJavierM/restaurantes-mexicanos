<?php

namespace App\Livewire;

use App\Models\Restaurant;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Component;

class RestaurantChatbot extends Component
{
    public Restaurant $restaurant;
    public array $messages = [];
    public string $userMessage = '';
    public bool $isOpen = false;
    public bool $isLoading = false;
    public string $locale = 'es';

    public function mount(Restaurant $restaurant)
    {
        $this->restaurant = $restaurant;
        $this->locale = app()->getLocale() === 'en' ? 'en' : 'es';

        $welcome = $this->locale === 'en'
            ? "Hi! I'm the virtual assistant for {$restaurant->name}. How can I help you? You can ask me about our menu, hours, location, or reservations."
            : "¡Hola! Soy el asistente virtual de {$restaurant->name}. ¿En qué puedo ayudarte? Puedes preguntarme sobre nuestro menú, horarios, ubicación o reservaciones.";

        $this->messages = [
            ['role' => 'assistant', 'content' => $welcome],
        ];
    }

    public function toggleChat()
    {
        $this->isOpen = !$this->isOpen;
    }

    public function sendMessage()
    {
        $message = trim($this->userMessage);
        if (empty($message)) return;

        // Rate limit: 10 messages per minute per IP
        $key = 'chatbot:' . request()->ip() . ':' . $this->restaurant->id;
        if (RateLimiter::tooManyAttempts($key, 10)) {
            $retryAfter = RateLimiter::availableIn($key);
            $errorMsg = $this->locale === 'en'
                ? "Too many messages. Please wait {$retryAfter} seconds."
                : "Demasiados mensajes. Por favor espera {$retryAfter} segundos.";
            $this->messages[] = ['role' => 'assistant', 'content' => $errorMsg];
            return;
        }
        RateLimiter::hit($key, 60);

        $this->messages[] = ['role' => 'user', 'content' => $message];
        $this->userMessage = '';
        $this->isLoading = true;

        try {
            $response = $this->getAIResponse($message);
            $this->messages[] = ['role' => 'assistant', 'content' => $response];
        } catch (\Exception $e) {
            Log::error('Chatbot error: ' . $e->getMessage());
            $errorMsg = $this->locale === 'en'
                ? "Sorry, I couldn't process your request. Please try again."
                : "Lo siento, no pude procesar tu solicitud. Por favor intenta de nuevo.";
            $this->messages[] = ['role' => 'assistant', 'content' => $errorMsg];
        }

        $this->isLoading = false;
    }

    protected function getAIResponse(string $userMessage): string
    {
        $apiKey = config('services.anthropic.api_key');

        if (!$apiKey) {
            return $this->getFallbackResponse($userMessage);
        }

        $context = $this->buildRestaurantContext();
        $systemPrompt = $this->buildSystemPrompt($context);

        // Build conversation history (last 10 messages for context)
        $conversationMessages = [];
        $recentMessages = array_slice($this->messages, -10);
        foreach ($recentMessages as $msg) {
            $conversationMessages[] = [
                'role' => $msg['role'],
                'content' => $msg['content'],
            ];
        }
        $conversationMessages[] = ['role' => 'user', 'content' => $userMessage];

        $response = Http::withHeaders([
            'x-api-key' => $apiKey,
            'anthropic-version' => '2023-06-01',
            'content-type' => 'application/json',
        ])->timeout(15)->post('https://api.anthropic.com/v1/messages', [
            'model' => 'claude-haiku-4-5-20251001',
            'max_tokens' => 300,
            'system' => $systemPrompt,
            'messages' => $conversationMessages,
        ]);

        if ($response->successful()) {
            $data = $response->json();
            return $data['content'][0]['text'] ?? $this->getFallbackResponse($userMessage);
        }

        Log::error('Claude API error: ' . $response->body());
        return $this->getFallbackResponse($userMessage);
    }

    protected function buildRestaurantContext(): array
    {
        $r = $this->restaurant;
        $context = [
            'name' => $r->name,
            'address' => $r->address . ', ' . $r->city . ', ' . ($r->state?->name ?? '') . ' ' . $r->zip_code,
            'phone' => $r->phone,
            'website' => $r->website,
            'description' => $r->description,
            'category' => $r->category?->name,
        ];

        // Hours
        $hours = $r->hours ?? $r->opening_hours ?? $r->yelp_hours;
        if ($hours) {
            $context['hours'] = $hours;
        }

        // Menu items (top 20)
        $menuItems = $r->availableMenuItems()->take(20)->get();
        if ($menuItems->count() > 0) {
            $context['menu'] = $menuItems->map(fn($item) => [
                'name' => $item->name,
                'price' => $item->price ? '$' . number_format($item->price, 2) : null,
                'description' => $item->description,
                'category' => $item->category?->name,
            ])->toArray();
        }

        // Amenities
        $amenities = [];
        if ($r->accepts_reservations) $amenities[] = 'Accepts reservations';
        if ($r->has_delivery) $amenities[] = 'Delivery available';
        if ($r->has_takeout) $amenities[] = 'Takeout available';
        if ($r->has_parking) $amenities[] = 'Parking available';
        if ($r->has_wifi) $amenities[] = 'WiFi available';
        if ($r->alcohol_served) $amenities[] = 'Serves alcohol';
        if ($r->has_outdoor_seating) $amenities[] = 'Outdoor seating';
        if (!empty($amenities)) {
            $context['amenities'] = $amenities;
        }

        // Price range
        if ($r->price_range) {
            $context['price_range'] = $r->price_range;
        }

        // Rating
        if ($r->rating) {
            $context['rating'] = $r->rating . '/5';
        }

        return $context;
    }

    protected function buildSystemPrompt(array $context): string
    {
        $lang = $this->locale === 'en' ? 'English' : 'Spanish';
        $contextJson = json_encode($context, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

        return <<<PROMPT
You are a friendly virtual assistant for the restaurant "{$context['name']}".
Respond in {$lang}. Be concise (2-3 sentences max). Be helpful and warm.

IMPORTANT RULES:
- Only answer questions about THIS restaurant using the data below.
- If asked something you don't know, politely say you don't have that information and suggest calling the restaurant.
- Never make up information not in the data.
- If asked about reservations and the restaurant accepts them, encourage them to use the reservation form on the page.
- For directions, provide the address and suggest using Google Maps.
- Keep responses short and conversational.

RESTAURANT DATA:
{$contextJson}
PROMPT;
    }

    protected function getFallbackResponse(string $message): string
    {
        $message = mb_strtolower($message);
        $r = $this->restaurant;
        $isEn = $this->locale === 'en';

        // Hours
        if (preg_match('/horar|hours|open|cierra|abre|when/i', $message)) {
            $hours = $r->hours ?? $r->opening_hours ?? $r->yelp_hours;
            if ($hours && is_array($hours)) {
                $formatted = [];
                $dayNames = $isEn
                    ? ['monday' => 'Mon', 'tuesday' => 'Tue', 'wednesday' => 'Wed', 'thursday' => 'Thu', 'friday' => 'Fri', 'saturday' => 'Sat', 'sunday' => 'Sun']
                    : ['monday' => 'Lun', 'tuesday' => 'Mar', 'wednesday' => 'Mié', 'thursday' => 'Jue', 'friday' => 'Vie', 'saturday' => 'Sáb', 'sunday' => 'Dom'];
                foreach ($hours as $day => $info) {
                    $dayLabel = $dayNames[strtolower($day)] ?? $day;
                    if (is_array($info)) {
                        $open = $info['open'] ?? $info['start'] ?? '';
                        $close = $info['close'] ?? $info['end'] ?? '';
                        if ($open && $close) {
                            $formatted[] = "{$dayLabel}: {$open} - {$close}";
                        }
                    } elseif (is_string($info)) {
                        $formatted[] = "{$dayLabel}: {$info}";
                    }
                }
                if (!empty($formatted)) {
                    $header = $isEn ? "Our hours are:" : "Nuestros horarios son:";
                    return $header . "\n" . implode("\n", $formatted);
                }
            }
            return $isEn
                ? "I don't have the exact hours available. Please call us at {$r->phone} for current hours."
                : "No tengo los horarios exactos disponibles. Por favor llámanos al {$r->phone} para confirmar horarios.";
        }

        // Location/directions
        if (preg_match('/donde|direc|ubicac|location|address|where|llegar|how to get/i', $message)) {
            $addr = $r->address . ', ' . $r->city . ', ' . ($r->state?->name ?? '') . ' ' . $r->zip_code;
            return $isEn
                ? "We're located at: {$addr}. You can get directions via Google Maps from the map on this page!"
                : "Estamos ubicados en: {$addr}. ¡Puedes obtener indicaciones desde el mapa en esta página!";
        }

        // Phone
        if (preg_match('/telef|phone|call|llam|contact/i', $message)) {
            return $isEn
                ? "You can reach us at: {$r->phone}"
                : "Puedes contactarnos al: {$r->phone}";
        }

        // Menu
        if (preg_match('/menu|plat|comida|food|dish|eat|precio|price/i', $message)) {
            $items = $r->availableMenuItems()->take(5)->get();
            if ($items->count() > 0) {
                $header = $isEn ? "Here are some of our dishes:" : "Aquí algunos de nuestros platillos:";
                $list = $items->map(fn($i) => "• {$i->name}" . ($i->price ? " - \${$i->price}" : ''))->implode("\n");
                $footer = $isEn ? "Check the Menu tab on this page for the full menu!" : "¡Revisa la pestaña de Menú en esta página para ver el menú completo!";
                return "{$header}\n{$list}\n{$footer}";
            }
            return $isEn
                ? "Please check the Menu tab on this page or contact us at {$r->phone} for menu information."
                : "Por favor revisa la pestaña de Menú en esta página o contáctanos al {$r->phone} para información del menú.";
        }

        // Reservations
        if (preg_match('/reserv|book|mesa|table/i', $message)) {
            if ($r->accepts_reservations) {
                return $isEn
                    ? "Yes, we accept reservations! You can use the reservation form on this page to book your table."
                    : "¡Sí, aceptamos reservaciones! Puedes usar el formulario de reservaciones en esta página para reservar tu mesa.";
            }
            return $isEn
                ? "Please call us at {$r->phone} to inquire about reservations."
                : "Por favor llámanos al {$r->phone} para consultar sobre reservaciones.";
        }

        // Default
        return $isEn
            ? "I'm here to help! You can ask me about our menu, hours, location, or reservations. For specific questions, call us at {$r->phone}."
            : "¡Estoy aquí para ayudarte! Puedes preguntarme sobre nuestro menú, horarios, ubicación o reservaciones. Para preguntas específicas, llámanos al {$r->phone}.";
    }

    public function render()
    {
        return view('livewire.restaurant-chatbot');
    }
}
