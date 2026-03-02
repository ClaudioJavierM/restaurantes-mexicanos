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
        Schema::table('suggestions', function (Blueprint $table) {
            // Validation & Trust Score
            $table->integer('trust_score')->default(0)->after('status'); // 0-100
            $table->string('validation_status')->default('pending')->after('trust_score'); // pending, verified, rejected
            $table->json('validation_data')->nullable()->after('validation_status'); // Store all validation results

            // Google Places Data
            $table->string('google_place_id')->nullable()->after('validation_data');
            $table->boolean('google_verified')->default(false)->after('google_place_id');
            $table->decimal('google_rating', 2, 1)->nullable()->after('google_verified');
            $table->integer('google_reviews_count')->nullable()->after('google_rating');

            // Duplicate Detection
            $table->boolean('is_potential_duplicate')->default(false)->after('google_reviews_count');
            $table->json('duplicate_check_data')->nullable()->after('is_potential_duplicate');

            // Additional validation flags
            $table->boolean('website_verified')->default(false)->after('duplicate_check_data');
            $table->boolean('phone_verified')->default(false)->after('website_verified');
            $table->timestamp('verified_at')->nullable()->after('phone_verified');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('suggestions', function (Blueprint $table) {
            $table->dropColumn([
                'trust_score',
                'validation_status',
                'validation_data',
                'google_place_id',
                'google_verified',
                'google_rating',
                'google_reviews_count',
                'is_potential_duplicate',
                'duplicate_check_data',
                'website_verified',
                'phone_verified',
                'verified_at',
            ]);
        });
    }
};
