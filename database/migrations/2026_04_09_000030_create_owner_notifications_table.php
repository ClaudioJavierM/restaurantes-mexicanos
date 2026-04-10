<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Table may already exist from an earlier migration; skip if so.
        if (Schema::hasTable('owner_notifications')) {
            return;
        }

        Schema::create('owner_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->constrained('restaurants')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('type', 50)->default('system'); // new_order|new_review|new_vote|system
            $table->string('title', 200);
            $table->text('message');
            $table->json('data')->nullable();
            $table->string('icon', 50)->nullable();
            $table->string('color', 30)->nullable();
            $table->string('action_url', 500)->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->index(['restaurant_id', 'read_at'], 'idx_restaurant_read');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('owner_notifications');
    }
};
