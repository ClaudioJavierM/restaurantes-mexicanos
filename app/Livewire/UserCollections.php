<?php

namespace App\Livewire;

use App\Models\UserCollection;
use App\Models\UserCollectionItem;
use Livewire\Component;
use Illuminate\Support\Collection;

class UserCollections extends Component
{
    // Create form state
    public string $newName        = '';
    public string $newDescription = '';
    public bool   $newIsPublic    = true;
    public bool   $showCreateForm = false;

    // Detail view state
    public ?int $selectedCollectionId = null;

    // ─── Computed ────────────────────────────────────────────────────────────────

    public function getCollectionsProperty(): Collection
    {
        return UserCollection::forUser(auth()->id())
            ->with([
                'coverRestaurant',
                'items.restaurant',
            ])
            ->withCount('items')
            ->latest()
            ->get();
    }

    public function getSelectedCollectionProperty(): ?UserCollection
    {
        if (! $this->selectedCollectionId) {
            return null;
        }

        return UserCollection::forUser(auth()->id())
            ->with(['items' => fn ($q) => $q->with('restaurant')->orderBy('sort_order')])
            ->find($this->selectedCollectionId);
    }

    // ─── Actions ─────────────────────────────────────────────────────────────────

    public function createCollection(): void
    {
        $this->validate([
            'newName'        => 'required|string|max:100',
            'newDescription' => 'nullable|string|max:300',
            'newIsPublic'    => 'boolean',
        ]);

        UserCollection::create([
            'user_id'     => auth()->id(),
            'name'        => trim($this->newName),
            'description' => trim($this->newDescription) ?: null,
            'is_public'   => $this->newIsPublic,
        ]);

        $this->reset(['newName', 'newDescription', 'newIsPublic', 'showCreateForm']);
        $this->newIsPublic = true;

        session()->flash('success', 'Lista creada exitosamente.');
    }

    public function deleteCollection(int $id): void
    {
        $collection = UserCollection::forUser(auth()->id())->findOrFail($id);
        $collection->delete();

        if ($this->selectedCollectionId === $id) {
            $this->selectedCollectionId = null;
        }

        session()->flash('success', 'Lista eliminada.');
    }

    public function removeItem(int $itemId): void
    {
        $item = UserCollectionItem::whereHas('collection', function ($q) {
            $q->where('user_id', auth()->id());
        })->findOrFail($itemId);

        $item->delete();
    }

    public function selectCollection(?int $id): void
    {
        $this->selectedCollectionId = ($this->selectedCollectionId === $id) ? null : $id;
    }

    public function toggleCreateForm(): void
    {
        $this->showCreateForm = ! $this->showCreateForm;
        if (! $this->showCreateForm) {
            $this->reset(['newName', 'newDescription']);
            $this->newIsPublic = true;
        }
    }

    // ─── Lifecycle ───────────────────────────────────────────────────────────────

    public function mount(): void
    {
        if (! auth()->check()) {
            redirect()->route('login');
        }
    }

    public function render()
    {
        return view('livewire.user-collections', [
            'collections'        => $this->collections,
            'selectedCollection' => $this->selectedCollection,
        ])->layout('layouts.app', ['title' => 'Mis Listas']);
    }
}
