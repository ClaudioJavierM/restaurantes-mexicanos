<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Restaurant;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class ReviewController extends Controller
{
    /**
     * Get reviews for a restaurant
     */
    public function index(Request $request, $restaurantId): JsonResponse
    {
        $restaurant = Restaurant::findOrFail($restaurantId);

        $reviews = $restaurant->reviews()
            ->with(['user:id,name,avatar'])
            ->where('status', 'approved')
            ->when($request->sort === 'recent', function ($q) {
                $q->orderBy('created_at', 'desc');
            })
            ->when($request->sort === 'highest', function ($q) {
                $q->orderBy('rating', 'desc');
            })
            ->when($request->sort === 'lowest', function ($q) {
                $q->orderBy('rating', 'asc');
            })
            ->when($request->sort === 'helpful', function ($q) {
                $q->orderBy('helpful_count', 'desc');
            })
            ->when(!$request->sort, function ($q) {
                $q->orderBy('created_at', 'desc');
            })
            ->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $reviews->items(),
            'meta' => [
                'current_page' => $reviews->currentPage(),
                'last_page' => $reviews->lastPage(),
                'total' => $reviews->total(),
                'average_rating' => $restaurant->average_rating,
                'total_reviews' => $restaurant->total_reviews,
                'rating_distribution' => $this->getRatingDistribution($restaurant),
            ]
        ]);
    }

    /**
     * Create a review
     */
    public function store(Request $request, $restaurantId): JsonResponse
    {
        $restaurant = Restaurant::findOrFail($restaurantId);
        $user = $request->user();

        // Check if user already reviewed this restaurant
        $existingReview = Review::where('user_id', $user->id)
            ->where('restaurant_id', $restaurantId)
            ->first();

        if ($existingReview) {
            return response()->json([
                'success' => false,
                'message' => 'Ya has escrito una reseña para este restaurante',
                'data' => ['review_id' => $existingReview->id]
            ], 400);
        }

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'title' => 'nullable|string|max:100',
            'comment' => 'required|string|min:10|max:2000',
            'visit_date' => 'nullable|date|before_or_equal:today',
            'photos' => 'nullable|array|max:5',
            'photos.*' => 'image|max:5120', // 5MB each
        ]);

        // Handle photo uploads
        $photoUrls = [];
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                $path = $photo->store('reviews/' . $restaurantId, 'public');
                $photoUrls[] = Storage::url($path);
            }
        }

        $review = Review::create([
            'user_id' => $user->id,
            'restaurant_id' => $restaurantId,
            'rating' => $validated['rating'],
            'title' => $validated['title'] ?? null,
            'comment' => $validated['comment'],
            'visit_date' => $validated['visit_date'] ?? null,
            'photos' => $photoUrls,
            'status' => 'approved', // Auto-approve for now
        ]);

        // Update restaurant rating
        $this->updateRestaurantRating($restaurant);

        // Update fan score
        $fanScore = \App\Models\FanScore::getOrCreate($user->id, $restaurantId);
        $fanScore->addAction('review');

        return response()->json([
            'success' => true,
            'message' => 'Reseña publicada exitosamente',
            'data' => $review->load('user:id,name,avatar'),
        ], 201);
    }

    /**
     * Update a review
     */
    public function update(Request $request, $reviewId): JsonResponse
    {
        $review = Review::where('id', $reviewId)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        $validated = $request->validate([
            'rating' => 'sometimes|integer|min:1|max:5',
            'title' => 'nullable|string|max:100',
            'comment' => 'sometimes|string|min:10|max:2000',
        ]);

        $review->update($validated);

        // Update restaurant rating
        $this->updateRestaurantRating($review->restaurant);

        return response()->json([
            'success' => true,
            'message' => 'Reseña actualizada exitosamente',
            'data' => $review->fresh()->load('user:id,name,avatar'),
        ]);
    }

    /**
     * Delete a review
     */
    public function destroy(Request $request, $reviewId): JsonResponse
    {
        $review = Review::where('id', $reviewId)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        $restaurant = $review->restaurant;

        // Delete photos
        if ($review->photos) {
            foreach ($review->photos as $photo) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $photo));
            }
        }

        $review->delete();

        // Update restaurant rating
        $this->updateRestaurantRating($restaurant);

        return response()->json([
            'success' => true,
            'message' => 'Reseña eliminada exitosamente',
        ]);
    }

    /**
     * Mark review as helpful
     */
    public function markHelpful(Request $request, $reviewId): JsonResponse
    {
        $review = Review::findOrFail($reviewId);
        $user = $request->user();

        // Check if user already marked this review
        $alreadyMarked = $review->helpfulVotes()
            ->where('user_id', $user->id)
            ->exists();

        if ($alreadyMarked) {
            // Remove vote
            $review->helpfulVotes()->detach($user->id);
            $review->decrement('helpful_count');
            $message = 'Voto removido';
        } else {
            // Add vote
            $review->helpfulVotes()->attach($user->id);
            $review->increment('helpful_count');
            $message = 'Marcado como útil';
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => [
                'helpful_count' => $review->fresh()->helpful_count,
                'is_helpful' => !$alreadyMarked,
            ]
        ]);
    }

    /**
     * Report a review
     */
    public function report(Request $request, $reviewId): JsonResponse
    {
        $review = Review::findOrFail($reviewId);

        $request->validate([
            'reason' => 'required|in:spam,inappropriate,fake,other',
            'description' => 'nullable|string|max:500',
        ]);

        // Store report (you can create a ReviewReport model)
        $review->reports()->create([
            'user_id' => $request->user()->id,
            'reason' => $request->reason,
            'description' => $request->description,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Reporte enviado. Lo revisaremos pronto.',
        ]);
    }

    /**
     * Get rating distribution
     */
    private function getRatingDistribution(Restaurant $restaurant): array
    {
        $distribution = $restaurant->reviews()
            ->where('status', 'approved')
            ->selectRaw('rating, COUNT(*) as count')
            ->groupBy('rating')
            ->pluck('count', 'rating')
            ->toArray();

        return [
            5 => $distribution[5] ?? 0,
            4 => $distribution[4] ?? 0,
            3 => $distribution[3] ?? 0,
            2 => $distribution[2] ?? 0,
            1 => $distribution[1] ?? 0,
        ];
    }

    /**
     * Update restaurant rating after review changes
     */
    private function updateRestaurantRating(Restaurant $restaurant): void
    {
        $stats = $restaurant->reviews()
            ->where('status', 'approved')
            ->selectRaw('AVG(rating) as avg, COUNT(*) as count')
            ->first();

        $restaurant->update([
            'average_rating' => round($stats->avg ?? 0, 1),
            'total_reviews' => $stats->count ?? 0,
        ]);
    }
}
