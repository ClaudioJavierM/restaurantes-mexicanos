<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('restaurant_team_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('role', ['admin', 'manager', 'editor', 'viewer'])->default('viewer');
            $table->boolean('is_primary')->default(false); // The original owner/claimer
            $table->foreignId('invited_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('team_request_id')->nullable()->constrained()->onDelete('set null');
            $table->timestamp('accepted_at')->nullable();
            $table->timestamps();

            $table->unique(['restaurant_id', 'user_id']);
            $table->index(['user_id', 'role']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('restaurant_team_members');
    }
};
