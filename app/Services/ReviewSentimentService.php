<?php

namespace App\Services;

use App\Models\Review;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ReviewSentimentService
{
    private string $apiKey;
    private string $model = 'gpt-4o-mini';
    private string $apiUrl = 'https://api.openai.com/v1/chat/completions';

    public function __construct()
    {
        $this->apiKey = config('services.openai.key', '');
    }

    /**
     * Analyze a single review and persist sentiment data.
     */
    public function analyzeReview(Review $review): array
    {
        $text = trim($review->title . ' ' . $review->comment);

        if (empty($text)) {
            return [];
        }

        $systemPrompt = 'Eres un analizador de sentimientos especializado en reseñas de restaurantes mexicanos. '
            . 'Analiza el sentimiento de la reseña proporcionada y devuelve SOLO un objeto JSON válido (sin markdown, sin texto adicional) con esta estructura exacta: '
            . '{"score": 0.0, "label": "neutral", "keywords": [{"word": "ejemplo", "sentiment": "positive", "category": "food"}]}. '
            . 'Valores para score: 0.0 a 1.0 (0=muy negativo, 0.5=neutro, 1=muy positivo). '
            . 'Valores para label: very_positive, positive, neutral, negative, very_negative. '
            . 'Valores para sentiment de cada keyword: positive, negative, neutral. '
            . 'Valores para category de cada keyword: food, service, ambiance, price, location. '
            . 'Extrae entre 3 y 10 keywords relevantes.';

        $response = Http::withToken($this->apiKey)
            ->timeout(30)
            ->post($this->apiUrl, [
                'model'       => $this->model,
                'messages'    => [
                    ['role' => 'system', 'content' => $systemPrompt],
                    ['role' => 'user', 'content' => $text],
                ],
                'temperature' => 0.2,
                'max_tokens'  => 500,
            ]);

        if ($response->failed()) {
            Log::error('ReviewSentimentService: OpenAI API error', [
                'review_id' => $review->id,
                'status'    => $response->status(),
                'body'      => $response->body(),
            ]);
            throw new \RuntimeException('OpenAI API error: ' . $response->status());
        }

        $content = $response->json('choices.0.message.content', '');
        $analysis = json_decode($content, true);

        if (json_last_error() !== JSON_ERROR_NONE || empty($analysis)) {
            Log::warning('ReviewSentimentService: invalid JSON from OpenAI', [
                'review_id' => $review->id,
                'content'   => $content,
            ]);
            throw new \RuntimeException('Invalid JSON response from OpenAI');
        }

        $score    = max(0.0, min(1.0, (float) ($analysis['score'] ?? 0.5)));
        $label    = $this->validateLabel($analysis['label'] ?? 'neutral');
        $keywords = $this->validateKeywords($analysis['keywords'] ?? []);

        $review->update([
            'sentiment_score'       => $score,
            'sentiment_label'       => $label,
            'sentiment_keywords'    => $keywords,
            'sentiment_analyzed_at' => now(),
        ]);

        return [
            'review_id' => $review->id,
            'score'     => $score,
            'label'     => $label,
            'keywords'  => $keywords,
        ];
    }

    /**
     * Analyze pending reviews for a restaurant.
     */
    public function analyzeForRestaurant(int $restaurantId, int $limit = 50): array
    {
        $reviews = Review::where('restaurant_id', $restaurantId)
            ->where('status', 'approved')
            ->whereNull('sentiment_analyzed_at')
            ->limit($limit)
            ->get();

        $processed = 0;
        $failed    = 0;
        $results   = [];

        foreach ($reviews as $review) {
            try {
                $result    = $this->analyzeReview($review);
                $results[] = $result;
                $processed++;
            } catch (\Throwable $e) {
                Log::warning('ReviewSentimentService: failed to analyze review', [
                    'review_id' => $review->id,
                    'error'     => $e->getMessage(),
                ]);
                $failed++;
            }
        }

        return [
            'restaurant_id' => $restaurantId,
            'processed'     => $processed,
            'failed'        => $failed,
            'total'         => $reviews->count(),
            'results'       => $results,
        ];
    }

    /**
     * Get aggregated sentiment summary for a restaurant.
     */
    public function getSentimentSummary(int $restaurantId): array
    {
        $reviews = Review::where('restaurant_id', $restaurantId)
            ->where('status', 'approved')
            ->whereNotNull('sentiment_analyzed_at')
            ->get();

        if ($reviews->isEmpty()) {
            return [
                'avg_score'              => null,
                'label_counts'           => $this->emptyLabelCounts(),
                'top_positive_keywords'  => [],
                'top_negative_keywords'  => [],
                'category_scores'        => $this->emptyCategoryScores(),
                'total_analyzed'         => 0,
            ];
        }

        $avgScore = round($reviews->avg('sentiment_score'), 2);

        $labelCounts = $this->emptyLabelCounts();
        foreach ($reviews as $review) {
            if ($review->sentiment_label && isset($labelCounts[$review->sentiment_label])) {
                $labelCounts[$review->sentiment_label]++;
            }
        }

        // Aggregate keywords across all reviews
        $positiveKeywords = [];
        $negativeKeywords = [];
        $categoryTotals   = [
            'food'      => ['sum' => 0, 'count' => 0],
            'service'   => ['sum' => 0, 'count' => 0],
            'ambiance'  => ['sum' => 0, 'count' => 0],
            'price'     => ['sum' => 0, 'count' => 0],
            'location'  => ['sum' => 0, 'count' => 0],
        ];

        foreach ($reviews as $review) {
            $keywords = $review->sentiment_keywords ?? [];
            if (!is_array($keywords)) {
                continue;
            }

            foreach ($keywords as $kw) {
                $word      = strtolower($kw['word'] ?? '');
                $sentiment = $kw['sentiment'] ?? 'neutral';
                $category  = $kw['category'] ?? 'food';
                $score     = (float) ($review->sentiment_score ?? 0.5);

                if ($sentiment === 'positive') {
                    $positiveKeywords[$word] = ($positiveKeywords[$word] ?? 0) + 1;
                } elseif ($sentiment === 'negative') {
                    $negativeKeywords[$word] = ($negativeKeywords[$word] ?? 0) + 1;
                }

                if (isset($categoryTotals[$category])) {
                    if ($sentiment === 'positive') {
                        $categoryTotals[$category]['sum']   += $score;
                        $categoryTotals[$category]['count'] += 1;
                    } elseif ($sentiment === 'negative') {
                        $categoryTotals[$category]['sum']   += (1 - $score);
                        $categoryTotals[$category]['count'] += 1;
                    }
                }
            }
        }

        arsort($positiveKeywords);
        arsort($negativeKeywords);

        $topPositive = array_slice(array_keys($positiveKeywords), 0, 5);
        $topNegative = array_slice(array_keys($negativeKeywords), 0, 5);

        $categoryScores = [];
        foreach ($categoryTotals as $cat => $data) {
            $categoryScores[$cat] = $data['count'] > 0
                ? round($data['sum'] / $data['count'], 2)
                : null;
        }

        return [
            'avg_score'             => $avgScore,
            'label_counts'          => $labelCounts,
            'top_positive_keywords' => $topPositive,
            'top_negative_keywords' => $topNegative,
            'category_scores'       => $categoryScores,
            'total_analyzed'        => $reviews->count(),
        ];
    }

    // -------------------------------------------------------------------------
    // Private helpers
    // -------------------------------------------------------------------------

    private function validateLabel(string $label): string
    {
        $allowed = ['very_positive', 'positive', 'neutral', 'negative', 'very_negative'];
        return in_array($label, $allowed, true) ? $label : 'neutral';
    }

    private function validateKeywords(array $keywords): array
    {
        $allowedSentiments  = ['positive', 'negative', 'neutral'];
        $allowedCategories  = ['food', 'service', 'ambiance', 'price', 'location'];
        $validated          = [];

        foreach ($keywords as $kw) {
            if (!is_array($kw) || empty($kw['word'])) {
                continue;
            }
            $validated[] = [
                'word'      => mb_substr((string) $kw['word'], 0, 50),
                'sentiment' => in_array($kw['sentiment'] ?? '', $allowedSentiments, true)
                    ? $kw['sentiment']
                    : 'neutral',
                'category'  => in_array($kw['category'] ?? '', $allowedCategories, true)
                    ? $kw['category']
                    : 'food',
            ];
        }

        return $validated;
    }

    private function emptyLabelCounts(): array
    {
        return [
            'very_positive' => 0,
            'positive'      => 0,
            'neutral'       => 0,
            'negative'      => 0,
            'very_negative' => 0,
        ];
    }

    private function emptyCategoryScores(): array
    {
        return [
            'food'     => null,
            'service'  => null,
            'ambiance' => null,
            'price'    => null,
            'location' => null,
        ];
    }
}
