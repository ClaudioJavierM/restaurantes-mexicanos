<?php

namespace App\Livewire;

use App\Models\Restaurant;
use App\Models\RestaurantQuestion;
use Livewire\Component;

class RestaurantQA extends Component
{
    public Restaurant $restaurant;
    public $questions;

    // Form fields
    public string $question = '';
    public string $author_name = '';
    public string $author_email = '';

    public bool $submitted = false;
    public ?string $error = null;

    protected function rules(): array
    {
        return [
            'question' => 'required|string|min:10|max:500',
            'author_name' => auth()->check() ? 'nullable' : 'required|string|max:100',
            'author_email' => auth()->check() ? 'nullable' : 'required|email|max:150',
        ];
    }

    protected $messages = [
        'question.required' => 'Por favor escribe tu pregunta.',
        'question.min' => 'La pregunta debe tener al menos 10 caracteres.',
        'author_name.required' => 'Por favor ingresa tu nombre.',
        'author_email.required' => 'Por favor ingresa tu email.',
        'author_email.email' => 'El email no es válido.',
    ];

    public function mount(Restaurant $restaurant)
    {
        $this->restaurant = $restaurant;
        $this->loadQuestions();
    }

    protected function loadQuestions(): void
    {
        $this->questions = RestaurantQuestion::where('restaurant_id', $this->restaurant->id)
            ->public()
            ->answered()
            ->latest()
            ->take(10)
            ->get();
    }

    public function submitQuestion(): void
    {
        $this->error = null;
        $this->validate();

        // Rate limiting: 2 questions per IP per hour
        $recentCount = RestaurantQuestion::where('restaurant_id', $this->restaurant->id)
            ->where('created_at', '>=', now()->subHour())
            ->when(!auth()->check(), fn ($q) => $q->where('author_email', $this->author_email))
            ->count();

        if ($recentCount >= 3) {
            $this->error = 'Has enviado demasiadas preguntas. Intenta más tarde.';
            return;
        }

        RestaurantQuestion::create([
            'restaurant_id' => $this->restaurant->id,
            'user_id' => auth()->id(),
            'author_name' => auth()->check() ? null : $this->author_name,
            'author_email' => auth()->check() ? null : $this->author_email,
            'question' => $this->question,
            'is_public' => true,
            'is_approved' => false, // Owner must approve first
        ]);

        $this->submitted = true;
        $this->reset(['question', 'author_name', 'author_email']);
    }

    public function render()
    {
        return view('livewire.restaurant-qa');
    }
}
