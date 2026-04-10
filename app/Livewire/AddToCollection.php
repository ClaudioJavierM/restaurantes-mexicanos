<?php

namespace App\Livewire;

use App\Models\UserCollection;
use App\Models\UserCollectionItem;
use Livewire\Component;
use Illuminate\Support\Collection;

class AddToCollection extends Component
{
    public int  $restaurantId;
    public bool $showDropdown    = false;
    public bool $showNewInput    = false;
    public string $newListName   = '';

    // ─── Computed ────────────────────────────────────────────────────────────────

    public function getCollectionsProperty(): Collection
    {
        if (! auth()->check()) {
            return collect();
        }

        return UserCollection::forUser(auth()->id())
            ->withCount(['items as has_restaurant' => fn ($q) =>
                $q->where('restaurant_id', $this->restaurantId)
            ])
            ->orderBy('name')
            ->get();
    }

    public function getIsInAnyCollectionProperty(): bool
    {
        if (! auth()->check()) {
            return false;
        }

        return UserCollectionItem::where('restaurant_id', $this->restaurantId)
            ->whereHas('collection', fn ($q) => $q->where('user_id', auth()->id()))
            ->exists();
    }

    // ─── Actions ─────────────────────────────────────────────────────────────────

    public function isInCollection(int $collectionId): bool
    {
        return UserCollectionItem::where('collection_id', $collectionId)
            ->where('restaurant_id', $this->restaurantId)
            ->exists();
    }

    public function toggleCollection(int $collectionId): void
    {
        if (! auth()->check()) {
            $this->redirect(route('login'));
            return;
        }

        // Verify the collection belongs to this user
        $collection = UserCollection::forUser(auth()->id())->find($collectionId);
        if (! $collection) {
            return;
        }

        $existing = UserCollectionItem::where('collection_id', $collectionId)
            ->where('restaurant_id', $this->restaurantId)
            ->first();

        if ($existing) {
            $existing->delete();
        } else {
            $maxSort = UserCollectionItem::where('collection_id', $collectionId)
                ->max('sort_order') ?? -1;

            UserCollectionItem::create([
                'collection_id' => $collectionId,
                'restaurant_id' => $this->restaurantId,
                'sort_order'    => $maxSort + 1,
            ]);
        }
    }

    public function createAndAdd(string $name): void
    {
        if (! auth()->check()) {
            $this->redirect(route('login'));
            return;
        }

        $name = trim($name);
        if (empty($name) || strlen($name) > 100) {
            return;
        }

        $collection = UserCollection::create([
            'user_id'   => auth()->id(),
            'name'      => $name,
            'is_public' => true,
        ]);

        UserCollectionItem::create([
            'collection_id' => $collection->id,
            'restaurant_id' => $this->restaurantId,
            'sort_order'    => 0,
        ]);

        $this->newListName   = '';
        $this->showNewInput  = false;
    }

    public function toggleDropdown(): void
    {
        $this->showDropdown  = ! $this->showDropdown;
        $this->showNewInput  = false;
        $this->newListName   = '';
    }

    public function closeDropdown(): void
    {
        $this->showDropdown = false;
        $this->showNewInput = false;
        $this->newListName  = '';
    }

    public function render()
    {
        return view('livewire.add-to-collection', [
            'collections'       => $this->collections,
            'isInAnyCollection' => $this->isInAnyCollection,
        ]);
    }
}
