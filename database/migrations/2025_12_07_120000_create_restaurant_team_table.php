<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('restaurant_team', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // Role: owner, manager, staff
            $table->enum('role', ['owner', 'manager', 'staff'])->default('staff');

            // Optional granular permissions (JSON)
            // Example: {"reservations": true, "reviews": true, "menu": false, "settings": false}
            $table->json('permissions')->nullable();

            // Invitation tracking
            $table->foreignId('invited_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('invited_at')->nullable();
            $table->timestamp('accepted_at')->nullable();
            $table->string('invitation_token')->nullable()->unique();
            $table->timestamp('invitation_expires_at')->nullable();

            // Status: pending (invitation sent), active, revoked
            $table->enum('status', ['pending', 'active', 'revoked'])->default('pending');
            $table->timestamp('revoked_at')->nullable();
            $table->string('revoked_reason')->nullable();

            $table->timestamps();

            // Unique constraint: one user can have one role per restaurant
            $table->unique(['restaurant_id', 'user_id']);

            // Indexes for common queries
            $table->index(['user_id', 'status']);
            $table->index(['restaurant_id', 'role']);
            $table->index('invitation_token');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('restaurant_team');
    }
};
