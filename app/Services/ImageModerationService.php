<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ImageModerationService
{
    protected $apiKey;

    public function __construct()
    {
        $this->apiKey = config('services.google.vision_api_key')
            ?: config('services.google.places_api_key'); // Fallback to places key
    }

    /**
     * Check if an image is safe using Google Vision Safe Search
     * Returns ['safe' => bool, 'reasons' => array, 'scores' => array]
     */
    public function checkImageSafety(string $imagePath): array
    {
        try {
            // Get the image content
            if (str_starts_with($imagePath, 'http')) {
                $imageContent = file_get_contents($imagePath);
            } else {
                // Local file path
                $fullPath = Storage::disk('public')->path($imagePath);
                if (!file_exists($fullPath)) {
                    return ['safe' => true, 'reasons' => [], 'scores' => [], 'error' => 'File not found'];
                }
                $imageContent = file_get_contents($fullPath);
            }

            if (!$imageContent) {
                return ['safe' => true, 'reasons' => [], 'scores' => [], 'error' => 'Could not read image'];
            }

            $base64Image = base64_encode($imageContent);

            // Call Google Vision API
            $response = Http::post('https://vision.googleapis.com/v1/images:annotate?key=' . $this->apiKey, [
                'requests' => [
                    [
                        'image' => [
                            'content' => $base64Image,
                        ],
                        'features' => [
                            ['type' => 'SAFE_SEARCH_DETECTION'],
                            ['type' => 'LABEL_DETECTION', 'maxResults' => 10],
                        ],
                    ],
                ],
            ]);

            if (!$response->successful()) {
                Log::warning('Google Vision API error', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                // If API fails, allow the image (fail open for better UX)
                return ['safe' => true, 'reasons' => [], 'scores' => [], 'error' => 'API error'];
            }

            $data = $response->json();
            $safeSearch = $data['responses'][0]['safeSearchAnnotation'] ?? null;
            $labels = $data['responses'][0]['labelAnnotations'] ?? [];

            if (!$safeSearch) {
                return ['safe' => true, 'reasons' => [], 'scores' => [], 'error' => 'No safe search data'];
            }

            // Check safety levels
            // UNKNOWN, VERY_UNLIKELY, UNLIKELY, POSSIBLE, LIKELY, VERY_LIKELY
            $unsafeLevels = ['LIKELY', 'VERY_LIKELY'];
            $reasons = [];
            $scores = [
                'adult' => $safeSearch['adult'] ?? 'UNKNOWN',
                'violence' => $safeSearch['violence'] ?? 'UNKNOWN',
                'racy' => $safeSearch['racy'] ?? 'UNKNOWN',
                'spoof' => $safeSearch['spoof'] ?? 'UNKNOWN',
                'medical' => $safeSearch['medical'] ?? 'UNKNOWN',
            ];

            // Check each category
            if (in_array($safeSearch['adult'] ?? '', $unsafeLevels)) {
                $reasons[] = 'adult_content';
            }
            if (in_array($safeSearch['violence'] ?? '', $unsafeLevels)) {
                $reasons[] = 'violence';
            }
            if (in_array($safeSearch['racy'] ?? '', $unsafeLevels)) {
                $reasons[] = 'racy_content';
            }

            // Check if it's food-related (for restaurant photos)
            $labelNames = array_map(fn($l) => strtolower($l['description'] ?? ''), $labels);
            $foodRelated = $this->isFoodRelated($labelNames);

            $isSafe = empty($reasons);

            Log::info('Image moderation result', [
                'path' => $imagePath,
                'safe' => $isSafe,
                'reasons' => $reasons,
                'scores' => $scores,
                'food_related' => $foodRelated,
                'labels' => array_slice($labelNames, 0, 5),
            ]);

            return [
                'safe' => $isSafe,
                'reasons' => $reasons,
                'scores' => $scores,
                'food_related' => $foodRelated,
                'labels' => $labelNames,
            ];

        } catch (\Exception $e) {
            Log::error('Image moderation error', [
                'message' => $e->getMessage(),
                'path' => $imagePath,
            ]);
            // Fail open - allow image if moderation fails
            return ['safe' => true, 'reasons' => [], 'scores' => [], 'error' => $e->getMessage()];
        }
    }

    /**
     * Check if labels indicate food-related content
     */
    protected function isFoodRelated(array $labels): bool
    {
        $foodKeywords = [
            'food', 'dish', 'meal', 'cuisine', 'restaurant', 'plate', 'bowl',
            'cooking', 'ingredient', 'recipe', 'breakfast', 'lunch', 'dinner',
            'appetizer', 'dessert', 'beverage', 'drink', 'cocktail', 'beer',
            'wine', 'taco', 'burrito', 'enchilada', 'salsa', 'guacamole',
            'mexican food', 'tortilla', 'quesadilla', 'nacho', 'fajita',
            'interior design', 'room', 'table', 'chair', 'dining', 'bar',
            'menu', 'sign', 'building', 'architecture', 'storefront',
        ];

        foreach ($labels as $label) {
            foreach ($foodKeywords as $keyword) {
                if (str_contains($label, $keyword)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Moderate a batch of images
     */
    public function moderateBatch(array $imagePaths): array
    {
        $results = [];
        foreach ($imagePaths as $path) {
            $results[$path] = $this->checkImageSafety($path);
        }
        return $results;
    }

    /**
     * Get human-readable rejection reason
     */
    public function getReasonLabel(string $reason): string
    {
        return match($reason) {
            'adult_content' => 'Contenido para adultos detectado',
            'violence' => 'Contenido violento detectado',
            'racy_content' => 'Contenido sugestivo detectado',
            default => 'Contenido inapropiado detectado',
        };
    }
}
