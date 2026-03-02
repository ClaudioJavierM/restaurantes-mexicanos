<?php

namespace App\Listeners;

use Illuminate\Mail\Events\MessageSent;
use App\Models\EmailLog;
use Illuminate\Support\Str;
use Symfony\Component\Mime\Address;

class LogSentEmail
{
    public function handle(MessageSent $event): void
    {
        try {
            $message = $event->message;
            
            // Get recipients
            $to = $message->getTo();
            $toEmail = '';
            $toName = '';
            
            if (!empty($to)) {
                $firstTo = reset($to);
                if ($firstTo instanceof Address) {
                    $toEmail = $firstTo->getAddress();
                    $toName = $firstTo->getName() ?? '';
                } elseif (is_string($firstTo)) {
                    $toEmail = $firstTo;
                }
            }
            
            // Get sender
            $from = $message->getFrom();
            $fromEmail = '';
            $fromName = '';
            
            if (!empty($from)) {
                $firstFrom = reset($from);
                if ($firstFrom instanceof Address) {
                    $fromEmail = $firstFrom->getAddress();
                    $fromName = $firstFrom->getName() ?? '';
                } elseif (is_string($firstFrom)) {
                    $fromEmail = $firstFrom;
                }
            }
            
            // Get subject
            $subject = $message->getSubject() ?? 'Sin asunto';
            
            // Get body preview
            $body = $message->getBody();
            $bodyPreview = '';
            
            if ($body) {
                $bodyContent = $body->bodyToString();
                $bodyPreview = Str::limit(strip_tags($bodyContent), 500);
            }
            
            // Detect category and type
            $category = $this->detectCategory($subject, $event->data['mailable'] ?? null);
            $type = $this->detectType($category);
            
            // Get metadata
            $metadata = [];
            if (isset($event->data['mailable']) && method_exists($event->data['mailable'], 'getMetadata')) {
                $metadata = $event->data['mailable']->getMetadata();
            }
            
            // Get restaurant_id and user_id from mailable if available
            $restaurantId = null;
            $userId = null;
            
            if (isset($event->data['mailable'])) {
                $mailable = $event->data['mailable'];
                if (property_exists($mailable, 'restaurant_id')) {
                    $restaurantId = $mailable->restaurant_id;
                } elseif (property_exists($mailable, 'restaurant') && $mailable->restaurant) {
                    $restaurantId = $mailable->restaurant->id ?? null;
                }
                if (property_exists($mailable, 'user_id')) {
                    $userId = $mailable->user_id;
                }
            }

            EmailLog::create([
                'type' => $type,
                'category' => $category,
                'to_email' => $toEmail,
                'to_name' => $toName,
                'from_email' => $fromEmail,
                'from_name' => $fromName,
                'subject' => $subject,
                'body_preview' => $bodyPreview,
                'mailable_class' => isset($event->data['mailable']) ? get_class($event->data['mailable']) : null,
                'metadata' => $metadata,
                'status' => EmailLog::STATUS_SENT,
                'sent_at' => now(),
                'provider' => config('mail.default'),
                'message_id' => $message->getHeaders()->get('Message-ID')?->getBodyAsString(),
                'restaurant_id' => $restaurantId,
                'user_id' => $userId,
            ]);
        } catch (\Exception $e) {
            \Log::warning('Failed to log email', ['error' => $e->getMessage()]);
        }
    }

    protected function detectCategory(string $subject, $mailable): string
    {
        // Check mailable class name first
        if ($mailable) {
            $className = get_class($mailable);
            
            if (str_contains($className, 'Reservation')) return 'reservation';
            if (str_contains($className, 'Order')) return 'order';
            if (str_contains($className, 'Claim')) return 'claim';
            if (str_contains($className, 'Review')) return 'review';
            if (str_contains($className, 'Welcome')) return 'welcome';
            if (str_contains($className, 'Password')) return 'password';
            if (str_contains($className, 'Verify')) return 'verification';
            if (str_contains($className, 'Newsletter') || str_contains($className, 'Campaign')) return 'marketing';
            if (str_contains($className, 'FamerIntroduction')) return 'famer_email_1';
            if (str_contains($className, 'FamerHowItWorks')) return 'famer_email_2';
            if (str_contains($className, 'FamerReminder')) return 'famer_email_3';
        }
        
        // Check subject
        $subjectLower = strtolower($subject);
        
        if (str_contains($subjectLower, 'reservación') || str_contains($subjectLower, 'reservation')) return 'reservation';
        if (str_contains($subjectLower, 'pedido') || str_contains($subjectLower, 'order')) return 'order';
        if (str_contains($subjectLower, 'reclamo') || str_contains($subjectLower, 'claim')) return 'claim';
        if (str_contains($subjectLower, 'reseña') || str_contains($subjectLower, 'review')) return 'review';
        if (str_contains($subjectLower, 'bienvenid') || str_contains($subjectLower, 'welcome')) return 'welcome';
        if (str_contains($subjectLower, 'contraseña') || str_contains($subjectLower, 'password')) return 'password';
        if (str_contains($subjectLower, 'verific')) return 'verification';
        
        return 'other';
    }

    protected function detectType(string $category): string
    {
        return match($category) {
            'marketing', 'famer_email_1', 'famer_email_2', 'famer_email_3' => 'campaign',
            'famer_email' => 'campaign',
            'notification' => 'notification',
            default => 'transactional',
        };
    }
}
