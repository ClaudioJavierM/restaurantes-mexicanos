<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('api_usage', function (Blueprint $table) {
            $table->id();
            $table->string('service'); // 'google_places_text_search', 'google_places_details', 'google_street_view'
            $table->string('endpoint'); // API endpoint called
            $table->integer('requests_count')->default(1);
            $table->decimal('estimated_cost', 10, 4)->default(0); // Cost in USD
            $table->date('usage_date'); // Date of usage
            $table->json('metadata')->nullable(); // Additional info (query, place_id, etc.)
            $table->timestamps();

            // Indexes for fast queries
            $table->index(['service', 'usage_date']);
            $table->index('usage_date');
        });

        // Create settings table for limits
        Schema::create('api_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('value');
            $table->timestamps();
        });

        // Insert default limits
        DB::table('api_settings')->insert([
            ['key' => 'monthly_budget_limit', 'value' => '180', 'created_at' => now(), 'updated_at' => now()], // $180 to be safe
            ['key' => 'daily_request_limit', 'value' => '200', 'created_at' => now(), 'updated_at' => now()], // ~200 requests/day
            ['key' => 'alert_threshold', 'value' => '150', 'created_at' => now(), 'updated_at' => now()], // Alert at $150
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('api_usage');
        Schema::dropIfExists('api_settings');
    }
};
