<?php

namespace App\Livewire\Owner;

use App\Models\MenuUpload as MenuUploadModel;
use App\Models\MenuCategory;
use App\Models\MenuItem;
use App\Models\Restaurant;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithFileUploads;

class MenuUpload extends Component
{
    use WithFileUploads;

    public Restaurant $restaurant;
    public $menuFile;
    public $menuUrl;
    public $uploadType = 'file';
    public $uploads = [];

    // Processing state
    public $isProcessing = false;
    public $processingStep = '';
    public $processingProgress = 0;
    public $processingError = '';
    public $processingResult = null;

    protected $rules = [
        'menuFile' => 'nullable|file|mimes:pdf,jpg,jpeg,png,webp|max:20480',
        'menuUrl' => 'nullable|url',
    ];

    public function mount(Restaurant $restaurant)
    {
        $this->restaurant = $restaurant;
        $this->loadUploads();
    }

    public function loadUploads()
    {
        $this->uploads = $this->restaurant->menuUploads()
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
    }

    public function uploadMenu()
    {
        $this->validate();
        $this->processingError = '';
        $this->processingResult = null;

        $apiKey = config('services.openai.api_key');

        if (empty($apiKey)) {
            $this->processingError = 'La API de IA no está configurada. Contacta al administrador.';
            return;
        }

        try {
            $this->isProcessing = true;
            $this->processingStep = 'Subiendo archivo...';
            $this->processingProgress = 10;

            $upload = null;

            if ($this->uploadType === 'file' && $this->menuFile) {
                $path = $this->menuFile->store('menu-uploads/' . $this->restaurant->id, 'public');
                $extension = $this->menuFile->getClientOriginalExtension();
                $fileType = in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'webp']) ? 'image' : 'pdf';

                $upload = MenuUploadModel::create([
                    'restaurant_id' => $this->restaurant->id,
                    'user_id' => auth()->id(),
                    'file_path' => $path,
                    'file_type' => $fileType,
                    'original_name' => $this->menuFile->getClientOriginalName(),
                    'status' => 'processing',
                ]);

                $this->menuFile = null;

            } elseif ($this->uploadType === 'url' && $this->menuUrl) {
                $upload = MenuUploadModel::create([
                    'restaurant_id' => $this->restaurant->id,
                    'user_id' => auth()->id(),
                    'file_path' => $this->menuUrl,
                    'file_type' => 'url',
                    'original_name' => $this->menuUrl,
                    'status' => 'processing',
                ]);

                $this->menuUrl = null;
            }

            if (!$upload) {
                $this->isProcessing = false;
                $this->processingError = 'No se seleccionó archivo ni URL.';
                return;
            }

            // Process synchronously
            $this->processingStep = 'Analizando menú con IA...';
            $this->processingProgress = 30;

            $menuData = $this->processWithAI($upload, $apiKey);

            $this->processingStep = 'Creando platillos...';
            $this->processingProgress = 70;

            $upload->update(['ai_extracted_data' => $menuData]);

            $itemsCreated = $this->createMenuItems($menuData);

            $upload->update([
                'status' => 'completed',
                'items_extracted' => $itemsCreated,
                'processed_at' => now(),
            ]);

            $this->processingStep = 'Completado';
            $this->processingProgress = 100;

            $this->processingResult = [
                'items' => $itemsCreated,
                'categories' => count($menuData['categories'] ?? []),
            ];

            $this->loadUploads();

