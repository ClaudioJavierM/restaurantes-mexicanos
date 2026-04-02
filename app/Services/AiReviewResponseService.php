<?php

namespace App\Services;

use App\Models\Review;
use Illuminate\Support\Facades\Http;

class AiReviewResponseService
{
    public function suggestResponse(Review $review): string
    {
        $rating = $review->rating;
        $comment = $review->comment ?? '';
        $reviewerName = $review->reviewer_name;

        $response = Http::withHeaders([
            'x-api-key'         => config('services.anthropic.api_key'),
            'anthropic-version' => '2023-06-01',
            'content-type'      => 'application/json',
        ])->timeout(20)->post('https://api.anthropic.com/v1/messages', [
            'model'      => 'claude-haiku-4-5-20251001',
            'max_tokens' => 300,
            'system'     => 'You are a professional Mexican restaurant owner. Write a warm, professional response to a customer review. Detect the language of the review (Spanish or English) and respond in the same language. Be genuinely grateful, address specific points mentioned, and invite them to visit again. Keep your response to 2-3 sentences maximum. Do not use generic templates — make it feel personal and authentic.',
            'messages'   => [
                [
                    'role'    => 'user',
                    'content' => "Customer name: {$reviewerName}\nRating: {$rating}/5\nReview: \"{$comment}\"",
                ],
            ],
        ]);

        if ($response->failed()) {
            throw new \RuntimeException('Anthropic API error: ' . $response->status());
        }

        $body = $response->json();

        return $body['content'][0]['text'] ?? '';
    }
}
