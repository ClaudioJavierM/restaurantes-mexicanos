<?php

namespace App\Services;

use App\Models\Restaurant;
use App\Models\RestaurantVote;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class CompetitorInsightsService
{
    /**
     * Find top competitors in the same city/state.
     */
    public function getCompetitors(Restaurant $restaurant, int $limit = 5): Collection
    {
        return Restaurant::approved()
            ->where('city', $restaurant->city)
            ->where('state_id', $restaurant->state_id)
            ->whereKeyNot($restaurant->id)
            ->with(['famerScore'])
            ->withCount(['votes as total_votes_count'])
            ->orderByDesc('average_rating')
            ->limit($limit)
            ->get();
    }

    /**
     * Get full competitive intelligence for a restaurant (cached 24h).
     */
    public function getInsights(Restaurant $restaurant): array
    {
        return Cache::remember("competitor_insights_{$restaurant->id}", 86400, function () use ($restaurant) {
            return $this->computeInsights($restaurant);
        });
    }

    /**
     * Force-recompute insights (bypasses cache).
     */
    public function refreshInsights(Restaurant $restaurant): array
    {
        Cache::forget("competitor_insights_{$restaurant->id}");
        return $this->getInsights($restaurant);
    }

    /**
     * Core computation — called by getInsights() via the cache closure.
     */
    protected function computeInsights(Restaurant $restaurant): array
    {
        // All approved restaurants in the same city
        $cityRestaurants = Restaurant::approved()
            ->where('city', $restaurant->city)
            ->where('state_id', $restaurant->state_id)
            ->withCount(['votes as total_votes_count'])
            ->orderByDesc('average_rating')
            ->get();

        $totalInCity = $cityRestaurants->count();

        if ($totalInCity === 0) {
            return $this->emptyInsights($restaurant);
        }

        // Rank = how many restaurants have a HIGHER rating + 1
        $rank = $cityRestaurants->where('id', '!=', $restaurant->id)
            ->where('average_rating', '>', (float) ($restaurant->average_rating ?? 0))
            ->count() + 1;

        // Percentile: % of restaurants this restaurant beats
        $beatenCount = $cityRestaurants->where('id', '!=', $restaurant->id)
            ->where('average_rating', '<', (float) ($restaurant->average_rating ?? 0))
            ->count();

        $denominator = max($totalInCity - 1, 1); // exclude self
        $percentile  = (int) round(($beatenCount / $denominator) * 100);

        // City leader (highest-rated, excluding self)
        $leader = $cityRestaurants->where('id', '!=', $restaurant->id)->first();

        $leaderRating  = $leader ? (float) ($leader->average_rating ?? 0) : 0;
        $myRating      = (float) ($restaurant->average_rating ?? 0);
        $ratingGap     = round($myRating - $leaderRating, 2); // negative = behind leader

        $leaderReviews = $leader ? (int) ($leader->total_reviews ?? 0) : 0;
        $myReviews     = (int) ($restaurant->total_reviews ?? 0);
        $reviewGap     = $myReviews - $leaderReviews; // negative = behind leader

        // Top 5 competitors (excluding self)
        $competitors = $cityRestaurants->where('id', '!=', $restaurant->id)->take(5)->values();

        $recommendations = $this->buildRecommendations($restaurant, $ratingGap, $reviewGap, $rank, $totalInCity, $leader);

        return [
            'rank'          => $rank,
            'total_in_city' => $totalInCity,
            'percentile'    => $percentile,
            'rating_gap'    => $ratingGap,
            'review_gap'    => $reviewGap,
            'competitors'   => $competitors,
            'recommendations' => $recommendations,
            'leader'        => $leader,
            'city'          => $restaurant->city,
        ];
    }

    /**
     * Build actionable recommendations based on competitive gaps.
     */
    protected function buildRecommendations(
        Restaurant $restaurant,
        float      $ratingGap,
        int        $reviewGap,
        int        $rank,
        int        $totalInCity,
        ?Restaurant $leader
    ): array {
        $recommendations = [];

        // Rating gap recommendation
        if ($ratingGap < -0.5) {
            $pts = abs(round($ratingGap, 1));
            $recommendations[] = [
                'icon'    => '⭐',
                'text'    => "El líder tiene {$pts} puntos más de calificación. Responde activamente a las reseñas negativas para mejorar tu promedio.",
                'cta'     => 'Ver Reseñas',
                'cta_url' => '/owner/reviews',
            ];
        } elseif ($ratingGap < -0.1) {
            $pts = abs(round($ratingGap, 1));
            $recommendations[] = [
                'icon'    => '⭐',
                'text'    => "Estás a solo {$pts} pts del líder. Un esfuerzo enfocado en la calidad del servicio puede ponerte en #1.",
                'cta'     => 'Ver Reseñas',
                'cta_url' => '/owner/reviews',
            ];
        }

        // Review volume gap recommendation
        if ($leader && $leader->total_reviews > 0 && ($restaurant->total_reviews ?? 0) > 0) {
            $ratio = round($leader->total_reviews / max($restaurant->total_reviews, 1), 1);
            if ($ratio >= 2) {
                $recommendations[] = [
                    'icon'    => '📲',
                    'text'    => "El líder tiene {$ratio}x más reseñas que tú. Activa el sistema de solicitud de reseñas por SMS para cerrar la brecha rápidamente.",
                    'cta'     => 'Activar SMS',
                    'cta_url' => '/owner/review-hub',
                ];
            } elseif ($reviewGap < -10) {
                $gap = abs($reviewGap);
                $recommendations[] = [
                    'icon'    => '📲',
                    'text'    => "El líder tiene {$gap} reseñas más. Invita a tus clientes frecuentes a dejar su opinión en FAMER.",
                    'cta'     => 'Solicitar Reseñas',
                    'cta_url' => '/owner/review-hub',
                ];
            }
        } elseif (($restaurant->total_reviews ?? 0) === 0) {
            $recommendations[] = [
                'icon'    => '📲',
                'text'    => 'Aún no tienes reseñas en FAMER. Envía tu primer recordatorio a tus clientes para comenzar a construir tu reputación.',
                'cta'     => 'Solicitar Reseñas',
                'cta_url' => '/owner/review-hub',
            ];
        }

        // Rank-based recommendation
        if ($rank > 3 && $totalInCity >= 5) {
            $recommendations[] = [
                'icon'    => '🏆',
                'text'    => "Estás en el puesto #{$rank} de {$totalInCity}. Completa tu perfil al 100% (fotos, menú, horarios) para subir posiciones.",
                'cta'     => 'Completar Perfil',
                'cta_url' => '/owner/profile',
            ];
        }

        // Fallback if no specific recommendations
        if (empty($recommendations)) {
            $recommendations[] = [
                'icon'    => '🚀',
                'text'    => '¡Vas muy bien! Mantén tu calidad y sigue respondiendo a tus clientes para mantenerte en el top.',
                'cta'     => 'Ver Dashboard',
                'cta_url' => '/owner/dashboard',
            ];
        }

        return array_slice($recommendations, 0, 3);
    }

    /**
     * Return empty insights when no city data exists.
     */
    protected function emptyInsights(Restaurant $restaurant): array
    {
        return [
            'rank'            => 1,
            'total_in_city'   => 1,
            'percentile'      => 100,
            'rating_gap'      => 0.0,
            'review_gap'      => 0,
            'competitors'     => collect(),
            'recommendations' => [],
            'leader'          => null,
            'city'            => $restaurant->city,
        ];
    }
}
