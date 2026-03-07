<?php

namespace App\Services;

use App\Models\MenuUpload;
use App\Models\MenuCategory;
use App\Models\MenuItem;
use App\Models\Restaurant;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MenuExtractionService
{
    protected string $ocrApiKey;
    protected string $openaiApiKey;

    public function __construct()
    {
        $this->ocrApiKey = config('services.ocr_space.api_key') ?? '';
        $this->openaiApiKey = config('services.openai.api_key') ?? '';
    }

    /**
     * Extract text from uploaded file using OCR
     */
    public function extractText(MenuUpload $upload): string
    {
        if ($upload->file_type === 'url') {
            return $this->extractFromUrl($upload->file_path);
        }

        $filePath = Storage::disk('public')->path($upload->file_path);
        
        if ($upload->file_type === 'pdf') {
            return $this->extractFromPdf($filePath);
        }
        
        return $this->extractFromImage($filePath);
    }

    /**
     * Extract text from PDF using OCR.space API
     */
    protected function extractFromPdf(string $filePath): string
    {
        $response = Http::withHeaders([
            'apikey' => $this->ocrApiKey,
        ])->attach(
            'file', file_get_contents($filePath), basename($filePath)
        )->post('https://api.ocr.space/parse/image', [
            'language' => 'spa',
            'isOverlayRequired' => 'false',
            'filetype' => 'PDF',
            'detectOrientation' => 'true',
            'scale' => 'true',
            'OCREngine' => '2',
        ]);

        $data = $response->json();

        if (!$response->successful() || !isset($data['ParsedResults'])) {
            throw new \Exception('Error en OCR: ' . ($data['ErrorMessage'] ?? 'Unknown error'));
        }

        return collect($data['ParsedResults'])
            ->pluck('ParsedText')
            ->implode("\n");
    }

    /**
     * Extract text from image using OCR.space API
     */
    protected function extractFromImage(string $filePath): string
    {
        $imageData = base64_encode(file_get_contents($filePath));
        $extension = pathinfo($filePath, PATHINFO_EXTENSION);
        $mimeType = match(strtolower($extension)) {
            'jpg', 'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'webp' => 'image/webp',
            default => 'image/jpeg',
        };

        $response = Http::withHeaders([
            'apikey' => $this->ocrApiKey,
        ])->post('https://api.ocr.space/parse/image', [
            'base64Image' => 'data:' . $mimeType . ';base64,' . $imageData,
            'language' => 'spa',
            'isOverlayRequired' => 'false',
            'detectOrientation' => 'true',
            'scale' => 'true',
            'OCREngine' => '2',
        ]);

        $data = $response->json();

        if (!$response->successful() || !isset($data['ParsedResults'])) {
            throw new \Exception('Error en OCR: ' . ($data['ErrorMessage'] ?? 'Unknown error'));
        }

        return collect($data['ParsedResults'])
            ->pluck('ParsedText')
            ->implode("\n");
    }

    /**
     * Extract text from URL (for digital menus)
     */
    protected function extractFromUrl(string $url): string
    {
        // Try to fetch and extract text from the URL
        $response = Http::get($url);
        
        if (!$response->successful()) {
            throw new \Exception('No se pudo acceder a la URL del menú');
        }

        $html = $response->body();
        
        // Strip HTML and extract text
        $text = strip_tags($html);
        $text = preg_replace('/\s+/', ' ', $text);
        
        return $text;
    }

    /**
     * Use OpenAI to structure the extracted text into menu data
     */
    public function structureMenuWithAI(string $rawText, Restaurant $restaurant): array
    {
        $prompt = $this->buildExtractionPrompt($rawText, $restaurant);

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->openaiApiKey,
            'Content-Type' => 'application/json',
        ])->post('https://api.openai.com/v1/chat/completions', [
            'model' => 'gpt-4o',
            'max_tokens' => 4096,
            'messages' => [
                [
                    'role' => 'user',
                    'content' => $prompt,
                ],
            ],
        ]);

        if (!$response->successful()) {
            throw new \Exception('Error en AI: ' . $response->body());
        }

        $data = $response->json();
        $content = $data['choices'][0]['message']['content'] ?? '';

        // Extract JSON from response
        preg_match('/\{[\s\S]*\}/', $content, $matches);
        
        if (empty($matches)) {
            throw new \Exception('No se pudo extraer la estructura del menú');
        }

        $menuData = json_decode($matches[0], true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception('Error al parsear JSON: ' . json_last_error_msg());
        }

        return $menuData;
    }

    protected function buildExtractionPrompt(string $rawText, Restaurant $restaurant): string
    {
        return <<<PROMPT
Eres un experto en extracción de datos de menús de restaurantes mexicanos.

Analiza el siguiente texto extraído de un menú y conviértelo en un JSON estructurado.

TEXTO DEL MENÚ:
{$rawText}

INFORMACIÓN DEL RESTAURANTE:
- Nombre: {$restaurant->name}
- Categoría: {$restaurant->category?->name}

Devuelve SOLO un JSON válido con esta estructura exacta:
{
  "categories": [
    {
      "name": "Nombre de la categoría en español",
      "name_es": "Nombre en español",
      "icon": "emoji apropiado",
      "items": [
        {
          "name": "Nombre del platillo en español",
          "name_es": "Nombre en español",
          "description": "Descripción breve del platillo",
          "description_es": "Descripción en español",
          "price": 12.99,
          "dietary_tags": ["vegetarian", "spicy", "vegan", "gluten-free"],
          "is_popular": true
        }
      ]
    }
  ]
}

Reglas:
1. Extrae TODOS los platillos que puedas identificar
2. Los precios deben ser números (sin "$")
3. Agrupa los platillos en categorías lógicas (Tacos, Sopas, Bebidas, etc.)
4. Si no hay precio visible, usa 0
5. Marca como popular los platillos destacados o con indicadores especiales
6. Detecta opciones vegetarianas/veganas/picantes cuando sea evidente
7. Usa emojis apropiados para las categorías (🌮 🥤 🍲 🌯 etc.)

Responde SOLO con el JSON, sin texto adicional.
PROMPT;
    }

    /**
     * Create menu items from extracted data
     */
    public function createMenuItems(Restaurant $restaurant, array $menuData): int
    {
        $itemsCreated = 0;

        foreach ($menuData['categories'] ?? [] as $categoryData) {
            // Create or find category
            $category = MenuCategory::firstOrCreate(
                [
                    'restaurant_id' => $restaurant->id,
                    'name' => $categoryData['name'],
                ],
                [
                    'name_es' => $categoryData['name_es'] ?? $categoryData['name'],
                    'icon' => $categoryData['icon'] ?? '🍽️',
                    'sort_order' => MenuCategory::where('restaurant_id', $restaurant->id)->count(),
                ]
            );

            // Create items
            foreach ($categoryData['items'] ?? [] as $itemData) {
                $item = MenuItem::create([
                    'menu_category_id' => $category->id,
                    'name' => $itemData['name'],
                    'name_es' => $itemData['name_es'] ?? $itemData['name'],
                    'description' => $itemData['description'] ?? null,
                    'description_es' => $itemData['description_es'] ?? $itemData['description'] ?? null,
                    'price' => (float) ($itemData['price'] ?? 0),
                    'dietary_tags' => $itemData['dietary_tags'] ?? [],
                    'is_popular' => $itemData['is_popular'] ?? false,
                    'sort_order' => MenuItem::where('menu_category_id', $category->id)->count(),
                ]);

                $itemsCreated++;
            }
        }

        return $itemsCreated;
    }
}
