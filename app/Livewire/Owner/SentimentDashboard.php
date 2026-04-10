<?php

namespace App\Livewire\Owner;

use App\Models\Review;
use App\Services\ReviewSentimentService;
use Livewire\Component;

class SentimentDashboard extends Component
{
    public int $restaurantId;

    protected static bool $isLazy = true;

    public function getSentimentSummaryProperty(): array
    {
        return app(ReviewSentimentService::class)->getSentimentSummary($this->restaurantId);
    }

    public function getRecentSentimentProperty()
    {
        return Review::where('restaurant_id', $this->restaurantId)
            ->where('status', 'approved')
            ->whereNotNull('sentiment_analyzed_at')
            ->orderByDesc('sentiment_analyzed_at')
            ->limit(10)
            ->get();
    }

    public function render()
    {
        return view('livewire.owner.sentiment-dashboard', [
            'summary'       => $this->sentimentSummary,
            'recentReviews' => $this->recentSentiment,
        ]);
    }
}
