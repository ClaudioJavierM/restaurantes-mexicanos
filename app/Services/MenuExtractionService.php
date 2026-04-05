<?php

namespace App\Services;

use App\Models\MenuUpload;
use App\Models\MenuCategory;
use App\Models\MenuItem;
use App\Models\Restaurant;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class MenuExtractionService
{
    protected string $openaiApiKey;

    public function __construct()
    {
        $this->openaiApiKey = config('services.openai.api_key') ?? env('OPENAI_API_KEY', '');
    }

    /**
     * Main entry point: extract menu data from upload using GPT-4o Vision
     */
    public function extractMenuFromUpload(MenuUpload $upload, Restaurant $restaurant): array
    {
        if (empty($this->openaiApiKey)) {
            throw new \Exception('OpenAI API key no configurada');
        }

        if ($upload->file_type === 'url') {
            return $this->extractFromUrl($upload->file_path, $restaurant);
        }

        $filePath = Storage::disk('public')->path($upload->file_path);

        if (!file_exists($filePath)) {
            throw new \Exception('Archivo no encontrado: ' . $upload->file_path);
        }

        if ($upload->file_type === 'pdf') {
            return $this->extractFromPdfWithVision($filePath, $restaurant);
        }

        return $this->extractFromImageWithVision($filePath, $restaurant);
    }

    /**
     * Extract text from image using GPT-4o Vision (OCR + structuring in one call)
     */
    protected function extractFromImageWithVision(string $filePath, Restaurant $restaurant): array
    {
        $imageData = base64_encode(file_get_contents($filePath));
        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        $mimeType = match ($extension) {
            'jpg', 'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'webp' => 'image/webp',
            'gif' => 'image/gif',
            default => 'image/jpeg',
        };

        return $this->callOpenAIVision(
            "data:{$mimeType};base64,{$imageData}",
            $restaurant
        );
    }

    /**
     * Extract from PDF: convert first page to image, then use Vision
     */
    protected function extractFromPdfWithVision(string $filePath, Restaurant $restaurant): array
    {
        $imagePath = $this->convertPdfToImage($filePath);

        if (!$imagePath) {
            throw new \Exception('No se pudo procesar el PDF. Por favor sube el menu como imagen (JPG/PNG).');
        }

        try {
            $result = $this->extractFromImageWithVision($imagePath, $restaurant);
            @unlink($imagePath);
            return $result;
        } catch (\Exception $e) {
            @unlink($imagePath);
            throw $e;
        }
    }

    /**
     * Convert first page of PDF to image using Imagick or pdftoppm
     */
    protected function convertPdfToImage(string $pdfPath): ?string
    {
        $outputPath = sys_get_temp_dir() . '/menu_' . Str::random(10) . '.jpg';

        if (extension_loaded('imagick')) {
            try {
                $imagick = new \Imagick();
                $imagick->setResolution(150, 150);
                $imagick->readImage($pdfPath . '[0]');
                $imagick->setImageFormat('jpg');
                $imagick->setImageCompressionQuality(85);
                $imagick->writeImage($outputPath);
                $imagick->clear();
                return file_exists($outputPath) ? $outputPath : null;
            } catch (\Exception $e) {
                Log::warning('MenuExtraction: Imagick failed', ['error' => $e->getMessage()]);
            }
        }

        $cmd = "pdftoppm -jpeg -r 150 -f 1 -l 1 " . escapeshellarg($pdfPath) . " " . escapeshellarg(substr($outputPath, 0, -4));
        exec($cmd . ' 2>&1', $output, $returnCode);

        $pdftoppmOutput = substr($outputPath, 0, -4) . '-1.jpg';
        if (file_exists($pdftoppmOutput)) {
            rename($pdftoppmOutput, $outputPath);
            return $outputPath;
        }

        return null;
    }

    /**
     * Extract from URL
     */
    protected function extractFromUrl(string $url, Restaurant $restaurant): array
    {
        $response = Http::timeout(30)->get($url);

        if (!$response->successful()) {
            throw new \Exception('No se pudo acceder a la URL del menu');
        }

        $contentType = $response->header('Content-Type') ?? '';

        if (str_contains($contentType, 'image/')) {
            $imageData = base64_encode($response->body());
            return $this->callOpenAIVision("data:{$contentType};base64,{$imageData}", $restaurant);
        }

        $html = $response->body();
        $text = strip_tags($html);
        $text = preg_replace('/\s+/', ' ', $text);
        $text = trim(substr($text, 0, 15000));

        return $this->callOpenAIChat($text, $restaurant);
    }

    /**
     * Call OpenAI GPT-4o-mini Vision API
     */
    protected function callOpenAIVision(string $imageUrl, Restaurant $restaurant): array
    {
        $prompt = $this->buildExtractionPrompt($restaurant);

        $response = Http::timeout(120)->withHeaders([
            'Authorization' => 'Bearer ' . $this->openaiApiKey,
            'Content-Type' => 'application/json',
        ])->post('https://api.openai.com/v1/chat/completions', [
            'model' => 'gpt-4o-mini',
            'max_tokens' => 4096,
            'response_format' => ['type' => 'json_object'],
            'messages' => [
                [
                    'role' => 'user',
                    'content' => [
                        ['type' => 'text', 'text' => $prompt],
                        ['type' => 'image_url', 'image_url' => ['url' => $imageUrl, 'detail' => 'high']],
                    ],
                ],
            ],
        ]);

        if (!$response->successful()) {
            Log::error('MenuExtraction: OpenAI Vision failed', ['response' => $response->body()]);
            throw new \Exception('Error en IA: ' . ($response->json('error.message') ?? 'Error desconocido'));
        }

        $content = $response->json('choices.0.message.content') ?? '';
        return $this->parseJsonResponse($content);
    }

    /**
     * Call OpenAI chat (for text-only content)
     */
    protected function callOpenAIChat(string $text, Restaurant $restaurant): array
    {
        $prompt = $this->buildExtractionPrompt($restaurant) . "\n\nTEXTO DEL MENU:\n" . $text;

        $response = Http::timeout(90)->withHeaders([
            'Authorization' => 'Bearer ' . $this->openaiApiKey,
            'Content-Type' => 'application/json',
        ])->post('https://api.openai.com/v1/chat/completions', [
            'model' => 'gpt-4o-mini',
            'max_tokens' => 4096,
            'response_format' => ['type' => 'json_object'],
            'messages' => [['role' => 'user', 'content' => $prompt]],
        ]);

        if (!$response->successful()) {
            throw new \Exception('Error en IA: ' . ($response->json('error.message') ?? 'Error desconocido'));
        }

        $content = $response->json('choices.0.message.content') ?? '';
        return $this->parseJsonResponse($content);
    }

    /**
     * Parse JSON response from OpenAI
     */
    protected function parseJsonResponse(string $content): array
    {
        $content = trim($content);
        $content = preg_replace('/^```(?:json)?\s*|\s*```$/m', '', $content);

        $menuData = json_decode($content, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            if (preg_match('/\{[\s\S]*\}/', $content, $matches)) {
                $menuData = json_decode($matches[0], true);
            }
        }

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception('Error al parsear respuesta de IA: ' . json_last_error_msg());
        }

        if (!isset($menuData['categories']) || !is_array($menuData['categories'])) {
            throw new \Exception('La IA no devolvio categorias validas');
        }

        return $menuData;
    }

    protected function buildExtractionPrompt(Restaurant $restaurant): string
    {
        $name = $restaurant->name;
        $category = $restaurant->category?->name ?? 'Mexicano';

        return <<<PROMPT
Eres un experto en extraccion de datos de menus de restaurantes mexicanos.

Analiza esta imagen de menu y devuelve un JSON estructurado con todos los platillos.

INFORMACION DEL RESTAURANTE:
- Nombre: {$name}
- Categoria: {$category}

Devuelve SOLO un JSON valido con esta estructura exacta:
{
  "categories": [
    {
      "name": "Nombre de la categoria",
      "name_es": "Nombre en espanol",
      "icon": "emoji apropiado",
      "items": [
        {
          "name": "Nombre del platillo",
          "name_es": "Nombre en espanol",
          "description": "Descripcion breve",
          "description_es": "Descripcion en espanol",
          "price": 12.99,
          "dietary_tags": ["vegetarian", "spicy", "vegan", "gluten-free"],
          "is_popular": false
        }
      ]
    }
  ]
}

Reglas:
1. Extrae TODOS los platillos visibles en el menu
2. Los precios deben ser numeros decimales (sin simbolo \$)
3. Agrupa los platillos en categorias logicas (Tacos, Sopas, Bebidas, Postres, etc.)
4. Si no hay precio visible, usa 0
5. Marca is_popular=true solo para platillos destacados o marcados como especiales
6. Detecta tags dieteticos cuando sea evidente (vegetarian, vegan, spicy, gluten-free)
7. Usa emojis apropiados para categorias
8. Si el menu esta en ingles, traduce los nombres al espanol en los campos _es
9. Si el menu esta en espanol, copia los mismos valores en name y name_es

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

            foreach ($categoryData['items'] ?? [] as $itemData) {
                MenuItem::create([
                    'menu_category_id' => $category->id,
                    'name' => $itemData['name'] ?? 'Sin nombre',
                    'name_es' => $itemData['name_es'] ?? $itemData['name'] ?? 'Sin nombre',
                    'description' => $itemData['description'] ?? null,
                    'description_es' => $itemData['description_es'] ?? $itemData['description'] ?? null,
                    'price' => (float) ($itemData['price'] ?? 0),
                    'dietary_tags' => $itemData['dietary_tags'] ?? [],
                    'is_popular' => (bool) ($itemData['is_popular'] ?? false),
                    'sort_order' => MenuItem::where('menu_category_id', $category->id)->count(),
                    'is_available' => true,
                ]);

                $itemsCreated++;
            }
        }

        return $itemsCreated;
    }

    // ─── Backward compatibility ─────────────────────────────────────

    public function extractText(MenuUpload $upload): string
    {
        return '';
    }

    public function structureMenuWithAI(string $rawText, Restaurant $restaurant): array
    {
        if (empty(trim($rawText))) {
            throw new \Exception('Texto vacio. Use extractMenuFromUpload() en su lugar.');
        }
        return $this->callOpenAIChat($rawText, $restaurant);
    }
}