            Log::info('Menu processed successfully', [
                'upload_id' => $upload->id,
                'items_created' => $itemsCreated,
            ]);

        } catch (\Exception $e) {
            Log::error('Menu processing failed', [
                'error' => $e->getMessage(),
            ]);

            if (isset($upload)) {
                $upload->update([
                    'status' => 'failed',
                    'error_message' => $e->getMessage(),
                ]);
            }

            $this->processingError = 'Error al procesar: ' . $e->getMessage();
            $this->loadUploads();
        } finally {
            $this->isProcessing = false;
        }
    }

    protected function processWithAI(MenuUploadModel $upload, string $apiKey): array
    {
        if ($upload->file_type === 'url') {
            return $this->processUrlWithAI($upload->file_path, $apiKey);
        }

        $filePath = Storage::disk('public')->path($upload->file_path);

        if ($upload->file_type === 'image') {
            return $this->processImageWithVision($filePath, $apiKey);
        }

        // PDF: extract text first, then send to AI
        return $this->processPdfWithAI($filePath, $apiKey);
    }

    protected function resizeImageForAPI(string $filePath): array
    {
        $maxBytes = 3500000; // ~3.5MB raw = ~4.67MB base64 (safe margin under 5MB API limit)
        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

        // Check if GD is available
        if (!function_exists('imagecreatefromstring')) {
            // No GD - just read and hope it's small enough
            $data = file_get_contents($filePath);
            $mimeType = match($extension) {
                'jpg', 'jpeg' => 'image/jpeg',
                'png' => 'image/png',
                'webp' => 'image/webp',
                default => 'image/jpeg',
            };
            return ['data' => base64_encode($data), 'mime' => $mimeType];
        }

        $imageString = file_get_contents($filePath);

        // Check base64 size (base64 adds ~33% overhead)
        $base64Size = (int)(strlen($imageString) * 1.37); // approximate base64 size
        if ($base64Size <= 5 * 1024 * 1024) {
            // Small enough even in base64
            $mimeType = match($extension) {
                'jpg', 'jpeg' => 'image/jpeg',
                'png' => 'image/png',
                'webp' => 'image/webp',
                default => 'image/jpeg',
            };
            return ['data' => base64_encode($imageString), 'mime' => $mimeType];
        }

        // Need to resize/compress
        $img = imagecreatefromstring($imageString);
        if (!$img) {
            throw new \Exception('No se pudo leer la imagen. Formato no soportado.');
        }

        $origW = imagesx($img);
        $origH = imagesy($img);

        // Try reducing quality first (convert to JPEG at 85%)
        // Then progressively reduce dimensions if still too large
        $quality = 85;
        $scale = 1.0;

        // Max dimension 2048px for API (good enough for menu reading)
        $maxDim = 2048;
        if ($origW > $maxDim || $origH > $maxDim) {
            $scale = min($maxDim / $origW, $maxDim / $origH);
        }

        for ($attempt = 0; $attempt < 5; $attempt++) {
            $newW = (int)($origW * $scale);
            $newH = (int)($origH * $scale);

            $resized = imagecreatetruecolor($newW, $newH);
            // Preserve transparency for PNG
            imagealphablending($resized, false);
            imagesavealpha($resized, true);
            imagecopyresampled($resized, $img, 0, 0, 0, 0, $newW, $newH, $origW, $origH);

            ob_start();
            imagejpeg($resized, null, $quality);
            $data = ob_get_clean();
            imagedestroy($resized);

            // Check base64 encoded size against API 5MB limit
            $base64Len = (int)(strlen($data) * 1.37);
            if ($base64Len <= 5 * 1024 * 1024) {
                imagedestroy($img);
                \Log::info('Image resized for API', [
                    'original_size' => strlen($imageString),
                    'new_size' => strlen($data),
                    'dimensions' => "{$newW}x{$newH}",
                    'quality' => $quality,
                    'attempt' => $attempt + 1,
                ]);
                return ['data' => base64_encode($data), 'mime' => 'image/jpeg'];
            }

            // Reduce further
            $scale *= 0.7;
            $quality = max(60, $quality - 10);
        }

        imagedestroy($img);
        throw new \Exception('La imagen es demasiado grande incluso después de comprimirla. Intenta con una imagen más pequeña.');
    }

    protected function processImageWithVision(string $filePath, string $apiKey): array
    {
        $imageInfo = $this->resizeImageForAPI($filePath);
        $imageData = $imageInfo['data'];
        $mimeType = $imageInfo['mime'];

        $response = Http::timeout(120)->withHeaders([
            'Authorization' => 'Bearer ' . $apiKey,
            'Content-Type' => 'application/json',
        ])->post('https://api.openai.com/v1/chat/completions', [
            'model' => 'gpt-4o',
            'max_tokens' => 4096,
            'messages' => [
                [
                    'role' => 'user',
                    'content' => [
                        [
                            'type' => 'image_url',
                            'image_url' => [
                                'url' => 'data:' . $mimeType . ';base64,' . $imageData,
                            ],
                        ],
                        [
                            'type' => 'text',
                            'text' => $this->buildPrompt(),
                        ],
                    ],
                ],
            ],
        ]);

        return $this->parseAIResponse($response);
    }

    protected function processPdfWithAI(string $filePath, string $apiKey): array
    {
        // For PDF, convert first page to image or extract text
        // Use pdftotext if available, otherwise send as document
        $text = '';

        // Try pdftotext
        $output = [];
        exec("pdftotext " . escapeshellarg($filePath) . " - 2>/dev/null", $output, $code);
        if ($code === 0 && !empty($output)) {
            $text = implode("\n", $output);
        }

        if (empty($text)) {
            // Fallback: try to read PDF as text
            $content = file_get_contents($filePath);
            // Basic PDF text extraction
            preg_match_all('/\((.*?)\)/', $content, $matches);
            if (!empty($matches[1])) {
                $text = implode(' ', array_filter($matches[1], fn($t) => strlen($t) > 2));
            }
        }

        if (empty($text)) {
            throw new \Exception('No se pudo extraer texto del PDF. Intenta subir una imagen del menú en su lugar.');
        }

        return $this->processTextWithAI($text, $apiKey);
    }

    protected function processUrlWithAI(string $url, string $apiKey): array
    {
        $response = Http::timeout(30)->get($url);

        if (!$response->successful()) {
            throw new \Exception('No se pudo acceder a la URL del menú.');
        }

        $html = $response->body();
        $text = strip_tags($html);
        $text = preg_replace('/\s+/', ' ', $text);
        $text = substr($text, 0, 8000); // Limit text length

        if (strlen($text) < 50) {
            throw new \Exception('No se pudo extraer suficiente texto de la URL.');
        }

        return $this->processTextWithAI($text, $apiKey);
    }

    protected function processTextWithAI(string $text, string $apiKey): array
    {
        $response = Http::timeout(120)->withHeaders([
            'Authorization' => 'Bearer ' . $apiKey,
            'Content-Type' => 'application/json',
        ])->post('https://api.openai.com/v1/chat/completions', [
            'model' => 'gpt-4o',
            'max_tokens' => 4096,
            'messages' => [
                [
                    'role' => 'user',
                    'content' => "TEXTO EXTRAÍDO DEL MENÚ:\n{$text}\n\n" . $this->buildPrompt(),
                ],
            ],
        ]);

        return $this->parseAIResponse($response);
    }

    protected function buildPrompt(): string
    {
        $name = $this->restaurant->name;
        $category = $this->restaurant->category?->name ?? 'Mexicano';

        return <<<PROMPT
Eres un experto en extracción de datos de menús de restaurantes.

Analiza el menú de "{$name}" (cocina: {$category}) y extrae TODOS los platillos que puedas identificar.

Devuelve SOLO un JSON válido con esta estructura:
{
  "categories": [
    {
      "name": "Nombre de la categoría",
      "icon": "emoji apropiado",
      "items": [
        {
          "name": "Nombre del platillo",
          "name_es": "Nombre en español",
          "description": "Descripción breve",
          "price": 12.99,
          "dietary_tags": [],
          "is_popular": false
        }
      ]
    }
  ]
}

Reglas:
1. Extrae TODOS los platillos visibles
2. Precios como números sin "$". Si no hay precio, usa 0
3. Agrupa en categorías lógicas (Tacos, Sopas, Bebidas, Postres, etc.)
4. Usa emojis apropiados para categorías
5. dietary_tags válidos: "vegetarian", "vegan", "gluten-free", "spicy", "dairy-free", "keto"
6. Marca is_popular solo platillos destacados o especiales
7. Responde SOLO con JSON válido, sin texto adicional
PROMPT;
    }

    protected function parseAIResponse($response): array
    {
        if (!$response->successful()) {
            $body = $response->json();
            $errorMsg = $body['error']['message'] ?? $response->body();
            throw new \Exception('Error de IA: ' . $errorMsg);
        }

        $data = $response->json();
        $content = $data['choices'][0]['message']['content'] ?? '';

        // Extract JSON from response
        preg_match('/\{[\s\S]*\}/', $content, $matches);

        if (empty($matches)) {
            throw new \Exception('La IA no devolvió una estructura válida.');
        }

        $menuData = json_decode($matches[0], true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception('Error al interpretar la respuesta: ' . json_last_error_msg());
        }

        if (empty($menuData['categories'])) {
            throw new \Exception('No se encontraron platillos en el menú.');
        }

        return $menuData;
    }

    protected function createMenuItems(array $menuData): int
    {
        $itemsCreated = 0;

        foreach ($menuData['categories'] ?? [] as $categoryData) {
            $category = MenuCategory::firstOrCreate(
                [
                    'restaurant_id' => $this->restaurant->id,
                    'name' => $categoryData['name'],
                ],
                [
                    'name_es' => $categoryData['name_es'] ?? $categoryData['name'],
                    'icon' => $categoryData['icon'] ?? '',
                    'sort_order' => MenuCategory::where('restaurant_id', $this->restaurant->id)->count(),
                ]
            );

            foreach ($categoryData['items'] ?? [] as $itemData) {
                MenuItem::create([
                    'menu_category_id' => $category->id,
                    'name' => $itemData['name'],
                    'name_es' => $itemData['name_es'] ?? $itemData['name'],
                    'description' => $itemData['description'] ?? null,
                    'price' => (float) ($itemData['price'] ?? 0),
                    'dietary_tags' => $itemData['dietary_tags'] ?? [],
                    'is_popular' => $itemData['is_popular'] ?? false,
                    'is_available' => true,
                    'sort_order' => MenuItem::where('menu_category_id', $category->id)->count(),
                ]);

                $itemsCreated++;
            }
        }

        return $itemsCreated;
    }

    public function deleteUpload($uploadId)
    {
        $upload = MenuUploadModel::where('restaurant_id', $this->restaurant->id)
            ->findOrFail($uploadId);

        if ($upload->file_type !== 'url' && Storage::disk('public')->exists($upload->file_path)) {
            Storage::disk('public')->delete($upload->file_path);
        }

        $upload->delete();
        $this->loadUploads();
    }

    public function retryUpload($uploadId)
    {
        $upload = MenuUploadModel::where('restaurant_id', $this->restaurant->id)
            ->findOrFail($uploadId);

        $apiKey = config('services.openai.api_key');
        if (empty($apiKey)) {
            $this->processingError = 'La API de IA no está configurada.';
            return;
        }

        try {
            $this->isProcessing = true;
            $this->processingStep = 'Reprocesando menú con IA...';
            $this->processingProgress = 30;

            $upload->update(['status' => 'processing', 'error_message' => null]);

            $menuData = $this->processWithAI($upload, $apiKey);

            $this->processingStep = 'Creando platillos...';
            $this->processingProgress = 70;

            $upload->update(['ai_extracted_data' => $menuData]);
            $itemsCreated = $this->createMenuItems($menuData);

            $upload->update([
                'status' => 'completed',
                'items_extracted' => $itemsCreated,
                'processed_at' => now(),
            ]);

            $this->processingProgress = 100;
            $this->processingResult = [
                'items' => $itemsCreated,
                'categories' => count($menuData['categories'] ?? []),
            ];

            $this->loadUploads();

        } catch (\Exception $e) {
            $upload->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);
            $this->processingError = 'Error: ' . $e->getMessage();
            $this->loadUploads();
        } finally {
            $this->isProcessing = false;
        }
    }

    public function render()
    {
        return view('livewire.owner.menu-upload');
    }
}
