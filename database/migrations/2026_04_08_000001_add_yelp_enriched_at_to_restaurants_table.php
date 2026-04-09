<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('restaurants', function (Blueprint $table) {
            // Tracks when full Yelp detail (photos, hours, attributes) was last fetched.
            // All backfill commands check this before calling businesses/{id} again.
            // Threshold: 30 days — same as yelp_last_sync.
            $table->timestamp('yelp_enriched_at')->nullable()->after('yelp_last_sync');
        });
    }

    public function down(): void
    {
        Schema::table('restaurants', function (Blueprint $table) {
            $table->dropColumn('yelp_enriched_at');
        });
    }
};
