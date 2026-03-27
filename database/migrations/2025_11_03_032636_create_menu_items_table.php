<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('menu_items')) {
            Schema::create('menu_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('restaurant_id')->constrained()->cascadeOnDelete();
                $table->string('name');
                $table->string('name_en')->nullable();
                $table->text('description')->nullable();
                $table->text('description_en')->nullable();
                $table->decimal('price', 8, 2)->nullable();
                $table->string('category')->nullable();
                $table->integer('spice_level')->nullable();
                $table->json('dietary_options')->nullable();
                $table->json('ingredients')->nullable();
                $table->string('image')->nullable();
                $table->boolean('is_popular')->default(false);
                $table->boolean('is_available')->default(true);
                $table->integer('sort_order')->default(0);
                $table->timestamps();
                $table->index('restaurant_id');
                $table->index('category');
                $table->index('is_popular');
                $table->index('is_available');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('menu_items');
    }
};
