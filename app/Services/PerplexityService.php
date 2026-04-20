<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PerplexityService
{
    protected string $apiKey;
    protected string $baseUrl = 'https://api.perplexity.ai';

    public function __construct()
    {
        $this->apiKey = config('services.perplexity.api_key', '');
    }

    /**
     * Search for a restaurant's contact email using Perplexity Sonar online search.
     * Returns email string or null if not found.
     */
    public function findRestaurantEmail(string $name, string $city, string $state, ?string $phone = null): ?string
    {
        if (empty($this->apiKey)) {
            Log::warning('PerplexityService: no API key configured');
            return null;
        }

        $phoneHint = $phone ? " phone {$phone}" : '';
        $prompt = "Find the contact email address for the restaurant \"{$name}\" located in {$city}, {$state}{$phoneHint}. Search their website, Google Maps listing, Yelp page, or any official source. Return ONLY the email address with no explanation. If no email is found, return the single word: NONE.";

        try {
            $response = Http::withToken($this->apiKey)
                ->timeout(20)
                ->post("{$this->baseUrl}/chat/completions", [
                    'model'    => 'sonar',
                    'messages' => [
                        ['role' => 'system', 'content' => 'You are a research assistant that finds business contact emails. Return ONLY the email address or the word NONE. No explanations, no extra text.'],
                        ['role' => 'user',   'content' => $prompt],
                    ],
                    'max_tokens'  => 50,
                    'temperature' => 0,
                ]);

            if (!$response->successful()) {
                Log::warning("Perplexity API error: " . $response->status() . ' ' . $response->body());
                return null;
            }

            $content = trim($response->json('choices.0.message.content', ''));

            // Validate it looks like an email
            if (filter_var($content, FILTER_VALIDATE_EMAIL)) {
                return strtolower($content);
            }

            // Try to extract email from response if it included extra text
            if (preg_match('/[\w._%+\-]+@[\w.\-]+\.[a-zA-Z]{2,}/', $content, $matches)) {
                return strtolower($matches[0]);
            }

            return null;

        } catch (\Exception $e) {
            Log::error("PerplexityService error for {$name}: " . $e->getMessage());
            return null;
        }
    }
}
