<?php

namespace App\Livewire\Owner;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Restaurant;
use App\Models\ExternalReview;
use App\Models\PlatformConnection;
use App\Models\ReviewResponseTemplate;
use App\Services\GoogleBusinessService;
use App\Services\FacebookBusinessService;
use Illuminate\Support\Facades\Auth;

class ReviewHub extends Component
{
    use WithPagination;

    public Restaurant $restaurant;
    
    // Filters
    public $platformFilter = '';
    public $ratingFilter = '';
    public $responseFilter = '';
    public $searchQuery = '';
    
    // Active tab
    public $activeTab = 'dashboard';
    
    // Response modal
    public $showResponseModal = false;
    public $selectedReview = null;
    public $responseText = '';
    public $selectedTemplate = '';
    
    // Stats
    public $stats = [];
    
    protected $queryString = ['activeTab', 'platformFilter', 'ratingFilter'];
    
    public function mount(Restaurant $restaurant)
    {
        // Verify ownership
        if ($restaurant->user_id !== Auth::id()) {
            abort(403, 'No tienes permiso para ver este restaurante');
        }
        
        $this->restaurant = $restaurant;
        $this->loadStats();
    }
    
    public function loadStats()
    {
        $reviews = ExternalReview::where('restaurant_id', $this->restaurant->id);
        
        $this->stats = [
            'total_reviews' => $reviews->count(),
            'average_rating' => round($reviews->avg('rating'), 1) ?: 0,
            'pending_responses' => $reviews->clone()->whereNull('owner_response')->count(),
            'negative_reviews' => $reviews->clone()->where('rating', '<=', 2)->count(),
            'this_month' => $reviews->clone()->whereMonth('review_date', now()->month)->count(),
            'by_platform' => [
                'google' => ExternalReview::where('restaurant_id', $this->restaurant->id)
                    ->where('platform', 'google')->count(),
                'facebook' => ExternalReview::where('restaurant_id', $this->restaurant->id)
                    ->where('platform', 'facebook')->count(),
                'yelp' => ExternalReview::where('restaurant_id', $this->restaurant->id)
                    ->where('platform', 'yelp')->count(),
                'tripadvisor' => ExternalReview::where('restaurant_id', $this->restaurant->id)
                    ->where('platform', 'tripadvisor')->count(),
            ],
            'rating_distribution' => [
                5 => ExternalReview::where('restaurant_id', $this->restaurant->id)->where('rating', 5)->count(),
                4 => ExternalReview::where('restaurant_id', $this->restaurant->id)->where('rating', '>=', 4)->where('rating', '<', 5)->count(),
                3 => ExternalReview::where('restaurant_id', $this->restaurant->id)->where('rating', '>=', 3)->where('rating', '<', 4)->count(),
                2 => ExternalReview::where('restaurant_id', $this->restaurant->id)->where('rating', '>=', 2)->where('rating', '<', 3)->count(),
                1 => ExternalReview::where('restaurant_id', $this->restaurant->id)->where('rating', '<', 2)->count(),
            ],
        ];
    }
    
    public function setTab($tab)
    {
        $this->activeTab = $tab;
        $this->resetPage();
    }
    
    public function getReviewsProperty()
    {
        $query = ExternalReview::where('restaurant_id', $this->restaurant->id);
        
        if ($this->platformFilter) {
            $query->where('platform', $this->platformFilter);
        }
        
        if ($this->ratingFilter) {
            if ($this->ratingFilter === 'positive') {
                $query->where('rating', '>=', 4);
            } elseif ($this->ratingFilter === 'negative') {
                $query->where('rating', '<=', 2);
            } elseif ($this->ratingFilter === 'neutral') {
                $query->where('rating', '>', 2)->where('rating', '<', 4);
            }
        }
        
        if ($this->responseFilter) {
            if ($this->responseFilter === 'pending') {
                $query->whereNull('owner_response');
            } elseif ($this->responseFilter === 'responded') {
                $query->whereNotNull('owner_response');
            }
        }
        
        if ($this->searchQuery) {
            $query->where(function($q) {
                $q->where('reviewer_name', 'like', '%' . $this->searchQuery . '%')
                  ->orWhere('review_text', 'like', '%' . $this->searchQuery . '%');
            });
        }
        
        return $query->orderBy('review_date', 'desc')->paginate(10);
    }
    
    public function getConnectionsProperty()
    {
        return PlatformConnection::where('restaurant_id', $this->restaurant->id)->get();
    }
    
