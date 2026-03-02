<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('menu_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->constrained()->cascadeOnDelete();
            $table->string('name'); // e.g., "Tacos al Pastor"
            $table->string('name_en')->nullable(); // English translation
            $table->text('description')->nullable();
            $table->text('description_en')->nullable();
            $table->decimal('price', 8, 2)->nullable(); // e.g., 12.99
            $table->string('category')->nullable(); // Appetizers, Main Dishes, Desserts, Drinks
            $table->integer('spice_level')->nullable(); // 0-5, same as restaurant
            $table->json('dietary_options')->nullable(); // vegetarian, vegan, gluten_free, etc.
            $table->json('ingredients')->nullable(); // List of ingredients
            $table->string('image')->nullable(); // Photo of the dish
            $table->boolean('is_popular')->default(false); // Mark as popular/signature dish
            $table->boolean('is_available')->default(true); // Currently available
            $table->integer('sort_order')->default(0); // For custom ordering
            $table->timestamps();

            // Indexes
            $table->index('restaurant_id');
            $table->index('category');
            $table->index('is_popular');
            $table->index('is_available');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menu_items');
    }
};
