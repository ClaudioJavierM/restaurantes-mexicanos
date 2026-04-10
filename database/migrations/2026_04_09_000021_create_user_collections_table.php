<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_collections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name', 100);
            $table->text('description')->nullable();
            $table->boolean('is_public')->default(true);
            $table->foreignId('cover_restaurant_id')
                  ->nullable()
                  ->constrained('restaurants')
                  ->nullOnDelete();
            $table->timestamps();

            $table->index('user_id');
            $table->index('is_public');
        });

        Schema::create('user_collection_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('collection_id')
                  ->constrained('user_collections')
                  ->cascadeOnDelete();
            $table->foreignId('restaurant_id')
                  ->constrained('restaurants')
                  ->cascadeOnDelete();
            $table->text('note')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['collection_id', 'restaurant_id']);
            $table->index('collection_id');
            $table->index('restaurant_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_collection_items');
        Schema::dropIfExists('user_collections');
    }
};
