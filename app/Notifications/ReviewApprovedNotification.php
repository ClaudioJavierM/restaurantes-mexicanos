<?php

namespace App\Notifications;

use App\Models\Review;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReviewApprovedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public Review $review)
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $restaurantName = $this->review->restaurant->name;
        $restaurantSlug = $this->review->restaurant->slug;

        return (new MailMessage)
            ->subject(__('notifications.review_approved.subject'))
            ->greeting(__('notifications.review_approved.greeting', ['name' => $notifiable->name ?? $this->review->guest_name]))
            ->line(__('notifications.review_approved.message', ['restaurant' => $restaurantName]))
            ->line(__('notifications.review_approved.details', [
                'rating' => $this->review->rating,
                'title' => $this->review->title,
            ]))
            ->action(__('notifications.review_approved.action'), route('restaurant.show', $restaurantSlug))
            ->line(__('notifications.review_approved.thanks'))
            ->salutation(__('notifications.salutation'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'review_id' => $this->review->id,
            'restaurant_id' => $this->review->restaurant_id,
            'restaurant_name' => $this->review->restaurant->name,
            'restaurant_slug' => $this->review->restaurant->slug,
            'rating' => $this->review->rating,
            'title' => $this->review->title,
            'message' => __('notifications.review_approved.notification_message', [
                'restaurant' => $this->review->restaurant->name,
            ]),
        ];
    }
}
