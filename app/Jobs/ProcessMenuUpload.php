<?php

namespace App\Jobs;

use App\Models\MenuUpload;
use App\Models\MenuCategory;
use App\Models\MenuItem;
use App\Services\MenuExtractionService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessMenuUpload implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 300; // 5 minutes

    public function __construct(
        public MenuUpload $upload
    ) {}

    public function handle(MenuExtractionService $extractionService): void
    {
        try {
            // Update status to processing
            $this->upload->update(['status' => 'processing']);

            // Step 1: Extract text using OCR
            $ocrText = $extractionService->extractText($this->upload);
            
            if (empty($ocrText)) {
                throw new \Exception('No se pudo extraer texto del menú');
            }

            $this->upload->update(['ocr_raw_text' => $ocrText]);

            // Step 2: Use AI to structure the menu data
            $menuData = $extractionService->structureMenuWithAI($ocrText, $this->upload->restaurant);
            
            $this->upload->update(['ai_extracted_data' => $menuData]);

            // Step 3: Create menu items from extracted data
            $itemsCreated = $extractionService->createMenuItems($this->upload->restaurant, $menuData);

            // Update status
            $this->upload->update([
                'status' => 'needs_review',
                'items_extracted' => $itemsCreated,
                'processed_at' => now(),
            ]);

            Log::info('Menu processed successfully', [
                'upload_id' => $this->upload->id,
                'items_created' => $itemsCreated,
            ]);

        } catch (\Exception $e) {
            Log::error('Menu processing failed', [
                'upload_id' => $this->upload->id,
                'error' => $e->getMessage(),
            ]);

            $this->upload->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    public function failed(\Throwable $exception): void
    {
        $this->upload->update([
            'status' => 'failed',
            'error_message' => $exception->getMessage(),
        ]);
    }
}
