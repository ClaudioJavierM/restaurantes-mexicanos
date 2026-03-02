<?php

namespace App\Observers;

use App\Models\Review;
use App\Models\OwnerNotification;

class ReviewObserver
{
    public function created(Review $review): void
    {
        // Update restaurant rating average
        try {
            $review->restaurant->updateRating();
        } catch (\Exception $e) {
            \Log::error('Failed to update rating on review create: ' . $e->getMessage());
        }

        // Only notify for approved reviews on claimed restaurants
        if ($review->status === 'approved' && $review->restaurant->is_claimed && $review->restaurant->owner_id) {
            OwnerNotification::notifyNewReview($review->restaurant, $review);
        }
    }

    public function updated(Review $review): void
    {
        // Update restaurant rating average
        try {
            $review->restaurant->updateRating();
        } catch (\Exception $e) {
            \Log::error('Failed to update rating on review update: ' . $e->getMessage());
        }

        // Notify when a review gets approved
        if ($review->wasChanged('status') && $review->status === 'approved') {
            if ($review->restaurant->is_claimed && $review->restaurant->owner_id) {
                OwnerNotification::notifyNewReview($review->restaurant, $review);
            }
        }
    }
}
