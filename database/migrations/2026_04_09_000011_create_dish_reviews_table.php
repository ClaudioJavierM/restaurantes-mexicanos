<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dish_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('menu_item_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('dish_name'); // snapshot of dish name at review time
            $table->unsignedTinyInteger('rating'); // 1-5
            $table->text('comment')->nullable();
            $table->string('reviewer_name')->nullable(); // for guests
            $table->string('reviewer_email')->nullable(); // for guests
            $table->boolean('is_approved')->default(false);
            $table->boolean('is_verified_purchase')->default(false);
            $table->json('photos')->nullable(); // array of photo URLs
            $table->unsignedInteger('helpful_count')->default(0);
            $table->timestamps();

            $table->index(['restaurant_id', 'is_approved']);
            $table->index(['menu_item_id', 'is_approved']);
            $table->index(['user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dish_reviews');
    }
};
