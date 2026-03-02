<?php

namespace App\Services;

use App\Models\Suggestion;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class SpamDetectionService
{
    /**
     * Analiza una sugerencia para detectar spam
     *
     * @param array $data
     * @return array
     */
    public function analyzeSuggestion(array $data): array
    {
        $spamScore = 0;
        $flags = [];

        // 1. Detectar emails desechables
        if ($this->isDisposableEmail($data['submitter_email'] ?? '')) {
            $spamScore += 30;
            $flags[] = 'disposable_email';
        }

        // 2. Detectar patrones sospechosos en el nombre
        if ($this->hasSuspiciousPattern($data['restaurant_name'] ?? '')) {
            $spamScore += 20;
            $flags[] = 'suspicious_name_pattern';
        }

        // 3. Detectar texto repetitivo o spam
        if ($this->hasRepetitiveText($data['description'] ?? '')) {
            $spamScore += 15;
            $flags[] = 'repetitive_text';
        }

        // 4. Detectar URLs sospechosas
        if ($this->hasSuspiciousUrl($data['restaurant_website'] ?? '')) {
            $spamScore += 25;
            $flags[] = 'suspicious_url';
        }

        // 5. Verificar rate limiting (mismo IP/email)
        if ($this->isRateLimited($data['submitter_email'] ?? '', request()->ip())) {
            $spamScore += 40;
            $flags[] = 'rate_limited';
        }

        // 6. Detectar información incompleta o sospechosa
        if ($this->hasIncompleteInfo($data)) {
            $spamScore += 10;
            $flags[] = 'incomplete_info';
        }

        // 7. Verificar historial del submitter
        $submitterHistory = $this->checkSubmitterHistory($data['submitter_email'] ?? '');
        if ($submitterHistory['is_suspicious']) {
            $spamScore += $submitterHistory['score'];
            $flags = array_merge($flags, $submitterHistory['flags']);
        }

        return [
            'spam_score' => min($spamScore, 100),
            'is_spam' => $spamScore >= 60,
            'is_suspicious' => $spamScore >= 30,
            'flags' => $flags,
            'risk_level' => $this->getRiskLevel($spamScore),
        ];
    }

    /**
     * Verifica si es un email desechable
     */
    protected function isDisposableEmail(string $email): bool
    {
        $disposableDomains = [
            'tempmail.com', 'guerrillamail.com', '10minutemail.com', 'mailinator.com',
            'throwaway.email', 'temp-mail.org', 'yopmail.com', 'fakeinbox.com',
            'maildrop.cc', 'getnada.com', 'trashmail.com', 'sharklasers.com',
        ];

        $domain = Str::after($email, '@');

        return in_array(strtolower($domain), $disposableDomains);
    }

    /**
     * Detecta patrones sospechosos en el nombre
     */
    protected function hasSuspiciousPattern(string $name): bool
    {
        $patterns = [
            '/test/i',
            '/fake/i',
            '/xxx/i',
            '/\d{5,}/', // Muchos números seguidos
            '/(.)\1{4,}/', // Caracteres repetidos (aaaa, bbbb)
            '/^[^a-zA-Z]+$/', // Solo caracteres especiales
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $name)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Detecta texto repetitivo
     */
    protected function hasRepetitiveText(string $text): bool
    {
        if (empty($text)) {
            return false;
        }

        // Dividir en palabras
        $words = str_word_count(strtolower($text), 1);

        if (count($words) < 5) {
            return false;
        }

        // Contar frecuencia de palabras
        $wordCounts = array_count_values($words);

        // Si alguna palabra aparece más del 40% del tiempo, es sospechoso
        foreach ($wordCounts as $count) {
            if ($count / count($words) > 0.4) {
                return true;
            }
        }

        return false;
    }

    /**
     * Detecta URLs sospechosas
     */
    protected function hasSuspiciousUrl(string $url): bool
    {
        if (empty($url)) {
            return false;
        }

        $suspiciousPatterns = [
            '/\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}/', // IP address
            '/\.tk$/', // Dominios gratuitos sospechosos
            '/\.ga$/',
            '/\.cf$/',
            '/\.ml$/',
            '/\.gq$/',
        ];

        foreach ($suspiciousPatterns as $pattern) {
            if (preg_match($pattern, $url)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Verifica rate limiting
     */
    protected function isRateLimited(string $email, string $ip): bool
    {
        $emailKey = "submissions_email_" . md5($email);
        $ipKey = "submissions_ip_" . md5($ip);

        $emailCount = Cache::get($emailKey, 0);
        $ipCount = Cache::get($ipKey, 0);

        // Más de 3 sugerencias en 1 hora del mismo email o IP
        if ($emailCount >= 3 || $ipCount >= 5) {
            return true;
        }

        // Incrementar contadores
        Cache::put($emailKey, $emailCount + 1, 3600);
        Cache::put($ipKey, $ipCount + 1, 3600);

        return false;
    }

    /**
     * Verifica si la información está incompleta
     */
    protected function hasIncompleteInfo(array $data): bool
    {
        // Campos críticos que deberían estar completos
        $criticalFields = [
            'restaurant_name',
            'restaurant_address',
            'restaurant_city',
            'restaurant_state',
            'submitter_name',
            'submitter_email',
        ];

        foreach ($criticalFields as $field) {
            if (empty($data[$field]) || strlen($data[$field]) < 3) {
                return true;
            }
        }

        // Si falta teléfono Y website, es sospechoso
        if (empty($data['restaurant_phone']) && empty($data['restaurant_website'])) {
            return true;
        }

        return false;
    }

    /**
     * Verifica el historial del submitter
     */
    protected function checkSubmitterHistory(string $email): array
    {
        if (empty($email)) {
            return ['is_suspicious' => false, 'score' => 0, 'flags' => []];
        }

        // Contar sugerencias previas
        $previousSuggestions = Suggestion::where('submitter_email', $email)->get();

        if ($previousSuggestions->isEmpty()) {
            return ['is_suspicious' => false, 'score' => 0, 'flags' => []];
        }

        $score = 0;
        $flags = [];

        // Si ha tenido muchas rechazadas
        $rejected = $previousSuggestions->where('status', 'rejected')->count();
        if ($rejected > 2) {
            $score += 20;
            $flags[] = 'history_rejected';
        }

        // Si todas fueron rechazadas
        if ($rejected === $previousSuggestions->count() && $rejected > 0) {
            $score += 30;
            $flags[] = 'all_rejected';
        }

        // Si tiene muchas sugerencias pendientes (posible spam)
        $pending = $previousSuggestions->where('status', 'pending')->count();
        if ($pending > 5) {
            $score += 25;
            $flags[] = 'too_many_pending';
        }

        return [
            'is_suspicious' => $score > 0,
            'score' => $score,
            'flags' => $flags,
            'total_submissions' => $previousSuggestions->count(),
            'rejected' => $rejected,
            'approved' => $previousSuggestions->where('status', 'approved')->count(),
        ];
    }

    /**
     * Obtiene el nivel de riesgo
     */
    protected function getRiskLevel(int $score): string
    {
        if ($score >= 60) return 'high';
        if ($score >= 30) return 'medium';
        return 'low';
    }

    /**
     * Limpia el cache de rate limiting (para testing o casos especiales)
     */
    public function clearRateLimits(string $email, string $ip): void
    {
        Cache::forget("submissions_email_" . md5($email));
        Cache::forget("submissions_ip_" . md5($ip));
    }
}
