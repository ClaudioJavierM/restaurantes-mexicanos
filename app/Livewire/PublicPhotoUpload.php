<?php

namespace App\Livewire;

use App\Models\UserPhoto;
use Livewire\Component;
use Livewire\WithFileUploads;

class PublicPhotoUpload extends Component
{
    use WithFileUploads;

    protected static bool $isLazy = true;

    public int $restaurantId;

    public array $photos = [];

    public string $caption = '';

    public bool $success = false;

    public int $uploadedCount = 0;

    protected function rules(): array
    {
        return [
            'photos'   => 'required|array|min:1|max:5',
            'photos.*' => 'image|max:5120|mimes:jpg,jpeg,png,webp',
            'caption'  => 'nullable|string|max:200',
        ];
    }

    protected $messages = [
        'photos.required'   => 'Por favor selecciona al menos una foto.',
        'photos.max'        => 'Puedes subir máximo 5 fotos a la vez.',
        'photos.*.image'    => 'El archivo debe ser una imagen.',
        'photos.*.max'      => 'Cada imagen no debe superar los 5MB.',
        'photos.*.mimes'    => 'Solo se aceptan formatos JPG, JPEG, PNG y WEBP.',
        'caption.max'       => 'La descripción no puede superar los 200 caracteres.',
    ];

    public function mount(int $restaurantId): void
    {
        $this->restaurantId = $restaurantId;
    }

    public function upload(): void
    {
        if (! auth()->check()) {
            $this->dispatch('show-login-prompt');
            return;
        }

        $this->validate();

        $uploaded = 0;

        foreach ($this->photos as $photo) {
            $filename = time() . '_' . uniqid() . '.' . $photo->getClientOriginalExtension();
            $path = $photo->storeAs(
                'user-photos/' . $this->restaurantId,
                $filename,
                'public'
            );

            UserPhoto::create([
                'restaurant_id' => $this->restaurantId,
                'user_id'       => auth()->id(),
                'photo_path'    => $path,
                'caption'       => $this->caption ?: null,
                'status'        => UserPhoto::STATUS_PENDING,
                'photo_type'    => 'other',
                'file_size'     => $photo->getSize(),
                'mime_type'     => $photo->getMimeType(),
                'ip_address'    => request()->ip(),
                'user_agent'    => request()->userAgent(),
            ]);

            $uploaded++;
        }

        $this->uploadedCount = $uploaded;
        $this->success = true;

        $this->reset(['photos', 'caption']);

        $this->dispatch('photos-uploaded');
    }

    public function resetForm(): void
    {
        $this->success = false;
        $this->uploadedCount = 0;
        $this->reset(['photos', 'caption']);
    }

    public function getPhotoCountProperty(): int
    {
        return UserPhoto::where('restaurant_id', $this->restaurantId)
            ->where('status', UserPhoto::STATUS_APPROVED)
            ->count();
    }

    public function getRecentPhotosProperty()
    {
        return UserPhoto::where('restaurant_id', $this->restaurantId)
            ->where('status', UserPhoto::STATUS_APPROVED)
            ->with('user')
            ->orderByDesc('created_at')
            ->limit(6)
            ->get();
    }

    public function render()
    {
        return view('livewire.public-photo-upload', [
            'photoCount'   => $this->photoCount,
            'recentPhotos' => $this->recentPhotos,
        ]);
    }
}
