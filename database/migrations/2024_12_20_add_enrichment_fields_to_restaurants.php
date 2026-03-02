<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('restaurants', function (Blueprint $table) {
            // Opening Hours (JSON from Google/Yelp)
            if (!Schema::hasColumn('restaurants', 'opening_hours')) {
                $table->json('opening_hours')->nullable()->after('hours');
            }

            // Services available
            if (!Schema::hasColumn('restaurants', 'services')) {
                $table->json('services')->nullable()->after('opening_hours')
                    ->comment('delivery, takeout, dine_in, curbside_pickup, reservations');
            }

            // Accessibility features
            if (!Schema::hasColumn('restaurants', 'accessibility')) {
                $table->json('accessibility')->nullable()->after('services')
                    ->comment('wheelchair_accessible, parking, restroom');
            }

            // Payment methods
            if (!Schema::hasColumn('restaurants', 'payment_methods')) {
                $table->json('payment_methods')->nullable()->after('accessibility')
                    ->comment('credit_cards, debit_cards, cash, apple_pay, google_pay');
            }

            // Additional contact
            if (!Schema::hasColumn('restaurants', 'instagram_url')) {
                $table->string('instagram_url')->nullable()->after('facebook_url');
            }
            if (!Schema::hasColumn('restaurants', 'twitter_url')) {
                $table->string('twitter_url')->nullable()->after('instagram_url');
            }
            if (!Schema::hasColumn('restaurants', 'tiktok_url')) {
                $table->string('tiktok_url')->nullable()->after('twitter_url');
            }

            // Google additional data
            if (!Schema::hasColumn('restaurants', 'google_photos_count')) {
                $table->integer('google_photos_count')->nullable()->after('google_reviews_count');
            }
            if (!Schema::hasColumn('restaurants', 'google_price_level')) {
                $table->tinyInteger('google_price_level')->nullable()->after('google_photos_count')
                    ->comment('0=Free, 1=$, 2=$$, 3=$$$, 4=$$$$');
            }

            // Yelp additional data
            if (!Schema::hasColumn('restaurants', 'yelp_categories')) {
                $table->json('yelp_categories')->nullable()->after('yelp_reviews_count');
            }
            if (!Schema::hasColumn('restaurants', 'yelp_transactions')) {
                $table->json('yelp_transactions')->nullable()->after('yelp_categories')
                    ->comment('delivery, pickup, restaurant_reservation');
            }

            // Street View
            if (!Schema::hasColumn('restaurants', 'streetview_url')) {
                $table->string('streetview_url')->nullable()->after('google_maps_url');
            }
            if (!Schema::hasColumn('restaurants', 'streetview_downloaded_at')) {
                $table->timestamp('streetview_downloaded_at')->nullable()->after('streetview_url');
            }
        });
    }

    public function down(): void
    {
        Schema::table('restaurants', function (Blueprint $table) {
            $columns = [
                'opening_hours', 'services', 'accessibility', 'payment_methods',
                'instagram_url', 'twitter_url', 'tiktok_url',
                'google_photos_count', 'google_price_level',
                'yelp_categories', 'yelp_transactions',
                'streetview_url', 'streetview_downloaded_at'
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('restaurants', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
