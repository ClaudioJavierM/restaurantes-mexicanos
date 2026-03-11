<?php

namespace App\Services;

use App\Models\Review;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class ReviewTrustService
{
    // Score starts at 50 (neutral). Above 80 = trusted, below 30 = suspicious.
    private int $baseScore = 50;

    /**
     * Analyze a new review submission and return trust data.
     */
    public function analyze(array $data, ?User $user = null): array
    {
        $score = $this->baseScore;
        $flags = [];

        // ── Positive signals ──────────────────────────────────────────────────

        // Registered user with verified email
        if ($user && $user->email_verified_at) {
            $score += 15;
            $accountAgeDays = $user->created_at->diffInDays(now());
            if ($accountAgeDays >= 30) $score += 10;
            if ($accountAgeDays >= 180) $score += 5;
        }

        // User has previous approved reviews (established reviewer)
        if ($user) {
            $approvedReviews = Review::where('user_id', $user->id)
                ->where('status', 'approved')
                ->count();
            if ($approvedReviews >= 3) {
                $score += 10;
            } elseif ($approvedReviews >= 1) {
                $score += 5;
            }
        }

        // Detailed sub-ratings provided
        $subRatings = array_filter([
            $data['service_rating'] ?? 0,
            $data['food_rating'] ?? 0,
            $data['ambiance_rating'] ?? 0,
        ]);
        if (count($subRatings) >= 2) $score += 5;

        // Comment has good length (between 50–2000 chars)
        $commentLen = strlen($data['comment'] ?? '');
        if ($commentLen >= 50 && $commentLen <= 2000) $score += 5;

        // ── Negative signals ──────────────────────────────────────────────────

        // Guest (not logged in) — less trust
        if (!$user) {
            $score -= 10;
            $flags[] = 'guest_reviewer';
        }

        // New account (< 7 days)
        if ($user && $user->created_at->diffInDays(now()) < 7) {
            $score -= 15;
            $flags[] = 'new_account';
        }

        // Disposable email
        $email = $user ? $user->email : ($data['guest_email'] ?? '');
        if ($this->isDisposableEmail($email)) {
            $score -= 25;
            $flags[] = 'disposable_email';
        }

        // Too many reviews from same IP in short window
        if ($this->isIpRateLimited(request()->ip())) {
            $score -= 20;
            $flags[] = 'ip_rate_limited';
        }

        // Burst reviews from same user in last 24h
        if ($user && $this->isUserBursting($user->id)) {
            $score -= 20;
            $flags[] = 'review_burst';
        }

        // Repetitive / low-quality comment
        if ($this->hasRepetitiveText($data['comment'] ?? '')) {
            $score -= 15;
            $flags[] = 'repetitive_text';
        }

        // Very short comment
        if ($commentLen > 0 && $commentLen < 20) {
            $score -= 10;
            $flags[] = 'comment_too_short';
        }

        // Extreme rating (1 or 5) from new/guest account
        $rating = (int) ($data['rating'] ?? 3);
        if (in_array($rating, [1, 5]) && (!$user || $user->created_at->diffInDays(now()) < 30)) {
            $score -= 10;
            $flags[] = 'extreme_rating_unverified';
        }

        // Multiple reviews for same restaurant from same user/IP
        if ($this->hasDuplicateForRestaurant($data['restaurant_id'] ?? null, $user, request()->ip())) {
            $score -= 30;
            $flags[] = 'duplicate_restaurant_review';
        }

        $score = max(0, min(100, $score));

        return [
            'trust_score'        => $score,
            'trust_flags'        => $flags,
            'is_verified'        => $user && $user->email_verified_at && $score >= 70,
            'flagged_suspicious' => $score < 30 || in_array('duplicate_restaurant_review', $flags),
            'auto_approve'       => $score >= 60 && empty(array_intersect($flags, [
                'duplicate_restaurant_review', 'ip_rate_limited', 'review_burst',
            ])),
        ];
    }

    /**
     * Re-evaluate trust score for existing reviews on a restaurant
     * and detect suspicious patterns (e.g., coordinated 5-star bursts).
     */
    public function detectSuspiciousPatterns(int $restaurantId): array
    {
        $recent = Review::where('restaurant_id', $restaurantId)
            ->where('created_at', '>=', now()->subDays(7))
            ->where('status', 'approved')
            ->get();

        $alerts = [];

        if ($recent->count() < 3) return $alerts;

        // Pattern 1: sudden burst of 5-star reviews
        $fiveStars = $recent->where('rating', 5)->count();
        if ($fiveStars / $recent->count() > 0.8 && $recent->count() >= 5) {
            $alerts[] = [
                'type'    => 'rating_burst',
                'message' => 'Más del 80% de las reseñas recientes son de 5 estrellas.',
            ];
        }

        // Pattern 2: multiple reviews from same IP
        $ipGroups = $recent->groupBy('ip_address')->filter(fn($g) => $g->count() > 2);
        if ($ipGroups->isNotEmpty()) {
            $alerts[] = [
                'type'    => 'ip_cluster',
                'message' => 'Múltiples reseñas desde la misma dirección IP.',
            ];
        }

        // Pattern 3: suspiciously similar comments
        $comments = $recent->pluck('comment')->toArray();
        if ($this->hasSimilarComments($comments)) {
            $alerts[] = [
                'type'    => 'similar_comments',
                'message' => 'Se detectaron comentarios muy similares entre sí.',
            ];
        }

        // Pattern 4: all reviewers are brand-new accounts
        $newAccounts = $recent->filter(function ($r) {
            return $r->user && $r->user->created_at->diffInDays(now()) < 14;
        });
        if ($recent->count() >= 4 && $newAccounts->count() / $recent->count() > 0.6) {
            $alerts[] = [
                'type'    => 'new_account_cluster',
                'message' => 'La mayoría de los reviewers son cuentas nuevas (< 14 días).',
            ];
        }

        return $alerts;
    }

    // ─── Private helpers ──────────────────────────────────────────────────────

    private function isDisposableEmail(string $email): bool
    {
        $disposable = [
            'tempmail.com', 'guerrillamail.com', '10minutemail.com', 'mailinator.com',
            'throwaway.email', 'temp-mail.org', 'yopmail.com', 'fakeinbox.com',
            'maildrop.cc', 'getnada.com', 'trashmail.com', 'sharklasers.com',
            'trashmail.me', 'dispostable.com', 'spamgourmet.com',
        ];
        $domain = strtolower(Str::after($email, '@'));
        return in_array($domain, $disposable);
    }

    private function isIpRateLimited(string $ip): bool
    {
        $key   = 'review_ip_' . md5($ip);
        $count = Cache::get($key, 0);
        if ($count >= 5) return true;
        Cache::put($key, $count + 1, now()->addHours(24));
        return false;
    }

    private function isUserBursting(int $userId): bool
    {
        $count = Review::where('user_id', $userId)
            ->where('created_at', '>=', now()->subHours(24))
            ->count();
        return $count >= 3;
    }

    private function hasRepetitiveText(string $text): bool
    {
        if (strlen($text) < 10) return false;
        $words = str_word_count(strtolower($text), 1);
        if (count($words) < 5) return false;
        $counts = array_count_values($words);
        foreach ($counts as $count) {
            if ($count / count($words) > 0.4) return true;
        }
        return false;
    }

    private function hasDuplicateForRestaurant(?int $restaurantId, ?User $user, string $ip): bool
    {
        if (!$restaurantId) return false;

        $query = Review::where('restaurant_id', $restaurantId)
            ->where('created_at', '>=', now()->subDays(30));

        if ($user) {
            return $query->where('user_id', $user->id)->exists();
        }

        return $query->where('ip_address', $ip)->whereNull('user_id')->exists();
    }

    private function hasSimilarComments(array $comments): bool
    {
        if (count($comments) < 3) return false;
        $similar = 0;
        for ($i = 0; $i < count($comments) - 1; $i++) {
            similar_text(
                strtolower($comments[$i]),
                strtolower($comments[$i + 1]),
                $percent
            );
            if ($percent > 70) $similar++;
        }
        return $similar >= 2;
    }
}
