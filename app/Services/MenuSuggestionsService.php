<?php

namespace App\Services;

use App\Models\Restaurant;
use App\Models\MenuItem;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class MenuSuggestionsService
{
    protected ?string $apiKey = null;
    protected string $model = "claude-sonnet-4-20250514";
    
    public function __construct()
    {
        $this->apiKey = config("services.anthropic.api_key") ?: env("ANTHROPIC_API_KEY") ?: null;
    }
    
    public function generateSuggestions(Restaurant $restaurant, string $type = "general"): array
    {
        $cacheKey = "menu_suggestions_{$restaurant->id}_{$type}_" . now()->format("Y-m-d");
        
        return Cache::remember($cacheKey, 3600, function () use ($restaurant, $type) {
            return $this->callAI($restaurant, $type);
        });
    }
    
    public function generateFreshSuggestions(Restaurant $restaurant, string $type = "general"): array
    {
        $cacheKey = "menu_suggestions_{$restaurant->id}_{$type}_" . now()->format("Y-m-d");
        Cache::forget($cacheKey);
        
        return $this->generateSuggestions($restaurant, $type);
    }
    
    protected function callAI(Restaurant $restaurant, string $type): array
    {
        if (empty($this->apiKey)) {
            return $this->getFallbackSuggestions($restaurant, $type);
        }
        
        $prompt = $this->buildPrompt($restaurant, $type);
        
        try {
            $response = Http::withHeaders([
                "x-api-key" => $this->apiKey,
                "anthropic-version" => "2023-06-01",
                "content-type" => "application/json",
            ])->timeout(30)->post("https://api.anthropic.com/v1/messages", [
                "model" => $this->model,
                "max_tokens" => 1500,
                "messages" => [["role" => "user", "content" => $prompt]]
            ]);
            
            if ($response->successful()) {
                $content = $response->json()["content"][0]["text"] ?? "";
                return $this->parseResponse($content, $type);
            }
            
            return $this->getFallbackSuggestions($restaurant, $type);
            
        } catch (\Exception $e) {
            Log::error("AI Menu Suggestions exception", ["error" => $e->getMessage()]);
            return $this->getFallbackSuggestions($restaurant, $type);
        }
    }
    
    protected function buildPrompt(Restaurant $restaurant, string $type): string
    {
        $category = $restaurant->category->name ?? "Mexicano";
        $state = $restaurant->state->name ?? "Unknown";
        $city = $restaurant->city ?? "Unknown";
        $currentItems = $restaurant->menuItems()->take(20)->pluck("name")->toArray();
        $currentMenuText = !empty($currentItems) ? "Menu actual: " . implode(", ", $currentItems) : "Sin menu registrado.";
        $season = $this->getCurrentSeason();
        $month = now()->locale("es")->monthName;
        
        $basePrompt = "Eres un experto chef consultor en gastronomia mexicana. ";
        $basePrompt .= "Genera sugerencias para \"{$restaurant->name}\" en {$city}, {$state}. ";
        $basePrompt .= "Categoria: {$category}. {$currentMenuText}\n\n";
        
        switch ($type) {
            case "seasonal": $basePrompt .= "Genera 5 platillos de TEMPORADA para {$month} ({$season})."; break;
            case "trending": $basePrompt .= "Genera 5 platillos TENDENCIA populares en 2024-2025."; break;
            case "profitable": $basePrompt .= "Genera 5 platillos de ALTA RENTABILIDAD con estimacion de margen."; break;
            case "vegetarian": $basePrompt .= "Genera 5 opciones VEGETARIANAS/VEGANAS autenticas mexicanas."; break;
            default: $basePrompt .= "Genera 5 sugerencias que complementen su menu actual.";
        }
        
        $basePrompt .= "\n\nFormato JSON: {\"suggestions\": [{\"name\": \"Nombre\", \"description\": \"Descripcion\", \"price_suggestion\": \"150-200\", \"tip\": \"Consejo\"}]}";
        $basePrompt .= "\n\nResponde SOLO con JSON.";
        
        return $basePrompt;
    }
    
    protected function parseResponse(string $content, string $type): array
    {
        try {
            $content = trim($content);
            if (preg_match("/\{[\s\S]*\}/", $content, $matches)) {
                $json = json_decode($matches[0], true);
                if (json_last_error() === JSON_ERROR_NONE && isset($json["suggestions"])) {
                    return ["success" => true, "type" => $type, "generated_at" => now()->toDateTimeString(), "suggestions" => $json["suggestions"]];
                }
            }
        } catch (\Exception $e) {}
        
        return $this->getFallbackSuggestions(null, $type);
    }
    
    protected function getFallbackSuggestions(?Restaurant $restaurant, string $type): array
    {
        $suggestions = [
            "general" => [
                ["name" => "Tacos de Birria", "description" => "Tacos de res estilo Jalisco con consome", "price_suggestion" => "180-220", "tip" => "Servir con cebolla y cilantro"],
                ["name" => "Enchiladas Suizas", "description" => "Con salsa verde cremosa y queso gratinado", "price_suggestion" => "160-200", "tip" => "Usar crema fresca"],
                ["name" => "Pozole Rojo", "description" => "Caldo tradicional con maiz y cerdo", "price_suggestion" => "150-180", "tip" => "Ofrecer tostadas aparte"],
                ["name" => "Chiles en Nogada", "description" => "Chile poblano con picadillo y nogada", "price_suggestion" => "250-320", "tip" => "Ideal temporada patria"],
                ["name" => "Molcajete Mixto", "description" => "Carnes en molcajete con queso fundido", "price_suggestion" => "380-450", "tip" => "Para compartir"],
            ],
            "seasonal" => [
                ["name" => "Sopa de Flor de Calabaza", "description" => "Crema de flor de calabaza con elote", "price_suggestion" => "130-160", "tip" => "Decorar con flores"],
                ["name" => "Tacos de Huitlacoche", "description" => "Huitlacoche con epazote en tortilla azul", "price_suggestion" => "170-200", "tip" => "Oro negro mexicano"],
                ["name" => "Ensalada de Nopales", "description" => "Nopales con queso panela y vinagreta", "price_suggestion" => "120-150", "tip" => "Nopales tiernos"],
                ["name" => "Chayotes Rellenos", "description" => "Gratinados con queso y crema", "price_suggestion" => "140-170", "tip" => "Opcion vegetariana"],
                ["name" => "Agua de Jamaica", "description" => "Refrescante con chia", "price_suggestion" => "45-60", "tip" => "Alto margen"],
            ],
            "trending" => [
                ["name" => "Birria Ramen", "description" => "Fusion ramen japones con birria", "price_suggestion" => "220-280", "tip" => "Viral en redes"],
                ["name" => "Taco Smash Burger", "description" => "Hamburguesa smash en tortilla gigante", "price_suggestion" => "180-220", "tip" => "Tendencia 2024"],
                ["name" => "Elote Gourmet", "description" => "Esquites premium con toppings", "price_suggestion" => "90-120", "tip" => "Toppings extra"],
                ["name" => "Michelada Premium", "description" => "Con camaron, pulpo y aguacate", "price_suggestion" => "180-250", "tip" => "Instagrameable"],
                ["name" => "Churros Rellenos", "description" => "Con cajeta o nutella", "price_suggestion" => "80-120", "tip" => "Para compartir"],
            ],
            "profitable" => [
                ["name" => "Quesadillas de Chicharron", "description" => "Con salsa verde", "price_suggestion" => "100-140", "tip" => "Margen 70%"],
                ["name" => "Sopa de Tortilla", "description" => "Con aguacate y tiras", "price_suggestion" => "110-140", "tip" => "Margen 65%"],
                ["name" => "Tostadas de Tinga", "description" => "Pollo en salsa chipotle", "price_suggestion" => "80-110", "tip" => "Margen 60%"],
                ["name" => "Flan Napolitano", "description" => "Casero con caramelo", "price_suggestion" => "70-90", "tip" => "Margen 75%"],
                ["name" => "Aguas Frescas", "description" => "Jamaica, horchata, tamarindo", "price_suggestion" => "40-55", "tip" => "Margen 80%"],
            ],
            "vegetarian" => [
                ["name" => "Tacos de Coliflor al Pastor", "description" => "Coliflor marinada con pina", "price_suggestion" => "140-170", "tip" => "Alternativa vegana"],
                ["name" => "Enfrijoladas de Queso", "description" => "Tortillas con frijol y queso", "price_suggestion" => "120-150", "tip" => "Clasico vegetariano"],
                ["name" => "Sopes de Hongos", "description" => "Con hongos silvestres", "price_suggestion" => "130-160", "tip" => "Variedad de hongos"],
                ["name" => "Tamales de Rajas", "description" => "Rajas con queso", "price_suggestion" => "50-70", "tip" => "Tradicional"],
                ["name" => "Buddha Bowl Mexicano", "description" => "Arroz, frijoles, aguacate", "price_suggestion" => "160-200", "tip" => "Saludable"],
            ],
        ];
        
        return [
            "success" => true,
            "type" => $type,
            "generated_at" => now()->toDateTimeString(),
            "source" => "fallback",
            "suggestions" => $suggestions[$type] ?? $suggestions["general"]
        ];
    }
    
    protected function getCurrentSeason(): string
    {
        $month = now()->month;
        if ($month >= 3 && $month <= 5) return "Primavera";
        if ($month >= 6 && $month <= 8) return "Verano";
        if ($month >= 9 && $month <= 11) return "Otono";
        return "Invierno";
    }
    
    public static function getSuggestionTypes(): array
    {
        return [
            "general" => "Sugerencias Generales",
            "seasonal" => "De Temporada",
            "trending" => "Tendencias",
            "profitable" => "Alta Rentabilidad",
            "vegetarian" => "Vegetarianas/Veganas",
        ];
    }
}