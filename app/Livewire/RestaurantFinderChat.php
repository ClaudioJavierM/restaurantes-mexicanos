<?php

namespace App\Livewire;

use App\Models\Restaurant;
use App\Models\MenuItem;
use App\Models\Category;
use App\Services\CountryContext;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;

class RestaurantFinderChat extends Component
{
    public bool $isOpen = false;
    public bool $showBubble = false;
    public array $messages = [];
    public string $userInput = "";
    public ?string $userCity = null;
    public ?string $userState = null;
    public ?float $userLat = null;
    public ?float $userLng = null;
    public bool $isTyping = false;
    public string $country;
    
    protected $listeners = ["setUserLocation"];
    
    public function mount()
    {
        $this->country = CountryContext::getCountry();
        $this->messages = [];
    }
    
    public function setUserLocation($lat, $lng, $city = null, $state = null)
    {
        $this->userLat = $lat;
        $this->userLng = $lng;
        $this->userCity = $city;
        $this->userState = $state;
    }
    
    public function showChat()
    {
        $this->showBubble = true;
    }
    
    public function openChat()
    {
        $this->isOpen = true;
        
        if (empty($this->messages)) {
            $greeting = $this->country === "MX" 
                ? "¡Hola! Soy tu asistente para encontrar restaurantes mexicanos. ¿Qué tipo de comida te gustaría hoy?"
                : "¡Hola! Soy tu asistente para encontrar restaurantes mexicanos en USA. ¿Qué tipo de comida buscas hoy?";
            
            $this->addBotMessage($greeting);
            $this->addQuickReplies([
                "Tacos",
                "Mariscos",
                "Birria",
                "Comida Oaxaqueña",
                "Antojitos",
                "Ver todo cerca"
            ]);
        }
    }
    
    public function closeChat()
    {
        $this->isOpen = false;
    }
    
    public function sendMessage()
    {
        if (empty(trim($this->userInput))) return;
        
        $message = trim($this->userInput);
        $this->addUserMessage($message);
        $this->userInput = "";
        $this->isTyping = true;
        
        $this->processUserMessage($message);
        
        $this->isTyping = false;
    }
    
    public function selectQuickReply($reply)
    {
        $this->addUserMessage($reply);
        $this->isTyping = true;
        $this->processUserMessage($reply);
        $this->isTyping = false;
    }
    
    protected function processUserMessage(string $message)
    {
        $message = strtolower($message);
        
        // Detect intent
        if (str_contains($message, "cerca") || str_contains($message, "near") || str_contains($message, "todo")) {
            $this->findNearbyRestaurants();
        } elseif (str_contains($message, "taco")) {
            $this->findByFoodType("tacos", "Tacos");
        } elseif (str_contains($message, "birria")) {
            $this->findByFoodType("birria", "Birria");
        } elseif (str_contains($message, "marisco") || str_contains($message, "seafood")) {
            $this->findByFoodType("mariscos", "Mariscos");
        } elseif (str_contains($message, "oaxaq")) {
            $this->findByFoodType("oaxaca", "comida Oaxaqueña");
        } elseif (str_contains($message, "antoj") || str_contains($message, "street")) {
            $this->findByFoodType("antojitos", "Antojitos");
        } elseif (str_contains($message, "torta")) {
            $this->findByFoodType("tortas", "Tortas");
        } elseif (str_contains($message, "burrito")) {
            $this->findByFoodType("burritos", "Burritos");
        } elseif (str_contains($message, "pozole") || str_contains($message, "sopa")) {
            $this->findByFoodType("pozole sopa", "Sopas y Caldos");
        } elseif (str_contains($message, "menudo")) {
            $this->findByFoodType("menudo", "Menudo");
        } else {
            $this->findByFoodType($message, $message);
        }
    }
    
    protected function findNearbyRestaurants()
    {
        $query = Restaurant::where("status", "approved")
            ->where("country", $this->country)
            ->with(["category", "state"]);
        
        if ($this->userCity) {
            $query->where("city", "like", "%{$this->userCity}%");
        } elseif ($this->userState) {
            $query->whereHas("state", function($q) {
                $q->where("name", "like", "%{$this->userState}%")
                  ->orWhere("code", $this->userState);
            });
        }
        
        $restaurants = $query->orderByDesc("average_rating")
            ->limit(5)
            ->get();
        
        if ($restaurants->isEmpty()) {
            $restaurants = Restaurant::where("status", "approved")
                ->where("country", $this->country)
                ->orderByDesc("average_rating")
                ->limit(5)
                ->get();
        }
        
        $locationText = $this->userCity ? "en {$this->userCity}" : "cerca de ti";
        $this->addBotMessage("Estos son los mejores restaurantes mexicanos {$locationText}:");
        $this->addRestaurantCards($restaurants);
    }
    
