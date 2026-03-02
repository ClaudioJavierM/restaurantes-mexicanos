<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('famer_score_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('famer_score_id')->nullable()->constrained()->onDelete('set null');

            // Contact info (lead data)
            $table->string('email');
            $table->string('name')->nullable();
            $table->string('phone', 20)->nullable();

            // Restaurant search data (for external/unclaimed restaurants)
            $table->string('restaurant_name')->nullable();
            $table->string('restaurant_city')->nullable();
            $table->string('restaurant_state', 2)->nullable();
            $table->string('yelp_id')->nullable();
            $table->string('google_place_id')->nullable();

            // Lead tracking (UTM parameters)
            $table->string('utm_source')->nullable();
            $table->string('utm_medium')->nullable();
            $table->string('utm_campaign')->nullable();
            $table->string('referrer')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();

            // Status tracking
            $table->enum('status', ['pending', 'sent', 'opened', 'clicked', 'claimed'])->default('pending');
            $table->timestamp('email_sent_at')->nullable();
            $table->timestamp('email_opened_at')->nullable();
            $table->timestamp('email_clicked_at')->nullable();

            // Marketing
            $table->boolean('marketing_consent')->default(false);
            $table->boolean('is_owner')->default(false);

            $table->timestamps();

            $table->index('email');
            $table->index('status');
            $table->index(['restaurant_id', 'created_at']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('famer_score_requests');
    }
};
