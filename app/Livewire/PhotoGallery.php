<?php

namespace App\Livewire;

use App\Models\PhotoReport;
use App\Models\Restaurant;
use App\Models\UserPhoto;
use App\Services\ImageModerationService;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class PhotoGallery extends Component
{
    use WithFileUploads, WithPagination;

    public Restaurant $restaurant;
    public $showUploadForm = false;
    public $showLightbox = false;
    public $selectedPhoto = null;
    public $selectedPhotoIndex = 0;

    // Upload form fields
    public $photos = [];
    public $caption = '';
    public $photoType = 'food';

    // Filters
    public $filterType = 'all';

    // Report modal
    public $showReportModal = false;
    public $reportingPhotoId = null;
    public $reportReason = '';
    public $reportDescription = '';

    protected $rules = [
        'photos.*' => 'required|image|max:10240', // 10MB max
        'caption' => 'nullable|string|max:500',
        'photoType' => 'required|in:food,interior,exterior,menu,drink,other',
        'reportReason' => 'required|in:inappropriate,not_restaurant,duplicate,spam,other',
        'reportDescription' => 'nullable|string|max:500',
    ];

    protected $messages = [
        'photos.*.required' => 'Por favor selecciona al menos una foto.',
        'photos.*.image' => 'El archivo debe ser una imagen.',
        'photos.*.max' => 'La imagen no debe superar los 10MB.',
    ];

    public function mount(Restaurant $restaurant)
    {
        $this->restaurant = $restaurant;
    }

    public function toggleUploadForm()
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }
        $this->showUploadForm = !$this->showUploadForm;
    }

    public function filterByType($type)
    {
        $this->filterType = $type;
        $this->resetPage();
    }

    public function openLightbox($photoId)
    {
        $this->selectedPhoto = UserPhoto::find($photoId);
        if ($this->selectedPhoto) {
            $this->selectedPhoto->incrementViews();
            $this->showLightbox = true;
        }
    }

    public function closeLightbox()
    {
        $this->showLightbox = false;
        $this->selectedPhoto = null;
    }

    public function nextPhoto()
    {
        $photos = $this->getGalleryPhotosProperty();
        $currentIndex = $photos->search(fn($p) => $p->id === $this->selectedPhoto->id);
        if ($currentIndex !== false && $currentIndex < $photos->count() - 1) {
            $this->selectedPhoto = $photos[$currentIndex + 1];
            $this->selectedPhoto->incrementViews();
        }
    }

    public function previousPhoto()
    {
        $photos = $this->getGalleryPhotosProperty();
        $currentIndex = $photos->search(fn($p) => $p->id === $this->selectedPhoto->id);
        if ($currentIndex !== false && $currentIndex > 0) {
            $this->selectedPhoto = $photos[$currentIndex - 1];
            $this->selectedPhoto->incrementViews();
        }
    }

    public function toggleLike($photoId)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $photo = UserPhoto::find($photoId);
        if ($photo) {
            $photo->toggleLike(auth()->user());
        }
    }

    public function openReportModal($photoId)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $this->reportingPhotoId = $photoId;
        $this->reportReason = '';
        $this->reportDescription = '';
        $this->showReportModal = true;
    }

    public function closeReportModal()
    {
        $this->showReportModal = false;
        $this->reportingPhotoId = null;
        $this->reportReason = '';
        $this->reportDescription = '';
    }

    public function submitReport()
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $this->validate([
            'reportReason' => 'required|in:inappropriate,not_restaurant,duplicate,spam,other',
            'reportDescription' => 'nullable|string|max:500',
        ]);

        $photo = UserPhoto::find($this->reportingPhotoId);
        if (!$photo) {
            $this->closeReportModal();
            return;
        }

        // Check if user already reported this photo
        $existingReport = PhotoReport::where('user_photo_id', $this->reportingPhotoId)
            ->where('user_id', auth()->id())
            ->first();

        if ($existingReport) {
            session()->flash('photo-error', 'Ya has reportado esta foto anteriormente.');
            $this->closeReportModal();
            return;
        }

        // Create the report
        PhotoReport::create([
            'user_photo_id' => $this->reportingPhotoId,
            'user_id' => auth()->id(),
            'reason' => $this->reportReason,
            'description' => $this->reportDescription,
            'status' => PhotoReport::STATUS_PENDING,
            'ip_address' => request()->ip(),
        ]);

        // Increment reports count on photo
        $photo->increment('reports_count');

        // Auto-hide photo if it gets too many reports (community moderation)
        if ($photo->reports_count >= 3) {
            $photo->update(['status' => UserPhoto::STATUS_FLAGGED]);
        }

        $this->closeReportModal();
        session()->flash('photo-success', 'Gracias por reportar. Revisaremos la foto pronto.');
    }

    public function getReportReasonsProperty()
    {
        return [
            'inappropriate' => 'Contenido inapropiado',
            'not_restaurant' => 'No es de este restaurante',
            'duplicate' => 'Foto duplicada',
            'spam' => 'Spam o publicidad',
            'other' => 'Otro motivo',
        ];
    }

    public function uploadPhotos()
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $this->validate([
            'photos' => 'required|array|min:1',
            'photos.*' => 'required|image|max:10240',
            'caption' => 'nullable|string|max:500',
            'photoType' => 'required|in:food,interior,exterior,menu,drink,other',
        ]);

        $moderationService = app(ImageModerationService::class);
        $rejectedPhotos = [];
        $approvedCount = 0;

        foreach ($this->photos as $photo) {
            // Store original photo first
            $path = $photo->store('user-photos/' . $this->restaurant->id, 'public');

            // AI Moderation: Check if image is safe
            $moderationResult = $moderationService->checkImageSafety($path);

            if (!$moderationResult['safe']) {
                // Delete the unsafe photo
                Storage::disk('public')->delete($path);
                $rejectedPhotos[] = $moderationService->getReasonLabel($moderationResult['reasons'][0] ?? 'inappropriate');
                continue;
            }

            // Create thumbnail
            $thumbnailPath = $this->createThumbnail($photo, $this->restaurant->id);

            // Get image dimensions
            $imageInfo = getimagesize($photo->getRealPath());

            // Create UserPhoto record - auto-approved for safe photos
            UserPhoto::create([
                'restaurant_id' => $this->restaurant->id,
                'user_id' => auth()->id(),
                'caption' => $this->caption,
                'photo_path' => $path,
                'thumbnail_path' => $thumbnailPath,
                'status' => UserPhoto::STATUS_APPROVED,
                'approved_at' => now(),
                'approved_by' => auth()->id(),
                'width' => $imageInfo[0] ?? null,
                'height' => $imageInfo[1] ?? null,
                'file_size' => $photo->getSize(),
                'mime_type' => $photo->getMimeType(),
                'photo_type' => $this->photoType,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            $approvedCount++;
        }

        // Reset form
        $this->reset(['photos', 'caption', 'photoType', 'showUploadForm']);

        // Show appropriate message
        if ($approvedCount > 0 && empty($rejectedPhotos)) {
            session()->flash('photo-success', 'Fotos subidas exitosamente. Gracias por contribuir!');
        } elseif ($approvedCount > 0 && !empty($rejectedPhotos)) {
            session()->flash('photo-success', "{$approvedCount} foto(s) subida(s). Algunas fotos fueron rechazadas: " . implode(', ', $rejectedPhotos));
        } else {
            session()->flash('photo-error', 'Las fotos fueron rechazadas: ' . implode(', ', $rejectedPhotos));
        }
    }

    protected function createThumbnail($photo, $restaurantId)
    {
        try {
            $sourcePath = $photo->getRealPath();
            $imageInfo = getimagesize($sourcePath);

            if (!$imageInfo) {
                return null;
            }

            $width = $imageInfo[0];
            $height = $imageInfo[1];
            $mime = $imageInfo['mime'];

            // Calculate new dimensions (max 300x300, maintaining aspect ratio)
            $maxSize = 300;
            if ($width > $height) {
                $newWidth = $maxSize;
                $newHeight = (int) ($height * ($maxSize / $width));
            } else {
                $newHeight = $maxSize;
                $newWidth = (int) ($width * ($maxSize / $height));
            }

            // Create source image based on type
            switch ($mime) {
                case 'image/jpeg':
                    $source = imagecreatefromjpeg($sourcePath);
                    break;
                case 'image/png':
                    $source = imagecreatefrompng($sourcePath);
                    break;
                case 'image/gif':
                    $source = imagecreatefromgif($sourcePath);
                    break;
                case 'image/webp':
                    $source = imagecreatefromwebp($sourcePath);
                    break;
                default:
                    return null;
            }

            if (!$source) {
                return null;
            }

            // Create thumbnail
            $thumb = imagecreatetruecolor($newWidth, $newHeight);

            // Preserve transparency for PNG
            if ($mime === 'image/png') {
                imagealphablending($thumb, false);
                imagesavealpha($thumb, true);
            }

            imagecopyresampled($thumb, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

            // Generate thumbnail path
            $filename = 'thumb_' . time() . '_' . uniqid() . '.jpg';
            $thumbnailPath = 'user-photos/' . $restaurantId . '/thumbnails/' . $filename;

            // Ensure directory exists
            Storage::disk('public')->makeDirectory('user-photos/' . $restaurantId . '/thumbnails');

            // Save thumbnail
            $fullPath = Storage::disk('public')->path($thumbnailPath);
            imagejpeg($thumb, $fullPath, 80);

            // Free memory
            imagedestroy($source);
            imagedestroy($thumb);

            return $thumbnailPath;
        } catch (\Exception $e) {
            // If thumbnail creation fails, return null
            return null;
        }
    }

    /**
     * Check if the restaurant is on the free plan (limit gallery to 5 photos).
     */
    protected function isFreePlan(): bool
    {
        $tier = $this->restaurant->subscription_tier;
        return empty($tier) || $tier === 'free';
    }

    public function getGalleryPhotosProperty()
    {
        $limit = $this->isFreePlan() ? 5 : null;

        // Get user-uploaded photos
        $query = $this->restaurant->userPhotos()
            ->approved()
            ->with('user')
            ->orderBy('created_at', 'desc');

        if ($this->filterType !== 'all' && $this->filterType !== 'yelp') {
            $query->byType($this->filterType);
        }

        // If filtering by yelp only, return empty collection for user photos
        if ($this->filterType === 'yelp') {
            return collect();
        }

        if ($limit) {
            $query->limit($limit);
        }

        return $query->get();
    }

    public function getYelpPhotosProperty()
    {
        // Return Yelp photos as array
        if ($this->filterType !== 'all' && $this->filterType !== 'yelp') {
            return [];
        }

        $photos = $this->restaurant->yelp_photos ?? [];

        if ($this->isFreePlan() && count($photos) > 5) {
            $photos = array_slice($photos, -5);
        }

        return $photos;
    }

    public function getPhotosCountProperty()
    {
        $limit = $this->isFreePlan() ? 5 : null;

        $userPhotosCount = $this->restaurant->userPhotos()->approved()->count();
        $yelpPhotosCount = is_array($this->restaurant->yelp_photos) ? count($this->restaurant->yelp_photos) : 0;
        $total = $userPhotosCount + $yelpPhotosCount;

        if ($limit) {
            return min($total, $limit);
        }

        return $total;
    }

    public function getPhotoTypesProperty()
    {
        $types = [
            'food' => 'Comida',
            'interior' => 'Interior',
            'exterior' => 'Exterior',
            'menu' => 'Menú',
            'drink' => 'Bebidas',
            'other' => 'Otro',
        ];

        // Add Yelp filter if restaurant has Yelp photos
        if (is_array($this->restaurant->yelp_photos) && count($this->restaurant->yelp_photos) > 0) {
            $types['yelp'] = 'Yelp (' . count($this->restaurant->yelp_photos) . ')';
        }

        return $types;
    }

    public function render()
    {
        return view('livewire.photo-gallery', [
            'galleryPhotos' => $this->galleryPhotos,
            'yelpPhotos' => $this->yelpPhotos,
            'photosCount' => $this->photosCount,
            'photoTypes' => $this->photoTypes,
        ]);
    }
}
