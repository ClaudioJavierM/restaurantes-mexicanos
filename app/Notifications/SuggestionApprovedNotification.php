<?php

namespace App\Notifications;

use App\Models\Restaurant;
use App\Models\Suggestion;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SuggestionApprovedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public Suggestion $suggestion, public ?Restaurant $restaurant = null)
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
        $restaurantName = $this->suggestion->restaurant_name;

        $message = (new MailMessage)
            ->subject(__('notifications.suggestion_approved.subject'))
            ->greeting(__('notifications.suggestion_approved.greeting', ['name' => $notifiable->name ?? $this->suggestion->submitter_name]))
            ->line(__('notifications.suggestion_approved.message', ['restaurant' => $restaurantName]))
            ->line(__('notifications.suggestion_approved.details', [
                'city' => $this->suggestion->restaurant_city,
                'state' => $this->suggestion->restaurant_state,
            ]));

        // Si ya se creó el restaurante, agregar enlace
        if ($this->restaurant) {
            $message->action(
                __('notifications.suggestion_approved.action'),
                route('restaurant.show', $this->restaurant->slug)
            );
        }

        $message->line(__('notifications.suggestion_approved.thanks'))
            ->salutation(__('notifications.salutation'));

        return $message;
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $data = [
            'suggestion_id' => $this->suggestion->id,
            'restaurant_name' => $this->suggestion->restaurant_name,
            'restaurant_city' => $this->suggestion->restaurant_city,
            'restaurant_state' => $this->suggestion->restaurant_state,
            'message' => __('notifications.suggestion_approved.notification_message', [
                'restaurant' => $this->suggestion->restaurant_name,
            ]),
        ];

        // Si se creó el restaurante, agregar información
        if ($this->restaurant) {
            $data['restaurant_id'] = $this->restaurant->id;
            $data['restaurant_slug'] = $this->restaurant->slug;
        }

        return $data;
    }
}
