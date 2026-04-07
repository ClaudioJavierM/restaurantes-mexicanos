<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // External platform ratings (Google, Yelp, Facebook, TripAdvisor)
        if (!Schema::hasTable('external_ratings')) {
            Schema::create('external_ratings', function (Blueprint $table) {
                $table->id();
                $table->foreignId('restaurant_id')->constrained()->onDelete('cascade');
                $table->string('platform'); // google, yelp, facebook, tripadvisor
                $table->string('platform_id')->nullable(); // ID in external platform
                $table->decimal('rating', 3, 2)->nullable(); // 0.00 to 5.00
                $table->integer('review_count')->default(0);
                $table->string('price_level')->nullable(); // $, $$, $$$, $$$$
                $table->string('platform_url')->nullable();
                $table->json('extra_data')->nullable(); // Additional platform-specific data
                $table->timestamp('last_synced_at')->nullable();
                $table->timestamps();
                
                $table->unique(['restaurant_id', 'platform']);
                $table->index(['platform', 'rating']);
            });
        }

        // Restaurant scores (calculated daily/weekly)
        if (!Schema::hasTable('restaurant_scores')) {
            Schema::create('restaurant_scores', function (Blueprint $table) {
                $table->id();
                $table->foreignId('restaurant_id')->constrained()->onDelete('cascade');
                
                // External platform scores (0-100 normalized)
                $table->decimal('google_score', 5, 2)->default(0);
                $table->decimal('yelp_score', 5, 2)->default(0);
                $table->decimal('facebook_score', 5, 2)->default(0);
                $table->decimal('tripadvisor_score', 5, 2)->default(0);
                
                // FAMER internal scores (0-100)
                $table->decimal('famer_rating_score', 5, 2)->default(0);
                $table->decimal('review_count_score', 5, 2)->default(0);
                $table->decimal('verified_reviews_score', 5, 2)->default(0);
                $table->decimal('owner_response_score', 5, 2)->default(0);
                $table->decimal('engagement_score', 5, 2)->default(0);
                $table->decimal('subscription_score', 5, 2)->default(0);
                $table->decimal('seniority_score', 5, 2)->default(0);
                $table->decimal('survey_score', 5, 2)->default(0);
                
                // Total weighted score
                $table->decimal('total_score', 5, 2)->default(0);
                
                // Ranking positions
                $table->integer('city_rank')->nullable();
                $table->integer('state_rank')->nullable();
                $table->integer('national_rank')->nullable();
                
                $table->timestamp('calculated_at')->nullable();
                $table->timestamps();
                
                $table->unique('restaurant_id');
                $table->index(['total_score', 'city_rank']);
                $table->index(['total_score', 'state_rank']);
                $table->index(['total_score', 'national_rank']);
            });
        }

        // Annual rankings history
        if (!Schema::hasTable('restaurant_rankings')) {
            Schema::create('restaurant_rankings', function (Blueprint $table) {
                $table->id();
                $table->foreignId('restaurant_id')->constrained()->onDelete('cascade');
                $table->year('year');
                $table->string('ranking_type'); // city, state, national, category
                $table->string('ranking_scope'); // city name, state code, 'usa', or category name
                $table->integer('position'); // 1-10 for city/state, 1-100 for national
                $table->decimal('final_score', 5, 2);
                $table->json('score_breakdown')->nullable(); // Detailed scores
                $table->string('badge_name')->nullable(); // Top 10 Dallas 2025
                $table->string('certificate_path')->nullable(); // PDF path
                $table->boolean('is_published')->default(false);
                $table->timestamp('published_at')->nullable();
                $table->timestamps();
                
                $table->unique(['restaurant_id', 'year', 'ranking_type', 'ranking_scope'], 'rest_rankings_unique');
                $table->index(['year', 'ranking_type', 'ranking_scope', 'position'], 'rest_rankings_idx');
            });
        }

        // Score weights configuration
        if (!Schema::hasTable('ranking_weights')) {
            Schema::create('ranking_weights', function (Blueprint $table) {
                $table->id();
                $table->string('factor'); // google, yelp, famer_rating, etc.
                $table->decimal('weight', 5, 2); // Percentage weight
                $table->string('category')->default('default'); // For different ranking types
                $table->boolean('is_active')->default(true);
                $table->timestamps();
                
                $table->unique(['factor', 'category']);
            });
        }

        // Add external IDs to restaurants table
        if (!Schema::hasColumn('restaurants', 'google_place_id')) {
            Schema::table('restaurants', function (Blueprint $table) {
                $table->string('google_place_id')->nullable()->after('longitude');
                $table->string('yelp_id')->nullable()->after('google_place_id');
                $table->string('facebook_page_id')->nullable()->after('yelp_id');
                $table->string('tripadvisor_id')->nullable()->after('facebook_page_id');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('ranking_weights');
        Schema::dropIfExists('restaurant_rankings');
        Schema::dropIfExists('restaurant_scores');
        Schema::dropIfExists('external_ratings');
        
        if (Schema::hasColumn('restaurants', 'google_place_id')) {
            Schema::table('restaurants', function (Blueprint $table) {
                $table->dropColumn(['google_place_id', 'yelp_id', 'facebook_page_id', 'tripadvisor_id']);
            });
        }
    }
};
