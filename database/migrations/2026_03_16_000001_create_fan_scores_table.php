<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fan_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('restaurant_id')->constrained()->cascadeOnDelete();

            // Point breakdown
            $table->integer('vote_points')->default(0);
            $table->integer('checkin_points')->default(0);
            $table->integer('review_points')->default(0);
            $table->integer('favorite_points')->default(0);
            $table->integer('share_points')->default(0);
            $table->integer('coupon_points')->default(0);
            $table->integer('total_points')->default(0);

            // Counts
            $table->integer('votes_count')->default(0);
            $table->integer('checkins_count')->default(0);
            $table->integer('reviews_count')->default(0);
            $table->integer('coupons_redeemed')->default(0);
            $table->integer('shares_count')->default(0);

            // Fan level: fan, super_fan, fan_destacado
            $table->string('fan_level')->nullable();

            // Badge display
            $table->boolean('badge_accepted')->default(false);
            $table->timestamp('badge_offered_at')->nullable();
            $table->timestamp('badge_accepted_at')->nullable();

            // Year tracking (fans reset yearly for awards)
            $table->integer('year')->default(2026);

            $table->timestamps();

            $table->unique(['user_id', 'restaurant_id', 'year']);
            $table->index(['restaurant_id', 'total_points']);
            $table->index(['restaurant_id', 'fan_level']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fan_scores');
    }
};
