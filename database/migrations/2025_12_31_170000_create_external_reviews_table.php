<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('external_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->constrained()->onDelete('cascade');
            
            // Platform info
            $table->string('platform'); // google, yelp, tripadvisor, facebook
            $table->string('platform_review_id')->nullable(); // ID from the platform
            $table->string('platform_url')->nullable(); // Direct link to review
            
            // Reviewer info
            $table->string('reviewer_name');
            $table->string('reviewer_avatar')->nullable();
            $table->string('reviewer_profile_url')->nullable();
            $table->integer('reviewer_review_count')->nullable(); // How many reviews they've written
            
            // Review content
            $table->tinyInteger('rating'); // 1-5 stars
            $table->text('comment')->nullable();
            $table->json('photos')->nullable(); // Photos attached to review
            $table->timestamp('reviewed_at')->nullable(); // When review was posted
            
            // Owner response
            $table->text('owner_response')->nullable();
            $table->timestamp('owner_response_at')->nullable();
            $table->boolean('response_synced')->default(false); // If response was sent to platform
            $table->timestamp('response_synced_at')->nullable();
            
            // Status
            $table->enum('status', ['pending', 'responded', 'flagged', 'hidden'])->default('pending');
            $table->boolean('is_featured')->default(false); // Show on website
            $table->integer('helpful_count')->default(0);
            
            // Sentiment analysis (optional AI feature)
            $table->enum('sentiment', ['positive', 'neutral', 'negative'])->nullable();
            $table->json('keywords')->nullable(); // Extracted keywords
            
            // Sync metadata
            $table->timestamp('last_synced_at')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->unique(['restaurant_id', 'platform', 'platform_review_id']);
            $table->index(['restaurant_id', 'platform']);
            $table->index(['restaurant_id', 'status']);
            $table->index('reviewed_at');
        });

        // Platform connections for OAuth tokens
        Schema::create('platform_connections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            $table->string('platform'); // google, facebook, yelp, tripadvisor
            $table->string('platform_account_id')->nullable(); // Business ID on platform
            $table->string('platform_account_name')->nullable();
            
            // OAuth tokens
            $table->text('access_token')->nullable();
            $table->text('refresh_token')->nullable();
            $table->timestamp('token_expires_at')->nullable();
            
            // Connection status
            $table->enum('status', ['active', 'expired', 'revoked', 'pending'])->default('pending');
            $table->timestamp('connected_at')->nullable();
            $table->timestamp('last_sync_at')->nullable();
            $table->text('last_error')->nullable();
            
            $table->timestamps();
            
            $table->unique(['restaurant_id', 'platform']);
        });

        // Review response templates
        Schema::create('review_response_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            $table->string('name');
            $table->string('category'); // positive, negative, neutral
            $table->text('template'); // With placeholders like {customer_name}, {restaurant_name}
            $table->boolean('is_global')->default(false); // Available to all restaurants
            $table->integer('usage_count')->default(0);
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('review_response_templates');
        Schema::dropIfExists('platform_connections');
        Schema::dropIfExists('external_reviews');
    }
};
