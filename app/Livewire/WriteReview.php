<?php

namespace App\Livewire;

use App\Models\Restaurant;
use App\Models\Review;
use App\Models\ReviewPhoto;
use App\Services\ReviewTrustService;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class WriteReview extends Component
{
    use WithFileUploads;

    public Restaurant $restaurant;
    public $rating = 0;
    public $serviceRating = 0;
    public $foodRating = 0;
    public $ambianceRating = 0;
    public $title = '';
    public $comment = '';
    public $visitDate;
    public $visitType = 'dine_in';
    public $guestName = '';
    public $guestEmail = '';
    public $photos = [];
    public $showForm = false;

    protected function rules(): array
    {
        $rules = [
            'rating'         => 'required|integer|min:1|max:5',
            'serviceRating'  => 'nullable|integer|min:0|max:5',
            'foodRating'     => 'nullable|integer|min:0|max:5',
            'ambianceRating' => 'nullable|integer|min:0|max:5',
            'title'          => 'nullable|string|max:200',
            'comment'        => 'required|string|min:20|max:5000',
            'photos.*'       => 'nullable|image|max:5120',
        ];

        if (!auth()->check()) {
            $rules['guestName']  = 'required|string|max:100';
            $rules['guestEmail'] = 'required|email|max:255';
        }

        return $rules;
    }

    public function mount(Restaurant $restaurant): void
    {
        $this->restaurant = $restaurant;
    }

    public function setRating($rating): void       { $this->rating = $rating; }
    public function setServiceRating($rating): void { $this->serviceRating = $rating; }
    public function setFoodRating($rating): void    { $this->foodRating = $rating; }
    public function setAmbianceRating($rating): void { $this->ambianceRating = $rating; }
    public function toggleForm(): void              { $this->showForm = !$this->showForm; }

    public function submitReview()
    {
        $this->validate();

        $user   = auth()->user();
        $locale = app()->getLocale();

        // ── Spam / trust analysis ─────────────────────────────────────────────
        $trustService = app(ReviewTrustService::class);

        $trustData = $trustService->analyze([
            'restaurant_id'   => $this->restaurant->id,
            'rating'          => $this->rating,
            'service_rating'  => $this->serviceRating ?: null,
            'food_rating'     => $this->foodRating ?: null,
            'ambiance_rating' => $this->ambianceRating ?: null,
            'comment'         => $this->comment,
            'guest_email'     => $this->guestEmail,
        ], $user);

        // Block obvious duplicates immediately
        if (in_array('duplicate_restaurant_review', $trustData['trust_flags'] ?? [])) {
            $this->addError('comment', $locale === 'en'
                ? 'You have already submitted a review for this restaurant recently.'
                : 'Ya enviaste una reseña para este restaurante recientemente.');
            return;
        }

        // Determine status: new/guest accounts go to pending; trusted users auto-approve
        $isNewAccount = $user && $user->created_at->diffInDays(now()) < 7;
        $autoApprove  = $trustData['auto_approve'] && !$isNewAccount;

        // ── Create the review ─────────────────────────────────────────────────
        $review = Review::create([
            'restaurant_id'   => $this->restaurant->id,
            'user_id'         => $user?->id,
            'name'            => $user ? ($user->name ?? null) : $this->guestName,
            'email'           => $user ? ($user->email ?? null) : $this->guestEmail,
            'guest_name'      => $user ? null : $this->guestName,
            'guest_email'     => $user ? null : $this->guestEmail,
            'rating'          => $this->rating,
            'service_rating'  => $this->serviceRating > 0 ? $this->serviceRating : null,
            'food_rating'     => $this->foodRating > 0 ? $this->foodRating : null,
            'ambiance_rating' => $this->ambianceRating > 0 ? $this->ambianceRating : null,
            'title'           => $this->title ?: null,
            'comment'         => $this->comment,
            'visit_type'      => $this->visitType,
            'visit_date'      => $this->visitDate ?: null,
            'trust_score'     => $trustData['trust_score'],
            'trust_flags'     => $trustData['trust_flags'],
            'is_verified'     => $trustData['is_verified'],
            'flagged_suspicious' => $trustData['flagged_suspicious'],
            'status'          => $autoApprove ? 'approved' : 'pending',
            'approved_at'     => $autoApprove ? now() : null,
            'ip_address'      => request()->ip(),
            'user_agent'      => request()->userAgent(),
        ]);

        // ── Photos ────────────────────────────────────────────────────────────
        foreach ($this->photos as $index => $photo) {
            $path = $photo->store('reviews', 'public');
            ReviewPhoto::create([
                'review_id'     => $review->id,
                'photo_path'    => $path,
                'display_order' => $index,
            ]);
        }

        if ($autoApprove) {
            $this->restaurant->updateRating();
        }

        // Invalidate cached suspicious alerts so banner refreshes
        cache()->forget("review_alerts_{$this->restaurant->id}");

        $this->reset(['rating', 'serviceRating', 'foodRating', 'ambianceRating',
                      'title', 'comment', 'visitDate', 'visitType',
                      'guestName', 'guestEmail', 'photos', 'showForm']);

        if ($autoApprove) {
            session()->flash('review-success', $locale === 'en'
                ? 'Thank you! Your review has been published.'
                : '¡Gracias! Tu reseña ha sido publicada.');
        } else {
            session()->flash('review-success', $locale === 'en'
                ? 'Thank you! Your review is pending approval and will be published shortly.'
                : '¡Gracias! Tu reseña está pendiente de aprobación y se publicará en breve.');
        }

        return redirect()->to(route('restaurants.show', $this->restaurant->slug));
    }

    public function render()
    {
        return view('livewire.write-review');
    }
}
