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
        Schema::table('restaurants', function (Blueprint $table) {
            $table->string('facebook_page_id')->nullable()->after('google_place_id');
            $table->string('facebook_url')->nullable()->after('facebook_page_id');
            $table->decimal('facebook_rating', 3, 2)->nullable()->after('facebook_url');
            $table->integer('facebook_review_count')->nullable()->after('facebook_rating');
            $table->json('facebook_hours')->nullable()->after('facebook_review_count');
            $table->timestamp('facebook_enriched_at')->nullable()->after('facebook_hours');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('restaurants', function (Blueprint $table) {
            $table->dropColumn([
                'facebook_page_id',
                'facebook_url',
                'facebook_rating',
                'facebook_review_count',
                'facebook_hours',
                'facebook_enriched_at',
            ]);
        });
    }
};
