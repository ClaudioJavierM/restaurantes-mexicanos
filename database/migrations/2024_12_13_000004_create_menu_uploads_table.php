<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('menu_uploads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('file_path');
            $table->string('file_type'); // pdf, image, url
            $table->string('original_name');
            $table->enum('status', ['pending', 'processing', 'completed', 'failed', 'needs_review'])->default('pending');
            $table->text('ocr_raw_text')->nullable();
            $table->json('ai_extracted_data')->nullable();
            $table->text('error_message')->nullable();
            $table->integer('items_extracted')->default(0);
            $table->integer('items_approved')->default(0);
            $table->timestamp('processed_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menu_uploads');
    }
};
