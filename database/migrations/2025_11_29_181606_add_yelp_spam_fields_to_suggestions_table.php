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
            // Yelp Data
            $table->string('yelp_id')->nullable()->after('google_reviews_count');
            $table->boolean('yelp_verified')->default(false)->after('yelp_id');
            $table->decimal('yelp_rating', 2, 1)->nullable()->after('yelp_verified');
            $table->integer('yelp_reviews_count')->nullable()->after('yelp_rating');

            // Spam Detection
            $table->integer('spam_score')->default(0)->after('yelp_reviews_count'); // 0-100
            $table->string('spam_risk_level')->default('low')->after('spam_score'); // low, medium, high
            $table->json('spam_flags')->nullable()->after('spam_risk_level');
            $table->boolean('is_spam')->default(false)->after('spam_flags');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('suggestions', function (Blueprint $table) {
            $table->dropColumn([
                'yelp_id',
                'yelp_verified',
                'yelp_rating',
                'yelp_reviews_count',
                'spam_score',
                'spam_risk_level',
                'spam_flags',
                'is_spam',
            ]);
        });
    }
};
