<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('caption')->nullable();
            $table->string('photo_path');
            $table->string('thumbnail_path')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->text('rejection_reason')->nullable();

            // Photo metadata
            $table->integer('width')->nullable();
            $table->integer('height')->nullable();
            $table->integer('file_size')->nullable(); // in bytes
            $table->string('mime_type')->nullable();

            // Engagement metrics
            $table->integer('views_count')->default(0);
            $table->integer('likes_count')->default(0);
            $table->integer('reports_count')->default(0);

            // Photo type
            $table->enum('photo_type', ['food', 'interior', 'exterior', 'menu', 'drink', 'other'])->default('other');

            // Upload info
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['restaurant_id', 'status']);
            $table->index('user_id');
            $table->index('status');
            $table->index('photo_type');
            $table->index('created_at');
        });

        // Photo likes table
        Schema::create('photo_likes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_photo_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('ip_address')->nullable();
            $table->timestamps();

            // Unique constraint - user can only like once
            $table->unique(['user_photo_id', 'user_id']);
            $table->index('user_photo_id');
        });

        // Photo reports table
        Schema::create('photo_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_photo_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->enum('reason', ['inappropriate', 'not_restaurant', 'duplicate', 'spam', 'other']);
            $table->text('description')->nullable();
            $table->enum('status', ['pending', 'resolved', 'dismissed'])->default('pending');
            $table->timestamp('resolved_at')->nullable();
            $table->foreignId('resolved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->string('ip_address')->nullable();
            $table->timestamps();

            $table->index(['user_photo_id', 'status']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('photo_reports');
        Schema::dropIfExists('photo_likes');
        Schema::dropIfExists('user_photos');
    }
};