    public function getTemplatesProperty()
    {
        return ReviewResponseTemplate::where('restaurant_id', $this->restaurant->id)
            ->orWhereNull('restaurant_id')
            ->orderBy('restaurant_id', 'desc')
            ->get();
    }
    
    public function openResponseModal($reviewId)
    {
        $this->selectedReview = ExternalReview::find($reviewId);
        $this->responseText = $this->selectedReview->owner_response ?? '';
        $this->showResponseModal = true;
    }
    
    public function closeResponseModal()
    {
        $this->showResponseModal = false;
        $this->selectedReview = null;
        $this->responseText = '';
        $this->selectedTemplate = '';
    }
    
    public function applyTemplate()
    {
        if ($this->selectedTemplate) {
            $template = ReviewResponseTemplate::find($this->selectedTemplate);
            if ($template) {
                $this->responseText = $template->fillPlaceholders([
                    'customer_name' => $this->selectedReview->reviewer_name ?? 'Cliente',
                    'restaurant_name' => $this->restaurant->name,
                ]);
            }
        }
    }
    
    public function submitResponse()
    {
        $this->validate([
            'responseText' => 'required|min:10|max:2000',
        ]);
        
        $review = $this->selectedReview;
        
        // Try to post response to platform if supported
        $postedToApi = false;
        
        if ($review->platform === 'google' && $review->canRespondViaApi()) {
            $connection = PlatformConnection::where('restaurant_id', $this->restaurant->id)
                ->where('platform', 'google')
                ->where('is_active', true)
                ->first();
                
            if ($connection) {
                try {
                    app(GoogleBusinessService::class)->replyToReview(
                        $connection,
                        $review->external_review_id,
                        $this->responseText
                    );
                    $postedToApi = true;
                } catch (\Exception $e) {
                    \Log::error('Failed to post Google review response', ['error' => $e->getMessage()]);
                }
            }
        }
        
        if ($review->platform === 'facebook' && $review->canRespondViaApi()) {
            $connection = PlatformConnection::where('restaurant_id', $this->restaurant->id)
                ->where('platform', 'facebook')
                ->where('is_active', true)
                ->first();
                
            if ($connection) {
                try {
                    app(FacebookBusinessService::class)->replyToReview(
                        $connection,
                        $review->external_review_id,
                        $this->responseText
                    );
                    $postedToApi = true;
                } catch (\Exception $e) {
                    \Log::error('Failed to post Facebook review response', ['error' => $e->getMessage()]);
                }
            }
        }
        
        // Save response locally
        $review->update([
            'owner_response' => $this->responseText,
            'response_date' => now(),
            'response_synced' => $postedToApi,
        ]);
        
        $this->closeResponseModal();
        $this->loadStats();
        
        $this->dispatch('notify', [
            'type' => 'success',
            'message' => $postedToApi 
                ? 'Respuesta publicada exitosamente' 
                : 'Respuesta guardada (no se pudo sincronizar con la plataforma)'
        ]);
    }
    
    public function connectPlatform($platform)
    {
        if ($platform === 'google') {
            return redirect()->route('oauth.google.redirect', $this->restaurant->id);
        }
        
        if ($platform === 'facebook') {
            return redirect()->route('oauth.facebook.redirect', $this->restaurant->id);
        }
    }
    
    public function disconnectPlatform($connectionId)
    {
        $connection = PlatformConnection::find($connectionId);
        
        if ($connection && $connection->restaurant_id === $this->restaurant->id) {
            $connection->update(['is_active' => false]);
            $this->dispatch('notify', ['type' => 'success', 'message' => 'Plataforma desconectada']);
        }
    }
    
    public function syncPlatform($platform)
    {
        $connection = PlatformConnection::where('restaurant_id', $this->restaurant->id)
            ->where('platform', $platform)
            ->where('is_active', true)
            ->first();
            
        if (!$connection) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Plataforma no conectada']);
            return;
        }
        
        try {
            if ($platform === 'google') {
                $synced = app(GoogleBusinessService::class)->syncReviews($this->restaurant, $connection);
            } elseif ($platform === 'facebook') {
                $synced = app(FacebookBusinessService::class)->syncReviews($this->restaurant, $connection);
            }
            
            $this->dispatch('notify', ['type' => 'success', 'message' => 'Sincronizadas ' . $synced . ' reseñas']);
        } catch (\Exception $e) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Error al sincronizar: ' . $e->getMessage()]);
        }
    }
    
    public function render()
    {
        return view('livewire.owner.review-hub', [
            'reviews' => $this->reviews,
            'connections' => $this->connections,
            'templates' => $this->templates,
        ])->layout('layouts.app');
    }
}
