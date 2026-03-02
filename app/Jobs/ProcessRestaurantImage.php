<?php

namespace App\Jobs;

use App\Models\Restaurant;
use Illuminate\Bus\Queueable as BusQueueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class ProcessRestaurantImage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, BusQueueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 120;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Restaurant $restaurant,
        public string $imagePath,
        public string $imageType = 'main' // main, gallery, logo
    ) {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            // Check if file exists
            if (!Storage::disk('public')->exists($this->imagePath)) {
                Log::warning("Image not found: {$this->imagePath}");
                return;
            }

            // Get the full path
            $fullPath = Storage::disk('public')->path($this->imagePath);

            // Create image manager with GD driver
            $manager = new ImageManager(new Driver());

            // Read the image
            $image = $manager->read($fullPath);

            // Define sizes based on image type
            $sizes = $this->getSizesForType();

            foreach ($sizes as $sizeName => $dimensions) {
                // Create resized version
                $resized = clone $image;

                // Resize maintaining aspect ratio
                if ($dimensions['width'] && $dimensions['height']) {
                    $resized->cover($dimensions['width'], $dimensions['height']);
                } else {
                    $resized->scale(
                        width: $dimensions['width'],
                        height: $dimensions['height']
                    );
                }

                // Generate new filename
                $pathInfo = pathinfo($this->imagePath);
                $newFilename = $pathInfo['dirname'] . '/' .
                              $pathInfo['filename'] . '_' . $sizeName . '.' .
                              $pathInfo['extension'];

                // Save the resized image
                $newPath = Storage::disk('public')->path($newFilename);
                $resized->save($newPath, quality: 85);

                Log::info("Created {$sizeName} version: {$newFilename}");
            }

            // Optimize original image
            $image->save($fullPath, quality: 90);

            Log::info("Processed image for restaurant {$this->restaurant->id}: {$this->imagePath}");

        } catch (\Exception $e) {
            Log::error("Error processing image {$this->imagePath}: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get sizes configuration based on image type
     */
    protected function getSizesForType(): array
    {
        return match ($this->imageType) {
            'main' => [
                'thumb' => ['width' => 300, 'height' => 200],
                'medium' => ['width' => 800, 'height' => 600],
                'large' => ['width' => 1200, 'height' => 900],
            ],
            'gallery' => [
                'thumb' => ['width' => 200, 'height' => 200],
                'medium' => ['width' => 600, 'height' => 600],
                'large' => ['width' => 1000, 'height' => 1000],
            ],
            'logo' => [
                'small' => ['width' => 100, 'height' => 100],
                'medium' => ['width' => 200, 'height' => 200],
                'large' => ['width' => 400, 'height' => 400],
            ],
            default => [
                'thumb' => ['width' => 300, 'height' => null],
                'medium' => ['width' => 800, 'height' => null],
            ],
        };
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("Failed to process image for restaurant {$this->restaurant->id}: {$exception->getMessage()}");
    }
}
