<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('restaurants', function (Blueprint $table) {
            $table->string('apple_maps_id')->nullable()->after('foursquare_last_sync');
            $table->string('apple_maps_url')->nullable()->after('apple_maps_id');
            $table->timestamp('apple_maps_last_sync')->nullable()->after('apple_maps_url');
        });
    }

    public function down(): void
    {
        Schema::table('restaurants', function (Blueprint $table) {
            $table->dropColumn(['apple_maps_id', 'apple_maps_url', 'apple_maps_last_sync']);
        });
    }
};
