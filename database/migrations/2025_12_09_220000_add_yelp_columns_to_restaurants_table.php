<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('restaurants', function (Blueprint $table) {
            $table->string('yelp_id')->nullable()->after('google_reviews_count')->index();
            $table->string('yelp_url')->nullable()->after('yelp_id');
            $table->decimal('yelp_rating', 2, 1)->nullable()->after('yelp_url');
            $table->integer('yelp_reviews_count')->default(0)->after('yelp_rating');
        });
    }

    public function down(): void
    {
        Schema::table('restaurants', function (Blueprint $table) {
            $table->dropColumn(['yelp_id', 'yelp_url', 'yelp_rating', 'yelp_reviews_count']);
        });
    }
};
