<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('authenticity_badge_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('badge_id'); // slug: family_owned, regional_recipe, etc.
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('evidence')->nullable();
            $table->text('admin_notes')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();

            // Unique: one active request per badge per restaurant
            $table->unique(['restaurant_id', 'badge_id', 'status'], 'unique_active_badge_request');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('authenticity_badge_requests');
    }
};
