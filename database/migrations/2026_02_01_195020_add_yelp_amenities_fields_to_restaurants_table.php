<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add columns one by one, checking each first
        if (!Schema::hasColumn('restaurants', 'yelp_attributes')) {
            Schema::table('restaurants', function (Blueprint $table) {
                $table->json('yelp_attributes')->nullable();
            });
        }
        
        if (!Schema::hasColumn('restaurants', 'yelp_hours')) {
            Schema::table('restaurants', function (Blueprint $table) {
                $table->json('yelp_hours')->nullable();
            });
        }
        
        if (!Schema::hasColumn('restaurants', 'amenities')) {
            Schema::table('restaurants', function (Blueprint $table) {
                $table->json('amenities')->nullable();
            });
        }
        
        if (!Schema::hasColumn('restaurants', 'menu_url')) {
            Schema::table('restaurants', function (Blueprint $table) {
                $table->string('menu_url')->nullable();
            });
        }
    }

    public function down(): void
    {
        $columns = ['yelp_attributes', 'yelp_hours', 'amenities', 'menu_url'];
        
        foreach ($columns as $column) {
            if (Schema::hasColumn('restaurants', $column)) {
                Schema::table('restaurants', function (Blueprint $table) use ($column) {
                    $table->dropColumn($column);
                });
            }
        }
    }
};
