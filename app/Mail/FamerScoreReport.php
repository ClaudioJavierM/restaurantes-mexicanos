<?php

namespace App\Mail;

use App\Models\FamerScoreRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class FamerScoreReport extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public FamerScoreRequest $request;
    public array $restaurant;
    public array $scoreData;
    public string $recipientName;
    public string $claimUrl;
    public string $unsubscribeUrl;

    /**
     * Create a new message instance.
     */
    public function __construct(
        FamerScoreRequest $request,
        array $restaurant,
        array $scoreData
    ) {
        $this->request = $request;
        $this->restaurant = $restaurant;
        $this->scoreData = $scoreData;
        $this->recipientName = $request->name ?? 'Restaurant Owner';

        // Build claim URL with tracking
        $this->claimUrl = url('/claim?' . http_build_query([
            'restaurant' => $restaurant['slug'] ?? null,
            'utm_source' => 'famer_score_report',
            'utm_medium' => 'email',
            'utm_campaign' => 'score_report',
            'ref' => $request->id,
        ]));

        $this->unsubscribeUrl = url('/unsubscribe?' . http_build_query([
            'email' => $request->email,
            'token' => md5($request->email . config('app.key')),
        ]));
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $grade = $this->scoreData['letter_grade'] ?? 'N/A';
        $score = $this->scoreData['overall_score'] ?? 0;
        $restaurantName = $this->restaurant['name'] ?? 'Tu Restaurante';

        return new Envelope(
            subject: "Tu FAMER Score: {$score} ({$grade}) - {$restaurantName}",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.famer-score-report',
            with: [
                'gradeColorHex' => $this->getGradeColorHex(),
                'gradeBgColorHex' => $this->getGradeBgColorHex(),
            ],
        );
    }

    /**
     * Get the grade color hex code for email
     */
    public function getGradeColorHex(): string
    {
        return match ($this->scoreData['grade_color'] ?? 'gray') {
            'emerald' => '#10B981',
            'blue' => '#3B82F6',
            'yellow' => '#F59E0B',
            'orange' => '#F97316',
            'red' => '#EF4444',
            default => '#6B7280',
        };
    }

    /**
     * Get the grade background color hex for email
     */
    public function getGradeBgColorHex(): string
    {
        return match ($this->scoreData['grade_color'] ?? 'gray') {
            'emerald' => '#D1FAE5',
            'blue' => '#DBEAFE',
            'yellow' => '#FEF3C7',
            'orange' => '#FFEDD5',
            'red' => '#FEE2E2',
            default => '#F3F4F6',
        };
    }

    /**
     * Get priority badge color
     */
    public function getPriorityColor(string $priority): string
    {
        return match ($priority) {
            'critical' => '#EF4444',
            'high' => '#F97316',
            'medium' => '#F59E0B',
            'low' => '#10B981',
            default => '#6B7280',
        };
    }

    /**
     * Get priority badge background
     */
    public function getPriorityBgColor(string $priority): string
    {
        return match ($priority) {
            'critical' => '#FEE2E2',
            'high' => '#FFEDD5',
            'medium' => '#FEF3C7',
            'low' => '#D1FAE5',
            default => '#F3F4F6',
        };
    }

    /**
     * Get category icon
     */
    public function getCategoryIcon(string $category): string
    {
        return match ($category) {
            'profile' => '👤',
            'presence' => '🌐',
            'engagement' => '💬',
            'menu' => '📖',
            'authenticity' => '🔥',
            'digital' => '📱',
            default => '📊',
        };
    }

    /**
     * Get attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
