<?php

namespace App\Livewire;

use App\Models\DishReview;
use Illuminate\Validation\Rule;
use Livewire\Component;

class DishReviewForm extends Component
{
    public int $restaurantId;
    public ?int $menuItemId = null;
    public string $dishName = '';
    public int $rating = 0;
    public string $comment = '';
    public string $reviewerName = '';
    public string $reviewerEmail = '';
    public bool $submitted = false;

    public function mount(int $restaurantId, ?int $menuItemId = null, string $dishName = ''): void
    {
        $this->restaurantId = $restaurantId;
        $this->menuItemId   = $menuItemId;
        $this->dishName     = $dishName;
    }

    public function setRating(int $rating): void
    {
        $this->rating = $rating;
    }

    public function submit(): void
    {
        $this->validate($this->validationRules());

        $isVerified = false;
        if (auth()->check()) {
            // Mark as verified if user has any order at this restaurant
            $isVerified = \App\Models\DishReview::where('restaurant_id', $this->restaurantId)
                ->where('user_id', auth()->id())
                ->exists() === false
                ? false
                : false; // extend with real order check when orders table is linked
        }

        DishReview::create([
            'restaurant_id'        => $this->restaurantId,
            'menu_item_id'         => $this->menuItemId ?: null,
            'user_id'              => auth()->id(),
            'dish_name'            => $this->dishName,
            'rating'               => $this->rating,
            'comment'              => $this->comment,
            'reviewer_name'        => auth()->check() ? null : $this->reviewerName,
            'reviewer_email'       => auth()->check() ? null : $this->reviewerEmail,
            'is_approved'          => false,
            'is_verified_purchase' => $isVerified,
            'photos'               => null,
        ]);

        $this->submitted = true;
        $this->dispatch('dish-review-submitted');
    }

    protected function validationRules(): array
    {
        return [
            'rating'        => 'required|integer|min:1|max:5',
            'comment'       => 'required|string|min:10|max:1000',
            'dishName'      => 'required_without:menuItemId|string|max:100',
            'reviewerName'  => [Rule::requiredIf(! auth()->check()), 'nullable', 'string', 'max:100'],
            'reviewerEmail' => [Rule::requiredIf(! auth()->check()), 'nullable', 'email', 'max:191'],
        ];
    }

    protected function messages(): array
    {
        return [
            'rating.required'        => 'Por favor selecciona una calificación.',
            'rating.min'             => 'La calificación mínima es 1 estrella.',
            'rating.max'             => 'La calificación máxima es 5 estrellas.',
            'comment.required'       => 'El comentario es obligatorio.',
            'comment.min'            => 'El comentario debe tener al menos 10 caracteres.',
            'comment.max'            => 'El comentario no puede exceder 1,000 caracteres.',
            'dishName.required_without' => '¿Qué platillo probaste?',
            'reviewerName.required'  => 'Por favor ingresa tu nombre.',
            'reviewerEmail.required' => 'Por favor ingresa tu correo electrónico.',
            'reviewerEmail.email'    => 'Ingresa un correo electrónico válido.',
        ];
    }

    public function render()
    {
        return view('livewire.dish-review-form');
    }
}
