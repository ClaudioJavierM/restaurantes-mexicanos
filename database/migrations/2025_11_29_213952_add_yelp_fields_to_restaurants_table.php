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
            // Yelp Integration Fields
            $table->string('yelp_id')->nullable()->unique()->after('google_place_id');
            $table->decimal('yelp_rating', 2, 1)->nullable()->after('google_reviews_count');
            $table->integer('yelp_reviews_count')->nullable()->after('yelp_rating');
            $table->string('yelp_url')->nullable()->after('yelp_reviews_count');
            $table->timestamp('yelp_last_sync')->nullable()->after('yelp_url');

            // Import metadata
            $table->string('import_source')->nullable()->after('yelp_last_sync')->comment('yelp, google, manual, suggestion');
            $table->timestamp('imported_at')->nullable()->after('import_source');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('restaurants', function (Blueprint $table) {
            $table->dropColumn([
                'yelp_id',
                'yelp_rating',
                'yelp_reviews_count',
                'yelp_url',
                'yelp_last_sync',
                'import_source',
                'imported_at',
            ]);
        });
    }
};
