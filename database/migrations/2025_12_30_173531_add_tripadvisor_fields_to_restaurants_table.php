<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('restaurants', function (Blueprint $table) {
            $table->string('tripadvisor_id')->nullable()->after('tripadvisor_url');
            $table->decimal('tripadvisor_rating', 2, 1)->nullable()->after('tripadvisor_id');
            $table->unsignedInteger('tripadvisor_reviews_count')->nullable()->after('tripadvisor_rating');
            $table->string('tripadvisor_ranking')->nullable()->after('tripadvisor_reviews_count');
            $table->string('tripadvisor_price_level')->nullable()->after('tripadvisor_ranking');
            $table->timestamp('tripadvisor_last_sync')->nullable()->after('tripadvisor_price_level');
        });
    }

    public function down(): void
    {
        Schema::table('restaurants', function (Blueprint $table) {
            $table->dropColumn([
                'tripadvisor_id',
                'tripadvisor_rating',
                'tripadvisor_reviews_count',
                'tripadvisor_ranking',
                'tripadvisor_price_level',
                'tripadvisor_last_sync',
            ]);
        });
    }
};