    protected function findByFoodType(string $searchTerm, string $displayName)
    {
        // Search in menu items first
        $restaurantIds = MenuItem::where(function($q) use ($searchTerm) {
            $q->where("name", "like", "%{$searchTerm}%")
              ->orWhere("description", "like", "%{$searchTerm}%");
        })->pluck("restaurant_id")->unique()->toArray();
        
        // Also search in restaurant names and descriptions
        $query = Restaurant::where("status", "approved")
            ->where("country", $this->country);
        
        if (!empty($restaurantIds)) {
            $query->where(function($q) use ($restaurantIds, $searchTerm) {
                $q->whereIn("id", $restaurantIds)
                  ->orWhere("name", "like", "%{$searchTerm}%")
                  ->orWhere("description", "like", "%{$searchTerm}%");
            });
        } else {
            $query->where(function($q) use ($searchTerm) {
                $q->where("name", "like", "%{$searchTerm}%")
                  ->orWhere("description", "like", "%{$searchTerm}%");
            });
        }
        
        // Filter by location if available
        if ($this->userCity) {
            $query->where("city", "like", "%{$this->userCity}%");
        }
        
        $restaurants = $query->orderByDesc("average_rating")
            ->limit(5)
            ->get();
        
        if ($restaurants->isEmpty()) {
            // Fallback: show any restaurants in location with good ratings
            $this->addBotMessage("No encontré restaurantes con {$displayName} específicamente, pero aquí hay excelentes opciones:");
            $this->findNearbyRestaurants();
            return;
        }
        
        $currency = $this->country === "MX" ? "pesos" : "dólares";
        $locationText = $this->userCity ? "en {$this->userCity}" : "";
        $this->addBotMessage("¡Encontré {$restaurants->count()} restaurantes con {$displayName} {$locationText}! (Precios en {$currency})");
        $this->addRestaurantCards($restaurants);
        
        $this->addQuickReplies(["Ver más opciones", "Buscar otra cosa", "Mariscos", "Antojitos"]);
    }
    
    protected function addUserMessage(string $text)
    {
        $this->messages[] = [
            "type" => "user",
            "text" => $text,
            "time" => now()->format("H:i")
        ];
    }
    
    protected function addBotMessage(string $text)
    {
        $this->messages[] = [
            "type" => "bot",
            "text" => $text,
            "time" => now()->format("H:i")
        ];
    }
    
    protected function addRestaurantCards($restaurants)
    {
        $cards = [];
        foreach ($restaurants as $restaurant) {
            $price = $this->country === "MX" ? "$" . rand(80, 200) . "-" . rand(250, 400) : "$" . rand(8, 15) . "-" . rand(20, 35);
            
            // Get popular menu items
            $menuItems = $restaurant->menuItems()->where("is_popular", true)->limit(3)->pluck("name")->toArray();
            
            $cards[] = [
                "id" => $restaurant->id,
                "name" => $restaurant->name,
                "slug" => $restaurant->slug,
                "rating" => $restaurant->average_rating ?? 4.0,
                "reviews" => $restaurant->total_reviews ?? 0,
                "city" => $restaurant->city,
                "category" => $restaurant->category->name ?? "Mexicano",
                "image" => $restaurant->image 
                    ? (str_starts_with($restaurant->image, "http") ? $restaurant->image : asset("storage/" . $restaurant->image))
                    : asset("images/restaurant-placeholder.jpg"),
                "price_range" => $price,
                "popular_items" => $menuItems
            ];
        }
        
        $this->messages[] = [
            "type" => "restaurants",
            "cards" => $cards,
            "time" => now()->format("H:i")
        ];
    }
    
    protected function addQuickReplies(array $replies)
    {
        $this->messages[] = [
            "type" => "quick_replies",
            "replies" => $replies
        ];
    }
    
    public function render()
    {
        return view("livewire.restaurant-finder-chat");
    }
}