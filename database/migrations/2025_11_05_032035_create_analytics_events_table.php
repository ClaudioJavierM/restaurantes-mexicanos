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
        Schema::create('analytics_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->constrained()->onDelete('cascade');
            $table->string('event_type'); // page_view, phone_click, website_click, direction_click, coupon_view, coupon_click, photo_view, etc.
            $table->string('user_type')->nullable(); // guest, authenticated, owner
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();

            // Request data
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->string('referrer')->nullable();
            $table->string('device_type')->nullable(); // mobile, tablet, desktop
            $table->string('browser')->nullable();
            $table->string('platform')->nullable(); // iOS, Android, Windows, etc.

            // Location data
            $table->string('country_code', 2)->nullable();
            $table->string('city')->nullable();
            $table->string('region')->nullable();

            // Event metadata (JSON for flexibility)
            $table->json('metadata')->nullable(); // coupon_id, page_path, search_query, etc.

            // Session tracking
            $table->string('session_id')->nullable();

            $table->timestamps();

            // Indexes for performance
            $table->index('restaurant_id');
            $table->index('event_type');
            $table->index('user_id');
            $table->index('created_at');
            $table->index(['restaurant_id', 'event_type', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('analytics_events');
    }
};
