<?php

namespace App\Services;

use App\Models\Restaurant;
use App\Models\Review;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ReviewResponseService
{
    protected ?string $apiKey = null;
    protected string $model = 'claude-sonnet-4-20250514';

    public function __construct()
    {
        $this->apiKey = config('services.anthropic.api_key') ?: env('ANTHROPIC_API_KEY') ?: null;
    }

    /**
     * Generate an AI-suggested response for a review
     */
    public function generateResponse(Review $review, Restaurant $restaurant): string
    {
        if (empty($this->apiKey)) {
            return $this->getFallbackResponse($review);
        }

        $prompt = $this->buildPrompt($review, $restaurant);

        try {
            $response = Http::withHeaders([
                'x-api-key' => $this->apiKey,
                'anthropic-version' => '2023-06-01',
                'content-type' => 'application/json',
            ])->timeout(20)->post('https://api.anthropic.com/v1/messages', [
                'model' => $this->model,
                'max_tokens' => 400,
                'messages' => [['role' => 'user', 'content' => $prompt]],
            ]);

            if ($response->successful()) {
                $text = $response->json()['content'][0]['text'] ?? '';
                return trim($text);
            }

            return $this->getFallbackResponse($review);
        } catch (\Exception $e) {
            Log::error('ReviewResponseService exception', ['error' => $e->getMessage()]);
            return $this->getFallbackResponse($review);
        }
    }

    protected function buildPrompt(Review $review, Restaurant $restaurant): string
    {
        $rating = $review->rating;
        $reviewerName = $review->reviewer_name ?? 'Cliente';
        $comment = $review->comment ?? '';
        $restaurantName = $restaurant->name;

        $tone = match(true) {
            $rating >= 4 => 'agradecido y calido',
            $rating === 3 => 'profesional y constructivo',
            default => 'empatico, profesional y resolutivo',
        };

        return "Eres el propietario de '{$restaurantName}', un restaurante mexicano. " .
               "Escribe una respuesta profesional a la siguiente resena en espanol. " .
               "El tono debe ser {$tone}.\n\n" .
               "Resena de {$reviewerName} ({$rating}/5 estrellas):\n" .
               "\"{$comment}\"\n\n" .
               "Instrucciones:\n" .
               "- Maximos 3 oraciones, no mas de 150 palabras\n" .
               "- Menciona el nombre del cliente una vez\n" .
               "- No uses emojis excesivos\n" .
               "- Si la calificacion es baja (1-2), ofrece disculpas y solucion\n" .
               "- Si es alta (4-5), agradece y refuerza lo positivo\n" .
               "- Firma con 'El equipo de {$restaurantName}'\n" .
               "- Responde SOLO con el texto de la respuesta, sin explicaciones adicionales";
    }

    protected function getFallbackResponse(Review $review): string
    {
        $name = $review->reviewer_name ?? 'estimado cliente';

        if ($review->rating >= 4) {
            return "¡Muchas gracias, {$name}! Tu visita y tus palabras nos llenan de alegria. " .
                   "Es un placer servirte y esperamos verte pronto. " .
                   "El equipo de " . ($review->restaurant->name ?? 'nuestro restaurante');
        }

        if ($review->rating === 3) {
            return "Gracias por tu visita y por tomarte el tiempo de compartir tu experiencia, {$name}. " .
                   "Tus comentarios nos ayudan a mejorar y trabajaremos para superar tus expectativas en tu proxima visita. " .
                   "El equipo de " . ($review->restaurant->name ?? 'nuestro restaurante');
        }

        return "Lamentamos que tu experiencia no haya sido la esperada, {$name}. " .
               "Tus comentarios son muy valiosos para nosotros y tomaremos las medidas necesarias para mejorar. " .
               "Te invitamos a contactarnos directamente para que podamos resolver cualquier inconveniente. " .
               "El equipo de " . ($review->restaurant->name ?? 'nuestro restaurante');
    }
}
