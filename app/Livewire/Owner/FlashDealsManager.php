<?php

namespace App\Livewire\Owner;

use App\Models\FlashDeal;
use App\Models\Restaurant;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class FlashDealsManager extends Component
{
    protected static bool $isLazy = true;

    public ?Restaurant $restaurant = null;

    // Form fields
    public string $title        = '';
    public string $discount_type  = 'percentage';
    public string $discount_value = '';
    public string $promo_code    = '';
    public string $start_date    = '';
    public string $end_date      = '';
    public string $description   = '';
    public string $applicable_for = 'all';
    public ?int   $max_redemptions = null;

    // UI state
    public bool   $showForm      = false;
    public ?int   $editingDealId = null;
    public ?string $successMessage = null;
    public ?string $errorMessage   = null;

    protected array $rules = [
        'title'           => 'required|string|max:255',
        'discount_type'   => 'required|in:percentage,fixed,bogo,free_item',
        'discount_value'  => 'required|numeric|min:0',
        'promo_code'      => 'nullable|string|max:50',
        'start_date'      => 'required|date',
        'end_date'        => 'required|date|after:start_date',
        'description'     => 'nullable|string|max:1000',
        'applicable_for'  => 'required|in:all,dine_in,takeout,delivery',
        'max_redemptions' => 'nullable|integer|min:1',
    ];

    public function mount(): void
    {
        $user = Auth::user();
        if ($user) {
            $this->restaurant = $user->allAccessibleRestaurants()->first();
        }
        $this->start_date = now()->format('Y-m-d\TH:i');
        $this->end_date   = now()->addDay()->format('Y-m-d\TH:i');
    }

    public function getDealsProperty()
    {
        if (!$this->restaurant) return collect();
        return FlashDeal::where('restaurant_id', $this->restaurant->id)
            ->orderByDesc('created_at')
            ->get();
    }

    public function openForm(): void
    {
        $this->resetForm();
        $this->showForm    = true;
        $this->editingDealId = null;
    }

    public function editDeal(int $dealId): void
    {
        $deal = FlashDeal::findOrFail($dealId);
        $this->authorize('update', $deal);

        $this->editingDealId   = $deal->id;
        $this->title           = $deal->title;
        $this->discount_type   = $deal->discount_type;
        $this->discount_value  = (string) $deal->discount_value;
        $this->promo_code      = $deal->code ?? '';
        $this->start_date      = $deal->starts_at->format('Y-m-d\TH:i');
        $this->end_date        = $deal->ends_at->format('Y-m-d\TH:i');
        $this->description     = $deal->description ?? '';
        $this->applicable_for  = $deal->applicable_for;
        $this->max_redemptions = $deal->max_redemptions;
        $this->showForm = true;
    }

    public function save(): void
    {
        $this->validate();

        if (!$this->restaurant) {
            $this->errorMessage = 'No se encontró el restaurante asociado.';
            return;
        }

        $data = [
            'restaurant_id'    => $this->restaurant->id,
            'title'            => $this->title,
            'discount_type'    => $this->discount_type,
            'discount_value'   => (float) $this->discount_value,
            'code'             => $this->promo_code ?: strtoupper(Str::random(8)),
            'starts_at'        => $this->start_date,
            'ends_at'          => $this->end_date,
            'description'      => $this->description ?: null,
            'applicable_for'   => $this->applicable_for,
            'max_redemptions'  => $this->max_redemptions ?: null,
            'is_active'        => true,
        ];

        if ($this->editingDealId) {
            $deal = FlashDeal::findOrFail($this->editingDealId);
            $deal->update($data);
            $this->successMessage = 'Oferta actualizada correctamente.';
        } else {
            FlashDeal::create($data);
            $this->successMessage = 'Oferta creada correctamente.';
        }

        $this->resetForm();
        $this->showForm = false;
        $this->errorMessage = null;
    }

    public function toggleActive(int $dealId): void
    {
        $deal = FlashDeal::findOrFail($dealId);
        $deal->update(['is_active' => !$deal->is_active]);
    }

    public function deleteDeal(int $dealId): void
    {
        $deal = FlashDeal::findOrFail($dealId);
        $deal->delete();
        $this->successMessage = 'Oferta eliminada.';
    }

    public function cancelForm(): void
    {
        $this->resetForm();
        $this->showForm    = false;
        $this->errorMessage = null;
    }

    private function resetForm(): void
    {
        $this->title           = '';
        $this->discount_type   = 'percentage';
        $this->discount_value  = '';
        $this->promo_code      = '';
        $this->start_date      = now()->format('Y-m-d\TH:i');
        $this->end_date        = now()->addDay()->format('Y-m-d\TH:i');
        $this->description     = '';
        $this->applicable_for  = 'all';
        $this->max_redemptions = null;
        $this->editingDealId   = null;
        $this->successMessage  = null;
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.owner.flash-deals-manager', [
            'deals' => $this->deals,
        ]);
    }
}
