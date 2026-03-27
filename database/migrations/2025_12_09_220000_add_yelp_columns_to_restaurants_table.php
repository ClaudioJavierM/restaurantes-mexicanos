<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('restaurants', function (Blueprint $table) {
            if (!Schema::hasColumn('restaurants', 'yelp_id')) {
                $table->string('yelp_id')->nullable()->index();
            }
            if (!Schema::hasColumn('restaurants', 'yelp_url')) {
                $table->string('yelp_url')->nullable();
            }
            if (!Schema::hasColumn('restaurants', 'yelp_rating')) {
                $table->decimal('yelp_rating', 2, 1)->nullable();
            }
            if (!Schema::hasColumn('restaurants', 'yelp_reviews_count')) {
                $table->integer('yelp_reviews_count')->default(0);
            }
        });
    }

    public function down(): void
    {
        Schema::table('restaurants', function (Blueprint $table) {
            $table->dropColumn(['yelp_id', 'yelp_url', 'yelp_rating', 'yelp_reviews_count']);
        });
    }
};
