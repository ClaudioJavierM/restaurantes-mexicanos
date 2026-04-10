<?php

namespace App\Mail;

use App\Models\Restaurant;
use App\Models\AnalyticsEvent;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Headers;
use Illuminate\Queue\SerializesModels;
use Carbon\Carbon;

class WeeklyReport extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public Restaurant $restaurant;
    public array $stats;
    public string $weekRange;

    public function __construct(Restaurant $restaurant)
    {
        $this->restaurant = $restaurant;
        $this->stats = $this->calculateStats();
        $this->weekRange = Carbon::now()->subWeek()->format('M d') . ' - ' . Carbon::now()->format('M d, Y');
    }

    protected function calculateStats(): array
    {
        $restaurant = $this->restaurant;
        $lastWeek = Carbon::now()->subWeek();
        $twoWeeksAgo = Carbon::now()->subWeeks(2);
        
        $thisWeekViews = AnalyticsEvent::where('restaurant_id', $restaurant->id)
            ->where('event_type', 'page_view')
            ->where('created_at', '>=', $lastWeek)
            ->count();
        
        $lastWeekViews = AnalyticsEvent::where('restaurant_id', $restaurant->id)
            ->where('event_type', 'page_view')
            ->whereBetween('created_at', [$twoWeeksAgo, $lastWeek])
            ->count();
        
        $growth = $lastWeekViews > 0 
            ? round((($thisWeekViews - $lastWeekViews) / $lastWeekViews) * 100, 1)
            : ($thisWeekViews > 0 ? 100 : 0);
        
        $totalViews = AnalyticsEvent::where('restaurant_id', $restaurant->id)
            ->where('event_type', 'page_view')
            ->count();
        
        $newReviews = $restaurant->reviews()
            ->where('created_at', '>=', $lastWeek)
            ->count();
        
        $avgRating = $restaurant->reviews()
            ->where('status', 'approved')
            ->avg('rating') ?? 0;
        
        $pendingResponses = $restaurant->reviews()
            ->where('status', 'approved')
            ->whereNull('response')
            ->count();
        
        $cityRestaurants = Restaurant::where('city', $restaurant->city)
            ->where('is_claimed', true)
            ->count();
        
        return [
            'this_week_views' => $thisWeekViews,
            'last_week_views' => $lastWeekViews,
            'growth' => $growth,
            'total_views' => $totalViews,
            'new_reviews' => $newReviews,
            'avg_rating' => round($avgRating, 1),
            'pending_responses' => $pendingResponses,
            'city_total' => $cityRestaurants,
        ];
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '📊 Reporte Semanal: ' . $this->restaurant->name . ' | FAMER',
        );
    }

    public function headers(): Headers
    {
        return new Headers(
            text: [
                'X-Mailer' => 'FAMER-Platform',
                'List-Unsubscribe' => '<mailto:unsubscribe@restaurantesmexicanosfamosos.com>',
                'List-Unsubscribe-Post' => 'List-Unsubscribe=One-Click',
                'Precedence' => 'bulk',
            ],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.weekly-report',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
