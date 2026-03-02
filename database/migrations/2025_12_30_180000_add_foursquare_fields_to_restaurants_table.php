<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('restaurants', function (Blueprint $table) {
            $table->string('foursquare_id')->nullable()->after('tripadvisor_last_sync');
            $table->decimal('foursquare_rating', 3, 1)->nullable()->after('foursquare_id');
            $table->unsignedInteger('foursquare_checkins')->nullable()->after('foursquare_rating');
            $table->unsignedInteger('foursquare_tips_count')->nullable()->after('foursquare_checkins');
            $table->unsignedTinyInteger('foursquare_price')->nullable()->after('foursquare_tips_count');
            $table->timestamp('foursquare_last_sync')->nullable()->after('foursquare_price');
        });
    }

    public function down(): void
    {
        Schema::table('restaurants', function (Blueprint $table) {
            $table->dropColumn([
                'foursquare_id',
                'foursquare_rating',
                'foursquare_checkins',
                'foursquare_tips_count',
                'foursquare_price',
                'foursquare_last_sync',
            ]);
        });
    }
};
