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
            // Pricing
            $table->enum('price_range', ['$', '$$', '$$$', '$$$$'])->default('$$')->after('average_rating');

            // Spice level (1-5, 1 = mild, 5 = muy picante)
            $table->tinyInteger('spice_level')->default(3)->after('price_range');

            // Regional authenticity
            $table->string('mexican_region')->nullable()->after('spice_level'); // Oaxaca, Jalisco, etc.

            // Dietary options (JSON array)
            $table->json('dietary_options')->nullable()->after('mexican_region'); // ['vegetarian', 'vegan', 'gluten_free', 'halal']

            // Atmosphere tags (JSON array)
            $table->json('atmosphere')->nullable()->after('dietary_options'); // ['family_friendly', 'romantic', 'casual', 'formal', 'outdoor_seating']

            // Special features (JSON array)
            $table->json('special_features')->nullable()->after('atmosphere'); // ['live_music', 'mariachi', 'outdoor_patio', 'bar', 'takeout', 'delivery', 'parking']

            // Authenticity badges
            $table->boolean('chef_certified')->default(false)->after('special_features');
            $table->boolean('traditional_recipes')->default(false)->after('chef_certified');
            $table->boolean('imported_ingredients')->default(false)->after('traditional_recipes');

            // Business details
            $table->boolean('accepts_reservations')->default(false)->after('imported_ingredients');
            $table->boolean('online_ordering')->default(false)->after('accepts_reservations');
            $table->string('order_url')->nullable()->after('online_ordering');

            // Wait time (in minutes, updated real-time)
            $table->integer('current_wait_time')->nullable()->after('order_url');
            $table->timestamp('wait_time_updated_at')->nullable()->after('current_wait_time');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('restaurants', function (Blueprint $table) {
            $table->dropColumn([
                'price_range',
                'spice_level',
                'mexican_region',
                'dietary_options',
                'atmosphere',
                'special_features',
                'chef_certified',
                'traditional_recipes',
                'imported_ingredients',
                'accepts_reservations',
                'online_ordering',
                'order_url',
                'current_wait_time',
                'wait_time_updated_at'
            ]);
        });
    }
};
